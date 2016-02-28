
angular.module('app').factory('search', function($rootScope, $http, utils) {
    var search = {
        query: '',

        ajaxInProgress: false,

        results: [],

        /**
         * Clear current query from search bar.
         */
        clearQuery: function() {
            this.query = '';
            this.results = [];
        },

        getResults: function(query) {
            if ( ! query || query.length < 2) return;

            this.ajaxInProgress = true;

            $http.get('search/'+query+'?limit=3').success(function(data) {
                search.results = data;
                search.showPanel();
                search.ajaxInProgress = false;
            })
        },

        showPanel: function() {
            if ($rootScope.isPhone) {
                utils.toState('phone-search');
            } else {
                document.querySelector('.search-panel-backdrop').classList.remove('hidden');

                requestAnimationFrame(function() {
                    document.querySelector('.search-panel').classList.add('out');
                });
            }
        },

        hidePanel: function(keepQuery) {
            if (utils.getCurrentStateName() !== 'search' && !keepQuery) {
                this.query = '';
            }

            if ( ! $rootScope.isPhone) {
                document.querySelector('.search-panel').classList.remove('out');

                setTimeout(function() {
                    document.querySelector('.search-panel-backdrop').classList.add('hidden');
                }, 150);
            }
        },

        /**
         * Go to search page using current query.
         *
         * @returns {promise}
         */
        viewAllResults: function() {
            if ( ! this.query || this.query.length < 2) return;

            this.hidePanel(true);
            return utils.toState('search', {query: this.query});
        },

        /**
         * Open give artists state and close search panel.
         *
         * @param {object} artist
         */
        goToArtistPage: function(artist) {
            utils.toState('artist', {name: artist.name}).then(function() {
                search.hidePanel();
                search.clearQuery();
            })
        },

        /**
         * Open give albums state and close search panel.
         *
         * @param {object} album
         */
        goToAlbumPage: function(album) {
            if (album.artist) {
                utils.toState('album', {name: album.name, artistName: album.artist.name}).then(function() {
                    search.hidePanel();
                    search.clearQuery();
                });
            } else {
                utils.toState('album-no-artist', {name: album.name}).then(function() {
                    search.hidePanel();
                    search.clearQuery();
                });
            }
        },

        /**
         * Go to give tracks album state and start playing the track.
         *
         * @param {object} track
         */
        playTrack: function(track) {
            $rootScope.autoplay = { trackName: track.name };
            this.goToAlbumPage(track.album || { name: track.album_name });
        }
    };

    return search;
});