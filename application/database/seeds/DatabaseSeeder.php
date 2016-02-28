<?php

use App\Genre;
use App\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

        Setting::insert([
            ['name' => 'homepage', 'value' => 'default'],
            ['name' => 'enableRegistration', 'value' => 1],
            ['name' => 'siteName', 'value' => 'Youtubify'],
            ['name' => 'enablePushState', 'value' => 0],
            ['name' => 'dateLocale', 'value' => 'en'],
            ['name' => 'pushStateRootUrl', 'value' => '/'],
            ['name' => 'homepageGenres', 'value' => 'Rock, Hip Hop, Pop, Country'],
            ['name' => 'primaryHomeSection', 'value' => 'new-releases'],
            ['name' => 'artist_update_interval', 'value' => 7],
            ['name' => 'latest_albums_update_interval', 'value' => 1],
            ['name' => 'homepage_update_interval', 'value' => 1],
            ['name' => 'force_login', 'value' => 0],
            ['name' => 'search_provider', 'value' => 'spotify'],
            ['name' => 'enable_https', 'value' => 0],
            ['name' => 'latest_albums_strict', 'value' => 0],
            ['name' => 'top_songs_provider', 'value' => 'spotify'],
            ['name' => 'youtube_region_code', 'value' => 'US'],
            ['name' => 'show_youtube_player', 'value' => 0],
            ['name' => 'hide_lyrics_button', 'value' => 0],
            ['name' => 'hide_video_button', 'value' => 0],
            ['name' => 'hide_queue', 'value' => 0],
        ]);

        Genre::insert([
            ['name' => 'Rock'],
            ['name' => 'Hip Hop'],
            ['name' => 'Pop'],
            ['name' => 'Jazz'],
        ]);
	}
}
