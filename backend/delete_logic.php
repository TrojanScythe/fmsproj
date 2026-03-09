<?php
session_start();
require "../db.php";

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

$userId = $_SESSION['user_id'];

// --- CASE 1: DELETE A SINGLE FILE ---
if (isset($_GET['delete_file'])) {
    $fileId = (int)$_GET['delete_file'];

    // 1. Get the path so we can delete the actual file from your computer
    $stmt = $conn->prepare("SELECT file_path FROM media WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $fileId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $row['file_path'];
        if (file_exists($fullPath)) {
            unlink($fullPath); // Physically deletes the file from C:/xampp/htdocs/...
        }
        
        // 2. Remove the record from the database
        $del = $conn->prepare("DELETE FROM media WHERE id = ?");
        $del->bind_param("i", $fileId);
        $del->execute();
    }
    header("Location: ../feat/folder.php?msg=file_deleted");
    exit;
}

// --- CASE 2: DELETE A FOLDER (And everything inside it) ---
if (isset($_GET['delete_folder'])) {
    $folderId = (int)$_GET['delete_folder'];

    // 1. Delete physical files belonging to this folder first
    $stmt = $conn->prepare("SELECT file_path FROM media WHERE folder_id = ?");
    $stmt->bind_param("i", $folderId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $row['file_path'];
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    // 2. Delete all file records in DB that were inside this folder
    $delFiles = $conn->prepare("DELETE FROM media WHERE folder_id = ?");
    $delFiles->bind_param("i", $folderId);
    $delFiles->execute();

    // 3. Finally, delete the folder record itself
    $delFolder = $conn->prepare("DELETE FROM folders WHERE id = ? AND user_id = ?");
    $delFolder->bind_param("ii", $folderId, $userId);
    $delFolder->execute();

    header("Location: ../feat/folder.php?msg=folder_deleted");
    exit;
}
