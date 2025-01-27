<?php

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
        ['path' => $path] = parse_url($this->server('REQUEST_URI'));
        return trim(implode('.', explode('/', $path)), '.');
    }

    public function verify()
    {
        if ($this->server('HTTP_X_API_KEY') !== $this->quintype->config('apiKey')) {
            throw new \Exception('', 401);
        }
    }
}
