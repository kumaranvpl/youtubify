<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => env('MAILGUN_DOMAIN'),
		'secret' => env('MAILGUN_SECRET'),
	],

	'mandrill' => [
		'secret' => env('MANDRILL_API_KEY'),
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'App\User',
		'secret' => env('STRIPE_SECRET_KEY'),
	],

	'google' => [
		'client_id' => env('GOOGLE_ID'),
		'client_secret' => env('GOOGLE_SECRET'),
		'redirect' =>   env('BASE_URL').'/auth/social/google/login'
	],

	'twitter' => [
		'client_id' => env('TWITTER_ID'),
		'client_secret' => env('TWITTER_SECRET'),
		'redirect' =>   env('BASE_URL').'/auth/social/twitter/login'
	],

	'facebook' => [
		'client_id' => env('FACEBOOK_ID'),
		'client_secret' => env('FACEBOOK_SECRET'),
		'redirect' =>   env('BASE_URL').'/auth/social/facebook/login'
	],

];
