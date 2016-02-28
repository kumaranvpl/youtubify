angular.module('app').controller('ArtistAboutController', function($rootScope, $scope, $http, utils) {
    $scope.$watch('tabs.active', function(newTab, oldTab) {
        if (newTab === 'about' && ! $scope.initiated) {
            getBio();
        }
    });

    function getBio() {
        $scope.aboutLoading = true;

        $http.post('artist/'+$scope.artist.name+'/get-bio').success(function(data) {
            $scope.bio = data.bio;
            $scope.images = data.images;
            $scope.initiated = true;
            $scope.aboutLoading = false;
        });
    }
});


