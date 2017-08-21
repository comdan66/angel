
/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

var gulp = require ('gulp'),
    livereload = require('gulp-livereload'),
    apidoc = require('gulp-apidoc');

gulp.task ('default', function () {
  livereload.listen ();

  ['./root/application/**/*.+(css|js|html|php)'].forEach (function (t) {
    gulp.watch (t).on ('change', function () {
      gulp.run ('reload');
    });
  });
});

gulp.task ('reload', function () {
  livereload.changed ();
  console.info ('\nReLoad Browser!\n');
});

gulp.task ('api', function () {
  livereload.listen ();

  apidoc ({
    src: './root/application/controllers/',
    dest: './root/apidoc/',
  },function () {
    gulp.run ('reload');
  });
    

  gulp.watch ('./root/application/controllers/**/*.+(css|js|html|php)').on ('change', function () {
    apidoc ({
      src: './root/application/controllers/',
    dest: './root/apidoc/',
    },function () {
      gulp.run ('reload');
    });
  });
});
