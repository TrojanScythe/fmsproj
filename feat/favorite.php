<?php
session_start();
require "../db.php"; // Adjust path if your db.php is in the root

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$u_res = $conn->query("SELECT profile_pic FROM users WHERE id = $userId")->fetch_assoc();
$user_avatar = !empty($u_res['profile_pic']) ? $u_res['profile_pic'] : '/fms/uploads/konata.png';


$fav_query = "SELECT media.*, users.full_name 
              FROM favorites 
              JOIN media ON favorites.file_id = media.id 
              JOIN users ON media.user_id = users.id 
              WHERE favorites.user_id = $userId 
              ORDER BY media.upload_date DESC";

$fav_result = $conn->query($fav_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title> My Favorites</title>
    <link rel="stylesheet" type="text/css" href="/fms/cssshi/dashboard.css">
    <link rel="stylesheet" type="text/css" href="/fms/feat/css/favorite.css">
    <link rel="stylesheet" type="text/css" href="/fms/feat/css/folder.css">
    <style>

    </style>
</head>

<body>
<div class="top-nav">
    <nav>
        <div class="search-container">
            <div class="search-bar">
                <form action="/fms/search/search.php" method="GET">
                    <input type="text" name="q" placeholder="⌕ Search your favorites...">
                </form>
            </div>
        </div>
    </nav>
    <div class="nav-icons">


    <div class="dropdown">
        <img src="<?= htmlspecialchars($user_avatar) ?>" class="user-icon" style="object-fit: cover; border: 1px solid var(--accent);">
        
        <div class="dropdown-content">
            <div style="padding: 10px; border-bottom: 1px solid var(--border); font-size: 0.8rem; color: var(--accent);">
                Logged in as: <b><?= htmlspecialchars($_SESSION['username']) ?></b>
            </div>
            <a href="/fms/users/viewprof.php">View Profile</a>
            <a href="/fms/users/editprof.php">Edit Profile</a>
            <a href="/fms/logout.php" style="color: #ff4d4d;">Logout</a>
        </div>
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
<div class="fav-container">

        <h2 style="color: #f3da35;">★ Your Starred Items</h2>
        <hr style="opacity: 0.2; margin-bottom: 20px;">

        <div class="folder-list">
            <?php if ($fav_result && $fav_result->num_rows > 0): ?>
                <?php while($row = $fav_result->fetch_assoc()): 
                    // Simple icon picker based on file extension
                    $ext = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
                    $icon = (in_array($ext, ['png', 'jpg', 'jpeg'])) ? "photo2.png" : "doc.png";
                ?>
                    <div class="folder-item">
                        <div class="folder-icon-name">
                            <img src="/fms/uploads/icon/<?= $icon ?>" class="folder-icon">
                            <div class="file-info">
                                <span class="folder-name"><?= htmlspecialchars($row['file_name']) ?></span>
                                <small style="display:block; opacity:0.6;">Uploaded by <?= htmlspecialchars($row['full_name']) ?></small>
                            </div>
                        </div>
                        <div class="folder-actions">
                            <a href="/fms/backend/fav_logic.php?file_id=<?= $row['id'] ?>" class="fav-star">★</a>
                            <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" style="margin-left:15px; text-decoration:none;">👁</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-msg">
                    <h3>No favorites yet!</h3>
                    <p>Click the star icon on any file in your folders to see them here.</p>
                </div>
            <?php endif; ?>
        </div>
</div>
<script>
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleBtn");
    toggleBtn.addEventListener("click", () => sidebar.classList.toggle("active"));
</script>

</body>
</html>