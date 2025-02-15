<?php

class Database
{
    /**
     * PDO Instance
     *
     * @var \PDO
     */
    private $conn;

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
        'host' => '',
        'user' => '',
        'password' => '',
        'dbname' => '',
        'prefix' => '',
    ];

    /**
     * @param Quintype $quintype
     */
    public function __construct(Quintype $quintype)
    {
        $this->quintype = $quintype;
        $this->settings = array_merge($this->settings, $this->quintype->config('db'));

        $this->conn = new PDO(
            'mysql:host=' . $this->config('host') . ';dbname=' . $this->config('dbname'),
            $this->config('user'),
            $this->config('password'),
        );
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
     * Get api configs from database
     *
     * @return void
     */
    public function apiConfigs()
    {
        $query = "SELECT * FROM `" . $this->config('prefix') . "settings`";

        $statement = $this->conn->prepare($query);
        $statement->execute();
        $configs = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->quintype->set($configs);
    }
}
