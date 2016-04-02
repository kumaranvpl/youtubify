angular.module('app').factory('clipboard', function($translate) {
    var clipboard = {

        /**
         * Copy given text to clipboard if supported by browser.
         *
         * @param {string} text
         */
        copy: function(text) {
           var input = document.createElement('input'); input.value = text;
           document.body.appendChild(input);
           input.select();

           try {
               var copied = document.execCommand('copy');
           } catch(err) {
               var copied = false;
           }
            console.log(copied);
           document.body.removeChild(input);

            if (copied) {
                alertify.delay(2000).success($translate.instant('linkCopySuccess'));
            } else {
                alertify.delay(2000).error($translate.instant('linkCopyFail'));
            }
        }
    };

    return clipboard;
});