'use strict';

angular.module('app').controller('ShareModalController', function($scope, $rootScope, $translate, $http, utils, contextMenu, clipboard) {
    $scope.utils = utils;

    $scope.emails = [];

    //folder or file that is being shared
    $scope.shareable = $scope.$parent.shareable || contextMenu.item;

    $scope.makeShareableUrl = function(shareable) {
        var url = $rootScope.baseUrl+(! utils.getSetting('enablePushState') ? '#/' : '');
        console.log(shareable);
        if (shareable.duration || shareable.identifier) {
            url+='track/'+shareable.id;
            $scope.type = 'track';
        }

        else if (shareable.release_date && shareable.artist_id === 0) {
            url+='album/'+utils.encodeUrlParam(shareable.name);
            $scope.type = 'album';
        }

        else if (shareable.artist && shareable.artist.id) {
            url+='album/'+utils.encodeUrlParam(shareable.artist.name)+'/'+utils.encodeUrlParam(shareable.name);
            $scope.type = 'album';
        }

        else if (shareable.artist_id) {
            url+='album/'+utils.encodeUrlParam(shareable.tracks[0].artists[0])+'/'+utils.encodeUrlParam(shareable.name);
            $scope.type = 'album';
        }

        else if ( ! angular.isUndefined(shareable.owner)) {
            url+='playlist/'+shareable.id;
            $scope.type = 'playlist';
        }

        else {
            url+='artist/'+utils.encodeUrlParam(shareable.name);
            $scope.type = 'artist';
        }

        return url;
    };

    //public link to view the shareable
    $scope.link = $scope.makeShareableUrl($scope.shareable);

    $scope.copyLinkToClipboard = function() {
        clipboard.copy($scope.link);
    };

    $scope.closeModal = function() {
        modal.hide();
    };

    $scope.closeModalAndSendEmails = function() {
        if ($scope.emails.length) {
            utils.showLoader();

            var payload = {emails: $scope.emails.map(function(e) { return e.text; }), link: $scope.link, name: $scope.shareable.name, message: $scope.emailMessage};

            $http.post('send-links', payload).success(function(data) {
                alertify.delay(2000).success(data);
                modal.hide();
            }).error(function() {
                alertify.delay(2000).error($translate.instant('genericError'));
            }).finally(function() {
                utils.hideLoader();
            })
        } else {
            modal.hide();
        }
    };
});
