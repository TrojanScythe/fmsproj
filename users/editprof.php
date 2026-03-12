<?php
session_start();
require "../db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /fms/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// 1. Fetch User Data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $role = $_POST['role'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $profile_pic = $user['profile_pic']; // Keep old one by default

    // Handle Profile Picture Upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/fms/uploads/profiles/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

        $fileExt = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
        $newFileName = "user_" . $userId . "_" . time() . "." . $fileExt;
        $targetFilePath = $targetDir . $newFileName;
        
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFilePath)) {
            $profile_pic = "/fms/uploads/profiles/" . $newFileName;
        }
    }

    // Update Database
    $stmt = $conn->prepare("UPDATE users SET full_name=?, bio=?, notes=?, role=?, contact=?, profile_pic=? WHERE id=?");
    $stmt->bind_param("ssssssi", $full_name, $bio, $notes, $role, $contact, $profile_pic, $userId);
    
    if($stmt->execute()) {
        $message = "Profile updated successfully!";
        // Update local array for immediate display
        $user['full_name'] = $full_name;
        $user['role'] = $role;
        $user['contact'] = $contact;
        $user['bio'] = $bio;
        $user['notes'] = $notes;
        $user['profile_pic'] = $profile_pic;
        $_SESSION['full_name'] = $full_name; // Sync session
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="user/editprof.css"></link>
    <style>
        :root { --accent: #f3da35; --glass: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.1); }
        body { background: #0f0f12; color: white; font-family: 'Inter', sans-serif; }
        
        .main-content { margin-left: 100px; padding: 40px; display: flex; justify-content: center; }

    </style>
</head>
<body>

<!-- TOGGLE BUTTON -->
<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<!--sidebar-->
<!-- HTMLs -->
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

<!-- NAVBAR -->
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

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="profile-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="avatar-section">
                <div class="avatar-wrapper">
                    <img src="<?= htmlspecialchars($user['profile_pic']) ?>" class="avatar-img" id="preview">
                    <label for="avatar-input" class="upload-btn">+</label>
                    <input type="file" name="avatar" id="avatar-input" hidden accept="image/*">
                </div>
                <h2 style="margin: 15px 0 5px;"><?= htmlspecialchars($user['username']) ?></h2>
                <p style="opacity: 0.5; font-size: 0.9rem;">Manage your public profile information</p>
            </div>

            <?php if(isset($message)) echo "<div class='message'>$message</div>"; ?>

            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" name="role" value="<?= htmlspecialchars($user['role'] ?? '') ?>" placeholder="e.g. Faculty">
                </div>
                <div class="form-group">
                    <label>Contact</label>
                    <input type="text" name="contact" value="<?= htmlspecialchars($user['contact'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Email / Bio</label>
                    <input type="text" name="bio" value="<?= htmlspecialchars($user['bio'] ?? '') ?>">
                </div>
                <div class="form-group full">
                    <label>Broadcast Note (Shows on Dashboard)</label>
                    <textarea name="notes" rows="3"><?= htmlspecialchars($user['notes']) ?></textarea>
                </div>
            </div>

            <button type="submit" class="submit-btn">Save Changes</button>
            <div style="text-align: center; margin-top: 20px;">
                <a href="/fms/dashboard.php" style="color: var(--accent); text-decoration: none; font-size: 0.9rem;">← Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>

<script>
    
        // Toggle Sidebar
    document.getElementById("toggleBtn").addEventListener("click", () => {
        document.getElementById("sidebar").classList.toggle("active");
    });

    // Preview image before uploading
    document.getElementById('avatar-input').onchange = function (evt) {
        const [file] = this.files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
        }
    }
</script>

</body>
</html>