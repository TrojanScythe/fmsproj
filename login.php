<?php
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, password, status, role FROM users WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            
 
            if ($user['status'] === 'pending') {
                $error = " <b>Access Pending:</b> Your account is awaiting administrator approval. Please check back later.";
            } elseif ($user['status'] === 'rejected') {
                $error = " <b>Access Denied:</b> Your registration request was declined by the admin.";
            } else {

                $_SESSION["username"] = $user["username"];
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["role"] = $user["role"];
                $_SESSION['login_success'] = true;

                if ($user['role'] === 'Admin') {
                    header("Location: /fmsadmin/index.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit;
            }
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="cssshi/login.css">
    <style>
                
        .login-box {
            position: relative; 
            background: #ffffff;
            padding: 60px 35px 35px 35px; 
            border-radius: 15px;
            width: 320px;
            text-align: center;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
        }

        .login-box .profile-img {
            position: absolute;
            top: -50px;              
            left: 50%;               
            transform: translateX(-50%);
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;     
            background: #ffffff;            
        }
                .warning-box {
            background: rgba(243, 218, 53, 0.1);
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
        }

    </style>
</head>

<body>




<div class="login-box">

    <img src="/fms/uploads/icon/user.png" class="profile-img">

    <h2>Login</h2>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="✉︎ Username" required>
        <input type="password" name="password" placeholder="🔒︎ Password" required>

        <button type="submit">Login</button>

        <button type="button"
                onclick="window.location.href='register.php'"
                class="register-btn">
            Create New Account
        </button>
        <?php if ($error): ?>
                <div class="warning-box">
                    <span>⚠️</span>
                    <div><?= $error ?></div>
                </div>
            <?php endif; ?>
    </form>
</div>
</body>
</html>
