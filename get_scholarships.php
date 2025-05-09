<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

$stmt = $conn->prepare("SELECT id, name, deadline FROM scholarships WHERE status = 'open' AND deadline >= CURDATE()");
$stmt->execute();
$result = $stmt->get_result();

$scholarships = [];
while ($row = $result->fetch_assoc()) {
    $scholarships[] = $row;
}

echo json_encode(['success' => true, 'scholarships' => $scholarships]);

$stmt->close();
$conn->close();
?>