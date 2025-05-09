<?php
session_start();

// Check if the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['student_id'])) {
    header("Location: ../index.html");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scholarship_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: student/student_dashboard.php?error=invalidrequest");
    exit;
}

$student_id = $_SESSION['student_id'];
$scholarship_id = $_POST['scholarship_id'] ?? '';
if (!$scholarship_id) {
    header("Location: student/student_dashboard.php?error=missingscholarship");
    exit;
}

// Handle file upload
if (!isset($_FILES['document']) || $_FILES['document']['error'] == UPLOAD_ERR_NO_FILE) {
    header("Location: student/student_dashboard.php?error=nodocument");
    exit;
}

$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$file_name = basename($_FILES['document']['name']);
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
if ($file_ext !== 'pdf') {
    header("Location: student/student_dashboard.php?error=invalidfiletype");
    exit;
}

$unique_file_name = uniqid() . "_" . $file_name;
$upload_path = $upload_dir . $unique_file_name;

if (!move_uploaded_file($_FILES['document']['tmp_name'], $upload_path)) {
    error_log("File upload failed: " . $_FILES['document']['error']);
    header("Location: student/student_dashboard.php?error=uploadfailed");
    exit;
}

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');
$date_applied = date('Y-m-d H:i:s');

// Insert application into database
$query = "INSERT INTO applications (student_id, scholarship_id, document_path, status, date_applied) VALUES (?, ?, ?, 'Under Review', ?)";
$stmt = $conn->prepare($query);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("iiss", $student_id, $scholarship_id, $upload_path, $date_applied);
if (!$stmt->execute()) {
    error_log("Insert failed: " . $stmt->error);
    header("Location: student/student_dashboard.php?error=applicationfailed");
    exit;
}

$stmt->close();
$conn->close();

// Redirect back to dashboard with success message
header("Location: student/student_dashboard.php?success=applicationreceived");
exit;
?>