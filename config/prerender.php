<?php return [

    /*
    |--------------------------------------------------------------------------
    | Enable Prerender
    |--------------------------------------------------------------------------
    |
    | Set this field to false to fully disable the prerender service. You
    | would probably override this in a local configuration, to disable
    | prerender on your local machine.
    |
    */

    'enable' => env('PRERENDER_ENABLE', true),

    /*
    |--------------------------------------------------------------------------
    | Prerender URL
    |--------------------------------------------------------------------------
    |
    | This is the prerender URL to the service that prerenders the pages.
    | By default, Prerender's hosted service on prerender.io is used
    | (https://service.prerender.io). But you can also set it to your
    | own server address.
    |
    */

    'prerender_url' => env('PRERENDER_URL', 'https://service.prerender.io'),


    /*
    |--------------------------------------------------------------------------
    | Return soft HTTP status codes
    |--------------------------------------------------------------------------
    |
    | By default Prerender returns soft HTTP codes. If you would like it to
    | return the real ones in case of Redirection (3xx) or status Not Found (404),
    | set this parameter to false.
    | Keep in mind that returning real HTTP codes requires appropriate meta tags
    | to be set. For more details, see github.com/prerender/prerender#httpheaders
    |
    */

    'prerender_soft_http_codes' => env('PRERENDER_SOFT_HTTP_STATUS_CODES', true),

    /*
    |--------------------------------------------------------------------------
    | Prerender Token
    |--------------------------------------------------------------------------
    |
    | If you use prerender.io as service, you need to set your prerender.io
    | token here. It will be sent via the X-Prerender-Token header. If
    | you do not provide a token, the header will not be added.
    |
    */

    'prerender_token' => env('PRERENDER_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Prerender Whitelist
    |--------------------------------------------------------------------------
    |
    | Whitelist paths or patterns. You can use asterix syntax, or regular
    | expressions (without start and end markers). If a whitelist is supplied,
    | only url's containing a whitelist path will be prerendered. An empty
    | array means that all URIs will pass this filter. Note that this is the
    | full request URI, so including starting slash and query parameter string.
    | See github.com/JeroenNoten/Laravel-Prerender for an example.
    |
    */

    'whitelist' => [],

    /*
    |--------------------------------------------------------------------------
    | Prerender Blacklist
    |--------------------------------------------------------------------------
    |
    | Blacklist paths to exclude. You can use asterix syntax, or regular
    | expressions (without start and end markers). If a blacklist is supplied,
    | all url's will be prerendered except ones containing a blacklist path.
    | By default, a set of asset extentions are included (this is actually only
    | necessary when you dynamically provide assets via routes). Note that this
    | is the full request URI, so including starting slash and query parameter
    | string. See github.com/JeroenNoten/Laravel-Prerender for an example.
    |
    */

    'blacklist' => [
        '*.js',
        '*.css',
        '*.xml',
        '*.less',
        '*.png',
        '*.jpg',
        '*.jpeg',
        '*.svg',
        '*.gif',
        '*.pdf',
        '*.doc',
        '*.txt',
        '*.ico',
        '*.rss',
        '*.zip',
        '*.mp3',
        '*.rar',
        '*.exe',
        '*.wmv',
        '*.doc',
        '*.avi',
        '*.ppt',
        '*.mpg',
        '*.mpeg',
        '*.tif',
        '*.wav',
        '*.mov',
        '*.psd',
        '*.ai',
        '*.xls',
        '*.mp4',
        '*.m4a',
        '*.swf',
        '*.dat',
        '*.dmg',
        '*.iso',
        '*.flv',
        '*.m4v',
        '*.torrent',
        '*.eot',
        '*.ttf',
        '*.otf',
        '*.woff',
        '*.woff2'
    ],


    /*
    |--------------------------------------------------------------------------
    | Crawler User Agents
    |--------------------------------------------------------------------------
    |
    | Requests from crawlers that do not support _escaped_fragment_ will
    | nevertheless be served with prerendered pages. You can customize
    | the list of crawlers here.
    |
    */

    'crawler_user_agents' => [
        'googlebot',
        'yahoo',
        'bingbot',
        'yandex',
        'baiduspider',
        'facebookexternalhit',
        'twitterbot',
        'rogerbot',
        'linkedinbot',
        'embedly',
        'quora link preview',
        'showyoubot',
        'outbrain',
        'pinterest',
        'developers.google.com/+/web/snippet',
        'slackbot',
        'vkshare',
        'w3c_validator',
        'redditbot',
        'applebot',
        'whatsapp',
        'flipboard',
        'tumblr',
        'bitlybot',
        'skypeuripreview',
        'nuzzel',
        'discordbot',
        'google page speed',
        'qwantify',
        'pinterestbot',
        'bitrix link preview',
        'xing-contenttabreceiver',
        'chrome-lighthouse',
        'telegrambot',
    ],
];
