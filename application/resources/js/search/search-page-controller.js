angular.module('app').controller('SearchPageController', function($rootScope, $http, $scope, $stateParams, utils) {
    utils.showLoader();

    $scope.activeTab = 'all';

    $http.get('search/'+utils.decodeUrlParam($stateParams.query)+'?limit=20').success(function(data) {
        $scope.results = data;

        for (var i = 0; i < $scope.results.tracks.length; i++) {
            $scope.results.tracks[i].duration = utils.secondsToMSS($scope.results.tracks[i].duration / 1000);
        }

        if ( !data.tracks.length && !data.albums.length && !data.artists.length) {
            $scope.noResults = true;
        }

        $scope.query   = $stateParams.query;
        utils.hideLoader();
        $scope.searchPageReady = true;
    });

    $scope.openTab = function(tab) {
        $scope.activeTab = tab;
    };

    $scope.playAlbum = function(album) {
        $rootScope.autoplay = true;

        utils.toState('album', {
            artistName: album.artist.name,
            name: album.name
        });
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
     * Play given track and load the tracks after index into queue.
     *
     * @param {object} track
     * @param {int} index
     */
    $scope.loadQueueAndPlayTrack = function(track, index) {
        if ($scope.trackIsPlaying(track)) {
            player.pause();
        } else if ($scope.trackIsLoaded(track)) {
            player.play();
        } else {
            player.loadQueue($scope.results.tracks, true, index);
        }
    };
});


