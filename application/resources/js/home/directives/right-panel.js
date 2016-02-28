angular.module('app').value('queuePanelStatus', { open:true })

.directive('rightPanel', function($rootScope, player, queuePanelStatus, localStorage, utils) {
    return {
        restrict: 'E',
        templateUrl: 'assets/views/directives/right-panel.html',
        replace: true,
        link: function($scope) {
            queuePanelStatus.open = (document.body.clientWidth <= 1550 || utils.getSetting('hide_queue')) ? false : localStorage.get('queuePanelOpen', true);

            $scope.player = player;
            $scope.queuePanelStatus = queuePanelStatus;

            $scope.$watchCollection('player.queue', function(newQueue) {
                if (newQueue && newQueue.length) {
                    setTimeout(function() {
                        $rootScope.$emit('lazyImg:refresh');
                    })
                }
            })
        }
    };
});