<?php return [

    'enable' => true,

    'prerender_url' => 'http://service.prerender.io',

    'prerender_token' => '',

    'crawler_user_agents' => [
        // googlebot, yahoo, and bingbot are not in this list because
        // we support _escaped_fragment_ and want to ensure people aren't
        // penalized for cloaking.

        // 'googlebot',
        // 'yahoo',
        // 'bingbot',
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
        'developers.google.com/+/web/snippet'
    ],

    'whitelist' => [],

    'blacklist' => [
        '*.js',
        '*.css',
        '*.xml',
        '*.less',
        '*.png',
        '*.jpg',
        '*.jpeg',
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
        '*.torrent'
    ],

];