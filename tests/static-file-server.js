var connect = require('connect');
var serveStatic = require('serve-static');
connect().use(serveStatic(__dirname + '/files')).listen(8080);
