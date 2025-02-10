<?php

$configs = require(__DIR__ . '/configs.php');
spl_autoload_register($configs['spl']);

$app = new Quintype($configs);
$app->db()->apiConfigs();

$configs = $app->toArray();
$appKey = $app->config('appKey');

foreach ($configs as $config) {
    echo $config['title'] . " Token: ";
    echo hash_hmac('sha256', $config['app_token'], $appKey);
    echo PHP_EOL;
}

exit(1);