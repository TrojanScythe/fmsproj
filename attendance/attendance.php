<?php
// your PHP can stay here
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>

    <style>
    
        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: Arial, sans-serif !important;
            background: linear-gradient(135deg, #706081, #b3c2dd) !important;
            display: block !important;
            justify-content: unset !important;
            align-items: unset !important;
            height: auto !important;
        }

        /* Top navbar */
        .top-nav {
            width: 98%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            flex-wrap: wrap;
            padding: 10px 18px;
            border-bottom: 1px solid #ccc;
            position: sticky;
            top: 0;
            z-index: 1000;
     }

            /* Logo */
            .top-nav .logo {
            font-size: 1.4rem;
            font-weight: bold;
            }

            /* Menu links */
            .top-nav .menu a {
            margin-left: 15px;
            text-decoration: none;
            color: #333;
            font-size: 1rem;
            transition: color 0.2s;
            }

            .top-nav .menu a:hover {
            color: #007bff; 
            }
            /* Dropdown container */
            .dropdown {
                position: relative;
                display: inline-block;
            }

            /* Dropdown button (Profile link) */
            .dropbtn {
                text-decoration: none;
                color: #333;
                font-size: 1rem;
                padding: 8px 12px;
                cursor: pointer;
            }

            /* Dropdown content (hidden by default) */
            .dropdown-content {
                display: none;
                position: absolute;
                background-color: white;
                min-width: 150px;
                box-shadow: 0 8px 16px rgba(0,0,0,0.2);
                border-radius: 8px;
                z-index: 1000;
            }

            /* Links inside dropdown */
            .dropdown-content a {
                display: block;
                padding: 10px;
                text-decoration: none;
                color: #333;
            }

            .dropdown-content a:hover {
                background-color: #f1f1f1;
                color: #007bff;
            }

            /* Show dropdown on hover */
            .dropdown:hover .dropdown-content {
                display: block;
            }
    </style>
</head>

<body>

    <header class="top-nav">
        <div class="logo">
            <a href="/prproj/dashboard.php">ChecR</a>
        </div>
        <nav class="menu">
            <a href="/prproj/attendance/attendance.php">Attendance</a>
            <a href="/prproj/students/students.php">Students</a>
            <div class="dropdown">
                <a href="#" class="dropbtn">User</a>
                <div class="dropdown-content">
                    <a href="/prproj/users/viewprof.php">View Profile</a>
                    <a href="/prproj/users/editprof.php">Edit Profile</a>
                </div>
            </div>
            <a href="/prproj/logout.php">Logout</a>
        </nav>
    </header>



</body>
</html>