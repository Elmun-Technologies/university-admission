# Deployment Guide

## Prerequisites
- Docker & Docker Compose
- 4GB RAM minimum
- 20GB Disk space

## Setup Process
1. **Clone repository**
2. **Environment Configuration**
   Copy `.env.example` to `.env` and fill:
   - `DB_PASSWORD`
   - `TELEGRAM_BOT_TOKEN`
   - `COOKIE_VALIDATION_KEY`

3. **Build & Start**
   ```bash
   docker-compose up -d --build
   ```

4. **Initialize Database**
   ```bash
   docker-compose exec php php yii migrate/up
   docker-compose exec php php yii seed/students 50
   ```

## Scaling for Peak Admissions
- Increase `php` container replicas in `docker-compose.yml`.
- Ensure `StatsController` ActionStats is running in cron to keep dashboards pre-calculated.
- Use Redis for Caching if multiple app instances are used.

## Adding a New University Branch
1. Login to Backend as SuperAdmin.
2. Go to **🏢 Branches** -> **New Branch**.
3. Create Admin user for the branch.
4. Branch isolation (`BranchActiveRecord`) will automatically handle data scoping.
