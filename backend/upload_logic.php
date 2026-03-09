<?php
session_start();
require "../db.php"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    
    // 1. Setup Paths
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/fms/uploads/media/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // 2. Get File Info
    $fileName = basename($_FILES["fileToUpload"]["name"]);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = uniqid() . "_" . $fileName;
    $targetFilePath = $targetDir . $newFileName;
    $dbPath = "/fms/uploads/media/" . $newFileName;

    // 3. Catch the Folder ID from the hidden input
    $folderId = !empty($_POST['folder_id']) ? (int)$_POST['folder_id'] : null;

    // 4. Allowed Extensions
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf', 'docx', 'txt');
    
    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFilePath)) {
            
            $userId = $_SESSION['user_id'];

            // 5. INSERT into Database
            $stmt = $conn->prepare("INSERT INTO media (user_id, folder_id, file_name, file_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $userId, $folderId, $fileName, $dbPath);
            
            if ($stmt->execute()) {
                // FIX: Check if we have a folderId to decide between ? or &
                if ($folderId) {
                    // Redirect back into the specific folder
                    header("Location: ../feat/folder.php?fid=" . $folderId . "&upload=success");
                } else {
                    // Redirect back to the main directory
                    header("Location: ../feat/folder.php?upload=success");
                }
                exit;
            } else {
                echo "Database error: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error moving file.";
        }
    } else {
        echo "Error: File type not allowed.";
    }
}
?>