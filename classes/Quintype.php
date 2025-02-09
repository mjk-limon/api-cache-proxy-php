<?php

final class Quintype
{
    /**
     * @var array
     */
    protected $settings = [
        'dt' => '',
        'service' => [],
        'db' => [],
        'cache' => [],
    ];

    /**
     * @var array
     */
    public $data = [];

    /**
     * Api instance
     *
     * @var QuintypeApi
     */
    private $api;

    /**
     * Database instance
     *
     * @var Database
     */
    private $db;

    /**
     * Cache instance
     *
     * @var Cache
     */
    private $cache;

    /**
     * Request params
     *
     * @var array
     */
    private $request;

    /**
     * @param array $configs
     */
    public function __construct($configs)
    {
        $this->settings = array_merge($this->settings, $configs);
    }

    public function setConfig($key, $value)
    {
        $this->settings[$key] = $value;
    }

    /**
     * Get config value
     *
     * @param string $key
     * @return mixed
     */
    public function config(string $key)
    {
        return $this->settings[$key];
    }

    /**
     * Set data
     *
     * @param mixed $data
     * @param mixed $key
     * @return void
     */
    public function set($data, $key = null)
    {
        if (!is_array($data)) {
            $data = json_decode($data, true);
        }

        if ($key === null) {
            $this->data = $data;
            return;
        }

        $this->data[$key] = $data;
    }

    /**
     * Cast builded object properties to array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Get database instance
     *
     * @return Database
     */
    public function router()
    {
        if (!$this->db) {
            $this->db = new Database($this);
        }

        return $this->db;
    }

    /**
     * Get database instance
     *
     * @return Database
     */
    public function db()
    {
        if (!$this->db) {
            $this->db = new Database($this);
        }

        return $this->db;
    }

    /**
     * Get cache instance
     *
     * @return Cache
     */
    public function cache()
    {
        if (!$this->cache) {
            $this->cache = new Cache($this);
        }

        return $this->cache;
    }

    /**
     * Get api instance
     *
     * @return QuintypeApi
     */
    public function api()
    {
        if (!$this->api) {
            $this->api = new QuintypeApi($this);
        }

        return $this->api;
    }

    /**
     * Get request object
     *
     * @return Request
     */
    public function request()
    {
        if (!$this->request) {
            $this->request = new Request($this);
        }

        return $this->request;
    }

    /**
     * Get response
     *
     * @param mixed $data
     * @return true
     */
    public function response($data)
    {
        session_cache_limiter('public');

        echo (new Response)
            ->setType(Response::TYPE_JSON)
            ->generate($data);
    }
}
