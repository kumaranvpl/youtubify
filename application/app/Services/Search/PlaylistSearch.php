<?php namespace App\Services\Search;

use App\Playlist;

class PlaylistSearch implements SearchInterface {


    /**
     * Search playlists in local database.
     *
     * @param string  $q
     * @param int     $limit
     * @param string  $type
     *
     * @return array
     */
    public function search($q, $limit = 10)
    {
        $playlists = Playlist::with(['users' => function($q) {
            $q->wherePivot('owner', 1);
        }, 'tracks' => function($q) {
            $q->with('album')->limit(1);
        }])
                             ->where('public', 1)
                             ->where('name', 'like', $q.'%')
                             ->has('tracks')
                             ->limit(20)
                             ->get();

       foreach($playlists as $k => $playlist) {
           $playlists[$k]['owner'] = [
               'username' => $playlist['users'][0]->getNameOrEmail(),
               'id'       => $playlist['users'][0]->id
           ];

           if (! $playlist->tracks->isEmpty()) {
               $playlists[$k]['image'] = $playlist['tracks'][0]['album']['image'];
           }

           unset($playlist['users']);
           unset($playlist['tracks']);
       }

        return $playlists->toArray();
    }
}