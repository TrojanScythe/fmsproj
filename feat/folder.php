<?php
session_start();
require "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// 1. Get the current Folder ID from the URL (e.g., folder.php?fid=5)
$current_folder_id = isset($_GET['fid']) ? (int)$_GET['fid'] : null;

// 2. Fetch ALL Folders (This shows your directories)
$folder_query = "SELECT * FROM folders ORDER BY folder_name ASC";
$folders_result = $conn->query($folder_query);

// 3. Fetch Files filtered by the current folder
if ($current_folder_id) {
    // Show files INSIDE the selected folder
    $file_query = "SELECT media.*, users.full_name 
                   FROM media 
                   JOIN users ON media.user_id = users.id 
                   WHERE media.folder_id = $current_folder_id 
                   ORDER BY upload_date DESC";
} else {
    // Show only "Root" files (files that aren't in any folder)
    $file_query = "SELECT media.*, users.full_name 
                   FROM media 
                   JOIN users ON media.user_id = users.id 
                   WHERE media.folder_id IS NULL OR media.folder_id = 0
                   ORDER BY upload_date DESC";
}

$files_result = $conn->query($file_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Directory Explorer</title>
    <link rel="stylesheet" type="text/css" href="/fms/cssshi/dashboard.css">
    <link rel="stylesheet" type="text/css" href="/fms/feat/css/folder.css">
    <style>
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); }
        .modal-content { background: white; margin: 15% auto; padding: 25px; border-radius: 15px; width: 320px; text-align: center; color: #333; }
        .modal-content input { width: 80%; padding: 10px; margin: 15px 0; border: 1px solid #ddd; border-radius: 8px; }
        .confirm-btn { background: #7016d2; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .cancel-btn { background: #ccc; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-right: 5px; }
        .back-btn { display: inline-block; text-decoration: none; color: white; background: rgba(0,0,0,0.3); padding: 8px 15px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
        .new-folder-btn { background: #f3da35; color: #333; border: none; padding: 10px 15px; border-radius: 10px; font-weight: bold; cursor: pointer; margin-left: 10px; }
    </style>
</head>
<body>

<div class="top-nav">
    <nav>
        <div class="search-container">
            <div class="search-bar">
                <form action="/fms/search/search.php" method="GET">
                    <input type="text" placeholder="⌕ Search directory...">
                </form>
            </div>
        </div>
    </nav>
    <div class="dropdown">
        <img src="/fms/uploads/konata.png" class="user-icon">
        <div class="dropdown-content">
            <a href="/fms/users/viewprof.php">Profile</a>
            <a href="/fms/logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="sidebar" id="sidebar">
    <button onclick="window.location.href='/fms/dashboard.php'"><img src="/fms/uploads/icon/db.png" title="Dashboard"></button>
    <button onclick="window.location.href='/fms/feat/folder.php'"><img src="/fms/uploads/icon/folder.png" title="Folders"></button>        
    <button onclick="window.location.href='/fms/feat/document.php'"><img src="/fms/uploads/icon/doc.png" title="Document"></button>
    <button onclick="window.location.href='/fms/feat/media.php'"><img src="/fms/uploads/icon/media.png" title="Media"></button>
    <button onclick="window.location.href='/fms/feat/favorite.php'"><img src="/fms/uploads/icon/fav.png" title="Favorite"></button>
    <button onclick="window.location.href='/fms/users/faculty.php'"><img src="/fms/uploads/icon/user.png" title="Faculty"></button>
</div>

<button id="toggleBtn" class="toggle-btn">&#9776;</button>

<div class="explorer-container">
    <div class="upload-header" style="display: flex; align-items: center; justify-content: space-between;">
        <form action="/fms/backend/upload_logic.php" method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="upload-wrapper">
                <input type="hidden" name="folder_id" value="<?= $current_folder_id ?>">
                
                <input type="file" name="fileToUpload" id="file-upload" class="hidden-input" accept=".jpg,.png,.pdf,.docx,.txt">
                <label for="file-upload" class="custom-upload-btn">
                    <img src="/fms/uploads/icon/folder.png" class="btn-icon"> Choose File
                </label>
                <span id="file-name" style="color: white; margin-left: 10px;">No file chosen</span>
                <button type="submit" class="upload-submit-btn" id="submitBtn" style="display:none;">Upload Now</button>
            </div>
        </form>

        <button class="new-folder-btn" onclick="openModal()">+ New Folder</button>
    </div>

    <?php if ($current_folder_id): ?>
        <a href="folder.php" class="back-btn">← Back to Main Directory</a>
    <?php endif; ?>

    <div class="folder-list">
        <?php if (!$current_folder_id && $folders_result && $folders_result->num_rows > 0): ?>
            <?php while($f = $folders_result->fetch_assoc()): ?>
                <div class="folder-item" style="cursor: pointer; border-left: 4px solid #f3da35;" onclick="window.location.href='folder.php?fid=<?= $f['id'] ?>'">
                    <div class="folder-icon-name">
                        <img src="/fms/uploads/icon/folder.png" class="folder-icon">
                        <span class="folder-name"><?= htmlspecialchars($f['folder_name']) ?></span>
                    </div>
                    <div class="folder-actions">
                        <span class="action-icon" onclick="event.stopPropagation(); renameFolder(<?= $f['id'] ?>, '<?= addslashes($f['folder_name']) ?>')">✎</span>
                        
                        <a href="/fms/backend/delete_logic.php?delete_folder=<?= $f['id'] ?>" 
                        onclick="event.stopPropagation(); return confirm('Delete folder?')" 
                        class="action-icon">🗑</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

        <?php if ($files_result && $files_result->num_rows > 0): ?>
            <?php while($row = $files_result->fetch_assoc()): 
                $ext = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['png', 'jpg', 'jpeg'])) { $icon = "photo2.png"; }
                else if ($ext == 'pdf') { $icon = "doc.png"; }
                else { $icon = "doc.png"; }
            ?>
                <div class="folder-item">
                    <div class="folder-icon-name">
                        <img src="/fms/uploads/icon/<?= $icon ?>" class="folder-icon">
                        <div class="file-info">
                            <span class="folder-name"><?= htmlspecialchars($row['file_name']) ?></span>
                            <small style="color: rgba(255,255,255,0.6); display: block;">
                                By <?= htmlspecialchars($row['full_name']) ?> | <?= date("M d, Y", strtotime($row['upload_date'])) ?>
                            </small>
                        </div>
                    </div>
                    <div class="folder-actions">
                        <a href="/fms/backend/fav_logic.php?file_id=<?= $row['id'] ?><?= $current_folder_id ? '&fid='.$current_folder_id : '' ?>" 
                            style="text-decoration:none; color: <?= $row['is_favorite'] ? '#f3da35' : 'inherit' ?>;">
                            ★
                        </a>
                        <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" style="text-decoration:none; color:inherit;">👁</a>
                        <?php if ($row['user_id'] == $_SESSION['user_id']): ?>
                            <a href="/fms/backend/delete_logic.php?delete_file=<?= $row['id'] ?>" 
                            onclick="return confirm('Are you sure you want to delete this file?')" 
                            style="margin-left:10px; cursor:pointer; text-decoration:none;">🗑</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color:rgba(255,255,255,0.4); padding:40px;">No files found in this directory.</p>
        <?php endif; ?>
    </div>
</div>

<div id="folderModal" class="modal">
    <div class="modal-content">
        <h3>Create Folder</h3>
        <form action="/fms/backend/create_folder.php" method="POST">
            <input type="text" name="folder_name" placeholder="Enter folder name..." required>
            <div>
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="confirm-btn">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleBtn");
    toggleBtn.addEventListener("click", () => sidebar.classList.toggle("active"));

    const fileInput = document.getElementById('file-upload');
    const fileNameDisplay = document.getElementById('file-name');
    const submitBtn = document.getElementById('submitBtn');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            fileNameDisplay.textContent = this.files[0].name;
            submitBtn.style.display = "inline-block";
        }
    });

    function openModal() { document.getElementById("folderModal").style.display = "block"; }
    function closeModal() { document.getElementById("folderModal").style.display = "none"; }

function renameFolder(id, oldName) {
    let newName = prompt("Enter new folder name:", oldName);
    
    // Only proceed if user typed something and didn't hit cancel
    if (newName !== null && newName.trim() !== "") {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/fms/backend/rename_logic.php';

        // Add the new name
        const nameInput = document.createElement('input');
        nameInput.type = 'hidden';
        nameInput.name = 'new_name';
        nameInput.value = newName;
        form.appendChild(nameInput);

        // Add the ID
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'folder_id';
        idInput.value = id;
        form.appendChild(idInput);

        // MUST attach to body for some browsers to work
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

</body>
</html>