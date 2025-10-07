<?php
require_once 'config.php';

echo "<h1>Student Portal - Login Test</h1>";
echo "<p>This script tests the login functionality with registered users.</p>";

// Test database connection
$conn = getDBConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✅ Database connection successful!</p>";

// Get all users from database
$result = $conn->query("SELECT id, name, email, roll_no, password FROM users ORDER BY created_at DESC LIMIT 10");

echo "<h2>Registered Users in Database:</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Name</th><th>Email</th><th>Roll No</th><th>Password Hash</th><th>Test Login</th>";
    echo "</tr>";

    $testUsers = [];
    while ($row = $result->fetch_assoc()) {
        $testUsers[] = $row;
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['roll_no']) . "</td>";
        echo "<td style='font-family: monospace; font-size: 12px; max-width: 200px; overflow: hidden;'>" . htmlspecialchars(substr($row['password'], 0, 20)) . "...</td>";
        echo "<td><button onclick=\"testLogin('" . $row['email'] . "', 'password123')\">Test</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ No users found in database. Please register some users first.</p>";
    echo "<p><a href='test_registration.php'>Click here to create a test user</a></p>";
}

echo "<h2>Login Test with Known Password:</h2>";
echo "<p>Testing login with password: <strong>password123</strong></p>";

// Test login for each user
foreach ($testUsers as $user) {
    echo "<h3>Testing User: " . htmlspecialchars($user['name']) . " (" . htmlspecialchars($user['email']) . ")</h3>";

    // Test the same login logic as the API
    $stmt = $conn->prepare("SELECT id, name, email, roll_no, department, phone, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $user['email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $dbUser = $result->fetch_assoc();
        echo "<p style='color: green;'>✅ User found in database</p>";

        // Test password verification
        $testPassword = "password123"; // Assuming this is the password used during registration
        if (password_verify($testPassword, $dbUser['password'])) {
            echo "<p style='color: green;'>✅ Password verification: SUCCESS</p>";

            // Remove password from response
            unset($dbUser['password']);

            echo "<p><strong>Login would return:</strong></p>";
            echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => $dbUser
            ], JSON_PRETTY_PRINT);
            echo "</pre>";

        } else {
            echo "<p style='color: red;'>❌ Password verification: FAILED</p>";
            echo "<p>This means the password hash doesn't match the test password.</p>";
            echo "<p><strong>Stored hash:</strong> " . htmlspecialchars($dbUser['password']) . "</p>";
            echo "<p><strong>Test password:</strong> $testPassword</p>";

            // Try to find what password would work
            echo "<p><strong>Troubleshooting:</strong></p>";
            echo "<ul>";
            echo "<li>Check if the user was registered with a different password</li>";
            echo "<li>Try using the password generator to create a new test user</li>";
            echo "<li>Verify the password hashing is working correctly</li>";
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>❌ User not found in database</p>";
    }

    $stmt->close();
    echo "<hr>";
}

$conn->close();

echo "<h2>API Test Commands:</h2>";

// Generate API test commands for each user
foreach ($testUsers as $user) {
    echo "<h3>Test Login for " . htmlspecialchars($user['name']) . ":</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo "curl -X POST http://localhost/student-portal-backend/login.php \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -d '{\n";
    echo "    \"email\": \"" . $user['email'] . "\",\n";
    echo "    \"password\": \"password123\"\n";
    echo "  }'";
    echo "</pre>";
}

echo "<h2>JavaScript Test Code:</h2>";
echo "<p>Copy and paste this into your browser's developer console:</p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
// JavaScript test code
echo "// Test login API
async function testLoginAPI(email, password) {
  try {
    const response = await fetch('http://localhost/student-portal-backend/login.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: email,
        password: password
      })
    });
    const data = await response.json();
    console.log('Login response:', data);
    return data;
  } catch (error) {
    console.error('Login error:', error);
  }
}

// Test with first user
testLoginAPI('" . ($testUsers[0]['email'] ?? 'user@example.com') . "', 'password123');";
echo "</pre>";

echo "<h2>Common Login Issues:</h2>";
echo "<ol>";
echo "<li><strong>Wrong Password:</strong> Make sure you're using the correct password that was used during registration</li>";
echo "<li><strong>Email Case Sensitivity:</strong> Check if the email case matches exactly</li>";
echo "<li><strong>Database Issues:</strong> Verify the user exists in the database</li>";
echo "<li><strong>Password Hashing:</strong> Ensure password_verify() is working correctly</li>";
echo "<li><strong>API Connection:</strong> Check if the frontend can reach the backend</li>";
echo "</ol>";

echo "<h2>Quick Solutions:</h2>";
echo "<ul>";
echo "<li><a href='test_registration.php'>Create a new test user</a> with known password</li>";
echo "<li><a href='password_generator.php'>Generate password hashes</a> for testing</li>";
echo "<li><a href='test_db.php'>Check database contents</a> and user data</li>";
echo "<li><a href='test_cors.php'>Test connectivity</a> between frontend and backend</li>";
echo "</ul>";

echo "<script>
// JavaScript function for testing login
function testLogin(email, password) {
  fetch('http://localhost/student-portal-backend/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: email,
      password: password || 'password123'
    })
  })
  .then(response => response.json())
  .then(data => {
    alert('Login Test Result:\\n' + JSON.stringify(data, null, 2));
    console.log('Login test result:', data);
  })
  .catch(error => {
    alert('Login Test Error:\\n' + error);
    console.error('Login test error:', error);
  });
}
</script>";
?>