'use strict';

angular.module('app').controller('UsersController', function($scope, $rootScope, $state, modal, utils, users) {
    $scope.users = users;

    $scope.deleteUsers = function() {
        if (utils.isDemo) {
            alertify.delay(2000).error('Sorry, you can\'t do that on demo site.');
            $scope.selectedItems = [];
            return;
        }

        users.delete($scope.selectedItems).success(function() {
            $scope.selectedItems = [];
            $scope.paginate($scope.params);
        }).error(function(data) {
            alertify.delay(2000).success(data);
        })
    };

    $scope.showCreateUserModal = function() {
        modal.show('new-user', $scope);
    };

    $scope.showUpdateUserModal = function(user) {
        $scope.userModel = angular.copy(user);
        modal.show('update-user', $scope);
    };

    $scope.updateUser = function() {
        users.updateAccountSettings($scope.userModel, $scope.userModel.id).success(function() {
            alertify.delay(2000).success(utils.trans('updatedUser'));
            $scope.paginate($scope.params);
        }).error(function(data) {
            $scope.setErrors(data);
        });
    };

    $scope.createNewUser = function() {
        users.register($scope.userModel).success(function() {
            $scope.closeModal();
            alertify.delay(2000).success(utils.trans('createdNewUser'));
            $scope.paginate($scope.params);
            $scope.errors = [];
        }).error(function(data) {
            $scope.setErrors(data);
        });
    };

    $scope.closeModal = function() {
        modal.hide();
        $scope.userModel = {};
    };

    $scope.paginate = function(params) {
        if ($scope.usersAjaxInProgress) return;

        $scope.usersAjaxInProgress = true;
        utils.showLoader();

        users.paginate(params).success(function(data) {
            $scope.items = data.data;
            $scope.totalItems = data.total;

            $scope.usersAjaxInProgress = false;
            utils.hideLoader();
        })
    };

    $scope.paginate($scope.params);
});
