<?php
require_once 'config.php';

echo "<h1>🔐 Direct Login API Test</h1>";
echo "<p>This test directly calls the login API to verify it's working.</p>";

// Test database connection
$conn = getDBConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✅ Database connection successful!</p>";

// Get test user
$result = $conn->query("SELECT id, name, email FROM users WHERE email = 'test@student.com' LIMIT 1");
$testUser = null;
if ($result->num_rows > 0) {
    $testUser = $result->fetch_assoc();
    echo "<p style='color: green;'>✅ Test user found: " . htmlspecialchars($testUser['name']) . "</p>";
} else {
    echo "<p style='color: red;'>❌ Test user not found. Please run reset_test_user.php first.</p>";
    $conn->close();
    exit();
}

$conn->close();

echo "<h2>🧪 Login API Test</h2>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>Test 1: Valid Credentials</h3>";
echo "<p><strong>Testing with:</strong></p>";
echo "<ul>";
echo "<li><strong>Email:</strong> test@student.com</li>";
echo "<li><strong>Password:</strong> test123</li>";
echo "</ul>";

// Simulate the login API call
$testData = [
    'email' => 'test@student.com',
    'password' => 'test123'
];

echo "<h4>📤 Request Data:</h4>";
echo "<pre style='background: #e9ecef; padding: 10px; border-radius: 3px;'>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";

// Make the API call
$ch = curl_init('http://localhost/student-portal-backend/login.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "<h4>📡 Response:</h4>";
echo "<p><strong>HTTP Status:</strong> $httpCode</p>";

if ($curlError) {
    echo "<p style='color: red;'><strong>cURL Error:</strong> $curlError</p>";
} else {
    echo "<p><strong>Raw Response:</strong></p>";
    echo "<pre style='background: #e9ecef; padding: 10px; border-radius: 3px;'>" . htmlspecialchars($response) . "</pre>";

    // Try to parse JSON
    $jsonResponse = json_decode($response, true);
    if ($jsonResponse) {
        echo "<p><strong>Parsed JSON:</strong></p>";
        echo "<pre style='background: #d4edda; padding: 10px; border-radius: 3px; color: #155724;'>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";

        if (isset($jsonResponse['success']) && $jsonResponse['success']) {
            echo "<p style='color: green; font-weight: bold;'>✅ LOGIN SUCCESS! API is working correctly.</p>";
            echo "<p><strong>User:</strong> " . htmlspecialchars($jsonResponse['user']['name']) . "</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ LOGIN FAILED: " . htmlspecialchars($jsonResponse['message'] ?? 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color: red;'><strong>JSON Parse Error:</strong> Response is not valid JSON</p>";
    }
}

echo "</div>";

echo "<h2>🔍 Troubleshooting</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";

if ($httpCode === 0) {
    echo "<h3>🚫 Connection Issues:</h3>";
    echo "<ul>";
    echo "<li>Apache/XAMPP not running</li>";
    echo "<li>Backend files not in correct location</li>";
    echo "<li>Firewall blocking connection</li>";
    echo "</ul>";
} elseif ($httpCode === 404) {
    echo "<h3>📁 File Not Found:</h3>";
    echo "<ul>";
    echo "<li>login.php file missing</li>";
    echo "<li>Wrong file path</li>";
    echo "<li>Apache configuration issue</li>";
    echo "</ul>";
} elseif ($httpCode === 500) {
    echo "<h3>💥 Server Error:</h3>";
    echo "<ul>";
    echo "<li>Database connection failed</li>";
    echo "<li>PHP syntax error</li>";
    echo "<li>File permission issues</li>";
    echo "</ul>";
} elseif ($httpCode === 200 && isset($jsonResponse) && !$jsonResponse['success']) {
    echo "<h3>🔐 Authentication Issues:</h3>";
    echo "<ul>";
    echo "<li>Wrong password</li>";
    echo "<li>User not found</li>";
    echo "<li>Password hash mismatch</li>";
    echo "</ul>";
} else {
    echo "<h3>✅ Everything Looks Good!</h3>";
    echo "<p>If the API test above shows success, then the backend is working correctly.</p>";
    echo "<p>The issue might be with the Angular frontend configuration.</p>";
}

echo "</div>";

echo "<h2>🎯 Next Steps</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; border-left: 4px solid #17a2b8;'>";

if (isset($jsonResponse) && isset($jsonResponse['success']) && $jsonResponse['success']) {
    echo "<h3>✅ Backend Working - Frontend Issue</h3>";
    echo "<p>The login API is working correctly! The issue is likely with:</p>";
    echo "<ul>";
    echo "<li>Angular HttpClient configuration</li>";
    echo "<li>CORS policy</li>";
    echo "<li>Frontend request headers</li>";
    echo "<li>Network/firewall blocking frontend requests</li>";
    echo "</ul>";
    echo "<p><strong>Solution:</strong> Check the frontend connection test at <code>test_frontend_connection.php</code></p>";
} else {
    echo "<h3>🔧 Backend Issues Detected</h3>";
    echo "<p>Please fix the backend issues shown above before testing the frontend.</p>";
}

echo "</div>";
?>