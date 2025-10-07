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
$required_fields = ['name', 'email', 'roll_no', 'department', 'phone', 'password'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
        exit();
    }
}

// Additional validation
$name = trim($input['name']);
$email = trim($input['email']);
$roll_no = trim($input['roll_no']);
$department = trim($input['department']);
$phone = trim($input['phone']);
$password = $input['password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Validate phone number (basic validation)
if (!preg_match('/^[0-9+\-\s()]{10,15}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
    exit();
}

// Validate password length
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
    exit();
}

// Check for uniqueness
$errors = [];

// Check if email already exists
$check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$result = $check_email->get_result();
if ($result->num_rows > 0) {
    $errors[] = 'Email already exists';
}
$check_email->close();

// Check if roll number already exists
$check_roll = $conn->prepare("SELECT id FROM users WHERE roll_no = ?");
$check_roll->bind_param("s", $roll_no);
$check_roll->execute();
$result = $check_roll->get_result();
if ($result->num_rows > 0) {
    $errors[] = 'Roll number already exists';
}
$check_roll->close();

// Check if phone number already exists
$check_phone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
$check_phone->bind_param("s", $phone);
$check_phone->execute();
$result = $check_phone->get_result();
if ($result->num_rows > 0) {
    $errors[] = 'Phone number already exists';
}
$check_phone->close();

// If there are validation errors, return them
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit();
}

// Sanitize input
$name = trim($conn->real_escape_string($input['name']));
$email = trim($conn->real_escape_string($input['email']));
$roll_no = trim($conn->real_escape_string($input['roll_no']));
$department = trim($conn->real_escape_string($input['department']));
$phone = trim($conn->real_escape_string($input['phone']));
$password = password_hash($input['password'], PASSWORD_DEFAULT);

// Check if email already exists
$check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$result = $check_email->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit();
}

// Check if roll number already exists
$check_roll = $conn->prepare("SELECT id FROM users WHERE roll_no = ?");
$check_roll->bind_param("s", $roll_no);
$check_roll->execute();
$result = $check_roll->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Roll number already registered']);
    exit();
}

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, roll_no, department, phone, password, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("ssssss", $name, $email, $roll_no, $department, $phone, $password);

if ($stmt->execute()) {
    $user_id = $conn->insert_id;

    // Return user data (without password)
    $user_data = [
        'id' => $user_id,
        'name' => $name,
        'email' => $email,
        'roll_no' => $roll_no,
        'department' => $department,
        'phone' => $phone,
        'created_at' => date('Y-m-d H:i:s')
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'user' => $user_data
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>