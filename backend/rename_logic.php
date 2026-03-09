<?php
// Ensure NO spaces or lines exist above the <?php tag
session_start();
require "../db.php";

// Use $_REQUEST to catch both POST and GET just in case
$newName = isset($_REQUEST['new_name']) ? trim($_REQUEST['new_name']) : null;
$folderId = isset($_REQUEST['folder_id']) ? (int)$_REQUEST['folder_id'] : null;
$userId = $_SESSION['user_id'];

if ($newName && $folderId) {
    $stmt = $conn->prepare("UPDATE folders SET folder_name = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $newName, $folderId, $userId);
    
    if ($stmt->execute()) {
        // Redirect back
        header("Location: ../feat/folder.php?msg=rename_success");
        exit(); // Crucial: stops script execution
    } else {
        die("Database error: " . $conn->error);
    }
} else {
    die("Error: Missing data. Received Name: $newName, ID: $folderId");
}
?>