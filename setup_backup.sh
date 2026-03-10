#!/bin/bash
# university-admission backup and stats cron setup
# This script is intended to be run via cron to trigger Yii2 console actions.

# Get the absolute path to the project directory
PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"

# Ensure the yii script is executable
chmod +x "$PROJECT_DIR/yii"

# 1. Daily DB Backup at 02:00
# 0 2 * * * cd $PROJECT_DIR && ./yii backup/create >> common/runtime/cron.log 2>&1

# 2. Daily CRM Queue processing every 5 minutes
# */5 * * * * cd $PROJECT_DIR && ./yii crm/process >> common/runtime/cron.log 2>&1

# 3. Weekly Stats Report Every Monday at 08:00
# 0 8 * * 1 cd $PROJECT_DIR && ./yii dashboard/weekly-stats >> common/runtime/cron.log 2>&1

echo "Cron commands prepared. To install, run: crontab -e"
echo "And add the lines above (uncommented) replacing paths if necessary."
