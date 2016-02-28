angular.module('app').controller('AlbumController', function($rootScope, $http, $scope, $stateParams, utils, player) {
    $scope.utils = utils;
    utils.showLoader();
    $scope.player = player;

    var albumName  = utils.decodeUrlParam($stateParams.name),
        artistName = utils.decodeUrlParam($stateParams.artistName);

    $http.post('get-album', {artistName: artistName, albumName: albumName}).success(function(data) {
        $scope.album = data;
        $scope.totalLength = getTotalLength($scope.album);

        utils.hideLoader();
        $scope.albumReady = true;

        if ($rootScope.autoplay) {
            $scope.playAllTracks();
            $rootScope.autoplay = false;
        }
    }).error(function() {
        utils.toState('404');
        utils.hideLoader();
    });

    $scope.playAllTracks = function() {

        //if albumIsPlaying is false and not undefined it means
        //we have already loaded album into the player and paused it
        //so we can just resume playing without reloading album into queue
        if ($scope.albumIsPlaying === false) {
            player.play();
        } else {

            //if we got passed a track name start playing that track, otherwise play first one in queue
            var autoPlay = angular.isObject($rootScope.autoplay) ? $rootScope.autoplay.trackName : true;
            player.loadQueue($scope.album.tracks, autoPlay);
        }

        $scope.albumIsPlaying = true;
    };

    $scope.pauseAllTracks = function() {
        $scope.albumIsPlaying = false;
        player.pause();
    };

    /**
     * Return total length of album in 0:00 format.
     *
     * @param {object} album
     * @returns {string}
     */
    function getTotalLength(album) {
        var ms = 0;

        album.tracks.forEach(function(track) {
            ms+=parseInt(track.duration);
            track.duration = utils.secondsToMSS(track.duration / 1000);
            track.image    = album.image;
        });

        return utils.secondsToMSS(ms / 1000);
    }
});