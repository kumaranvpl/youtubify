<?php namespace App\Services\Crawlers;

use App\Artist;
use App\Services\HttpClient;
use App\Services\Artist\ArtistSaver;
use App\Services\Artist\SpotifyArtist;

class SpotifyCrawler {

	private $saver;

	private $artistService;

	/**
	 * HttpClient instance.
	 *
	 * @var HttpClient
	 */
	private $httpClient;

	public function __construct(ArtistSaver $saver, SpotifyArtist $artistService)
	{
        $this->saver = $saver;
		$this->artistService = $artistService;
		$this->httpClient = new HttpClient(['base_url' => 'https://api.spotify.com/v1/'], true);

		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '256M');
	}

	public function crawlAlbums()
	{
		Artist::where('updated_at', '=', '0000-00-00 00:00:00')->orderBy('spotify_popularity', 'desc')->chunk(1000, function($artists) {
			foreach($artists as $k => $artist) {
				$data = $this->artistService->getArtist($artist->name);

				if ( ! $data) {
					$artist->touch();
					$this->httpClient->feedback("Skipping <strong>{$artist->name}</strong> albums. <strong>$k out of 1000</strong>");
					continue;
				}

				$artist = $this->saver->save($data);
					
				$this->httpClient->feedback("Saved <strong>{$artist->name}</strong> albums. <strong>$k out of 1000</strong>");
			}
		});
	}

    public function crawlArtists()
	{
    	$letters = range('a', 'z');
		$fullLetters = range('a', 'z');

		foreach($letters as $letter) {
			$this->httpClient->feedback("<strong>Started first loop, letter: $letter</strong>");

			foreach($fullLetters as $letter2) {
				$this->httpClient->feedback("Started second loop, letter: $letter2");

				$r = $this->httpClient->get("search?q=$letter$letter2*&type=artist&limit=50");
				$this->insert($r);

				$timesToCall = ceil($r['artists']['total'] / 50);
				$timesCalled = 0;

				$this->httpClient->feedback("Second loop, letter <strong>$letter2</strong>, times to call: <strong>$timesToCall</strong>");

				while($timesToCall > $timesCalled) {
					$offset = $timesCalled*50;

					if ($offset > 100000) {
						$this->httpClient->feedback("Reached 100000 offset. Moving to next letter.");
						break;
					}

					$r = $this->httpClient->get("search?q=$letter$letter2*&type=artist&limit=50&offset=".$offset);
					$this->insert($r);
					$timesCalled++;

					$this->httpClient->feedback("Called letter: <strong>$letter2</strong>, offset: <strong>$offset</strong>, total times called: <strong>$timesCalled</strong>");
				}

				$this->httpClient->feedback("Done with letter: <strong>$letter2</strong>, times called: <strong>$timesCalled</strong>");
			}
		}
    }

    private function insert($r)
    {
    	if ( ! isset($r['artists']['items'])) {
			$this->httpClient->feedback(json_encode($r));
			return;
		}

		$formatted = [];

    	foreach($r['artists']['items'] as $artist) {
    		$formatted[] = [
				'spotify_followers' => $artist['followers']['total'] ?: null,
				'name' => $artist['name'] ?: null,
				'spotify_popularity' => $artist['popularity'] ?: null,
				'image_small' => isset($artist['images'][2]['url']) ? $artist['images'][2]['url'] : null,
				'image_large' => isset($artist['images'][0]['url']) ? $artist['images'][0]['url'] : null,
				'fully_scraped' => 0,
    		];
    	}

    	$this->saver->saveOrUpdate($formatted, array_flatten($formatted), 'artists');
    }
}