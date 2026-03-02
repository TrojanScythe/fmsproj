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
    <style>
        body { font-family: Arial; background: linear-gradient(135deg,#706081,#b3c2dd); margin:0; padding:0; }
        .container { max-width: 500px; margin: 60px auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 12px 25px rgba(0,0,0,0.2);}
        h2 { text-align:center; margin-bottom: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight:bold; margin-bottom: 5px; display:block; }
        .value { padding: 10px; background:#f9f9f9; border-radius:5px; }
        .nav-link { display:block; margin-top:10px; text-align:center; text-decoration:none; color:#333; }
        .nav-link:hover { color:#007bff; }
    </style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>'s Profile</h2>

    <div class="field">
        <span class="label">Username:</span>
        <span class="value"><?= htmlspecialchars($user['username']) ?></span>
    </div>

    <div class="field">
        <span class="label">Full Name:</span>
        <span class="value"><?= htmlspecialchars($user['full_name']) ?></span>
    </div>

    <div class="field">
        <span class="label">Bio:</span>
        <span class="value"><?= nl2br(htmlspecialchars($user['bio'])) ?></span>
    </div>

    <div class="field">
        <span class="label">Notes / Broadcast:</span>
        <span class="value"><?= nl2br(htmlspecialchars($user['notes'])) ?></span>
    </div>

    <a href="../users/faculty.php" class="nav-link">Back to Faculty</a>
    <a href="../dashboard.php" class="nav-link">Back to Dashboard</a>
</div>

</body>
</html>