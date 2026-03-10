# University Admission System

A high-performance, multi-tenant University Admission System built with Yii2 Advanced Framework and Docker. It is designed to handle large-scale student applications, automated exam processing, and multi-university management.

## 🚀 Key Features

- **Multi-University Tenancy:** Deploy separate instances for each university with individual databases and isolated environments.
- **Automated Workflows:** Student registration, document upload (oferta), and automated admission processing.
- **Zero-Downtime Deployment:** Blue-Green deployment strategy using Docker and Nginx.
- **Platform Management:** Centralized dashboard for platform operators to monitor all deployments.
- **Security:** Hardened Nginx configurations, Rate Limiting, HSTS, and backward-compatible database migrations.
- **Disaster Recovery:** Automated backup restoration and data export tools.

## 🛠 Tech Stack

- **Framework:** Yii2 Advanced (PHP 8.x)
- **Database:** MariaDB / MySQL
- **Environment:** Docker & Docker Compose
- **Server:** Nginx (Reverse Proxy with SSL)
- **Dependencies:** mPDF (PDF), PHPSpreadsheet (Excel), JWT (API Auth), Yii2 Queue.

## 📦 Installation & Setup

### 1. Prerequisites
- Docker & Docker Compose
- Git
- Access to an Ubuntu server (for production)

### 2. Local Development
```bash
git clone https://github.com/Elmun-Technologies/beruniy-qabul.git
cd beruniy-qabul
docker-compose up -d
docker-compose exec php composer update
docker-compose exec php php yii migrate --interactive=0
```
Access:
- Frontend: `http://localhost/`
- Backend: `http://localhost/admin/` (admin / admin123)

### 3. Production Deployment
Use the built-in deployment suite:
```bash
# Initial server hardening
sudo ./deploy/setup_server.sh

# Onboard a new university instance (Interactive)
./deploy/onboard_university.sh
```

## 📖 Documentation

- [Migration Standards](docs/MIGRATIONS.md)
- [Disaster Recovery Plan](docs/DISASTER_RECOVERY.md)
- [Nginx Configuration](nginx/nginx_production.conf)

## 🤝 Contributing

Every migration must follow the [Naming Convention](migrations/NAMING_CONVENTION.md). Ensure all migrations are backward-compatible to support zero-downtime updates.

---
© 2026 Elmun Technologies. All rights reserved.
