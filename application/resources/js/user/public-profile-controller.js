angular.module('app').controller('PublicProfileController', function($scope, $stateParams, $http, users, utils, player) {
    $scope.users = users;
    $scope.selectedTab = 'playlists';

    utils.showLoader();

    $http.get('users/'+$stateParams.id).success(function(data) {
        $scope.user = data;
        $scope.profileReady = true;
        utils.hideLoader();
    });

    users.loadCurrentUsersFollows();

    $scope.selectTab = function(name) {
        $scope.selectedTab = name;
    };

    $scope.followUser = function(user) {
        if ( ! users.current) utils.toState('login');
        users.follow(user || $scope.user);
    };

    $scope.unfollowUser = function(user) {
        if ( ! users.current) utils.toState('login');

        users.unfollow(user || $scope.user).success(function() {
            if (users.current && $scope.user.id == users.current.id) {
                users.loadCurrentUsersFollows();
            }
        });
    };

    $scope.playPlaylist = function(playlist) {
        player.loadQueue(playlist.tracks, true);
    };

    $scope.getPlaylistImage = function(playlist) {
        for (var i = 0; i < playlist.tracks.length; i++) {
            if (playlist.tracks[i].album && playlist.tracks[i].album.image) {
                return playlist.tracks[i].album.image;
            }
        }

        return utils.img(false, 'album');
    };

    $scope.followingUser = function(user) {
        if ( ! users.current.followed_users) return;

        for (var i = 0; i < users.current.followed_users.length; i++) {
            if (users.current.followed_users[i].id == user.id) {
                return true;
            }
        }
    };

    $scope.followingThisUser = function() {
        if ( ! users.current.followed_users || ! $scope.user) return;

        for (var i = 0; i < users.current.followed_users.length; i++) {
            if (users.current.followed_users[i].id == $scope.user.id) {
                return true;
            }
        }
    }
});


