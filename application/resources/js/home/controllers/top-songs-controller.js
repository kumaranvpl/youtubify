angular.module('app').controller('TopSongsController', function($rootScope, $scope, $http, utils) {
    utils.showLoader();

    $http.get('tracks/top').success(function(data) {
        data.forEach(function(item) {
            item.duration = utils.secondsToMSS(item.duration / 1000);
        });

        $scope.topSongs = data;
        utils.hideLoader();
    });
});


