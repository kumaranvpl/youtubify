angular.module('app').controller('NewReleasesController', function($rootScope, $scope, $http, utils) {
    utils.showLoader();

    $http.get('albums/latest-releases').success(function(data) {
        $scope.albums = data;
        utils.hideLoader();

        setTimeout(function() {
            $rootScope.$emit('lazyImg:refresh');
        })
    });

    $scope.playAlbum = function(album) {
        $rootScope.autoplay = true;

        utils.toState('album', {
            artistName: album.artist.name,
            name: album.name
        });
    };
});


