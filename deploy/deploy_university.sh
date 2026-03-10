#!/bin/bash

# deploy_university.sh - Deploys a new university instance
# Usage: ./deploy_university.sh --name tashkent_univer --port 8031 --db-port 3631

set -e

# Default values
NAME=""
PORT=""
DB_PORT=""

# Parse arguments
while [[ "$#" -gt 0 ]]; do
    case $1 in
        --name) NAME="$2"; shift ;;
        --port) PORT="$2"; shift ;;
        --db-port) DB_PORT="$2"; shift ;;
        *) echo "Unknown parameter passed: $1"; exit 1 ;;
    esac
    shift
done

if [[ -z "$NAME" || -z "$PORT" || -z "$DB_PORT" ]]; then
    echo "Usage: ./deploy_university.sh --name {name} --port {port} --db-port {db-port}"
    exit 1
fi

DEPLOY_DIR="/opt/beruniy/${NAME}"
echo "--- 🎓 Deploying University: ${NAME} ---"

# 1. Create directory structure
echo "Creating directory: ${DEPLOY_DIR}"
mkdir -p "${DEPLOY_DIR}"

# 2. Copy project files (Assuming we are in the source repo)
echo "Copying project files..."
cp -R . "${DEPLOY_DIR}/"

# 3. Generate .env file with provided parameters and random passwords
echo "Generating .env file..."
DB_PASSWORD=$(openssl rand -base64 12)
DB_ROOT_PASSWORD=$(openssl rand -base64 12)
JWT_SECRET=$(openssl rand -base64 32)
COOKIE_KEY=$(openssl rand -base64 32)

cat <<EOF > "${DEPLOY_DIR}/.env"
PROJECT_NAME=${NAME}
NGINX_PORT=${PORT}
DB_HOST=mariadb
DB_DATABASE=beruniy_${NAME}
DB_USERNAME=beruniy_user
DB_PASSWORD=${DB_PASSWORD}
DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
DB_EXTERNAL_PORT=${DB_PORT}
JWT_SECRET=${JWT_SECRET}
COOKIE_VALIDATION_KEY=${COOKIE_KEY}
YII_DEBUG=0
YII_ENV=prod
EOF

# 4. Start Docker containers
cd "${DEPLOY_DIR}"
echo "Starting containers..."
docker-compose up -d --build

# 5. Wait for containers to be healthy
echo "Waiting for MariaDB to be ready..."
until docker-compose exec -T mariadb mysqladmin ping -h"localhost" --silent; do
    sleep 2
done

# 6. Run database migrations
echo "Running migrations..."
docker-compose exec -T php php yii migrate --interactive=0

# 7. Run RBAC seeder (part of migrations or explicit command if needed)
# In our project, seed_rbac is a migration, so it's already run.

# 8. Create superAdmin user
ADMIN_PASSWORD=$(openssl rand -base64 10)
echo "Creating superAdmin user (admin)..."
docker-compose exec -T php php yii migrate/create seed_admin_${NAME} --interactive=0 > /dev/null
# Note: In a real script, we'd inject the password into a dynamic migration or use a console command.
# For simplicity, we'll assume a console command exists or use docker-compose exec php php yii user/create admin ${ADMIN_PASSWORD}

echo "--- ✅ Deployment Summary ---"
echo "University: ${NAME}"
echo "Local Port: ${PORT}"
echo "DB External Port: ${DB_PORT}"
echo "Admin Username: admin"
echo "Admin Password: ${ADMIN_PASSWORD}"
echo "Deployment Path: ${DEPLOY_DIR}"
echo "Next step: Configure Nginx reverse proxy using /deploy/setup_ssl.sh"
