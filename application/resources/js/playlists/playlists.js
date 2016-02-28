
angular.module('app').factory('playlists', function($rootScope, $http, users, utils) {
    var playlists = {

        /**
         * All playlists user has created or followed.
         *
         * @type {array}
         */
        all: [],

        /**
         * Track that needs to be added to newly created playlist.
         *
         * @type {object|false}
         */
        trackToAddToNewPlaylist: false,

        /**
         * Are user playlists fetched from server already.
         *
         * @type {boolean}
         */
        loaded: false,

        /**
         * Email of user for which playlists are loaded.
         *
         * @type {string|false}
         */
        forUser: false,

        /**
         * Follow playlist with currently logged in user.
         *
         * @param {int} id
         * @returns {HttpPromise}
         */
        follow: function(id) {
            return $http.post('playlist/'+id+'/follow').success(function(data) {
                playlists.all.push(data);
            });
        },


        /**
         * Un-follow playlist with currently logged in user.
         *
         * @param {int} id
         * @returns {HttpPromise}
         */
        unfollow: function(id) {
            return $http.post('playlist/'+id+'/unfollow').success(function() {
                for (var i = 0; i < playlists.all.length; i++) {
                    if (playlists.all[i].id == id) {
                        playlists.all.splice(i, 1); break;
                    }
                }
            });
        },

        /**
         * Check if user is already following playlist with given id.
         *
         * @param {int} id
         * @returns {boolean}
         */
        isFollowing: function(id) {
            for (var i = 0; i < this.all.length; i++) {
                if (this.all[i].id == id) return true;
            }

            return false;
        },

        /**
         * Get playlist by given id.
         *
         * @param {int|string} id
         * @returns {promise}
         */
        getPlaylist: function(id) {
            return $http.get('get-playlist/'+id);
        },

        /**
         * Make given playlist private.
         *
         * @param {object} playlist
         * @returns {promise}
         */
        makePlaylistPrivate: function(playlist) {
            return $http.put('playlist/'+playlist.id, { 'public': 0 }).success(function(data) {
                alertify.delay(2000).success(utils.trans('playlistToPrivate'));
                $rootScope.$emit('playlist.updated', data);
            });
        },

        /**
         * Make given playlist public.
         *
         * @param {object} playlist
         * @returns {promise}
         */
        makePlaylistPublic: function(playlist) {
            return $http.put('playlist/'+playlist.id, { 'public': 1 }).success(function(data) {
                alertify.delay(2000).success(utils.trans('playlistToPublic'));
                $rootScope.$emit('playlist.updated', data);
            });
        },

        /**
         * Create a new playlist
         *
         * @param {string} name
         * @returns {promise}
         */
        createNew: function(name) {
            return $http.post('playlist', { name: name }).success(function(data) {
                playlists.all.push(data);

                //attach a track to new playlist if needed
                if (playlists.trackToAddToNewPlaylist) {
                    playlists.addTracks(playlists.trackToAddToNewPlaylist, data);
                }
            })
        },

        /**
         * Check if track is already added to playlist.
         *
         * @param {object} track
         * @param {object} playlist
         * @returns {boolean}
         */
        isTrackInPlaylist: function(track, playlist) {
            return playlist.tracks.indexOf(track) > -1;
        },

        /**
         * Add tracks to playlist.
         *
         * @param {object} tracks
         * @param {object} playlist
         * @returns {promise}
         */
        addTracks: function(tracks, playlist) {
            if ( ! angular.isArray(tracks)) tracks = [tracks];
            return $http.post('playlist/'+playlist.id+'/add-tracks', {tracks: tracks});
        },

        /**
         * Remove track from playlist.
         *
         * @param {object} track
         * @param {object|int|string} playlist
         * @returns {promise}
         */
        removeTrack: function(track, playlist) {
            var id = angular.isObject(playlist) ? playlist.id : playlist;
            return $http.post('playlist/'+id+'/remove-track', {track_id: track.id}).success(function() {
                $rootScope.$emit('playlist.track.removed', track);
            })
        },

        /**
         * fetch all playlists user has created or followed
         */
        fetchAll: function() {
            $http.get('playlist').success(function(data) {
                playlists.all = data;
                playlists.loaded = true;
                playlists.forUser = users.current.email;
            });
        }
    };

    if (users.current && ! playlists.loaded) {
        playlists.fetchAll();
    }

    //fetch playlists on user login
    $rootScope.$on('user.newCurrent', function() {
        if ( ! playlists.loaded || playlists.forUser !== users.current.email) {
            playlists.fetchAll();
        }
    });

    return playlists;
});