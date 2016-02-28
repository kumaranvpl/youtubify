'use strict';

angular.module('app').controller('TranslationsController', function($scope, $http, $translate, utils, modal, $timeout) {
    utils.showLoader();

    var original = 'en';

    $scope.activeLocale = original;

    /**
     * Get language lines for currently active locale.
     */
    $http.get('translations').success(function(data) {
        $scope.translations = {};
        $scope.translations[data.activeLocale] = formatLines(data.lines);
        $scope.locales = data.locales;

        $timeout(function() {
            $scope.activeLocale = data.activeLocale;
        })
    }).finally(function() {
        utils.hideLoader();
    });

    /**
     * Open model to confirm locale deletion.
     */
    $scope.confirmTranslationsReset = function() {
        modal.confirm({
            title: 'resetTranslations',
            content: 'confirmLangReset',
            subcontent: 'confirmPlaylistDelete2',
            ok: 'reset',
            onConfirm: $scope.resetTranslations,
            onClose: $scope.closeModal
        });
    };

    /**
     * Reset translation lines to original for currently selected locale.
     */
    $scope.resetTranslations = function() {
        $http.post('reset-translations', { locale: $scope.activeLocale }).success(function(data) {
            $scope.translations[$scope.activeLocale] = formatLines(data);
            alertify.delay(2000).success($translate.instant('translationsReset'));
        }).error(function(data) {
            alertify.delay(2000).error(data);
        })
    };

    /**
     * Update translation lines on the server.
     */
    $scope.saveTranslations = function() {
        var lines = {};

        $scope.translations[$scope.activeLocale].forEach(function(item) {
            lines[item.key] = item.trans;
        });

        $http.post('update-translations', { lines: lines, locale: $scope.activeLocale }).success(function() {
            alertify.delay(2000).success($translate.instant('updatedTranslations'));
        }).error(function(data) {
            alertify.delay(2000).error(data);
        })
    };

    /**
     * Change currently selected locale.
     */
    $scope.changeLocale = function() {
        $http.post('update-settings', { env: { trans_locale: $scope.activeLocale } }).error(function(data) {
            alertify.delay(2000).error(data);
        });

        //fetch language lines for new translation if not fetched already
        if ( ! $scope.translations[$scope.activeLocale]) {
            utils.showLoader();

            $http.get('translation-lines/'+$scope.activeLocale).success(function(data) {
                $scope.translations[$scope.activeLocale] = formatLines(data);
                utils.hideLoader();
            })
        }

    };

    /**
     * Delete currently selected locale.
     */
    $scope.deleteActiveLocale = function() {
        $http.delete('locale/'+$scope.activeLocale).success(function() {
            $scope.locales.splice($scope.locales.indexOf($scope.activeLocale), 1);
            $scope.activeLocale = original;
            $scope.changeLocale();
        }).error(function(data) {
            alertify.delay(2000).error(data);
        });
    };

    /**
     * Create a new locale.
     *
     * @param {string} name
     */
    $scope.createNewLocale = function(name) {
        if ( ! name) return;

        $http.post('new-locale', { name: name }).success(function() {
            $scope.activeLocale = name;
            $scope.locales.push(name);
            $scope.newLocaleName = '';
            $scope.changeLocale();
            alertify.delay(2000).success($translate.instant('createdNewLocale'));
        }).error(function(data) {
            alertify.delay(2000).error(data);
        })
    };

    $scope.closeModal = function() {
        modal.hide();
    };

    /**
     * Format language lines from backed so we can loop
     * and bind to them with angular.
     *
     * @param {object} lines
     * @returns {Array}
     */
    formatLines = function(lines) {
        var formatted = [];

        angular.forEach(lines, function(trans, key) {
            formatted.push({ key: key, trans: trans });
        });

        return formatted;
    };
});
