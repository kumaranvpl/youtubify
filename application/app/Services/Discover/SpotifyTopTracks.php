<?php namespace App\Services\Discover;

use App\Artist;
use App\Album;
use App\Services\Artist\ArtistSaver;
use App\Services\Artist\SpotifyArtist;
use App\Track;
use App\Traits\AuthorizesWithSpotify;
use App\Services\HttpClient;

class SpotifyTopTracks {

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
        $this->httpClient    = new HttpClient(['base_url' => 'https://spotifycharts.com/api/']);
        $this->spotifyArtist = $spotifyArtist;
        $this->saver         = $saver;

        ini_set('max_execution_time', 0);
    }

    public function get()
    {
        $response = $this->httpClient->get('', ['query' => [
            'type'       => 'regional',
            'country'    => 'global',
            'recurrence' => 'daily',
            'date'       => 'latest',
            'limit'      => 50,
            'offset'     => 0
        ]]);

        $tracks = $response['entries']['items'];
        $tracks = array_map(function($track) { return $track['track']; }, $tracks);
        $tracks = $this->spotifyArtist->formatTracks($tracks, true);

        $artists = [];
        $artistNames = [];

        foreach($tracks as $track) {
            $artistNames[] = $track['artist']['name'];

            $artists[] = [
                'name' => $track['artist']['name'],
                'fully_scraped' => 0
            ];
        }

        $artists = $this->saveArtists($artists, $artistNames);

        $albums = $this->saveAlbums($tracks, $artists);

        return $this->saveTracks($tracks, $albums)->values();
    }

    private function saveTracks($tracks, $albums)
    {
        $tracks = array_values($tracks);

        $originalOrder = [];

        $tempId = str_random(8);

        foreach($tracks as $k => $track) {

            $tracks[$k]['album_id'] = $this->getItemId($track['album']['name'], $albums);
            $tracks[$k]['temp_id'] = $tempId;

            unset($tracks[$k]['artist']);
            unset($tracks[$k]['album']);

            $originalOrder[$track['name']] = $k;
        }

        $this->saver->saveOrUpdate($tracks, array_flatten($tracks), 'tracks');

        $tracks = Track::with('album.artist')->where('temp_id', $tempId)->limit(50)->get();

        return $tracks->sort(function($a, $b) use ($originalOrder) {
            $originalAIndex = isset($originalOrder[$a->name]) ? $originalOrder[$a->name] : 0;
            $originalBIndex = isset($originalOrder[$b->name]) ? $originalOrder[$b->name] : 0;

            if ($originalAIndex == $originalBIndex) {
                return 0;
            }
            return ($originalAIndex < $originalBIndex) ? -1 : 1;
        });
    }

    private function saveAlbums($tracks, $artists)
    {
        $albums = []; $albumNames = []; $albumImages = [];

        foreach($tracks as $track) {
            $image = isset($track['album']['images'][1]['url']) ? $track['album']['images'][1]['url'] : head($track['album']['images'])['url'];

            $albums[] = [
                'name'  => $track['album']['name'],
                'image' => $image,
                'fully_scraped' => 0,
                'artist_id' => $this->getItemId($track['artist']['name'], $artists)
            ];

            $albumNames[]  = $track['album']['name'];
            $albumImages[] = $image;
        }

        $existing = Album::whereIn('name', $albumNames)->whereIn('image', $albumImages)->groupBy('name')->distinct()->get();

        $albumsToFetch = [];

        foreach($albums as $k => $album) {
            if ($this->inArray($album['name'], $existing)) {
                unset($albums[$k]);
            } else {
                $albumsToFetch[] = $album['name'];
            }
        }

        $this->saver->saveOrUpdate($albums, array_flatten($albums), 'albums');

        $new = Album::whereIn('name', $albumsToFetch)->get();

        return $existing->merge($new);

    }

    private function saveArtists($artists, $artistNames)
    {
        $existing = Artist::whereIn('name', $artistNames)->get();

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

        return $existing->merge($new);
    }

    private function getItemId($name, $items)
    {
        foreach($items as $item) {
            if (strtolower($name) == strtolower($item->name)) {
                return $item->id;
            }
        }
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