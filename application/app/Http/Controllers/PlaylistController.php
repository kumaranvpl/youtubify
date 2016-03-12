<?php namespace App\Http\Controllers;

use Auth;
use Input;
use Validator;
use App\User;
use App\Playlist;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class PlaylistController extends Controller {

	public function __construct()
	{
		$this->user = Auth::user() ?: new User;
	}

	/**
	 * Follow playlist with currently logged in user.
	 *
	 * @param int $id
	 */
	public function follow($id)
	{
		if ( ! $this->user->playlists()->find($id)) {
			$this->user->playlists()->attach($id, ['owner' => 0]);
			return Playlist::find($id);
		}
	}

	/**
	 * Un-Follow playlist with currently logged in user.
	 *
	 * @param int $id
	 */
	public function unfollow($id)
	{
		if ($this->user->playlists()->find($id)) {
			$this->user->playlists()->detach($id);
		}
	}

	/**
	 * Fetch all playlists user has created or followed.
	 *
	 * @return Collection
	 */
	public function index()
	{
		return $this->user->playlists()->get();
	}

	/**
	 * Return playlist matching given id.
	 *
	 * @param {int|string} $id
	 * @return mixed
	 */
	public function show($id)
	{
		$playlist = Playlist::with('tracks.album.artist')->findOrFail($id);
		$owner    = $playlist->users()->wherePivot('owner', 1)->first();

		//only return playlist if it's public or current user is the owner of it
		if ($owner->id == $this->user->id || $playlist->public) {

			//set owner attribute if needed, otherwise it will error
			// out if playlist is not loaded via user relationship
			if ($owner->id == $this->user->id) {
				$playlist->owner = 1;
			} else {
				$playlist->owner = 0;
			}

			$playlist->createdBy = $owner->getNameOrEmail();
			$playlist->creatorId = $owner->id;

			return $playlist;
		}

		abort(403);
	}

	/**
	 * Create a new playlist.
	 *
	 * @return Playlist
	 */
	public function store(Request $request)
	{
		Validator::extend('uniqueName', function($attribute, $value) {
			return ! $this->user->playlists()->where('name', $value)->first();
		});

		$this->validate($request, [
			'name' => 'required|uniqueName|max:255',
		], ['unique_name' => trans('app.playlistNameExists')]);

		$playlist = $this->user->playlists()->create(Input::all(), ['owner' => 1]);
		$playlist->owner = 1;

		return $playlist;
	}

	/**
	 * Update playlist.
	 *
	 * @param  int  $id
	 * @return Playlist
	 */
	public function update($id)
	{
		$playlist = Playlist::findOrFail($id);
		$owner    = $playlist->users()->wherePivot('owner', 1)->first();

		if ($owner->id == $this->user->id || $this->user->is_admin) {
			$playlist->fill(Input::all())->save();
			$playlist->owner = 1;
		} else {
			abort(403);
		}

		return $playlist;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$playlist = Playlist::findOrFail($id);
		$owner    = $playlist->users()->wherePivot('owner', 1)->first();

		if ($owner->id == $this->user->id || $this->user->is_admin) {
			$playlist->tracks()->detach();
			$playlist->delete();
		}
	}

}
