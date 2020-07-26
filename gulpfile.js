var concat = require('gulp-concat');
var gulp = require('gulp');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');

gulp.task('scss', function (done) {
    gulp.src('assets/css/editor.scss')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(concat('editor.min.css'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('assets/css'));
    gulp.src('assets/css/frontend.scss')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(concat('frontend.min.css'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('assets/css'));
    done();
});

gulp.task('default', gulp.series(['scss']));

gulp.task('watch', function () {
    gulp.watch('assets/css/*.scss', gulp.series(['scss']));
});
