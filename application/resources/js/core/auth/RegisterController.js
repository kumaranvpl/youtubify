'use strict';

angular.module('app')

.controller('RegisterController', ['$rootScope', '$scope', '$state', 'users', function($rootScope, $scope, $state, users) {

    $scope.credentials = {};

    $scope.submit = function() {
        $scope.loading = true;

        return users.register($scope.credentials).success(function() {
            $scope.credentials = {};
            $state.go('songs');
        }).error(function(data) {
            $scope.errors = data;
        }).finally(function() {
            $scope.loading = false;
        })
    };

}]);



