# 🎓 Student Portal - Full Stack Application

A comprehensive student management system built with **Angular 20** (frontend) and **PHP/MySQL** (backend). Features user authentication, complaint management, and responsive design.

## 📋 Table of Contents

- [🚀 Features](#-features)
- [🛠️ Tech Stack](#️-tech-stack)
- [📁 Project Structure](#-project-structure)
- [⚡ Quick Start](#-quick-start)
- [🔧 Installation & Setup](#-installation--setup)
- [🎨 Frontend (Angular)](#-frontend-angular)
- [🔙 Backend (PHP/MySQL)](#-backend-phpmysql)
- [🧪 Testing & Troubleshooting](#-testing--troubleshooting)
- [📚 API Documentation](#-api-documentation)
- [🔒 Security Features](#-security-features)
- [🚀 Deployment](#-deployment)
- [🆘 Troubleshooting](#-troubleshooting)
- [📝 Contributing](#-contributing)
- [📄 License](#-license)

## 🚀 Features

### ✅ Core Features
- **🔐 User Authentication** - Secure login/registration system
- **📝 Complaint Management** - Submit and track student complaints
- **🎨 Responsive Design** - Works on desktop, tablet, and mobile
- **⚡ Real-time Updates** - Live complaint status tracking
- **🛡️ Route Protection** - Auth guards for secure navigation
- **⏰ Session Management** - Automatic logout after 30 minutes
- **📱 Modern UI** - Bootstrap 5 with custom styling

### ✅ Technical Features
- **🏗️ Angular 20** - Latest Angular with standalone components
- **🔄 Angular Universal** - Server-side rendering support
- **🗄️ MySQL Database** - Robust data storage
- **🔒 JWT-like Sessions** - Secure authentication
- **📡 RESTful APIs** - Clean API design
- **🧪 Comprehensive Testing** - Unit tests included
- **📦 Modular Architecture** - Scalable code structure

## 🛠️ Tech Stack

### Frontend
- **Framework**: Angular 20
- **Language**: TypeScript
- **Styling**: SCSS + Bootstrap 5
- **State Management**: RxJS BehaviorSubject
- **Build Tool**: Angular CLI
- **Server-Side Rendering**: Angular Universal

### Backend
- **Language**: PHP 8+
- **Database**: MySQL 8+
- **Server**: Apache (XAMPP)
- **Security**: bcrypt password hashing
- **API**: RESTful JSON endpoints

### Development Tools
- **IDE**: Visual Studio Code
- **Version Control**: Git
- **Package Manager**: npm
- **Database Tool**: phpMyAdmin
- **Testing**: Angular TestBed + PHP unit tests

## 📁 Project Structure

```
Student Portal/
├── 📁 AngularProject/
│   ├── 📁 student-portal/          # Angular Frontend
│   │   ├── 📁 src/
│   │   │   ├── 📁 app/
│   │   │   │   ├── 📁 components/
│   │   │   │   │   ├── 📁 home/
│   │   │   │   │   ├── 📁 login/
│   │   │   │   │   ├── 📁 register/
│   │   │   │   │   └── 📁 complaint/
│   │   │   │   ├── 📁 guards/
│   │   │   │   ├── 📁 services/
│   │   │   │   ├── 📄 app.config.ts
│   │   │   │   ├── 📄 app.routes.ts
│   │   │   │   └── 📄 app.ts
│   │   │   ├── 📁 environments/
│   │   │   └── 📄 index.html
│   │   ├── 📄 angular.json
│   │   ├── 📄 package.json
│   │   └── 📄 server.ts
│   └──
└── 📁 student-portal-backend/     # PHP Backend
    ├── 📄 config.php
    ├── 📄 login.php
    ├── 📄 register.php
    ├── 📄 submit_complaint.php
    ├── 📄 get_complaints.php
    ├── 📄 database_setup.sql
    └── 📁 test_*.php (14 test files)
```

## ⚡ Quick Start

### Prerequisites
- **Node.js** 18+ and **npm**
- **XAMPP** (Apache + MySQL + PHP)
- **Git** for version control
- **Visual Studio Code** (recommended)

### 🚀 One-Command Setup

```bash
# 1. Clone and setup frontend
cd AngularProject/student-portal
npm install
npm start

# 2. Setup backend (in new terminal)
# Copy student-portal-backend to C:\xampp\htdocs\
# Start XAMPP Apache & MySQL
# Run: http://localhost/student-portal-backend/setup_database.php

# 3. Access application
# Frontend: http://localhost:4200
# Backend API: http://localhost/student-portal-backend
```

## 🔧 Installation & Setup

### Step 1: Frontend Setup

```bash
# Navigate to Angular project
cd AngularProject/student-portal

# Install dependencies
npm install

# Start development server
npm start

# Application will be available at: http://localhost:4200
```

### Step 2: Backend Setup

```bash
# 1. Start XAMPP Control Panel
# 2. Start Apache and MySQL services

# 3. Copy backend to htdocs
cp -r student-portal-backend C:/xampp/htdocs/

# 4. Create database
# Open: http://localhost/phpmyadmin
# Create database: student_portal

# 5. Run database setup
# Open: http://localhost/student-portal-backend/setup_database.php
```

### Step 3: Configuration

#### Frontend Configuration (`src/environments/environment.ts`)
```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost/student-portal-backend'
};
```

#### Backend Configuration (`config.php`)
```php
<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'student_portal';
?>
```

## 🎨 Frontend (Angular)

### Key Components

#### 🏠 Home Component
- **Welcome message** with user greeting
- **Feature cards** showcasing portal capabilities
- **Responsive design** with Bootstrap grid
- **Navigation buttons** to login/register

#### 🔐 Authentication Components
- **Login Form**: Email/password with validation
- **Registration Form**: Multi-step form with validation
- **Form Validation**: Real-time error messages
- **Loading States**: User feedback during API calls

#### 📝 Complaint Management
- **Complaint Form**: Category selection and description
- **Complaint List**: View submitted complaints
- **Status Tracking**: Real-time status updates
- **Responsive Tables**: Mobile-friendly display

### Services

#### 🔐 AuthService
```typescript
@Injectable({ providedIn: 'root' })
export class AuthService {
  // Session management (30 minutes)
  // User authentication
  // Login/logout functionality
  // Password hashing (frontend validation)
}
```

#### 🌐 ApiService
```typescript
@Injectable({ providedIn: 'root' })
export class ApiService {
  // HTTP client configuration
  // API endpoint management
  // Error handling
  // Request/response interceptors
}
```

### Routing & Guards

#### 🛡️ AuthGuard
```typescript
@Injectable({ providedIn: 'root' })
export class AuthGuard implements CanActivate {
  canActivate(): boolean {
    // Check authentication status
    // Redirect to login if not authenticated
    // Allow access if authenticated
  }
}
```

#### 🛣️ Routes Configuration
```typescript
export const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'complaint', component: ComplaintComponent, canActivate: [AuthGuard] },
  { path: '**', redirectTo: '' }
];
```

## 🔙 Backend (PHP/MySQL)

### Database Schema

#### 👤 Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    roll_no VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

#### 📝 Complaints Table
```sql
CREATE TABLE complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    category ENUM('Academics', 'Hostel', 'Admin', 'Other') NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Pending', 'In Progress', 'Resolved', 'Closed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### API Endpoints

#### 🔐 Authentication
- `POST /register.php` - User registration
- `POST /login.php` - User authentication

#### 📝 Complaints
- `POST /submit_complaint.php` - Create new complaint
- `GET /get_complaints.php` - Retrieve complaints (with optional student_id)

### Security Features

#### 🔒 Password Security
```php
// Password hashing
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Password verification
if (password_verify($password, $hashedPassword)) {
    // Authentication successful
}
```

#### 🛡️ SQL Injection Protection
```php
// Prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
```

#### 🌐 CORS Configuration
```php
// Allow cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

## 🧪 Testing & Troubleshooting

### Sample Test Users

| Name | Email | Password | Roll No | Department |
|------|-------|----------|---------|------------|
| John Doe | john@example.com | password123 | CS2024001 | Computer Science |
| Jane Smith | jane@example.com | password123 | IT2024002 | Information Technology |

### Testing Tools

#### 🔍 Backend Testing URLs
- `http://localhost/student-portal-backend/test_db.php` - Database connection test
- `http://localhost/student-portal-backend/test_login.php` - Login functionality test
- `http://localhost/student-portal-backend/test_registration.php` - Registration test
- `http://localhost/student-portal-backend/test_cors.php` - CORS connectivity test
- `http://localhost/student-portal-backend/diagnose_login.php` - Complete login diagnosis

#### 🧪 Frontend Testing
```bash
# Run Angular tests
ng test

# Run with coverage
ng test --code-coverage

# Run e2e tests
ng e2e
```

### Common Issues & Solutions

#### ❌ "Cannot GET /complaint" Error
```bash
# Solution: Check server.ts middleware
# The Express server needs proper fallback routing
```

#### ❌ CORS Errors
```bash
# Solution: Verify Apache is running
# Check: http://localhost/student-portal-backend/test_cors.php
```

#### ❌ Database Connection Failed
```bash
# Solution: Start XAMPP MySQL
# Verify credentials in config.php
```

## 📚 API Documentation

### Authentication Endpoints

#### User Registration
```http
POST /register.php
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "roll_no": "CS2024001",
  "department": "Computer Science",
  "phone": "+1234567890",
  "password": "password123"
}
```

#### User Login
```http
POST /login.php
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

### Complaint Endpoints

#### Submit Complaint
```http
POST /submit_complaint.php
Content-Type: application/json

{
  "student_id": 1,
  "title": "WiFi Issue",
  "category": "Admin",
  "description": "WiFi not working in library"
}
```

#### Get Complaints
```http
GET /get_complaints.php
GET /get_complaints.php?student_id=1
```

## 🔒 Security Features

### Frontend Security
- **Input Validation**: Angular reactive forms with validators
- **Route Guards**: Authentication checks before navigation
- **Session Management**: Automatic logout on inactivity
- **XSS Protection**: Angular's built-in sanitization

### Backend Security
- **Password Hashing**: bcrypt with cost factor 12
- **SQL Injection Prevention**: Prepared statements
- **Input Sanitization**: mysqli_real_escape_string
- **CORS Protection**: Configurable origin restrictions
- **Error Handling**: No sensitive data in error messages

## 🚀 Deployment

### Production Frontend Build
```bash
# Build for production
ng build --configuration production

# Build with SSR
ng build --configuration production && ng run student-portal:server:production
```

### Production Backend Setup
```bash
# 1. Update database credentials
# 2. Configure production domain
# 3. Enable HTTPS
# 4. Set up proper CORS origins
# 5. Configure Apache virtual host
```

### Environment Variables
```bash
# Frontend (.env)
API_URL=https://api.yourdomain.com

# Backend (config.php)
PRODUCTION_DB_HOST=your-db-host
PRODUCTION_DB_USER=your-db-user
PRODUCTION_DB_PASS=your-secure-password
```

## 🆘 Troubleshooting

### Frontend Issues

#### Build Errors
```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install
```

#### Routing Issues
```bash
# Check server.ts for proper fallback routing
# Verify base href in index.html
```

#### CORS Issues
```bash
# Check backend CORS configuration
# Verify Apache headers
```

### Backend Issues

#### Database Connection
```bash
# Verify XAMPP MySQL is running
# Check credentials in config.php
# Test connection: http://localhost/student-portal-backend/test_db.php
```

#### API Errors
```bash
# Check PHP error logs
# Verify file permissions
# Test endpoints with Postman
```

#### File Permissions
```bash
# Ensure PHP files are readable
# Check Apache user permissions
```

## 📝 Contributing

### Development Workflow
1. **Fork** the repository
2. **Create** a feature branch
3. **Make** your changes
4. **Test** thoroughly
5. **Submit** a pull request

### Code Standards
- **Angular**: Follow Angular style guide
- **TypeScript**: Strict mode enabled
- **PHP**: PSR-12 standards
- **SCSS**: BEM methodology
- **Git**: Conventional commits

### Testing Requirements
- **Unit Tests**: 80%+ coverage
- **E2E Tests**: Critical user flows
- **Manual Testing**: Cross-browser compatibility

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Angular Team** for the amazing framework
- **Bootstrap Team** for the responsive CSS framework
- **PHP Community** for the robust backend language
- **Open Source Community** for the countless libraries and tools

---

## 🎯 Quick Commands Reference

### Frontend
```bash
npm install          # Install dependencies
npm start           # Start dev server
npm run build      # Production build
ng test            # Run tests
ng lint            # Code linting
```

### Backend
```bash
# Start XAMPP Apache & MySQL
# Access: http://localhost/student-portal-backend
# Database setup: /setup_database.php
# Testing: /test_*.php files
```

### Database
```bash
# phpMyAdmin: http://localhost/phpmyadmin
# Database: student_portal
# Import: database_setup.sql
```

---

**🎓 Happy Coding!** Your Student Portal is ready to empower students with modern web technology! 🚀

## 🚀 Setup Instructions

### 1. Database Setup
1. Open phpMyAdmin in your browser (usually `http://localhost/phpmyadmin`)
2. Create a new database named `student_portal`
3. Open the SQL tab and run the contents of `database_setup.sql`
4. This will create the required tables and insert sample data

### 2. Backend Deployment
1. Copy the entire `student-portal-backend` folder to your XAMPP's `htdocs` directory:
   ```
   C:\xampp\htdocs\student-portal-backend\
   ```
2. Make sure XAMPP's Apache server is running

### 3. Frontend Configuration
The frontend is already configured to connect to `http://localhost/student-portal-backend`

## 📋 API Endpoints

### Authentication
- `POST /register.php` - User registration
- `POST /login.php` - User login

### Complaints
- `POST /submit_complaint.php` - Submit a new complaint
- `GET /get_complaints.php` - Get complaints (with optional `student_id` parameter)

## 🗄️ Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    roll_no VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

### Complaints Table
```sql
CREATE TABLE complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    category ENUM('Academics', 'Hostel', 'Admin', 'Other') NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Pending', 'In Progress', 'Resolved', 'Closed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 🔧 Configuration

### Database Connection (config.php)
- **Host**: localhost
- **Username**: root (default XAMPP)
- **Password**: (empty by default)
- **Database**: student_portal

### CORS Configuration
The backend is configured to allow cross-origin requests from any origin for development purposes.

## 🧪 Testing & Troubleshooting

### Sample Users for Testing
After running the database setup, you can login with these sample users:

| Name | Email | Password |
|------|-------|----------|
| John Doe | john@example.com | password123 |
| Jane Smith | jane@example.com | password123 |

### 🔧 Troubleshooting Tools

#### 1. Database Test
**URL**: `http://localhost/student-portal-backend/test_db.php`
- ✅ Tests database connection
- ✅ Shows all users in database
- ✅ Tests password verification
- ✅ Displays database tables and data

#### 2. Create Test User
**URL**: `http://localhost/student-portal-backend/create_test_user.php`
- ✅ Creates a test user automatically
- ✅ Shows password hashing in action
- ✅ Verifies user creation
- ✅ Provides login test credentials

#### 3. Password Generator
**URL**: `http://localhost/student-portal-backend/password_generator.php`
- ✅ Generates bcrypt hashed passwords
- ✅ Multiple test passwords included
- ✅ Copy-friendly format
- ✅ Usage instructions provided

#### 4. Registration Test
**URL**: `http://localhost/student-portal-backend/test_registration.php`
- ✅ Tests complete registration flow
- ✅ Creates test users automatically
- ✅ Validates database operations
- ✅ Tests password hashing/verification
- ✅ Provides API test commands

#### 5. CORS & Connectivity Test
**URL**: `http://localhost/student-portal-backend/test_cors.php`
- ✅ Tests frontend-backend connectivity
- ✅ Verifies CORS configuration
- ✅ Checks database table existence
- ✅ Provides JavaScript test code
- ✅ Diagnoses network issues

#### 6. Login Test
**URL**: `http://localhost/student-portal-backend/test_login.php`
- ✅ Tests login with all registered users
- ✅ Shows database user data
- ✅ Tests password verification
- ✅ Provides API test commands
- ✅ Interactive login testing

#### 7. Reset Test User
**URL**: `http://localhost/student-portal-backend/reset_test_user.php`
- ✅ Creates/updates a reliable test user
- ✅ Provides known login credentials
- ✅ Tests password hashing/verification
- ✅ Ready-to-use login credentials

#### 8. Complete Login Diagnosis
**URL**: `http://localhost/student-portal-backend/diagnose_login.php`
- ✅ **Complete diagnostic tool** for login issues
- ✅ Tests database connection and tables
- ✅ Shows all users with password verification
- ✅ Simulates exact login API process
- ✅ Tests CORS and API accessibility
- ✅ Provides JavaScript test code
- ✅ Interactive testing buttons
- ✅ Auto-diagnosis of common issues

#### 9. Registration Validation Test
**URL**: `http://localhost/student-portal-backend/test_registration_validation.php`
- ✅ **Complete validation testing** for registration
- ✅ Tests email uniqueness validation
- ✅ Tests roll number uniqueness validation
- ✅ Tests phone number uniqueness validation
- ✅ Tests format validation (email, phone)
- ✅ Tests password strength requirements
- ✅ Shows existing users for reference
- ✅ Provides test API commands

#### 10. Login Issue Debugger
**URL**: `http://localhost/student-portal-backend/debug_login_issue.php`
- ✅ **Complete login debugging** for registered users
- ✅ Shows all registered users with details
- ✅ Tests password verification for each user
- ✅ Finds correct passwords for existing users
- ✅ Simulates exact login API process
- ✅ Interactive testing with common passwords
- ✅ Identifies password hash mismatches
- ✅ Provides working login credentials

#### 11. Frontend Connection Test
**URL**: `http://localhost/student-portal-backend/test_frontend_connection.php`
- ✅ **Complete frontend-backend connectivity testing**
- ✅ Tests API endpoints reachability
- ✅ Simulates Angular HttpClient requests
- ✅ Checks CORS headers and policies
- ✅ Provides JavaScript test code for browser console
- ✅ Tests network connectivity and firewall issues
- ✅ Diagnoses Apache and XAMPP configuration
- ✅ Interactive testing with real-time results

#### 12. Page Refresh Test
**URL**: `http://localhost/student-portal-backend/test_page_refresh.php`
- ✅ **Complete page refresh testing for Angular routing**
- ✅ Tests browser refresh on all routes
- ✅ Verifies direct URL access works
- ✅ Checks for 'Cannot GET' errors
- ✅ Tests Angular Universal fallback routing
- ✅ Provides step-by-step testing instructions
- ✅ Alternative server configuration options
- ✅ Success checklist and troubleshooting guide

#### 13. Direct Login API Test
**URL**: `http://localhost/student-portal-backend/test_login_direct.php`
- ✅ **Direct login API testing without Angular**
- ✅ Tests backend login functionality independently
- ✅ Verifies database connection and user authentication
- ✅ Simulates exact API calls made by frontend
- ✅ Identifies if issue is frontend or backend
- ✅ Provides detailed error diagnosis
- ✅ cURL-based testing for reliability
- ✅ Complete troubleshooting guide

#### 14. Database Setup
**URL**: `http://localhost/student-portal-backend/setup_database.php`
- ✅ **Complete database initialization**
- ✅ Creates student_portal database
- ✅ Sets up users table with all required columns
- ✅ Creates complaints table with foreign keys
- ✅ Adds missing columns like last_login
- ✅ Verifies table structure and indexes
- ✅ Provides database status overview
- ✅ One-click database setup solution

### Register a New User
```bash
curl -X POST http://localhost/student-portal-backend/register.php \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "roll_no": "CS2024001",
    "department": "Computer Science",
    "phone": "+1234567890",
    "password": "password123"
  }'
```

### Login with Sample User
```bash
curl -X POST http://localhost/student-portal-backend/login.php \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Submit Complaint
```bash
curl -X POST http://localhost/student-portal-backend/submit_complaint.php \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "title": "WiFi Issue",
    "category": "Admin",
    "description": "WiFi not working in library"
  }'
```

### Get Complaints
```bash
# Get all complaints
curl http://localhost/student-portal-backend/get_complaints.php

# Get complaints for specific student
curl http://localhost/student-portal-backend/get_complaints.php?student_id=1
```

## 🔒 Security Features

- **Password Hashing**: Uses PHP's `password_hash()` with bcrypt
- **Input Sanitization**: All inputs are sanitized using `real_escape_string()`
- **SQL Injection Protection**: Uses prepared statements
- **CORS Protection**: Configured for development (restrict in production)

## 📝 Notes

- The backend uses MySQLi for database connections
- Passwords are hashed using bcrypt for security
- All API responses are in JSON format
- Error handling is implemented for all endpoints
- The system tracks user login times automatically

## 🚨 Production Deployment

For production deployment:
1. Update database credentials in `config.php`
2. Restrict CORS origins to your domain only
3. Enable HTTPS
4. Add rate limiting
5. Implement proper logging
6. Add input validation middleware

## 🆘 Troubleshooting

### Common Issues:
1. **CORS Errors**: Make sure Apache is running and the backend is accessible
2. **Database Connection**: Verify XAMPP MySQL is running and credentials are correct
3. **File Permissions**: Ensure PHP files have proper read permissions
4. **Port Conflicts**: Make sure no other service is using port 80

### Debug Steps:
1. Check Apache error logs: `C:\xampp\apache\logs\error.log`
2. Verify database connection in phpMyAdmin
3. Test API endpoints using curl or Postman
4. Check browser developer tools for network errors