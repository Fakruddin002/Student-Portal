# 🎓 Student Portal - Full Stack Application

A comprehensive student management system built with **Angular 20** (frontend) and **PHP/MySQL** (backend). Features user authentication, complaint management, and responsive design.

## 📋 Table of Contents

- [🚀 Features](#-features)
- [🛠️ Tech Stack](#️-tech-stack)
- [📁 Project Structure](#-project-structure)
- [⚡ Quick Start](#-quick-start)
- [🔧 Installation & Setup](#-installation--setup)
- [🎨 Frontend (Angular)](#-frontend-angular)

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

