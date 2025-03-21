#!/bin/bash

# Set working directory to where your project is located
cd /var/www/PA

# Pull the latest code from GitHub (assuming your remote is set to 'origin')
git pull origin main

# Optional: Restart your web server (if necessary)
sudo systemctl restart apache2

# Log the update to a file (optional)
echo "$(date): Code updated from GitHub" >> /var/log/git_update.log

