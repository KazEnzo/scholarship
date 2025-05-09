<?php
session_start();

// Log session details
error_log("Session data: " . print_r($_SESSION, true));

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    error_log("Error: User not logged in as student");
    header("Location: ../index.html");
    exit;
}

if (!isset($_SESSION['student_id'])) {
    error_log("Error: student_id not set in session");
    echo json_encode(['success' => false, 'message' => 'Session error: Student ID not found']);
    exit;
}

// Include the database connection
require_once '../db_connect.php';

// Create uploads directory if it doesn't exist
$upload_dir = "../uploads/";
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        error_log("Error: Failed to create uploads directory: $upload_dir");
        echo json_encode(['success' => false, 'message' => 'Server error: Could not create uploads directory']);
        exit;
    } else {
        error_log("Created uploads directory: $upload_dir");
    }
}

// Check if the directory is writable
if (!is_writable($upload_dir)) {
    error_log("Error: Uploads directory is not writable: $upload_dir");
    echo json_encode(['success' => false, 'message' => 'Server error: Uploads directory is not writable']);
    exit;
} else {
    error_log("Uploads directory is writable: $upload_dir");
}

// Handle file upload
$student_id = $_SESSION['student_id'];
$scholarship_id = $_POST['scholarship_id'] ?? '';
$allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
$document_path = null;

// Log POST data
error_log("POST data: " . print_r($_POST, true));

// Validate scholarship_id
if (empty($scholarship_id)) {
    error_log("Error: scholarship_id not provided");
    echo json_encode(['success' => false, 'message' => 'Please select a scholarship']);
    exit;
}

// Debug: Log all file upload details
if (isset($_FILES['document'])) {
    error_log("File upload details: " . print_r($_FILES['document'], true));
    error_log("Temporary file path: " . $_FILES['document']['tmp_name']);
    error_log("File error code: " . $_FILES['document']['error']);
} else {
    error_log("No file uploaded: \$_FILES['document'] is not set");
    echo json_encode(['success' => false, 'message' => 'A file is required']);
    exit;
}

if (isset($_FILES['document']) && $_FILES['document']['error'] == UPLOAD_ERR_OK) {
    $file = $_FILES['document'];
    $file_type = mime_content_type($file['tmp_name']);
    error_log("Detected file type: $file_type");
    
    if (in_array($file_type, $allowed_types)) {
        $file_name = uniqid() . '_' . basename($file['name']);
        $destination_path = $upload_dir . $file_name;
        
        error_log("Attempting to move file to: $destination_path");
        if (move_uploaded_file($file['tmp_name'], $destination_path)) {
            error_log("File uploaded successfully to: $destination_path");
            // Save the path relative to the project root (e.g., uploads/...)
            $document_path = "uploads/" . $file_name;
            error_log("Set document_path for database: $document_path");
        } else {
            error_log("Failed to move uploaded file to $destination_path");
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
            exit;
        }
    } else {
        error_log("Invalid file type: $file_type");
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF, JPG, and PNG are allowed']);
        exit;
    }
} else {
    $error_code = $_FILES['document']['error'] ?? 'No file';
    error_log("No file uploaded or upload error: $error_code");
    echo json_encode(['success' => false, 'message' => 'A file is required']);
    exit;
}

// Insert application into database
error_log("Inserting application: student_id=$student_id, scholarship_id=$scholarship_id, document_path=" . ($document_path ?? 'NULL'));
$stmt = $conn->prepare("INSERT INTO applications (student_id, scholarship_id, date_applied, status, document_path) VALUES (?, ?, NOW(), 'Under Review', ?)");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare statement']);
    exit;
}

$stmt->bind_param("iis", $student_id, $scholarship_id, $document_path);
if ($stmt->execute()) {
    error_log("Application inserted successfully, document_path: " . ($document_path ?? 'NULL'));
    echo json_encode(['success' => true, 'message' => 'Application submitted successfully']);
} else {
    error_log("Database insert failed: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to submit application']);
}

$stmt->close();
$conn->close();
?>