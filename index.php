<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role']==='admin' ? 'admin/home.php' : 'user/home.php'));
} else {
    header("Location: login.php");
}
exit;
