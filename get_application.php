<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$stmt = $conn->prepare("SELECT a.id, a.application_date, a.documents, a.status, 
                        CONCAT(s.first_name, ' ', s.last_name) AS student_name, 
                        sc.name AS scholarship_name 
                        FROM applications a 
                        JOIN students s ON a.student_id = s.id 
                        JOIN scholarships sc ON a.scholarship_id = sc.id 
                        WHERE a.status = 'pending'");
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