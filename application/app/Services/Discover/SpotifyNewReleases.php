<?php namespace App\Services\Discover;

use App\Artist;
use App\Album;
use App\Services\Artist\ArtistSaver;
use App\Services\Artist\SpotifyArtist;
use App\Traits\AuthorizesWithSpotify;
use App\Services\HttpClient;

class SpotifyNewReleases {

    use AuthorizesWithSpotify;

    /**
     * HttpClient instance.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Create new SpotifyArtist instance.
     */
    public function __construct(SpotifyArtist $spotifyArtist, ArtistSaver $saver)
    {
        $this->httpClient    = new HttpClient(['base_url' => 'https://api.spotify.com/v1/']);
        $this->spotifyArtist = $spotifyArtist;
        $this->saver         = $saver;

        ini_set('max_execution_time', 0);
    }

    public function get()
    {
        $this->authorize();

        $response = $this->httpClient->get('browse/new-releases?country=US&limit=40', ['headers' => ['Authorization' => 'Bearer '.$this->token]]);

        $albums = $this->spotifyArtist->getAlbums(null, $response['albums']);

        $artists = []; $names = [];

        foreach($albums as $album) {
            $artists[] = [
                'name' => $album['artist']['name'],
                'fully_scraped' => 0
            ];

            $names[] = $album['artist']['name'];
        }

        $existing = Artist::whereIn('name', $names)->get();

        $artistsToFetch = [];

        foreach($artists as $k => $artist) {
            if ($this->inArray($artist['name'], $existing)) {
                unset($artists[$k]);
            } else {
                $artistsToFetch[] = $artist['name'];
            }
        }

        $this->saver->saveOrUpdate($artists, array_flatten($artists), 'artists');

        $new = Artist::whereIn('name', $artistsToFetch)->get();

        $artists = $existing->merge($new);

        $albumNames = [];

        foreach($albums as $k => $album) {
            $model = $artists->filter(function($artist) use($album) { return strtolower($artist->name) == strtolower($album['artist']['name']); })->first();

            $id = $model ? $model->id : false;

            $albums[$k]['artist_id'] = $id;
            $albums[$k]['fully_scraped'] = null;

            unset($albums[$k]['artist']);

            if ( ! $id) {
                unset($albums[$k]);
                continue;
            }

            $albumNames[] = $album['name'];
        }

        $this->saver->saveAlbums(['albums' => $albums]);

        $albums = Album::with('artist', 'tracks')->whereIn('name', $albumNames)->orderBy('release_date', 'desc')->limit(40)->get();

        return $albums->sortByDesc('artist.spotify_popularity')->values();
    }

    private function inArray($name, $items)
    {
        foreach($items as $item) {
            if (strtolower($name) == strtolower($item->name)) {
                return true;
            }
        }
    }
}