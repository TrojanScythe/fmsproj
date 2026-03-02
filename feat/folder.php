<?php
echo '<link rel="stylesheet" type="text/css" href="dashboard.css" />';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/fms/cssshi/dashboard.css"></link>
    <link rel="stylesheet" type="text/css" href="/fms/feat/css/folder.css"></link>
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
    

    <!-- User dropdown -->
    <div class="dropdown">
        <img src="/fms/uploads/konata.png" class="user-icon">
        <div class="dropdown-content">
            <a href="/fms/users/viewprof.php">View Profile</a>
            <a href="/fms/users/editprof.php">Edit Profile</a>

            <a href="/fms/logout.php">Logout</a>
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


<script>
    // JS: Toggle sidebar
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("toggleBtn");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
</script>

<div class="explorer-container">
    <div class="upload-header">
        <button class="upload-btn">Upload</button>
        <button class="upload-btn">+</button>
        <!-- or your search bar / other controls -->
    </div>
    
    <div class="folder-list">
        <div class="folder-item">
            <div class="folder-icon-name">
                <div><img src="/fms/uploads/icon/fold.png" class="folder-icon"></div>
                <span class="folder-name">records</span>
            </div>
            <div class="folder-actions">
                <span class="action-icon">★</span>
                <span class="action-icon">👁</span>
                <span class="action-icon">⋯</span>
            </div>
        </div>

        <div class="folder-item">
            <div class="folder-icon-name">
                <div><img src="/fms/uploads/icon/fold.png" class="folder-icon"></div>
                <span class="folder-name">sched</span>
            </div>
            <div class="folder-actions">
                <span class="action-icon">★</span>
                <span class="action-icon">👁</span>
                <span class="action-icon">⋯</span>
            </div>
        </div>

        <!-- add more items similarly for cp, notvirus, etc. -->
    </div>
</div>


</body>
</html>