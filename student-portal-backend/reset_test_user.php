<?php
require_once 'config.php';

echo "<h1>Reset/Create Test User</h1>";
echo "<p>This script creates a reliable test user for login testing.</p>";

// Test database connection
$conn = getDBConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✅ Database connection successful!</p>";

// Test user credentials
$testUser = [
    'name' => 'Test Student',
    'email' => 'test@student.com',
    'roll_no' => 'TEST001',
    'department' => 'Computer Science',
    'phone' => '+1234567890',
    'password' => 'test123'
];

// Hash the password
$hashedPassword = password_hash($testUser['password'], PASSWORD_DEFAULT);

echo "<h2>Test User Credentials:</h2>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>Login Information:</h3>";
echo "<ul>";
echo "<li><strong>Email:</strong> " . $testUser['email'] . "</li>";
echo "<li><strong>Password:</strong> " . $testUser['password'] . "</li>";
echo "<li><strong>Roll No:</strong> " . $testUser['roll_no'] . "</li>";
echo "</ul>";
echo "<p style='color: blue; font-weight: bold;'>📋 Save these credentials for testing!</p>";
echo "</div>";

// Check if test user already exists
$check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check_stmt->bind_param("s", $testUser['email']);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing user
    $existing_user = $result->fetch_assoc();
    $user_id = $existing_user['id'];

    $update_stmt = $conn->prepare("UPDATE users SET name=?, roll_no=?, department=?, phone=?, password=? WHERE id=?");
    $update_stmt->bind_param("sssssi",
        $testUser['name'],
        $testUser['roll_no'],
        $testUser['department'],
        $testUser['phone'],
        $hashedPassword,
        $user_id
    );

    if ($update_stmt->execute()) {
        echo "<p style='color: green;'>✅ Test user updated successfully! (ID: $user_id)</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to update test user: " . $conn->error . "</p>";
    }
    $update_stmt->close();

} else {
    // Create new user
    $insert_stmt = $conn->prepare("INSERT INTO users (name, email, roll_no, department, phone, password) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("ssssss",
        $testUser['name'],
        $testUser['email'],
        $testUser['roll_no'],
        $testUser['department'],
        $testUser['phone'],
        $hashedPassword
    );

    if ($insert_stmt->execute()) {
        $user_id = $conn->insert_id;
        echo "<p style='color: green;'>✅ Test user created successfully! (ID: $user_id)</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create test user: " . $conn->error . "</p>";
    }
    $insert_stmt->close();
}

$check_stmt->close();

// Verify the user
if (isset($user_id)) {
    $verify_stmt = $conn->prepare("SELECT id, name, email, roll_no, password FROM users WHERE id = ?");
    $verify_stmt->bind_param("i", $user_id);
    $verify_stmt->execute();
    $result = $verify_stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<h2>User Verification:</h2>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>ID</td><td>" . $user['id'] . "</td></tr>";
        echo "<tr><td>Name</td><td>" . htmlspecialchars($user['name']) . "</td></tr>";
        echo "<tr><td>Email</td><td>" . htmlspecialchars($user['email']) . "</td></tr>";
        echo "<tr><td>Roll No</td><td>" . htmlspecialchars($user['roll_no']) . "</td></tr>";
        echo "<tr><td>Password Hash</td><td style='font-family: monospace; font-size: 12px;'>" . htmlspecialchars(substr($user['password'], 0, 30)) . "...</td></tr>";
        echo "</table>";

        // Test password verification
        if (password_verify($testUser['password'], $user['password'])) {
            echo "<p style='color: green; font-weight: bold;'>✅ Password verification: SUCCESS</p>";
            echo "<p>The test user is ready for login!</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ Password verification: FAILED</p>";
        }
    }
    $verify_stmt->close();
}

$conn->close();

echo "<hr>";
echo "<h2>Test Login Now:</h2>";
echo "<p>You can now test login with these exact credentials:</p>";
echo "<ul>";
echo "<li><strong>Email:</strong> " . $testUser['email'] . "</li>";
echo "<li><strong>Password:</strong> " . $testUser['password'] . "</li>";
echo "</ul>";

echo "<h3>Quick Test Options:</h3>";
echo "<ol>";
echo "<li><a href='test_login.php' target='_blank'>Test Login Page</a> - See all users and test login</li>";
echo "<li><a href='test_db.php' target='_blank'>Database Test</a> - Verify database connection</li>";
echo "<li>Test in your Angular app with the credentials above</li>";
echo "</ol>";

echo "<h3>API Test:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "curl -X POST http://localhost/student-portal-backend/login.php \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\n";
echo "    \"email\": \"" . $testUser['email'] . "\",\n";
echo "    \"password\": \"" . $testUser['password'] . "\"\n";
echo "  }'";
echo "</pre>";

echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #4CAF50;'>";
echo "<h3>🎉 Success!</h3>";
echo "<p>Your test user is now ready. Use these credentials in your Angular login form:</p>";
echo "<p style='font-size: 18px; font-weight: bold; color: #2E7D32;'>";
echo "Email: " . $testUser['email'] . "<br>";
echo "Password: " . $testUser['password'];
echo "</p>";
echo "</div>";
?>