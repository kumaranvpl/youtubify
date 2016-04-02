<?php namespace App\Http\Controllers;

use App;
use Input;
use App\Track;
use App\Artist;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Services\Paginator;
use App\Services\Artist\ArtistSaver;
use App\Services\Artist\SpotifyArtist as Provider;

class ArtistController extends Controller {

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
	public function __construct(Provider $provider, ArtistSaver $saver, Paginator $paginator, Artist $model)
	{
		$this->middleware('admin', ['only' => ['destroy', 'index', 'update']]);

        if (IS_DEMO) {
            $this->middleware('disableOnDemoSite', ['only' => ['destroy', 'update']]);
        }

		$this->provider  = $provider;
		$this->saver     = $saver;
		$this->paginator = $paginator;
		$this->model     = $model;
		$this->settings  = App::make('Settings');
	}

	/**
	 * Paginate all artists.
	 *
	 * @return Collection
	 */
	public function index()
	{
		return $this->paginator->paginate($this->model->with('albums.tracks'), Input::all(), 'artists');
	}

	/**
	 * Update artist.
	 *
	 * @param  int  $id
	 * @return Artist
	 */
	public function update($id)
	{
		$artist = $this->model->findOrFail($id);

        foreach(($input = Input::all()) as $key => $value) {
            if (is_array($value)) unset($input[$key]);
        }

		$artist->fill($input)->save();

		return $artist;
	}

	/**
	 * Return 30 most popular artists (by spotify popularity).
	 *
	 * @return Collection
	 */
	public function getMostPopularArtists()
	{
		return $this->model->orderBy('spotify_popularity', 'desc')->limit(30)->get();
	}

	/**
	 * Return artist data from db or 3rd party service.
	 *
	 * @return Artist
	 */
	public function getArtist()
	{
		if (Input::get('name') === 'Various Artists') abort(404);

        $name = preg_replace('!\s+!', ' ', Input::get('name'));

        $artist = $this->model->with('albums')->where('name', $name)->first();

        if ( ! $artist || ! $artist->fully_scraped || $artist->albums->isEmpty() || $artist->updated_at->addDays($this->settings->get('artist_update_interval')) <= Carbon::now()) {
			$artist = $this->provider->getArtist($name);

			//if provider couldn't find artist, bail with 404
			if ( ! $artist) abort(404);

			$artist = $this->saver->save($artist);
		}

		$artist = $artist->load('albums.tracks', 'similar', 'genres');

        if (Input::get('top-tracks')) {
            $artist->topTracks = $this->getTopTracks($name);
        }

        return $artist;
	}

	/**
	 * Get 20 most popular artists tracks.
	 *
	 * @return Collection
	 */
	public function getTopTracks($name)
	{
		$tracks = Track::with('album.artist')
			->where('artists', 'like', $name.'%')
			->orderBy('spotify_popularity', 'desc')
			->limit(20)
			->get();

		return $tracks;
	}

    /**
     * Get artists biography and images from external sites.
     *
     * @param string $name
     * @return string
     */
    public function getBio($name)
    {
        $client = new Client([
            'base_url' => 'http://developer.echonest.com/api/v4/',
            'timeout' => 8.0,
        ]);

        $response = $client->get('artist/profile?bucket=biographies&bucket=images', ['query' => [
            'api_key' => $this->settings->get('echonest_api_key'),
            'name'    => $name,
            'format'  => 'json',
        ]])->json();

        if (isset($response['response']['artist']['biographies'])) {
            foreach($response['response']['artist']['biographies'] as $bio) {
                if ( ! isset($bio['truncated']) ||  ! $bio['truncated']) {
                    $biography = $bio; break;
                }
            }
        }

        return [
            'bio'    => isset($biography) ? $biography : '',
            'images' => isset($response['response']['artist']['images']) ? array_slice($response['response']['artist']['images'], 0, 10) : []
        ];
    }

	/**
	 * Remove artists from database.
	 *
	 * @return mixed
	 */
	public function destroy()
	{
		if ( ! Input::has('items')) return;

		foreach (Input::get('items') as $artist) {
            $artist = Artist::find($artist['id']);

			if ($artist) {
                foreach($artist->albums as $album) {
                    $album->tracks()->delete();
                }

                $artist->albums()->delete();
                $artist->delete();
            }
		}

		return response(trans('app.deleted', ['number' => count(Input::get('items'))]));
	}

}
