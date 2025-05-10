#!/bin/bash
set -e

# Function to execute SQL files in numerical order
process_sql_migrations() {
  echo "Checking for SQL migrations..."
  
  # Wait for MySQL to be ready
  until mysql -h db -u root -ppassword -e "SELECT 1" >/dev/null 2>&1; do
    echo "Waiting for MySQL to be ready..."
    sleep 3
  done
  
  # Create migrations tracking table if it doesn't exist
  mysql -h db -u root -ppassword op_ranking -e "
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
    APPLIED=$(mysql -h db -u root -ppassword op_ranking -se "SELECT COUNT(*) FROM migrations WHERE filename = '$FILENAME'")
    
    if [ "$APPLIED" -eq "0" ]; then
      echo "Applying migration: $FILENAME"
      mysql -h db -u root -ppassword op_ranking < "$SQL_FILE"
      
      # Record the migration
      mysql -h db -u root -ppassword op_ranking -e "INSERT INTO migrations (filename) VALUES ('$FILENAME')"
      echo "Migration $FILENAME applied successfully"
    else
      echo "Migration $FILENAME already applied, skipping"
    fi
  done
  
  echo "All migrations processed"
}

# Execute initial setup if needed
setup_initial_database() {
  # Check if op_ranking database exists and has tables
  echo "Checking if database needs initialization..."
  TABLES_COUNT=$(mysql -h db -u root -ppassword -e "SELECT COUNT(TABLE_NAME) FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'op_ranking'" | tail -n 1)
  
  if [ "$TABLES_COUNT" -eq "0" ]; then
    echo "Initializing database with op_ranking.sql"
    # Apply the initial schema
    if [ -f /var/www/html/op-ranking-page/admin/sql/op_ranking.sql ]; then
      mysql -h db -u root -ppassword < /var/www/html/op-ranking-page/admin/sql/op_ranking.sql
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

# Start the main process
exec "$@"