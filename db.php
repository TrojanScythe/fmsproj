<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "testdb";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed.");
}
// Default avatar
$user_avatar = '/fms/uploads/konata.png';

// Only attempt to fetch the profile pic if a user is actually logged in
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['profile_pic'])) {
        $u_id = $_SESSION['user_id'];
        
        // Use a basic check to ensure u_id isn't empty before querying
        if (!empty($u_id)) {
            $res = $conn->query("SELECT profile_pic FROM users WHERE id = $u_id");
            if ($res && $row = $res->fetch_assoc()) {
                $_SESSION['profile_pic'] = $row['profile_pic'];
            }
        }
    }
    
    // Update the avatar variable if the session has one
    if (!empty($_SESSION['profile_pic'])) {
        $user_avatar = $_SESSION['profile_pic'];
    }
}
?>