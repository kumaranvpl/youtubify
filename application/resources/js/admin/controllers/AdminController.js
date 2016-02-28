'use strict';

angular.module('app')

.controller('AdminController', ['$rootScope', '$scope', function($rootScope, $scope) {

    //current page for all paginations in admin
    $scope.currentPage = 1;

    $scope.itemsPerPage = 15;

    $rootScope.$on('$stateChangeSuccess', function() {
        $scope.selectedItems = [];
        $scope.currentPage = 1;
    })
}]);
