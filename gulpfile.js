"use strict";
var gulp = require('gulp'),
sass = require('gulp-sass'),
uglify = require('gulp-uglify'),
notify = require('gulp-notify'),
autoprefixer = require('gulp-autoprefixer'),
sourcemaps = require('gulp-sourcemaps'),
concat = require('gulp-concat'),
rename = require('gulp-rename'),
plumber = require('gulp-plumber'),
del = require('del'),
mjml = require('gulp-mjml'),
print = require('gulp-print'),
tinypng = require('gulp-tinypng'),
copy = require('gulp-contrib-copy');

var TINYPNG_API = "tyvNmfx8GqoWNUxCLsnrLyTwLqPfNAnq";
var path_sass = "./public/web-res/assets/sass";
var path_css = "./public/web-res/assets/css";
var path_mail = "./resources/views/front/email";
var path_images_sources = "./public/web-res/assets/img_sources";
var path_images = "./public/web-res/assets/img";


gulp.task('default', ['sass', 'sass-min', 'mjml']);

gulp.task('sass', function () {
    del([
        './public/web-chipeur/assets/css/**/*'
        ]);
    gulp.src(path_sass+'/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass())
    .on('error', notify.onError("Error: <%= error.message %>"))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('.'))
    .pipe(plumber())
    .pipe(gulp.dest(path_css));
});
gulp.task('sass-min', function () {
    del([
        './public/web-chipeur/assets/css/**/*'
        ]);
    gulp.src(path_sass+'/**/*.scss')
    .pipe(sass({
        outputStyle: 'compressed'
    }))
    .on('error', notify.onError("Error: <%= error.message %>"))
    .pipe(autoprefixer())
    .pipe(plumber())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(path_css));
});

gulp.task('mjml', function () {
    var make = function(file){
        gulp.src(path_mail+"/"+file)
        .pipe(mjml())
        .pipe(plumber())
        .pipe(rename((function (element) {
            element.extname = ".blade.php"
        })))
        .pipe(gulp.dest(path_mail))
    };

    make("template.mjml");
    make("template_button.mjml");
});

gulp.task('images', function(cb) {
    return gulp.src([path_images_sources+'/**/*.png',
        path_images_sources+'/**/*.jpg',
        path_images_sources+'/**/*.jpeg'])
    .pipe(tinypng(TINYPNG_API))
    .pipe(gulp.dest(path_images));
});

gulp.task('watch', function () {
    gulp.watch(path_sass+'/**/*.scss', ['sass', 'sass-min']);
    gulp.watch(path_mail+'/**/*.mjml', ['mjml']);
});