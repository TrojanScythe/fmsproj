<?php
echo '<link rel="stylesheet" type="text/css" href="editprof.css" />';
session_start();
require "../db.php";

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: fms /login.php");
    exit;
}

// Get logged-in user info
$stmt = $conn->prepare("SELECT id, username, full_name, bio, notes FROM users WHERE username=?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $notes = $_POST['notes'] ?? '';

    $stmt = $conn->prepare("UPDATE users SET full_name=?, bio=?, notes=? WHERE id=?");
    $stmt->bind_param("sssi", $full_name, $bio, $notes, $user['id']);
    $stmt->execute();
    $stmt->close();

    $message = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="user/editprof.css"></link>
    <style>
      
    </style>
</head>
<body>

<!-- TOGGLE BUTTON -->
<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<!--sidebar-->
<!-- HTML -->
<div class="sidebar" id="sidebar">
    <button onclick="window.location.href='/fms/dashboard.php'">
        <img src="/fms/uploads/icon/db.png" alt="Dashboard" title="Dashboard">
    </button>
    <button onclick="window.location.href='/'">
        <img src="/fms/uploads/icon/folder.png" alt="Folders" title="Folders">
    </button>
    <button onclick="window.location.href='/'">
        <img src="/fms/uploads/icon/doc.png" alt="Document" title="Document">
    </button>
    <button onclick="window.location.href='/'">
        <img src="/fms/uploads/icon/media.png" alt="Media" title="Media">
    </button>
    <button onclick="window.location.href='/'">
        <img src="/fms/uploads/icon/fav.png" alt="Favorite" title="Favorite">
    </button>
    <button onclick="window.location.href='/fms/users/faculty.php'">
        <img src="/fms/uploads/icon/user.png" alt="Faculty" title="Faculty">
    </button>
</div>

<!-- NAVBAR -->
<div class="top-nav">
    <nav>
        <!-- Menu items if any -->
         <div class="search-container">
        <div class="search-bar">
                <input type="text" placeholder="⌕ Type here to search">
            <button type="submit">⌕</button>
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

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="container">
        <h2>Edit Profile</h2>

        <?php if(isset($message)) echo "<p class='message'>{$message}</p>"; ?>

        <form method="post">
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>">

            <label>Bio:</label>
            <textarea name="bio" rows="4"><?= htmlspecialchars($user['bio']) ?></textarea>

            <label>Notes / Broadcast:</label>
            <textarea name="notes" rows="4"><?= htmlspecialchars($user['notes']) ?></textarea>

            <button type="submit" class="submit-btn">Update Profile</button>
        </form>

        <a href="/fms/users/viewprof.php" class="nav-link">View My Profile</a>
        <a href="/fms/dashboard.php" class="nav-link">Back to Dashboard</a>
    </div>
</div>

<!-- JS -->
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
}
</script>

</body>
</html>