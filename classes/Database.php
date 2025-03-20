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

        $query = <<<SQL
        SELECT
            p.*,
            d.provider,
            d.provider_bn,
            d.provider_logo,
            d.provider_favicon,
            d.api_endpoint,
            d.collection_slug,
            pt.key_text AS token_code,
            pt.generated_token
        FROM
            `{$prefix}publishers` p
        INNER JOIN
            `{$prefix}publisher_tokens` pt ON pt.publisher_id = p.id
        LEFT JOIN
            `{$prefix}publisher_data_sources` pd ON pd.publisher_id = p.id
        LEFT JOIN
            `{$prefix}datasources` d ON pd.data_source_id = d.id AND d.status = '1'
        LEFT JOIN
            `{$prefix}permissions` pp ON pp.publisher_id = p.id
        WHERE
            p.deleted_at IS NULL
        AND
            pp.is_locked = 0
        AND
            pp.allow_send_request = 1;
        SQL;

        $statement = $this->conn->prepare($query);
        $statement->execute();
        $publishers = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->quintype->set($publishers);
    }
}
