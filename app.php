<?php

$configs = require(__DIR__ . '/configs.php');

date_default_timezone_set($configs['timezone']);
spl_autoload_register($configs['spl']);

$app = new Quintype($configs);

try {
    $app->cache()->get();
} catch (\Exception $e) {
    try {
        $app->api()->call();
        $app->cache()->store();
    } catch (\Exception $e) {
        return $app->response(['error' => $e->getCode()]);
    }
}

return $app->response($app->data);
