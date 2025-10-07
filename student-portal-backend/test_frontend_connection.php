<?php
require_once 'config.php';

echo "<h1>🔗 Frontend-Backend Connection Test</h1>";
echo "<p>This script tests if your Angular frontend can properly connect to the PHP backend.</p>";

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
}

$conn->close();

echo "<h2>🌐 API Endpoint Tests:</h2>";

// Test login endpoint
echo "<h3>1. Login API Test:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>Endpoint:</strong> <code>POST /student-portal-backend/login.php</code></p>";
echo "<p><strong>Test Data:</strong></p>";
echo "<pre style='background: #e9ecef; padding: 10px; border-radius: 3px;'>{
  \"email\": \"test@student.com\",
  \"password\": \"test123\"
}</pre>";
echo "</div>";

// Test register endpoint
echo "<h3>2. Register API Test:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>Endpoint:</strong> <code>POST /student-portal-backend/register.php</code></p>";
echo "<p><strong>Test Data:</strong></p>";
echo "<pre style='background: #e9ecef; padding: 10px; border-radius: 3px;'>{
  \"name\": \"Test User\",
  \"email\": \"test" . time() . "@example.com\",
  \"roll_no\": \"TEST" . time() . "\",
  \"department\": \"Computer Science\",
  \"phone\": \"+1234567890\",
  \"password\": \"testpassword123\"
}</pre>";
echo "</div>";

echo "<h2>🖥️ Frontend JavaScript Tests:</h2>";
echo "<p>Copy and paste these tests into your browser's developer console:</p>";

// Test 1: Basic connectivity
echo "<h3>Test 1: Basic API Connectivity</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
// JavaScript test
echo "// Test 1: Basic connectivity to backend
console.log('🔍 Testing frontend-backend connection...');

fetch('http://localhost/student-portal-backend/login.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'test@student.com',
    password: 'test123'
  })
})
.then(response => {
  console.log('📡 Response Status:', response.status);
  console.log('📡 Response Headers:', Object.fromEntries(response.headers.entries()));
  return response.text();
})
.then(text => {
  console.log('📄 Raw Response:', text);
  try {
    const data = JSON.parse(text);
    console.log('✅ Parsed JSON Response:', data);
    if (data.success) {
      console.log('🎉 LOGIN SUCCESS!');
    } else {
      console.log('❌ LOGIN FAILED:', data.message);
    }
  } catch (e) {
    console.log('❌ JSON Parse Error:', e);
    console.log('📄 Response Text:', text);
  }
})
.catch(error => {
  console.error('🚫 Network Error:', error);
  console.log('💡 Possible issues:');
  console.log('   - Backend server not running');
  console.log('   - CORS policy blocking request');
  console.log('   - Wrong API endpoint URL');
  console.log('   - Firewall blocking connection');
});";
echo "</pre>";

// Test 2: CORS headers check
echo "<h3>Test 2: CORS Headers Check</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
// JavaScript test
echo "// Test 2: Check CORS and response headers
console.log('🔍 Testing CORS and headers...');

fetch('http://localhost/student-portal-backend/login.php', {
  method: 'OPTIONS',  // Pre-flight request
  headers: {
    'Content-Type': 'application/json',
  }
})
.then(response => {
  console.log('🚀 OPTIONS Response Status:', response.status);
  console.log('🚀 OPTIONS Headers:', Object.fromEntries(response.headers.entries()));
  return response.text();
})
.then(text => {
  console.log('📄 OPTIONS Response:', text);
})
.catch(error => {
  console.log('⚠️ OPTIONS request failed (this is normal):', error);
});

// Now test actual POST request
setTimeout(() => {
  fetch('http://localhost/student-portal-backend/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      email: 'test@student.com',
      password: 'test123'
    })
  })
  .then(response => {
    console.log('📡 POST Response Status:', response.status);
    console.log('📡 POST Response Type:', response.type);
    console.log('📡 POST Headers:', Object.fromEntries(response.headers.entries()));
    return response.text();
  })
  .then(text => {
    console.log('📄 POST Response Text:', text);
  })
  .catch(error => {
    console.error('🚫 POST Error:', error);
  });
}, 1000);";
echo "</pre>";

// Test 3: Angular service simulation
echo "<h3>Test 3: Angular Service Simulation</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
// JavaScript test
echo "// Test 3: Simulate Angular HttpClient request
console.log('🔍 Simulating Angular HttpClient...');

// Simulate the exact request your Angular service makes
const apiUrl = 'http://localhost/student-portal-backend/login.php';
const loginData = {
  email: 'test@student.com',
  password: 'test123'
};

console.log('📤 Sending request to:', apiUrl);
console.log('📤 Request data:', loginData);

fetch(apiUrl, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify(loginData)
})
.then(response => {
  console.log('📡 Response received');
  console.log('📡 Status:', response.status);
  console.log('📡 Status Text:', response.statusText);
  console.log('📡 Content-Type:', response.headers.get('content-type'));

  if (!response.ok) {
    throw new Error('HTTP error! status: ' + response.status);
  }

  return response.json();
})
.then(data => {
  console.log('✅ Success Response:', data);
  if (data.success) {
    console.log('🎉 Authentication successful!');
    console.log('👤 User data:', data.user);
  } else {
    console.log('❌ Authentication failed:', data.message);
  }
})
.catch(error => {
  console.error('🚫 Request failed:', error);
  console.log('💡 Troubleshooting steps:');
  console.log('   1. Check if Apache is running');
  console.log('   2. Verify backend files are in C:\\xampp\\htdocs\\');
  console.log('   3. Check browser network tab for request details');
  console.log('   4. Test with different browser or incognito mode');
});";
echo "</pre>";

// Test 4: Network connectivity
echo "<h3>Test 4: Network Connectivity Test</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
// JavaScript test
echo "// Test 4: Basic network connectivity
console.log('🔍 Testing basic network connectivity...');

// Test 1: Can we reach the backend at all?
fetch('http://localhost/student-portal-backend/')
.then(response => {
  console.log('✅ Backend directory accessible');
  console.log('📡 Status:', response.status);
  return response.text();
})
.then(text => {
  console.log('📄 Directory listing received');
  if (text.includes('login.php')) {
    console.log('✅ login.php file found in directory');
  } else {
    console.log('❌ login.php file not found in directory');
  }
})
.catch(error => {
  console.error('🚫 Cannot reach backend directory:', error);
});

// Test 2: Can we reach the login endpoint?
setTimeout(() => {
  console.log('🔍 Testing login endpoint reachability...');
  fetch('http://localhost/student-portal-backend/login.php')
  .then(response => {
    console.log('✅ Login endpoint reachable');
    console.log('📡 Status:', response.status);
    return response.text();
  })
  .then(text => {
    console.log('📄 Login endpoint response received');
    if (text.includes('email') || text.includes('password')) {
      console.log('✅ Login endpoint responding correctly');
    } else {
      console.log('⚠️ Login endpoint response unexpected');
      console.log('📄 Response:', text.substring(0, 200) + '...');
    }
  })
  .catch(error => {
    console.error('🚫 Cannot reach login endpoint:', error);
  });
}, 500);";
echo "</pre>";

echo "<h2>🔧 Troubleshooting Guide:</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
echo "<h3>Most Common Connection Issues:</h3>";
echo "<ol>";
echo "<li><strong>Apache Not Running:</strong> Start Apache in XAMPP control panel</li>";
echo "<li><strong>Wrong File Location:</strong> Backend files must be in <code>C:\\xampp\\htdocs\\student-portal-backend\\</code></li>";
echo "<li><strong>CORS Issues:</strong> Browser blocking cross-origin requests</li>";
echo "<li><strong>Firewall:</strong> Windows Firewall blocking Apache</li>";
echo "<li><strong>Port Conflict:</strong> Another service using port 80</li>";
echo "<li><strong>Angular Proxy:</strong> Frontend not configured to proxy to backend</li>";
echo "</ol>";
echo "</div>";

echo "<h2>✅ Quick Verification Steps:</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; border-left: 4px solid #17a2b8;'>";
echo "<h3>Step-by-Step Testing:</h3>";
echo "<ol>";
echo "<li>Open browser developer tools (F12)</li>";
echo "<li>Go to Console tab</li>";
echo "<li>Copy and paste Test 1 code above</li>";
echo "<li>Check the console output for errors</li>";
echo "<li>If errors, follow the troubleshooting steps</li>";
echo "<li>Test your Angular login form</li>";
echo "<li>Check Network tab for request/response details</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🎯 Expected Results:</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
echo "<h3>When Working Correctly:</h3>";
echo "<ul>";
echo "<li>✅ Console shows: 'Response Status: 200'</li>";
echo "<li>✅ Console shows: 'Parsed JSON Response: {success: true, ...}'</li>";
echo "<li>✅ Angular login form works</li>";
echo "<li>✅ No CORS errors in console</li>";
echo "<li>✅ Network tab shows successful POST request</li>";
echo "</ul>";
echo "</div>";

echo "<h2>🚀 Quick Test:</h2>";
echo "<p>Run this in your browser console:</p>";
echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
echo "fetch('http://localhost/student-portal-backend/login.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({email: 'test@student.com', password: 'test123'})
}).then(r => r.json()).then(d => console.log('Result:', d)).catch(e => console.error('Error:', e));";
echo "</pre>";

echo "<div style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;'>";
echo "<h3>📞 Need Help?</h3>";
echo "<p>If you're still having issues:</p>";
echo "<ul>";
echo "<li>Check the browser console for detailed error messages</li>";
echo "<li>Look at the Network tab in developer tools</li>";
echo "<li>Try the tests in an incognito/private window</li>";
echo "<li>Verify all XAMPP services are running</li>";
echo "</ul>";
echo "</div>";
?>