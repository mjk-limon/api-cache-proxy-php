<?php

$configs = require(__DIR__ . '/configs.php');

date_default_timezone_set($configs['timezone']);
spl_autoload_register($configs['spl']);

$app = new Quintype($configs);

try {
    $app->setConfig('dt', 'configs');
    $app->cache()->pull();
} catch (\Exception $e) {
    $app->db()->apiConfigs();
    $app->cache()->store();
}

try {
    $apiConfs = $app->toArray();

    $dt = $app->request()
        ->verify($apiConfs)
        ->route();

    $app->setConfig('dt', $dt);
    $app->cache()->pull();
} catch (\Exception $e) {
    $code = $e->getCode();

    switch ($code) {
        case 103:
            try {
                $app->api()->call();
                $app->cache()->store();
            } catch (\Exception $e) {
                return $app->response(['error' => $e->getCode()]);
            }
            break;

        default:
            return $app->response(['error' => $e->getCode()]);
    }
}

return $app->response($app->data);
