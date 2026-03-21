<?php
include("../config/database.php");

// ADD
if(isset($_POST['add'])){
    $name = $_POST['language_name'];
    mysqli_query($conn,"INSERT INTO languages (language_name) VALUES ('$name')");
}

// DELETE
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM languages WHERE language_id=$id");
}

// EDIT
if(isset($_POST['edit'])){
    $id = $_POST['language_id'];
    $name = $_POST['language_name'];
    mysqli_query($conn,"UPDATE languages SET language_name='$name' WHERE language_id=$id");
}

$lang = mysqli_query($conn,"SELECT * FROM languages");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Languages</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
.sidebar { height: 100vh; width: 240px; background: #fff; position: fixed; padding: 20px; border-right: 1px solid #e5e7eb; }
.sidebar h4 { font-weight: 700; margin-bottom: 30px; }
.sidebar a { display: flex; align-items: center; padding: 12px; border-radius: 10px; color: #6b7280; text-decoration: none; margin-bottom: 10px; }
.sidebar a:hover { background: #f3f4f6; color: #111827; }
.sidebar a i { margin-right: 10px; }
.content { margin-left: 260px; padding: 30px; }
.topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.card-custom { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
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
    <h3>Languages</h3>
    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Language</button>
</div>

<div class="card-custom">
<table class="table table-hover">
<thead class="table-light">
<tr>
<th>ID</th>
<th>Language</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($lang)){ ?>
<tr>
<td><?php echo $row['language_id']; ?></td>
<td><?php echo $row['language_name']; ?></td>
<td>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['language_id']; ?>">Edit</button>
    <a href="?delete=<?php echo $row['language_id']; ?>" class="btn btn-sm btn-danger">Delete</a>
</td>
</tr>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal<?php echo $row['language_id']; ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header"><h5>Edit Language</h5></div>
<div class="modal-body">
<input type="hidden" name="language_id" value="<?php echo $row['language_id']; ?>">
<input type="text" name="language_name" class="form-control" value="<?php echo $row['language_name']; ?>">
</div>
<div class="modal-footer">
<button class="btn btn-success" name="edit">Save</button>
</div>
</form>
</div>
</div>
</div>

<?php } ?>
</tbody>
</table>
</div>

</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header"><h5>Add Language</h5></div>
<div class="modal-body">
<input type="text" name="language_name" class="form-control" placeholder="Enter language...">
</div>
<div class="modal-footer">
<button class="btn btn-dark" name="add">Add</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>