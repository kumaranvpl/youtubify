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
			});
		}
	};

	function shouldCloseVideo(e) {
		return e.target.classList.contains('backdrop') ||
			(e.target.classList.contains('close-lyrics-icon')) || e.target.parentNode.classList.contains('close-lyrics-icon');
	}
});