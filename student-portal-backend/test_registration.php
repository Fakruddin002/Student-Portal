<?php
require_once 'config.php';

echo "<h1>Student Portal - Registration Test</h1>";
echo "<p>This script tests the registration functionality.</p>";

// Test database connection
$conn = getDBConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✅ Database connection successful!</p>";

// Test data
$testData = [
    'name' => 'Test Student',
    'email' => 'student' . time() . '@example.com', // Unique email
    'roll_no' => 'CS' . time(), // Unique roll number
    'department' => 'Computer Science',
    'phone' => '+1234567890',
    'password' => 'testpassword123'
];

echo "<h2>Test Registration Data:</h2>";
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Value</th></tr>";
foreach ($testData as $key => $value) {
    echo "<tr><td>" . ucfirst($key) . "</td><td>" . htmlspecialchars($value) . "</td></tr>";
}
echo "</table>";

// Test the same validation as the API
echo "<h2>Validation Test:</h2>";

// Check if email already exists
$check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check_email->bind_param("s", $testData['email']);
$check_email->execute();
$result = $check_email->get_result();

if ($result->num_rows > 0) {
    echo "<p style='color: red;'>❌ Email already exists: " . $testData['email'] . "</p>";
    $check_email->close();
    exit();
} else {
    echo "<p style='color: green;'>✅ Email is available</p>";
}
$check_email->close();

// Check if roll number already exists
$check_roll = $conn->prepare("SELECT id FROM users WHERE roll_no = ?");
$check_roll->bind_param("s", $testData['roll_no']);
$check_roll->execute();
$result = $check_roll->get_result();

if ($result->num_rows > 0) {
    echo "<p style='color: red;'>❌ Roll number already exists: " . $testData['roll_no'] . "</p>";
    $check_roll->close();
    exit();
} else {
    echo "<p style='color: green;'>✅ Roll number is available</p>";
}
$check_roll->close();

// Hash password
$hashed_password = password_hash($testData['password'], PASSWORD_DEFAULT);
echo "<p style='color: green;'>✅ Password hashed successfully</p>";
echo "<p><strong>Hashed Password:</strong> <code>$hashed_password</code></p>";

// Attempt registration
$stmt = $conn->prepare("INSERT INTO users (name, email, roll_no, department, phone, password, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("ssssss",
    $testData['name'],
    $testData['email'],
    $testData['roll_no'],
    $testData['department'],
    $testData['phone'],
    $hashed_password
);

if ($stmt->execute()) {
    $user_id = $conn->insert_id;
    echo "<p style='color: green; font-weight: bold;'>✅ Registration successful! User ID: $user_id</p>";

    // Verify the user was created
    $verify_stmt = $conn->prepare("SELECT id, name, email, roll_no FROM users WHERE id = ?");
    $verify_stmt->bind_param("i", $user_id);
    $verify_stmt->execute();
    $result = $verify_stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<h3>Created User Details:</h3>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> " . $user['id'] . "</li>";
        echo "<li><strong>Name:</strong> " . htmlspecialchars($user['name']) . "</li>";
        echo "<li><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</li>";
        echo "<li><strong>Roll No:</strong> " . htmlspecialchars($user['roll_no']) . "</li>";
        echo "</ul>";
    }
    $verify_stmt->close();

} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Registration failed: " . $conn->error . "</p>";
}

$stmt->close();

// Test password verification
echo "<h2>Password Verification Test:</h2>";
$login_stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
$login_stmt->bind_param("s", $testData['email']);
$login_stmt->execute();
$result = $login_stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($testData['password'], $user['password'])) {
        echo "<p style='color: green;'>✅ Password verification: SUCCESS</p>";
    } else {
        echo "<p style='color: red;'>❌ Password verification: FAILED</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Could not find user for password verification</p>";
}

$login_stmt->close();
$conn->close();

echo "<hr>";
echo "<h2>API Test Commands:</h2>";

// Generate API test commands
echo "<h3>Test Registration via API:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "curl -X POST http://localhost/student-portal-backend/register.php \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\n";
echo "    \"name\": \"" . $testData['name'] . "\",\n";
echo "    \"email\": \"newstudent@example.com\",\n";
echo "    \"roll_no\": \"CS" . (time() + 1) . "\",\n";
echo "    \"department\": \"" . $testData['department'] . "\",\n";
echo "    \"phone\": \"" . $testData['phone'] . "\",\n";
echo "    \"password\": \"" . $testData['password'] . "\"\n";
echo "  }'";
echo "</pre>";

echo "<h3>Test Login with Created User:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "curl -X POST http://localhost/student-portal-backend/login.php \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\n";
echo "    \"email\": \"" . $testData['email'] . "\",\n";
echo "    \"password\": \"" . $testData['password'] . "\"\n";
echo "  }'";
echo "</pre>";

echo "<h2>Troubleshooting Steps:</h2>";
echo "<ol>";
echo "<li><strong>Check Database:</strong> Visit <code>test_db.php</code> to verify database connection</li>";
echo "<li><strong>Test API:</strong> Use the curl commands above to test the API directly</li>";
echo "<li><strong>Check CORS:</strong> Make sure your Angular app can connect to the backend</li>";
echo "<li><strong>Verify Tables:</strong> Ensure users and complaints tables exist</li>";
echo "<li><strong>Check Logs:</strong> Look at Apache error logs for PHP errors</li>";
echo "</ol>";
?>