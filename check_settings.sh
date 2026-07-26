#!/bin/bash

echo "=== Checking settings table columns ==="
mysql -u root -e "USE monetizearticle; DESCRIBE settings;"

echo ""
echo "=== Current settings (URL related) ==="
mysql -u root -e "USE monetizearticle; SELECT * FROM settings WHERE name LIKE '%url%' OR name LIKE '%domain%' OR name LIKE '%site%' LIMIT 20;"

echo ""
echo "=== All settings ==="
mysql -u root -e "USE monetizearticle; SELECT id, name, val FROM settings LIMIT 30;"
