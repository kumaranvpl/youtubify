angular.module('app').directive('infiniteScroll', function(utils) {
    return {
        restrict: 'A',
        link: function ($scope, element, attrs) {
            var offset = parseInt(attrs.threshold) || 0;
            var el = element[0];

            var throttled = utils.throttle(function() {
                if ( ! $scope.disableInfinateScroll && el.scrollTop + el.offsetHeight >= el.scrollHeight - offset) {
                    $scope.$apply(attrs.infiniteScroll);
                }
            }, 300);

            element.bind('scroll', throttled);
        }
    };
});