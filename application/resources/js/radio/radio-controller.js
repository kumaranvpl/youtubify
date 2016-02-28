angular.module('app').controller('RadioController', function($rootScope, $scope, $http, $stateParams, utils, player) {
    $scope.radioPageIsReady = false;
    player.queue = [];
    player.ignoreNext = true;
    $scope.likedTracks = [];
    utils.showLoader(true);

    $http.post('radio/artist', { name: $stateParams.name }).success(function(data) {
        $scope.loadRadioItem(data.track);
        $scope.sessionId = data.session_id;
    });

    $scope.loadRadioItem = function(item) {
        if ( ! item.artist_name) {
            return alertify.delay(2000).error(utils.trans('radioNoMoreTracks'))
        }

        $http.post('get-artist', {name:item.artist_name}).success(function(data) {
            var track = findTrack(item, data);

            //if we could't find matching track in artists discography load the next track
            if ( ! track) {
                $scope.loadNextRadioItem();
            } else {
                player.addToQueue(track, true, true);
                utils.hideLoader(true);
                $scope.radioPageIsReady = true;
            }
        });
    };

    /**
     * Fetch and load next radio track.
     */
    $scope.loadNextRadioItem = function() {
        utils.showLoader(true);

        //pause player immediately and make sure elapsed time is set to 0
        $rootScope.$emit('player.trackLoadingStarted');
        player.pause();

        //load next track
        $http.post('radio/artist/next', {session_id: $scope.sessionId}).success(function(data) {
            $scope.loadRadioItem(data);
        });
    };

    $scope.moreLikeThis = function(track) {
        $http.post('radio/artist/more-like-this', {session_id: $scope.sessionId, id: track.echo_nest_id});

        $scope.likedTracks.push(track.name);
        alertify.delay(2000).success(utils.trans('improvedStation'));
    };

    $scope.lessLikeThis = function(track) {
        utils.showLoader(true);
        var payload = {session_id: $scope.sessionId, id: track.echo_nest_id};
        $http.post('radio/artist/less-like-this', payload).success(function() {
            $scope.loadNextRadioItem();
            alertify.delay(2000).success(utils.trans('improvedStation'));
        })
    };

    $rootScope.$on('player.playNext', function(e) {
        $scope.loadNextRadioItem();
    });

    $scope.$on('$destroy', function() {
        player.ignoreNext = false;
    });

    function findTrack(item, artist) {
        for (var i = 0; i < artist.albums.length; i++) {
            var album = artist.albums[i];

            for (var j = 0; j < album.tracks.length; j++) {
                if (album.tracks[j].name === item.title) {
                    var track = album.tracks[j];

                    track.image  = album.image;
                    track.image_large = artist.image_large;
                    track.artist = artist.name;
                    track.echo_nest_id = item.id;

                    return track;
                }
            }
        }
    }
});


