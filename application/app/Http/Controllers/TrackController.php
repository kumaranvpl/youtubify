<?php namespace App\Http\Controllers;

use App;
use Auth;
use Cache;
use Input;
use App\Track;
use Carbon\Carbon;
use App\Http\Requests;
use App\Services\Paginator;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TrackController extends Controller {

	/**
	 * Eloquent Track model instance.
	 *
	 * @var Track
	 */
	private $model;

	/**
	 * Paginator Instance.
	 *
	 * @var Paginator
	 */
	private $paginator;

	public function __construct(Track $track, Paginator $paginator)
	{
        $this->middleware('admin', ['only' => ['destroy', 'index']]);

        if (IS_DEMO) {
            $this->middleware('disableOnDemoSite', ['only' => ['destroy']]);
        }

		$this->model = $track;
		$this->paginator = $paginator;
		$this->settings  = App::make('Settings');
	}

	/**
	 * Return 50 most popular songs.
	 *
	 * @return mixed
	 */
	public function getTopSongs()
	{
        return Cache::remember('tracks.top50', Carbon::now()->addDays($this->settings->get('homepage_update_interval')), function() {
			if ($this->settings->get('top_songs_provider') === 'local') {
                return $this->model->with('album.artist')->orderBy('spotify_popularity', 'desc')->limit(50)->get();
            } else {
                return App::make('App\Services\Discover\SpotifyTopTracks')->get();
            }
		});
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->paginator->paginate($this->model->with('Album'), Input::all(), 'tracks');
	}

	/**
	 * Find track matching given id.
	 *
	 * @param  int  $id
	 * @return Track
	 */
	public function show($id)
	{
		return $this->model->with('album.artist')->findOrFail($id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Track
	 */
	public function update($id)
	{
		$track = $this->model->findOrFail($id);

        //when admin is not logged in only youtube id can be changed
        if ( ! Auth::user() || ! Auth::user()->isAdmin) {
            $input = ['youtube_id' => Input::get('youtube_id')];
        } else {
            $input = Input::all();
        }

		if (isset($input['artists'])) {
			if (is_array($input['artists'])) {
				$input['artists'] = implode('*|*', $input['artists']);
			} else {
				$input['artists'] = str_replace(',', '*|*', $input['artists']);
			}
		}

		$track->fill($input)->save();

		return $track;
	}

	/**
	 * Remove tracks from database.
	 *
	 * @return mixed
	 */
	public function destroy()
	{
		if ( ! Input::has('items')) return;

		$ids = array_map(function($i) { return $i['id']; }, Input::get('items'));

		if ($deleted = $this->model->destroy($ids)) {
			return response(trans('app.deleted', ['number' => $deleted]));
		}
	}

}
