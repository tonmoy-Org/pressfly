#!/bin/bash

echo "=== VPS env.php ==="
cat /var/www/monetizearticle/private/env.php

echo ""
echo "=== MySQL Users ==="
mysql -u root -e "SELECT user, host FROM mysql.user;"

echo ""
echo "=== monetizearticle DB Tables ==="
mysql -u root -e "USE monetizearticle; SHOW TABLES;"

echo ""
echo "=== settings table ==="
mysql -u root -e "USE monetizearticle; SELECT * FROM settings WHERE key_name IN ('app_url', 'site_url', 'app_name') LIMIT 20;" 2>/dev/null || \
mysql -u root -e "USE monetizearticle; SELECT * FROM settings LIMIT 10;" 2>/dev/null || \
echo "No settings table"
