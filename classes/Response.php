<?php

class Response
{
    const TYPE_JSON = 1;
    const TYPE_TEXT = 2;

    private int $type;

    public function setType(int $type)
    {
        $this->type = $type;

        return $this;
    }

    public function generate($data)
    {
        $data = static::format($data);

        if ($this->type === static::TYPE_JSON) {
            header("Content-Type: application/json");
            return json_encode($data);
        }

        return $data;
    }

    private static function format($data)
    {
        if ($data instanceof Exception) {
            return [
                "response-status" => "failed",
                "error-code" => $data->getCode(),
                "error-reason" => $data->getMessage()
            ];
        }

        if (!is_array($data)) {
            $data = json_decode($data, true);
        }

        $data += ["response-status" => "success", "error-code" => null, "error-reason" => null];

        return $data;
    }
}
