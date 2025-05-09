<?php
session_start();

$conn = new mysqli("localhost", "root", "", "scholarship_db");
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.html?error=invalidrequest");
    exit;
}

$role = $_POST['role'] ?? '';
$identifier = $role === "student" ? ($_POST['email'] ?? '') : ($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
error_log("Login attempt: role=$role, identifier=$identifier, password='$password'");

if (!$role || !$identifier || !$password) {
    header("Location: index.html?error=missingfields");
    exit;
}

$table = $role === "student" ? "students" : ($role === "admin" ? "admins" : null);
if (!$table) {
    error_log("Invalid role: $role");
    header("Location: index.html?error=invalidrole");
    exit;
}

$field = $role === "student" ? "email" : "username";
$select_fields = $role === "student" ? "id, email, password, first_name, last_name" : "id, username, password, name";
$query = "SELECT $select_fields FROM $table WHERE $field = ?";
error_log("Executing query: $query with $field=$identifier");

$stmt = $conn->prepare($query);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("No user found for $field: $identifier");
    $stmt->close();
    header("Location: index.html?error=usernotfound");
    exit;
}

$row = $result->fetch_assoc();
if (!password_verify($password, $row['password'])) {
    error_log("Password verification failed for $field: $identifier");
    $stmt->close();
    header("Location: index.html?error=invalidcredentials");
    exit;
}

$_SESSION['role'] = $role;
if ($role === "student") {
    $_SESSION['student_id'] = $row['id'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['name'] = $row['first_name'] . ' ' . $row['last_name'];
    $redirect = "student/student_dashboard.php";
} else {
    $_SESSION['admin_id'] = $row['id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['name'] = $row['name'];
    $redirect = "admin/admin_dashboard.php";
}

$stmt->close();
$conn->close();

header("Location: $redirect");
exit;
?>