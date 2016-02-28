angular.module('app').controller('SongsController', function($scope, $translate, userLibrary, player) {
    $scope.library = userLibrary;

    //field songs are sorted on in the view
    $scope.params = {
        sort: 'name'
    };

    $scope.sortSongs = function(sort) {
        $scope.params.sort = sort;
    };

    /**
     * Translate given sort field.
     *
     * @param {string} string
     * @returns {string}
     */
    $scope.translate = function(string) {
        string = string.replace('-', '');

        if (string.indexOf('spotify_popularity') > -1) {
            string = 'popularity';
        }

        if (string === 'album.name') {
            string = 'albumName';
        }

        if (string === 'album.artist.name') {
            string = 'artistName';
        }

        if (string === 'pivot.created_at') {
            string = 'dateAdded';
        }

        return $translate.instant(string);
    };
});


