<?php namespace App\Services\Search;

use Illuminate\Support\Str;
use App\Services\HttpClient;

class SpotifySearch implements SearchInterface {

    /**
     * Http client instance.
     *
     * @var Client
     */
    private $httpClient;

    /**
     * Create new SpotifySearch instance.
     */
    public function __construct(HttpClient $client) {
        $this->httpClient = new HttpClient(['base_url' => 'https://api.spotify.com/v1/']);
    }

    /**
     * Search using spotify api and given params.
     *
     * @param string  $q
     * @param int     $limit
     * @param string  $type
     *
     * @return array
     */
    public function search($q, $limit = 10, $type = 'artist,album,track')
    {
        $response = $this->httpClient->get("search?q=".Str::ascii($q)."*&type=$type&limit=$limit");

        return $this->formatResponse($response);
    }

    /**
     * Format and normalize spotify response for use in our app.
     *
     * @param array   $response
     *
     * @return array
     */
    private function formatResponse($response) {

        $callback = function($item) {
            $formatted = [
                'spotify_id' => $item['id'],
                'name'       => $item['name'],
                'image_small' =>  null,
                'image_large' =>  null,
            ];

            if (isset($item['images']) && count($item['images'])) {
                $formatted['image_small'] = last($item['images'])['url'];
                $formatted['image_large'] = head($item['images'])['url'];
            }

            if (isset($item['popularity'])) {
                $formatted['spotify_popularity'] = $item['popularity'];
            }

            if (isset($item['duration_ms'])) {
                $formatted['duration'] = $item['duration_ms'];
            }

            if (isset($item['track_number'])) {
                $formatted['number'] = $item['track_number'];
            }

            if (isset($item['genres'])) {
                $formatted['genres'] = implode('|', $item['genres']);
            }

            if (isset($item['artists']) && count($item['artists'])) {
                $formatted['artists'] = $item['artists'];
            }

            if (isset($item['album']) && count($item['album'])) {
                $formatted['album'] = $item['album'];

                if ( ! isset($formatted['image'])) {
                    if (isset($item['album']['images'][2]['url'])) {
                        $formatted['image'] = $item['album']['images'][2]['url'];
                    } else {
                        $formatted['image'] = head($item['album']['images']);
                    }

                }
            }

            return $formatted;
        };

        $formatted = ['albums' => [], 'tracks' => [], 'artists' => []];

        if ( ! isset($response['error'])) {
            $formatted['albums']  = $this->getAlbums($response['albums']['items']);
            $formatted['tracks']  = array_map($callback, $response['tracks']['items']);
            $formatted['artists'] = array_map($callback, $response['artists']['items']);
        }

        return $formatted;
    }

    /**
     * Fetch full album objects from spotify and format them.
     *
     * @param array   $albums
     *
     * @return array
     */
    private function getAlbums($albums)
    {
        if (empty($albums)) return [];

        $ids = $this->makeAlbumsIdsString($albums);

        $response = $this->httpClient->get("albums?ids=$ids");

        $formatted = [];

        foreach($response['albums'] as $album) {
            $artist = [
                'name'           => $album['artists'][0]['name'],
                'spotify_id'    => $album['artists'][0]['id'],
                'fully_scraped' => 0
            ];

            $formatted[] = [
                'name'       => $album['name'],
                'popularity' => $album['popularity'],
                'artist'     => $artist,
                'image'      =>  isset($album['images'][1]['url']) ? $album['images'][1]['url'] : null,
            ];
        }

        return $formatted;
    }

    /**
     * Concat spotify ids of given albums into a single string.
     *
     * @param array $albums
     * @return string
     */
    private function makeAlbumsIdsString($albums)
    {
        $ids = [];

        foreach($albums as $album) {
            $ids[] = $album['id'];
        }

        return implode(',', $ids);
    }
}