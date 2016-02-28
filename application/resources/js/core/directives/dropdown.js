angular.module('app').directive('dropdown', function() {
	return {
		restrict: 'A',
		link: function ($scope, el) {
			var menu = el[0].querySelector('.dropdown-menu');

			el.on('click', function() {
                hideAllDropdownMenus(menu);
				menu.classList.toggle('hidden');
			});
		}
	};

    function hideAllDropdownMenus(menu) {
        var menus = document.querySelectorAll('.dropdown-menu');
        for (var i = 0; i < menus.length; i++) {
            if (menu !== menus[i]) {
                menus[i].classList.add('hidden');
            }
        }
    }
});