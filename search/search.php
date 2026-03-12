<?php
require "../db.php"; 

$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT profile_pic FROM users WHERE id = $user_id");
$user_data = $user_query->fetch_assoc();
$user_avatar = !empty($user_data['profile_pic']) ? $user_data['profile_pic'] : '/fms/uploads/konata.png';

// Get the search query and clean it to prevent SQL injection
$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

// 1. Search Files (Removed 'file_type' to fix the error)
$fileResults = $conn->query("SELECT * FROM media WHERE file_name LIKE '%$query%' ORDER BY upload_date DESC LIMIT 20");

// 2. Search Users
$userResults = $conn->query("SELECT id, username, full_name, profile_pic FROM users WHERE username LIKE '%$query%' OR full_name LIKE '%$query%' LIMIT 5");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Results: <?= htmlspecialchars($query) ?></title>
    <link rel="stylesheet" href="/fms/cssshi/dashboard.css">
    <style>
        .search-wrapper { max-width: 1000px; margin: 80px auto; padding: 20px; min-height: 450px};
        .result-section { margin-bottom: 40px; }
        .section-header { color: var(--accent); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px; display: block; }
        
        /* Glass Grid */
        .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        
        .search-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 20px;
            backdrop-filter: blur(10px);
            transition: 0.3s;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .search-card:hover { background: rgba(255,255,255,0.08); border-color: var(--accent); transform: translateY(-3px); }
        
        .user-thumb { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); }
        .file-icon { font-size: 1.5rem; opacity: 0.7; }
    </style>
</head>
<body>
<div class="top-nav">
    <nav>
        <!-- Menu items if any -->
         <div class="search-container">
        <div class="search-bar">
            <form action="/fms/search/search.php" method="GET">
                <input type="text" name="q" placeholder="⌕ Type here to search">
            </form>
            </div>
        </div>
    </nav>


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
<!-- buton -->
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
<script>   
    document.getElementById("toggleBtn").addEventListener("click", () => {
        document.getElementById("sidebar").classList.toggle("active");
    });
</script>
<!-- search-->
<div class="search-wrapper">
    <h2 style="margin-bottom: 40px;">Results for <span style="color:var(--accent);">"<?= htmlspecialchars($query) ?>"</span></h2>

    <?php if($userResults->num_rows > 0): ?>
    <div class="result-section">
        <span class="section-header">People Found</span>
        <div class="results-grid">
            <?php while($u = $userResults->fetch_assoc()): ?>
                <a href="/fms/users/viewprof.php?user_id=<?= $u['id'] ?>" class="search-card">
                    <img src="<?= $u['profile_pic'] ?: '/fms/uploads/konata.png' ?>" class="user-thumb">
                    <div>
                        <div style="font-weight:bold;"><?= htmlspecialchars($u['full_name'] ?: $u['username']) ?></div>
                        <small style="opacity:0.5;">@<?= htmlspecialchars($u['username']) ?></small>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="result-section">
        <span class="section-header">Files Found</span>
        <div class="results-grid">
            <?php if($fileResults->num_rows > 0): ?>
                <?php while($f = $fileResults->fetch_assoc()): ?>
                    <a href="<?= $f['file_path'] ?>" target="_blank" class="search-card">
                        <span class="file-icon">📁</span>
                        <div style="overflow:hidden;">
                            <div style="font-weight:bold; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;">
                                <?= htmlspecialchars($f['file_name']) ?>
                            </div>
                            <small style="opacity:0.5;"><?= strtoupper(pathinfo($f['file_name'], PATHINFO_EXTENSION)) ?></small>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="opacity:0.3;">No matching files found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>