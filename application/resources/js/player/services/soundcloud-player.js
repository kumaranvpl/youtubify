angular.module('app')

.factory('soundcloudPlayer', function($rootScope, utils, localStorage) {

    var soundcloud = {

        /**
         * User facing player interface.
         */
        frontPlayer: false,

        /**
         * Youtube player implementation.
         */
        soundcloudPlayer: false,

        bootstraped: false,

        soundcloudPlayerOptions: {buying: false, liking: false, download: false, sharing: false, show_artwork: false, show_comments: false, show_playcount: false, show_user: false},

        duration: 0,

        playing: false,

        position: 0,

        play: function() {
            soundcloud.soundcloudPlayer.play();
        },

        pause: function() {
            this.soundcloudPlayer.pause();
        },

        seekTo: function(time) {
            this.soundcloudPlayer.seekTo(time * 1000);
        },

        loadVideo: function(id, autoPlay, quality) {
            this.soundcloudPlayer.load(id, angular.merge(this.soundcloudPlayerOptions, { auto_play: autoPlay }));
        },

        cueVideo: function(id, autoPlay, quality) {
            this.soundcloudPlayer.load(id, angular.merge(this.soundcloudPlayerOptions, { auto_play: autoPlay }));
        },

        getDuration: function() {
            return this.duration / 1000;
        },

        getCurrentTime: function() {
            return this.position / 1000;
        },

        getVolume: function() {
            this.soundcloudPlayer.getVolume(number);
        },

        setVolume: function(number) {
            this.soundcloudPlayer.setVolume(number / 100);
        },

        mute: function() {
            this.soundcloudPlayer.setVolume(0);
        },

        unMute: function() {
            this.soundcloudPlayer.setVolume(50);
        },

        isPlaying: function() {
            return this.playing;
        },

        init: function(frontPlayer) {
            this.frontPlayer = frontPlayer;
            utils.loadScript('https://w.soundcloud.com/player/api.js', bootStrapSoundcloud);
        }
    };

    function bootStrapSoundcloud() {
        var playerNode = document.getElementById('player');

        var iframe = document.createElement('iframe');

        iframe.onload = function() {
            soundcloud.soundcloudPlayer = SC.Widget(iframe);

            //ready
            soundcloud.soundcloudPlayer.bind(SC.Widget.Events.READY, function() {
                $rootScope.$apply(function() {
                    soundcloud.frontPlayer.loadLastPlayerTrack();
                });

                $rootScope.$apply(function() {
                    soundcloud.frontPlayer.loadingTrack = false;
                });

                soundcloud.frontPlayer.setVolume(localStorage.get('youtubify-volume', 17));

                $rootScope.$emit('player.loaded');
            });

            //on play
            soundcloud.soundcloudPlayer.bind(SC.Widget.Events.PLAY, function(e) {
                soundcloud.playing = true;

                $rootScope.$apply(function() {
                    soundcloud.frontPlayer.loadingTrack = false;
                });

                soundcloud.frontPlayer.setVolume(localStorage.get('youtubify-volume', 17));

                soundcloud.soundcloudPlayer.getDuration(function(duration) {
                    soundcloud.duration = duration;

                    setTimeout(function() {
                        $rootScope.$emit('player.trackChanged');
                    });
                });
            });

            //on pause
            soundcloud.soundcloudPlayer.bind(SC.Widget.Events.PAUSE, function() {
                soundcloud.playing = false;
            });

            //during play
            soundcloud.soundcloudPlayer.bind(SC.Widget.Events.PLAY_PROGRESS, function(e) {
                soundcloud.position = e.currentPosition;
            });

            //error
            soundcloud.soundcloudPlayer.bind(SC.Widget.Events.ERROR, function(e) {
                alertify.delay(2500).error(utils.trans('couldntFindTrack'));
                soundcloud.frontPlayer.playNext();

                $rootScope.$apply(function() {
                    soundcloud.frontPlayer.stop();
                })
            });

            //track ended
            soundcloud.soundcloudPlayer.bind(SC.Widget.Events.FINISH, function() {
                $rootScope.$apply(function() {
                    soundcloud.frontPlayer.playNext();
                })
            });
        };

        iframe.id = 'soundcloud-iframe';
        iframe.src = 'https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/34019569&color=0066cc';

        soundcloud.playerContainer = document.querySelector('.player-container');
        soundcloud.playerContainer.style.cssText = 'display: block !important;';

        document.body.appendChild(iframe);

        soundcloud.bootstraped = true;
    }

    return soundcloud;
});
