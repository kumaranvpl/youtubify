angular.module('app').controller('TopAlbumsController', function($rootScope, $scope, $http, utils) {
    utils.showLoader();

    $http.get('albums/top').success(function(data) {
        $scope.topAlbums = data;
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


