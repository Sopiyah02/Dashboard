<?php
include("../config/database.php");

// ADD
if(isset($_POST['add'])){
    $lang = $_POST['language_id'];
    $question = $_POST['question_text'];
    mysqli_query($conn,"INSERT INTO questions (language_id, question_text) VALUES ('$lang','$question')");
}

// DELETE
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM questions WHERE question_id=$id");
}

// EDIT
if(isset($_POST['edit'])){
    $id = $_POST['question_id'];
    $lang = $_POST['language_id'];
    $question = $_POST['question_text'];
    mysqli_query($conn,"UPDATE questions SET language_id='$lang', question_text='$question' WHERE question_id=$id");
}

$result = mysqli_query($conn,"
SELECT q.question_id, q.question_text, l.language_name, q.language_id
FROM questions q
JOIN languages l ON q.language_id = l.language_id
");

$languages = mysqli_query($conn,"SELECT * FROM languages");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Questions</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { background: #ffe6f0; font-family: 'Segoe UI', sans-serif; }

/* Sidebar */
.sidebar { height: 100vh; width: 240px; background: #ffe6f0; position: fixed; padding: 20px; border-right: 1px solid #f0c0d6; }
.sidebar h4 { font-weight: 700; margin-bottom: 30px; color: #d63384; }
.sidebar a { display: flex; align-items: center; padding: 12px; border-radius: 10px; color: #a64d79; text-decoration: none; margin-bottom: 10px; }
.sidebar a:hover { background: #f9cfe3; color: #800040; }
.sidebar a i { margin-right: 10px; }

/* Content */
.content { margin-left: 260px; padding: 30px; }
.topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

/* Cards */
.card-custom { background: #fff0f8; border-radius: 16px; padding: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }

/* Buttons */
.btn-dark { background: linear-gradient(135deg, #ff66b2, #ff3399); color: #fff; border: none; }
.btn-dark:hover { background: linear-gradient(135deg, #ff3399, #ff66b2); color: #fff; }

.btn-primary { background: #ff66b2; border: none; color: #fff; }
.btn-primary:hover { background: #ff3399; color: #fff; }

.btn-success { background: #ff99cc; border: none; color: #fff; }
.btn-success:hover { background: #ff66b2; color: #fff; }

.btn-danger { background: #ff3399; border: none; color: #fff; }
.btn-danger:hover { background: #d63384; color: #fff; }

/* Table hover */
.table-hover tbody tr:hover { background: #ffd6e6; }
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
    <h3>Manage Questions</h3>
    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Question</button>
</div>

<div class="card-custom">
<table class="table table-hover">
<thead class="table-light">
<tr>
<th>ID</th>
<th>Language</th>
<th>Question</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
<td><?php echo $row['question_id']; ?></td>
<td><?php echo $row['language_name']; ?></td>
<td><?php echo $row['question_text']; ?></td>
<td>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['question_id']; ?>">Edit</button>
    <a href="?delete=<?php echo $row['question_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal<?php echo $row['question_id']; ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header"><h5>Edit Question</h5></div>
<div class="modal-body">
<input type="hidden" name="question_id" value="<?php echo $row['question_id']; ?>">
<select name="language_id" class="form-control mb-2">
<?php mysqli_data_seek($languages,0); while($lang=mysqli_fetch_assoc($languages)){ ?>
<option value="<?php echo $lang['language_id']; ?>" <?php if($lang['language_id']==$row['language_id']) echo 'selected'; ?>>
<?php echo $lang['language_name']; ?>
</option>
<?php } ?>
</select>
<textarea name="question_text" class="form-control"><?php echo $row['question_text']; ?></textarea>
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
<div class="modal-header"><h5>Add Question</h5></div>
<div class="modal-body">
<select name="language_id" class="form-control mb-2">
<?php while($lang=mysqli_fetch_assoc($languages)){ ?>
<option value="<?php echo $lang['language_id']; ?>"><?php echo $lang['language_name']; ?></option>
<?php } ?>
</select>
<textarea name="question_text" class="form-control" placeholder="Enter question..."></textarea>
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