<?php namespace App\Http\Controllers;

use App;
use Input;
use GuzzleHttp\Client;

class RadioController extends Controller
{

    /**
     * Guzzle http client instance.
     *
     * @var Client
     */
    private $httpClient;

    /**
     * Settings instance.
     *
     * @var App\Services\Settings
     */
    private $settings;

    /**
     * Create new RadioController instance.
     */
    public function __construct()
    {
        $this->httpClient = new Client([
            'base_url' => 'http://developer.echonest.com/api/v4/',
            'timeout' => 8.0,
        ]);

        $this->settings = App::make('Settings');
    }

    /**
     * Start artist radio on EchoNest based in supplied artist name.
     *
     * @return array
     */
    public function artistRadio()
    {
        $response = $this->httpClient->get('playlist/dynamic/create', ['query' => [
            'api_key' => $this->settings->get('echonest_api_key'),
            'artist'  => Input::get('name'),
            'format'  => 'json',
            'results' => 1,
            'type'    => 'artist-radio',
        ]])->json();

        $track = isset($response['response']['songs'][0]) ? $response['response']['songs'][0] : [];

        return ['session_id' => $response['response']['session_id'], 'track' => $track];
    }

    public function nextSong()
    {
        $response = $this->httpClient->get('playlist/dynamic/next', ['query' => [
            'api_key' => $this->settings->get('echonest_api_key'),
            'format'  => 'json',
            'results' => 1,
            'session_id' => Input::get('session_id'),
        ]])->json();

        return isset($response['response']['songs'][0]) ? $response['response']['songs'][0] : [];
    }

    public function moreLikeThis()
    {
        $response = $this->httpClient->get('playlist/dynamic/steer', ['query' => [
            'api_key' => $this->settings->get('echonest_api_key'),
            'format'  => 'json',
            'session_id' => Input::get('session_id'),
            'more_like_this' => Input::get('id')
        ]])->json();

        return $response;
    }

    public function lessLikeThis()
    {
        $response = $this->httpClient->get('playlist/dynamic/steer', ['query' => [
            'api_key' => $this->settings->get('echonest_api_key'),
            'format'  => 'json',
            'session_id' => Input::get('session_id'),
            'less_like_this' => Input::get('id')
        ]])->json();

        return $response;
    }

}
