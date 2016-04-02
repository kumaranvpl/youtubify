angular.module('app').directive('adContainer', function(utils) {
    return {
        restrict: 'C',
        replace: true,
        priority: 0,
        link: function ($scope, el) {
            setTimeout(function() {
                var html = el.html();

                if (html && html.indexOf('google') > -1) {
                    utils.loadScript('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js');
                } else {
                    var pattern = /.+?src=.(.+?).>/g, match;

                    while (match = pattern.exec(html)) {
                        utils.loadScript(match[1]);
                    }
                }

                setTimeout(function() {
                    (adsbygoogle = window.adsbygoogle || []).push({});
                });

                setTimeout(function() {
                    el.css('display', 'flex');
                }, 600)
            })
        }
    }
});