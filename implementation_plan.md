# 🐳 Docker Deployment Plan — Sterling Dashboard (Ubuntu VPS)

## প্রজেক্ট বিশ্লেষণ

আপনার প্রজেক্ট বিশ্লেষণ করে যা পেলাম:

| Component | Technology | Notes |
|---|---|---|
| **Backend** | Django 5.2 + DRF | Gunicorn দিয়ে serve হবে |
| **Scheduler** | APScheduler (BackgroundScheduler) | Django AppConfig এর `ready()` তে start হয় |
| **Scrapers** | Playwright (Chromium) | Headless browser — সবচেয়ে critical dependency |
| **Database** | MySQL (mysqlclient) | VPS-এ MySQL container বা external MySQL হবে |
| **Frontend** | React (Vite) + Nginx | Static build, Nginx দিয়ে serve |
| **Static Files** | WhiteNoise | Django থেকেই serve হয় |

---

## Architecture Overview

```
Internet (Port 80/443)
        │
        ▼
  ┌─────────────┐
  │  Nginx (VPS)│  ← SSL termination, reverse proxy
  │  Port 80/443│
  └──────┬──────┘
         │
    ┌────┴─────────────────────────────┐
    │                                  │
    ▼                                  ▼
┌───────────────────┐     ┌────────────────────────┐
│ frontend container│     │  backend container     │
│ nginx:alpine      │     │  Python 3.12 + Gunicorn│
│ Port 3000 (int)   │     │  Port 8000 (int)       │
│                   │     │  + APScheduler         │
│ /usr/share/nginx/ │     │  + Playwright/Chromium │
│ html/ ← dist/     │     │                        │
└───────────────────┘     └────────────┬───────────┘
                                       │
                          ┌────────────▼───────────┐
                          │   mysql container      │
                          │   MySQL 8.0            │
                          │   Port 3306 (int)      │
                          └────────────────────────┘
```

---

## তৈরি করতে হবে যেসব ফাইল

### 1. `Backend/Dockerfile`

```dockerfile
FROM python:3.12-slim

# System dependencies for mysqlclient + Playwright Chromium
RUN apt-get update && apt-get install -y \
    gcc \
    pkg-config \
    default-libmysqlclient-dev \
    # Playwright Chromium dependencies
    libnss3 libnspr4 libdbus-1-3 libatk1.0-0 libatk-bridge2.0-0 \
    libcups2 libdrm2 libxkbcommon0 libxcomposite1 libxdamage1 \
    libxfixes3 libxrandr2 libgbm1 libpango-1.0-0 libcairo2 \
    libasound2 libx11-xcb1 fonts-liberation \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt
RUN pip install gunicorn

# Install Playwright browsers
RUN playwright install chromium

COPY . .

# Collect static files
RUN python manage.py collectstatic --noinput

EXPOSE 8000

CMD ["gunicorn", "core.wsgi:application", "--bind", "0.0.0.0:8000", "--workers", "2", "--timeout", "300"]
```

> [!IMPORTANT]
> `--timeout 300` দিতে হবে কারণ Playwright scraper গুলো অনেক সময় নেয়। ডিফল্ট 30s timeout এ সমস্যা হবে।

---

### 2. `Fontend/Dockerfile`

```dockerfile
# Stage 1: Build
FROM node:20-alpine AS builder

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Serve with Nginx
FROM nginx:alpine
COPY --from=builder /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
```

---

### 3. `docker-compose.yml` (Root directory)

```yaml
version: '3.9'

services:
  db:
    image: mysql:8.0
    container_name: sterling_db
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_NAME}
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - sterling_net
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-u", "root", "-p${DB_PASSWORD}"]
      interval: 10s
      timeout: 5s
      retries: 5

  backend:
    build:
      context: ./Backend
    container_name: sterling_backend
    restart: unless-stopped
    env_file:
      - ./Backend/.env
    environment:
      DB_HOST: db          # MySQL container name
      DB_PORT: 3306
    depends_on:
      db:
        condition: service_healthy
    volumes:
      - media_files:/app/media
      - scraper_data:/app/automation/data
    networks:
      - sterling_net
    ports:
      - "8000:8000"

  frontend:
    build:
      context: ./Fontend
    container_name: sterling_frontend
    restart: unless-stopped
    networks:
      - sterling_net
    ports:
      - "3000:80"

volumes:
  mysql_data:
  media_files:
  scraper_data:

networks:
  sterling_net:
    driver: bridge
```

---

### 4. VPS এর Nginx Config (`/etc/nginx/sites-available/sterling`)

VPS-এ যে Nginx টা SSL handle করবে:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name yourdomain.com;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    # Frontend → Docker container port 3000
    location / {
        proxy_pass http://localhost:3000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # API → Backend container port 8000
    location /api/ {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 300s;   # scraper API calls এর জন্য
        proxy_connect_timeout 75s;
    }

    # Django static/admin
    location /static/ {
        proxy_pass http://localhost:8000;
    }

    location /media/ {
        proxy_pass http://localhost:8000;
    }
}
```

---

### 5. `.env` ফাইলে যা পরিবর্তন দরকার

```env
# DB_HOST পরিবর্তন করতে হবে — container name দেওয়া হবে
DB_HOST=db           # ছিল: host.docker.internal বা 127.0.0.1

# ALLOWED_HOSTS-এ domain যোগ করতে হবে
ALLOWED_HOSTS=localhost,127.0.0.1,yourdomain.com

# CORS Origins-এ actual domain
CORS_ALLOWED_ORIGINS=https://yourdomain.com

# Frontend URL
FONTEND_URL=https://yourdomain.com

# API URL (backend container এর internal address)
API_URL=http://backend:8000/api/
```

---

### 6. `requirements.txt`-এ Gunicorn যোগ করতে হবে

```
gunicorn==23.0.0
```

---

## ⚠️ গুরুত্বপূর্ণ সমস্যা ও সমাধান

> [!CAUTION]
> **Playwright + Docker** — সবচেয়ে বড় চ্যালেঞ্জ। Playwright Chromium কে `--no-sandbox` flag ছাড়া Docker container-এ চালানো যায় না।

Backend এর `base_scraper.py`-তে launch args-এ এটা নিশ্চিত করতে হবে:
```python
browser = await playwright.chromium.launch(
    headless=True,
    args=['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu']
)
```

> [!WARNING]
> **MySQL Data Persistence** — Docker volume ছাড়া container restart করলে সব data হারিয়ে যাবে। `mysql_data` volume অবশ্যই দিতে হবে।

> [!NOTE]
> **APScheduler Single Worker** — আপনার `apps.py` তে socket lock দিয়ে already এটা handle করা আছে। Gunicorn এ `--workers 2` রাখলে শুধু একটা worker scheduler চালাবে।

---

## Step-by-Step Deployment (VPS-এ)

```bash
# 1. VPS-এ Docker install
sudo apt update
sudo apt install docker.io docker-compose -y
sudo usermod -aG docker $USER

# 2. Code clone করুন
git clone <your-repo> /opt/sterling-dashboard
cd /opt/sterling-dashboard

# 3. .env ফাইল সেটআপ করুন
cp Backend/.env.example Backend/.env
nano Backend/.env   # values fill করুন

# 4. Build + Start
docker-compose up -d --build

# 5. Database migration
docker exec sterling_backend python manage.py migrate

# 6. Superuser তৈরি (প্রথমবার)
docker exec -it sterling_backend python manage.py createsuperuser

# 7. SSL (Certbot)
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com

# 8. Nginx VPS config link
sudo ln -s /etc/nginx/sites-available/sterling /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## Open Questions

> [!IMPORTANT]
> **নিচের বিষয়গুলো confirm করুন তারপর implementation শুরু করব:**
> 
> 1. **VPS-এ কি আলাদা MySQL আছে?** নাকি Docker container-এ MySQL ব্যবহার করবেন?
> 2. **VPS-এ ইতিমধ্যে Nginx installed?** এবং domain আছে কি?
> 3. **`automation/data/` বা scraper screenshot/log ফাইল কি VPS-এ persist করতে হবে?**
> 4. **আপনার existing Windows data MySQL এ — VPS-এ migrate করবেন নাকি fresh start?**
