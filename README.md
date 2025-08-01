# PHP CRUD Admin Dashboard

A complete PHP CRUD application with MySQL database integration and Bootstrap UI for managing users with advanced categorization and reporting features.

## Features

### User Management

- ✅ **Complete CRUD Operations** (Create, Read, Update, Delete)
- ✅ **Advanced User Filtering** by status, category, and search
- ✅ **User Status Management** (Active/Inactive toggle)
- ✅ **Real-time User Categorization**:
  - **New People**: Users registered in the last 7 days
  - **Regular People**: Users registered 7-30 days ago
  - **Old People**: Users registered 30+ days ago
  - **Active People**: Users with active status
  - **Inactive People**: Users with inactive status

### Admin Dashboard

- 📊 **Real-time Statistics** and metrics
- 📈 **Interactive Charts** (User status, age groups, monthly trends)
- 📋 **Comprehensive Reports** with visual analytics
- 🔍 **Advanced Search** and filtering capabilities
- 📱 **Responsive Design** with Bootstrap 5

### Database Structure

- 👥 **Users Table**: Complete user information with timestamps
- 🔐 **Admins Table**: Secure admin authentication
- 📅 **Automatic Timestamps**: Registration date and last login tracking
- 🏷️ **Status Management**: Active/Inactive user states

## Installation Instructions

### Prerequisites

- XAMPP (or any PHP server with MySQL)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Setup Steps

1. **Extract Files**

   ```
   Copy all files to: C:\xampp\htdocs\admin_crud\
   ```

2. **Start XAMPP Services**

   - Start Apache
   - Start MySQL

3. **Create Database**

   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Import the `database.sql` file to create the database and tables
   - Or manually run the SQL commands from `database.sql`

4. **Configuration**

   - Database settings are in `config.php`
   - Default settings work with standard XAMPP installation
   - Modify if using different database credentials

5. **Access Application**
   - Open: http://localhost/admin_crud/
   - Login with default credentials:
     - **Username**: `admin`
     - **Password**: `admin123`

## Database Structure

### Users Table

```sql
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    age INT(3),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Admins Table

```sql
CREATE TABLE admins (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## File Structure

```
admin_crud/
├── config.php              # Database configuration and helper functions
├── login.php               # Admin login page
├── dashboard.php           # Main dashboard with statistics
├── users.php               # User management (list, filter, delete)
├── add_user.php            # Add new user form
├── edit_user.php           # Edit existing user form
├── reports.php             # Advanced reports and analytics
├── logout.php              # Logout functionality
├── database.sql            # Database structure and sample data
├── index.html              # Welcome page
└── README.md               # This file
```

## User Categories

The system automatically categorizes users based on their registration date:

1. **New People** (Badge: Warning/Yellow)

   - Users registered in the last 7 days
   - Helps identify recent sign-ups

2. **Regular People** (Badge: Primary/Blue)

   - Users registered between 7-30 days ago
   - Regular user base

3. **Old People** (Badge: Info/Light Blue)

   - Users registered more than 30 days ago
   - Long-term users

4. **Active People** (Badge: Success/Green)

   - Users with active status
   - Can access the system

5. **Inactive People** (Badge: Secondary/Gray)
   - Users with inactive status
   - Restricted access

## Features Overview

### Dashboard

- User count statistics
- Quick overview of recent users
- Category-based user distribution
- Real-time metrics

### User Management

- Complete user listing with pagination
- Advanced filtering by category and status
- Search functionality across name, email, phone
- Bulk operations support
- Individual user actions (edit, delete, toggle status)

### Reports & Analytics

- Interactive charts using Chart.js
- User distribution by status and age groups
- Monthly registration trends
- Most active users list
- Printable reports

### Security Features

- Secure admin authentication
- Password hashing with PHP password_hash()
- SQL injection prevention with PDO prepared statements
- XSS protection with data sanitization
- Session management

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5.3.0
- **Icons**: Font Awesome 6.0.0
- **Charts**: Chart.js
- **Architecture**: MVC-like structure with separate concerns

## Sample Data

The system comes with sample users representing different categories:

- John Doe (New user, Active)
- Jane Smith (New user, Active)
- Bob Johnson (Old user, Inactive)
- Alice Brown (Regular user, Active)
- Charlie Wilson (Old user, Active)

## Support

For issues or questions:

1. Check the database connection in `config.php`
2. Ensure XAMPP services are running
3. Verify database import was successful
4. Check PHP error logs for debugging

## License

This project is open source and available under the MIT License.
