#!/bin/bash
cd /var/www/monetizearticle/private

echo "=== PHP Version ==="
php -v | head -1

echo ""
echo "=== Checking composer.lock for PHP requirement ==="
if [ -f composer.lock ]; then
    grep -A2 '"platform"' composer.lock | head -20
    echo "---"
    grep '"php"' composer.lock | head -10
else
    echo "No composer.lock found"
fi

echo ""
echo "=== Checking vendor/composer/platform_check.php ==="
if [ -f vendor/composer/platform_check.php ]; then
    grep -i "php" vendor/composer/platform_check.php | head -20
else
    echo "No platform_check.php found"
fi

echo ""
echo "=== Packages requiring PHP >= 8.3 ==="
grep -r '"php".*8\.3' vendor/*/composer.json 2>/dev/null | head -20
