<?php


namespace Nutsweb\LaravelPrerender;


use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response as GuzzleResponse;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class PrerenderMiddleware implements HttpKernelInterface
{
    private $app;

    private $client;

    private $prerenderToken;

    private $crawlerUserAgents;

    private $whitelist;

    private $blacklist;

    public function __construct(HttpKernelInterface $app,
                                GuzzleClient $client,
                                $prerenderToken,
                                array $crawlerUserAgents,
                                array $whitelist,
                                array $blacklist)
    {
        $this->app = $app;
        $this->client = $client;
        $this->crawlerUserAgents = $crawlerUserAgents;
        $this->prerenderToken = $prerenderToken;
        $this->whitelist = $whitelist;
        $this->blacklist = $blacklist;
    }

    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        if ($this->shouldShowPrerenderedPage($request)) {
            $prerenderedResponse = $this->getPrerenderedPageResponse($request);
            if ($prerenderedResponse) {
                return $this->buildSymfonyResponseFromGuzzleResponse($prerenderedResponse);
            }
        }

        return $this->app->handle($request, $type, $catch);
    }

    private function shouldShowPrerenderedPage(SymfonyRequest $request)
    {
        $userAgent = strtolower($request->server->get('HTTP_USER_AGENT'));
        $bufferAgent = $request->server->get('X-BUFFERBOT');
        $requestUri = $request->getRequestUri();
        $referer = $request->headers->get('Referer');

        $isRequestingPrerenderedPage = false;

        if (!$userAgent) return false;
        if (!$request->isMethod('GET')) return false;

        if ($request->query->has('_escaped_fragment_')) $isRequestingPrerenderedPage = true;

        foreach ($this->crawlerUserAgents as $crawlerUserAgent) {
            if (str_contains($userAgent, strtolower($crawlerUserAgent))) {
                $isRequestingPrerenderedPage = true;
            }
        }

        if ($bufferAgent) $isRequestingPrerenderedPage = true;

        if (!$isRequestingPrerenderedPage) return false;

        if ($this->whitelist) {
            if (!$this->isListed($requestUri, $this->whitelist)) {
                return false;
            }
        }

        if ($this->blacklist) {
            $uris[] = $requestUri;
            if ($referer) $uris[] = $referer;
            if ($this->isListed($uris, $this->blacklist)) {
                return false;
            }
        }

        return true;
    }

    private function getPrerenderedPageResponse(SymfonyRequest $request)
    {
        $headers = [
            'User-Agent' => $request->server->get('HTTP_USER_AGENT'),
        ];
        if ($this->prerenderToken) {
            $headers['X-Prerender-Token'] = $this->prerenderToken;
        }

        try {
            return $this->client->get('/' . urlencode($request->getUri()), compact('headers'));
        } catch (RequestException $exception) {
            if ($this->app['config']->get('app.debug')) {
                throw $exception;
            }
            return null;
        }
    }

    private function buildSymfonyResponseFromGuzzleResponse(GuzzleResponse $prerenderedResponse)
    {
        $body = $prerenderedResponse->getBody();
        $statusCode = $prerenderedResponse->getStatusCode();
        $headers = $prerenderedResponse->getHeaders();
        return new SymfonyResponse($body, $statusCode, $headers);
    }

    private function isListed($needles, $list)
    {
        $needles = is_array($needles) ? $needles : [$needles];

        foreach ($list as $pattern) {
            foreach ($needles as $needle) {
                if (str_is($pattern, $needle)) {
                    return true;
                }
            }
        }
        return false;
    }

}