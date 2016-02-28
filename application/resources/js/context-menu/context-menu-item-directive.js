angular.module('app').directive('contextMenuItem',function(contextMenu) {
    return {
        restrict: 'A',
        link: function($scope, el, attrs) {
            el.on('contextmenu', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var item = $scope.track || $scope.album || $scope.$eval(attrs.contextMenuContextItem) || $scope.artist;

                contextMenu.show(e, item, attrs, $scope);
            });
        }
    }
});



