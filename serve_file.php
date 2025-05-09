<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

$document_path = $_GET['file'] ?? '';
error_log("serve_file.php: Requested document_path = $document_path");

$full_path = realpath($document_path); // Resolve the path relative to the root
error_log("serve_file.php: Resolved full_path = $full_path");

// Ensure the file is within the uploads directory to prevent directory traversal
$upload_dir = realpath('../uploads');
error_log("serve_file.php: upload_dir = $upload_dir");

if ($full_path && strpos($full_path, $upload_dir) === 0 && file_exists($full_path)) {
    header('Content-Type: ' . mime_content_type($full_path));
    header('Content-Disposition: inline; filename="' . basename($full_path) . '"');
    readfile($full_path);
} else {
    error_log("serve_file.php: File not found or outside uploads directory: $full_path");
    http_response_code(404);
    echo 'File not found';
}
?>