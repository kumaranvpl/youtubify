angular.module('app').directive('videoContainer', function(player) {
	return {
		restrict: 'A',
		link: function ($scope, el) {
			el.on('click', function(e) {
				if (shouldCloseVideo(e)) {
					$scope.$apply(function() {
						player.toggleVideo();
					})
				}

                if (shouldToggleFullScreen) {
                    $scope.$apply(function() {
                        player.goFullScreen();
                    })
                }
			});
		}
	};

	function shouldCloseVideo(e) {
		return e.target.classList.contains('backdrop') ||
			(e.target.classList.contains('close-lyrics-icon')) || e.target.parentNode.classList.contains('close-lyrics-icon');
	}

    function shouldToggleFullScreen(e) {
        return (e.target.classList.contains('toggle-fullscreen')) || e.target.parentNode.classList.contains('toggle-fullscreen');
    }
});