<?php
include("auth_guard.php");
include("../config/database.php");

$msg = $msg_type = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['send_fb'])) {
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? 'General');
    $rating   = intval($_POST['rating'] ?? 0);
    $message  = trim(mysqli_real_escape_string($conn, $_POST['message'] ?? ''));
    $uid      = $_SESSION['user_id'];
    $uname    = mysqli_real_escape_string($conn, $_SESSION['username']);

    if (strlen($message) >= 10) {
        mysqli_query($conn,
            "INSERT INTO feedback (user_id,username,category,rating,message)
             VALUES ($uid,'$uname','$category',$rating,'$message')"
        );
        $msg='Thank you! Your feedback has been submitted.';
        $msg_type='success';
    } else {
        $msg='Message must be at least 10 characters.';
        $msg_type='error';
    }
}

// My past feedback
$my_fb = mysqli_query($conn,
    "SELECT * FROM feedback WHERE user_id=".$_SESSION['user_id']." ORDER BY created_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Feedback – QuizSystem</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-chat-heart-fill me-2"></i>Send Feedback</h5>
        <div class="user-chip"><div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div><?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>
    <div class="page-body">

        <div class="row g-3">
            <!-- Form -->
            <div class="col-md-5">
                <div class="card-box">
                    <div class="card-head"><h6><i class="bi bi-pencil-fill me-2"></i>New Report</h6></div>
                    <div class="card-body-p">

                        <?php if($msg): ?>
                        <div class="alert alert-<?php echo $msg_type==='success'?'success-pink':'pink'; ?> mb-3 py-2 px-3 small">
                            <i class="bi bi-<?php echo $msg_type==='success'?'check-circle':'exclamation-circle'; ?> me-2"></i><?php echo $msg; ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="send_fb" value="1">

                            <div class="mb-3">
                                <label class="form-label">Report Type</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select a type…</option>
                                    <option value="Bug Report">🐛 Bug Report – Something is broken</option>
                                    <option value="UI Issue">🎨 UI Issue – Layout or design problem</option>
                                    <option value="Feature Request">💡 Feature Request – Suggest an improvement</option>
                                    <option value="Performance">⚡ Performance – Slow loading or lag</option>
                                    <option value="Wrong Answer">❌ Wrong Answer – Incorrect question/answer</option>
                                    <option value="General">💬 General – Any other feedback</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Overall Rating</label>
                                <div class="star-rating">
                                    <?php for($s=5;$s>=1;$s--): ?>
                                    <input type="radio" name="rating" id="s<?php echo $s; ?>" value="<?php echo $s; ?>" <?php echo $s===3?'checked':''; ?>>
                                    <label for="s<?php echo $s; ?>"><i class="bi bi-star-fill"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Describe the Issue <span style="color:var(--pink-500);">*</span></label>
                                <textarea name="message" class="form-control" rows="5"
                                    placeholder="Describe what happened, what you expected, and steps to reproduce (if it's a bug)…"
                                    required minlength="10"></textarea>
                                <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Minimum 10 characters</div>
                            </div>

                            <button type="submit" class="btn-pink w-100 py-2">
                                <i class="bi bi-send me-2"></i>Submit Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- My past feedback -->
            <div class="col-md-7">
                <div class="card-box">
                    <div class="card-head"><h6><i class="bi bi-clock-history me-2"></i>My Previous Reports</h6></div>
                    <?php
                    $rows = [];
                    while($f=mysqli_fetch_assoc($my_fb)) $rows[]=$f;
                    ?>
                    <?php if(empty($rows)): ?>
                    <div class="card-body-p text-center" style="color:var(--text-muted);padding:50px;">
                        <i class="bi bi-chat-heart fs-1 d-block mb-2" style="color:var(--pink-200);"></i>
                        No feedback submitted yet.<br><small>Your reports will appear here.</small>
                    </div>
                    <?php else: ?>
                    <div style="padding:16px;">
                    <?php foreach($rows as $f): ?>
                    <div style="background:var(--pink-50);border-radius:14px;padding:16px;margin-bottom:12px;border:1px solid var(--border);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge-pill badge-general" style="font-size:.72rem;"><?php echo htmlspecialchars($f['category']); ?></span>
                            <span style="font-size:.75rem;color:var(--text-muted);"><?php echo date('M j, Y',strtotime($f['created_at'])); ?></span>
                        </div>
                        <?php if($f['rating']>0): ?>
                        <div style="margin-bottom:6px;">
                            <?php for($s=1;$s<=5;$s++) echo "<i class='bi bi-star-fill' style='font-size:.75rem;color:".($s<=$f['rating']?'#f9a825':'#eee')."'></i>"; ?>
                        </div>
                        <?php endif; ?>
                        <p style="font-size:.85rem;color:var(--text);margin:0;line-height:1.5;"><?php echo nl2br(htmlspecialchars($f['message'])); ?></p>
                        <div style="margin-top:8px;">
                            <?php echo $f['is_read']
                                ? '<span style="font-size:.72rem;color:var(--text-muted);"><i class="bi bi-check2-all me-1"></i>Seen by admin</span>'
                                : '<span style="font-size:.72rem;color:var(--pink-500);"><i class="bi bi-clock me-1"></i>Pending review</span>'; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
