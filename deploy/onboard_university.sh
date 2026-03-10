#!/bin/bash

# onboard_university.sh - Interactive Multi-University Onboarding
# Usage: ./onboard_university.sh

set -e

echo "--- 🏢 University Admission System Onboarding ---"

# 1. Interactive Input
read -p "University Name (e.g. Tashkent State): " UNI_NAME_RAW
read -p "Domain Name (e.g. tsu.beruniy-qabul.uz): " DOMAIN
read -p "Nginx Port (e.g. 8031): " PORT
read -p "DB External Port (e.g. 3631): " DB_PORT
read -p "Rector Name: " RECTOR

NAME_SLUG=$(echo "$UNI_NAME_RAW" | tr '[:upper:]' '[:lower:]' | tr ' ' '_')

echo "--- 🚀 Launching Deployment for ${NAME_SLUG} ---"

# 2. Run base deployment
./deploy/deploy_university.sh --name "$NAME_SLUG" --port "$PORT" --db-port "$DB_PORT"

# 3. Seed additional data (Mock)
echo "Seeding university details..."
docker-compose -p "$NAME_SLUG" exec -T php php yii branch/update 1 --name_uz="$UNI_NAME_RAW" --rector_uz="$RECTOR"

# 4. Configure SSL
echo "Configuring SSL for ${DOMAIN}..."
./deploy/setup_ssl.sh "$DOMAIN"

# 5. Summary
echo "--- ✅ Onboarding Complete! ---"
echo "University: ${UNI_NAME_RAW}"
echo "URL: https://${DOMAIN}"
echo "Admin Login: admin / {Generated in deploy step}"
echo "Telegram Bot: Please configure via /admin/settings"
echo "--- 🥂 Welcome to the platform! ---"
