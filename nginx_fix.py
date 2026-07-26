content = r"""server {
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
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
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
    f.write(content)
print('Nginx config written successfully!')
