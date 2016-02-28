angular.module('app')

.directive('authErrors', function() {
    return {
        restrict: 'E',
        template: '<ul class="errors"></ul>',
        replace: true,
        link: function($scope, el) {
            var form = document.querySelector('form');

            $scope.$watch('errors', function(errors) {
                if ( ! errors) return;

                el.html('');

                for (var key in errors) {
                    el.append('<li>'+errors[key]+'</li>');
                }
            });
        }
    };
});