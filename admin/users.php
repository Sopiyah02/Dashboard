<?php
include("../config/database.php");

$users = mysqli_query($conn,"SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background: #f5f7fb;
    font-family: 'Segoe UI', sans-serif;
}

.sidebar {
    height: 100vh;
    width: 240px;
    background: #ffffff;
    position: fixed;
    padding: 20px;
    border-right: 1px solid #e5e7eb;
}

.sidebar h4 {
    font-weight: 700;
    margin-bottom: 30px;
}

.sidebar a {
    display: flex;
    align-items: center;
    padding: 12px;
    border-radius: 10px;
    color: #6b7280;
    text-decoration: none;
    margin-bottom: 10px;
    transition: 0.3s;
}

.sidebar a:hover {
    background: #f3f4f6;
    color: #111827;
}

.sidebar a i {
    margin-right: 10px;
}

.content {
    margin-left: 260px;
    padding: 30px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.search-box {
    background: #fff;
    padding: 8px 15px;
    border-radius: 20px;
    border: 1px solid #e5e7eb;
}

.card-custom {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}

.table-custom {
    border-radius: 12px;
    overflow: hidden;
}

</style>
</head>
<body>

<div class="sidebar">
    <h4>ADMIN</h4>
    <a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="questions.php"><i class="bi bi-question-circle"></i> Manage Questions</a>
    <a href="languages.php"><i class="bi bi-translate"></i> Languages</a>
    <a href="users.php"><i class="bi bi-people"></i> Users</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">

    <div class="topbar">
        <h3>Users</h3>
        <input type="text" class="search-box" placeholder="Search users...">
    </div>

    <div class="card-custom">
        <table class="table table-hover table-custom">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($users)){ ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
