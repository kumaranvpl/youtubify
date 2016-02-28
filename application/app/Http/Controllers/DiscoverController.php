<?php namespace App\Http\Controllers;

use App\Services\Discover\SpotifyNewReleases;

class DiscoverController extends Controller {

	/**
	 * Show the application home screen to the user.
	 *
	 * @return Response
	 */
	public function newReleases(SpotifyNewReleases $newReleases)
	{
		$newReleases->get();
	}
}
