import subprocess

# Read current nginx config
with open('/etc/nginx/sites-enabled/monetizearticle.com', 'r') as f:
    content = f.read()

# Replace PHP 8.1 FPM socket with PHP 8.3
content = content.replace(
    'fastcgi_pass unix:/run/php/php8.1-fpm.sock;',
    'fastcgi_pass unix:/run/php/php8.3-fpm.sock;'
)

# Write updated config
with open('/etc/nginx/sites-enabled/monetizearticle.com', 'w') as f:
    f.write(content)

print('Nginx config updated to PHP 8.3!')
print('New socket: php8.3-fpm.sock')

# Test nginx config
result = subprocess.run(['nginx', '-t'], capture_output=True, text=True)
print(result.stdout)
print(result.stderr)
