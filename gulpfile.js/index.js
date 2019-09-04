const { series, src, dest } = require('gulp');
const ftpCredentials = require('./ftp-credentials');

const PATH = Object.freeze({
  DEST: 'wp-content',
  PHP: 'src/**/*.php'
})

function php() {
  return src(PATH.PHP)
    .pipe(dest(PATH.DEST));
}

function defaultTask(cb) {
  // place code for your default task here
  console.log("FTP credentials:", ftpCredentials);
  cb();
}

exports.default = defaultTask;