<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "quiz_system";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("<div style='font-family:sans-serif;padding:40px;color:#c00;'>
        <h2>Database Connection Failed</h2>
        <p>" . mysqli_connect_error() . "</p>
        <p>Make sure MySQL is running and the database <strong>quiz_system</strong> exists.</p>
    </div>");
}
mysqli_set_charset($conn, "utf8mb4");
