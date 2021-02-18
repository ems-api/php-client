<?php

exit('COMMENT ME TO TEST THE EXAMPLES!');

require_once __DIR__ . '/../vendor/autoload.php';

// configuration object
$config = new \EmsApi\Config([
    'apiUrl'    => 'http://www.mailwizz-powered-website.tld/api',
    'apiKey'    => 'PUBLIC-KEY',

    // components
    'components' => [
        'cache' => [
            'class'     => \EmsApi\Cache\File::class,
            'filesPath' => __DIR__ . '/data/cache', // make sure it is writable by webserver
        ]
    ],
]);

// now inject the configuration and we are ready to make api calls
\EmsApi\Base::setConfig($config);

// start UTC
date_default_timezone_set('UTC');
