<?php namespace App\Http\Controllers;

use Artisan;
use Exception;

class UpdateController extends Controller {

	public function index()
    {
        return view('install.update');
    }

    public function runUpdate()
	{
        Artisan::call('migrate', ['--force' => 'true']);

        try {
            Artisan::call('db:seed', ['--force' => 'true']);
        } catch (Exception $e) {}

        return redirect('/');
	}
}