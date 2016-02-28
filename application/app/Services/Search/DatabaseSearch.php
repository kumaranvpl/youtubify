<?php namespace App\Services\Search;

use App\Album;
use App\Track;
use App\Artist;

class DatabaseSearch implements SearchInterface {

    /**
     * Search database using given params.
     *
     * @param string  $q
     * @param int     $limit
     * @param string  $type
     *
     * @return array
     */
    public function search($q, $limit = 10) {
        $q = urldecode($q);

        return [
            'artists' => Artist::where('name', 'like', $q.'%')->limit($limit)->get(),
            'albums'  => Album::with('artist')->where('name' ,'like', $q.'%')->limit($limit)->get(),
            'tracks'  => Track::with('album.artist')->where('name', 'like', $q.'%')->limit($limit)->get()
        ];
    }
}