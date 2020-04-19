
    var gulp = require('gulp'),
        sass = require('gulp-sass'),
        watch = require('gulp-watch'),
        concat = require('gulp-concat'),
        uglify = require('gulp-uglify'),
        gutil = require('gulp-util'),
        rename = require('gulp-rename');

    //task para o sass
    gulp.task('scss', function () {
        return gulp.src('scss/*.scss')
            .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
            .pipe(concat('main.min.css'))
            .pipe(gulp.dest('css/min'));
    });
    // task para o js -- nao se faz mais necessario no momento
    // gulp.task('script', function () {
    //         return gulp.src('js/*.js')
    //             .pipe(concat('main.js'))
    //             .pipe(rename({suffix: '.min'}))
    //             .pipe(uglify())
    //             .on('error', function (err) {
    //                 gutil.log(gutil.colors.red('[Error]'), err.toString());
    //             })
    //             .pipe(gulp.dest('js/min'));
    // });

    //task para o watch
    gulp.task('watch', function () {
        gulp.watch('scss/*.scss', ['scss']);
        // gulp.watch('js/*.js', ['script']);
    });

    //task default gulp
    gulp.task('default', ['scss', 'watch']);