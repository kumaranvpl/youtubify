<?php namespace App\Services\Artist;

use DB;
use Exception;
use App\Genre;
use App\Album;
use App\Artist;
use Illuminate\Support\Str;

class ArtistSaver {

    /**
     * Save artist to database and return it.
     * 
     * @param  array $data
     * @return Artist
     */
    public function save($data)
    {
        $artist = Artist::whereName($data['mainInfo']['name'])->first();

        if ( ! $artist) {
            $artist = Artist::create($data['mainInfo']);
        } else {
            $artist->fill($data['mainInfo'])->save();
        }

        $this->saveAlbums($data, $artist);

        if (isset($data['albums'])) {
            $this->saveTracks($data['albums'], $artist);
        }

        if (isset($data['similar'])) {
            $this->saveSimilar($data['similar'], $artist);
        }

        if (isset($data['genres']) && ! empty($data['genres'])) {
            $this->saveGenres($data['genres'], $artist);
        }

        return $artist;
    }

    /**
     * Save and attach artist genres.
     *
     * @param array $genres
     * @param Artist $artist
     */
    public function saveGenres($genres, $artist) {

        $existing = Genre::whereIn('name', $genres)->get();
        $ids = [];

        foreach($genres as $genre) {
            $dbGenre = $existing->filter(function($item) use($genre) { return $item->name === $genre; })->first();

            //genre doesn't exist in db yet, so we need to insert it
            if ( ! $dbGenre) {
                try {
                    $dbGenre = Genre::create(['name' => $genre]);
                } catch(Exception $e) {
                    continue;
                }
            }

            $ids[] = $dbGenre->id;
        }

        //attach genres to artist
        $artist->genres()->sync($ids, false);
    }

    /**
     * Save artists similar artists to database.
     *
     * @param $similar
     * @param $artist
     * @return void
     */
    public function saveSimilar($similar, $artist)
    {
        $names = array_map(function($item) { return $item['name']; }, $similar);

        //insert similar artists that don't exist in db yet
        $this->saveOrUpdate($similar, array_flatten($similar), 'artists');

        //get ids in database for artist we just inserted
        $ids = Artist::whereIn('name', $names)->lists('id');

        //attach ids to given artist
        $artist->similar()->sync($ids);
    }

    /**
     * Save artist albums to database.
     * 
     * @param  array $data  
     * @param  Artist|null $artist
     * $param  int|null
     * @return void      
     */
    public function saveAlbums($data, $artist = null, $albumId = null)
    {
        if (isset($data['albums']) && count($data['albums'])) {
            $b = $this->prepareAlbumBindings($data['albums'], $artist, $albumId);
            $this->saveOrUpdate($b['values'], $b['bindings'], 'albums');
        }
    }

    /**
     * Save albums tracks to database.
     * 
     * @param  array $albums
     * @param  Artist|null $artist
     * @param  Album|null $trackAlbum
     * @return void
     */
    public function saveTracks($albums, $artist, $tracksAlbum = null)
    {
        if ( ! $albums || ! count($albums)) return;

        $tracks = [];

        foreach($albums as $album) {
            if ($tracksAlbum) {
                $id = $tracksAlbum['id'];
            } else {
                $id = $this->getIdFromAlbumsArray($album['name'], $artist['albums']);
            }

            foreach($album['tracks'] as $track) {
                $track['album_id'] = $id;
                $tracks[] = $track;
            }
        }

        $this->saveOrUpdate($tracks, array_flatten($tracks), 'tracks');
    }

    private function getIdFromAlbumsArray($name, $albums) {
        $id = false;

        foreach($albums as $album) {
            if ($name === $album['name']) {
                $id = $album['id']; break;
            }
        }

        if ( ! $id) {
            foreach($albums as $album) {
                if (Str::slug($name) == Str::slug($album['name'])) {
                    $id = $album['id']; break;
                }
            }
        }

        return $id;
    }

    /**
     * Unset tracks key from album arrays and flatten them into single array.
     * 
     * @param  array $albums
     * @param  Artist|null $artist
     * @param  int|null $albumId
     * @return array       
     */
    private function prepareAlbumBindings($albums, $artist = null, $albumId = null)
    {
        $flat = [];

        foreach($albums as $k => $album) {
            if (isset($albums[$k]['tracks'])) unset($albums[$k]['tracks']);

            if ( ! isset($albums[$k]['artist_id']) || ! $albums[$k]['artist_id']) {
                $albums[$k]['artist_id'] = $artist ? $artist->id : 0;
            }

            //can't insert null into auto incrementing id because
            //mysql will increment the id instead of keeping the old one
            if ($albumId) {
                $albums[$k]['id'] = $albumId;
            }

            foreach($albums[$k] as $name => $data) {
                if ($name !== 'tracks') {
                    $flat[] = $data;
                }
            }
        }

        return ['values' => $albums, 'bindings' => $flat];
    }

    /**
     * Compiles insert on duplicate update query for multiple inserts.
     *
     * @param array  $values
     * @param array  $bindings
     * @param string $table
     *
     * @return void
     */
    public function saveOrUpdate(array $values, $bindings, $table)
    {
        if (empty($values)) return;

        $first = head($values);

        //count how many inserts we need to make
        $amount = count($values);

        //count in how many columns we're inserting
        $columns = array_fill(0, count($first), '?');

        $columns = '(' . implode(', ', $columns) . ') ';

        //make placeholders for the amount of inserts we're doing
        $placeholders = array_fill(0, $amount, $columns);
        $placeholders = implode(',', $placeholders);

        $updates = [];

        //construct update part of the query if we're trying to insert duplicates
        foreach ($first as $column => $value) {
            $updates[] = "$column = COALESCE(values($column), $column)";
        }

        $prefixed = DB::getTablePrefix() ? DB::getTablePrefix().$table : $table;

        $query = "INSERT INTO {$prefixed} " . '(' . implode(',' , array_keys($first)) . ')' . ' VALUES ' . $placeholders .
            'ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);

        DB::statement($query, $bindings);
    }
}