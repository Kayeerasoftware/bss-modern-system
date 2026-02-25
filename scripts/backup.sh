#!/bin/bash

BACKUP_DIR="storage/app/backups"
DATE=$(date +%Y-%m-%d_%H%M%S)
DB_NAME="bss_system"

echo "Creating backup..."

mkdir -p $BACKUP_DIR

mysqldump -u root -p $DB_NAME > "$BACKUP_DIR/backup_$DATE.sql"

tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" storage/app/public

echo "Backup completed: backup_$DATE"
