<?php
session_start();


session_unset();


session_destroy();

//red
header("Location: login.php");
exit;

