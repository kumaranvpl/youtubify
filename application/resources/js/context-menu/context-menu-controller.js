angular.module('app').controller('ContextMenuController', function($rootScope, $scope, $stateParams, $translate, playlists, contextMenu, modal, player, userLibrary, clipboard, users, utils) {
    $scope.playlists = playlists;

    $scope.addToPlaylist = function(playlist) {
        if ( ! loggedIn()) return;

        if (contextMenu.context == 'album') {
            playlists.addTracks(contextMenu.item.tracks, playlist);
        } else {
            playlists.addTracks(contextMenu.item, playlist);
        }

        contextMenu.hide();
        alertify.delay(2000).success($translate.instant('addedToPlaylist'));
    };

    /**
     * Go to artist radio state and start the artist radio.
     */
    $scope.startArtistRadio = function() {
        utils.toState('artist-radio', { name: contextMenu.item.name });
    };

    /**
     * Go to artists state and start playing all his tracks.
     */
    $scope.playArtist = function() {
        if (utils.stateIs('artist')) {
            contextMenu.$scope.playAllTracks()
        } else {
            $rootScope.autoplay = true;
            utils.toState('artist', {name: contextMenu.item.name});
        }
    };

    $scope.showShareModal = function() {
        modal.show('share', $scope);
    };

    $scope.removeFromPlaylist = function() {
       if ( ! loggedIn()) return;

        playlists.removeTrack(contextMenu.item, $stateParams.id);
        contextMenu.hide();
    };

    $scope.createPlaylist = function() {
       if ( ! loggedIn()) return;

        playlists.trackToAddToNewPlaylist = contextMenu.item;
        contextMenu.hide();
        modal.show('new-playlist', $rootScope.$new());
    };

    $scope.copySongLink = function() {
        clipboard.copy($rootScope.baseUrl+(! utils.getSetting('enablePushState') ? '#/' : '')+'track/'+contextMenu.item.id);
    };

    $scope.copyAlbumLink = function() {
        var artist = contextMenu.item.artist || contextMenu.$scope.artist;
        clipboard.copy($rootScope.baseUrl+(! utils.getSetting('enablePushState') ? '#/' : '')+'album/'+artist.name+'/'+contextMenu.item.name);
    };

    $scope.copyArtistLink = function() {
        clipboard.copy($rootScope.baseUrl+(! utils.getSetting('enablePushState') ? '#/' : '')+'artist/'+contextMenu.item.name);
    };

    /**
     * Add tracks to users library.
     */
    $scope.addToYourMusic = function() {
       if ( ! loggedIn()) return;

        if (contextMenu.context == 'album') {
            userLibrary.addTracks(contextMenu.item.tracks);
        } else {
            userLibrary.addTracks(contextMenu.item);
        }

        alertify.delay(2000).success($translate.instant('addedTracksToLibrary'));
    };

    /**
     * Add a track to player queue.
     */
    $scope.addToQueue = function() {
        $scope.$apply(function() {
            if (contextMenu.context == 'album') {
                player.addToQueue(contextMenu.item.tracks);
            } else if (contextMenu.context == 'artist') {

                //if we're in user library artists page, add
                //artists tracks user has in his library only
                if (utils.stateIs('artists')) {
                    player.addToQueue(userLibrary.getArtistTracks(contextMenu.item.name));
                }
            } else {
                player.addToQueue(contextMenu.item);
            }
        });
    };

    /**
     * Remove a track from player queue.
     */
    $scope.removeFromQueue = function() {
        $scope.$apply(function() {
            player.removeFromQueue(contextMenu.item);
        });
    };

    function loggedIn() {
        if ( ! users.current) {
            player.pause();
            utils.toState('login');
            contextMenu.hide();
            return false;
        }

        return true;
    }
});


