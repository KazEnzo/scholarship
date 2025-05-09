<?php
session_start();
session_destroy();
header("Location: index.html");
echo json_encode(['success' => true]);
?>