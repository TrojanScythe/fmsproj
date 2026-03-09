<?php
session_start();
require "../db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder_name'])) {
    $folderName = trim($_POST['folder_name']);
    $userId = $_SESSION['user_id'];

    if (!empty($folderName)) {
        // We prepare the SQL to prevent hackers from breaking the database
        $stmt = $conn->prepare("INSERT INTO folders (user_id, folder_name) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $folderName);
        
        if ($stmt->execute()) {
            // Success! Send them back to the folder page
            header("Location: ../feat/folder.php?msg=folder_created");
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>