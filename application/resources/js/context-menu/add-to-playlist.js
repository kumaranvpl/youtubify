angular.module('app').directive('addToPlaylist', function() {
	return {
		restrict: 'E',
		templateUrl: 'assets/views/directives/add-to-playlist-context-menu.html',
		replace: true,
		link: function ($scope, el) {
			el.on('click', function(e) {
			  	if (e.target.classList.contains('back') || e.target.parentNode.classList.contains('back')) {
					el.addClass('slide-out');
				}
			})
		}
	};
});