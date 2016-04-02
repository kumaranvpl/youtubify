angular.module('app').directive('ckEditor', function(utils) {
    return {
        restrict: 'A',
        link: function ($scope, el, attrs) {
            if (angular.isUndefined(window.CKEDITOR)) {
                utils.loadScript($scope.baseUrl+'assets/js/ckeditor/ckeditor.js', function() {
                    initEditor.apply(this, [$scope, el, attrs]);
                });
            } else {
                initEditor($scope, el, attrs);
            }
        }
    };

    function initEditor($scope, el, attrs) {
        CKEDITOR.config.basicEntities = false;
        CKEDITOR.replace(el[0], {
            height: el.parent()[0].getBoundingClientRect().height - 145,
            skin: 'bootstrapck',
            htmlEncodeOutput: false,
            entities: false
        });

        CKEDITOR.instances['editor'].on('change', function(a) {
            $scope[attrs.ckEditor] = CKEDITOR.instances['editor'].getData();
        });

        CKEDITOR.on('instanceLoaded', function() {
            angular.element(window).on('resize', function() {
                CKEDITOR.instances['editor'].resize('100%', el.parent()[0].getBoundingClientRect().height - 20);
            });
        });

        $scope.$watch(attrs.ckEditor, function(newTemplate) {
            if (newTemplate) {
                CKEDITOR.instances['editor'].setData(newTemplate);
            }
        });

        $scope.$on('$destroy', function() {
            CKEDITOR.instances['editor'].destroy();
        });
    }
});
