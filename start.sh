#!/bin/bash

echo "========================================"
echo "Campus Management System - Quick Start"
echo "========================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP is not installed"
    echo "Please install PHP and try again"
    exit 1
fi

echo "PHP detected successfully!"
echo ""

# Display instructions
echo "========================================"
echo "SETUP INSTRUCTIONS:"
echo "========================================"
echo "1. Make sure MySQL is running"
echo "2. Import database.sql into MySQL:"
echo "   mysql -u root -p < database.sql"
echo ""
echo "3. Update config.php with your database credentials"
echo ""
echo "4. Press Enter to start the server..."
echo "========================================"
read

echo ""
echo "Starting PHP development server..."
echo "Server will be available at: http://localhost:8000"
echo ""
echo "Default login credentials:"
echo "- Admin: umar.farooq@gmail.com / umarfarooq"
echo "- Faculty: faculty@campus.edu / password"
echo "- Student: student@campus.edu / password"
echo "- Club Admin: club@campus.edu / password"
echo ""
echo "Press Ctrl+C to stop the server"
echo "========================================"
echo ""

php -S localhost:8000
