'use strict';

angular.module('app')

.controller('SocialLoginController', function($rootScope, $scope, $http, $state, $translate, users, modal, utils) {

    $scope.credentials = {};

    $scope.loginWith = function(service) {
        var url = $scope.baseUrl + 'auth/social/' + service;

        var left = (screen.width/2)-(600/2);
        var top = (screen.height/2)-(500/2);

        window.$tempScope = $scope;

        window.open(url, "Authenticate Account", 'menubar=0, location=0, toolbar=0, titlebar=0, status=0, width=600, height=500, '+'left='+left+', top='+top);
    };

    $scope.socialLoginCallback = function(user) {
        window.$tempScope = undefined;

        if (user) {
            users.current = user;
            $state.go(utils.previousState || 'songs');
        } else {
            $scope.requestUserEmail();
        }
    };

    $scope.socialLoginCallbackError = function() {
        $mdToast.show($mdToast.simple({ position: 'bottom right' }).content($translate.instant('genericSocialError')));
    };

    $scope.createAndLoginUser = function() {
        $http.post($rootScope.baseUrl+'auth/social/request-email-callback', { email: $scope.credentials.email }).success(function(data) {
            $scope.goToDashboard(data);
        }).error(function(data) {

            //User with email already exists and has password
            //so we need to request it to connect the accounts
            if (data.code === 1) {
                $scope.closeModal();
                $scope.requestUserPassword();
            }
        })
    };

    $scope.connectAccounts = function() {
        $http.post($rootScope.baseUrl+'auth/social/connect-accounts', {password: $scope.credentials.password}).success(function(data) {
            $scope.goToDashboard(data);
        }).error(function(data) {
            $scope.errorMessage = data;
        })
    };

    $scope.requestUserPassword = function() {
        modal.show('request-password', $scope);
    };

    $scope.requestUserEmail = function() {
        modal.show('request-email', $scope);
    };

    $scope.closeModal = function() {
        modal.hide();
    };

    $scope.goToDashboard = function(user) {
        users.current = user;
        $rootScope.tempUser = undefined;
        $scope.credentials = {};
        $scope.errorMessage = undefined;

        $scope.closeModal();
        $state.go('songs');
    }
});