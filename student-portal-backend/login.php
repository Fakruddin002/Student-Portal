<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$conn = getDBConnection();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit();
}

// Validate required fields
if (!isset($input['email']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit();
}

// Sanitize input
$email = trim($conn->real_escape_string($input['email']));
$password = $input['password'];

// Get user from database
$stmt = $conn->prepare("SELECT id, name, email, roll_no, department, phone, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    exit();
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    exit();
}

// Remove password from response
unset($user['password']);

// Update last login time
$update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
$update_stmt->bind_param("i", $user['id']);
$update_stmt->execute();
$update_stmt->close();

echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'user' => $user
]);

$stmt->close();
$conn->close();
?>