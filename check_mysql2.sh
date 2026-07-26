#!/bin/bash

echo "=== Checking monetize_user password from existing configs ==="
# Check other app configs for password clues
grep -r "monetize_user\|monetize" /var/www/ --include="*.php" --include="*.env" -l 2>/dev/null | head -5

echo ""
echo "=== Checking adlinkfly for reference ==="
cat /var/www/dragonlinkads/private/env.php 2>/dev/null | grep -E "DB_|APP_URL" | head -10

echo ""
echo "=== MySQL root auth method ==="
mysql -u root -e "SELECT user, plugin, host FROM mysql.user WHERE user IN ('root', 'monetize_user');"

echo ""
echo "=== Test monetize_user connection ==="
mysql -u monetize_user -p'monetize@pass123' -e "SHOW DATABASES;" 2>&1 | head -5

echo ""
echo "=== monetizearticle DB tables count ==="
mysql -u root -e "SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema='monetizearticle';"
