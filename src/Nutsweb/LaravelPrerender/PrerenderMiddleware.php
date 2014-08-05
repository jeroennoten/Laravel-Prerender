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
    /**
     * The application instance (implements the HttpKernelInterface)
     *
     * @var HttpKernelInterface
     */
    private $app;

    /**
     * The Guzzle Client that sends GET requests to the prerender server
     * This client must take care of the base URL by itself
     *
     * @var GuzzleClient
     */
    private $client;

    /**
     * This token will be provided via the X-Prerender-Token header.
     *
     * @var string
     */
    private $prerenderToken;

    /**
     * List of crawler user agents that will be
     *
     * @var array
     */
    private $crawlerUserAgents;

    /**
     * URI whitelist for prerendering pages only on this list
     *
     * @var array
     */
    private $whitelist;

    /**
     * URI blacklist for prerendering pages that are not on the list
     *
     * @var array
     */
    private $blacklist;

    /**
     * Creates a new PrerenderMiddleware instance
     *
     * @param HttpKernelInterface $app
     * @param GuzzleClient $client
     * @param $prerenderToken
     * @param array $crawlerUserAgents
     * @param array $whitelist
     * @param array $blacklist
     */
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

    /**
     * Handles a request and prerender if it should, otherwise call the next middleware.
     *
     * @param SymfonyRequest $request
     * @param int $type
     * @param bool $catch
     * @return SymfonyResponse
     */
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

    /**
     * Returns whether the request must be prerendered.
     *
     * @param SymfonyRequest $request
     * @return bool
     */
    private function shouldShowPrerenderedPage(SymfonyRequest $request)
    {
        $userAgent = strtolower($request->server->get('HTTP_USER_AGENT'));
        $bufferAgent = $request->server->get('X-BUFFERBOT');
        $requestUri = $request->getRequestUri();
        $referer = $request->headers->get('Referer');

        $isRequestingPrerenderedPage = false;

        if (!$userAgent) return false;
        if (!$request->isMethod('GET')) return false;

        // prerender if _escaped_fragment_ is in the query string
        if ($request->query->has('_escaped_fragment_')) $isRequestingPrerenderedPage = true;

        // prerender if a crawler is detected
        foreach ($this->crawlerUserAgents as $crawlerUserAgent) {
            if (str_contains($userAgent, strtolower($crawlerUserAgent))) {
                $isRequestingPrerenderedPage = true;
            }
        }

        if ($bufferAgent) $isRequestingPrerenderedPage = true;

        if (!$isRequestingPrerenderedPage) return false;

        // only check whitelist if it is not empty
        if ($this->whitelist) {
            if (!$this->isListed($requestUri, $this->whitelist)) {
                return false;
            }
        }

        // only check blacklist if it is not empty
        if ($this->blacklist) {
            $uris[] = $requestUri;
            // we also check for a blacklisted referer
            if ($referer) $uris[] = $referer;
            if ($this->isListed($uris, $this->blacklist)) {
                return false;
            }
        }

        // Okay! Prerender please.
        return true;
    }

    /**
     * Prerender the page and return the Guzzle Response
     *
     * @param SymfonyRequest $request
     * @return null|void
     */
    private function getPrerenderedPageResponse(SymfonyRequest $request)
    {
        $headers = [
            'User-Agent' => $request->server->get('HTTP_USER_AGENT'),
        ];
        if ($this->prerenderToken) {
            $headers['X-Prerender-Token'] = $this->prerenderToken;
        }

        try {
            // Return the Guzzle Response
            return $this->client->get('/' . urlencode($request->getUri()), compact('headers'));
        } catch (RequestException $exception) {
            // In case of an exception, we only throw the exception if we are in debug mode. Otherwise,
            // we return null and the handle() method will just pass the request to the next middleware
            // and we do not show a prerendered page.
            if ($this->app['config']->get('app.debug')) {
                throw $exception;
            }
            return null;
        }
    }

    /**
     * Convert a Guzzle Response to a Symfony Response
     *
     * @param GuzzleResponse $prerenderedResponse
     * @return SymfonyResponse
     */
    private function buildSymfonyResponseFromGuzzleResponse(GuzzleResponse $prerenderedResponse)
    {
        $body = $prerenderedResponse->getBody();
        $statusCode = $prerenderedResponse->getStatusCode();
        $headers = $prerenderedResponse->getHeaders();
        return new SymfonyResponse($body, $statusCode, $headers);
    }

    /**
     * Check whether one or more needles are in the given list
     *
     * @param $needles
     * @param $list
     * @return bool
     */
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