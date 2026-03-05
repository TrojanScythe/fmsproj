    <?php
    echo '<link rel="stylesheet" type="text/css" href="fty.css" />';
    session_start();
    require "../db.php";


    if (!isset($_SESSION['username'])) {
        header("Location: prproj/login.php");
        exit;
    }

    // Fetch all users
    $result = $conn->query("SELECT id, username, email FROM users ORDER BY id ASC");
    ?>


<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="user/fty.css"></link>
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
        <h2>All Faculty Members</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td>
                            <a href="/fms/users/viewprof.php?user_id=<?= $user['id'] ?>">
                                 <?= htmlspecialchars($user['username']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>