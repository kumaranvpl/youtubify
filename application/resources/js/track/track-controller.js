angular.module('app').controller('TrackController', function($rootScope, $http, $scope, $stateParams, utils) {
    utils.showLoader();

    $http.get('get-track/'+$stateParams.id).success(function(data) {

        //set track to autoplay once album state is loaded
        $rootScope.autoplay = {
            trackName: data.name
        };

        //load this tracks album state
        if (data.artist) {
            utils.toState('album', {
                artistName: data.album.artist.name,
                name: data.album.name
            })
        } else {
            utils.toState('album', {
                name: data.album.name
            })
        }
    }).error(function() {
        utils.toState('404');
        utils.hideLoader();
    });
});


