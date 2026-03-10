#!/bin/bash

# setup_ssl.sh - Configures SSL for a domain using Certbot
# Usage: ./setup_ssl.sh example.uz

set -e

DOMAIN=$1

if [[ -z "$DOMAIN" ]]; then
    echo "Usage: ./setup_ssl.sh domain.uz"
    exit 1
fi

echo "--- 🔐 Setting up SSL for ${DOMAIN} ---"

# 1. Validate Nginx config
echo "Testing Nginx configuration..."
nginx -t

# 2. Use certbot with nginx plugin
echo "Requesting SSL certificate..."
certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos --email admin@university-admission.uz

# 3. Set up auto-renewal cron if not exists
if ! crontab -l | grep -q "certbot renew"; then
    echo "Adding auto-renewal to crontab..."
    (crontab -l 2>/dev/null; echo "0 0,12 * * * perl -e 'sleep int(rand(43200))'; certbot renew -q") | crontab -
fi

# 4. Reload Nginx
echo "Reloading Nginx..."
systemctl reload nginx

echo "--- ✅ SSL Setup Complete for ${DOMAIN} ---"
