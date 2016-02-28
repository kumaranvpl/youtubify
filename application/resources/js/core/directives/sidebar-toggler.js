angular.module('app').directive('sidebarToggler', function() {
	return {
		restrict: 'A',
		link: function ($scope, el, attrs) {
			el.on('click', function() {
                if (attrs.sidebarToggler) {
                    var node = document.querySelector(attrs.sidebarToggler);
                } else {
                    var node = document.getElementsByClassName('togglable-sidebar')[0];
                }

                node.classList.toggle('closed');
			});
		}
	};
});