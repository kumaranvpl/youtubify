<?php namespace App\Services\Search;

use App;
use GuzzleHttp\Client;
use App\Services\HttpClient;
use League\Flysystem\Exception;

class YoutubeSearch {

    /**
     * Guzzle http client instance.
     *
     * @var Client
     */
    private $httpClient;

    /**
     * Create new YoutubeSearch instance.
     */
    public function __construct() {
        $this->httpClient = new HttpClient([
            'base_url' => 'https://www.googleapis.com/youtube/v3/',
        ]);

        $this->settings = App::make('Settings');
    }

    /**
     * Search using youtube api and given params.
     *
     * @param string $artist
     * @param string $artist
     * @param int    $limit
     * @param string $type
     *
     * @return array
     */
    public function search($artist, $track, $limit = 10, $type = 'video')
    {
        $params = $this->getParams($artist, $track, $limit, $type);

        try {
            $response = $this->httpClient->get('search', ['query' => $params, 'config' => ['curl' => [CURLOPT_REFERER => url()]]]);
        } catch(\Exception $e) {
            $response = [];
        }

        return $this->formatResponse($response);
    }

    public function getRelatedVideos($youtubeId, $limit = 20) {
        $response = $this->httpClient->get('search', ['query' => [
            'key' => $this->settings->get('youtube_api_key'),
            'relatedToVideoId' => $youtubeId,
            'part' => 'snippet',
            'maxResults' => $limit,
            'type' => 'video',
            'videoEmbeddable' => 'true',
        ]]);

        return $this->formatResponse($response);
    }

    private function getParams($artist, $track, $limit, $type)
    {
        $track = str_replace(' - Without Skits', '', $track);

        if (str_contains($track, ['(', ')'])) {
            $track = trim(explode('(', $track)[0]);
        }

        $params = [
            'q' => "$artist - $track",
            'key' => $this->settings->get('youtube_api_key'),
            'part' => 'snippet',
            'maxResults' => $limit,
            'type' => $type,
            'videoEmbeddable' => 'true',
        ];

        $regionCode = $this->settings->get('youtube_region_code');

        if ($regionCode && $regionCode !== 'none') {
            $params['regionCode'] = strtoupper($regionCode);
        }

        return $params;
    }

    /**
     * Format and normalize youtube response for use in our app.
     *
     * @param array $response
     * @return array
     */
    private function formatResponse($response) {

        $formatted = [];

        if ( ! isset($response['items'])) return $formatted;

        foreach($response['items'] as $item) {
            $formatted[] = ['name' => $item['snippet']['title'], 'id' => $item['id']['videoId']];
        }

        if (empty($formatted)) return $formatted;

        return count($formatted) < 2 ? $formatted[0] : $formatted;
    }
}