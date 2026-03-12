<?php
session_start();
require "../db.php"; // session_start is inside here

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$userId  = $_SESSION['user_id'];
$user_query = $conn->query("SELECT profile_pic FROM users WHERE id = $user_id");
$user_data = $user_query->fetch_assoc();
$user_avatar = !empty($user_data['profile_pic']) ? $user_data['profile_pic'] : '/fms/uploads/konata.png';



$mediaExts = "('jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm')";

$query = "SELECT media.*, users.username, users.full_name 
          FROM media 
          JOIN users ON media.user_id = users.id 
          WHERE LOWER(SUBSTRING_INDEX(media.file_name, '.', -1)) IN $mediaExts 
          ORDER BY media.upload_date DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/fms/cssshi/dashboard.css"></link>
    <link rel="stylesheet" type="text/css" href="/fms/feat/css/media.css"></link>
</head>

<body>
<!-- top navbar -->


<div class="top-nav">
    <nav>
        <!-- Menu items if any -->
         <div class="search-container">
        <div class="search-bar">
            <form action="/fms/search/search.php" method="GET">
                <input type="text" placeholder="⌕ Type here to search">
            </form>
            </div>
        </div>
    </nav>
    <div class="nav-icons">


    <!-- User dropdown -->
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
<!--sidebar-->
<!-- HTML -->
  <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
<div class="sidebar" id="sidebar">
    <button onclick="window.location.href='/fms/dashboard.php'">
        <img src="/fms/uploads/icon/db.png" alt="Dashboard" title="Dashboard">
    </button>
    <button onclick="window.location.href='/fms/feat/folder.php'">
        <img src="/fms/uploads/icon/folder.png" alt="Folders" title="Folders">
    </button>         
    <button onclick="window.location.href='/fms/feat/document.php'">
        <img src="/fms/uploads/icon/doc.png" alt="Document" title="Document">
    </button>
    <button onclick="window.location.href='/fms/feat/media.php'">
        <img src="/fms/uploads/icon/media.png" alt="Media" title="Media">
    </button>
    <button onclick="window.location.href='/fms/feat/favorite.php'">
        <img src="/fms/uploads/icon/fav.png" alt="Favorite" title="Favorite">
    </button>
    <button onclick="window.location.href='/fms/users/faculty.php'">
        <img src="/fms/uploads/icon/user.png" alt="Faculty" title="Faculty">
    </button>
</div>

<button id="toggleBtn" class="toggle-btn">&#9776;</button>

<!-- MEDIA -->
<div class="main-content" style="margin-left: 90px; padding-top: 80px;">
    <h2 style="margin-left: 20px; color: var(--accent);">Media Gallery</h2>
    
    <div class="media-container">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($file = $result->fetch_assoc()): 
            $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
            $filePath = $file['file_path'];
            $fileName = htmlspecialchars($file['file_name']);
        ?>
                <?php 
                    // Check if THIS specific file is favorited by THIS specific user
                    $f_id = $file['id'];
                    $is_fav_query = $conn->query("SELECT id FROM favorites WHERE user_id = $userId AND file_id = $f_id");
                    $is_starred = ($is_fav_query->num_rows > 0);
                ?>
            <div class="media-card">
                <div class="media-preview">
                    <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                        <img src="<?= $filePath ?>" alt="<?= $fileName ?>" style="width:100%; height:100%; object-fit:cover;">
                    
                    <?php elseif (in_array($ext, ['mp4', 'webm'])): ?>
                        <video muted onmouseover="this.play()" onmouseout="this.pause();this.currentTime=0;">
                            <source src="<?= $filePath ?>" type="video/<?= $ext ?>">
                        </video>
                        <span class="play-hint">▶</span>
                    
                    <?php elseif ($ext === 'pdf'): ?>
                        <embed src="<?= $filePath ?>#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="100%">
                    
                    <?php else: ?>
                        <div class="file-icon">📄<br><small><?= strtoupper($ext) ?></small></div>
                    <?php endif; ?>
                </div>


               
                <div class="media-footer">
                    <span class="file-name-label" title="<?= $fileName ?>"><?= $fileName ?></span>
                    <div class="doc-meta">
                        <span>By: <?= htmlspecialchars($file['full_name'] ?: $file['username']) ?></span>
                        <span><?= date("m/d/y", strtotime($file['upload_date'])) ?></span>
                        <a href="<?= $filePath ?>" target="_blank" class="view-btn">👁</a>
                         <a href="/fms/backend/fav_logic.php?file_id=<?= $file['id'] ?>" 
                            class="fav-btn" 
                            style="text-decoration: none; color: <?= $is_starred ? '#f3da35' : 'rgba(255,255,255,0.3)' ?>;">
                            <?= $is_starred ? '★' : '☆' ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
    </div>
</div>

<script>
    // JS: Toggle sidebar
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("toggleBtn");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
</script>




</body>
</html>