<?php namespace App\Http\Controllers;

use Exception;
use App\Services\Oauth;
use Auth, View, Socialize, Input, Session;

class SocialAuthController extends Controller {

    private $validProviders = ['google', 'facebook', 'twitter'];

    private $oauth;

    public function __construct(Oauth $oauth)
    {
        $this->oauth = $oauth;
    }

    /**
     * Connect to given provider and request user authorization.
     *
     * @param  string $provider
     * @return mixed
     */
    public function connectToProvider($provider)
    {
        $this->validateProvider($provider);

        return Socialize::with($provider)->redirect();
    }

    /**
     * Handle callback from one of the social auth services.
     *
     * @param  string $provider
     * @return mixed
     */
    public function loginCallback($provider)
    {
        $this->validateProvider($provider);

        try {
            $profile = Socialize::with($provider)->user();
        } catch (Exception $e) {
            return View::make('oauth/error');
        }

        //if we have already created a user for this profile - log him in
        if ($user = $this->oauth->findUserByProfile($profile)) {
            return $this->oauth->logUserIn($user);
        }

        //if this service didn't return user email, we'll need to request it
        if ( ! $profile->email) {
            return $this->oauth->requestUserEmail($profile, $provider);
        }

        //if we have email and didn't create an account for this profile yet, do it now
        $user = $this->oauth->createUserFromProfile($profile, $provider, $profile->email);
        return $this->oauth->logUserIn($user);
    }

    /**
     * Handle callback from user password request.
     *
     * @return mixed
     */
    public function requestEmailCallback()
    {
        $profile = Session::get('social_profile');
        $email   = Input::get('email');

        //if we don't have a profile in session or email wasn't passed, bail
        if ( ! $profile || ! $email) return response(trans('app.genericSocialError'), 422);

        //if account with given email already exists we'll ask user if
        //he wants to connect that account to this social service profile
        if ($user = $this->oauth->findUserByEmail($email)) {
            return $this->oauth->askUserToConnectAccountToProfile($user);
        }

        return $this->oauth->createUserFromProfile($profile['profile'], $profile['service'], $email);
    }

    /**
     * Connect existing account to given oauth profile.
     *
     * @return mixed
     */
    public function connectAccounts()
    {
        if ( ! $this->oauth->callbackPasswordIsValid(Input::all())) {
            return response()->json(trans('app.wrongPassword'), 422);
        }

        return $this->oauth->connectAccountToProfile();
    }

    private function validateProvider($provider)
    {
        if ( ! in_array($provider, $this->validProviders)) {
            abort(404);
        }
    }
}
