'use strict';

angular.module('app')

.directive('appearanceInput', function(sass) {
    return {
        restrict: 'A',
        link: function($scope, el) {
            var value = sass.applyTransforms($scope.variable.value, $scope.selectedStylesheet.variables);

            var color = tinycolor(value);

            if (color.isValid()) {
                var preview = angular.element('<div class="color-preview" style="background-color: '+color.toString()+'"></div>');

                el.parent().append(preview);
                var picker = new jscolor.color(el[0], { binding: false, adjust: false, required: false, hash: true, styleElement: preview[0]});

                preview.on('click', function() {
                    picker.showPicker();
                });
            }
        }
   	};
});