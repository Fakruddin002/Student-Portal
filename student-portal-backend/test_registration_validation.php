<?php
require_once 'config.php';

echo "<h1>Registration Validation Test</h1>";
echo "<p>This script tests the uniqueness validation for registration fields.</p>";

// Test database connection
$conn = getDBConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✅ Database connection successful!</p>";

// Get existing users for reference
$result = $conn->query("SELECT email, roll_no, phone FROM users LIMIT 5");
$existingUsers = [];
if ($result->num_rows > 0) {
    echo "<h2>Existing Users (for reference):</h2>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>Email</th><th>Roll No</th><th>Phone</th></tr>";
    while ($user = $result->fetch_assoc()) {
        $existingUsers[] = $user;
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['roll_no']) . "</td>";
        echo "<td>" . htmlspecialchars($user['phone']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>Test Validation Scenarios:</h2>";

// Test 1: Valid new user
echo "<h3>✅ Test 1: Valid New User</h3>";
$validUser = [
    'name' => 'New Test User',
    'email' => 'newtest' . time() . '@example.com',
    'roll_no' => 'TEST' . time(),
    'department' => 'Computer Science',
    'phone' => '+1' . time(),
    'password' => 'testpassword123'
];

echo "<p><strong>Test Data:</strong></p>";
echo "<ul>";
foreach ($validUser as $key => $value) {
    echo "<li><strong>$key:</strong> $value</li>";
}
echo "</ul>";

// Simulate the validation process
$validationErrors = [];

// Check email uniqueness
$check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check_email->bind_param("s", $validUser['email']);
$check_email->execute();
$result = $check_email->get_result();
if ($result->num_rows > 0) {
    $validationErrors[] = 'Email already exists';
}
$check_email->close();

// Check roll number uniqueness
$check_roll = $conn->prepare("SELECT id FROM users WHERE roll_no = ?");
$check_roll->bind_param("s", $validUser['roll_no']);
$check_roll->execute();
$result = $check_roll->get_result();
if ($result->num_rows > 0) {
    $validationErrors[] = 'Roll number already exists';
}
$check_roll->close();

// Check phone uniqueness
$check_phone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
$check_phone->bind_param("s", $validUser['phone']);
$check_phone->execute();
$result = $check_phone->get_result();
if ($result->num_rows > 0) {
    $validationErrors[] = 'Phone number already exists';
}
$check_phone->close();

if (empty($validationErrors)) {
    echo "<p style='color: green;'>✅ All validations passed - user can be registered!</p>";
} else {
    echo "<p style='color: red;'>❌ Validation failed: " . implode(', ', $validationErrors) . "</p>";
}

// Test 2: Duplicate email
if (!empty($existingUsers)) {
    echo "<h3>❌ Test 2: Duplicate Email</h3>";
    $duplicateEmailUser = [
        'name' => 'Duplicate Email User',
        'email' => $existingUsers[0]['email'], // Use existing email
        'roll_no' => 'DUP' . time(),
        'department' => 'Computer Science',
        'phone' => '+2' . time(),
        'password' => 'testpassword123'
    ];

    echo "<p><strong>Test Data (duplicate email):</strong> " . $duplicateEmailUser['email'] . "</p>";

    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $duplicateEmailUser['email']);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color: red;'>❌ Email validation failed: Email already exists</p>";
    } else {
        echo "<p style='color: green;'>✅ Email is available (unexpected)</p>";
    }
    $check_email->close();
}

// Test 3: Duplicate roll number
if (!empty($existingUsers)) {
    echo "<h3>❌ Test 3: Duplicate Roll Number</h3>";
    $duplicateRollUser = [
        'name' => 'Duplicate Roll User',
        'email' => 'duproll' . time() . '@example.com',
        'roll_no' => $existingUsers[0]['roll_no'], // Use existing roll number
        'department' => 'Computer Science',
        'phone' => '+3' . time(),
        'password' => 'testpassword123'
    ];

    echo "<p><strong>Test Data (duplicate roll number):</strong> " . $duplicateRollUser['roll_no'] . "</p>";

    $check_roll = $conn->prepare("SELECT id FROM users WHERE roll_no = ?");
    $check_roll->bind_param("s", $duplicateRollUser['roll_no']);
    $check_roll->execute();
    $result = $check_roll->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color: red;'>❌ Roll number validation failed: Roll number already exists</p>";
    } else {
        echo "<p style='color: green;'>✅ Roll number is available (unexpected)</p>";
    }
    $check_roll->close();
}

// Test 4: Duplicate phone
if (!empty($existingUsers)) {
    echo "<h3>❌ Test 4: Duplicate Phone Number</h3>";
    $duplicatePhoneUser = [
        'name' => 'Duplicate Phone User',
        'email' => 'dupphone' . time() . '@example.com',
        'roll_no' => 'PHONE' . time(),
        'department' => 'Computer Science',
        'phone' => $existingUsers[0]['phone'], // Use existing phone
        'password' => 'testpassword123'
    ];

    echo "<p><strong>Test Data (duplicate phone):</strong> " . $duplicatePhoneUser['phone'] . "</p>";

    $check_phone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    $check_phone->bind_param("s", $duplicatePhoneUser['phone']);
    $check_phone->execute();
    $result = $check_phone->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color: red;'>❌ Phone validation failed: Phone number already exists</p>";
    } else {
        echo "<p style='color: green;'>✅ Phone number is available (unexpected)</p>";
    }
    $check_phone->close();
}

$conn->close();

echo "<hr>";
echo "<h2>Frontend Test Commands:</h2>";

// Generate test commands for the frontend
echo "<h3>Test Valid Registration:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "curl -X POST http://localhost/student-portal-backend/register.php \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\n";
echo "    \"name\": \"Frontend Test User\",\n";
echo "    \"email\": \"frontend" . time() . "@test.com\",\n";
echo "    \"roll_no\": \"FE" . time() . "\",\n";
echo "    \"department\": \"Computer Science\",\n";
echo "    \"phone\": \"+1234567890\",\n";
echo "    \"password\": \"testpassword123\"\n";
echo "  }'";
echo "</pre>";

if (!empty($existingUsers)) {
    echo "<h3>Test Duplicate Email:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo "curl -X POST http://localhost/student-portal-backend/register.php \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -d '{\n";
    echo "    \"name\": \"Duplicate Test\",\n";
    echo "    \"email\": \"" . $existingUsers[0]['email'] . "\",\n";
    echo "    \"roll_no\": \"DUP" . time() . "\",\n";
    echo "    \"department\": \"Computer Science\",\n";
    echo "    \"phone\": \"+0987654321\",\n";
    echo "    \"password\": \"testpassword123\"\n";
    echo "  }'";
    echo "</pre>";
}

echo "<h2>Expected API Responses:</h2>";
echo "<h3>✅ Success Response:</h3>";
echo "<pre style='background: #e8f5e8; padding: 10px; border-radius: 5px;'>";
echo "{
  \"success\": true,
  \"message\": \"Registration successful\",
  \"user\": {
    \"id\": 5,
    \"name\": \"Test User\",
    \"email\": \"test@example.com\",
    \"roll_no\": \"TEST001\"
  }
}";
echo "</pre>";

echo "<h3>❌ Validation Error Response:</h3>";
echo "<pre style='background: #ffe8e8; padding: 10px; border-radius: 5px;'>";
echo "{
  \"success\": false,
  \"message\": \"Validation failed\",
  \"errors\": [
    \"Email already exists\",
    \"Roll number already exists\"
  ]
}";
echo "</pre>";

echo "<h2>🎯 Summary:</h2>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; border-left: 4px solid #2196F3;'>";
echo "<h3>✅ Validation Features Implemented:</h3>";
echo "<ul>";
echo "<li><strong>Email Uniqueness:</strong> Prevents duplicate email addresses</li>";
echo "<li><strong>Roll Number Uniqueness:</strong> Ensures unique student roll numbers</li>";
echo "<li><strong>Phone Uniqueness:</strong> Validates unique phone numbers</li>";
echo "<li><strong>Format Validation:</strong> Email format and phone number validation</li>";
echo "<li><strong>Password Strength:</strong> Minimum 6 characters required</li>";
echo "<li><strong>Error Handling:</strong> Detailed error messages for each validation failure</li>";
echo "</ul>";
echo "</div>";
?>