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

    public function path()
    {
        return parse_url($this->server('REQUEST_URI'), PHP_URL_PATH);
    }

    /**
     * Get requested route
     *
     * @return string
     */
    public function route(): string
    {
        $path = $this->path();
        $code = $this->quintype->config('service.code');

        return $code . '_' . trim(implode('.', explode('/', $path)), '.');
    }

    /**
     * Verify request
     *
     * @param array $publishers
     * @return static
     */
    public function verify(array $publishers)
    {
        $appToken = $this->server('HTTP_X_APP_TOKEN');
        $requestMethod = $this->server('REQUEST_METHOD');

        if (strtolower($requestMethod) !== 'post') {
            throw new RequestException(400);
        }

        if (!$appToken) {
            throw new RequestException(1001);
        }

        $service = array_reduce($publishers, function ($c, $v) use ($appToken) {
            if (
                !$v['is_locked'] &&
                $v['allow_send_request'] &&
                $v['generated_token'] &&
                hash_equals($v['generated_token'], $appToken)
            ) {
                ['api_endpoint' => $endpoint, 'collection_slug' => $slug] = $v;
                unset($v['api_endpoint'], $v['collection_slug']);

                $v['apis'] = array_merge($c['apis'] ?? [], [$endpoint => $slug]);

                return $v;
            }

            return $c;
        }, []);

        if (!count($service)) {
            throw new RequestException(1001);
        }

        $this->quintype->setConfig('service', $service);
        return $this;
    }
}
