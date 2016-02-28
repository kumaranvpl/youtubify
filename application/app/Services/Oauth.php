<?php

namespace App\Services;

use App;
use App\User;
use App\Social;
use View, Auth, Session;

class Oauth {

    /**
     * Return user matching given social profile from database.
     *
     * @param $profile
     * @return User|null
     */
    public function findUserByProfile($profile)
    {
        if ( ! $profile) return;

        //if there's no email returned from provider (twitter) we
        //will try to find the user using their unique profile id
        if ( ! $profile->email) {
            $oauth = Social::where('token', $profile->id)->first();

            if ($oauth) return $oauth->user;
        }

        return User::where('email', $profile->email)->first();
    }

    /**
     * Return user matching given email from database.
     *
     * @param  string $email
     * @return User|null
     */
    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user from given social profile and log him in.
     *
     * @param  string $profile
     * @param  string $email
     * @param  string $service
     * @return User
     */
    public function createUserFromProfile($profile, $service, $email)
    {
        $profile = (array) $profile;

        $newUser = User::create([
            'email' => $email
        ]);

        $newUser->oauth()->create([
            'service' => $service,
            'token'   => $profile['id'],
        ]);

        return Auth::loginUsingId($newUser->id, true);
    }

    /**
     * Log given user into the app and return
     * a view to close popup in front end.
     *
     * @param  User $user
     * @return string
     */
    public function logUserIn($user)
    {
        $user = Auth::loginUsingId($user->id, true);
        return View::make('oauth/popup')->with('user', $user);
    }

    /**
     * Request user to give his email if social service didn't return it.
     *
     * @param  string $profile
     * @param  string $service
     * @return string
     */
    public function requestUserEmail($profile, $service)
    {
        //store given profile in the session so we can create an
        //account from it when we receive an email from frontend
        Session::put('social_profile', array('service' => $service, 'profile' => $profile));

        return View::make('oauth/requestEmail');
    }

    /**
     * Connect social profile in session
     * @return mixed
     */
    public function connectAccountToProfile()
    {
        if ( ! Session::get('social_profile') || ! Session::get('account')) {
            return response(trans('app.genericSocialError'), 422);
        }

        $profile = Session::get('social_profile')['profile'];
        $service = Session::get('social_profile')['service'];
        $account = Session::get('account');

        $user = User::where('email', $account['email'])->first();

        $user->oauth()->create(['service' => $service, 'token' => $profile->id]);

        return $user;
    }

    /**
     * If email from service already exists in database ask user
     * to connect that account to the service he's trying to log in
     * with by entering accounts password or authenticating with
     * one of the services that account is already attached to
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function askUserToConnectAccountToProfile($user)
    {
        Session::put('account', $user->toArray());

        return response()->json(['code' => $user->password ? 1 : 2, 'message' => trans('app.userWithEmailExists')], 422);
    }

    /**
     * Validate that the password we requested from
     * user matches the one on account stored in session
     *
     * @param array $input
     * @return bool|void
     */
    public function callbackPasswordIsValid(array $input)
    {
        $profile = Session::get('social_profile');
        $account = Session::get('account');

        if ( ! $profile || ! $input['password'] || ! $account) {
            return response(trans('app.genericSocialError'), 422);
        }

        return Auth::validate([
            'email'    => $account['email'],
            'password' => $input['password']
        ]);
    }
}