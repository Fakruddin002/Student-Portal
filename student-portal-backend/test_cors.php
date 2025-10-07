<?php
require_once 'config.php';

echo "<h1>CORS & API Connectivity Test</h1>";
echo "<p>This script tests if your Angular frontend can connect to the PHP backend.</p>";

// Test basic API response
echo "<h2>API Health Check:</h2>";
echo "<p>✅ Backend is responding</p>";

// Test CORS headers
echo "<h2>CORS Headers:</h2>";
$headers = getallheaders();
echo "<ul>";
foreach ($headers as $key => $value) {
    echo "<li><strong>$key:</strong> $value</li>";
}
echo "</ul>";

// Test database connection
$conn = getDBConnection();
if ($conn) {
    echo "<p style='color: green;'>✅ Database connection: SUCCESS</p>";

    // Test table existence
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Users table exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Users table not found</p>";
    }

    $result = $conn->query("SHOW TABLES LIKE 'complaints'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Complaints table exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Complaints table not found</p>";
    }

    $conn->close();
} else {
    echo "<p style='color: red;'>❌ Database connection: FAILED</p>";
}

echo "<hr>";
echo "<h2>Frontend Connection Test:</h2>";
echo "<p>Test these URLs from your browser's developer console:</p>";

echo "<h3>1. Test Basic Connectivity:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
// JavaScript test code
echo "fetch('http://localhost/student-portal-backend/test_cors.php')
  .then(response => response.text())
  .then(data => console.log('Backend response:', data))
  .catch(error => console.error('Connection error:', error));";
echo "</pre>";

echo "<h3>2. Test Registration API:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "fetch('http://localhost/student-portal-backend/register.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    name: 'Test User',
    email: 'test" . time() . "@example.com',
    roll_no: 'CS" . time() . "',
    department: 'Computer Science',
    phone: '+1234567890',
    password: 'testpassword123'
  })
})
.then(response => response.json())
.then(data => console.log('Registration response:', data))
.catch(error => console.error('Registration error:', error));";
echo "</pre>";

echo "<h3>3. Test Login API:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "fetch('http://localhost/student-portal-backend/login.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'test@example.com',
    password: 'password123'
  })
})
.then(response => response.json())
.then(data => console.log('Login response:', data))
.catch(error => console.error('Login error:', error));";
echo "</pre>";

echo "<h2>Common Issues & Solutions:</h2>";
echo "<ol>";
echo "<li><strong>CORS Error:</strong> Make sure Apache is running and backend is accessible</li>";
echo "<li><strong>Connection Refused:</strong> Check if XAMPP Apache is started</li>";
echo "<li><strong>Database Error:</strong> Run the database setup script in phpMyAdmin</li>";
echo "<li><strong>404 Error:</strong> Verify backend files are in the correct XAMPP directory</li>";
echo "<li><strong>Network Error:</strong> Check firewall settings and port availability</li>";
echo "</ol>";

echo "<h2>Quick Diagnostics:</h2>";
echo "<ul>";
echo "<li><strong>Backend URL:</strong> http://localhost/student-portal-backend/</li>";
echo "<li><strong>Frontend URL:</strong> http://localhost:4200/</li>";
echo "<li><strong>phpMyAdmin:</strong> http://localhost/phpmyadmin/</li>";
echo "<li><strong>XAMPP Control:</strong> Check Apache and MySQL are running</li>";
echo "</ul>";
?>