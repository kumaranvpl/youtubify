angular.module('app').controller('ArtistsController', function($rootScope, $translate, $scope, $state, userLibrary, utils) {
    utils.showLoader();

    //field artists are sorted on in the view
    $scope.artistsSort = '-numberOfTracks';

    /**
     * Sort artists by given object key.
     *
     * @param {string} sort
     */
    $scope.sortArtists = function(sort) {
        $scope.artistsSort = sort;
    };

    /**
     * Translate given sort field.
     *
     * @param {string} string
     * @returns {string}
     */
    $scope.translate = function(string) {
        if (string.indexOf('spotify_popularity') > -1) {
            string = 'popularity';
        }

        return $translate.instant(string.replace('-', ''));
    };

    /**
     * Go to artist state and start playing all his tracks and albums.
     *
     * @param {object} artist
     */
    $scope.playArtist = function(artist) {
        $rootScope.autoplay = true;
        utils.toState('artist', {name: artist.name});
    };

    /**
     * Execute given function after user library is fetched from server.
     *
     * @param {function} callback
     */
    function waitUntilUserLibraryIsLoaded(callback) {
        if (userLibrary.loaded) {
            callback();
        } else {
            setTimeout(function() {
                waitUntilUserLibraryIsLoaded(callback)
            }, 100);
        }
    }

    //Count how many songs of artists user has in library and attach artists to $scope
    waitUntilUserLibraryIsLoaded(function() {
        for (var i = 0; i < userLibrary.artists.length; i++) {
            var artist = userLibrary.artists[i],
                numOfTracks = 0;

            for (var j = 0; j < userLibrary.tracks.length; j++) {

                //album is attached to particular artist
                if (userLibrary.tracks[j].album && userLibrary.tracks[j].album.artist) {
                    if (userLibrary.tracks[j].album.artist.name == artist.name) {
                        numOfTracks++;
                    }
                //album belongs to multiple artists
                } else {
                    if (userLibrary.tracks[j].artists[0] == artist.name) {
                        numOfTracks++;
                    }
                }

            }

            artist.numberOfTracks = numOfTracks;
        }

        $scope.artists = userLibrary.artists;
        utils.hideLoader();
    });
});


