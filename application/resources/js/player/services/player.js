angular.module('app')

.factory('player', function($rootScope, $http, $timeout, localStorage, utils) {
    var player = {

        /**
         * Youtube player instance.
         */
        ytPlayer: false,

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
                this.ytPlayer.playVideo();
                player.isPlaying = true;

                $rootScope.$emit('player.trackStarted');
            }
        },

        pause: function() {
            if (this.isPlaying) {
                this.ytPlayer.pauseVideo();
                this.isPlaying = false;

                $rootScope.$emit('player.trackPaused');
            }
        },

        stop: function() {
           if (this.isPlaying) {
               this.ytPlayer.pauseVideo();
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
                        alertify.delay(3000).error(utils.trans('couldntFindTrack'))
                    }

                    if (autoPlay) {
                        player.ytPlayer.loadVideoById(data.id, 0, 'large');
                        player.play();
                    } else {
                        player.ytPlayer.cueVideoById(data.id, 0, 'large');
                    }
                });
            } else {
                if (autoPlay) {
                    player.ytPlayer.loadVideoById(track.youtube_id, 0, 'large');
                    player.play();
                } else {
                    player.ytPlayer.cueVideoById(track.youtube_id, 0, 'large');
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
                hideVideoModal();
            }

            if ( ! this.isShowingLyrics) {
                $http.post('get-lyrics', { artist: player.currentTrack.artist, track: player.currentTrack.name }).success(function(data) {
                    showLyricsModal(data);
                    player.isShowingLyrics = true;
                }).error(function() {
                    alertify.delay(2500).error(utils.trans('noLyricsFound'));
                });
            } else {
                this.isShowingLyrics = false;
                hideLyricsModal();
            }
        },

        /**
         * Toggle video display.
         */
        toggleVideo: function() {
            if ( ! player.currentTrack) return;

            if (this.isShowingLyrics) {
                this.isShowingLyrics = false;
                hideLyricsModal();
            }

            if ( ! this.isShowingVideo) {
                player.play();
                showVideoModal();
                player.isShowingVideo = true;
            } else {
                this.isShowingVideo = false;
                hideVideoModal();
            }
        },

        getVolume: function() {
            return this.ytPlayer.getVolume();
        },

        setVolume: function(number) {
            localStorage.set('youtubify-volume', number);
            return this.ytPlayer.setVolume(number);
        },

        mute: function() {
            this.isMuted = true;
            this.ytPlayer.mute();
        },

        unMute: function() {
            this.isMuted = false;
            this.ytPlayer.unMute();
        },

        getDuration: function() {
            return this.ytPlayer.getDuration();
        },

        getCurrentTime: function() {
            return this.ytPlayer.getCurrentTime();
        },

        seekTo: function(time) {
            this.ytPlayer.seekTo(time, true);
        },

        init: function() {
            initPlayer();
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
            echo_nest_id: track.echo_nest_id,
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
     * Last last track that was playing before browser close.
     */
    function loadLastPlayerTrack() {
        if (player.currentTrack) {
            player.loadTrack(player.currentTrack);
        } else if (player.currentQueIndex) {
            player.loadTrack(player.queue[player.currentQueIndex]);
        }
        window.player = player;
    }

    /**
     * Fetch track info from youtube.
     *
     * @param {object} track
     * @returns {promise}
     */
    function fetchTrack(track) {
        var name = track.name.replace('/', ' ');
        return $http.get('search-audio/'+track.artist+'/'+name);
    }

    function initPlayer() {
        //fetch youtube iframe API
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        document.body.appendChild(tag);

        loadLastPlayerState();

        //init player
        window.onYouTubeIframeAPIReady = function() {
            player.ytPlayer = new YT.Player('player', {
                playerVars : {
                    autoplay: 0,
                    rel: 0,
                    showinfo: 0,
                    egm: 0,
                    showsearch: 0,
                    controls: 0,
                    modestbranding: 1,
                    iv_load_policy: 3,
                    disablekb: 1,
                    version: 3
                },
                events: {
                    onReady: function() {
                        $rootScope.$apply(function() {
                            loadLastPlayerTrack();
                        });
                        $rootScope.$emit('player.loaded');
                    },
                    onError:function(e) {
                        if (e.data == 150 || e.data == 101) {
                            alertify.delay(2500).error(utils.trans('couldntFindTrack'));
                            $rootScope.$apply(function() {
                                player.stop();
                            })
                        }
                    },
                    onStateChange: function(ev) {
                        if (ev.data === YT.PlayerState.ENDED) {
                            $rootScope.$apply(function() {
                                player.playNext();
                            })
                        } else if (ev.data === YT.PlayerState.CUED || ev.data === YT.PlayerState.PLAYING) {
                            $rootScope.$apply(function() {
                                player.loadingTrack = false;
                            });

                            if (ev.data === YT.PlayerState.PLAYING) {
                                setTimeout(function() {
                                    $rootScope.$emit('player.trackChanged');
                                })
                            }
                        }
                    }
                }
            });
        };
    }

    /**
     * Animate lyrics modal in.
     */
    function showLyricsModal(url) {
        var node = angular.element(document.querySelector('#lyrics-iframe'));

        node.html('<iframe src="'+url+'"></iframe>');

        requestAnimationFrame(function() {
            document.querySelector('.lyrics-container').classList.add('show');
        });
    }

    /**
     * Animate lyrics modal out.
     */
    function hideLyricsModal() {
        document.querySelector('.lyrics-container').classList.remove('show');
    }

    /**
     * Animate video modal in.
     */
    function showVideoModal() {
        document.querySelector('.player-container').classList.remove('hidden');

        requestAnimationFrame(function() {
            document.querySelector('.player-container .modal-inner-container').classList.add('out');
        });
    }

    /**
     * Animate video modal out.
     */
    function hideVideoModal() {
        document.querySelector('.player-container .modal-inner-container').classList.remove('out');

        setTimeout(function() {
            document.querySelector('.player-container').classList.add('hidden');
        }, 150);
    }

    function setDocumentTitle() {
        document.title = player.currentTrack.artist+' - '+player.currentTrack.name+' - '+utils.getSetting('siteName');
    }

    player.init();

    return player;
});