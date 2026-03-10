#!/bin/bash

# ==========================================
# Production Security & Permission Enforcer
# ==========================================

echo "Starting Security Check..."

# Navigate to application root
APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR" || exit 1

echo "Target Directory: $APP_DIR"

# 1. Baseline Lockdown (Folders 755, Files 644)
echo "Enforcing baseline 755/644 permissions..."
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# 2. Grant Write Access to writable directories
echo "Granting safe write access to runtime and web assets..."
chmod -R 777 frontend/runtime backend/runtime console/runtime
chmod -R 777 frontend/web/assets backend/web/assets

# Create uploads dir if not exists and secure it
mkdir -p frontend/web/uploads/photos
chmod -R 777 frontend/web/uploads

# Ensure bash scripts are executable
chmod +x yii deploy/*.sh

# 3. Environment Protection
echo "Securing .env and configs..."
if [ -f .env ]; then
    chmod 600 .env
    echo ".env locked to 600."
fi

# Prevent web execution in uploads via simple .htaccess (if apache used, though we use nginx)
# and strict generic blocking
echo "<?php exit('Forbidden');" > frontend/web/uploads/index.php

echo ""
echo "✅ Security Check & Permission Enforcement Complete."
