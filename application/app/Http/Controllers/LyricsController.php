<?php namespace App\Http\Controllers;

use Input;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class LyricsController extends Controller {

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
	 * Get lyrics for song from external site.
	 *
	 * @return string
	 */
	public function getLyrics()
	{
        $artist = Input::get('artist');
        $track  = Input::get('track');

        $response = $this->httpClient->get("http://lyrics.wikia.com/api.php?action=lyrics&artist=$artist&song=$track&fmt=realjson")->json();

        if ( ! $response['url'] || $response['lyrics'] === 'Not found') {
            abort(404);
        }

        return $response['url'];
	}
}
