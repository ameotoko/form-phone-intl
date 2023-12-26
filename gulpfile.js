const gulp = require('gulp');

gulp.task('default', function (cb) {
    gulp.src('node_modules/intl-tel-input/build/**')
        .pipe(gulp.dest('public/'))
    ;

    cb();
});
