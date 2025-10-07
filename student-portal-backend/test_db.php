<?php
require_once 'config.php';

echo "<h1>Student Portal - Database Test</h1>";

// Test database connection
$conn = getDBConnection();
if ($conn) {
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    exit();
}

echo "<h2>Current Users in Database:</h2>";

// Check users table
$result = $conn->query("SELECT id, name, email, password FROM users");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Name</th><th>Email</th><th>Password Hash</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td style='font-family: monospace; font-size: 12px;'>" . htmlspecialchars($row['password']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ No users found in database. Please run the database setup script.</p>";
}

echo "<h2>Test Login:</h2>";

// Test login with sample credentials
$test_email = "john@example.com";
$test_password = "password123";

echo "<p>Testing login with: <strong>$test_email</strong> / <strong>$test_password</strong></p>";

// Get user from database
$stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    echo "<h3>User Found:</h3>";
    echo "<ul>";
    echo "<li>ID: " . $user['id'] . "</li>";
    echo "<li>Name: " . htmlspecialchars($user['name']) . "</li>";
    echo "<li>Email: " . htmlspecialchars($user['email']) . "</li>";
    echo "<li>Password Hash: " . htmlspecialchars($user['password']) . "</li>";
    echo "</ul>";

    // Test password verification
    if (password_verify($test_password, $user['password'])) {
        echo "<p style='color: green; font-weight: bold;'>✅ Password verification: SUCCESS</p>";
        echo "<p>The password hash matches the plain password!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Password verification: FAILED</p>";
        echo "<p>The password hash does not match the plain password.</p>";

        // Show what the correct hash should be
        $correct_hash = password_hash($test_password, PASSWORD_DEFAULT);
        echo "<p>Expected hash for '$test_password': <code>$correct_hash</code></p>";
    }
} else {
    echo "<p style='color: red;'>❌ User not found with email: $test_email</p>";
    echo "<p>Please make sure you have run the database setup script.</p>";
}

echo "<h2>Database Tables:</h2>";
$tables_result = $conn->query("SHOW TABLES");
if ($tables_result) {
    echo "<ul>";
    while ($table = $tables_result->fetch_array()) {
        echo "<li>" . $table[0] . "</li>";
    }
    echo "</ul>";
}

echo "<h2>Complaints Table:</h2>";
$complaints_result = $conn->query("SELECT COUNT(*) as count FROM complaints");
if ($complaints_result) {
    $count = $complaints_result->fetch_assoc()['count'];
    echo "<p>Total complaints: $count</p>";
}

$stmt->close();
$conn->close();

echo "<hr>";
echo "<h2>Quick Fix Options:</h2>";
echo "<ol>";
echo "<li><strong>Re-run database setup:</strong> Execute the SQL from <code>simple_setup.sql</code> in phpMyAdmin</li>";
echo "<li><strong>Check XAMPP:</strong> Make sure Apache and MySQL are running</li>";
echo "<li><strong>Test API:</strong> Try the login API directly with curl or Postman</li>";
echo "<li><strong>Check logs:</strong> Look at Apache error logs if issues persist</li>";
echo "</ol>";
?>