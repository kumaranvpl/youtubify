<?php namespace App\Http\Controllers;

use Auth;
use Cache;
use Carbon\Carbon;
use Input;

class UserLibraryController extends Controller {

	public function __construct()
	{
		$this->middleware('loggedIn');
	}

	/**
	 * Add tracks to current users personal library.
	 */
	public function addTracks()
	{
		$alreadyAttachedIds = Auth::user()->tracks()->whereIn('tracks.id', Input::get('tracks'))->get()->map(function($t) {return $t->id;})->toArray();

		Auth::user()->tracks()->attach(array_diff(Input::get('tracks'), $alreadyAttachedIds));

        Cache::forget($this->getCacheKey());
	}

	/**
	 * Remove given track from current users personal library.
	 */
	public function removeTracks()
	{
		Auth::user()->tracks()->detach(Input::get('tracks'));

        Cache::forget($this->getCacheKey());
	}

	/**
	 * Get all tracks, artists and albums user has attached to his library.
	 *
	 * @return array
	 */
	public function getAll()
	{
		return Cache::remember($this->getCacheKey(), Carbon::now()->addDays(7), function() {
            $tracks = []; $albums = []; $artists = [];

            $user = Auth::user()->load('tracks.album.artist');

            foreach($user->tracks as $track) {
                $track->duration = $this->millisecondsToMSS($track->duration);
                $track->attached_at = $track->pivot->created_at->diffForHumans();
                $tracks[]  = $track;

                if ( ! in_array($track->album, $albums)) $albums[]  = $track->album;
                if ($track->album && $track->album->artist && ! in_array($track->album->artist, $artists)) {
                    $artists[] = $track->album->artist;
                }
            }

            return [
                'albums'  => $albums,
                'artists' => $artists,
                'tracks'  => $tracks
            ];
        });
	}

	/**
	 * Convert milliseconds into 0:00 format
	 *
	 * @param {int} $time
	 * @return string
	 */
	private function millisecondsToMSS($time)
	{
		$time = floor($time / 1000);

		$minutes = floor($time / 60);
		$seconds = ($time - $minutes * 60).'';

		if ($seconds == 0) $seconds = '00';
		if (strlen($seconds) < 2) $seconds = '0'.$seconds;

		return $minutes.':'.$seconds;
	}

    /**
     * Get current users library cache key.
     *
     * @return string
     */
    private function getCacheKey()
    {
        return 'user.library.'.Auth::user()->email;
    }
}
