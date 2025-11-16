@echo off
echo ========================================
echo Campus Management System - Quick Start
echo ========================================
echo.

REM Check if PHP is installed
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP and try again
    pause
    exit /b 1
)

echo PHP detected successfully!
echo.

REM Check if MySQL is running
echo Checking MySQL connection...
echo.

REM Display instructions
echo ========================================
echo SETUP INSTRUCTIONS:
echo ========================================
echo 1. Make sure MySQL is running
echo 2. Import database.sql into MySQL:
echo    mysql -u root -p ^< database.sql
echo.
echo 3. Update config.php with your database credentials
echo.
echo 4. Press any key to start the server...
echo ========================================
pause

echo.
echo Starting PHP development server...
echo Server will be available at: http://localhost:8000
echo.
echo Default login credentials:
echo - Admin: umar.farooq@gmail.com / umarfarooq
echo - Faculty: faculty@campus.edu / password
echo - Student: student@campus.edu / password
echo - Club Admin: club@campus.edu / password
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php -S localhost:8000

pause
