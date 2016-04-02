angular.module('app').directive('playerControls', function($rootScope, $timeout, player, utils, queuePanelStatus, localStorage) {
    var seekBarInterval = false;
    var nodes = {};

    return {
        restrict: 'E',
        templateUrl: 'assets/views/directives/player-controls.html',
        replace: true,
        compile: compile
    };

    function compile(el) {
        setTimeout(function() {
            cacheNodes();

            var throttled = utils.throttle(function() { cacheNodes(); }, 300);
            angular.element(window).bind('resize', throttled);

            $rootScope.$on('player.loaded', function() {
                var hammer = new Hammer(el[0]);
                hammer.on('tap', handleControlsBarClick);

                bindEvents();

                $rootScope.$on('player.trackLoadingStarted', function() {
                    stopSeekbarInterval(true);
                });

                $rootScope.$on('player.trackChanged', function() {
                    setTrackDuration();
                    startSeekbarInterval();
                });

                $rootScope.$on('player.trackStarted', function() {
                    startPlayback();
                });

                $rootScope.$on('player.trackPaused', function() {
                    pausePlayback();
                });

                $rootScope.$on('player.trackStopped', function() {
                    stopSeekbarInterval(true);
                });
            });
        }, 0);
    }

    function setTrackDuration() {
        var duration = player.getDuration();

        if (duration) {
            nodes.totalTime.textContent = utils.secondsToMSS(duration);
        } else {
            //track duration is only available after metadata
            //is loaded so we might need to poll for it
            setTimeout(function() {
                setTrackDuration();
            }, 150);
        }
    }

    function handleControlsBarClick(e) {

        //user clicked on play/pause icon
        if (e.target.classList.contains('toggle-play')) {
            $rootScope.$apply(function() {
                if (player.isPlaying) {
                    pausePlayback();
                } else {
                    startPlayback();
                }
            })
        }

        //user clicked on next track icon
        else if (e.target.classList.contains('next')) {
            $rootScope.$apply(function() {
                stopSeekbarInterval(true);
                player.playNext();
            });
        }

        //user clicked on previous track icon
        else if (e.target.classList.contains('prev')) {
            $rootScope.$apply(function() {
                stopSeekbarInterval(true);
                player.playPrevious();
            });
        }

        //user clicked on toggle queue icon
        else if (e.target.classList.contains('toggle-queue')) {
            $rootScope.$apply(function() {
                queuePanelStatus.open = !queuePanelStatus.open;
            });

            //wait after right panel transition is over before refreshing lazy images directive.
            setTimeout(function() {
                $rootScope.$emit('lazyImg:refresh');
            }, 310);
            localStorage.set('queuePanelOpen', queuePanelStatus.open);
        }

        //user clicked on repeat icon
        else if (e.target.classList.contains('repeat') || e.target.parentNode.classList.contains('repeat')) {
            $rootScope.$apply(function() {
                player.toggleRepeat();
            });
        }

        //user clicked on shuffle icon
        else if (e.target.classList.contains('shuffle')) {
            $rootScope.$apply(function() {
                player.toggleShuffle();
            })
        }

        //user clicked on toggle lyrics button
        else if (e.target.classList.contains('lyrics')) {
            $rootScope.$apply(function() {
                player.toggleLyrics();
            })
        }

        //user clicked on toggle video button
        else if (e.target.classList.contains('toggle-video')) {
            $rootScope.$apply(function() {
                player.toggleVideo();
            })
        }
    }

    function pausePlayback() {
        player.pause();
        stopSeekbarInterval();
    }

    function startPlayback() {
        player.play();
        startSeekbarInterval();
    }

    /**
     * Start interval to update seekbar, handle and elapsed seconds every 120ms.
     */
    function startSeekbarInterval() {
        stopSeekbarInterval();

        seekBarInterval = setInterval(function() {
            var percentage = (player.getCurrentTime()/player.getDuration())*100;

            if (isNaN(percentage)) percentage = 0;

            if (percentage <= 0) return;

            nodes.elapsed.style.width = percentage+'%';
            nodes.handle.style.left   = percentage-nodes.handlePercent+'%';

            //set currently elapsed time in DOM
            nodes.elapsedTime.textContent = utils.secondsToMSS(player.getCurrentTime());
        }, 120);
    }

    /**
     * Clear seekbar interval.
     */
    function stopSeekbarInterval(resetBarToStart) {
        if (seekBarInterval) {
            clearInterval(seekBarInterval);
            seekBarInterval = false;

            if (resetBarToStart) {
                nodes.elapsed.style.width = 0;
                nodes.handle.style.left   = 0;
                nodes.elapsedTime.textContent = '0:00';
            }
        }
    }

    /**
     * Seek video on youtube player using given ration. Will wait until
     * video duration is available if not available yet.
     *
     * @param {int} ratio
     * @param {undefined|int} duration
     */
    function seekPlayer(ratio, duration) {
        duration = player.getDuration();

        if ( ! duration) {

            //if player isn't playing already, play it now
            if ( ! player.playerBackend.isPlaying()) {
                player.playerBackend.mute();
                player.playerBackend.play();
            }

            duration = player.getDuration();

            setTimeout(function() {
                seekPlayer(ratio, duration);
            }, 100);
        } else {
            player.pause();
            player.unMute();
            player.seekTo(ratio * duration, true);

            if (utils.getSetting('player_provider', 'Youtube') === 'SoundCloud') {
                $timeout(function() {
                    startPlayback();
                }, 100);
            } else {
                $rootScope.$apply(function() {
                    startPlayback();
                })
            }
        }
    }

    /**
     * Seek player and seekbar based on given x coordinate.
     *
     * @param {int}     x           user click x coordinate
     * @param {boolean} seekYtPlayer  should we seek on the player as well
     */
    function seek(x, seekYtPlayer) {
        var ratio   = (x-nodes.box.left)/nodes.box.width,
            percent = ratio*100;

        nodes.elapsed.style.width = percent+'%';
        nodes.handle.style.left   = percent-nodes.handlePercent+'%';

        if (seekYtPlayer) {
            seekPlayer(ratio);
        }
    }

    /**
     * Bind click and drag events to seekbar.
     */
    function bindEvents() {
        var hammer = new Hammer(nodes.bar);

        //single click on seekbar
        hammer.on('tap', function(ev) {
            stopSeekbarInterval();
            seek(ev.pointers[0].pageX, true);
            startSeekbarInterval();
        });

        //mouse down before drag
        hammer.on("panstart", function(ev) {
            stopSeekbarInterval();
        });

        //click and drag left
        hammer.on("panleft", function(ev) {
            seek(ev.pointers[0].pageX);
        });

        //click and drag right
        hammer.on("panright", function(ev) {
            seek(ev.pointers[0].pageX);
        });

        //mouse up after drag
        hammer.on("panend", function(ev) {
            seek(ev.pointers[0].pageX, true);
            startSeekbarInterval();
        });
    }

    /**
     * Cache all the nodes required for player bar to work.
     */
    function cacheNodes() {
        nodes.bar         = document.querySelector('.progress-bar');
        nodes.box         = nodes.bar.getBoundingClientRect();
        nodes.elapsed     = document.querySelector('.elapsed');
        nodes.handle      = document.querySelector('.handle');
        nodes.elapsedTime = document.querySelector('.elapsed-time');
        nodes.totalTime   = document.querySelector('.track-length');
        nodes.playIcon    = document.querySelector('.toggle-play');
        nodes.handlePercent = (nodes.handle.getBoundingClientRect().width/nodes.box.width) * 100 / 2;
    }
});