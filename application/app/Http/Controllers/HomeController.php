<?php namespace App\Http\Controllers;

use App;
use Auth;
use Lang;

class HomeController extends Controller {

    /**
	 * Show the application home screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
        $settings = App::make('Settings');

        return view('main')
			->with('user', Auth::user())
			->with('baseUrl', $settings->get('enable_https', false) ? secure_url('') : url())
			->with('translations', json_encode(Lang::get('app')))
			->with('settings', $settings)
			->with('isDemo', IS_DEMO)
			->with('version', VERSION);
	}
}
