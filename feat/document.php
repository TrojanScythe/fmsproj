<?php
require "../db.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}


$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT profile_pic FROM users WHERE id = $user_id");
$user_data = $user_query->fetch_assoc();
$user_avatar = !empty($user_data['profile_pic']) ? $user_data['profile_pic'] : '/fms/uploads/konata.png';

$docExts = "('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf')";


$query = "SELECT media.*, users.username, users.full_name 
          FROM media 
          JOIN users ON media.user_id = users.id 
          WHERE LOWER(SUBSTRING_INDEX(media.file_name, '.', -1)) IN $docExts 
          ORDER BY media.upload_date DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/fms/cssshi/dashboard.css"></link>
    <link rel="stylesheet"  type="text/css" href="/fms/feat/css/document.css"></link>
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

<!-- documents -->
 <div class="main-content">
    <div class="document-surface">
        <div class="surface-header">
            <div>
                <h2 style="margin: 0; font-size: 1.8rem;">Document Archive</h2>
                <p style="margin: 5px 0 0; opacity: 0.5; font-size: 0.85rem;">Stored records and official files</p>
            </div>
            <div class="surface-stats">
                <span style="background: var(--accent); color: #000; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">
                    <?= $result->num_rows ?> Files
                </span>
            </div>
        </div>

        <div class="doc-grid">
            <?php while($row = $result->fetch_assoc()): 
                $ext = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
                $filePath = $row['file_path'];
                
                // Icon Logic
                $icon = "📄"; $colorClass = "txt";
                if($ext == 'pdf') { $icon = "📕"; $colorClass = "pdf"; }
                elseif(in_array($ext, ['doc', 'docx'])) { $icon = "📘"; $colorClass = "word"; }
                elseif(in_array($ext, ['xls', 'xlsx', 'csv'])) { $icon = "📗"; $colorClass = "excel"; }
                elseif(in_array($ext, ['ppt', 'pptx'])) { $icon = "📙"; $colorClass = "ppt"; }
            ?>
                <div class="doc-card">
                    <div class="doc-preview">
                        <span class="type-label"><?= $ext ?></span>
                        <?php if($ext == 'pdf'): ?>
                            <embed src="<?= $filePath ?>#toolbar=0" type="application/pdf" width="100%" height="100%">
                        <?php else: ?>
                            <span class="file-icon-large <?= $colorClass ?>"><?= $icon ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="doc-footer">
                        <span class="doc-name"><?= htmlspecialchars($row['file_name']) ?></span>
                        <div class="doc-meta">
                            <span>By: <?= htmlspecialchars($row['full_name'] ?: $row['username']) ?></span>
                            <span><?= date("m/d/y", strtotime($row['upload_date'])) ?></span>
                            <a href="<?= $filePath ?>" target="_blank" class="view-link">OPEN</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
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