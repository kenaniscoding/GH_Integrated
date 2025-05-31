<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "onelasalle", "db3");

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get the subject from the request
$subject = isset($_GET['subject']) ? trim($_GET['subject']) : '';

if (empty($subject)) {
    echo json_encode(['error' => 'Subject is required']);
    exit;
}

// Prepare and execute query to get teachers for the selected subject
$stmt = $conn->prepare("SELECT DISTINCT teacher_name FROM teachers WHERE subject = ? ORDER BY teacher_name");
$stmt->bind_param("s", $subject);
$stmt->execute();
$result = $stmt->get_result();

$teachers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row['teacher_name'];
    }
}

// Return JSON response
echo json_encode(['teachers' => $teachers]);

$stmt->close();
$conn->close();
?>