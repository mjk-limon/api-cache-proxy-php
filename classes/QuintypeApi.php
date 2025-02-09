<?php

use Exceptions\QuintypeApiException;

class QuintypeApi
{
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
     * Api call over router
     *
     * @return void
     */
    public function call()
    {
        $route = $this->quintype->request()->route();

        switch ($route) {
            case 'QT_PA_latest':
                return $this->buildCollection('latest');

            case 'QT_HF_latest':
                return $this->buildCollection('latest-haalfashion');
        }

        throw new QuintypeApiException(404);
    }

    /**
     * Build current condition data
     *
     * @throws \Exception
     * @return void
     */
    public function buildCollection($collectionType)
    {
        $route = $this->quintype->request()->route();
        $apiKey = $this->quintype->request()->server('HTTP_X_API_KEY');

        $data = $this->make('collections/' . $collectionType);

        if ($data === null || $data === false) {
            throw new QuintypeApiException(1002);
        }

        try {
            // Destruct data
            ["updated-at" => $updatedAt, "items" => $items] = $data;

            $items = array_map(function ($item) {
                // Destruct story
                [
                    "last-published-at" => $lastPublishedAt,
                    "subheadline" => $subheadLine,
                    "headline" => $headline,
                    "sections" => $sections,
                    "alternative" => $alternative,
                    "hero-image-s3-key" => $heroImage,
                    "url" => $url,
                    "story-template" => $storyTemplate,
                ] = $item['story'];

                // Get category name
                [["display-name" => $categoryName]] = $sections;

                // Get image url
                $image = $alternative
                    ? ($alternative["home"]["default"]["hero-image"]["hero-image-s3-key"] ?? $heroImage)
                    : $heroImage;

                $image = "https://media.prothomalo.com/" . $image;

                return [
                    "last-published-at" => $lastPublishedAt,
                    "subheadline" => $subheadLine,
                    "headline" => $headline,
                    "category-name" => $categoryName,
                    "image" => $image,
                    "url" => $url,
                    "story-template" => $storyTemplate
                ];
            }, $items);

            $this->quintype->set([
                "updated-at" => $updatedAt,
                "token" => $apiKey,
                "token-receive-time" => time() * 1000,
                "requester-code" => "HF-BL",

                "company-name" => "হাল ফ্যাশন",
                "company-name-en" => "Haal.Fashion",
                "logo" => "https://www.haal.fashion/haalfashion.svg",
                "favicon" => "https://www.haal.fashion/favicon.png",
                "response-status" => "success",
                "error-code" => null,
                "error-reason" => null,

                "item-count" => count($items),
                "items" => $items,
            ]);
        } catch (\Exception $e) {
            throw new QuintypeApiException(1003, null, $e);
        }
    }

    /**
     * Call to api and get response
     *
     * @param string $endpoint
     * @param array $params
     * @return array|null
     */
    private function make(string $endpoint, array $params = [])
    {
        $base = $this->quintype->config('service')['base_url'];
        $target = $base . '/' . ltrim($endpoint, '/');

        $c = curl_init();
        curl_setopt_array($c, [
            CURLOPT_URL => $target . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($c);
        curl_close($c);

        return json_decode($response, true);
    }
}
