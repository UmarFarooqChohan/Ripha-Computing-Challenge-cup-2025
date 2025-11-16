# Campus Management System

A simple campus management system with role-based access for Students, Faculty, Club Admins, and System Admins.

## Features

### 1. Event Management System
- **Full CRUD functionality** for event creation, editing, and deletion
- **Role-based event visibility** and participant approval
- **Event categories**: Academic, Cultural, Sports, and Technical
- **Registration management** with approval workflow
- **Attendance tracking** for registered participants
- **Certificate generation** with unique codes
- **Event feedback** with 1-5 star ratings and comments
- **Analytics** showing average ratings and participation

### 2. Campus Feed & Announcements
- **Unified feed** for posts, notices, and announcements
- **Filterable by category**: General, Academic, Cultural, Sports, Technical
- **Filter by type**: Posts, Announcements, Notices
- **Author role badges** for easy identification
- **Interaction features**: Likes, Comments, and Mentions
- **Role-based posting** (Faculty can post announcements)

### 3. Community Discussion Board
- **Discussion threads by topic**: General, Academic, Technical, Events, Other
- **Replies and nested comments**
- **Moderation by Faculty/Club Admins**
- **Thread locking** to prevent further replies
- **Thread archiving** for old discussions
- **Topic-based filtering**

### 4. User Profiles
- **Editable profiles** with academic details and contact info
- **Profile pictures** and department information
- **Activity summary** showing participation
- **Role-wise access control** for visibility

### 5. Feedback & Rating System
- **Event-based feedback** with 1-5 star ratings
- **Comment section** for detailed feedback
- **Analytics dashboard** showing average event ratings
- **Top-rated events** leaderboard
- **Feedback history** per user

### 6. Document and Resource Sharing Module
- **File uploads** for PDF, Docs, Images, Videos
- **Role-based sharing permissions**
- **Category organization**: Academic, Cultural, Sports, Technical, General
- **File type filtering**
- **Download tracking** and statistics
- **Description and metadata** for each resource

### 7. Attendance and Certificate Generator
- **Digital attendance marking** for registered participants
- **Automatic certificate generation** with unique codes
- **Certificate verification** system
- **Bulk certificate generation** for events
- **Certificate history** per user

### 8. Analytics & Reporting (Admin)
- **User statistics** by role
- **Event approval metrics**
- **Attendance rates** and trends
- **Engagement metrics** (posts, likes, comments)
- **Top-rated events** analysis
- **System-wide performance** dashboard

## Setup Instructions

### 1. Database Setup
```sql
-- Import the database schema
mysql -u root -p < database.sql
```

### 2. Configure Database Connection
Edit `config.php` and update database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'campus_management');
```

### 3. Start Server
```bash
# Using PHP built-in server
php -S localhost:8000
```

### 4. Access Application
Open browser and navigate to: `http://localhost:8000`

### Default Logins

- **Admin**: umar.farooq@gmail.com / **umarfarooq**
- **Faculty**: faculty@campus.edu / password
- **Student**: student@campus.edu / password
- **Club Admin**: club@campus.edu / password

## Project Structure
```
campus-management/
├── index.html          # Main HTML file
├── config.php          # Database configuration
├── database.sql        # Database schema
├── css/
│   └── style.css       # Styles
├── js/
│   └── app.js          # Frontend JavaScript
└── api/
    ├── auth.php                # Authentication & authorization
    ├── posts.php               # Campus feed with filtering
    ├── events.php              # Events CRUD operations
    ├── event_registration.php  # Event registration & attendance
    ├── discussions.php         # Discussion board with moderation
    ├── resources.php           # Resource sharing with permissions
    ├── feedback.php            # Event feedback & ratings
    ├── certificates.php        # Certificate generation
    └── analytics.php           # Admin analytics dashboard
```

## Technologies Used
- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Database: MySQL

## Security Notes
- Passwords are hashed using PHP's password_hash()
- Session-based authentication
- SQL injection protection with prepared statements
- Role-based access control

## Key Features Implemented

✅ Multi-role authentication (Student, Faculty, Club Admin, Admin)
✅ Event management with categories and CRUD operations
✅ Event registration with approval workflow
✅ Attendance tracking and certificate generation
✅ Campus feed with posts, announcements, and notices
✅ Category and type-based filtering
✅ Discussion board with topics and moderation
✅ Thread locking and archiving
✅ Resource sharing with file type categorization
✅ Role-based access control for resources
✅ Event feedback with star ratings
✅ Analytics dashboard for admins
✅ Download tracking for resources
✅ Badge system for roles and categories

## API Endpoints

### Authentication
- `POST /api/auth.php` - Login, Register, Logout

### Events
- `GET /api/events.php?category={category}` - List events with filtering
- `POST /api/events.php` - Create event
- `PUT /api/events.php` - Update event
- `DELETE /api/events.php?id={id}` - Delete event

### Event Registration
- `POST /api/event_registration.php` - Register for event, mark attendance, approve
- `GET /api/event_registration.php?event_id={id}` - Get registrations

### Campus Feed
- `GET /api/posts.php?type={type}&category={category}` - List posts with filters
- `POST /api/posts.php` - Create post/announcement

### Discussions
- `GET /api/discussions.php?topic={topic}` - List discussions
- `POST /api/discussions.php` - Create discussion
- `PUT /api/discussions.php` - Lock/archive discussion

### Resources
- `GET /api/resources.php?category={cat}&type={type}` - List resources
- `POST /api/resources.php` - Upload resource

### Feedback
- `GET /api/feedback.php?event_id={id}` - Get event feedback
- `POST /api/feedback.php` - Submit feedback

### Certificates
- `GET /api/certificates.php?user_id={id}` - Get user certificates
- `POST /api/certificates.php` - Generate certificate

### Analytics (Admin only)
- `GET /api/analytics.php` - Get system-wide statistics

## Future Enhancements
- Actual file upload functionality (currently placeholder)
- Real-time notifications using WebSockets
- Email notifications for events and mentions
- Advanced search functionality
- Mobile app version
- Export reports to PDF/Excel
- Calendar view for events
- User activity timeline
"# Ripha-Computing-Challenge-cup-2025" 
