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
    public function publishers()
    {
        $prefix = $this->config('prefix');

        $query = "SELECT p.*, pp.*, pt.generated_token, d.api_endpoint, d.collection_slug " .
            "FROM `" . $prefix . "publishers` p " .
            "LEFT JOIN `" . $prefix . "publisher_tokens` pt ON pt.publisher_id = p.id " .
            "LEFT JOIN `" . $prefix . "permissions` pp ON pp.publisher_id = p.id " .
            "JOIN `" . $prefix . "publisher_data_sources` pd ON pd.publisher_id = p.id " .
            "JOIN `" . $prefix . "datasources` d ON pd.data_source_id = d.id AND d.status = '1'";

        $statement = $this->conn->prepare($query);
        $statement->execute();
        $publishers = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->quintype->set($publishers);
    }
}
