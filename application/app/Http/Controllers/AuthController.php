<?php namespace App\Http\Controllers;

use App;
use Auth;
use Input;
use Exception;
use App\Services\Registrar;
use App\Http\Requests\LogUserInRequest;

class AuthController extends Controller {

    /**
     * Register/create a new user.
     *
     * @param Registrar $registrar
     * @return *
     */
    public function postRegister(Registrar $registrar)
	{
        //make sure that admin has enabled regisration before proceeding
        if ( ! App::make('Settings')->get('enableRegistration', true) && ( ! Auth::check() || ! Auth::user()->isAdmin)) {
            return response(trans('app.registrationDisabled'), 403);
        }

        $validator = $registrar->validator(Input::all());

        if ($validator->fails())
        {
            return response()->json($validator->errors(), 400);
        }

        $user = $registrar->create(Input::all());

        //if user is not logged in, do it now
        if ( ! Auth::check()) {
            Auth::login($user);
        }

        return $user;
	}

    /**
     * Login in a user.
     *
     * @param LogUserInRequest $request
     * @return Response
     */
    public function postLogin(LogUserInRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $attemptSuccessful = Auth::attempt($credentials, $request->get('remember'));
        } catch (Exception $e) {
            $attemptSuccessful = false;
        }

        if ($attemptSuccessful) {
            return response()->json(Auth::user(), 200);
        }

        return response()->json(array('*' => trans('app.wrongCredentials')), 422);
    }

    public function postLogout()
    {
        if (Auth::check()) {
            Auth::logout();
            return;
        }

        abort(404);
    }
}
