angular.module('app').factory('sass', function($rootScope) {
    var sass = {

        /**
         * Sass.js Compiler instance.
         */
        compiler: false,

        /**
         * Sass file that contains all @import statements.
         *
         * @type {string}
         */
        importsFile: false,

        /**
         * Apply sass transformations to value such as color darken and lighten.
         *
         * @param {string|undefined} color
         * @param {object} variables
         *
         * @returns string
         */
        applyTransforms: function(color, variables) {
            var value     = color || $scope.variable.value,
                isDarken  = value.indexOf('darken') > -1,
                isLighten = value.indexOf('lighten') > -1;

            if (isDarken || isLighten) {
                var matches = value.match(/\((.+?),.*?([0-9]+)%\)/);
                var val = matches[1];
                var percent = value.match(/,.*?([0-9]+)%\)/)[2];

                //value that is being darkened is sass variable, so need to find its value
                if (val.indexOf('$') > -1) {
                    val = this.findVariableValue(variables, val);
                }

                if (isDarken) {
                    return tinycolor(val).darken(percent).toString();
                } else {
                    return tinycolor(val).lighten(percent).toString();
                }
            }

            return value;
        },

        /**
         * Find sass variable value in selected stylesheet. Recursive.
         *
         * @param {array} variables  variables to search trough
         * @param {string} variable  variable to look for
         *
         * @returns {string}
         */
        findVariableValue: function(variables, variable) {
            variable = variable.replace('$', '');

            for(var key in variables) {
                for (var i = 0; i < variables[key].length; i++) {
                    var obj = variables[key][i];

                    if (obj.name === variable) {

                        if (obj.value.indexOf('$') === -1) {
                            return obj.value
                        } else {
                            return this.findVariableValue(variables, obj.value);
                        }
                    }
                }
            }
        },

        /**
         * Compile files currently loaded into compiler to css.
         *
         * @param {function} callback
         */
        compile: function(callback) {
            this.compiler.compile(this.importsFile, function(result) {
                callback(result);
                sass.removeCustomCssFromCompiler();
            })
        },

        /**
         * Add custom css to be compiled into main css file on next compile operation.
         *
         * @param {string} customCss
         */
        addCustomCssToCompiler: function(customCss) {
            this.compiler.writeFile('custom-css.scss', customCss);
            this.importsFile += '@import "custom-css.scss";';
        },

        /**
         * Remove any custom css that was added to compiler. Should be called
         * after every successful compile operation to avoid duplicate css.
         */
        removeCustomCssFromCompiler: function() {
            this.compiler.removeFile('custom-css.scss');
            this.importsFile = this.importsFile.replace('@import "custom-css.scss";', '');
        },

        /**
         * Initiate sass compiler with given files.
         *
         * @param {array} files
         */
        initCompiler: function(files) {
            Sass.setWorkerUrl($rootScope.baseUrl+'assets/js/sass.worker.js');

            this.compiler = new Sass();

            this.compiler.writeFile(files.imports);
            this.importsFile = files.main;
        }
    };

    return sass;
});