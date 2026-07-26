#!/bin/bash
set -e

echo "=== Step 1: Reset monetize_user password ==="
mysql -u root -e "ALTER USER 'monetize_user'@'localhost' IDENTIFIED BY 'MonetizePass2024#'; FLUSH PRIVILEGES;"
echo "Password set: MonetizePass2024#"

echo ""
echo "=== Step 2: Grant privileges on monetizearticle DB ==="
mysql -u root -e "GRANT ALL PRIVILEGES ON monetizearticle.* TO 'monetize_user'@'localhost'; FLUSH PRIVILEGES;"
echo "Privileges granted!"

echo ""
echo "=== Step 3: Test connection ==="
mysql -u monetize_user -p'MonetizePass2024#' -e "SHOW DATABASES;" 2>&1

echo ""
echo "=== Step 4: Create env.php ==="
cat > /var/www/monetizearticle/private/env.php << 'ENVEOF'
<?php

return array (
  'APP_INSTALLED' => 1,
  'APP_NAME' => 'MonetizeArticle',
  'APP_ENV' => 'production',
  'APP_KEY' => 'ZzTdxME6P4lLAaGc8IdvUOoSkLYdVZk0',
  'APP_SECRET_KEY' => '32a529ccae6a333f0af3eb99e9cfbf6030425b0c',
  'APP_DEBUG' => false,
  'APP_URL' => 'https://monetizearticle.com',
  'APP_LOCALE' => 'en',
  'APP_FALLBACK_LOCALE' => 'en',
  'APP_FAKER_LOCALE' => 'en_US',
  'APP_MAINTENANCE_DRIVER' => 'file',
  'BCRYPT_ROUNDS' => 12,
  'LOG_CHANNEL' => 'stack',
  'LOG_STACK' => 'daily',
  'LOG_DEPRECATIONS_CHANNEL' => NULL,
  'LOG_LEVEL' => 'error',
  'DB_CONNECTION' => 'mysql',
  'DB_HOST' => 'localhost',
  'DB_PORT' => '3306',
  'DB_DATABASE' => 'monetizearticle',
  'DB_USERNAME' => 'monetize_user',
  'DB_PASSWORD' => 'MonetizePass2024#',
  'SESSION_DRIVER' => 'file',
  'SESSION_LIFETIME' => 120,
  'SESSION_ENCRYPT' => false,
  'SESSION_PATH' => '/',
  'SESSION_DOMAIN' => NULL,
  'BROADCAST_CONNECTION' => 'log',
  'FILESYSTEM_DISK' => 'local',
  'QUEUE_CONNECTION' => 'sync',
  'CACHE_STORE' => 'file',
  'MEMCACHED_HOST' => '127.0.0.1',
  'REDIS_CLIENT' => 'phpredis',
  'REDIS_HOST' => '127.0.0.1',
  'REDIS_PASSWORD' => NULL,
  'REDIS_PORT' => '6379',
  'MAIL_MAILER' => 'sendmail',
  'MAIL_SCHEME' => NULL,
  'MAIL_HOST' => NULL,
  'MAIL_PORT' => NULL,
  'MAIL_USERNAME' => NULL,
  'MAIL_PASSWORD' => NULL,
  'MAIL_FROM_ADDRESS' => 'hello@monetizearticle.com',
  'MAIL_FROM_NAME' => 'MonetizeArticle',
  'AWS_ACCESS_KEY_ID' => '',
  'AWS_SECRET_ACCESS_KEY' => '',
  'AWS_DEFAULT_REGION' => 'us-east-1',
  'AWS_BUCKET' => '',
  'AWS_USE_PATH_STYLE_ENDPOINT' => false,
  'VITE_APP_NAME' => '',
  'DB_ENGINE' => 'InnoDB',
  'DB_CHARSET' => 'utf8mb4',
  'DB_COLLATION' => 'utf8mb4_unicode_520_ci',
  'FACEBOOK_CLIENT_ID' => NULL,
  'FACEBOOK_CLIENT_SECRET' => NULL,
  'TWITTER_CLIENT_ID' => NULL,
  'TWITTER_CLIENT_SECRET' => NULL,
  'GOOGLE_CLIENT_ID' => NULL,
  'GOOGLE_CLIENT_SECRET' => NULL,
  'MAIL_ENCRYPTION' => NULL,
);
ENVEOF

chown www-data:www-data /var/www/monetizearticle/private/env.php
chmod 644 /var/www/monetizearticle/private/env.php
echo "env.php created!"

echo ""
echo "=== Step 5: Set storage permissions ==="
chown -R www-data:www-data /var/www/monetizearticle/private/storage
chmod -R 775 /var/www/monetizearticle/private/storage
echo "Storage permissions set!"

echo ""
echo "=== Step 6: Run DB migrations ==="
cd /var/www/monetizearticle/private
php8.3 artisan migrate --force 2>&1
