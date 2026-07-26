#!/bin/bash

echo "=== Options table - all rows ==="
mysql -u root -e "USE monetizearticle; SELECT option_name, option_value FROM options LIMIT 50;"

echo ""
echo "=== Updating domain/URL in options table ==="
# Update any localhost references to monetizearticle.com
mysql -u root -e "USE monetizearticle; UPDATE options SET option_value = REPLACE(option_value, 'http://localhost:8000', 'https://monetizearticle.com') WHERE option_value LIKE '%localhost%';"
mysql -u root -e "USE monetizearticle; UPDATE options SET option_value = REPLACE(option_value, 'http://localhost', 'https://monetizearticle.com') WHERE option_value LIKE '%localhost%';"

echo "URL updated in options table!"

echo ""
echo "=== After update ==="
mysql -u root -e "USE monetizearticle; SELECT option_name, option_value FROM options WHERE option_value LIKE '%monetizearticle%' OR option_name LIKE '%url%' OR option_name LIKE '%site%' LIMIT 20;"

echo ""
echo "=== Clear Laravel cache ==="
cd /var/www/monetizearticle/private
php8.3 artisan cache:clear
php8.3 artisan config:clear
php8.3 artisan view:clear
echo "Cache cleared!"
