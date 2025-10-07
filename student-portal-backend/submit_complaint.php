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
$required_fields = ['title', 'category', 'description'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
        exit();
    }
}

// Get student_id from input (should be sent from frontend)
$student_id = isset($input['student_id']) ? (int)$input['student_id'] : null;

if (!$student_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit();
}

// Verify student exists
$check_student = $conn->prepare("SELECT id FROM users WHERE id = ?");
$check_student->bind_param("i", $student_id);
$check_student->execute();
$result = $check_student->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit();
}
$check_student->close();

// Sanitize input
$title = trim($conn->real_escape_string($input['title']));
$category = trim($conn->real_escape_string($input['category']));
$description = trim($conn->real_escape_string($input['description']));

// Insert complaint
$stmt = $conn->prepare("INSERT INTO complaints (student_id, title, category, description, status, created_at) VALUES (?, ?, ?, ?, 'Pending', NOW())");
$stmt->bind_param("isss", $student_id, $title, $category, $description);

if ($stmt->execute()) {
    $complaint_id = $conn->insert_id;

    // Get the inserted complaint data
    $complaint_data = [
        'id' => $complaint_id,
        'student_id' => $student_id,
        'title' => $title,
        'category' => $category,
        'description' => $description,
        'status' => 'Pending',
        'created_at' => date('Y-m-d H:i:s')
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Complaint submitted successfully',
        'complaint' => $complaint_data
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to submit complaint: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>