<?php
require_once 'config.php';

echo "<h1>🔍 Complete Login Diagnosis</h1>";
echo "<p>This script diagnoses every aspect of the login process.</p>";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
$conn = getDBConnection();
if ($conn) {
    echo "<p style='color: green;'>✅ Database connection: SUCCESS</p>";

    // Test table existence
    $tables = ['users', 'complaints'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' not found</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ Database connection: FAILED</p>";
    echo "<p>Check XAMPP MySQL is running and credentials are correct.</p>";
    exit();
}

// Test 2: Check for any users
echo "<h2>2. Users in Database</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result) {
    $count = $result->fetch_assoc()['count'];
    echo "<p>Total users in database: <strong>$count</strong></p>";

    if ($count == 0) {
        echo "<p style='color: red;'>❌ No users found! Please register some users first.</p>";
        echo "<p><a href='reset_test_user.php'>Click here to create a test user</a></p>";
    } else {
        echo "<p style='color: green;'>✅ Users found in database</p>";
    }
}

// Test 3: Show all users with details
echo "<h2>3. All Users Details</h2>";
$result = $conn->query("SELECT id, name, email, roll_no, password FROM users ORDER BY id");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Name</th><th>Email</th><th>Roll No</th><th>Password Hash</th><th>Test Login</th>";
    echo "</tr>";

    while ($user = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['roll_no']) . "</td>";
        echo "<td style='font-family: monospace; font-size: 11px; max-width: 150px; overflow: hidden;'>" . htmlspecialchars(substr($user['password'], 0, 20)) . "...</td>";
        echo "<td><button onclick=\"testSpecificLogin('" . $user['email'] . "')\">Test</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No users found in database</p>";
}

// Test 4: Test login API simulation
echo "<h2>4. Login API Simulation</h2>";
echo "<p>Testing the exact same logic as the login.php API:</p>";

// Get first user for testing
$result = $conn->query("SELECT id, name, email, roll_no, department, phone, password FROM users LIMIT 1");
if ($result->num_rows > 0) {
    $testUser = $result->fetch_assoc();
    $testEmail = $testUser['email'];
    $testPassword = "test123"; // Assume this is the password

    echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>Testing User: " . htmlspecialchars($testUser['name']) . "</h3>";
    echo "<p><strong>Test Email:</strong> $testEmail</p>";
    echo "<p><strong>Test Password:</strong> $testPassword</p>";
    echo "</div>";

    // Simulate the login process
    $stmt = $conn->prepare("SELECT id, name, email, roll_no, department, phone, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $testEmail);
    $stmt->execute();
    $loginResult = $stmt->get_result();

    if ($loginResult->num_rows > 0) {
        $dbUser = $loginResult->fetch_assoc();
        echo "<p style='color: green;'>✅ Step 1 - User found in database</p>";

        // Test password verification
        if (password_verify($testPassword, $dbUser['password'])) {
            echo "<p style='color: green;'>✅ Step 2 - Password verification: SUCCESS</p>";

            // Remove password from response
            unset($dbUser['password']);

            echo "<p style='color: green;'>✅ Step 3 - Login simulation: SUCCESS</p>";
            echo "<p><strong>API would return:</strong></p>";
            echo "<pre style='background: #e8f5e8; padding: 10px; border-radius: 5px;'>";
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => $dbUser
            ], JSON_PRETTY_PRINT);
            echo "</pre>";

        } else {
            echo "<p style='color: red;'>❌ Step 2 - Password verification: FAILED</p>";
            echo "<p><strong>Issue:</strong> The password hash doesn't match the test password.</p>";
            echo "<p><strong>Stored hash:</strong> " . htmlspecialchars($dbUser['password']) . "</p>";
            echo "<p><strong>Test password:</strong> $testPassword</p>";

            // Try common passwords
            echo "<h4>Trying Common Passwords:</h4>";
            $commonPasswords = ['password123', 'admin123', 'test123', '123456', 'password'];
            foreach ($commonPasswords as $pwd) {
                if (password_verify($pwd, $dbUser['password'])) {
                    echo "<p style='color: green; font-weight: bold;'>✅ Found matching password: <strong>$pwd</strong></p>";
                    break;
                }
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Step 1 - User not found in database</p>";
        echo "<p><strong>Issue:</strong> No user found with email: $testEmail</p>";
    }

    $stmt->close();
} else {
    echo "<p style='color: orange;'>⚠️ No users available for testing</p>";
}

// Test 5: CORS and API accessibility
echo "<h2>5. API Accessibility Test</h2>";
echo "<p>Test if the login API is accessible:</p>";

echo "<h3>Direct API Test:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
// Show curl command
if (isset($testEmail)) {
    echo "curl -X POST http://localhost/student-portal-backend/login.php \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -d '{\n";
    echo "    \"email\": \"$testEmail\",\n";
    echo "    \"password\": \"test123\"\n";
    echo "  }'";
} else {
    echo "curl -X POST http://localhost/student-portal-backend/login.php \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -d '{\n";
    echo "    \"email\": \"test@example.com\",\n";
    echo "    \"password\": \"test123\"\n";
    echo "  }'";
}
echo "</pre>";

echo "<h3>JavaScript Test (Browser Console):</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "// Test login API from browser console
fetch('http://localhost/student-portal-backend/login.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: '" . ($testEmail ?? 'test@example.com') . "',
    password: 'test123'
  })
})
.then(response => response.json())
.then(data => console.log('Login response:', data))
.catch(error => console.error('Login error:', error));";
echo "</pre>";

$conn->close();

echo "<h2>6. Quick Diagnosis Summary</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
echo "<h3>🔍 Most Common Issues:</h3>";
echo "<ol>";
echo "<li><strong>No Users:</strong> Database is empty - run registration first</li>";
echo "<li><strong>Wrong Password:</strong> Password used during registration doesn't match test password</li>";
echo "<li><strong>Case Sensitivity:</strong> Email case must match exactly</li>";
echo "<li><strong>Database Issues:</strong> Tables don't exist or connection failed</li>";
echo "<li><strong>API Issues:</strong> Frontend can't reach backend API</li>";
echo "</ol>";
echo "</div>";

echo "<h2>7. Immediate Solutions</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; border-left: 4px solid #17a2b8;'>";
echo "<h3>🚀 Quick Fixes:</h3>";
echo "<ol>";
echo "<li><a href='reset_test_user.php'>Create Test User</a> - Get reliable test credentials</li>";
echo "<li><a href='test_db.php'>Check Database</a> - Verify connection and data</li>";
echo "<li><a href='test_cors.php'>Test Connectivity</a> - Check frontend-backend connection</li>";
echo "<li>Run the curl command above to test API directly</li>";
echo "<li>Use browser console to test JavaScript API calls</li>";
echo "</ol>";
echo "</div>";

echo "<script>
// JavaScript function for testing specific user login
function testSpecificLogin(email) {
  fetch('http://localhost/student-portal-backend/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: email,
      password: 'test123'  // Try common password
    })
  })
  .then(response => response.json())
  .then(data => {
    alert('Login Test Result for ' + email + ':\\n\\n' + JSON.stringify(data, null, 2));
    console.log('Login test result:', data);
  })
  .catch(error => {
    alert('Login Test Error for ' + email + ':\\n\\n' + error);
    console.error('Login test error:', error);
  });
}

// Auto-test function
function runAutoTest() {
  console.log('🔍 Running automatic login diagnosis...');

  // Test basic connectivity
  fetch('http://localhost/student-portal-backend/login.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: 'test@example.com', password: 'test123' })
  })
  .then(response => {
    console.log('✅ API Response Status:', response.status);
    return response.json();
  })
  .then(data => {
    console.log('📋 API Response Data:', data);
    if (data.success) {
      console.log('🎉 Login test PASSED');
    } else {
      console.log('❌ Login test FAILED:', data.message);
    }
  })
  .catch(error => {
    console.error('🚫 Connection test FAILED:', error);
  });
}

// Run auto test on page load
runAutoTest();
</script>";

echo "<div style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;'>";
echo "<button onclick='runAutoTest()' style='padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;'>🔄 Run Auto Test</button>";
echo "<p style='margin-top: 10px; font-size: 14px; color: #666;'>Click to run automatic connectivity and login tests</p>";
echo "</div>";
?>