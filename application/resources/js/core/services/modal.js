
angular.module('app')

.factory('modal', function($rootScope, $compile, $templateRequest) {
    modal = {
        openModal: false,

        show: function(name, scope) {
            $templateRequest('assets/views/modals/'+name+'.html').then(function(template) {

                //cache modal element
                modal.openModal = document.body.insertBefore($compile(template)(scope)[0], null);

                //add classes for showing and animating modal
                modal.openModal.classList.remove('hidden');

                requestAnimationFrame(function() {
                    modal.openModal.querySelector('.modal-content').classList.add('scale-in');
                });

                //focus input in the modal if there is any
                var input = modal.openModal.querySelector('input[type="text"]');

                setTimeout(function() {
                    input && input.focus();
                }, 300)
            });
        },

        hide: function() {
            if (this.openModal) {
                this.openModal.querySelector('.modal-content').classList.remove('scale-in');

                setTimeout(function() {
                    modal.openModal.classList.add('hidden');
                    modal.openModal.parentNode.removeChild(modal.openModal);
                    modal.openModal = false;
                }, 200);
            }
        },

        showErrors: function(errors) {
            this.openModal = document.getElementById('playlist-name-modal');

            for (var key in errors) {
                this.openModal.querySelector('.modal-error').textContent = errors[key];
            }
        },

        confirm: function(options) {
            var template =
            '<div class="modal" id="confirm-modal">'+
                '<div class="backdrop" modal-backdrop></div>'+
                '<div class="modal-content">' +
                    '<div class="modal-header">'+
                        '<h1>{{:: \''+options.title+'\' | translate }}</h1>'+
                        '<div ng-click="closeModal()" class="close-modal-icon"><i class="icon icon-cancel"></i></div>'+
                    '</div>'+
                    '<p>{{:: \''+options.content+'\' | translate }}</p>'+
                    (options.subcontent ? '<strong>{{:: \''+options.subcontent+'\' | translate }}</strong>' : '')+
                    '<div class="buttons">'+
                        '<button ng-click="closeModal()" class="cancel">{{:: \'cancel\' | translate }}</button>'+
                        '<button ng-click="confirm()" class="primary">{{:: \''+options.ok+'\' | translate }}</button>'+
                    '</div>'+
                '</div>'+
            '</div>';

            var $scope = $rootScope.$new(true);
            $scope.closeModal = options.onClose;
            $scope.confirm = function() {
                options.onConfirm(options.params);
                options.onClose();
            };

            this.openModal = document.body.insertBefore($compile(template)($scope)[0], null);

            requestAnimationFrame(function() {
                modal.openModal.querySelector('.modal-content').classList.add('scale-in');
            });
        }
    };

    return modal;
});