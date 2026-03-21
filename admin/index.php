<?php
include("../config/database.php");

$users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM users"));
$questions = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM questions"));
$languages = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM languages"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background: #f5f7fb;
    font-family: 'Segoe UI', sans-serif;
}

/* Sidebar */
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

/* Content */
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

/* Cards */
.card-custom {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}

.card-dark {
    background: linear-gradient(135deg, #111827, #374151);
    color: #fff;
}

.card h2 {
    font-weight: 700;
}

/* Chart Placeholder */
.chart-box {
    height: 250px;
    background: linear-gradient(135deg, #e5e7eb, #f9fafb);
    border-radius: 16px;
}

/* Table */
.table-custom {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
}

</style>
</head>
<body>

<div class="sidebar">
    <h4>ADMIN</h4>
    <a href="questions.php"><i class="bi bi-question-circle"></i> Manage Questions</a>
    <a href="languages.php"><i class="bi bi-translate"></i> Languages</a>
    <a href="users.php"><i class="bi bi-people"></i> Users</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">

    <div class="topbar">
        <h3>Dashboard</h3>
        <input type="text" class="search-box" placeholder="Search...">
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card-custom card-dark">
                <p>Total Users</p>
                <h2><?php echo $users['total']; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom">
                <p>Total Questions</p>
                <h2><?php echo $questions['total']; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom">
                <p>Total Languages</p>
                <h2><?php echo $languages['total']; ?></h2>
            </div>
        </div>

    </div>

    <div class="row g-4">

        <div class="col-md-8">
            <div class="card-custom">
                <h5>Total Overview</h5>
                <div class="chart-box mt-3"></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom">
                <h5>Summary</h5>
                <p class="mt-3">Users: <?php echo $users['total']; ?></p>
                <p>Questions: <?php echo $questions['total']; ?></p>
                <p>Languages: <?php echo $languages['total']; ?></p>
            </div>
        </div>

    </div>

    <div class="mt-4">
        <div class="card-custom table-custom p-3">
            <h5>Recent Activity</h5>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Users</td>
                        <td><?php echo $users['total']; ?></td>
                    </tr>
                    <tr>
                        <td>Questions</td>
                        <td><?php echo $questions['total']; ?></td>
                    </tr>
                    <tr>
                        <td>Languages</td>
                        <td><?php echo $languages['total']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
