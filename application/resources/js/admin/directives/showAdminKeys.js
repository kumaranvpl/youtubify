'use strict';

angular.module('app')

.directive('showAdminKeys', ['utils', function(utils) {
    return {
        restrict: 'A',
        compile: function(el) {
            el.on('click', function(e) {
                if (utils.isDemo) {
                    alertify.delay(2000).error('Sorry, you can\'t do that on demo site.');
                } else {
                    var nodes = document.querySelectorAll('#keys [type="password"]');

                    for (var i = 0; i < nodes.length; i++) {
                        nodes[i].setAttribute('type', 'text');
                    }

                    el.hide();
                }
            })
        }
   	}
}]);