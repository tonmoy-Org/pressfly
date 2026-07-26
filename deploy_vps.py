import os
import subprocess
import sys

def run_cmd(cmd, shell=True, check=True):
    print(f"Running: {cmd}")
    res = subprocess.run(cmd, shell=shell, capture_output=True, text=True)
    if res.returncode != 0 and check:
        print(f"Error executing command: {cmd}")
        print(f"Stdout:\n{res.stdout}")
        print(f"Stderr:\n{res.stderr}")
        sys.exit(1)
    return res.stdout, res.stderr

print("=== Starting VPS Deployment Setup ===")

# 1. Directory and Git Repo setup on VPS
print("\n--- Setting up Directory and Git ---")
run_cmd("mkdir -p /var/www/monetizearticle")
run_cmd("cd /var/www/monetizearticle && git init")
run_cmd("cd /var/www/monetizearticle && git config receive.denyCurrentBranch updateInstead")
run_cmd("chown -R www-data:www-data /var/www/monetizearticle")

# 2. Database Setup: Create Database and Set root password to empty
print("\n--- Setting up Database ---")
run_cmd("mysql -u root -e 'DROP DATABASE IF EXISTS monetizearticle; CREATE DATABASE monetizearticle;'")

# Set root password to empty using standard MariaDB syntax
# Let's try ALTER USER ... IDENTIFIED BY '';
# If it fails, try ALTER USER ... IDENTIFIED VIA mysql_native_password USING '';
# If it fails, try SET PASSWORD FOR ...
alter_sqls = [
    "ALTER USER 'root'@'localhost' IDENTIFIED BY '';",
    "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING '';",
    "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('');",
    "FLUSH PRIVILEGES;"
]

for sql in alter_sqls:
    print(f"Trying SQL: {sql}")
    stdout, stderr = run_cmd(f"mysql -u root -e \"{sql}\"", check=False)
    if stderr:
        print(f"Notice/Error: {stderr.strip()}")
    else:
        print("Success")

# Test if root connection without password over 127.0.0.1 works now
stdout, stderr = run_cmd("mysql -h 127.0.0.1 -u root -e 'SELECT 1;'", check=False)
if "Access denied" in stderr or "ERROR" in stderr:
    print(f"Warning: Root TCP passwordless connection test failed: {stderr}")
else:
    print("MySQL Root passwordless TCP connection is verified!")

# 3. Nginx Config Setup
print("\n--- Setting up Nginx Config ---")
nginx_conf = """server {
    listen 80;
    server_name monetizearticle.com www.monetizearticle.com;

    root /var/www/monetizearticle;
    index index.php index.html index.htm;

    charset utf-8;

    location ^~ /.well-known/acme-challenge/ {
        default_type text/plain;
        root /var/www/monetizearticle;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    error_log /var/log/nginx/monetizearticle_error.log;
    access_log /var/log/nginx/monetizearticle_access.log;
}
"""

with open('/etc/nginx/sites-enabled/monetizearticle.com', 'w') as f:
    f.write(nginx_conf)

# Reload Nginx
run_cmd("nginx -t")
run_cmd("systemctl reload nginx")
print("Nginx config successfully created and reloaded.")

print("\n=== VPS Init Configuration Complete ===")
print("Please run 'git push vps master --force' from your local machine to upload the code.")
