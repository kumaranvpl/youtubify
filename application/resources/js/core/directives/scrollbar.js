angular.module('app').directive('prettyScrollbar', function() {
	return {
		restrict: 'A',
		link: function ($scope, el) {
            Ps.initialize(el[0]);

            setTimeout(function() {
                if( ! el.scrollTop()){
                    el.scrollTop(el.scrollTop()+1);
                    el.scrollTop(el.scrollTop()-1);
                }
            }, 350)
		}
	};
});