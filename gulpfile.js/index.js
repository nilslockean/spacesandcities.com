const { series, parallel, src, dest, watch } = require('gulp');
const gutil = require('gulp-util');
const babel = require('gulp-babel');
const minify = require('gulp-babel-minify');
const rimraf = require('rimraf');
const ftp = require('vinyl-ftp');
const { ftpCredentials } = require('./ftp-credentials');

const PATH = Object.freeze({
  DEST_LOCALHOST: '/Users/nilspersson/dev/ccsc2/wp-content',
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

var defaultTask = series(clear, parallel(php, javascript, css, img));

function deployToLocal() {
  return src(PATH.DEST + '/**/*')
    .pipe(dest(PATH.DEST_LOCALHOST));
}

function deployToProduction() {

  Object.assign(ftpCredentials, {
    parallel: 10,
    log: gutil.log
  })
  
  console.log(ftpCredentials)
  
  const conn = ftp.create(ftpCredentials);

  return src([PATH.DEST + '/**/*'], { base: '.', buffer: false })
    .pipe(conn.newer('/')) // only upload newer files
    .pipe(conn.dest('/'));
}

function watchFiles() {
  watch(PATH.CSS, css);
  watch(PATH.PHP, php);
  watch(PATH.JS, javascript);
}

function watchToLocal() {
  watch(PATH.CSS, series(css, deployToLocal));
  watch(PATH.PHP, series(php, deployToLocal));
  watch(PATH.JS, series(javascript, deployToLocal));
}

function watchToStage() {}

function watchToProduction() {}

exports.default = series(clear, defaultTask, watchToLocal);
exports.build = defaultTask;
exports.deploy = series(clear, defaultTask, deployToProduction);