angular.module('app').factory('users', function($http, $rootScope, $state, utils, modal, player) {

    var users = {

        /**
         * Currently logged in user.
         */
        current: false,

        /**
         * Account settings model for currently logged in user.
         */
        accountSettings: {},

        /**
         * Model for password change form.
         */
        changePasswordModel: {
            oldPassword: '',
        },

        follow: function(user) {
            return $http.post('users/'+user.id+'/follow').success(function() {
                users.current.followed_users.push(user);
            });
        },

        unfollow: function(user) {
           return $http.post('users/'+user.id+'/unfollow').success(function() {
               for (var i = 0; i < users.current.followed_users.length; i++) {
                   if (users.current.followed_users[i].id == user.id) {
                       users.current.followed_users.splice(i, 1);
                   }
               }
           })
        },

        /**
         * Load users current user is following if not loaded already.
         *
         * @returns {promise|undefined}
         */
        loadCurrentUsersFollows: function() {
            if ( ! this.current || this.current.followers) return;

            return $http.get('users/'+this.current.id+'/followed_users').success(function(data) {
                users.current.followed_users = data;
            })
        },

        /**
         * Paginate all existing users.
         *
         * @returns {promise}
         */
        paginate: function(params) {
            return $http.get('users', {params: params});
        },

        /**
         * Login in user matching given credentials.
         *
         * @param {object} credentials
         * @returns {promise}
         */
        login: function(credentials) {
            return $http.post('auth/login', credentials).success(function(data) {
                users.assignCurrentUser(data);
            })
        },

        /**
         * Register a new user with given credentials.
         *
         * @param {object} credentials
         * @returns {promise}
         */
        register: function(credentials) {
            return $http.post('auth/register', credentials).success(function(data) {
               if ( ! users.current) {
                   users.assignCurrentUser(data);
               }
            })
        },

        /**
         * Set given user as currently logged in user.
         *
         * @param {object} user
         */
        assignCurrentUser: function(user) {
            if (!user) return;

            this.current = user;
            this.accountSettings.username = this.getUsername();
            this.accountSettings.first_name = user.first_name;
            this.accountSettings.last_name = user.last_name;
            this.accountSettings.gender = user.gender;

            $rootScope.$emit('user.newCurrent');
        },

        /**
         * Delete given user from database.
         *
         * @param {array|object} values
         * @returns {*|void}
         */
        delete: function(values) {
            if (angular.isArray(values)) {
                return $http.post('users', {users:values})
            } else {
                return $http.delete('users/'+values.id);
            }
        },

        /**
         * Change currently logged in users password.
         */
        changePassword: function() {
            $http.post($rootScope.baseUrl+'password/change', this.changePasswordModel).success(function(data) {
                alertify.delay(2000).success(data);
                users.closeModal();
            }).error(function(data) {
                users.lastErrors = [];

                angular.forEach(data, function(error) {
                   if (angular.isArray(error)) {
                       error = error[0];
                   }
                    users.lastErrors.push(error);
                });
            })
        },

        /**
         * Logout current logged in user.
         *
         * @returns {promise}
         */
        logout: function() {
            return $http.post('auth/logout').success(function() {
                player.stop();
                users.current = false;
                $state.go('login');
                $rootScope.$emit('user.loggedOut');
                alertify.delay(2000).success(utils.trans('logOutSuccess'));
            });
        },

        /**
         * Return username if set otherwise first part of email.
         *
         * @param {object|undefined} user
         * @returns {string|undefined}
         */
        getUsername: function(user) {
            if ( ! user) user = this.current;

            if ( ! user || ! user.email) return;

            if (user.username) {
                return user.username;
            }

            return user.email.split('@')[0];
        },

        getNameOrEmail: function(user) {
            var name = '';

            if ( ! user) user = this.current;

            if (user.first_name) {
                name = user.first_name;
            }

            if (name && user.last_name) {
                name += ' ' + user.last_name;
            }

            if (name) {
                return name;
            } else {
                return user.email.split('@')[0];
            }
        },

        /**
         * Return users avatar url or url for a default avatar.
         *
         * @returns {string}
         */
        getAvatar: function(user) {
            if ( ! user) user = this.current;

            if (user.avatar_url) {
                if ( ! user.avatar_url.indexOf('//')) {
                    return $rootScope.baseUrl+user.avatar_url;
                } else {
                    return user.avatar_url;
                }
            }

            if (user.gender === 'male' || ! user.gender) {
                return  $rootScope.baseUrl+'assets/images/avatars/male.png';
            } else {
                return $rootScope.baseUrl+'assets/images/avatars/female.png'
            }
        },

        /**
         * Update account settings for currently logged in user.
         */
        updateAccountSettings: function(settings, id) {
            var payload = settings || this.accountSettings,
                userId  = id || this.current.id;

            return $http.put($rootScope.baseUrl+'users/'+userId, payload).success(function(data) {

                //user is updating his open profile, we can show a confirmation message here.
                if ( ! settings) {
                    alertify.delay(2000).success(utils.trans('profileUpdateSuccess'));
                    users.closeModal();
                    users.current = data;
                }
            })
        },

        /**
         * Remove currently logged in users custom avatar.
         *
         * @returns {promise}
         */
        removeAvatar: function() {
            return $http.delete($rootScope.baseUrl + 'users/'+this.current.id+'/avatar').success(function(data) {
                users.current.avatar_url = '';
                alertify.delay(2000).success(data);
            })
        },

        showAccountSettingsModal: function($event, fieldToFocus) {
            var scope = $rootScope.$new(true);
            scope.users = users;
            scope.activeTab = fieldToFocus || 'settings';

            scope.upload = function(file) {
                $http.post('users/'+users.current.id+'/avatar', file, {
                    transformRequest: angular.identity,
                    headers: {'Content-Type': undefined}
                }).success(function (data) {
                    users.current.avatar_url = data;
                }).error(function(data) {
                    users.lastErrors = [];

                    angular.forEach(data, function(error) {
                        if (angular.isArray(error)) {
                            error = error[0];
                        }
                        users.lastErrors.push(error);
                    });
                })
            };

            modal.show('account-settings', scope);
        },

        closeModal: function() {
            modal.hide();
            this.changePasswordModel = {};
        }
    };

    return users;
});
