<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$status = $data['status'];
$admin_id = $_SESSION['admin_id'];

$stmt = $conn->prepare("UPDATE applications SET status = ?, updated_by = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("sii", $status, $admin_id, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Status update failed']);
}

$stmt->close();
$conn->close();
?>