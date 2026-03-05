<?php
session_start();
require "../db.php";

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Determine which profile to show:
// - If URL has user_id (clicked from students.php) → show that user
// - Otherwise → show logged-in user
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT username, full_name, bio, notes FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// If user not found
if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>'s Profile</title>
    <link rel="stylesheet" href="/fms/users/viewprof.css"></link>
</head>
<body>

<div class="main-content">
    <div class="container profile-card">
        <div class="profile-header">
            <div class="avatar-container">
                <img src="path_to_avatar.png" class="avatar">
            </div>
        </div>

        <h2><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>'s Profile</h2>

        <div class="profile-details">
            <div class="form-group">
                <label>Username:</label>
                <div class="view-value"><?= htmlspecialchars($user['username']) ?></div>
            </div>

            <div class="form-group">
                <label>Full Name:</label>
                <div class="view-value"><?= htmlspecialchars($user['full_name']) ?></div>
            </div>

            <div class="form-group">
                <label>Bio / Role:</label>
                <div class="view-value"><?= nl2br(htmlspecialchars($user['bio'])) ?></div>
            </div>

            <div class="form-group">
                <label>Notes / Broadcast:</label>
                <div class="notes-box">
                    <?= nl2br(htmlspecialchars($user['notes'])) ?: '<span style="color:#ccc;">No broadcast notes available.</span>' ?>
                </div>
            </div>
        </div>

        <div class="footer-links">
            <a href="../users/faculty.php" class="nav-link">Back to Faculty</a>
            <a href="../dashboard.php" class="nav-link">Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>