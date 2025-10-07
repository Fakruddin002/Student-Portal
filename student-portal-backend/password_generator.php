<?php
// Password Generator for Testing
// Run this in your browser to generate hashed passwords

echo "<h2>Student Portal - Password Generator</h2>";
echo "<p>Use this to generate bcrypt hashed passwords for testing.</p>";

// Sample passwords to hash
$samplePasswords = [
    'password123',
    'admin123',
    'test123',
    'student123',
    'user123'
];

echo "<h3>Generated Hashed Passwords:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Plain Password</th><th>Hashed Password</th></tr>";

foreach ($samplePasswords as $password) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    echo "<tr>";
    echo "<td><strong>$password</strong></td>";
    echo "<td><code>$hashed</code></td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>How to Use:</h3>";
echo "<ol>";
echo "<li>Copy the hashed password from the table above</li>";
echo "<li>Use it in your SQL INSERT statement</li>";
echo "<li>Example: INSERT INTO users (name, email, password) VALUES ('Test User', 'test@example.com', '<hashed_password>')</li>";
echo "</ol>";

echo "<h3>Current Sample Users in Database:</h3>";
echo "<ul>";
echo "<li><strong>John Doe</strong> - Email: john@example.com - Password: <code>password123</code></li>";
echo "<li><strong>Jane Smith</strong> - Email: jane@example.com - Password: <code>password123</code></li>";
echo "</ul>";

echo "<p><strong>Note:</strong> The hashed passwords in the database_setup.sql file are for 'password123'</p>";
?>