<?php

use Exceptions\DataNotFoundException;

class Cache
{
    /**
     * Cache instance
     *
     * @var \Memcached
     */
    private $cache;

    /**
     * Quintype api instance
     *
     * @var Quintype
     */
    private $quintype;

    /**
     * @var array
     */
    private $settings = [
        'host' => '127.0.0.1',
        'port' => 11211,
        'password' => '',
        'key' => '',
    ];

    /**
     * @param Quintype $quintype
     */
    public function __construct(Quintype $quintype)
    {
        $this->quintype = $quintype;
        $this->settings = array_merge($this->settings, $this->quintype->config('cache'));

        $this->cache = new \Memcached;
        $this->cache->addServer($this->config('host'), $this->config('port'));
    }

    /**
     * Get database config
     *
     * @param string $key
     * @return mixed
     */
    private function config(string $key)
    {
        return $this->settings[$key];
    }

    /**
     * Get cache key
     *
     * @return mixed
     */
    private function cacheKey()
    {
        $keyPrefix = $this->config('key_prefix');
        $key = $this->quintype->config('dt');

        return $keyPrefix . $key;
    }

    /**
     * Ping cache driver
     *
     * @return false|string
     */
    public function ping()
    {
        $this->cache->set('ping', 'pong');
        return $this->cache->get('ping');
    }

    /**
     * Flush all cache
     *
     * @return false|string
     */
    public function flush()
    {
        return $this->cache->flush();
    }

    /**
     * @throws \Exception
     * @return true
     */
    public function pull()
    {
        $data = $this->cache->get($this->cacheKey());

        if ($data !== false) {
            return $this->quintype->set($data);
        }

        throw new DataNotFoundException;
    }

    /**
     * @return void
     */
    public function store()
    {
        $quintypeArray = $this->quintype->toArray();
        $expireSeconds = $this->quintype->config('service.request_interval_in_mins') * 60;

        $this->cache->set($this->cacheKey(), json_encode($quintypeArray), $expireSeconds);
    }
}
