angular.module('app').directive('contextMenu', function($rootScope, contextMenu, utils) {
    return {
        restrict: 'A',
        link: function($scope, el) {
            el.on('click', function(e) {
                if (e.target.classList.contains('context-menu-item')) {
                    e.stopPropagation();
                    e.preventDefault();

                    if (e.target.dataset.action === 'addToPlaylist') {
                        el[0].querySelector('.add-to-playlist').classList.remove('slide-out');
                    } else {
                        $scope[e.target.dataset.action](contextMenu.item);
                        el.addClass('hidden');
                    }
                }
            });

            el.on('contextmenu', function(e) {
                e.preventDefault();
            });

            //hide custom menu on window resize and update max height
            el.css('max-height', window.innerHeight - 20);
            window.onresize = function() {
                contextMenu.hide();
                el.css('max-height', window.innerHeight - 20);
            };

            //hide custom context menu on left click if user didn't click inside the menu itself or on more options button
            angular.element(document).on('click', function(e) {
                var button = e.which || e.button,
                    clickedInsideMenu = utils.closest(e.target, '#context-menu') || utils.closest(e.target, '.more-options');

                if (button === 1 && !clickedInsideMenu) {
                    $rootScope.$emit('contextmenu.closed');
                }
            });
        }
    }
});