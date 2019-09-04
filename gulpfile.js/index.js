const { series, src, dest } = require('gulp');
const babel = require('gulp-babel');
const ftpCredentials = require('./ftp-credentials');

const PATH = Object.freeze({
  DEST: 'wp-content',
  PHP: 'src/**/*.php',
  JS: 'src/**/*.js',
  CSS: 'src/**/*.css',
  IMG: ['src/**/*.png', 'src/**/*.jpg', 'src/**/*.jpeg']
})

function php() {
  return src(PATH.PHP)
    .pipe(dest(PATH.DEST));
}

function javascript() {
  return src(PATH.JS)
    .pipe(babel({
      presets: ['@babel/env']
    }))
    .pipe(dest(PATH.DEST));
}

function defaultTask(cb) {
  // place code for your default task here
  console.log("FTP credentials:", ftpCredentials);
  cb();
}

exports.javascript = javascript;
exports.default = defaultTask;