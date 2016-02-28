angular.module('app').directive('leftPanel', function($rootScope, player, search, users) {
    return {
        restrict: 'E',
        templateUrl: 'assets/views/directives/left-panel.html',
        replace: true,
        scope: true,
        link: function($scope, el) {
            var searchBar = el.find('input');

            $scope.player = player;
            $scope.search = search;
            $scope.users  = users;

            searchBar.on('focus', function() {
                if (search.query || search.results.length) {
                    search.showPanel();
                }
            });

            if ($rootScope.isPhone) {
                $rootScope.$on('$stateChangeStart', function() {
                    el.addClass('closed');
                });
            }
        }
    };
});