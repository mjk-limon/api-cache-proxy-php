<?php

return [
    'timezone' => 'Asia/Dhaka',
    'base_url' => 'https://www.prothomalo.com/api/v1',

    'db' => [
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => 'admin',
        'dbname' => 'ser_qt_api',
        'prefix' => 'story_api_',
    ],

    'cache' => [
        'host' => '127.0.0.1',
        'port' => 11211,
        'password' => '',
        'key_prefix' => 'palo_api_',
    ],

    'spl' => function ($class) {
        $path = sprintf('%s/classes/%s.php', __DIR__, str_replace('\\', '/', $class));

        if (file_exists($path)) {
            return require($path);
        }
    }
];
