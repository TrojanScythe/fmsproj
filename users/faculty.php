    <?php
    echo '<link rel="stylesheet" type="text/css" href="fty.css" />';
    session_start();
    require "../db.php";


    if (!isset($_SESSION['username'])) {
        header("Location: prproj/login.php");
        exit;
    }

    // Fetch all users
    $result = $conn->query("SELECT id, username, email, role FROM users ORDER BY id ASC");
    ?>


<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="user/fty.css"></link>
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
<!--sidebar-->
<!-- HTML -->
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


<!--faculty members -->

<div class="container">
    <h2 style="color: var(--accent); margin-left: 20px;">Faculty & Staff Directory</h2>
    
    <div class="faculty-grid">
        <?php while($row = $result->fetch_assoc()): 
            $pic = !empty($row['profile_pic']) ? $row['profile_pic'] : '/fms/uploads/konata.png';
        ?>
            <div class="member-card">
                <img src="<?= $pic ?>" class="member-avatar">
                <div class="role-badge"><?= htmlspecialchars($row['role']) ?></div>
                <h3 style="margin: 15px 0 5px;"><?= htmlspecialchars($row['full_name'] ?? $row['username']) ?></h3>
                <p style="opacity: 0.5; font-size: 0.8rem;"><?= htmlspecialchars($row['email']) ?></p>
                
                <a href="/fms/users/viewprof.php?id=<?= $row['id'] ?>" class="view-btn">View Profile</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>