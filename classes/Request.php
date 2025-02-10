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
        $code = $this->quintype->config('service.code');
        ['path' => $path] = parse_url($this->server('REQUEST_URI'));

        return $code . '_' . trim(implode('.', explode('/', $path)), '.');
    }

    /**
     * Verify request
     *
     * @param array $apiConfs
     * @return static
     */
    public function verify(array $apiConfs)
    {
        $appKey = $this->quintype->config('appKey');
        $appToken = $this->server('HTTP_X_APP_TOKEN');

        if (!$appToken) {
            throw new RequestException(1001);
        }

        $compare = fn ($v) => hash_equals(hash_hmac('sha256', $v['app_token'], $appKey), $appToken);
        $appService = array_filter($apiConfs, $compare);

        if (!count($appService)) {
            throw new RequestException(1001);
        }

        $this->quintype->setConfig('service', current($appService));

        return $this;
    }
}
