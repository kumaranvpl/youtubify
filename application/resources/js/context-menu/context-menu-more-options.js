angular.module('app').directive('moreOptionsMenu', function($rootScope, contextMenu) {
    return {
        restrict: 'A',
        link: function($scope, el, attrs) {
            var open = false,
                menu = document.getElementById('#context-menu');

            el.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if ( ! open || contextMenu.open) {
                    var context = attrs.moreOptionsMenu || el[0].parentNode.getAttribute('context-menu-item') || 'track',
                        rect = el[0].getBoundingClientRect();

                    contextMenu.context = context;
                    contextMenu.item = $scope.track || $scope.album || $scope.$eval(attrs.moreOptionsItem);
                    contextMenu.attrs = attrs;
                    contextMenu.$scope = $scope;

                    contextMenu.generateMenu(context);
                    contextMenu.positionMenu({
                        clientX: rect.left,
                        clientY: rect.top + rect.height
                    });

                    open = true;
                    contextMenu.open = false;
                } else {
                    contextMenu.hide();
                    open = false;
                }
            });

            $rootScope.$on('contextmenu.closed', function() {
                open = false;
            });
        }
    }
});