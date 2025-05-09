<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT a.application_date, a.status, s.name AS scholarship_name 
                        FROM applications a 
                        JOIN scholarships s ON a.scholarship_id = s.id 
                        WHERE a.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$applications = [];
while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}

echo json_encode(['success' => true, 'applications' => $applications]);

$stmt->close();
$conn->close();
?>