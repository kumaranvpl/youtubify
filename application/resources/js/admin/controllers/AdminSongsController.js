'use strict';

angular.module('app').controller('AdminSongsController', function($scope, $rootScope, $state, $http, modal, utils) {
    $scope.showUpdateItemModal = function(item) {
        $scope.selectedItem = angular.copy(item);
        modal.show('update-track', $scope);
    };

    $scope.updateTrack = function() {
        if (utils.isDemo) {
            alertify.delay(2000).error('Sorry, you can\'t do that on demo site.');
            return;
        }

        $http.put('track/'+$scope.selectedItem.id, $scope.selectedItem).success(function() {
            alertify.delay(2000).success(utils.trans('updatedTrack'));
            $scope.paginate($scope.params);
            $scope.closeModal();
        }).error(function(data) {
            $scope.setErrors(data);
        });
    };

    $scope.paginate = function(params) {
        if ($scope.ajaxInProgress || ! params) return;

        $scope.ajaxInProgress = true;
        utils.showLoader();

        $http.get('track', {params:params}).success(function(data) {
            $scope.items = data.data;
            $scope.totalItems = data.total;

            $scope.ajaxInProgress = false;
            utils.hideLoader();
        })
    };

    $scope.paginate($scope.params);
});
