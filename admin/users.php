<?php
include("auth_guard.php");
include("../config/database.php");

// Delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role='user'");
    }
    header("Location: users.php"); exit;
}

// Search
$search = trim($_GET['search'] ?? '');
$where  = "WHERE role='user'";
if ($search) {
    $es = mysqli_real_escape_string($conn, $search);
    $where .= " AND (username LIKE '%$es%' OR email LIKE '%$es%')";
}
$users = mysqli_query($conn, "SELECT * FROM users $where ORDER BY created_at DESC");
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users $where"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin – Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-people-fill me-2"></i>Users</h5>
        <div class="user-chip">
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div>
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </div>
    </div>
    <div class="page-body">

        <div class="card-box mb-3">
            <div class="card-head">
                <h6>All Registered Users <span style="color:var(--text-muted);font-size:.8rem;font-family:'DM Sans';">(<?php echo $total; ?>)</span></h6>
                <form method="GET" class="d-flex gap-2" style="margin:0;">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search username or email…"
                           value="<?php echo htmlspecialchars($search); ?>" style="width:220px;">
                    <button class="btn-pink btn-sm" style="padding:6px 14px;font-size:.82rem;">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if($search): ?><a href="users.php" class="btn-outline-pink btn-sm" style="padding:6px 14px;font-size:.82rem;text-decoration:none;">Clear</a><?php endif; ?>
                </form>
            </div>
            <div class="table-responsive">
            <table class="table table-pink mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Last Login</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i=1; while($u = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td style="color:var(--text-muted);"><?php echo $i++; ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--pink-grad);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;">
                                <?php echo strtoupper(substr($u['username'],0,1)); ?>
                            </div>
                            <?php echo htmlspecialchars($u['username']); ?>
                        </div>
                    </td>
                    <td style="color:var(--text-muted);"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><span class="badge-pill badge-<?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span></td>
                    <td style="font-size:.8rem;"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                    <td style="font-size:.8rem;color:var(--text-muted);">
                        <?php echo $u['last_login'] ? date('M j, Y g:i a', strtotime($u['last_login'])) : '—'; ?>
                    </td>
                    <td>
                        <a href="users.php?delete=<?php echo $u['id']; ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Delete this user?')" style="border-radius:8px;">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
