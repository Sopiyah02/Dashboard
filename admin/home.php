<?php
include("auth_guard.php");
include("../config/database.php");

$total_users    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users WHERE role='user'"))['c'];
$total_q        = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM questions"))['c'];
$total_lang     = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM languages"))['c'];
$total_fb       = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM feedback"))['c'];
$unread_fb      = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM feedback WHERE is_read=0"))['c'];

// Recent users
$recent_users = mysqli_query($conn,
    "SELECT username,email,created_at FROM users WHERE role='user' ORDER BY created_at DESC LIMIT 5");

// Recent feedback
$recent_fb = mysqli_query($conn,
    "SELECT username,category,rating,message,created_at,is_read FROM feedback ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin – Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-speedometer2 me-2"></i>Dashboard</h5>
        <div class="user-chip">
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div>
            <?php echo htmlspecialchars($_SESSION['username']); ?> &nbsp;<span style="opacity:.5;font-size:.75rem;">Admin</span>
        </div>
    </div>
    <div class="page-body">

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card gradient">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-num"><?php echo $total_users; ?></div>
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card white">
                    <div class="stat-label">Questions</div>
                    <div class="stat-num"><?php echo $total_q; ?></div>
                    <div class="stat-icon"><i class="bi bi-patch-question-fill"></i></div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card white">
                    <div class="stat-label">Languages</div>
                    <div class="stat-num"><?php echo $total_lang; ?></div>
                    <div class="stat-icon"><i class="bi bi-translate"></i></div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card white">
                    <div class="stat-label">Feedback<?php if($unread_fb>0): ?> <span style="color:var(--pink-500);font-size:.75rem;">(<?php echo $unread_fb; ?> new)</span><?php endif; ?></div>
                    <div class="stat-num"><?php echo $total_fb; ?></div>
                    <div class="stat-icon"><i class="bi bi-chat-heart-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Recent Users -->
            <div class="col-lg-6">
                <div class="card-box">
                    <div class="card-head">
                        <h6><i class="bi bi-people me-2"></i>Recent Registrations</h6>
                        <a href="users.php" class="btn-outline-pink btn-sm" style="font-size:.78rem;padding:5px 12px;border-radius:8px;text-decoration:none;">View All</a>
                    </div>
                    <table class="table table-pink mb-0">
                        <thead><tr><th>Username</th><th>Email</th><th>Joined</th></tr></thead>
                        <tbody>
                        <?php while($u = mysqli_fetch_assoc($recent_users)): ?>
                        <tr>
                            <td><i class="bi bi-person-circle me-1" style="color:var(--pink-400);"></i><?php echo htmlspecialchars($u['username']); ?></td>
                            <td style="font-size:.8rem;color:var(--text-muted);"><?php echo htmlspecialchars($u['email']); ?></td>
                            <td style="font-size:.78rem;color:var(--text-muted);"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Feedback -->
            <div class="col-lg-6">
                <div class="card-box">
                    <div class="card-head">
                        <h6><i class="bi bi-chat-heart me-2"></i>Recent Feedback</h6>
                        <a href="feedback.php" class="btn-outline-pink btn-sm" style="font-size:.78rem;padding:5px 12px;border-radius:8px;text-decoration:none;">View All</a>
                    </div>
                    <table class="table table-pink mb-0">
                        <thead><tr><th>User</th><th>Category</th><th>Rating</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php while($f = mysqli_fetch_assoc($recent_fb)): ?>
                        <tr class="<?php echo !$f['is_read']?'':''; ?>">
                            <td><?php echo htmlspecialchars($f['username']); ?></td>
                            <td><span class="badge-pill badge-general"><?php echo htmlspecialchars($f['category']); ?></span></td>
                            <td><?php for($s=1;$s<=5;$s++) echo "<i class='bi bi-star-fill' style='font-size:.7rem;color:".($s<=$f['rating']?'var(--pink-400)':'#eee')."'></i>"; ?></td>
                            <td><?php echo $f['is_read']
                                ? '<span class="badge-pill" style="background:#f5f5f5;color:#999;">Read</span>'
                                : '<span class="badge-pill" style="background:#fce4ec;color:#c2185b;">New</span>'; ?>
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
