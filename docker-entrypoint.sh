#!/bin/bash
set -e

# Get environment variables or use defaults
DB_HOST=${DB_HOST}
DB_USER=${DB_USER}
DB_PASS=${DB_PASS}
DB_NAME=${DB_NAME}

# Function to execute SQL files in numerical order
process_sql_migrations() {
  echo "Checking for SQL migrations..."
  
  # Wait for MySQL to be ready
  until mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} -e "SELECT 1" >/dev/null 2>&1; do
    echo "Waiting for MySQL to be ready..."
    sleep 3
  done
  
  # Create migrations tracking table if it doesn't exist
  mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} -e "
    CREATE DATABASE IF NOT EXISTS ${DB_NAME};
    USE ${DB_NAME};
    CREATE TABLE IF NOT EXISTS migrations (
      id INT AUTO_INCREMENT PRIMARY KEY,
      filename VARCHAR(255) NOT NULL,
      applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
  "
  
  # Look for numbered migration files and apply them in order
  echo "Looking for migration files in /var/www/html/op-ranking-page/admin/sql/"
  for SQL_FILE in $(find /var/www/html/op-ranking-page/admin/sql -name "[0-9][0-9][0-9]_*.sql" | sort); do
    FILENAME=$(basename "$SQL_FILE")
    
    # Check if migration has already been executed
    APPLIED=$(mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} ${DB_NAME} -se "SELECT COUNT(*) FROM migrations WHERE filename = '$FILENAME'" || echo "0")
    
    if [ "$APPLIED" = "0" ]; then
      echo "Applying migration: $FILENAME"
      
      # Apply the migration and immediately record it to prevent reapplication
      mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} ${DB_NAME} -e "
        START TRANSACTION;
        SOURCE $SQL_FILE;
        INSERT INTO migrations (filename) VALUES ('$FILENAME');
        COMMIT;
      " && echo "Migration $FILENAME applied successfully" || echo "Error applying migration $FILENAME"
    else
      echo "Migration $FILENAME already applied, skipping"
    fi
  done
  
  echo "All migrations processed"
}

# Execute initial setup if needed
setup_initial_database() {
  # Check if database exists and has tables
  echo "Checking if database needs initialization..."
  TABLES_COUNT=$(mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} -e "SELECT COUNT(TABLE_NAME) FROM information_schema.TABLES WHERE TABLE_SCHEMA = '${DB_NAME}'" | tail -n 1)
  
  if [ "$TABLES_COUNT" -eq "0" ]; then
    echo "Initializing database with op_ranking.sql"
    # Create database if it doesn't exist
    mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
    
    # Apply the initial schema
    if [ -f /var/www/html/op-ranking-page/admin/sql/op_ranking.sql ]; then
      mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} ${DB_NAME} < /var/www/html/op-ranking-page/admin/sql/op_ranking.sql
      echo "Initial database setup completed"
    else
      echo "Warning: Initial SQL file not found at /var/www/html/op-ranking-page/admin/sql/op_ranking.sql"
    fi
  else
    echo "Database already initialized"
  fi
}

# Run our database functions
echo "Starting database initialization process..."
setup_initial_database
process_sql_migrations
echo "Database initialization complete."

# Create a flag file to indicate successful initialization
touch /var/www/html/op-ranking-page/.db_initialized

# Start the main process
exec "$@"