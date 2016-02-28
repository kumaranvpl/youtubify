'use strict';

angular.module('app').controller('AppearanceController', function($scope, $http, modal, utils, sass) {
    $scope.sass = sass;

    initAppearancePage();

    /**
     * Open model to confirm deletion of selected custom stylesheet.
     */
    $scope.confirmStylesheetDeletion = function() {
        if ($scope.selectedStylesheet.name === 'original') {
            alertify.okBtn(utils.trans('gotIt')).alert(utils.trans('cantDeleteOriginalSheet'));
            return;
        }

        modal.confirm({
            title: 'deleteStylesheet',
            content: 'confirmStylesheetDelete',
            subcontent: 'confirmPlaylistDelete2',
            ok: 'delete',
            onConfirm: $scope.deleteSelectedStylesheet,
            onClose: $scope.closeModal
        });
    };

    /**
     * Open model to confirm reset of selected custom stylesheets variables.
     */
    $scope.confirmStylesheetReset = function() {
        modal.confirm({
            title: 'resetStylesheetVars',
            content: 'confirmStylesheetVarsReset',
            subcontent: 'confirmPlaylistDelete2',
            ok: 'reset',
            onConfirm: $scope.resetSelectedStylesheetVariables,
            onClose: $scope.closeModal
        });
    };

    /**
     * Delete currently selected stylesheet.
     */
    $scope.deleteSelectedStylesheet = function() {
        $http.delete('stylesheet/'+$scope.selectedStylesheet.name).success(function() {
            $scope.stylesheets.splice($scope.stylesheets.indexOf($scope.selectedStylesheet), 1);
            $scope.selectStylesheet($scope.stylesheets[0]);
            alertify.delay(2000).success(utils.trans('stylesheetDeleteSuccess'));
        });
    };

    /**
     * Reset currently selected stylesheets variables to original values.
     */
    $scope.resetSelectedStylesheetVariables = function() {
        $http.post('stylesheet/'+$scope.selectedStylesheet.name+'/reset').success(function(data) {
            $scope.selectedStylesheet.variables  = data.variables;
            $scope.selectedStylesheet.mainColors = data.mainColors;
            $scope.selectedStylesheet.customCss  = '';
            reloadCss();

            alertify.delay(2000).success(utils.trans('stylesheetResetSuccess'));
        }).error(function(data) {
            alertify.delay(2000).error(data);
        })
    };

    /**
     * Open modal for renaming currently selected stylesheet.
     */
    $scope.showRenameStylesheetModal = function() {
        if ($scope.selectedStylesheet.name === 'original') {
            alertify.okBtn(utils.trans('gotIt')).alert(utils.trans('cantRenameOriginalSheet'));
            return;
        }

        $scope.newStylesheetName = $scope.selectedStylesheet.name;
        modal.show('rename-stylesheet', $scope);
    };

    /**
     * Rename currently selected stylesheet.
     */
    $scope.renameStylesheet = function() {
        $http.put('rename-stylesheet/'+$scope.selectedStylesheet.name, { newName: $scope.newStylesheetName  }).success(function() {
            $scope.selectedStylesheet.name = $scope.newStylesheetName;
            $scope.closeModal();
            $scope.selectStylesheet($scope.selectedStylesheet);
        });
    };

    $scope.showStylesheetNameModal = function() {
        modal.show('new-stylesheet', $scope);
    };

    $scope.selectStylesheet = function(sheet) {
        $scope.selectedStylesheet = sheet;

        $http.post('update-settings', { selected_sheet: sheet.name === 'original' ? null : sheet.name });
    };

    $scope.openCustomCssModal = function() {
        modal.show('custom-css', $scope);
    };

    $scope.updateStylesheet = function() {
        if (utils.isDemo) {
            alertify.delay(2000).error(utils.trans('noDemoPermissions'));
            return;
        }

        utils.showLoader(true);

        var payload = {
            variables: angular.toJson($scope.selectedStylesheet.variables),
            name: $scope.selectedStylesheet.name,
            customCss: $scope.selectedStylesheet.customCss
        };

        sass.compiler.writeFile('variables.scss', compileVariablesToString());

        if ($scope.selectedStylesheet.customCss) {
            sass.addCustomCssToCompiler($scope.selectedStylesheet.customCss);
        }

        sass.compile(function(result) {
            payload.css = result.text;

            if (result.message) {
                alertify.okBtn(utils.trans('gotIt')).alert(utils.trans('errorInCustomCss')+result.message);
                return utils.hideLoader();
            }

            $http.put('update-stylesheet', payload).success(function() {
                reloadCss();
            }).finally(function() {
                utils.hideLoader();
            })
        });
    };

    $scope.createNewStylesheet = function() {
        if ($scope.creatingNewStylesheet) return;

        $scope.creatingNewStylesheet = true;

        $http.post('create-new-stylesheet', {name: $scope.newStylesheetName}).success(function(data) {
            var length = $scope.stylesheets.push(data);
            $scope.selectStylesheet($scope.stylesheets[length - 1]);
            $scope.closeModal();
        }).error(function(data) {
            alertify.delay(2000).error(data);
        }).finally(function() {
            $scope.creatingNewStylesheet = false;
        })
    };

    $scope.closeModal = function() {
        modal.hide();
        $scope.newStylesheetName = '';
    };

    /**
     * Check if given sass value should be represented as a select input.
     *
     * @param {string|boolean} value
     * @returns {boolean}
     */
    $scope.shouldBeSelect = function(value) {
        return value === 'false' || value === 'true';
    };
    
    $scope.$on('$$destroy', function() {
        sass.destroy();
    });

    function compileVariablesToString() {
        var compiled = '';

        angular.forEach($scope.selectedStylesheet.variables, function(group) {
            group.forEach(function(item) {
                compiled += '$'+item.name+':'+item.value+';';
            });
        });

        return compiled;
    }

    function initAppearancePage() {
        utils.showLoader();

        utils.loadScript($scope.baseUrl+'assets/js/jscolor/jscolor.js');
        utils.loadScript($scope.baseUrl+'assets/js/tinycolor.min.js');

        $http.get('available-stylesheets').success(function(data) {
            $scope.stylesheets = data.sheets;

            var selectedStylesheetName = utils.getSetting('selected_sheet') || 'original';

            for (var i = 0; i < $scope.stylesheets.length; i++) {
                if ($scope.stylesheets[i].name === selectedStylesheetName) {
                    $scope.selectedStylesheet = $scope.stylesheets[i];
                    break;
                }
            }

            sass.initCompiler(data.files);

            $scope.descriptions = data.files.variables.descriptions;
            $scope.originalVars = data.files.variables.vars;
            utils.hideLoader();
        });
    }

    /**
     * Force browser to reload main stylesheet.
     */
    function reloadCss() {
        var link = document.getElementById('main-stylesheet');
        link.href = link.href.split('?')[0] + "?id=" + new Date().getMilliseconds();
    }
});
