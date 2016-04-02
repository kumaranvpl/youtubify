<?php namespace App\Http\Controllers;

use DB;
use Cache;
use Input;
use App\Genre;
use Carbon\Carbon;
use App\Services\Paginator;

class GenreController extends Controller {

	/**
	 * Paginator Instance.
	 *
	 * @var Paginator
	 */
	private $paginator;

	public function __construct(Paginator $paginator)
	{
		$this->paginator = $paginator;
	}

	/**
	 * Get genres and artists related to it.
	 *
	 * @param string $names
	 * @return Collection
	 */
	public function getGenres($names)
	{
		$names    = str_replace(', ', ',', $names);
        $orderBy  = implode(',', array_map(function($v) { return "'".$v."'"; }, explode(',', $names)));
		$cacheKey = 'genres.'.Input::get('limit', 20).$names;

		if (Cache::has($cacheKey)) {
			return Cache::get($cacheKey);
		}

		$genres = Genre::whereIn('name', explode(',', $names))->orderByRaw(DB::raw("FIELD(name, $orderBy)"))->get();

		if ($genres->isEmpty()) {
			abort(404);
		}

		//limit actors loaded for genres
		$genres->map(function ($genre) {
			$genre->load(['artists' => function ($q) {
				$q->limit(Input::get('limit', 20));
			}]);

			return $genre;
		});

		Cache::put($cacheKey, $genres, Carbon::now()->addDays(1));

		return $genres;
	}

	/**
	 * Paginate given genres artists.
	 *
	 * @param string $name
	 * @return array
	 */
	public function paginateArtists($name)
	{
		$genre = Genre::where('name', $name)->firstOrFail();
		$input = Input::all(); $input['itemsPerPage'] = 20;
		$artists = $this->paginator->paginate($genre->artists(), $input, 'artists')->toArray();

		return ['genre' => $genre, 'artists' => $artists];
	}
}
