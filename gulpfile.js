var gulp = require('gulp');
var jshint = require('gulp-jshint');
var sass   = require('gulp-sass');
var minifyCSS = require('gulp-minify-css');
var path = require('path');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var sourcemaps = require('gulp-sourcemaps');
var ngAnnotate = require('gulp-ng-annotate');
var browserSync = require('browser-sync');
var autoprefixer = require('gulp-autoprefixer');
var reload      = browserSync.reload;
var minifyHTML = require('gulp-minify-html');
var angularTemplateCache = require('gulp-angular-templatecache');
var addStream = require('add-stream');

function prepareTemplates() {
    return gulp.src([
        'assets/views/directives/*.html'
    ])
        .pipe(minifyHTML())
        .pipe(angularTemplateCache({
            module: 'app',
            root: 'assets/views/directives'
        }));
}

gulp.task('sass', function () {
    gulp.src('./application/resources/sass/app.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(rename('styles.min.css'))
        .pipe(gulp.dest('./assets/css'))
        .pipe(gulp.dest('./assets/css/custom-stylesheets/original'))
        .pipe(browserSync.reload({stream:true}));
});

gulp.task('scripts.install', function() {
    return gulp.src([
        'application/resources/js/vendor/angular.js',
        'application/resources/js/vendor/angular-ui-router.js',
        'application/resources/js/installer.js'
    ]).pipe(concat('install.min.js'))
        .pipe(gulp.dest('assets/js'))
        .pipe(browserSync.reload({stream:true}));
});

gulp.task('scripts.core', function() {
    return gulp.src([
        'application/resources/js/vendor/angular.js',
        'application/resources/js/vendor/angular-ui-router.js',
        'application/resources/js/vendor/angular-translate.js',
        'application/resources/js/vendor/*.js',
        'application/resources/js/core/app.js',
        'application/resources/js/core/routes.js',
        'application/resources/js/core/acl.js',
        'application/resources/js/**/*.js',
        '!application/resources/js/installer.js'
    ])
        .pipe(addStream.obj(prepareTemplates()))
        .pipe(concat('core.min.js'))
        .pipe(gulp.dest('assets/js'))
        .pipe(browserSync.reload({stream:true}));
});

gulp.task('minify', function() {
    gulp.src('assets/js/core.min.js').pipe(ngAnnotate()).pipe(uglify()).pipe(gulp.dest('assets/js'));
    gulp.src('assets/js/install.min.js').pipe(ngAnnotate()).pipe(uglify()).pipe(gulp.dest('assets/js'));

    gulp.src('assets/css/styles.min.css')
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false,
            remove: false
        }))
        .pipe(minifyCSS({compatibility: 'ie10'}))
        .pipe(gulp.dest('assets/css'))
        .pipe(gulp.dest('assets/css/custom-stylesheets/original'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    browserSync({
        proxy: "localhost/youtubify/"
    });

    gulp.watch('application/resources/js/**/*.js', ['scripts.core', 'scripts.install']);
    gulp.watch('application/resources/js/installer.js', ['scripts.install']);
    gulp.watch('application/resources/sass/**/*.scss', ['sass']);
});

// Default Task
gulp.task('default', ['watch']);