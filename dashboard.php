<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// FIX: Define BOTH naming styles to satisfy all queries below
$user_id = $_SESSION['user_id'];
$userId  = $_SESSION['user_id']; 

// --- HELPER: FETCH USER AVATAR ---
$u_query = $conn->query("SELECT profile_pic FROM users WHERE id = $user_id");
$u_data = $u_query->fetch_assoc();
$user_avatar = !empty($u_data['profile_pic']) ? $u_data['profile_pic'] : '/fms/uploads/konata.png';

// --- HELPER: TIME AGO FUNCTION ---
function time_ago($timestamp) {
    if (!$timestamp) return "Unknown";
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours   = round($seconds / 3600);
    $days    = round($seconds / 8400);

    if ($seconds <= 60) return "Just now";
    else if ($minutes <= 60) return "$minutes min ago";
    else if ($hours <= 24) return "$hours hrs ago";
    else return "$days days ago";
}

// --- DATA FETCHING ---

// 1. Stats (Now using $userId correctly)
$totalFiles = $conn->query("SELECT COUNT(*) as count FROM media WHERE user_id = $userId")->fetch_assoc()['count'];
$totalFolders = $conn->query("SELECT COUNT(*) as count FROM folders WHERE user_id = $userId")->fetch_assoc()['count'];

// 2. Recent Files
$recentFiles = $conn->query("SELECT * FROM media WHERE user_id = $userId ORDER BY upload_date DESC LIMIT 5");

// 3. Faculty Broadcasts
$broadcasts = $conn->query("SELECT full_name, role, notes, note_updated_at FROM users WHERE notes IS NOT NULL AND notes != '' ORDER BY note_updated_at DESC LIMIT 3");

// 4. Storage Calculation
$percentage = ($totalFiles > 0) ? min(($totalFiles * 2.5), 100) : 5; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="cssshi/dashboard.css"></link>
<style>
        :root { --accent: #f3da35; --glass: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.1); }
        body { background: #0f0f12; color: #fff; font-family: 'Inter', sans-serif; margin: 0; overflow-x: hidden; }

        .dash-wrapper { margin-left: 90px; padding: 40px; display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        

        .card { background: var(--glass); border: 1px solid var(--border); border-radius: 24px; padding: 25px; backdrop-filter: blur(15px); }
        
  
        .main-col { display: flex; flex-direction: column; gap: 30px; }
        .stats-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .storage-card { background: linear-gradient(135deg, rgba(243, 218, 53, 0.1), rgba(0,0,0,0)); }
        .progress-bar { height: 8px; background: rgba(255,255,255,0.1); border-radius: 10px; margin: 15px 0; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--accent); width: <?= $percentage ?>%; transition: 1.5s cubic-bezier(0.4, 0, 0.2, 1); }

 
        .drop-zone { 
            border: 2px dashed var(--accent); border-radius: 24px; padding: 40px; 
            text-align: center; cursor: pointer; transition: 0.3s; 
            background: rgba(243, 218, 53, 0.02);
        }
        .drop-zone:hover, .drop-zone.drag-over { background: rgba(243, 218, 53, 0.1); transform: scale(1.01); }

       
        .notif-col { display: flex; flex-direction: column; gap: 20px; }
        .notif-item { 
            background: rgba(255, 255, 255, 0.03); border-radius: 15px; padding: 15px; 
            margin-bottom: 15px; border-left: 4px solid var(--accent); transition: 0.3s; 
        }
        .notif-item:hover { background: rgba(255, 255, 255, 0.07); transform: translateX(5px); }

  
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; font-size: 0.75rem; opacity: 0.5; padding-bottom: 12px; text-transform: uppercase; }
        td { padding: 14px 0; border-top: 1px solid rgba(255,255,255,0.05); font-size: 0.9rem; }

        /* Buttons */
        .btn-primary { width: 100%; padding: 12px; background: var(--accent); color: #000; border: none; border-radius: 12px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-secondary { width: 100%; padding: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border); border-radius: 12px; cursor: pointer; transition: 0.3s; margin-bottom: 10px; }
        .btn-primary:hover { background: #fff; transform: translateY(-2px); }
    </style>
</head>

<body>
<!-- top navbar -->


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
<!-- sadjadjsd -->
<div class="dash-wrapper">
    <div class="main-col">
            <header>
                <h1 style="margin:0; font-size: 2rem;">Workspace</h1>
                <?php 
                    // Fallback logic: if full_name is empty, use username
                    $userGreeting = !empty($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['username'];
                ?>
                <p style="opacity:0.5;">Welcome back, <?= htmlspecialchars($userGreeting) ?></p>
            </header>

        <div class="stats-row">
            <div class="card">
                <span style="opacity:0.6; font-size:0.7rem; letter-spacing:1px;">TOTAL ASSETS</span>
                <h2 style="font-size: 2.8rem; margin:10px 0;"><?= $totalFiles ?></h2>
            </div>
            <div class="card storage-card">
                <span style="opacity:0.6; font-size:0.7rem; letter-spacing:1px;">CLOUD STORAGE</span>
                <div class="progress-bar"><div class="progress-fill"></div></div>
                <small><?= round($percentage, 1) ?>% of 100MB limit used</small>
            </div>
        </div>

        <div class="drop-zone" id="drop-zone">
            <h3 style="margin:0; color:var(--accent);">+ Quick Upload</h3>
            <p style="opacity:0.5; font-size:0.8rem; margin: 10px 0 0;">Drag files here to upload to main directory</p>
            <input type="file" id="file-input" hidden>
        </div>

        <div class="card">
            <h3 style="margin-top:0;">Recent Activity</h3>
            <table>
                <thead>
                    <tr><th>File Name</th><th>Date Uploaded</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if ($recentFiles->num_rows > 0): ?>
                        <?php while($f = $recentFiles->fetch_assoc()): ?>
                        <tr>
                            <td>📄 <?= htmlspecialchars($f['file_name']) ?></td>
                            <td><?= date("M d, Y", strtotime($f['upload_date'])) ?></td>
                            <td><a href="<?= $f['file_path'] ?>" target="_blank" style="color:var(--accent); text-decoration:none;">View</a></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center; opacity:0.5; padding: 20px;">No recent files found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="notif-col">
        <div class="card" style="min-height: 550px;">
            <h3 style="margin-top:0; color: var(--accent);">Faculty Broadcasts</h3>
            
            <div class="broadcast-list">
                <?php if ($broadcasts && $broadcasts->num_rows > 0): ?>
                    <?php while($note = $broadcasts->fetch_assoc()): ?>
                        <div class="notif-item">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <small style="color: var(--accent); font-weight: bold;"><?= htmlspecialchars($note['full_name']) ?></small>
                                <small style="opacity: 0.4; font-size: 0.7rem;"><?= time_ago($note['note_updated_at']) ?></small>
                            </div>
                            <p style="margin: 8px 0 0; font-size: 0.85rem; line-height: 1.5; opacity: 0.9;">
                                <?= nl2br(htmlspecialchars($note['notes'])) ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="opacity: 0.5; font-size: 0.8rem; text-align: center;">No active broadcasts.</p>
                <?php endif; ?>
            </div>

            <hr style="border:0; border-top: 1px solid var(--border); margin: 30px 0;">
            
            <h3>Navigation</h3>
            <button onclick="window.location.href='/fms/users/editprof.php'" class="btn-secondary">Update My Broadcast</button>
            <button onclick="window.location.href='/fms/feat/folder.php'" class="btn-primary">Open File Explorer</button>
        </div>
    </div>
</div>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');

    // Toggle Sidebar
    document.getElementById("toggleBtn").addEventListener("click", () => {
        document.getElementById("sidebar").classList.toggle("active");
    });

    // Drag & Drop Logic
    dropZone.onclick = () => fileInput.click();

    dropZone.ondragover = (e) => { 
        e.preventDefault(); 
        dropZone.classList.add('drag-over'); 
    };
    dropZone.ondragleave = () => { 
        dropZone.classList.remove('drag-over'); 
    };

    dropZone.ondrop = (e) => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        handleUpload(e.dataTransfer.files[0]);
    };

    fileInput.onchange = () => handleUpload(fileInput.files[0]);

    function handleUpload(file) {
        if(!file) return;
        let formData = new FormData();
        formData.append('fileToUpload', file);
        
        fetch('/fms/backend/upload_logic.php', { method: 'POST', body: formData })
        .then(() => {
            alert("File successfully uploaded to Root!");
            location.reload();
        })
        .catch(() => alert("Upload failed."));
    }
</script>
</body>
</html>