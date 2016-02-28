angular.module('app').controller('AdminMailController', function($scope, $http, utils) {

    $scope.templateNames = [];

    $http.get('mail/templates').success(function(data) {
        $scope.templates = data;

        angular.forEach(data, function(text, name) {
             $scope.templateNames.push(name);
        });

        $scope.selectTemplate($scope.templateNames[0]);
    });

    $scope.selectTemplate = function(name) {
        $scope.activeTemplate = $scope.templates[name];
        $scope.activeTemplateName = name;
    };

    $scope.saveTemplate = function() {
        $http.post('mail/template/'+$scope.activeTemplateName, { content: $scope.activeTemplate }).success(function() {
            alertify.delay(2000).success(utils.trans('mailTemplateSaved'))
        }).error(function(data) {
            alertify.delay(2000).error(data);
        });
    };
});
