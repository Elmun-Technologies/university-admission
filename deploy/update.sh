#!/bin/bash

# update.sh - Updates an existing deployment
# Usage: ./update.sh --name tashkent_univer

set -e

NAME=""
while [[ "$#" -gt 0 ]]; do
    case $1 in
        --name) NAME="$2"; shift ;;
        *) echo "Unknown parameter: $1"; exit 1 ;;
    esac
    shift
done

if [[ -z "$NAME" ]]; then
    echo "Usage: ./update.sh --name {name}"
    exit 1
fi

DEPLOY_DIR="/opt/university-admission/${NAME}"
cd "${DEPLOY_DIR}"

echo "--- 🔄 Updating Instance: ${NAME} ---"

# 1. Pull latest code
echo "Pulling latest code..."
git pull origin main

# 2. Rebuild PHP container if needed
echo "Rebuilding containers (checking for changes)..."
docker-compose up -d --build php

# 3. Run new migrations
echo "Running pending migrations..."
docker-compose exec -T php php yii migrate --interactive=0

# 4. Reload nginx (The one inside docker, or the host one?)
# Host nginx reload is usually handled by the zero-downtime script or manual step.
# Here we ensure docker internal services are up.

# 5. Send update notification (Mock)
echo "Notification: Instance ${NAME} updated successfully at $(date)"
# Example: curl -X POST -H 'Content-Type: application/json' -d '{"text":"Update successful"}' $TELEGRAM_WEBHOOK

echo "--- ✅ Update Complete ---"
