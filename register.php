<?php
require "db.php";  // U have to make another file that acts as a db then set up sa phpmyadmin localhost
// Initialize variables
$username = $email = $password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Basic validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

   if (empty($errors)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        $errors[] = "Registration failed. Username or email may already exist.";
    }

    $stmt->close();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Sign-Up</title>
    <style>

          body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #706081, #b3c2dd);
        }
        .login-box {
        background: #ffffff;
        padding: 35px;
        border-radius: 15px;
        width: 320px;
        text-align: center;
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
        animation: fadeIn 1s ease-in-out;
        margin-right:55px;
        } 
                .login-box h2 {
            margin-bottom: 25px;
            font-size: 26px;
            color: #333;
            font-weight: bold;
        }

        /* Input style */
        .login-box input {
            width: 90%;
            padding: 12px;
            margin: 12px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: 0.3s;
        }

        .login-box input:focus {
            border-color: #6a11cb;
            outline: none;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        /* Button style */
        .login-box button {
            width: 100%;
            padding: 12px;
            background: #310a64;
            color: #fff;
            border: none;
            border-radius: 23px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
             .login-box button:hover {
            background: #540fa5;
        }
        .register-btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #2575fc;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .register-btn:hover {
            background: #1a5ed9;
        }

        /* Fade animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .h1 {
            font-size: 1.3rem;
            font-weight: bolder;
        }
        .dcp {
            align-items: center;
            margin-top: 40px;
            font-size: 16px;
            cursor: pointer;
        }
        .dcp a {
            color: #2575fc;
            font-size: 1rem;
        }
        .dcp a:hover {
            color: #1c4084;
        }
    </style>

</head>

<body>
    




<div class="login-box"
<h1 style="text-align:center;" class="h1">Sign-Up</h2>
<form method="post" action="">
    <input type="text" name="username" placeholder="@ Username" value="<?php echo htmlspecialchars($username); ?>">
    <input type="email" name="email" placeholder="✉︎ Email" value="<?php echo htmlspecialchars($email); ?>">
    <input type="password" name="password" placeholder="🔒︎ Password">
    <button type="submit">Register</button>
</form>
<a onclick="window.location.href='/fms/login.php'" class="dcp">Already have an account? Login</a>
<?php
// Display errors
if (!empty($errors)) {
    echo '<div class="error"><ul>';
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo '</ul></div>';
}
?>
</div>
</body>
</html>