'use strict';

angular.module('app').directive('customCssEditor', function(utils) {

    return {
        restrict: 'A',
        link: function($scope) {
           if (angular.isUndefined(window.ace)) {
               utils.loadScript($scope.baseUrl+'assets/js/ace/src-min/ace.js', function() {
                   initAceEditor.apply(this, [$scope]);
               });
           } else {
               initAceEditor($scope);
           }
        }
   	};

    function initAceEditor($scope) {
        setTimeout(function() {
            var editor = ace.edit('editor');
            editor.setTheme("ace/theme/tomorrow_night");
            editor.getSession().setMode("ace/mode/css");
            editor.setValue($scope.selectedStylesheet.customCss, 1);

            editor.on('blur', function() {
                $scope.selectedStylesheet.customCss = editor.getValue();
            });
        }, 100);
    }
});