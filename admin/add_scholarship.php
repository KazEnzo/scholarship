<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Include the database connection
require_once '../db_connect.php';

// Get form data
$scholarship_name = $_POST['scholarship_name'] ?? '';
$deadline = $_POST['deadline'] ?? '';
$amount = $_POST['amount'] ?? '';
$requirements = $_POST['requirements'] ?? '';

// Validate input
if (empty($scholarship_name) || empty($deadline) || empty($amount) || empty($requirements)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!is_numeric($amount) || $amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be a positive number']);
    exit;
}

$today = date('Y-m-d');
if ($deadline < $today) {
    echo json_encode(['success' => false, 'message' => 'Deadline must be in the future']);
    exit;
}

// Insert the scholarship into the database
$stmt = $conn->prepare("INSERT INTO scholarships (name, deadline, amount, requirements) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssds", $scholarship_name, $deadline, $amount, $requirements);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Scholarship added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add scholarship']);
}

$stmt->close();
$conn->close();
?>