<?php

$configs = require(__DIR__ . '/configs.php');

date_default_timezone_set($configs['timezone']);
spl_autoload_register($configs['spl']);

$app = new Quintype($configs);
// $app->cache()->flush(); EXIT;

try {
    (function () use ($app) {
        try {
            $app->setConfig('dt', 'configs');
            $app->cache()->pull();
        } catch (Exceptions\DataNotFoundException $e) {
            $app->db()->apiConfigs();
            $app->cache()->store();
        }
    })();

    (function () use ($app) {
        try {
            $apiConfs = $app->toArray();

            $dt = $app->request()
                ->verify($apiConfs)
                ->route();

            $app->setConfig('dt', $dt);
            $app->cache()->pull();
        } catch (Exceptions\DataNotFoundException $e) {
            $app->api()->call();
            $app->cache()->store();
        }
    })();
} catch (Exceptions\QuintypeException $e) {
    return $app->response($e);
} catch (Exception $e) {
    return $app->response($e);
}

return $app->response($app->toArray());
