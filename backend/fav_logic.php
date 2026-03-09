<?php
session_start();
require "../db.php";

if (isset($_GET['file_id'])) {
    $fileId = (int)$_GET['file_id'];
    $userId = $_SESSION['user_id'];

    // Toggle logic: 1 - current_status flips 0 to 1 and 1 to 0
    $stmt = $conn->prepare("UPDATE media SET is_favorite = 1 - is_favorite WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $fileId, $userId);
    $stmt->execute();

    // Redirect back to the same view (folder or root)
    $fid = isset($_GET['fid']) ? "?fid=" . $_GET['fid'] : "";
    header("Location: ../feat/folder.php" . $fid);
    exit;
}