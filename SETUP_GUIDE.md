# Campus Management System - Setup Guide

## Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

## Step-by-Step Installation

### 1. Clone/Download the Project
```bash
# Download and extract the project files to your desired directory
cd campus-management-system
```

### 2. Create Database
Open MySQL command line or phpMyAdmin and run:
```bash
mysql -u root -p
```

Then import the database schema:
```bash
mysql -u root -p < database.sql
```

Or manually:
```sql
CREATE DATABASE campus_management;
USE campus_management;
-- Then copy and paste the contents of database.sql
```

### 3. Configure Database Connection
Edit `config.php` and update these lines with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password
define('DB_NAME', 'campus_management');
```

### 4. Start the Server

#### Option A: PHP Built-in Server (Recommended for testing)
```bash
php -S localhost:8000
```

#### Option B: XAMPP/WAMP
1. Copy project folder to `htdocs` (XAMPP) or `www` (WAMP)
2. Start Apache and MySQL from control panel
3. Access via `http://localhost/campus-management-system`

#### Option C: Apache/Nginx
Configure virtual host to point to project directory

### 5. Access the Application
Open your browser and navigate to:
```
http://localhost:8000
```

### 6. Login with Default Accounts

| Role | Email | Password | Use Case |
|------|-------|----------|----------|
| Admin | umar.farooq@gmail.com | umarfarooq | Full system access, analytics, user management |
| Faculty | faculty@campus.edu | password | Create events, post announcements, moderate |
| Student | student@campus.edu | password | Register for events, participate in discussions |
| Club Admin | club@campus.edu | password | Manage club events, moderate discussions |

## Testing the Features

### As Student:
1. Login with student@campus.edu
2. View campus feed and filter by category
3. Browse events and register
4. Participate in discussions
5. Download resources
6. Submit event feedback

### As Faculty:
1. Login with faculty@campus.edu
2. Create a new event with category
3. Post an announcement
4. View event registrations
5. Mark attendance for participants
6. Upload teaching resources
7. Moderate discussions

### As Club Admin:
1. Login with club@campus.edu
2. Create club events
3. Manage event participants
4. Generate certificates for attendees
5. Upload club resources

### As Admin:
1. Login with admin@campus.edu
2. View analytics dashboard
3. Approve/reject events
4. Manage users
5. View system-wide statistics

## Common Issues & Solutions

### Issue: Database connection failed
**Solution**: Check config.php credentials and ensure MySQL is running

### Issue: Session errors
**Solution**: Ensure PHP session directory is writable
```bash
# Linux/Mac
sudo chmod 777 /var/lib/php/sessions

# Windows - sessions usually work by default
```

### Issue: API calls failing
**Solution**: Check browser console for errors. Ensure all API files are in the `api/` folder

### Issue: Blank page after login
**Solution**: Check browser console. Likely JavaScript error. Ensure js/app.js is loaded

## File Upload Configuration (Optional)

To enable actual file uploads for resources:

1. Create uploads directory:
```bash
mkdir uploads
chmod 777 uploads
```

2. Update `api/resources.php` to handle file uploads:
```php
if (isset($_FILES['file'])) {
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
}
```

3. Update PHP settings in `php.ini`:
```ini
upload_max_filesize = 50M
post_max_size = 50M
```

## Security Recommendations for Production

1. **Change default passwords** immediately
2. **Use HTTPS** for all connections
3. **Enable prepared statements** for all SQL queries
4. **Implement CSRF protection**
5. **Add rate limiting** for API endpoints
6. **Validate and sanitize** all user inputs
7. **Set proper file permissions**:
   ```bash
   chmod 644 *.php
   chmod 755 api/
   ```

## Backup Database

Regular backups are recommended:
```bash
mysqldump -u root -p campus_management > backup_$(date +%Y%m%d).sql
```

## Support

For issues or questions:
1. Check the README.md for feature documentation
2. Review database.sql for schema details
3. Check browser console for JavaScript errors
4. Review PHP error logs for backend issues

## Next Steps

After successful setup:
1. Create additional user accounts
2. Customize categories and topics
3. Add sample events and resources
4. Configure email notifications (future enhancement)
5. Customize styling in css/style.css
