<?php
session_start();
require "../db.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['file_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$file_id = (int)$_GET['file_id'];

// 1. Check if the link already exists
$check = $conn->query("SELECT id FROM favorites WHERE user_id = $user_id AND file_id = $file_id");

if ($check->num_rows > 0) {
    // 2. If it exists, DELETE it (Unstar)
    $conn->query("DELETE FROM favorites WHERE user_id = $user_id AND file_id = $file_id");
} else {
    // 3. If it doesn't, INSERT it (Star)
    $conn->query("INSERT INTO favorites (user_id, file_id) VALUES ($user_id, $file_id)");
}

// 4. Send them back to the gallery they were looking at
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;