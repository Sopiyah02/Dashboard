<?php
include("auth_guard.php");
include("../config/database.php");

$total_q    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM questions"))['c'];
$total_lang = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM languages"))['c'];
$total_cat  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM categories"))['c'];
$my_fb      = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM feedback WHERE user_id=".$_SESSION['user_id']))['c'];

// Joined date
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=".$_SESSION['user_id']));

// Available languages
$langs = mysqli_query($conn,
    "SELECT l.name, COUNT(q.id) cnt FROM languages l
     LEFT JOIN questions q ON q.language_id=l.id
     GROUP BY l.id ORDER BY cnt DESC LIMIT 6"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard – QuizSystem</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-house-fill me-2"></i>Dashboard</h5>
        <div class="user-chip">
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div>
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </div>
    </div>
    <div class="page-body">

        <!-- Welcome banner -->
        <div class="stat-card gradient mb-4" style="border-radius:20px;padding:28px 32px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 style="color:#fff;font-size:1.5rem;margin-bottom:6px;">
                        Hello, <?php echo htmlspecialchars($user['username']); ?>! 👋
                    </h4>
                    <p style="color:rgba(255,255,255,.8);margin:0;">
                        Ready to test your knowledge? <?php echo $total_q; ?> questions await you.
                    </p>
                </div>
                <a href="quiz.php" class="btn" style="background:rgba(255,255,255,.2);color:#fff;border:1.5px solid rgba(255,255,255,.5);border-radius:12px;padding:10px 22px;font-weight:600;text-decoration:none;backdrop-filter:blur(10px);">
                    <i class="bi bi-play-fill me-1"></i> Start Quiz
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-3 mb-4">
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
                    <div class="stat-label">Difficulties</div>
                    <div class="stat-num"><?php echo $total_cat; ?></div>
                    <div class="stat-icon"><i class="bi bi-bar-chart-fill"></i></div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card white">
                    <div class="stat-label">My Feedback</div>
                    <div class="stat-num"><?php echo $my_fb; ?></div>
                    <div class="stat-icon"><i class="bi bi-chat-heart-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Available Languages -->
            <div class="col-md-7">
                <div class="card-box">
                    <div class="card-head">
                        <h6><i class="bi bi-translate me-2"></i>Available Topics</h6>
                        <a href="quiz.php" class="btn-outline-pink" style="font-size:.78rem;padding:5px 12px;border-radius:8px;text-decoration:none;">Take Quiz</a>
                    </div>
                    <div class="card-body-p">
                    <div class="row g-2">
                    <?php while($l=mysqli_fetch_assoc($langs)): ?>
                    <div class="col-6">
                        <div style="background:var(--pink-50);border-radius:12px;padding:14px;border:1px solid var(--border);display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;background:var(--pink-grad);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;">
                                <i class="bi bi-code-slash"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.88rem;"><?php echo htmlspecialchars($l['name']); ?></div>
                                <div style="font-size:.75rem;color:var(--text-muted);"><?php echo $l['cnt']; ?> question<?php echo $l['cnt']!=1?'s':''; ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="col-md-5">
                <div class="card-box">
                    <div class="card-head">
                        <h6><i class="bi bi-person-fill me-2"></i>My Account</h6>
                        <a href="profile.php" class="btn-outline-pink" style="font-size:.78rem;padding:5px 12px;border-radius:8px;text-decoration:none;">Edit</a>
                    </div>
                    <div class="card-body-p">
                        <div style="text-align:center;margin-bottom:16px;">
                            <div style="width:60px;height:60px;border-radius:50%;background:var(--pink-grad);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;margin:0 auto 10px;">
                                <?php echo strtoupper(substr($user['username'],0,1)); ?>
                            </div>
                            <div style="font-weight:700;"><?php echo htmlspecialchars($user['username']); ?></div>
                            <div style="font-size:.8rem;color:var(--text-muted);"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div style="background:var(--pink-50);border-radius:10px;padding:12px;">
                            <div class="d-flex justify-content-between py-1">
                                <span style="font-size:.82rem;color:var(--text-muted);">Member since</span>
                                <span style="font-size:.82rem;font-weight:600;"><?php echo date('M j, Y',strtotime($user['created_at'])); ?></span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span style="font-size:.82rem;color:var(--text-muted);">Last login</span>
                                <span style="font-size:.82rem;font-weight:600;"><?php echo $user['last_login']?date('M j',strtotime($user['last_login'])):'Today'; ?></span>
                            </div>
                        </div>
                        <a href="feedback.php" class="btn-pink w-100 text-center d-block mt-3" style="text-decoration:none;padding:10px;">
                            <i class="bi bi-chat-heart me-1"></i> Send Feedback
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
