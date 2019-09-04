const ftpCredentials = require('./ftp-credentials');

function defaultTask(cb) {
  // place code for your default task here
  console.log("FTP credentials:", ftpCredentials);
  cb();
}

exports.default = defaultTask;