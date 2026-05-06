<?php
include("auth_guard.php");
include("../config/database.php");

if (isset($_GET['mark_read'])) {
    mysqli_query($conn,"UPDATE feedback SET is_read=1 WHERE id=".intval($_GET['mark_read']));
    header("Location: feedback.php"); exit;
}
if (isset($_GET['mark_all'])) {
    mysqli_query($conn,"UPDATE feedback SET is_read=1");
    header("Location: feedback.php"); exit;
}
if (isset($_GET['delete'])) {
    mysqli_query($conn,"DELETE FROM feedback WHERE id=".intval($_GET['delete']));
    header("Location: feedback.php"); exit;
}

$filter_cat  = $_GET['cat']    ?? '';
$filter_rate = intval($_GET['rate'] ?? 0);
$filter_stat = $_GET['stat']   ?? '';
$search      = trim($_GET['s'] ?? '');
$where_parts = [];
if ($filter_cat)  $where_parts[] = "category='".mysqli_real_escape_string($conn,$filter_cat)."'";
if ($filter_rate) $where_parts[] = "rating=$filter_rate";
if ($filter_stat==='unread') $where_parts[] = "is_read=0";
if ($filter_stat==='read')   $where_parts[] = "is_read=1";
if ($search) { $es=mysqli_real_escape_string($conn,$search); $where_parts[]="(message LIKE '%$es%' OR username LIKE '%$es%')"; }
$where = $where_parts ? "WHERE ".implode(" AND ",$where_parts) : "";

$feedback = mysqli_query($conn,"SELECT * FROM feedback $where ORDER BY created_at DESC");
$total    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM feedback"))['c'];
$unread   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM feedback WHERE is_read=0"))['c'];
$avg      = mysqli_fetch_assoc(mysqli_query($conn,"SELECT AVG(rating) a FROM feedback WHERE rating>0"))['a'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin – Feedback</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-chat-heart-fill me-2"></i>Feedback</h5>
        <div class="d-flex gap-2 align-items-center">
            <?php if($unread>0): ?>
            <a href="?mark_all=1" class="btn-outline-pink" style="font-size:.8rem;padding:6px 14px;text-decoration:none;border-radius:10px;">
                <i class="bi bi-check2-all me-1"></i>Mark all read
            </a>
            <?php endif; ?>
            <div class="user-chip"><div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div><?php echo htmlspecialchars($_SESSION['username']); ?></div>
        </div>
    </div>
    <div class="page-body">

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="stat-card gradient">
                    <div class="stat-label">Total</div>
                    <div class="stat-num"><?php echo $total; ?></div>
                    <div class="stat-icon"><i class="bi bi-chat-heart-fill"></i></div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card white">
                    <div class="stat-label">Unread</div>
                    <div class="stat-num" style="color:var(--pink-500);"><?php echo $unread; ?></div>
                    <div class="stat-icon"><i class="bi bi-envelope-fill"></i></div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card white">
                    <div class="stat-label">Avg Rating</div>
                    <div class="stat-num" style="color:#f9a825;"><?php echo $avg ? '★ '.round($avg,1) : '—'; ?></div>
                    <div class="stat-icon"><i class="bi bi-star-fill"></i></div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="card-box mb-3">
            <div class="card-body-p">
                <form method="GET" class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="s" class="form-control form-control-sm" placeholder="Search user or message…" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="cat" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <?php foreach(['Bug Report','Feature Request','UI Issue','Performance','General','Other'] as $fc): ?>
                            <option value="<?php echo $fc; ?>" <?php if($filter_cat===$fc) echo 'selected'; ?>><?php echo $fc; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="rate" class="form-select form-select-sm">
                            <option value="0">All Ratings</option>
                            <?php for($r=5;$r>=1;$r--): ?>
                            <option value="<?php echo $r; ?>" <?php if($filter_rate==$r) echo 'selected'; ?>><?php echo $r; ?> ★</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="stat" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="unread" <?php if($filter_stat==='unread') echo 'selected'; ?>>Unread</option>
                            <option value="read"   <?php if($filter_stat==='read')   echo 'selected'; ?>>Read</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn-pink" style="padding:6px 16px;font-size:.83rem;"><i class="bi bi-filter me-1"></i>Filter</button>
                        <a href="feedback.php" class="btn-outline-pink" style="padding:6px 14px;font-size:.83rem;text-decoration:none;border-radius:10px;">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card-box">
            <?php $rows = []; while($f=mysqli_fetch_assoc($feedback)) $rows[]=$f; ?>
            <?php if(empty($rows)): ?>
            <div class="card-body-p text-center" style="color:var(--text-muted);padding:60px;">
                <i class="bi bi-inbox fs-1 d-block mb-2" style="color:var(--pink-200);"></i>No feedback found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
            <table class="table table-pink mb-0">
                <thead><tr><th>#</th><th>User</th><th>Category</th><th>Rating</th><th>Message</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach($rows as $i=>$f): ?>
                <tr style="cursor:pointer;" class="<?php echo !$f['is_read']?'':''; ?>"
                    data-bs-toggle="modal" data-bs-target="#fm<?php echo $f['id']; ?>">
                    <td><?php echo $i+1; ?></td>
                    <td>
                        <?php if(!$f['is_read']): ?><span style="color:var(--pink-500);margin-right:4px;">●</span><?php endif; ?>
                        <?php echo htmlspecialchars($f['username']); ?>
                    </td>
                    <td><span class="badge-pill badge-general" style="font-size:.7rem;"><?php echo htmlspecialchars($f['category']); ?></span></td>
                    <td>
                        <?php if($f['rating']>0): for($s=1;$s<=5;$s++) echo "<i class='bi bi-star-fill' style='font-size:.7rem;color:".($s<=$f['rating']?'#f9a825':'#eee')."'></i>"; else echo '<span style="color:#ccc;">—</span>'; ?>
                    </td>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($f['message']); ?></td>
                    <td style="font-size:.78rem;color:var(--text-muted);"><?php echo date('M j, Y',strtotime($f['created_at'])); ?></td>
                    <td>
                        <?php echo $f['is_read']
                            ? '<span class="badge-pill" style="background:#f5f5f5;color:#999;font-size:.7rem;">Read</span>'
                            : '<span class="badge-pill" style="background:#fce4ec;color:#c2185b;font-size:.7rem;">New</span>'; ?>
                    </td>
                    <td onclick="event.stopPropagation();">
                        <?php if(!$f['is_read']): ?>
                        <a href="?mark_read=<?php echo $f['id']; ?>" class="btn btn-sm btn-outline-success me-1" style="border-radius:8px;"><i class="bi bi-check2"></i></a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $f['id']; ?>" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Modals -->
<?php foreach($rows as $f): ?>
<div class="modal fade" id="fm<?php echo $f['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-pink">
                <span class="modal-title-pink"><i class="bi bi-chat-heart me-2"></i>Feedback from <?php echo htmlspecialchars($f['username']); ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-6"><span class="form-label d-block">Category</span><strong><?php echo htmlspecialchars($f['category']); ?></strong></div>
                    <div class="col-6"><span class="form-label d-block">Rating</span>
                        <?php if($f['rating']>0): for($s=1;$s<=5;$s++) echo "<i class='bi bi-star-fill' style='color:".($s<=$f['rating']?'#f9a825':'#eee')."'></i>"; echo " ({$f['rating']}/5)"; else echo '<span style="color:#ccc;">Not rated</span>'; ?>
                    </div>
                    <div class="col-6"><span class="form-label d-block">Submitted</span><strong><?php echo date('M j, Y g:i a',strtotime($f['created_at'])); ?></strong></div>
                    <div class="col-6"><span class="form-label d-block">Status</span>
                        <?php echo $f['is_read']?'<span class="badge-pill" style="background:#f5f5f5;color:#999;">Read</span>':'<span class="badge-pill" style="background:#fce4ec;color:#c2185b;">Unread</span>'; ?>
                    </div>
                </div>
                <hr style="border-color:var(--border);">
                <span class="form-label d-block">Message</span>
                <p style="white-space:pre-wrap;color:var(--text);"><?php echo htmlspecialchars($f['message']); ?></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <?php if(!$f['is_read']): ?><a href="?mark_read=<?php echo $f['id']; ?>" class="btn btn-sm btn-outline-success" style="border-radius:8px;"><i class="bi bi-check2 me-1"></i>Mark Read</a><?php endif; ?>
                <a href="?delete=<?php echo $f['id']; ?>" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="return confirm('Delete?')"><i class="bi bi-trash me-1"></i>Delete</a>
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
