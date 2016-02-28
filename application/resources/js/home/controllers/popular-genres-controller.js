angular.module('app').controller('PopularGenresController', function($rootScope, $scope, $http, utils) {
    utils.showLoader();

    $http.get('genres/'+utils.getSetting('homepageGenres')+'?limit=10').success(function(data) {
        $scope.genres = data;
        utils.hideLoader();

        setTimeout(function() {
            $rootScope.$emit('lazyImg:refresh');
        })
    });

    /**
     * Go to artist state and start playing all his tracks and albums.
     *
     * @param {object} artist
     */
    $scope.playArtist = function(artist) {
        $rootScope.autoplay = true;
        utils.toState('artist', {name: artist.name});
    };
});


