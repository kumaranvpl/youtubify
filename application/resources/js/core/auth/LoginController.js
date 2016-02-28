'use strict';

angular.module('app')

.controller('LoginController', function($rootScope, $scope, $http, $state, users, utils, modal) {

    $scope.registrationEnabled = utils.getSetting('enableRegistration', true);

    $scope.credentials = {
        remember: true
    };

    $scope.passResetCredentials = {
        email: ''
    };

    $scope.resetPasswordError = '';

    $scope.resetPassword = function() {
        utils.showLoader(true);

        $http.post($rootScope.baseUrl + 'password/email', $scope.passResetCredentials).success(function(data) {
            alertify.delay(2000).success(data);
            $scope.resetPasswordError = '';
            $scope.closePasswordResetModal();
        }).error(function(data) {
            $scope.resetPasswordError = data.email || data;
        }).finally(function() {
            utils.hideLoader();
        })
    };

    $scope.submit = function() {
        $scope.loading = true;

        return users.login($scope.credentials).success(function() {
            $scope.credentials = {};
            utils.toState($rootScope.previousState.name || 'songs', $rootScope.previousState.params);
        }).error(function(data) {
            $scope.errors = data;
        }).finally(function() {
            $scope.loading = false;
        })
    };

    $scope.showPasswordResetModal = function() {
        modal.show('reset-password', $scope);
    };

    $scope.closePasswordResetModal = function() {
        $scope.passResetCredentials = {};
        modal.hide();
    };
});