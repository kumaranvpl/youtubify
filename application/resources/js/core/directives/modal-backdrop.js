angular.module('app').directive('modalBackdrop', function(modal) {
	return {
		restrict: 'A',
		link: function ($scope, el) {
			el.on('click', function(e) {
				modal.hide();
			});
		}
	};
});