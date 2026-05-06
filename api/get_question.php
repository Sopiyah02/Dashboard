<?php
include("../config/database.php");

$result = mysqli_query($conn, "SELECT * FROM questions");

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header("Content-Type: application/json");
echo json_encode($data);
?>