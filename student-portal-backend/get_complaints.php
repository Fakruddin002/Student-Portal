<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$conn = getDBConnection();

// Get student_id from query parameters
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;

try {
    if ($student_id) {
        // Get complaints for specific student
        $stmt = $conn->prepare("
            SELECT c.id, c.title, c.category, c.description, c.status, c.created_at,
                   u.name as student_name, u.roll_no
            FROM complaints c
            JOIN users u ON c.student_id = u.id
            WHERE c.student_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->bind_param("i", $student_id);
    } else {
        // Get all complaints (for admin view)
        $stmt = $conn->prepare("
            SELECT c.id, c.title, c.category, c.description, c.status, c.created_at,
                   u.name as student_name, u.roll_no
            FROM complaints c
            JOIN users u ON c.student_id = u.id
            ORDER BY c.created_at DESC
        ");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $complaints = [];
    while ($row = $result->fetch_assoc()) {
        $complaints[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'category' => $row['category'],
            'description' => $row['description'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'student_name' => $row['student_name'],
            'roll_no' => $row['roll_no']
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Complaints retrieved successfully',
        'complaints' => $complaints,
        'total' => count($complaints)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to retrieve complaints: ' . $e->getMessage()
    ]);
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>