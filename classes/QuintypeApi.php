<?php

use Exceptions\QuintypeApiException;
use Exceptions\QuintypeException;

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
        $apis = $this->quintype->config('service.apis');

        $path = $this->quintype->request()->path();
        $path = pathinfo($path, PATHINFO_FILENAME);

        if ($slug = $apis[$path] ?? null) {
            return $this->buildCollection($slug);
        }

        throw new QuintypeApiException(404);
    }

    public function sandboxed()
    {
        $is_sandboxed = $this->quintype->config('service.is_sandboxed');

        if ($is_sandboxed) {
            $apis = $this->quintype->config('service.apis');

            $path = $this->quintype->request()->path();
            $path = pathinfo($path, PATHINFO_FILENAME);
            $json = __DIR__ . '/../samples/' . ($apis[$path] ?? '') . '.json';

            if (file_exists($json)) {
                $data = file_get_contents($json);
                $this->quintype->set($data);

                return true;
            }

            throw new QuintypeApiException(404);
        }

        return false;
    }

    /**
     * Build current condition data
     *
     * @throws \Exception
     * @return void
     */
    public function buildCollection($collectionType)
    {
        $data = $this->make('collections/' . $collectionType, ['limit' => 30]);

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
                "item-count" => count($items),
                "items" => $items,
            ]);
        } catch (\Exception $e) {
            throw new QuintypeException(1004, null, $e);
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
        $base = $this->quintype->config('base_url');
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
