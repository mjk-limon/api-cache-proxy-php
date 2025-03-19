<?php

class Response
{
    const TYPE_JSON = 1;
    const TYPE_TEXT = 2;

    /**
     * Response type
     *
     * @var integer
     */
    private int $type;

    /**
     * Quintype api instance
     *
     * @var Quintype
     */
    private $quintype;

    /**
     * @param Quintype $quintype
     */
    public function __construct(Quintype $quintype)
    {
        $this->quintype = $quintype;
    }

    /**
     * @param integer $type
     * @return static
     */
    public function setType(int $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function generate($data)
    {
        $data = $this->format($data);

        if ($this->type === static::TYPE_JSON) {
            header("Content-Type: application/json");
            return json_encode($data);
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @return array
     */
    private function format($data)
    {
        $service = $this->quintype->config('service');
        $apiKey = $this->quintype->request()->server('HTTP_X_APP_TOKEN');

        $template = [
            "token" => $apiKey,
            "token-receive-time" => time() * 1000,
            "requester-code" => $service['code'] ?? '',

            "company-name" => $service['company_name'] ?? '',
            "company-name-en" => $service['title_en'] ?? '',
            "logo" => $service['logo'] ?? '',
            "favicon" => $service['favicon'] ?? '',

            "response-status" => "success",
            "error-code" => null,
            "error-reason" => null,
        ];

        if ($data instanceof Exception) {
            return array_merge($template, [
                "response-status" => "failed",
                "error-code" => $data->getCode(),
                "error-reason" => $data->getMessage()
            ]);
        }

        if (!is_array($data)) {
            $data = json_decode($data, true);
        }

        $data = array_merge($template, $data);

        return $data;
    }
}
