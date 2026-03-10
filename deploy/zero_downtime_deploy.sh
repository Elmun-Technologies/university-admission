#!/bin/bash

# zero_downtime_deploy.sh - Blue-Green deployment for updates
# Usage: ./zero_downtime_deploy.sh --name tashkent_univer --port 8031

set -e

NAME=""
PORT=""

while [[ "$#" -gt 0 ]]; do
    case $1 in
        --name) NAME="$2"; shift ;;
        --port) PORT="$2"; shift ;;
        *) echo "Unknown parameter: $1"; exit 1 ;;
    esac
    shift
done

if [[ -z "$NAME" || -z "$PORT" ]]; then
    echo "Usage: ./zero_downtime_deploy.sh --name {name} --port {port}"
    exit 1
fi

BASE_DIR="/opt/beruniy/${NAME}"
NEW_DIR="/opt/beruniy/${NAME}_new"
TEMP_PORT=$((PORT + 1000))

echo "--- 🚀 Starting Zero-Downtime Deploy: ${NAME} ---"

# 1. Pull new code to a temporary directory
echo "Cloning new code to ${NEW_DIR}..."
rm -rf "${NEW_DIR}"
git clone . "${NEW_DIR}" # In production, this would be a git clone from origin

# 2. Prepare .env for new instance
cp "${BASE_DIR}/.env" "${NEW_DIR}/.env"
sed -i "s/NGINX_PORT=${PORT}/NGINX_PORT=${TEMP_PORT}/" "${NEW_DIR}/.env"

# 3. Build and Start new containers on temporary port
cd "${NEW_DIR}"
echo "Building and starting new containers on port ${TEMP_PORT}..."
PROJECT_NAME="${NAME}_new" docker-compose -p "${NAME}_new" up -d --build

# 4. Wait for healthy
echo "Waiting for new containers to be ready..."
sleep 10

# 5. Run migrations on new containers
echo "Running migrations (Backward Compatible)..."
PROJECT_NAME="${NAME}_new" docker-compose -p "${NAME}_new" exec -T php php yii migrate --interactive=0

# 6. Run Smoke Tests
echo "Running smoke tests against http://localhost:${TEMP_PORT}..."
if curl -s "http://localhost:${TEMP_PORT}/admin/site/login" | grep -q "Kirish"; then
    echo "✅ Smoke tests passed!"
else
    echo "❌ Smoke tests failed! Rolling back..."
    PROJECT_NAME="${NAME}_new" docker-compose -p "${NAME}_new" down
    rm -rf "${NEW_DIR}"
    exit 1
fi

# 7. Update Nginx to point to new port
echo "Swapping Nginx backend to port ${TEMP_PORT}..."
NGINX_CONF="/etc/nginx/sites-available/${NAME}.conf"
sed -i "s/proxy_pass http:\/\/localhost:${PORT};/proxy_pass http:\/\/localhost:${TEMP_PORT};/" "${NGINX_CONF}"
nginx -t && systemctl reload nginx

# 8. Success! Clean up old instance
echo "Update successful. Cleaning up old containers..."
cd "${BASE_DIR}"
docker-compose down
rm -rf "${BASE_DIR}"
mv "${NEW_DIR}" "${BASE_DIR}"

# 9. Restore original port config in .env (for future updates)
sed -i "s/NGINX_PORT=${TEMP_PORT}/NGINX_PORT=${PORT}/" "${BASE_DIR}/.env"
cd "${BASE_DIR}"
# Quick restart to original port or keep TEMP_PORT and swap back? 
# To stay simple: we'll swap the port back in Nginx and docker-compose
echo "Swapping back to permanent port ${PORT}..."
docker-compose up -d
sed -i "s/proxy_pass http:\/\/localhost:${TEMP_PORT};/proxy_pass http:\/\/localhost:${PORT};/" "${NGINX_CONF}"
nginx -t && systemctl reload nginx

echo "--- ✅ Deployment Complete! Zero downtime achieved. ---"
