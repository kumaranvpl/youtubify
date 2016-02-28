angular.module('app').directive('onEnter', function () {
    return function ($scope, element, attrs) {
        element.bind('keydown keypress', function (e) {
            if(e.which === 13) {
                $scope.$apply(attrs.onEnter);
                e.preventDefault();
            }
        });
    };
});