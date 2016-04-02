angular.module('app')

.factory('player', function($rootScope, $http, $timeout, $injector, localStorage, utils) {
    var player = {

        /**
         * Backend player implementation instance (Youtube, SoundCloud, HTML5 etc)
         */
        playerBackend: false,

        /**
         * Is player fully loaded and ready to play tracks.
         *
         * @type {boolean}
         */
        isReady: false,

        /**
         * Is playback in progress.
         *
         * @type {boolean}
         */
        isPlaying: false,

        /**
         * Is player muted.
         *
         * @type {boolean}
         */
        isMuted: false,

        /**
         * Is player repeating all tracks currently in queue.
         *
         * @type {boolean}
         */
        isRepeating: false,

        /**
         * Is player repeating currently playing track after it's finished.
         *
         * @type {boolean}
         */
        isRepeatingOne: false,

        /**
         * Queue of tracks to play (before shuffling).
         *
         * @type {array}
         */
        originalQueue: [],

        /**
         * Is player shuffle activated.
         *
         * @type {boolean}
         */
        IsShuffling: false,

        /**
         * Queue of tracks to play.
         *
         * @type {array}
         */
        queue: [],

        /**
         * Index of currently active/playing track in queue.
         *
         * @type {int|false}
         */
        currentQueIndex: false,

        /**
         * Currently loaded/playing track.
         *
         * @type {object|false}
         */
        currentTrack: false,

        /**
         * Is player showing lyrics.
         *
         * type {boolean}
         */
        isShowingLyrics: false,

        /**
         * If true next/previous functionality is disabled and only
         * corresponding event is fired so that functionality can be overriden.
         *
         * type {boolean}
         */
        ignoreNext: false,

        play: function() {
            if ( ! player.currentTrack) return;

            if ( ! this.isPlaying) {
                this.playerBackend.play();

                player.isPlaying = true;

                $rootScope.$emit('player.trackStarted');
            }
        },

        pause: function() {
            if (this.isPlaying) {
                this.playerBackend.pause();
                this.isPlaying = false;

                $rootScope.$emit('player.trackPaused');
            }
        },

        stop: function() {
           if (this.isPlaying) {
               this.playerBackend.pause();
               this.isPlaying = false;
               player.seekTo(0);
               $rootScope.$emit('player.trackStopped');
           }
        },

        playNext: function() {
            if (player.ignoreNext) {
                $rootScope.$emit('player.playNext');
                return;
            }

            if ( ! this.queue) return;

            //if we player is repeating one track, reload and replay it now
            if (this.isRepeatingOne) {
                return this.loadTrack(this.currentTrack, true);
            }

            var next = this.queue[this.currentQueIndex + 1];

            //if player is repeating and we have no more tracks after
            //this one, load and play the first track in queue again
            if (! next) {
                if (this.isRepeating) {
                    return this.loadTrack(this.queue[0], true);
                } else {
                    player.stop();
                }
            }

            else {
                this.loadTrack(next, true);
            }
        },

        playPrevious: function() {
            if ( ! this.queue) return;

            //if we player is repeating one track, reload and replay it now
            if (this.isRepeatingOne) {
                return this.loadTrack(this.currentTrack, true);
            }

            var prev = this.queue[this.currentQueIndex - 1];

            //if player is repeating and we have no more tracks before
            //this one, load and play the last track in queue again
            if (! prev) {
                if (this.isRepeating) {
                    return this.loadTrack(this.queue[this.queue.length - 1], true);
                } else {
                    player.stop();
                }
            }

            else {
                this.loadTrack(prev, true);
            }
        },

        /**
         * Check if player has any tracks before current in queue.
         *
         * @returns {boolean}
         */
        hasPrevious: function() {
            return ! angular.isUndefined(this.queue[this.currentQueIndex - 1]);
        },

        /**
         * Add given track to queue.
         *
         * @param {object|array} tracks
         * @param {boolean} autoPlay
         * @param {boolean} append
         */
        addToQueue: function(tracks, autoPlay, append) {
            if ( ! angular.isArray(tracks)) tracks = [tracks];

            waitUntilReady(function() {
                for (var i = 0; i < tracks.length; i++) {
                    var formatted = formatTrackForQueue(tracks[i]);

                    //push new tracks to the end of the queue
                    if (append) {
                        player.queue.push(formatted);

                        if (autoPlay) {
                            player.loadTrack(player.queue[player.queue.length - 1], true);
                        }

                    //add new tracks after currently loaded/playing one in queue
                    } else {
                        player.queue.splice(player.currentQueIndex + 1 + i, 0, formatted);

                        if (autoPlay) {
                            if (player.queue.length > 1) {
                                player.playNext();
                            } else {
                                player.loadTrack(player.queue[0], true);
                            }
                        }
                    }
                }

                localStorage.set('queue', player.queue.slice(0, 20));
            });
        },

        /**
         * Add given track to queue.
         *
         * @param {object}  track
         */
        removeFromQueue: function(track) {
            player.queue.splice(player.queue.indexOf(track), 1);
            localStorage.set('queue', player.queue.slice(0, 20));
        },

        /**
         * Load given tracks into play queue. Will remove any tracks currently in queue.
         *
         * @param {array}                     tracks
         * @param {boolean|undefined|string}  autoPlay  should we start playing track after loading the queue
         * @param {int|undefined}             loadFrom  load only tracks starting from this index into queue
         */
        loadQueue: function(tracks, autoPlay, loadFrom) {
            waitUntilReady(function() {
                loadQueueFromIndex(tracks, loadFrom);
                player.loadTrack(getTrackByNameOrFirst(autoPlay), autoPlay);
                localStorage.set('queue', player.queue.slice(0, 20));
            })
        },

        /**
         * Check if given track is currently is player queue.
         *
         * @param {object} track
         * @returns {boolean}
         */
        isInQueue: function(track) {
            for (var i = 0; i < this.queue.length; i++) {
                if (this.queue[i].id == track.id) {
                    return true;
                }
            }
        },

        /**
         * Load given track and set it as active.
         *
         * @param {object} track
         * @param {boolean|undefined} autoPlay should we start playing track after loading it
         */
        loadTrack: function(track, autoPlay) {
            player.loadingTrack = true;
            $rootScope.$emit('player.trackLoadingStarted');

            if ( ! track.identifier) {
                track = formatTrackForQueue(track);
            }

            //set index if given track is in current queue
            var index = this.queue.indexOf(track);
            if (index > -1) this.currentQueIndex = index;
            this.currentTrack = track;

            if ( ! track.youtube_id || utils.isDemo) {
                fetchTrack(track).success(function(data) {
                    if ( ! data.id) {
                        player.loadingTrack = false;
                        alertify.delay(3000).error(utils.trans('couldntFindTrack'));
                        player.playNext();
                    }

                    if (autoPlay) {
                        player.playerBackend.loadVideo(data.id, autoPlay, 'large');
                        player.play();
                    } else {
                        player.playerBackend.cueVideo(data.id, autoPlay, 'large');
                    }
                });
            } else {
                if (autoPlay) {
                    player.playerBackend.loadVideo(track.youtube_id, autoPlay, 'large');
                    player.play();
                } else {
                    player.playerBackend.cueVideo(track.youtube_id, autoPlay, 'large');
                }
            }

            setDocumentTitle();

            localStorage.set('last-track', track);
            localStorage.set('queue-index', this.currentQueIndex);
            localStorage.set('queue', player.queue.slice(0, 20));
        },

        /**
         * Play/Pause or load given track if not already loaded.
         *
         * @param {object} track
         * @param {boolean} autoPlay
         */
        toggleTrack: function(track, autoPlay) {
            if (player.currentTrack.identifier == track.identifier) {
                if (player.isPlaying) {
                    player.pause();
                } else if (autoPlay) {
                    player.play();
                }
            } else {
                player.loadTrack(track, autoPlay);
            }
        },

        /**
         * Toggle player repeat state between no repeat, repeat all and repeat one.
         */
        toggleRepeat: function() {
            if ( ! this.isRepeating && ! this.isRepeatingOne) {
                this.isRepeating = true;
            } else if (this.isRepeating) {
                this.isRepeating = false;
                this.isRepeatingOne = true;
            } else if (this.isRepeatingOne) {
                this.isRepeatingOne = false;
                this.isRepeating = false;
            }

            localStorage.set('isRepeating', this.isRepeating);
            localStorage.set('isRepeatingOne', this.isRepeatingOne);
        },

        /**
         * Toggle player shuffle state on and off.
         */
        toggleShuffle: function() {
            if ( ! this.isShuffling) {
                shuffleQueue();
                this.isShuffling = true;
            } else {
                this.queue = this.originalQueue.slice();
                this.isShuffling = false;
            }

            localStorage.set('isShuffling', this.isShuffling);
        },

        /**
         * Toggle lyrics display.
         */
        toggleLyrics: function() {
            if ( ! player.currentTrack) return;

            if (this.isShowingVideo) {
                this.isShowingVideo = false;
                hideLyricsOrVideoModal('video');
            }

            if ( ! this.isShowingLyrics) {
                utils.showLoader();
                $http.post('get-lyrics', { artist: player.currentTrack.artist, track: player.currentTrack.name }).success(function(data) {
                    showLyricsOrVideoModal('lyrics', data);
                    player.isShowingLyrics = true;
                    utils.hideLoader();
                }).error(function() {
                    alertify.delay(2500).error(utils.trans('noLyricsFound'));
                    utils.hideLoader();
                });
            } else {
                this.isShowingLyrics = false;
                hideLyricsOrVideoModal('lyrics');
            }
        },

        /**
         * Toggle video display.
         */
        toggleVideo: function() {
            if ( ! player.currentTrack) return;

            if (this.isShowingLyrics) {
                this.isShowingLyrics = false;
                hideLyricsOrVideoModal('lyrics');
            }

            if ( ! this.isShowingVideo) {
                player.play();
                showLyricsOrVideoModal('video');
                player.isShowingVideo = true;
            } else {
                this.isShowingVideo = false;
                hideLyricsOrVideoModal('video')
            }
        },

        /**
         * Last last track that was playing before browser close.
         */
        loadLastPlayerTrack: function() {
            if (player.currentTrack) {
                player.loadTrack(player.currentTrack);
            } else if (player.currentQueIndex) {
                player.loadTrack(player.queue[player.currentQueIndex]);
            }
        },

        getVolume: function() {
            return this.playerBackend.getVolume();
        },

        setVolume: function(number) {
            localStorage.set('youtubify-volume', number);
            return this.playerBackend.setVolume(number);
        },

        mute: function() {
            this.isMuted = true;
            this.playerBackend.mute();
        },

        unMute: function() {
            this.isMuted = false;
            this.playerBackend.unMute();
        },

        getDuration: function() {
            return this.playerBackend.getDuration();
        },

        getCurrentTime: function() {
            return this.playerBackend.getCurrentTime();
        },

        seekTo: function(time) {
            this.playerBackend.seekTo(time, true);
        },

        goFullScreen: function() {
            thisVid = document.getElementById('player');

            if (thisVid.requestFullscreen) {
                thisVid.requestFullscreen();
            }
            else if (thisVid.msRequestFullscreen) {
                thisVid.msRequestFullscreen();
            }
            else if (thisVid.mozRequestFullScreen) {
                thisVid.mozRequestFullScreen();
            }
            else if (thisVid.webkitRequestFullScreen) {
                thisVid.webkitRequestFullScreen();
            }
        },

        init: function() {
            loadLastPlayerState();
            window.player = this;

            var serviceName = utils.getSetting('player_provider', 'youtube').toLowerCase()+'Player';

            this.playerBackend = $injector.get(serviceName);

            this.playerBackend.init(this);
        }
    };

    /**
     * Load all tracks from given index into queue
     * or load all tracks if no index is given.
     *
     * @param {array} tracks
     * @param {boolean|undefined} index
     * @returns {*}
     */
    function loadQueueFromIndex(tracks, index) {
        var sliced = tracks.slice(index),
            newQueue = [];

        //add a random identifier so we can highlight currently playing
        //track correctly if there are multiple same tracks in the queue
        for (var i = 0; i < sliced.length; i++) {
            newQueue.push(formatTrackForQueue(sliced[i]));
        }

        player.queue = newQueue;
    }

    /**
     * Only leave needed properties on track object and add unique identifier.
     *
     * @param {object} track
     * @returns {object}
     */
    formatTrackForQueue = function(track) {
        var image = '';

        if ( ! angular.isUndefined(track.image)) {
            image = track.image;
        } else if (track.album) {
            image = track.album.image;
        }

        return formatted = {
            id: track.id,
            identifier: utils.randomString(5),
            youtube_id: track.youtube_id,
            name: track.name,
            image: image,
            image_large: track.image_large,
            artist: track.artist || track.artists[0] || track.album.artist.name,
            artists: track.artists,
            echo_nest_id: track.echo_nest_id
        };
    };

    /**
     * Return track from player queue matching given name
     * or first one if no name is passed or can't find one.
     *
     * @param {string} name
     * @returns {object}
     */
    function getTrackByNameOrFirst(name) {
        if ( ! angular.isString(name)) {
            return player.queue[0];
        }

        for (var i = 0; i < player.queue.length; i++) {
            if (player.queue[i].name == name) {
                return player.queue[i];
            }
        }

        return player.queue[0];
    }

    /**
     * Call given function after player is ready to play tracks.
     *
     * @param {function} callback
     */
    function waitUntilReady(callback) {
        if (player.isReady) {
            callback();
        } else {
            setTimeout(function() {
                waitUntilReady(callback);
            }, 100);
        }
    }

    /**
     * Copy current queue and shuffle it.
     */
    function shuffleQueue() {
        player.originalQueue = player.queue.slice();

        var currentIndex = player.queue.length, temporaryValue, randomIndex;

        while (0 !== currentIndex) {
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;

            temporaryValue = player.queue[currentIndex];
            player.queue[currentIndex] = player.queue[randomIndex];
            player.queue[randomIndex] = temporaryValue;
        }
    }

    /**
     * Load last player state from local storage.
     */
    function loadLastPlayerState() {
        player.queue = localStorage.get('queue', []);
        player.currentQueIndex = localStorage.get('queue-index');
        player.currentTrack = localStorage.get('last-track');
        player.isRepeating = localStorage.get('isRepeating', false);
        player.isRepeatingOne = localStorage.get('isRepeatingOne', false);
        player.isShuffling = localStorage.get('isShuffling', false);

        if (player.isShuffling) {
            shuffleQueue();
        }

        player.isReady = true;
    }

    /**
     * Fetch track info from youtube.
     *
     * @param {object} track
     * @returns {promise}
     */
    function fetchTrack(track) {
        var name   = track.name.replace('/', ' '),
            artist = track.artist.replace('/', ' ');

        return $http.get('search-audio/'+artist+'/'+name);
    }

    /**
     * Animate video modal in.
     */
    function showLyricsOrVideoModal(type, text) {
        if (type === 'lyrics') {
            var className = 'lyrics';
        } else {
            var className = 'player';
        }

        document.querySelector('.'+className+'-container').classList.remove('hidden');

        if (type === 'lyrics') {
            var node = angular.element(document.querySelector('#lyrics-panel'));
            node.html(text);

            setTimeout(function() {
                node.scrollTop(node.scrollTop()+1);
                node.scrollTop(node.scrollTop()-1);
            }, 350);
        }

        requestAnimationFrame(function() {
            document.querySelector('.'+className+'-container .modal-inner-container').classList.add('out');
        });
    }

    /**
     * Animate video modal out.
     */
    function hideLyricsOrVideoModal(type) {
        if (type === 'lyrics') {
            var className = 'lyrics';
        } else {
            var className = 'player';
        }

        document.querySelector('.'+className+'-container .modal-inner-container').classList.remove('out');

        setTimeout(function() {
            document.querySelector('.'+className+'-container').classList.add('hidden');
        }, 150);
    }

    function setDocumentTitle() {
        if ( ! utils.stateIs('admin')) {
            document.title = player.currentTrack.artist+' - '+player.currentTrack.name+' - '+utils.getSetting('siteName');
        }
    }

    setTimeout(function() {
        player.init();
    });

    return player;
});