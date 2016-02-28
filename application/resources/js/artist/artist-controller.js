angular.module('app').controller('ArtistController', function($rootScope, $http, $scope, $stateParams, $timeout, utils, player, userLibrary) {
    utils.showLoader();

    $scope.artist = false;
    $scope.tracks = false;
    $scope.artistReady = false;
    $scope.showingMoreTopTracks = false;

    $scope.albumSort = '-release_date';
    $scope.tabs = { active: 'overview' };

    $http.post('get-artist?top-tracks=true', {name:utils.decodeUrlParam($stateParams.name)}).success(function(data) {
        $scope.artist = data;
        $scope.tracks = sortTracksByPopularity(getAllTracks(data.albums)).slice(0, 20);
        $scope.singles = extractSingles($scope.artist);

        for (var i = 0; i < data.topTracks.length; i++) {
            data.topTracks[i].duration = utils.secondsToMSS(data.topTracks[i].duration / 1000);
        }

        $scope.topTracks = data.topTracks;

        utils.hideLoader();

        if ($rootScope.autoplay) {
            waitUntilViewIsRendered(function() {
                $scope.playAllTracks();
                $rootScope.autoplay = false;
            })
        }

        waitUntilViewIsRendered(function() {
            $rootScope.$emit('lazyImg:refresh');
            $scope.$apply(function() {
                $scope.artistReady = true;
            })
        });
    }).error(function() {
        utils.toState('404');
        utils.hideLoader();
    });

    $scope.openTab = function(name) {
        $scope.tabs.active = name;
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
     * Play given track and load all tracks after it into queue.
     *
     * @param {object} track
     */
    $scope.playTrack = function(track) {
        if ($scope.trackIsPlaying(track)) {
            player.pause();
        } else if ($scope.trackIsLoaded(track)) {
            player.play();
        } else {
            var queue = $scope.topTracks.concat(getAllTracks($scope.filteredAlbums.concat($scope.filteredSingles), true));
            for (var i = 0; i < queue.length; i++) {
                if (queue[i].id == track.id) {
                    player.loadQueue(queue, true, i);
                    break;
                }
            }
        }
    };

    /**
     * Change field albums are sorted on.
     *
     * @param {string} sort
     */
    $scope.sortAlbums = function(sort) {
        $scope.albumSort = sort;
    };

    $scope.addAlbumToLibrary = function(album) {
        userLibrary.addTracks(album.tracks);
    };

    $scope.goToArtistPage = function(artist) {
        utils.toState('artist', { name: artist.name });
    };

    $scope.toggleTopTracksAmount = function() {
        $scope.showingMoreTopTracks = !$scope.showingMoreTopTracks;

        setTimeout(function() {
            $rootScope.$emit('lazyImg:refresh');
        }, 0);
    };

    $scope.playAllTracks = function() {
        var tracks = $scope.topTracks.concat(getAllTracks($scope.filteredAlbums, true));
        player.loadQueue(tracks, true);
    };

    $scope.pauseAllTracks = function() {
        player.pause();
    };

    /**
     * Check if current artists track is currently playing.
     *
     * @returns {boolean}
     */
    $scope.isPlaying = function() {
        return player.isPlaying && player.currentTrack.artist == $scope.artist.name;
    };

    /**
     * Extract singles albums into a separate array.
     *
     * @param {object} artist
     * @returns {Array}
     */
    extractSingles = function(artist) {
        var singles = [];

        for (var i = 0; i < artist.albums.length; i++) {
            if (artist.albums[i].tracks.length < 7) {
                singles.push(artist.albums[i]);
                delete artist.albums[i];
            }
        }

        return singles;
    };

    getAllTracks = function(albums, skipFormatting) {
        var tracks = [];

        for (var i = 0; i < albums.length; i++) {
            var album = albums[i];

            if ( ! album) continue;

            for (var j = 0; j < album.tracks.length; j++) {
                var track = album.tracks[j];

                if ( ! skipFormatting) {
                    track.duration = utils.secondsToMSS(track.duration / 1000);
                    track.artist   = $scope.artist.name;
                    track.image    = album.image;
                }

                tracks.push(track);
            }
        }

        return tracks;
    };

    sortTracksByPopularity = function(tracks) {
        tracks.sort(function(a, b) {
            if (a.spotify_popularity > b.spotify_popularity) return -1;
            if (a.spotify_popularity < b.spotify_popularity) return 1;
            return 0;
        });

        return tracks;
    };

    /**
     * Execute given function after artist view is rendered
     * and topTracks and filteredAlbums variables are available
     *
     * @param {function} callback
     */
    waitUntilViewIsRendered = function(callback) {
        if ( ! angular.isUndefined($scope.filteredAlbums) && ! angular.isUndefined($scope.topTracks)) {
            callback();
        } else {
            setTimeout(function() {
                waitUntilViewIsRendered(callback);
            }, 10);
        }
    }
});


