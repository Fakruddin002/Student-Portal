-- Student Portal Database Setup
-- Run this SQL script in phpMyAdmin to create the required tables

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    roll_no VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_roll_no (roll_no)
);

-- Complaints table
CREATE TABLE IF NOT EXISTS complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    category ENUM('Academics', 'Hostel', 'Admin', 'Other') NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Pending', 'In Progress', 'Resolved', 'Closed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Insert sample data (optional)
INSERT INTO users (name, email, roll_no, department, phone, password) VALUES
('John Doe', 'john@example.com', 'CS2024001', 'Computer Science', '+1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane Smith', 'jane@example.com', 'IT2024002', 'Information Technology', '+1234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Bob Johnson', 'bob@example.com', 'ME2024003', 'Mechanical Engineering', '+1234567892', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample complaints
INSERT INTO complaints (student_id, title, category, description, status) VALUES
(1, 'WiFi Connection Issue', 'Admin', 'WiFi is not working properly in the library area. Connection keeps dropping every few minutes.', 'In Progress'),
(1, 'Course Material Access', 'Academics', 'Cannot access the uploaded course materials for Data Structures course.', 'Resolved'),
(2, 'Hostel Room Maintenance', 'Hostel', 'The bathroom faucet is leaking and needs immediate repair.', 'Pending'),
(3, 'Library Book Return', 'Admin', 'The library system is not updating book returns properly.', 'In Progress');

-- Create a view for easy complaint management (optional)
-- Note: Remove this section if you get errors with the updated_at column
/*
CREATE OR REPLACE VIEW complaint_details AS
SELECT
    c.id,
    c.title,
    c.category,
    c.description,
    c.status,
    c.created_at,
    c.updated_at,
    u.name as student_name,
    u.email as student_email,
    u.roll_no,
    u.department,
    u.phone
FROM complaints c
JOIN users u ON c.student_id = u.id
ORDER BY c.created_at DESC;
*/

-- Success message
SELECT 'Database setup completed successfully!' as Status;