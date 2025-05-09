<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scholarship_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    header("Location: index.html?error=databaseerror");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.html?error=invalidrequest");
    exit;
}

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
    header("Location: index.html?error=missingfields");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: index.html?error=invalidemail");
    exit;
}

if (strlen($password) < 6) {
    header("Location: index.html?error=passwordlength");
    exit;
}

if ($password !== $confirm_password) {
    header("Location: index.html?error=passwordmismatch");
    exit;
}

$check_query = "SELECT id FROM students WHERE email = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
if ($check_result->num_rows > 0) {
    $check_stmt->close();
    $conn->close();
    header("Location: index.html?error=emailexists");
    exit;
}
$check_stmt->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$insert_query = "INSERT INTO students (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_query);
$insert_stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

if ($insert_stmt->execute()) {
    $insert_stmt->close();
    $conn->close();
    header("Location: index.html?success=registration_success");
    exit;
} else {
    error_log("Insert failed in register.php: " . $insert_stmt->error);
    $insert_stmt->close();
    $conn->close();
    header("Location: index.html?error=registrationfailed");
    exit;
}
?>