<?php
require_once 'config.php';

echo "<h1>Create Test User</h1>";
echo "<p>This script will create a test user for login testing.</p>";

// Test database connection
$conn = getDBConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✅ Database connection successful!</p>";

// Test user details
$name = "Test User";
$email = "test@example.com";
$roll_no = "CS2024001";
$department = "Computer Science";
$phone = "+1234567890";
$plain_password = "password123";

// Hash the password
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

echo "<h2>User Details:</h2>";
echo "<ul>";
echo "<li><strong>Name:</strong> $name</li>";
echo "<li><strong>Email:</strong> $email</li>";
echo "<li><strong>Roll No:</strong> $roll_no</li>";
echo "<li><strong>Department:</strong> $department</li>";
echo "<li><strong>Phone:</strong> $phone</li>";
echo "<li><strong>Plain Password:</strong> $plain_password</li>";
echo "<li><strong>Hashed Password:</strong> <code>$hashed_password</code></li>";
echo "</ul>";

// Check if user already exists
$check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    echo "<p style='color: orange;'>⚠️ User already exists with email: $email</p>";

    // Update existing user
    $update_stmt = $conn->prepare("UPDATE users SET name=?, roll_no=?, department=?, phone=?, password=? WHERE email=?");
    $update_stmt->bind_param("ssssss", $name, $roll_no, $department, $phone, $hashed_password, $email);

    if ($update_stmt->execute()) {
        echo "<p style='color: green;'>✅ User updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to update user: " . $conn->error . "</p>";
    }
    $update_stmt->close();
} else {
    // Create new user
    $insert_stmt = $conn->prepare("INSERT INTO users (name, email, roll_no, department, phone, password) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("ssssss", $name, $email, $roll_no, $department, $phone, $hashed_password);

    if ($insert_stmt->execute()) {
        $user_id = $conn->insert_id;
        echo "<p style='color: green;'>✅ User created successfully with ID: $user_id</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create user: " . $conn->error . "</p>";
    }
    $insert_stmt->close();
}

$check_stmt->close();

// Verify the user was created/updated
echo "<h2>Verify User:</h2>";
$verify_stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
$verify_stmt->bind_param("s", $email);
$verify_stmt->execute();
$result = $verify_stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>ID</td><td>" . $user['id'] . "</td></tr>";
    echo "<tr><td>Name</td><td>" . htmlspecialchars($user['name']) . "</td></tr>";
    echo "<tr><td>Email</td><td>" . htmlspecialchars($user['email']) . "</td></tr>";
    echo "<tr><td>Password Hash</td><td style='font-family: monospace; font-size: 12px;'>" . htmlspecialchars($user['password']) . "</td></tr>";
    echo "</table>";

    // Test password verification
    if (password_verify($plain_password, $user['password'])) {
        echo "<p style='color: green; font-weight: bold;'>✅ Password verification test: PASSED</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Password verification test: FAILED</p>";
    }
} else {
    echo "<p style='color: red;'>❌ User verification failed!</p>";
}

$verify_stmt->close();
$conn->close();

echo "<hr>";
echo "<h2>Test Login:</h2>";
echo "<p>You can now test login with:</p>";
echo "<ul>";
echo "<li><strong>Email:</strong> $email</li>";
echo "<li><strong>Password:</strong> $plain_password</li>";
echo "</ul>";

echo "<p><strong>API Test:</strong></p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "curl -X POST http://localhost/student-portal-backend/login.php \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\n";
echo "    \"email\": \"$email\",\n";
echo "    \"password\": \"$plain_password\"\n";
echo "  }'";
echo "</pre>";
?>