<?php namespace App\Http\Controllers;

use Auth;
use Input;
use Exception;
use Raven_Client;
use Illuminate\Database\QueryException;

class PlaylistTracksController extends Controller {

    /**
     * User model instance.
     *
     * @var \App\User
     */
    private $user;

    /**
     * Create new PLaylistTracksController instance.
     */
    public function __construct()
    {
        $this->middleware('loggedIn');
        $this->user = Auth::user();
    }

    /**
     * Add tracks to playlist.
     *
     * @param {int|string} $playlistId
     * @return \App\Playlist
     */
    public function addTracks($playlistId) {
        $playlist = $this->user->playlists()->findOrFail($playlistId);
        $playlist->id = $playlistId;

        $ids = array_map(function($t) { return $t['id']; }, Input::get('tracks'));

        $alreadyAttachedIds = $playlist->tracks()->whereIn('tracks.id', $ids)->get()->map(function($t) {
            return $t->id;
        })->toArray();

        if ($playlist->is_owner || $this->user->is_admin) {
            $ids = array_diff($ids, $alreadyAttachedIds);
            $playlist->tracks()->attach($ids);
        } else {
            abort(403);
        }

        return $playlist;
    }

    /**
     * Remove a track from playlist.
     *
     * @param {int|string} $playlistId
     * @return \App\Playlist
     */
    public function removeTrack($playlistId) {
        $playlist = $this->user->playlists()->findOrFail($playlistId);
        $playlist->id = $playlistId;

        if ($playlist->is_owner || $this->user->is_admin) {
            $playlist->tracks()->detach(Input::get('track_id'));
        } else {
            abort(403);
        }

        return $playlist;
    }

    public function updateTracksOrder($playlistId) {
        $orderedIds = Input::get('orderedIds');

        if ( ! $orderedIds || empty($orderedIds)) abort(403);

        $orderedIds = array_map(function($position) {
            return ['position' => $position];
        }, Input::get('orderedIds'));

        $playlist = $this->user->playlists()->findOrFail($playlistId);
        $playlist->id = $playlistId;

        if ($playlist->is_owner || $this->user->is_admin) {
            $playlist->tracks()->sync($orderedIds, true);
        } else {
            abort(403);
        }
    }
}
