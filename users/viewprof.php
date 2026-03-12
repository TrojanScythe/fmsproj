<?php
session_start();
require "../db.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// --- LOGIC FIX: Determine who we are looking at ---
// We check 'id' (from faculty grid) or 'user_id' (legacy links)
if (isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];
} elseif (isset($_GET['user_id'])) {
    $target_id = (int)$_GET['user_id'];
} else {
    $target_id = $_SESSION['user_id'];
}

// Check if this is the logged-in user's own page
$is_mine = ($target_id == $_SESSION['user_id']);

// --- DATA FETCHING ---
// 1. Fetch the Profile Owner's Info
$stmt = $conn->prepare("SELECT id, username, full_name, bio, notes, role, contact, profile_pic FROM users WHERE id=?");
$stmt->bind_param("i", $target_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) { die("User not found."); }

// 2. Fetch Logged-in User's Avatar (for top-nav if you have one)
$my_id = $_SESSION['user_id'];
$my_data = $conn->query("SELECT profile_pic FROM users WHERE id = $my_id")->fetch_assoc();
$my_avatar = !empty($my_data['profile_pic']) ? $my_data['profile_pic'] : '/fms/uploads/konata.png';

// 3. Fetch Activity & Stats for the TARGET user
$activity = $conn->query("SELECT file_name, upload_date FROM media WHERE user_id = $target_id ORDER BY upload_date DESC LIMIT 5");
$storageData = $conn->query("SELECT COUNT(*) as count FROM media WHERE user_id = $target_id")->fetch_assoc();

$fileCount = $storageData['count'];
$storagePercent = min(($fileCount * 2.5), 100); 

// Profile Picture for the page
$profile_pic_display = !empty($user['profile_pic']) ? $user['profile_pic'] : '/fms/uploads/konata.png';
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>'s Profile</title>
    <link rel="stylesheet" href="/fms/users/viewprof.css"></link>
    <style>
:root {  
        --accent: #f3da35; /* Brighter yellow for better visibility */
        --glass: rgba(255, 255, 255, 0.05); 
        --glass-thick: rgba(255, 255, 255, 0.1);
        --border: rgba(255, 255, 255, 0.15); 
    }

    body { 
        background: radial-gradient(circle at top right, #9a8ea8, #93a4c3); /* Solid dark color works better for glassmorphism */
        background-attachment: fixed;
        color: white; 
        font-family: 'Inter', sans-serif; 
        margin: 0;
        padding: 40px;
    }

    .profile-container {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 320px 1fr; /* Rigid left column, flexible right */
        gap: 30px;
        align-items: start;
    }

    .glass-card {
        background: var(--glass);
        border: 1px solid var(--border);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    /* Left Side: Profile Sidebar */
    .profile-side {
        text-align: center;
        position: sticky;
        top: 40px;
    }

    .avatar-glow {
        width: 140px;
        height: 140px;
        margin: 0 auto 20px;
        position: relative;
    }

    .avatar-glow img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--accent);
        box-shadow: 0 0 20px rgba(243, 218, 53, 0.2);
    }

    .status-indicator {
        position: absolute; bottom: 8px; right: 8px;
        width: 18px; height: 18px; background: #00ff64;
        border-radius: 50%; border: 3px solid #1a1a1e;
    }

    /* Right Side: Content Area */
    .content-side {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .section-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--accent);
        letter-spacing: 2px;
        margin-bottom: 12px;
        display: block;
        font-weight: 800;
    }

    .broadcast-box {
        background: rgba(243, 218, 53, 0.05); /* Tinted yellow */
        border-radius: 18px;
        padding: 20px;
        border-left: 4px solid var(--accent);
        font-style: italic;
        color: #ddd;
    }

    .activity-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .btn-action {
        display: inline-block;
        width: 50%;
        padding: 14px;
        background: var(--accent);
        color: #000;
        text-decoration: none;
        border-radius: 14px;
        font-weight: bold;
        transition: 0.3s;
        margin-top: 20px;
    }

    .btn-action:hover {
        background: #fff;
        transform: translateY(-2px);
    }
        /* Reveal Animation */
        @keyframes reveal {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-container {
            animation: reveal 0.8s ease-out;
        }

        /* Interactive Stats Cards */
        .glass-card {
            transition: transform 0.3s, border-color 0.3s;
        }
        
        .glass-card:hover {
            border-color: rgba(232, 224, 186, 0.4);
        }

        /* Contact Channel Styling */
        .contact-badge {
            background: rgba(255,255,255,0.05);
            padding: 10px 15px;
            border-radius: 12px;
            display: inline-block;
            border: 1px solid var(--border);
            font-family: monospace;
        }

        /* Custom Scrollbar for Bio */
        .bio-text {
            max-height: 150px;
            overflow-y: auto;
            padding-right: 10px;
        }
        .bio-text::-webkit-scrollbar { width: 4px; }
        .bio-text::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }
        </style>
</head>
<body>

<div class="profile-container">
    <div class="glass-card profile-side">
        <div class="avatar-glow">
            <img src="<?= htmlspecialchars($profile_pic_display) ?>">
        </div>
        
        <h2><?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></h2>
        <p style="opacity: 0.5; margin-top: -10px;">@<?= htmlspecialchars($user['username']) ?></p>
        
        <div class="role-badge"><?= htmlspecialchars($user['role'] ?? 'MEMBER') ?></div>

        <div style="text-align: left; margin-top: 20px;">
            <span class="section-title">Personal Bio</span>
            <p style="font-size: 0.9rem; opacity: 0.7; line-height: 1.5;">
                <?= nl2br(htmlspecialchars($user['bio'] ?? 'No bio yet.')) ?>
            </p>
        </div>

        <div style="text-align: left; margin-top: 30px;">
            <span class="section-title">Storage Usage</span>
            <div class="bar-bg"><div class="bar-fill"></div></div>
            <small style="opacity: 0.5;"><?= $fileCount ?> files (<?= $storagePercent ?>%)</small>
        </div>

        <?php if ($is_mine): ?>
            <a href="editprof.php" class="btn-action">Edit Profile</a>
        <?php endif; ?>
        <a href="../dashboard.php" style="display:block; margin-top:15px; font-size: 0.8rem; color: #888; text-decoration: none;">← Return Home</a>
    </div>

    <div class="content-side">
        <div class="glass-card">
            <span class="section-title">Active Broadcast</span>
            <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 15px; font-style: italic;">
                "<?= nl2br(htmlspecialchars($user['notes'] ?? 'No active notes.')) ?>"
            </div>
        </div>

        <div class="glass-card">
            <span class="section-title">Recent Uploads</span>
            <div class="activity-feed">
                <?php if ($activity && $activity->num_rows > 0): ?>
                    <?php while($row = $activity->fetch_assoc()): ?>
                        <div class="activity-row">
                            <span>📄 <?= htmlspecialchars($row['file_name']) ?></span>
                            <small style="opacity: 0.4;"><?= date("M d, Y", strtotime($row['upload_date'])) ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="opacity: 0.3; padding: 10px 0;">No uploads found for this user.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="glass-card">
            <span class="section-title">Contact Channels</span>
            <p><?= htmlspecialchars($user['contact'] ?? $user['email'] ?? 'No contact info.') ?></p>
        </div>
    </div>
</div>
</body>
</html>