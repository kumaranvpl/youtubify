angular.module('app').controller('ExternalPlayerControlsController', function($scope, player, userLibrary, utils, users) {

    /**
     * Play or pause (if already playing) given track.
     *
     * @param {object} track
     */
    $scope.playTrack = function(track) {
        if ($scope.trackIsPlaying(track)) {
            player.pause();
        } else if ($scope.trackIsLoaded(track)) {
            player.play();
        } else {
            player.loadTrack(track, true);
        }
    };

    /**
     * Play given track and load the tracks after index into queue.
     *
     * @param {object} track
     * @param {int} index
     * @param {array|undefined} tracks
     */
    $scope.loadQueueAndPlayTrack = function(track, index, tracks) {
        if ( ! tracks) tracks = $scope.sortedTracks;

        if ($scope.trackIsPlaying(track)) {
            player.pause();
        } else if ($scope.trackIsLoaded(track)) {
            player.play();
        } else {
            player.loadQueue(tracks, true, index);
        }
    };

    /**
     * Check if user has already saved given track to his library.
     *
     * @param {object} track
     * @returns {boolean}
     */
    $scope.trackSaved = function(track) {
        return userLibrary.has(track);
    };

    /**
     * Check if give track is currently playing.
     *
     * @param {object} track
     * @returns {boolean}
     */
    $scope.trackIsPlaying = function(track) {
        return $scope.trackIsLoaded(track) && player.isPlaying;
    };

    /**
     * Check if given track is currely loaded into player.
     *
     * @param {object} track
     * @returns {boolean}
     */
    $scope.trackIsLoaded = function(track) {
        if (track.id) {
            return player.currentTrack && player.currentTrack.id == track.id
        } else {
            return player.currentTrack && player.currentTrack.name === track.name
        }
    };

    /**
     * Attach track to users library.
     */
    $scope.addTrack = function(track) {
        if ( ! users.current) {
            player.pause();
            utils.toState('login');
        }

        userLibrary.addTracks(track);
    };

    /**
     * Remove track from users library.
     */
    $scope.removeTrack = function(track) {
        if ( ! users.current) {
            utils.toState('login');
        }

        userLibrary.removeTracks(track);
    };
});


