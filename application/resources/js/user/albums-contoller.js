angular.module('app').controller('AlbumsController', function($rootScope, $scope, $state, $translate, userLibrary, utils) {
    $scope.library = userLibrary;

    //field albums are sorted on in the view
    $scope.params = {
        sort: 'name'
    };

    $scope.sortAlbums = function(sort) {
        $scope.params.sort = sort;
    };

    /**
     * Translate given sort field.
     *
     * @param {string} string
     * @returns {string}
     */
    $scope.translate = function(string) {
        if (string === 'artist.name') {
            string = 'artist';
        }

        if (string === '-tracks.length') {
            string = 'duration';
        }

        return $translate.instant(string.replace('-', ''));
    };

    $scope.playAlbum = function(album) {
        $rootScope.autoplay = true;

        utils.toState('album', {
            artistName: album.artist.name,
            name: album.name
        });
    };
});