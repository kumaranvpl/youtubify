'use strict';

angular.module('app').directive('shareIcons', function ($rootScope, $compile, utils) {

    var shareIcons = {
        restrict: 'E',
        template: '<section class="social-icons"></section>',
        replace: true,
        link: function ($scope, el) {

            //if we already have a shareable, generate icons now
            if ($scope.shareable) {
                generateIcons($scope, el);

            //otherwise we might need to wait for ajax request, so we'll generate the icons on event
            } else {
                $scope.$on('shareable.ready', function() {
                    generateIcons($scope, el);
                });
            }
        }
    };

    var available = [
        'facebook',
        'twitter',
        'googleplus',
        'pinterest',
        'tumblr',
        'stumbleupon',
        'blogger'
    ];

    function generateIcons($scope, el) {
        var html = '';

        for (var i = 0; i < available.length; i++) {
            html += '<div class="social-icon '+available[i]+'" data-service="'+available[i]+'" ed-tooltip="'+available[i]+'"></div>';
        }

        $compile(el.html(html))($scope);

        el.on('click', function(e) {
            if ( ! e.target.classList.contains('social-icon')) return;

            var width  = 575,
                height = 400,
                left   = (window.innerWidth  - width)  / 2,
                top    = (window.innerHeight - height) / 2,
                url    = urls[e.target.dataset.service](),
                opts   = 'status=1'+',width='+width+',height='+height+',top='+top+',left='+left;

            window.open(url, 'share', opts);
        });

        var urls = {

            base: $scope.makeShareableUrl($scope.shareable),

            blogger: function() {
                return 'https://www.blogger.com/blog_this.pyra?t&u='+this.base+'&n='+$scope.shareable.name;
            },

            stumbleupon: function() {
                return 'http://www.stumbleupon.com/submit?url='+this.base;
            },

            tumblr: function() {
                var base = 'https://www.tumblr.com/widgets/share/tool?shareSource=legacy&canonicalUrl=&posttype=photo&title=&caption=';
                return base+$scope.shareable.name+'&content='+this.getImage()+'&url='+this.formatUrl(this.base);
            },

            twitter: function() {
                return 'https://twitter.com/intent/tweet?url='+this.formatUrl(this.base)+'&text='+utils.trans('share')+' '+$scope.shareable.name+' '+utils.trans('on')+' '+utils.getSetting('siteName');
            },

            facebook: function() {
                return 'https://www.facebook.com/sharer/sharer.php?u='+this.formatUrl(this.base);
            },

            googleplus: function() {
                return 'https://plus.google.com/share?url='+this.formatUrl(this.base);
            },

            pinterest: function() {
                return 'https://pinterest.com/pin/create/button/?url='+this.base+'&media='+this.getImage()
            },

            formatUrl: function(url) {
                return encodeURIComponent(url.replace(/^\/\//, 'http://'));
            },

            getImage: function() {
                if ($scope.type === 'album') {
                    return utils.img($scope.shareable.image, 'album');
                } else if ($scope.type === 'playlist') {
                    return $scope.shareable.tracks && $scope.shareable.tracks.length ? $scope.shareable.tracks[0].album.image : utils.img(false, 'album');
                } else if ($scope.type === 'track') {
                    return $scope.shareable.album ? $scope.shareable.album.image : utils.img(false, 'album');
                } else {
                    return utils.img($scope.shareable.image_large);
                }
            }
        };
    }

    return shareIcons;
});