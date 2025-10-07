<?php
require_once 'config.php';

echo "<h1>🗄️ Database Setup for Student Portal</h1>";
echo "<p>This script will create the necessary database tables and structure.</p>";

// Create database if it doesn't exist
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("<p style='color: red;'>❌ Connection failed: " . $conn->connect_error . "</p>");
}

echo "<p style='color: green;'>✅ Connected to MySQL successfully!</p>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✅ Database '" . DB_NAME . "' created or already exists!</p>";
} else {
    echo "<p style='color: red;'>❌ Error creating database: " . $conn->error . "</p>";
}

$conn->close();

// Connect to the specific database
$conn = getDBConnection();

if (!$conn) {
    die("<p style='color: red;'>❌ Could not connect to database!</p>");
}

echo "<p style='color: green;'>✅ Connected to database '" . DB_NAME . "' successfully!</p>";

// Create users table with all required columns
$createUsersTable = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    roll_no VARCHAR(50),
    department VARCHAR(100),
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email)
)";

if ($conn->query($createUsersTable) === TRUE) {
    echo "<p style='color: green;'>✅ Users table created or already exists!</p>";
} else {
    echo "<p style='color: red;'>❌ Error creating users table: " . $conn->error . "</p>";
}

// Create complaints table
$createComplaintsTable = "
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Pending', 'In Progress', 'Resolved') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_status (status)
)";

if ($conn->query($createComplaintsTable) === TRUE) {
    echo "<p style='color: green;'>✅ Complaints table created or already exists!</p>";
} else {
    echo "<p style='color: red;'>❌ Error creating complaints table: " . $conn->error . "</p>";
}

// Check if last_login column exists, if not add it
$checkColumn = "SHOW COLUMNS FROM users LIKE 'last_login'";
$result = $conn->query($checkColumn);

if ($result->num_rows === 0) {
    $addColumn = "ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL";
    if ($conn->query($addColumn) === TRUE) {
        echo "<p style='color: green;'>✅ Added 'last_login' column to users table!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error adding 'last_login' column: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ 'last_login' column already exists!</p>";
}

// Get current table structure
echo "<h2>📋 Current Database Structure</h2>";
echo "<h3>Users Table:</h3>";
$result = $conn->query("DESCRIBE users");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Get user count
$userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
echo "<p><strong>Total Users:</strong> $userCount</p>";

$conn->close();

echo "<h2>🎉 Database Setup Complete!</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
echo "<h3>✅ What's Been Set Up:</h3>";
echo "<ul>";
echo "<li>✅ Database: <code>" . DB_NAME . "</code></li>";
echo "<li>✅ Users table with all required columns</li>";
echo "<li>✅ Complaints table with foreign key relationships</li>";
echo "<li>✅ last_login column for session tracking</li>";
echo "<li>✅ Proper indexes for performance</li>";
echo "</ul>";
echo "</div>";

echo "<h2>🚀 Next Steps:</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; border-left: 4px solid #17a2b8;'>";
echo "<ol>";
echo "<li><strong>Create Test User:</strong> <a href='reset_test_user.php' target='_blank'>reset_test_user.php</a></li>";
echo "<li><strong>Test Login API:</strong> <a href='test_login_direct.php' target='_blank'>test_login_direct.php</a></li>";
echo "<li><strong>Test Frontend Connection:</strong> <a href='test_frontend_connection.php' target='_blank'>test_frontend_connection.php</a></li>";
echo "<li><strong>Try Angular Login:</strong> <a href='http://localhost:4200' target='_blank'>http://localhost:4200</a></li>";
echo "</ol>";
echo "</div>";

echo "<h2>📊 Database Status:</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
echo "<p><strong>Users Table:</strong> ✅ Created with " . (isset($userCount) ? $userCount : 0) . " users</p>";
echo "<p><strong>Complaints Table:</strong> ✅ Created</p>";
echo "<p><strong>last_login Column:</strong> ✅ Added</p>";
echo "</div>";
?>