<?php

Route::get('/', 'HomeController@index');

Route::get('update', ['middleware' => ['admin', 'disableOnDemoSite'], 'uses' => 'UpdateController@index']);
Route::post('run-update', ['middleware' => ['admin', 'disableOnDemoSite'], 'uses' => 'UpdateController@runUpdate']);

//SOCIAL AUTHENTICATION
Route::get('auth/social/{provider}', 'SocialAuthController@connectToProvider');
Route::get('auth/social/{provider}/login', 'SocialAuthController@loginCallback');
Route::post('auth/social/request-email-callback', 'SocialAuthController@requestEmailCallback');
Route::post('auth/social/connect-accounts', 'SocialAuthController@connectAccounts');

//AUTH
Route::post('password/change', 'Auth\PasswordChangeController@change');

Route::controllers([
    'auth'    => 'AuthController',
    'password' => 'Auth\PasswordController',
]);

//TRACKS
Route::resource('track', 'TrackController', ['only' => ['update', 'index']]);
Route::get('get-track/{id}', 'TrackController@show');
Route::post('delete-tracks', 'TrackController@destroy');
Route::get('tracks/top', 'TrackController@getTopSongs');

//LYRICS
Route::post('get-lyrics', 'LyricsController@getLyrics');

//PLAYLISTS
Route::resource('playlist', 'PlaylistController', ['except' => ['edit', 'show']]);
Route::get('get-playlist/{id}', 'PlaylistController@show');
Route::post('playlist/{id}/add-tracks', 'PlaylistTracksController@addTracks');
Route::post('playlist/{id}/remove-track', 'PlaylistTracksController@removeTrack');
Route::post('playlist/{id}/follow', 'PlaylistController@follow');
Route::post('playlist/{id}/unfollow', 'PlaylistController@unfollow');
Route::put('playlist/{id}/update-order', 'PlaylistTracksController@updateTracksOrder');

//SEARCH
Route::get('get-search-results/{q}', 'SearchController@search');
Route::get('search-audio/{artist}/{track}', 'SearchController@searchAudio');

//RADIO
Route::post('radio/artist', 'RadioController@artistRadio');
Route::post('radio/artist/next', 'RadioController@nextSong');
Route::post('radio/artist/more-like-this', 'RadioController@moreLikeThis');
Route::post('radio/artist/less-like-this', 'RadioController@lessLikeThis');

//USER LIBRARY
Route::post('user-library/add-tracks', 'UserLibraryController@addTracks');
Route::post('user-library/remove-tracks', 'UserLibraryController@removeTracks');
Route::get('user-library/get-all', 'UserLibraryController@getAll');

//USERS
Route::resource('users', 'UsersController', ['only' => ['show', 'update', 'index', 'destroy']]);
Route::post('users', 'UsersController@destroyAll');
Route::post('users/{id}/avatar', 'AvatarController@change');
Route::delete('users/{id}/avatar', 'AvatarController@remove');
Route::post('users/{id}/follow', 'UsersController@follow');
Route::post('users/{id}/unfollow', 'UsersController@unfollow');
Route::get('users/{id}/followers', 'UsersController@followers');
Route::get('users/{id}/followed_users', 'UsersController@followedUsers');

//ARTISTS
Route::get('artist', 'ArtistController@index');
Route::put('artist/{id}', 'ArtistController@update');
Route::post('get-artist', 'ArtistController@getArtist');
Route::post('artist/{name}/get-bio', 'ArtistController@getBio');
Route::post('get-artist-top-tracks', 'ArtistController@getTopTracks');
Route::get('artists/most-popular', 'ArtistController@getMostPopularArtists');
Route::post('delete-artists', 'ArtistController@destroy');

//GENRES
Route::get('genres/most-popular', 'GenreController@getMostPopularGenres');
Route::get('genres/{names}', 'GenreController@getGenres');
Route::get('genres/{names}/paginate-artists', 'GenreController@paginateArtists');

//ALBUMS
Route::get('album', 'AlbumController@index');
Route::put('album/{id}', 'AlbumController@update');
Route::post('get-album', 'AlbumController@getAlbum');
Route::get('albums/latest-releases', 'AlbumController@getLatestAlbums');
Route::get('albums/top', 'AlbumController@getTopAlbums');
Route::post('delete-albums', 'AlbumController@destroy');

//SHARING
Route::post('send-links', 'SendLinksController@send');

//ADMIN
Route::get('translations', 'TranslationsController@getLinesAndLocales');
Route::get('translation-lines/{locale}', 'TranslationsController@getLines');
Route::post('new-locale', 'TranslationsController@createNewLocale');
Route::delete('locale/{name}', 'TranslationsController@deleteLocale');
Route::post('update-translations', 'TranslationsController@updateLines');
Route::post('reset-translations', 'TranslationsController@resetTranslations');
Route::get('admin-stats', 'AdminStatsController@getStats');

Route::get('mail/templates', 'MailController@getTemplates');
Route::post('mail/template/{name}', 'MailController@saveTemplate');

Route::post('update-settings', ['middleware' => 'disableOnDemoSite', 'uses' => 'SettingsController@UpdateSettings']);
Route::get('settings', 'SettingsController@GetAllSettings');
Route::post('settings/upload-logo', ['middleware' => 'disableOnDemoSite', 'uses' => 'SettingsController@uploadLogo']);

//APPEARANCE
Route::get('sass-files', 'AppearanceController@getSassFiles');
Route::get('available-stylesheets', 'AppearanceController@getAvailableStylesheets');
Route::post('create-new-stylesheet', 'AppearanceController@createNewStylesheet');
Route::put('update-stylesheet', 'AppearanceController@updateStylesheet');
Route::delete('stylesheet/{name}', 'AppearanceController@deleteStylesheet');
Route::post('stylesheet/{name}/reset', 'AppearanceController@resetStylesheetVariables');
Route::put('rename-stylesheet/{name}', 'AppearanceController@renameStylesheet');

// Route::get('spider-artists', function() {
//     $crawler = App::make('App\Services\Crawlers\SpotifyCrawler');
//     $crawler->crawlArtists();
// })->middleware('admin');

// Route::get('spider-albums', function() {
//     $crawler = App::make('App\Services\Crawlers\SpotifyCrawler');
//     $crawler->crawlAlbums();
// })->middleware('admin');