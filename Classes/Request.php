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

    public function __construct(Quintype $quintype)
    {
        $this->params = $_REQUEST;
        $this->server = $_SERVER;
        $this->quintype = $quintype;
    }

    public function param($key, $def = null)
    {
        return $this->params[$key] ?? $def;
    }

    public function server($key)
    {
        return $this->server[$key] ?? '';
    }

    public function route()
    {
        ['path' => $path] = parse_url($this->server('REQUEST_URI'));
        return trim(implode('.', explode('/', $path)), '.');
    }
}
