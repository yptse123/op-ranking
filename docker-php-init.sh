#!/bin/bash
set -e

# Update PHP configuration for session management and timezone
echo "Configuring PHP for proper session management and timezone..."
echo "session.auto_start = 0" > /usr/local/etc/php/conf.d/session.ini
echo "output_buffering = 4096" >> /usr/local/etc/php/conf.d/session.ini
echo "date.timezone = Asia/Hong_Kong" >> /usr/local/etc/php/conf.d/session.ini

# Update PHP configuration for error handling
echo "display_errors = On" > /usr/local/etc/php/conf.d/error.ini
echo "log_errors = On" >> /usr/local/etc/php/conf.d/error.ini
echo "error_log = /var/log/php_errors.log" >> /usr/local/etc/php/conf.d/error.ini

# Make session directory writable
mkdir -p /var/lib/php/sessions
chmod 1733 /var/lib/php/sessions

# Execute the main entrypoint script
exec /usr/local/bin/docker-entrypoint.sh "$@"