<?php namespace App\Http\Controllers;

use App;
use Input;
use Cache;
use Exception;
use App\Album;
use App\Artist;
use Carbon\Carbon;
use App\Services\Paginator;
use App\Services\Artist\ArtistSaver;
use App\Services\Album\SpotifyAlbum;
use App\Services\Artist\SpotifyArtist as Provider;

class AlbumController extends Controller {

	/**
	 * External artist provider service.
	 *
	 * @var Provider
	 */
	private $provider;

	/**
	 * Artist db saver instance.
	 *
	 * @var \App\Services\Artist\ArtistSaver
	 */
	private $saver;

	/**
	 * Paginator Instance.
	 *
	 * @var Paginator
	 */
	private $paginator;

	/**
	 * Create new ArtistController instance.
	 *
	 * @param Provider $provider
	 */
	public function __construct(Provider $provider, ArtistSaver $saver, SpotifyAlbum $spotifyAlbum, Paginator $paginator, Album $model)
	{
        $this->middleware('admin', ['only' => ['destroy', 'index', 'update']]);

        if (IS_DEMO) {
            $this->middleware('disableOnDemoSite', ['only' => ['destroy', 'update']]);
        }

		$this->provider     = $provider;
		$this->saver        = $saver;
		$this->spotifyAlbum = $spotifyAlbum;
		$this->paginator    = $paginator;
		$this->model        = $model;
		$this->settings     = App::make('Settings');
	}

	/**
	 * Paginate all albums.
	 *
	 * @return Collection
	 */
	public function index()
	{
		return $this->paginator->paginate($this->model->with('tracks', 'artist'), Input::all(), 'albums');
	}

	/**
	 * Update album.
	 *
	 * @param  int  $id
	 * @return Artist
	 */
	public function update($id)
	{
		$album = $this->model->findOrFail($id);

		$album->fill(Input::all())->save();

		return $album;
	}

	/**
	 * Get most popular albums.
	 *
	 * @return mixed
	 */
	public function getTopAlbums()
	{
		return Cache::remember('albums.top', Carbon::now()->addDays($this->settings->get('homepage_update_interval')), function() {
			return Album::with('artist', 'tracks')
					->has('tracks', '>=', 5)
					->orderBy('spotify_popularity', 'desc')
					->limit(40)
					->get();
		});
	}

	/**
	 * Return latest album releases.
	 *
	 * @return mixed
	 */
	public function getLatestAlbums()
	{
        return Cache::remember('albums.latest', Carbon::now()->addDays($this->settings->get('homepage_update_interval')), function() {
            if ($this->settings->get('latest_albums_strict', false)) {
                return App::make('App\Services\Discover\SpotifyNewReleases')->get();
            } else {
                return Album::with('artist', 'tracks')
                    ->join('artists', 'artists.id', '=', 'albums.artist_id')
                    ->orderBy('release_date', 'desc')
                    ->limit(40)
                    ->select('albums.*')
                    ->get();
            }
		});
	}

	/**
	 * Return artist to who given album belongs
	 * along with all other albums and tracks.
	 *
	 * @return Artist
	 */
	public function getAlbum()
	{
        $artistName = $name = preg_replace('!\s+!', ' ', Input::get('artistName'));
		$albumName  = $name = preg_replace('!\s+!', ' ', Input::get('albumName'));

		//fetch album that isn't attached to any one particular artist
		if ( ! $artistName || $artistName === 'Various Artists') {
			$album = Album::where('name', $albumName)->where('artist_id', 0)->first();

			$this->updateAlbum($album, $artistName, $albumName);

			$album = Album::where('name', $albumName)->where('artist_id', 0)->firstOrFail();

		//fetch specific artists album
		} else {
			$album = Album::where('name', $albumName)->whereHas('artist', function($q) use($artistName) {
				$q->where('name', $artistName);
			})->first();

			$this->updateAlbum($album, $artistName, $albumName);

			$album = Album::where('name', $albumName)->whereHas('artist', function($q) use($artistName) {
				$q->where('name', $artistName);
			})->firstOrFail();
		}

		$album->load('artist', 'tracks');

		return $album;
	}

    /**
     * Update or fetch album from third party site if needed.
     *
     * @param null|Album   $album
     * @param null|string  $artistName
     * @param string       $albumName
     */
	private function updateAlbum($album, $artistName, $albumName)
	{
		if ( ! $album || ! $album->fully_scraped) {
			$data = $this->spotifyAlbum->getAlbumOrFail($artistName, $albumName);

            //if we're fetching specific artists album and that artist is not
            //in our database yet, we will need to fetch the artist first
            if ($artistName && $data['artist'] && (! $album || ! $album->artist)) {
                $artist = $this->provider->getArtistOrFail($artistName);
                $this->saver->save($artist);

                //since fetching artist will get all his
                //albums automatically we can just return
                return;
            }

            try {
                $this->saver->saveAlbums(['albums' => $data['album']], $album ? $album->artist : null, $album ? $album->id : null);
            } catch (Exception $e) {
                //
            }

			if ( ! $album) {
				$album = Album::where('name', $data['album'][0]['name'])->where('release_date', $data['album'][0]['release_date'])->firstOrFail();
			}

			$this->saver->saveTracks($data['album'], null, $album);
		}
	}

	/**
	 * Remove albums from database.
	 *
	 * @return mixed
	 */
	public function destroy()
	{
		if ( ! Input::has('items')) return;

		foreach (Input::get('items') as $album) {
            $album = Album::find($album['id']);

            if ($album) {
                $album->tracks()->delete();
                $album->delete();
            }
		}

		return response(trans('app.deleted', ['number' => count(Input::get('items'))]));
	}
}
