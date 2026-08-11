const { Client } = require('../clickbucks-pay-per-view-platform/Files/application/node_modules/ssh2');
const conn = new Client();

console.log('Connecting to VPS...');
conn.on('ready', () => {
  console.log('Connected!\n');

  const cmd = `cd /var/www/monetizearticle-repo && git remote -v`;
  
  conn.exec(cmd, (err, stream) => {
    if (err) throw err;
    stream.on('close', (code, signal) => {
      conn.end();
    }).on('data', (data) => {
      console.log('OUTPUT (monetizearticle): ' + data);
    }).stderr.on('data', (data) => {
      console.log('STDERR (monetizearticle): ' + data);
    });
  });
}).connect({
  host: '145.223.22.69',
  port: 22,
  username: 'root',
  password: "Ti13xphE'5ZU&7Vx"
});
