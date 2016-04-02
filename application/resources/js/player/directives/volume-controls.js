(function(angular) {
angular.module('app').directive('volumeControls', function($rootScope, player, localStorage, utils) {
    var nodes = {};

    return {
        restrict: 'A',
        compile: function () {
            setTimeout(function() {
                cacheNodes();

                var throttled = utils.throttle(function() { cacheNodes(); }, 300);
                angular.element(window).bind('resize', throttled);

                $rootScope.$on('player.loaded', function() {
                    setVolume();
                    bindVolumeBarEvents();
                });
            })
        }
    };

    function mutePlayer() {
        nodes.volumeIcon.classList.remove('icon-volume-up-1');
        nodes.volumeIcon.classList.add('icon-volume-off-1');
        nodes.volumeLevel.style.width   = 0;
        nodes.volumeHandle.style.left   = 0;
        player.mute();
    }

    function unMutePlayer() {
        nodes.volumeIcon.classList.remove('icon-volume-off-1');
        nodes.volumeIcon.classList.add('icon-volume-up-1');
        player.unMute();
    }

    function setVolume(x) {
        var percentage, ratio;

        if ($rootScope.isPhone) {
            cacheNodes();
        }

        if (x) {
            ratio       = (x-nodes.volumeBox.left)/nodes.volumeBox.width;
            percentage  = ratio*100;
        } else {
            percentage = ((localStorage.get('youtubify-volume', utils.getSetting('default_player_volume', 30))/100)*100);
        }

        if (percentage <= 0 && ! player.isMuted) {
            mutePlayer();
        } else if (percentage > 0 && percentage <= 100) {
            nodes.volumeLevel.style.width  = percentage+'%';
            nodes.volumeHandle.style.left  = percentage-nodes.handlePercent+'%';

            if (player.isMuted) {
                unMutePlayer();
            }

            player.setVolume(percentage);
        }
    }

    function bindVolumeBarEvents() {
        var bar    = new Hammer(nodes.volumeBar);
        var icon   = new Hammer(nodes.volumeIcon);

        //single click on volume icon
        icon.on('tap', function() {
            if (player.isMuted) {
                setVolume()
            } else {
                mutePlayer();
            }
        });

        //single click on volume bar
        bar.on('tap', function(ev) {
            setVolume(ev.pointers[0].pageX)
        });

        //click and drag left
        bar.on("panleft", function(ev) {
            setVolume(ev.pointers[0].pageX);
        });

        //click and drag right
        bar.on("panright", function(ev) {
            setVolume(ev.pointers[0].pageX);
        });
    }

    function cacheNodes() {
        nodes.volumeLevel  = document.querySelector('.volume-level');
        nodes.volumeBar    = document.querySelector('.volume-bar');
        nodes.volumeBox    = nodes.volumeBar.getBoundingClientRect();
        nodes.volumeHandle = document.querySelector('.volume-handle');
        nodes.volumeIcon   = document.querySelector('.volume');
        nodes.handlePercent = (nodes.volumeHandle.getBoundingClientRect().width/nodes.volumeBox.width) * 100 / 2;
    }
});
})(angular);