#!/bin/bash

echo "=== URL-related options ==="
mysql -u root monetizearticle -e "SELECT id, name, value FROM options WHERE name LIKE '%url%' OR name LIKE '%domain%' OR value LIKE '%localhost%';"

echo ""
echo "=== Updating site_name ==="
mysql -u root monetizearticle -e "UPDATE options SET value = 'MonetizeArticle' WHERE name = 'site_name';"

echo ""
echo "=== Updating ssl_enable to 1 ==="
mysql -u root monetizearticle -e "UPDATE options SET value = '1' WHERE name = 'ssl_enable';"

echo ""
echo "=== Replacing any localhost URLs in value column ==="
mysql -u root monetizearticle -e "UPDATE options SET value = REPLACE(value, 'http://localhost:8000', 'https://monetizearticle.com') WHERE value LIKE '%localhost%';"
mysql -u root monetizearticle -e "UPDATE options SET value = REPLACE(value, 'http://localhost', 'https://monetizearticle.com') WHERE value LIKE '%localhost%';"

echo ""
echo "=== All options after update ==="
mysql -u root monetizearticle -e "SELECT id, name, value FROM options ORDER BY id;"

echo ""
echo "=== Final: Clear all Laravel caches ==="
cd /var/www/monetizearticle/private
php8.3 artisan cache:clear
php8.3 artisan config:clear
php8.3 artisan route:clear
php8.3 artisan view:clear
echo "All caches cleared!"

echo ""
echo "=== Site HTTP test ==="
curl -s -o /dev/null -w "HTTP: %{http_code}" https://monetizearticle.com/
