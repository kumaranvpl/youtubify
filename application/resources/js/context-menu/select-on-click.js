angular.module('app').directive('selectOnClick', function() {
    return {
        restrict: 'A',
        link: function (scope, el) {
            el.on('click', function () {
                this.setSelectionRange(0, this.value.length)
            });
        }
    };
});
