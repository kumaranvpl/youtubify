'use strict';
angular.module('app')
.config(function($urlRouterProvider, $stateProvider, $urlMatcherFactoryProvider, $locationProvider) {

    //enable html5 pushState if user selected it
    if (parseInt(vars.settings.enablePushState)) {
        $locationProvider.html5Mode(true);
    }

    $urlMatcherFactoryProvider.strictMode(false);
    $urlRouterProvider.otherwise('/');

    $stateProvider
        .state('home', {
            url: '/',
            templateUrl: 'assets/views/home.html'
        })
        .state('new-releases', {
            url: '/new-releases',
            templateUrl: 'assets/views/new-releases.html'
        })
        .state('popular-genres', {
            url: '/popular-genres',
            templateUrl: 'assets/views/popular-genres.html'
        })
        .state('top-songs', {
            url: '/top-songs',
            templateUrl: 'assets/views/top-songs.html'
        })
        .state('top-albums', {
            url: '/top-albums',
            templateUrl: 'assets/views/top-albums.html'
        })
        .state('artist-radio', {
            url: '/radio/artist/:name',
            templateUrl: 'assets/views/radio.html'
        })
        .state('user', {
            url: '/user/:id',
            templateUrl: 'assets/views/public-user-profile.html'
        })
        .state('404', {
            templateUrl: 'assets/views/404.html'
        })
        .state('search', {
            url: '/search/:query',
            templateUrl: 'assets/views/search-page.html'
        })
        .state('phone-search', {
            url: '/phone-search',
            templateUrl: 'assets/views/phone-search.html'
        })
        .state('artist', {
            url: '/artist/:name',
            templateUrl: 'assets/views/artist.html',
        })
        .state('album', {
            url: '/album/:artistName/:name',
            templateUrl: 'assets/views/album.html',
            controller: 'AlbumController'
        })
        .state('album-no-artist', {
            url: '/album/:name',
            templateUrl: 'assets/views/album.html',
            controller: 'AlbumController'
        })
        .state('track', {
            url: '/track/:id',
            controller: 'TrackController'
        })
        .state('genre', {
            url: '/genre/:name',
            controller: 'GenreController',
            templateUrl: 'assets/views/genre.html'
        })
        .state('playlist', {
            url: '/playlist/:id',
            templateUrl: 'assets/views/playlist.html',
        })
        .state('songs', {
            url: '/songs',
            templateUrl: 'assets/views/songs.html',
        })
        .state('albums', {
            url: '/albums',
            templateUrl: 'assets/views/albums.html',
            controller: 'AlbumsController'
        })
        .state('artists', {
            url: '/artists',
            templateUrl: 'assets/views/artists.html',
            controller: 'ArtistsController'
        })
        .state('login', {
            url: '/login',
            views: {
                full: {
                    templateUrl: 'assets/views/login.html',
                    controller: 'LoginController'
                }
            }
        })
        .state('register', {
            url: '/register',
            views: {
                full: {
                    templateUrl: 'assets/views/register.html',
                    controller: 'RegisterController'
                }
            }
        })

        //ADMIN
        .state('admin', {
            url: '/admin',
            abstract: true,
            views: {
                full: {
                    templateUrl: 'assets/views/admin/admin.html',
                    controller: 'AdminController'
                }
            }
        })
        .state('admin.users', {
            url: '/users',
            templateUrl: 'assets/views/admin/users.html'
        })
        .state('admin.tracks', {
            url: '/tracks',
            templateUrl: 'assets/views/admin/tracks.html'
        })
        .state('admin.albums', {
            url: '/albums',
            templateUrl: 'assets/views/admin/albums.html'
        })
        .state('admin.artists', {
            url: '/artists',
            templateUrl: 'assets/views/admin/artists.html'
        })
        .state('admin.appearance', {
            url: '/appearance',
            templateUrl: 'assets/views/admin/appearance.html'
        })
        .state('admin.translations', {
            url: '/translations',
            templateUrl: 'assets/views/admin/translations.html'
        })
        .state('admin.analytics', {
            url: '/analytics',
            templateUrl: 'assets/views/admin/analytics.html',
            controller: 'AnalyticsController'
        })
        .state('admin.settings', {
            url: '/settings',
            templateUrl: 'assets/views/admin/settings.html'
        })
        .state('admin.mail', {
            url: '/settings/mail',
            templateUrl: 'assets/views/admin/mail.html'
        })
        .state('admin.ads', {
            url: '/ads',
            templateUrl: 'assets/views/admin/admin-ads.html',
            controller: 'SettingsController'
        })
});
