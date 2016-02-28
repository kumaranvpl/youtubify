angular.module('app').directive('globalDropdownClose', function() {
	return {
		restrict: 'A',
		link: function ($scope, el) {
			el.on('click', function(e) {
			  	if ( ! e.target.classList.contains('dropdown-trigger') && ! e.target.classList.contains('dropdown-item') && ! e.target.classList.contains('icon-sort-alt-up')) {
					hideMenus();
				}
			});
		}
	};

	function hideMenus() {
		var menus = document.querySelectorAll('.dropdown-menu');
		for (var i = 0; i < menus.length; i++) {
			menus[i].classList.add('hidden');
		}
	}
});