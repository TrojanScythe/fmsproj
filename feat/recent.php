<?php
echo '<link rel="stylesheet" type="text/css" href="dashboard.css" />';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/fms/cssshi/dashboard.css"></link>
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
    <div class="dropdown">
>

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
    <button onclick="window.location.href='/fms/dashboardashboard.php'">
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






</body>
</html>