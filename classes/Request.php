<?php

use Exceptions\RequestException;

class Request
{
    /**
     * Quintype api instance
     *
     * @var Quintype
     */
    private $quintype;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var array
     */
    private $server = [];

    /**
     * @param Quintype $quintype
     */
    public function __construct(Quintype $quintype)
    {
        $this->params = $_REQUEST;
        $this->server = $_SERVER;
        $this->quintype = $quintype;
    }

    /**
     * Get request param
     *
     * @param string $key
     * @param mixed $def
     * @return mixed
     */
    public function param($key, $def = null)
    {
        return $this->params[$key] ?? $def;
    }

    /**
     * Get request property
     *
     * @param string $key
     * @return mixed
     */
    public function server($key)
    {
        return $this->server[$key] ?? '';
    }

    /**
     * Get requested route
     *
     * @return string
     */
    public function route(): string
    {
        ['code' => $code] = $this->quintype->config('service');
        ['path' => $path] = parse_url($this->server('REQUEST_URI'));

        return $code . '_' . trim(implode('.', explode('/', $path)), '.');
    }

    public function verify(array $apiConfs)
    {
        $appToken = $this->server('HTTP_X_APP_TOKEN');

        if (!$appToken) {
            throw new RequestException(1001);
        }

        $appIndex = array_search($appToken, array_column($apiConfs, 'app_token'));

        if ($appIndex === false) {
            throw new RequestException(1001);
        }

        $this->quintype->setConfig('service', $apiConfs[$appIndex]);

        return $this;
    }
}
