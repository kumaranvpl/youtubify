angular.module('app').directive('lyricsContainer', function(player) {
	return {
		restrict: 'A',
		link: function ($scope, el) {
			el.on('click', function(e) {
				if (shouldCloseLyrics(e)) {
					$scope.$apply(function() {
						player.toggleLyrics();
					})
				}
			});
		}
	};

	function shouldCloseLyrics(e) {
		return e.target.classList.contains('backdrop') ||
			(e.target.classList.contains('close-lyrics-icon')) || e.target.parentNode.classList.contains('close-lyrics-icon');
	}
});