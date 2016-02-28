<?php namespace App\Services\Search;

use App\Album;
use App\Track;
use App\Artist;
use Illuminate\Support\Collection;
use App\Services\Artist\ArtistSaver;

class SearchSaver {

    /**
     * Create new SearchService instance.
     *
     * @param ArtistSaver $artistSaver
     */
    public function __construct(ArtistSaver $artistSaver) {
        $this->artistSaver = $artistSaver;
    }

    /**
     * Save search results from third party service to
     * database and return them as eloquent collection.
     *
     * @param array $data
     * @return array
     */
    public function save($data)
    {
        $prepared = $this->prepareData($data);

        return $this->saveAndGetData($prepared, $data);
    }

    /**
     * Compile all albums, artists and tracks array from given data.
     *
     * @param array $data
     * @return array
     */
    private function prepareData($data)
    {
        $allAlbums = []; $allArtists = []; $allTracks = [];

        //extract artists from albums array
        foreach($data['albums'] as $key => $album) {
            $allArtists[] = $album['artist'];
            $album['artist'] = $album['artist']['name'];
            $allAlbums[] = $album;
        }

        //extract albums and artists from tracks array
        foreach($data['tracks'] as $key => $track) {
            $allArtists[] = $track['artists'][0];
            $allTracks[] = $track;

            $allAlbums[] = [
                'name'   => $track['album']['name'],
                'artist' => $track['artists'][0],
            ];
        }

        foreach($data['artists'] as $artist) {
            $allArtists[] = $artist;
        }

        return ['allArtists' => $allArtists, 'allAlbums' => $allAlbums, 'allTracks' => $allTracks];
    }

    /**
     * Save compiled data to database and return it.
     *
     * @param array $compiled
     * @param array $original
     *
     * @return array
     */
    private function saveAndGetData($compiled, $original)
    {
        $artists = $this->saveAndGetArtists($compiled['allArtists']);
        $albums  = $this->saveAndGetAlbums($compiled['allAlbums'], $artists);
        $tracks  = $this->saveAndGetTracks($compiled['allTracks'], $albums);

        $artists = $artists->filter(function($artist) use($original) {
            return $this->inArray($artist['name'], $original['artists']);
        })->values();

        $albums = $albums->filter(function($album) use($original) {
            return $this->inArray($album['name'], $original['albums']);
        })->values();

        $tracks = $tracks->filter(function($track) use($original) {
            return $this->inArray($track['name'], $original['tracks']);
        })->values();

        return ['albums' => $albums, 'tracks' => $tracks, 'artists' => $artists];
    }

    /**
     * Save compiled tracks to database and return them.
     *
     * @param array $tracks
     * @param array $albums
     *
     * @return Collection
     */
    private function saveAndGetTracks($tracks, $albums)
    {
        $preparedTracks = $this->prepareTracks($tracks, $albums);
        $this->artistSaver->saveOrUpdate($preparedTracks['values'], $preparedTracks['bindings'], 'tracks');

        return Track::with('album.artist')
                ->whereIn('name', $preparedTracks['names'])
                ->whereIn('album_name', $preparedTracks['albumNames'])
                ->get();
    }

    /**
     * Save compiled artists to database and return them.
     *
     * @param array $artists
     * @return Collection
     */
    private function saveAndGetArtists($artists)
    {
        $preparedArtists = $this->prepareArtists($artists);
        $this->artistSaver->saveOrUpdate($preparedArtists['values'], $preparedArtists['bindings'], 'artists');
        return Artist::whereIn('name', $preparedArtists['names'])->get();
    }

    /**
     * Save compiled albums to database and return them.
     *
     * @param array $albums
     * @param array $artists
     *
     * @return Collection
     */
    private function saveAndGetAlbums($albums, $artists)
    {
        $preparedAlbums = $this->prepareAlbums($albums, $artists);
        $this->artistSaver->saveOrUpdate($preparedAlbums['values'], $preparedAlbums['bindings'], 'albums');

        return Album::with('artist', 'tracks')
                ->whereIn('name', $preparedAlbums['names'])
                ->whereIn('artist_id', $preparedAlbums['artistIds'])
                ->get();
    }

    /**
     * Prepare tracks for saving to database via insert on duplicate update query.
     *
     * @param array $tracks
     * @param array $albums
     *
     * @return array
     */
    private function prepareTracks($tracks, $albums)
    {
        $values = [];

        foreach($tracks as $track) {

            $formatted = [
                'name'               => $track['name'],
                'duration'           => $track['duration'],
                'number'             => $track['number'],
                'album_name'         => $track['album']['name'],
                'artists'            => implode('*|*', array_map(function($a) { return $a['name']; }, $track['artists'])),
                'spotify_popularity' => isset($track['spotify_popularity']) ? $track['spotify_popularity'] : null,
                'album_id'           => $this->getId($track['album']['name'], $albums)
            ];

            $values[] = $formatted;
        }

        $albumNames = [];
        $trackNames = [];

        foreach($values as $value) {
            $albumNames[] = $value['album_name'];
            $trackNames[] = $value['name'];
        }

        return ['values' => $values, 'bindings' => array_flatten($values), 'names' => $trackNames, 'albumNames' => $albumNames];
    }

    /**
     * Prepare albums for saving to database via insert on duplicate update query.
     *
     * @param array $albums
     * @param array $artists
     *
     * @return array
     */
    private function prepareAlbums($albums, $artists)
    {
        $values = [];

        foreach($albums as $album) {

            $formatted = [
                'name'               => $album['name'],
                'image'              => isset($album['image']) ? $album['image'] : null,
                'spotify_popularity' => isset($album['popularity']) ? $album['popularity'] : null,
                'fully_scraped'      => 0,
                'artist_id'          => $this->shouldAttachToArtist($album) ? $this->getId($album['artist'], $artists) : 0
            ];

            //if we already have this album in the values array
            //replace it only if current one has more data (image)
            if ( ! isset($values[$album['name']]) || $formatted['image']) {
                $values[$album['name']] = $formatted;
            }
        }

        $artistIds = [];

        foreach($values as $value) {
            $artistIds[] = $value['artist_id'] ?: 0;
        }

        return ['values' => $values, 'bindings' => array_flatten($values), 'names' => array_keys($values), 'artistIds' => $artistIds];
    }

    /**
     * Check if we should attach given album to an artist.
     *
     * @param array $album
     * @return bool
     */
    private function shouldAttachToArtist($album)
    {
        return ! str_contains($album['name'], 'Music From And Inspired By The Motion Picture');
    }

    /**
     * Prepare artists for saving to database via insert on duplicate update query.
     *
     * @param array $artists
     * @return array
     */
    private function prepareArtists($artists)
    {
        $values = [];

        foreach($artists as $artist) {

            $formatted = [
                'name' => $artist['name'],
                'image_small' => isset($artist['image_small']) ? $artist['image_small'] : null,
                'image_large' => isset($artist['image_large']) ? $artist['image_large'] : null,
                'spotify_popularity' => isset($artist['spotify_popularity']) ? $artist['spotify_popularity'] : null,
                //'genres' => isset($artist['genres']) ? $artist['genres'] : [],
                'fully_scraped' => isset($artist['fully_scraped']) ? $artist['fully_scraped'] : 0,
            ];

            //if we already have this artist in the values array
            // replace it only if current one has more data (images)
            if ( ! isset($values[$artist['name']]) || $formatted['image_large']) {
                $values[$artist['name']] = $formatted;
            }
        }

        return ['values' => $values, 'bindings' => array_flatten($values), 'names' => array_keys($values)];
    }

    /**
     * Get item it from collection by given name.
     *
     * @param string|array $name
     * @param array $items
     *
     * @return string|int
     */
    private function getId($name, $items)
    {
        //sometimes we might be passed an artist array
        //so we'll need to extract artist name from it
        $name = is_array($name) ? $name['name'] : $name;

        foreach($items as $item) {
            if ($item['name'] === $name) {
                return $item['id'];
            }
        }
    }

    /**
     * Check if item with given name is in collection.
     *
     * @param string $name
     * @param array $array
     *
     * @return boolean
     */
    private function inArray($name, &$array)
    {
        foreach($array as $key => $item) {
            if (strtolower($item['name']) === strtolower($name)) {
                return true;
            }
        }

        return false;
    }
}