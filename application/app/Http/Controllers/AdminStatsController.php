<?php namespace App\Http\Controllers;

use Cache;
use App\User;
use App\Album;
use App\Track;
use App\Artist;
use Carbon\Carbon;

class AdminStatsController extends Controller {

	/**
	 * Create new AdminStatsController instance.
	 */
	public function __constructor()
	{
		$this->middleware('admin');
	}

	public function getStats() {
		return Cache::remember('admin.stats', Carbon::now()->addDays(1), function() {
			return [
				'tracks'  => number_format(Track::count()),
				'albums'  => number_format(Album::count()),
				'artists' => number_format(Artist::count()),
				'users'   => number_format(User::count()),
			];
		});
	}
}
