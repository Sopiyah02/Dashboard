<?php
include("auth_guard.php");
include("../config/database.php");

$msg = $msg_type = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_cat'])) {
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    if ($name) {
        mysqli_query($conn,"INSERT IGNORE INTO categories (name) VALUES ('$name')");
        $msg='Category added!'; $msg_type='success';
    } else { $msg='Name required.'; $msg_type='error'; }
}
if (isset($_GET['delete'])) {
    $id=intval($_GET['delete']);
    mysqli_query($conn,"DELETE FROM categories WHERE id=$id");
    header("Location: categories.php"); exit;
}

$cats = mysqli_query($conn,"SELECT c.*,COUNT(q.id) qc FROM categories c LEFT JOIN questions q ON q.category_id=c.id GROUP BY c.id ORDER BY c.name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin – Categories</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-tag-fill me-2"></i>Categories</h5>
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
                    <div class="card-head"><h6><i class="bi bi-plus-circle me-2"></i>Add Category</h6></div>
                    <div class="card-body-p">
                        <form method="POST">
                            <input type="hidden" name="add_cat" value="1">
                            <div class="mb-3">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Easy, Hard, Difficult…" required>
                            </div>
                            <button class="btn-pink w-100">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card-box">
                    <div class="card-head"><h6><i class="bi bi-list me-2"></i>All Categories</h6></div>
                    <table class="table table-pink mb-0">
                        <thead><tr><th>#</th><th>Name</th><th>Questions</th><th>Added</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php $i=1; while($c=mysqli_fetch_assoc($cats)):
                            $badge = strtolower($c['name'])==='easy'?'easy':(strtolower($c['name'])==='hard'?'hard':'diff');
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><span class="badge-pill badge-<?php echo $badge; ?>"><?php echo htmlspecialchars($c['name']); ?></span></td>
                            <td><span style="color:var(--text-muted);font-size:.85rem;"><?php echo $c['qc']; ?> questions</span></td>
                            <td style="font-size:.8rem;color:var(--text-muted);"><?php echo date('M j, Y',strtotime($c['created_at'])); ?></td>
                            <td>
                                <a href="?delete=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="return confirm('Delete this category?')"><i class="bi bi-trash"></i></a>
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
