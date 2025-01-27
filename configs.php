<?php

return [
    'base' => 'https://pa-bn.test.api/api/v1',
    'apiKey' => '12345',
    'cityIds' => [28143 => 'Dhaka'],

    'db' => [
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => 'admin',
        'dbname' => 'ser_weather',
        'prefix' => 'wt_',
    ],

    'cache' => [
        'host' => '127.0.0.1',
        'port' => 11211,
        'password' => '',
        'key_prefix' => 'weather_data_city_',
    ],

    'timezone' => 'Asia/Dhaka',

    'spl' => function ($class) {
        $path = sprintf('%s/Classes/%s.php', __DIR__, $class);

        if (file_exists($path)) {
            return require($path);
        }
    }
];
