const { series, parallel, src, dest } = require('gulp');
const babel = require('gulp-babel');
const minify = require('gulp-babel-minify');
const rimraf = require('rimraf');
const ftpCredentials = require('./ftp-credentials');

const PATH = Object.freeze({
  DEST: 'wp-content',
  PHP: 'src/**/*.php',
  JS: 'src/**/*.js',
  CSS: 'src/**/*.css',
  IMG: ['src/**/*.png', 'src/**/*.jpg', 'src/**/*.jpeg']
});

function clear(cb) {
  return rimraf(PATH.DEST, cb);
}

function php() {
  return src(PATH.PHP)
    .pipe(dest(PATH.DEST));
}

function javascript() {
  return src(PATH.JS)
    .pipe(babel({
      presets: ['@babel/env']
    }))
    .pipe(minify({
      mangle: {
        keepClassName: true
      }
    }))
    .pipe(dest(PATH.DEST));
}

function css() {
  return src(PATH.CSS)
    .pipe(dest(PATH.DEST));
}

function img() {
  return src(PATH.IMG)
    .pipe(dest(PATH.DEST));
}

function defaultTask(cb) {
  // place code for your default task here
  console.log("FTP credentials:", ftpCredentials);
  cb();
}

exports.build = series(clear, parallel(php, javascript, css, img));
exports.default = defaultTask;