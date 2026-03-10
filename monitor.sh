#!/bin/bash
# beruniy-qabul monitoring script
# Checks the health endpoint and alerts via Telegram if system is down.

HEALTH_URL="https://beruniy-qabul.uz/health" # Replace with actual production URL
LOG_FILE="/tmp/monitor.log"
BOT_TOKEN="YOUR_BOT_TOKEN"
CHAT_ID="YOUR_CHAT_ID"

RESPONSE=$(curl -s -w "%{http_code}" "$HEALTH_URL")
HTTP_CODE="${RESPONSE: -3}"
BODY="${RESPONSE::-3}"

if [ "$HTTP_CODE" -ne 200 ]; then
    MSG="🚨 <b>SYSTEM ALERT!</b>\n\nHealth Check Failed: <b>$HTTP_CODE</b>\nURL: $HEALTH_URL\n\nPayload: <code>$BODY</code>"
    
    curl -s -X POST "https://api.telegram.org/bot$BOT_TOKEN/sendMessage" \
        -d "chat_id=$CHAT_ID" \
        -d "text=$MSG" \
        -d "parse_mode=HTML"
        
    echo "$(date) - ALERT SENT - Code: $HTTP_CODE" >> "$LOG_FILE"
else
    echo "$(date) - SYSTEM OK" >> "$LOG_FILE"
fi
