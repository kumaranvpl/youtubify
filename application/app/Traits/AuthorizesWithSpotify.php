<?php namespace App\Traits;

use App;
use App\Services\HttpClient;

trait AuthorizesWithSpotify {

    protected $token = false;

    public function authorize()
    {
        $client = new HttpClient();
        $result = $client->post('https://accounts.spotify.com/api/token', [
            'headers' => ['Authorization' => 'Basic '.base64_encode(env('SPOTIFY_ID').':'.env('SPOTIFY_SECRET'))],
            'body'    => ['grant_type' => 'client_credentials']
        ]);

        $this->token = $result['access_token'];
    }
}