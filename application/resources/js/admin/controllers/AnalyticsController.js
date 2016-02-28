'use strict';

angular.module('app').controller('AnalyticsController', function($scope, $http) {
    $scope.stats = {};

    $http.get('admin-stats').success(function(data) {
        $scope.stats = data;
    })
});
