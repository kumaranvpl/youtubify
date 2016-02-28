angular.module('app').controller('AppController', function($rootScope, $scope, $state, utils, users, search) {
    $scope.utils = utils;
    $scope.users = users;
    $scope.currentState = $state.current;
    $scope.search = search;

    var docWidth = document.body.clientWidth;
    $rootScope.isPhone  = docWidth <= 627;
    $rootScope.isTablet = docWidth <= 1024 && docWidth > 627;

    var ad1 = utils.getSetting('ad_slot_1'),
        ad2 = utils.getSetting('ad_slot_2'),
        ad3 = utils.getSetting('ad_slot_3'),
        ad4 = utils.getSetting('ad_slot_4'),
        ad5 = utils.getSetting('ad_slot_5');

    if (ad1) {
        $scope.ad1 = utils.trustHtml(ad1);
    }

    if (ad2) {
        $scope.ad2 = utils.trustHtml(ad2);
    }

    if (ad3) {
        $scope.ad3 = utils.trustHtml(ad3);
    }

    if (ad4) {
        $scope.ad4 = utils.trustHtml(ad4);
    }

    if (ad5) {
        $scope.ad5 = utils.trustHtml(ad5);
    }

    $scope.getCurrentStateName = function() {
        var name = utils.getCurrentStateName();

        if (name == 'phone-search') name = 'search';

        return utils.trans(name);
    };

    $scope.shouldPlayerControlsBeHidden = function() {
        return utils.stateIs(['admin', 'login', 'register']);
    };
});