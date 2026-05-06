<?php
include("auth_guard.php");
include("../config/database.php");

$msg = $msg_type = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_lang'])) {
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    if ($name) {
        if (mysqli_query($conn,"INSERT INTO languages (name) VALUES ('$name')"))
            $msg='Language added!'; $msg_type='success';
    } else { $msg='Name required.'; $msg_type='error'; }
}
if (isset($_GET['delete'])) {
    $id=intval($_GET['delete']);
    mysqli_query($conn,"DELETE FROM languages WHERE id=$id");
    header("Location: languages.php"); exit;
}

$langs = mysqli_query($conn,"SELECT l.*,COUNT(q.id) qc FROM languages l LEFT JOIN questions q ON q.language_id=l.id GROUP BY l.id ORDER BY l.name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin – Languages</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-translate me-2"></i>Languages</h5>
        <div class="user-chip"><div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div><?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>
    <div class="page-body">

        <?php if($msg): ?>
        <div class="alert alert-<?php echo $msg_type==='success'?'success-pink':'pink'; ?> mb-3 py-2 px-3 small">
            <i class="bi bi-<?php echo $msg_type==='success'?'check-circle':'exclamation-circle'; ?> me-2"></i><?php echo $msg; ?>
        </div>
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card-box">
                    <div class="card-head"><h6><i class="bi bi-plus-circle me-2"></i>Add Language</h6></div>
                    <div class="card-body-p">
                        <form method="POST">
                            <input type="hidden" name="add_lang" value="1">
                            <div class="mb-3">
                                <label class="form-label">Language Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Java, Python…" required>
                            </div>
                            <button class="btn-pink w-100">Add Language</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card-box">
                    <div class="card-head"><h6><i class="bi bi-list me-2"></i>All Languages</h6></div>
                    <table class="table table-pink mb-0">
                        <thead><tr><th>#</th><th>Name</th><th>Questions</th><th>Added</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php $i=1; while($l=mysqli_fetch_assoc($langs)): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><i class="bi bi-code-slash me-2" style="color:var(--pink-400);"></i><?php echo htmlspecialchars($l['name']); ?></td>
                            <td><span class="badge-pill badge-general"><?php echo $l['qc']; ?> questions</span></td>
                            <td style="font-size:.8rem;color:var(--text-muted);"><?php echo date('M j, Y',strtotime($l['created_at'])); ?></td>
                            <td>
                                <a href="?delete=<?php echo $l['id']; ?>" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="return confirm('Delete this language? All linked questions will also be deleted.')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
