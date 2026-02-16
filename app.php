<?php

$configs = require(__DIR__ . '/configs.php');

date_default_timezone_set($configs['timezone']);
spl_autoload_register($configs['spl']);

$app = new Quintype($configs);
// $app->cache()->flush(); exit;

try {
    (function () use ($app) {
        try {
            $app->setConfig('dt', 'configs');
            $app->cache()->pull();
        } catch (Exceptions\DataNotFoundException $e) {
            $app->db()->publishers();
            $app->cache()->store(30 * 60);
        }
    })();

    (function () use ($app) {
        try {
            $publishers = $app->toArray();

            $dt = $app->request()
                ->verify($publishers)
                ->route();

            $app->setConfig('dt', $dt);

            if ($app->api()->sandboxed()) {
                return false;
            }

            $app->cache()->pull();
            $app->request()->verifyRateLimit();
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
