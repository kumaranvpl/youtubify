'use strict';

angular.module('app').controller('AdminAlbumsController', function($scope, $rootScope, $state, $http, modal, utils) {
    $scope.showUpdateItemModal = function(item) {
        $scope.selectedItem = angular.copy(item);
        modal.show('update-album', $scope);
    };

    $scope.updateAlbum = function() {
        $http.put('album/'+$scope.selectedItem.id, $scope.selectedItem).success(function() {
            alertify.delay(2000).success(utils.trans('updatedAlbum'));
            $scope.paginate($scope.params);
            $scope.closeModal();
        }).error(function(data) {
            $scope.setErrors(data);
        });
    };

    $scope.getTotalNumberOfTracks = function(albums) {
        var num = 0;
        for (var i = 0; i < albums.length; i++) {
            num += albums[i].tracks.length;
        }

        return num;
    };

    $scope.paginate = function(params) {
        if ($scope.ajaxInProgress || ! params) return;

        $scope.ajaxInProgress = true;
        utils.showLoader();

        $http.get('album', {params:params}).success(function(data) {
            $scope.items = data.data;
            $scope.totalItems = data.total;

            $scope.ajaxInProgress = false;
            utils.hideLoader();
        })
    };

    $scope.paginate($scope.params);
});
