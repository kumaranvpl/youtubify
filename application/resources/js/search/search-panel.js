angular.module('app').directive('searchPanel', function(search) {
    return {
        restrict: 'E',
        templateUrl: 'assets/views/directives/search-panel.html',
        replace: true,
        scope: true,
        link: function($scope) {
            $scope.searchPanelNeeded = document.body.clientWidth >= 627;

            setTimeout(function() {
                angular.element(document.querySelector('.search-panel-backdrop')).on('click', function(e) {
                    if (e.target.classList.contains('search-panel-backdrop')) {
                        $scope.$apply(function() {
                            search.hidePanel();
                        })
                    }
                })
            })
        }
    };
});