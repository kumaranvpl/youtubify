
angular.module('app')

.factory('utils', ['$rootScope', '$translate', '$state', '$sce', function($rootScope, $translate, $state, $sce) {
    return {
        settings: {},

        albumUrl: function(album) {
            if ( ! album) return;

            if (album.album) album = album.album;

            var url = $rootScope.baseUrl+(! this.getSetting('enablePushState') ? '#/' : '')+'album/';

            if (album.artist_id && album.artist) {
                url+=this.encodeUrlParam(album.artist.name)+'/';
            }

            url+=this.encodeUrlParam(album.name);

            return url;
        },

        artistUrl: function(artist) {
            if ( ! artist) return;

            if (angular.isString(artist)) artist = { name: artist };

            //get artist if we get passed in an album
            if (artist.artist) artist = artist.artist;

            if (artist.name === 'Various Artists') {
                return;
            }

            var url = $rootScope.baseUrl+(! this.getSetting('enablePushState') ? '#/' : '')+'artist/';

            return url+this.encodeUrlParam(artist.name);
        },

        /**
         * Compile fully qualified track url.
         *
         * @param {object} track
         * @returns {string}
         */
        trackUrl: function(track) {
            if ( ! track) return;

            var url = $rootScope.baseUrl+(! this.getSetting('enablePushState') ? '#/' : '')+'track/';

            return url+track.id;
        },

        /**
         * Remove node matching given selector from DOM.
         *
         * @param {string} selector
         */
        removeNode: function(selector) {
            var el = document.querySelector(selector);

            if (el) {
                el.parentNode.removeChild(el);
            }
        },

        /**
         * Find closest node to element matching given selector.
         * @param {object} el
         * @param {string} selector
         * @returns {*}
         */
        closest: function(el, selector) {
            var matchesFn;

            // find vendor prefix
            ['matches','webkitMatchesSelector','mozMatchesSelector','msMatchesSelector','oMatchesSelector'].some(function(fn) {
                if (typeof document.body[fn] == 'function') {
                    matchesFn = fn;
                    return true;
                }
                return false;
            });

            if (el[matchesFn](selector)) return el;

            // traverse parents
            while (el!==null) {
                parent = el.parentElement;
                if (parent!==null && parent[matchesFn](selector)) {
                    return parent;
                }
                el = parent;
            }

            return null;
        },

        showLoader: function(overlay) {
            document.body.classList.add('loading');

            if (overlay) {
                document.body.classList.add('loading-with-overlay');
            }

            document.querySelector('#splash').style.display = 'flex';
        },

        hideLoader: function() {
            document.body.classList.remove('loading');
            document.body.classList.remove('loading-with-overlay');
            document.querySelector('#splash').style.display = 'none';
        },

        /**
         * Return image url or if no passed return default
         * image url for given resourceName (artist or album)
         *
         * @param {string} url
         * @param {string} resourceName
         * @returns {*}
         */
        img: function(url, resourceName) {
            if ( ! url) {
                return $rootScope.baseUrl+'assets/images/'+resourceName+'-no-image.png';
            }

            return url;
        },

        /**
         * Load js script and execute callback if provided.
         *
         * @param {string} url
         * @param {function|undefined} callback
         */
        loadScript: function(url, callback) {
            var filename = url.replace(/^.*[\\\/]/, ''), r = false;

            //bail if we have already loaded this script
            if (document.getElementById(filename)) return;

            var s = document.createElement('script');
            s.src = url;
            s.id = filename;

            if (callback) {
                s.onload = s.onreadystatechange = function() {
                    if ( !r && (!this.readyState || this.readyState == 'complete')) {
                        r = true; callback();
                    }
                };
            }

            document.body.appendChild(s);
        },

        /**
         * Convert seconds into 0:00 format
         *
         * @param {int} time
         * @returns {string}
         */
        secondsToMSS: function(time) {
            time = Math.floor(time);

            var minutes = Math.floor(time / 60),
                seconds = (time - minutes * 60)+'';

            if (seconds == 0) seconds = '00';
            if (seconds.length < 2) seconds = '0'+seconds;

            return minutes+':'+seconds;
        },

        /**
         * Truncate string to given length.
         *
         * @param {string} text
         * @param {int} length
         * @returns {string}
         */
        truncate: function(text, length) {
            if (text && text.length > length) {
                return text.substring(0, length)+'...';
            }

            return text;
        },

        /**
         * Add comma at every thousand in the given number.
         *
         * @param {int} number
         * @returns {string}
         */
        humanReadableNumber: function(number) {
            if ( ! number) return number;
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        getCurrentStateName: function() {
            return $state.current.name;
        },

        /**
         * Replace dashes to spaces on given string.
         *
         * @param string
         * @returns string
         */
        dashesToSpaces: function(string) {
            return string.split('-').join(' ');
        },

        /**
         * Generate random string of given length.
         *
         * @param {int|undefined} length
         * @returns {string}
         */
        randomString: function(length) {
            if ( ! length) length = 15;

            return Math.random().toString(36).substr(2, length);
        },

        /**
         * Capitalize given string.
         *
         * @param {string} string
         * @returns {string}
         */
        capitalize: function(string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
        },

        /**
         * Register given html with angular so we can output it with ng-bind-html.
         *
         * @param {string} html
         * @returns {string}
         */
        trustHtml: function(html) {
            return $sce.trustAsHtml(html);
        },

        /**
         * Get either a default logo or one user has uploaded.
         *
         * @param {string} type
         * @returns {string}
         */
        getLogoUrl: function(type) {
            if ( ! type) type = 'light';

            var logo = this.getSetting('logo_url');

            if (logo) {
                return $rootScope.baseUrl+'assets/images/custom_logo_light.png';
            } else {
                return $rootScope.baseUrl+'assets/images/logo_'+type+'.png';
            }

        },

        /**
         * Transition to given state.
         *
         * @param {string} state
         * @param {promise} params
         */
        toState: function(state, params) {
            if (params) {
                for(var key in params) {
                    params[key] = this.encodeUrlParam(params[key]);
                }
            }

            return $state.go(state, params);
        },

        /**
         * Replace + with %2B and %20 (space) with +
         *
         * @param {string} string
         * @returns {*}
         */
        encodeUrlParam: function(string) {
            if ( ! string || ! angular.isString(string)) return string;

            return string.replace('+', '%2B').replace(/ /g, '+').replace('#', '%23');
        },

        /**
         * Replace + with %20
         *
         * @param {string} string
         * @returns {*}
         */
        decodeUrlParam: function(string) {
            if ( ! string || ! angular.isString(string)) return string;

            return decodeURIComponent(string.replace(/\+/g, ' '));
        },

        /**
         * Return whether active state or it's parent matches any of the given names.
         *
         * @param {string|array} names
         * @returns {boolean}
         */
        stateIs: function(names) {
            if ( ! angular.isArray(names)) names = [names];

            for (var i = 0; i < names.length; i++) {
                if ($state.includes(names[i])) return true;
            }
        },

        /**
         * Return a translation for given key.
         *
         * @param {string} key
         * @param {object|undefined} params
         *
         * @returns {string}
         */
        trans: function(key, params) {
            return $translate.instant(key, params);
        },

        /**
         * Return base url for the site.
         *
         * @returns {string}
         */
        baseUrl: function() {
            return $rootScope.baseUrl;
        },

        throttle: function(func, wait, options) {
            var context, args, result;
            var timeout = null;
            var previous = 0;
            if (!options) options = {};
            var later = function() {
                previous = options.leading === false ? 0 : Date.now();
                timeout = null;
                result = func.apply(context, args);
                if (!timeout) context = args = null;
            };
            return function() {
                var now = Date.now();
                if (!previous && options.leading === false) previous = now;
                var remaining = wait - (now - previous);
                context = this;
                args = arguments;
                if (remaining <= 0 || remaining > wait) {
                    if (timeout) {
                        clearTimeout(timeout);
                        timeout = null;
                    }
                    previous = now;
                    result = func.apply(context, args);
                    if (!timeout) context = args = null;
                } else if (!timeout && options.trailing !== false) {
                    timeout = setTimeout(later, remaining);
                }
                return result;
            };
        },

        getAllSettings: function() {
            return this.settings;
        },

        getSetting: function(name, defaultValue) {
            if (name.indexOf('env') > -1) {
                var setting = this.settings['env'][name.split('.')[1]];
            } else {
                var setting = this.settings[name];
            }

            if (typeof setting === 'undefined') {
                setting = defaultValue;
            }

            if (setting === '1' || setting === '0') {
                return parseInt(setting);
            }

            return setting;
        },

        setAllSettings: function(settings) {
            if (angular.isObject(settings)) {
                this.settings = settings;
            }
        }
    };
}]);