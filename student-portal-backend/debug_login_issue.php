<?php
require_once 'config.php';

echo "<h1>🔍 Login Issue Debugger</h1>";
echo "<p>This script helps identify why login is failing for registered users.</p>";

// Test database connection
$conn = getDBConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✅ Database connection successful!</p>";

// Get all registered users
$result = $conn->query("SELECT id, name, email, roll_no, password, created_at FROM users ORDER BY created_at DESC");
$users = [];

echo "<h2>📋 All Registered Users:</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Name</th><th>Email</th><th>Roll No</th><th>Password Hash</th><th>Created</th><th>Test Login</th>";
    echo "</tr>";

    while ($user = $result->fetch_assoc()) {
        $users[] = $user;
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['roll_no']) . "</td>";
        echo "<td style='font-family: monospace; font-size: 11px; max-width: 150px; overflow: hidden;'>" . htmlspecialchars(substr($user['password'], 0, 20)) . "...</td>";
        echo "<td>" . $user['created_at'] . "</td>";
        echo "<td><button onclick=\"testUserLogin('" . $user['email'] . "', '" . $user['id'] . "')\">Test</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No users found in database. Please register some users first.</p>";
    echo "<p><a href='reset_test_user.php'>Click here to create a test user</a></p>";
    exit();
}

echo "<h2>🔐 Password Hash Analysis:</h2>";

// Analyze password hashes
foreach ($users as $user) {
    echo "<h3>User: " . htmlspecialchars($user['name']) . " (" . htmlspecialchars($user['email']) . ")</h3>";
    echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "<p><strong>Password Hash:</strong> <code>" . htmlspecialchars($user['password']) . "</code></p>";

    // Test common passwords
    $commonPasswords = ['password', 'password123', 'test123', 'admin123', '123456'];

    echo "<p><strong>Testing Common Passwords:</strong></p>";
    echo "<ul>";
    $found = false;
    foreach ($commonPasswords as $pwd) {
        if (password_verify($pwd, $user['password'])) {
            echo "<li style='color: green; font-weight: bold;'>✅ Found matching password: <strong>$pwd</strong></li>";
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "<li style='color: orange;'>⚠️ Password doesn't match common passwords</li>";
        echo "<li><strong>Possible issues:</strong></li>";
        echo "<li>- Password was set during registration with a different value</li>";
        echo "<li>- Password hashing algorithm mismatch</li>";
        echo "<li>- Special characters or encoding issues</li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "<h2>🧪 Login API Simulation:</h2>";
echo "<p>Testing the exact login process used by the frontend:</p>";

// Test login for the most recent user
if (!empty($users)) {
    $testUser = $users[0]; // Most recent user
    $testEmail = $testUser['email'];
    $testPassword = "password"; // Try the most common password

    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; border-left: 4px solid #2196F3;'>";
    echo "<h3>Testing Login for: " . htmlspecialchars($testUser['name']) . "</h3>";
    echo "<p><strong>Email:</strong> $testEmail</p>";
    echo "<p><strong>Test Password:</strong> $testPassword</p>";
    echo "</div>";

    // Simulate the exact login process from login.php
    $stmt = $conn->prepare("SELECT id, name, email, roll_no, department, phone, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $testEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<p style='color: green;'>✅ Step 1: User found in database</p>";

        // Test password verification
        if (password_verify($testPassword, $user['password'])) {
            echo "<p style='color: green;'>✅ Step 2: Password verification SUCCESS</p>";

            // Remove password from response (like the real API)
            unset($user['password']);

            echo "<p style='color: green;'>✅ Step 3: Login simulation SUCCESS</p>";
            echo "<p><strong>Expected API Response:</strong></p>";
            echo "<pre style='background: #e8f5e8; padding: 10px; border-radius: 5px;'>";
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user
            ], JSON_PRETTY_PRINT);
            echo "</pre>";

        } else {
            echo "<p style='color: red;'>❌ Step 2: Password verification FAILED</p>";
            echo "<p><strong>Issue Identified:</strong> Password hash doesn't match the test password.</p>";
            echo "<p><strong>Stored Hash:</strong> " . htmlspecialchars($user['password']) . "</p>";
            echo "<p><strong>Test Password:</strong> $testPassword</p>";

            // Try to find what password would work
            echo "<h4>Trying to Find Correct Password:</h4>";
            $possiblePasswords = ['password', 'password123', 'test123', 'admin123', '123456', 'student123'];

            echo "<ul>";
            foreach ($possiblePasswords as $pwd) {
                if (password_verify($pwd, $user['password'])) {
                    echo "<li style='color: green; font-weight: bold;'>🎯 FOUND: Correct password is <strong>'$pwd'</strong></li>";
                    echo "<p style='color: green; font-weight: bold;'>Use this password to login: <code>$pwd</code></p>";
                    break;
                }
            }
            echo "</ul>";

            if (!password_verify('password', $user['password']) &&
                !password_verify('password123', $user['password']) &&
                !password_verify('test123', $user['password'])) {
                echo "<p style='color: red; font-weight: bold;'>❌ Could not find matching password</p>";
                echo "<p>This suggests the password was set with special characters or a different value during registration.</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Step 1: User not found in database</p>";
        echo "<p><strong>Issue:</strong> Email '$testEmail' not found in users table</p>";
    }

    $stmt->close();
}

$conn->close();

echo "<h2>🚀 Quick Solutions:</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
echo "<h3>Most Common Login Issues:</h3>";
echo "<ol>";
echo "<li><strong>Wrong Password:</strong> Use the password you entered during registration</li>";
echo "<li><strong>Case Sensitive:</strong> Email must match exactly (check uppercase/lowercase)</li>";
echo "<li><strong>Registration Issue:</strong> User might not have been saved properly</li>";
echo "<li><strong>Password Hashing:</strong> Different algorithms between registration and login</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🛠️ Testing Tools:</h2>";
echo "<ul>";
echo "<li><a href='test_login.php'>Complete Login Test</a> - Test all users</li>";
echo "<li><a href='diagnose_login.php'>Login Diagnosis</a> - Step-by-step analysis</li>";
echo "<li><a href='reset_test_user.php'>Create Test User</a> - Known credentials</li>";
echo "</ul>";

echo "<h2>📱 Frontend Testing:</h2>";
echo "<p>Test login in your Angular app with the credentials found above.</p>";

echo "<h3>Browser Console Test:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
// JavaScript test
if (!empty($users)) {
    $testUser = $users[0];
    echo "// Test login API
fetch('http://localhost/student-portal-backend/login.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: '" . $testUser['email'] . "',
    password: 'password'  // Try this first
  })
})
.then(response => response.json())
.then(data => console.log('Login result:', data))
.catch(error => console.error('Login error:', error));";
}
echo "</pre>";

echo "<script>
// JavaScript function for testing specific user login
function testUserLogin(email, userId) {
  // Try common passwords
  const passwords = ['password', 'password123', 'test123', 'admin123'];

  passwords.forEach(password => {
    fetch('http://localhost/student-portal-backend/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email, password: password })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('✅ LOGIN SUCCESS for ' + email + ' with password: ' + password);
        console.log('Login success:', data);
      } else {
        console.log('Login failed for ' + email + ' with ' + password + ':', data.message);
      }
    })
    .catch(error => {
      console.error('Login error for ' + email + ':', error);
    });
  });

  alert('Testing login for ' + email + ' with common passwords. Check console for results.');
}
</script>";

echo "<div style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;'>";
echo "<button onclick=\"testUserLogin('" . ($users[0]['email'] ?? 'test@example.com') . "', '" . ($users[0]['id'] ?? '1') . "')\" style='padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;'>🔍 Test All Passwords</button>";
echo "<p style='margin-top: 10px; font-size: 14px; color: #666;'>Click to test login with common passwords for the most recent user</p>";
echo "</div>";
?>