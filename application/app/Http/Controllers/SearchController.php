<?php namespace App\Http\Controllers;

use App;
use Cache;
use Input;
use Carbon\Carbon;
use App\Services\Search\SearchSaver;
use App\Services\Search\YoutubeSearch;

class SearchController extends Controller {

    /**
     * Create new SearchController instance.
     *
     * @param YoutubeSearch $audio
     */
	public function __construct(YoutubeSearch $audio, SearchSaver $saver)
    {
        $this->provider = ucfirst(App::make('Settings')->get('search_provider', 'spotify'));
        $namespace = 'App\Services\Search\\'.$this->provider.'Search';

        $this->generalSearch = App::make($namespace);
        $this->audioSearch   = $audio;
        $this->saver         = $saver;
	}

    /**
     * Use active search provider to search for
     * songs, albums and artists matching given query.
     *
     * @param string $q
     * @return array
     */
    public function search($q)
    {
        $limit = Input::get('limit', 3);

        return Cache::remember('search.'.$q.$limit, Carbon::now()->addDays(3), function() use($q, $limit) {
            $results = $this->generalSearch->search($q, $limit);

            if ($this->provider === 'database') {
                return $results;
            }

            return $this->saver->save($results);
        });
    }

    /**
     * Search for audio matching given query.
     *
     * @param string $artist
     * @param string $artist
     *
     * @return array
     */
    public function searchAudio($artist, $track)
    {
        return $this->audioSearch->search($artist, $track, 1);
    }

}
