#!/bin/bash
set -e

echo "=== Step 1: Remove Nginx config ==="
rm -f /etc/nginx/sites-enabled/monetizearticle.com
rm -f /etc/nginx/sites-available/monetizearticle.com
echo "Nginx config removed!"

echo ""
echo "=== Step 2: Remove SSL Certificate ==="
certbot delete --cert-name monetizearticle.com --non-interactive 2>&1 || echo "SSL cert removed or not found"

echo ""
echo "=== Step 3: Remove project files ==="
rm -rf /var/www/monetizearticle
echo "Project files removed!"

echo ""
echo "=== Step 4: Drop MySQL database ==="
mysql -u root -e "DROP DATABASE IF EXISTS monetizearticle;"
echo "Database dropped!"

echo ""
echo "=== Step 5: Remove MySQL user ==="
mysql -u root -e "DROP USER IF EXISTS 'monetize_user'@'localhost'; FLUSH PRIVILEGES;"
echo "MySQL user removed!"

echo ""
echo "=== Step 6: Reload Nginx ==="
nginx -t && systemctl reload nginx
echo "Nginx reloaded!"

echo ""
echo "=== Step 7: Remove log files ==="
rm -f /var/log/nginx/monetizearticle_error.log
rm -f /var/log/nginx/monetizearticle_access.log
echo "Log files removed!"

echo ""
echo "=== Verification ==="
echo "Remaining /var/www dirs:"
ls /var/www/
echo ""
echo "Remaining nginx sites:"
ls /etc/nginx/sites-enabled/
echo ""
echo "Remaining databases:"
mysql -u root -e "SHOW DATABASES;"

echo ""
echo "✅ monetizearticle.com completely removed from VPS!"
