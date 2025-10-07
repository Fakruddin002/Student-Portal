-- Simple Database Setup for Student Portal
-- Run these commands one by one in phpMyAdmin

-- Step 1: Create database
CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- Step 2: Create users table
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

-- Step 3: Create complaints table
CREATE TABLE complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id)
);

-- Step 4: Add indexes for better performance
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_roll_no ON users(roll_no);
CREATE INDEX idx_student_id ON complaints(student_id);
CREATE INDEX idx_status ON complaints(status);

-- Step 5: Insert sample users (passwords are hashed)
INSERT INTO users (name, email, roll_no, department, phone, password) VALUES
('John Doe', 'john@example.com', 'CS2024001', 'Computer Science', '+1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane Smith', 'jane@example.com', 'IT2024002', 'Information Technology', '+1234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Step 6: Insert sample complaints
INSERT INTO complaints (student_id, title, category, description, status) VALUES
(1, 'WiFi Connection Issue', 'Admin', 'WiFi is not working properly in the library area.', 'In Progress'),
(1, 'Course Material Access', 'Academics', 'Cannot access the uploaded course materials.', 'Resolved');

-- Success message
SELECT 'Database setup completed successfully!' as Status;