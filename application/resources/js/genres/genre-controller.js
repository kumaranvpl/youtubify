angular.module('app').controller('GenreController', function($scope, $rootScope, $http, $stateParams, utils) {
    utils.showLoader();

    var name = utils.decodeUrlParam($stateParams.name);

    $scope.page = 2;
    $scope.ajaxInProgress = false;

    $http.get('genres/'+name+'/paginate-artists').success(function(data) {
        $scope.genre = data.genre;
        $scope.artists = data.artists.data;
        utils.hideLoader();
    }).error(function() {
        utils.toState('404');
        utils.hideLoader();
    });

    $scope.load = function() {
        if ($scope.ajaxInProgress) return;
        $scope.ajaxInProgress = true;
        utils.showLoader();

        if ($scope.query) {
            $scope.disableInfinateScroll = false;
            $scope.page = 1;
        }

        $http.get('genres/'+name+'/paginate-artists', {params: {page: $scope.page, query: $scope.query}}).success(function(data) {
            if ($scope.query) {
                $scope.artists = data.artists.data;
            } else {
                $scope.artists = $scope.artists.concat(data.artists.data);
                $scope.page = data.artists.current_page + 1;

                if (data.artists.total <= $scope.artists.length) {
                    $scope.disableInfinateScroll = true;
                }
            }

            $scope.ajaxInProgress = false;
            utils.hideLoader();
        })
    };

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


