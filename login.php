<?php
require "db.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, password FROM users WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            // Store both username and id in session
            $_SESSION["username"] = $user["username"];
            $_SESSION["user_id"] = $user["id"];
            header("Location: dashboard.php");
            exit;
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
            position: relative; /* <<< important for absolute positioning */
            background: #ffffff;
            padding: 60px 35px 35px 35px; /* extra top padding for image */
            border-radius: 15px;
            width: 320px;
            text-align: center;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
        }

        .login-box .profile-img {
            position: absolute;
            top: -50px;              /* half of image height */
            left: 50%;               /* center horizontally */
            transform: translateX(-50%);
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;  /* optional border */
            background: #ffffff;        /* fallback background */
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
            <p class="error" style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
