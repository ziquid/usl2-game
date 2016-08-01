var gulp       = require('gulp');
var less       = require('gulp-less');
var notify     = require('gulp-notify');
var sourcemaps = require('gulp-sourcemaps');
var exec       = require('child_process').exec;

gulp.task('less', function () {
  gulp.src('less/style.less')
    .pipe(less())
    .on("error", notify.onError({
      title: "Error Compiling Less",
      message: '<%= error.message %>',
      sound: true
    }))
    .pipe(gulp.dest('css'))
});

// gulp lessdev: compile the less with a sourcemap and clear the CSS/JS cache.
gulp.task('lessdev', function () {
  gulp.src('less/style.less')
    .pipe(sourcemaps.init())
    .pipe(less())
    .on("error", notify.onError({
        title: "Error Compiling Less",
        message: '<%= error.message %>',
        sound: true
    }))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('css'));
  exec('d7 cc css-js', function (err, stdout, stderr) {
    console.log(stdout);
    console.log(stderr);
  });
});

// gulp default: do lessdev.
gulp.task('default', ['less']);
