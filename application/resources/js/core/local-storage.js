angular.module('app').factory('localStorage', function() {
	return {
		/**
		 * Retrieve value under given key from local storage.
		 * 
		 * @param {string} key
		 * @param {mixed} defaultValue
		 * @return mixed
		 */
		get: function(key, defaultValue) {
			var value = JSON.parse(localStorage.getItem(key));
			
			if ( ! value) {
				return defaultValue;
			}

			return value.value;
		},

		/**
		 * Store value into browser local storage.
		 * 
		 * @param {string} key
		 * @param {*} value
		 */
		set: function(key, value) {
			var data = { value: value, time: new Date().getTime() };

			return localStorage.setItem(key, JSON.stringify(data));
		},

        /**
         * Remove item from local storage.
         *
         * @param key
         * @returns {*}
         */
        remove: function(key) {
            return localStorage.removeItem(key);
        }
	};
});