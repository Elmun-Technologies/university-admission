#!/bin/bash

# setup_server.sh - Initial Ubuntu Server Setup for University Admission System
# Run as root: sudo ./setup_server.sh

set -e

echo "--- 🛠 Starting Server Setup ---"

# 1. Update system packages
echo "Updating system packages..."
apt-get update && apt-get upgrade -y

# 2. Install useful tools
echo "Installing base tools (htop, curl, git, ufw, certbot)..."
apt-get install -y htop curl git ufw software-properties-common apt-transport-https ca-certificates gnupg lsb-release

# 3. Install Docker
echo "Installing Docker..."
if ! [ -x "$(command -v docker)" ]; then
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    rm get-docker.sh
fi

# 4. Install Docker Compose
echo "Installing Docker Compose..."
if ! [ -x "$(command -v docker-compose)" ]; then
    LATEST_VERSION=$(curl -s https://api.github.com/repos/docker/compose/releases/latest | grep 'tag_name' | cut -d\" -f4)
    curl -L "https://github.com/docker/compose/releases/download/${LATEST_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

# 5. Configure UFW firewall
echo "Configuring UFW (Allowing SSH, HTTP, HTTPS)..."
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
echo "y" | ufw enable

# 6. Set up SSH key authentication ONLY (Hardening)
echo "Hardening SSH (Disabling password authentication)..."
# WARNING: Ensure you have your SSH keys added before running this!
sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
systemctl restart ssh

# 7. Create directory structure
echo "Creating /opt/university-admission structure..."
mkdir -p /opt/university-admission
chmod 755 /opt/university-admission

# 8. Install Certbot for SSL
echo "Installing Certbot..."
apt-get install -y certbot python3-certbot-nginx

echo "--- ✅ Server Setup Complete! ---"
echo "Next step: Deploy your first university with ./deploy_university.sh"
