angular.module('app').controller('PlaylistTracksController', function($rootScope, $scope, $http, $stateParams, utils, playlists, player, clipboard, modal) {
    utils.showLoader();

    playlists.getPlaylist($stateParams.id).success(function(data) {
        $scope.playlist = data;

        $scope.playlist.tracks.forEach(function(track) {
            track.ms_duration = track.duration;
            track.duration = utils.secondsToMSS(track.duration / 1000);
        });

        $scope.totalLength = getTotalLength($scope.playlist);
        utils.hideLoader();
        $scope.playlistReady = true;
    }).error(function() {
        utils.toState('404');
        utils.hideLoader();
    });

    $scope.followPlaylist = function() {
        playlists.follow($scope.playlist.id);
    };

    $scope.unfollowPlaylist = function() {
        playlists.unfollow($scope.playlist.id);
    };

    $scope.playlistIsFollowed = function() {
        if ($scope.playlist) {
            return playlists.isFollowing($scope.playlist.id);
        }
    };

    $scope.showShareModal = function() {
        $scope.shareable = $scope.playlist;
        modal.show('share', $scope);
    };

    $scope.copyPlaylistLink = function() {
        clipboard.copy($rootScope.baseUrl+(! utils.getSetting('enablePushState') ? '#/' : '')+'playlist/'+$scope.playlist.id);
    };

    $scope.removeTrackFromPlaylist = function(track) {
        playlists.removeTrack(track, $scope.playlist);
    };

    /**
     * Return first track album image or a default image
     * if no tracks are attached to current playlist.
     *
     * @returns {string}
     */
    $scope.getImageForPlaylist = function() {
        if ($scope.playlist && $scope.playlist.tracks.length) {
            return $scope.playlist.tracks[0].album.image;
        }

        return $rootScope.baseUrl + 'assets/images/album-no-image.png';
    };

    $scope.playAllTracks = function() {

        //if playlistIsPlaying is false and not undefined it means
        //we have already loaded playlist into the player and paused it
        //so we can just resume playing without reloading it into queue
        if ($scope.playlistIsPlaying === false) {
            player.play();
        } else {

            //if we got passed a track name start playing that track, otherwise play first one in queue
            var autoPlay = angular.isObject($rootScope.autoplay) ? $rootScope.autoplay.trackName : true;
            player.loadQueue($scope.playlist.tracks, autoPlay);
        }

        $scope.playlistIsPlaying = true;
    };

    $scope.pauseAllTracks = function() {
        $scope.playlistIsPlaying = false;
        player.pause();
    };

    var unbind = $rootScope.$on('playlist.track.removed', function(e, track) {
        $scope.playlist.tracks.splice($scope.playlist.tracks.indexOf(track), 1);
        $scope.totalLength = getTotalLength($scope.playlist);
    });

    var unbind2 = $rootScope.$on('playlist.updated', function(e, playlist) {
        angular.forEach($scope.playlist, function(prop, name) {
            //make sure we only overwrite text properties and not tracks
            if ( ! angular.isArray($scope.playlist[name]) && ! angular.isUndefined(playlist[name])) {
                $scope.playlist[name] = playlist[name];
            }
        });
    });

    $scope.$on('$destroy', function() {
        unbind(); unbind2();
    });

    /**
     * Return total length of playlist in 0:00 format.
     *
     * @param {object} playlist
     * @returns {string}
     */
    function getTotalLength(playlist) {
        var ms = 0;

        playlist.tracks.forEach(function(track) {
            ms+=track.ms_duration;
        });

        var min = (ms/1000/60) << 0,
            sec = (ms/1000) % 60;

        return min + ' min ' + Math.floor(sec) + ' sec';
    }
});


