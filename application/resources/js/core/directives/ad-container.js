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