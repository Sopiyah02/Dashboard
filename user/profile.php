<?php
include("auth_guard.php");
include("../config/database.php");

$msg = $msg_type = '';
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=".$_SESSION['user_id']));

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['update_profile'])) {
        $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
        $uname = trim(mysqli_real_escape_string($conn, $_POST['username']));
        if ($email && $uname) {
            $check = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT id FROM users WHERE (email='$email' OR username='$uname') AND id!=".$_SESSION['user_id']." LIMIT 1"
            ));
            if ($check) {
                $msg='Username or email already in use.'; $msg_type='error';
            } else {
                mysqli_query($conn,"UPDATE users SET email='$email',username='$uname' WHERE id=".$_SESSION['user_id']);
                $_SESSION['username']=$_POST['username'];
                $_SESSION['email']=$_POST['email'];
                $msg='Profile updated!'; $msg_type='success';
                $user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=".$_SESSION['user_id']));
            }
        }
    }
    if (isset($_POST['change_password'])) {
        $old = $_POST['old_password'];
        $new = $_POST['new_password'];
        $con = $_POST['confirm_password'];
        if (!password_verify($old,$user['password'])) {
            $msg='Current password is incorrect.'; $msg_type='error';
        } elseif (strlen($new)<6) {
            $msg='New password must be at least 6 characters.'; $msg_type='error';
        } elseif ($new!==$con) {
            $msg='New passwords do not match.'; $msg_type='error';
        } else {
            $hash=password_hash($new,PASSWORD_BCRYPT);
            mysqli_query($conn,"UPDATE users SET password='$hash' WHERE id=".$_SESSION['user_id']);
            $msg='Password changed!'; $msg_type='success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profile – QuizSystem</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-person-fill me-2"></i>My Profile</h5>
        <div class="user-chip"><div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div><?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>
    <div class="page-body">

        <?php if($msg): ?>
        <div class="alert alert-<?php echo $msg_type==='success'?'success-pink':'pink'; ?> mb-3 py-2 px-3 small">
            <i class="bi bi-<?php echo $msg_type==='success'?'check-circle':'exclamation-circle'; ?> me-2"></i><?php echo $msg; ?>
        </div>
        <?php endif; ?>

        <div class="row g-3">
            <!-- Avatar card -->
            <div class="col-md-4">
                <div class="card-box">
                    <div class="card-body-p text-center">
                        <div style="width:80px;height:80px;border-radius:50%;background:var(--pink-grad);color:#fff;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;margin:0 auto 16px;box-shadow:0 8px 20px rgba(233,30,140,.3);">
                            <?php echo strtoupper(substr($user['username'],0,1)); ?>
                        </div>
                        <h5 style="color:var(--pink-700);"><?php echo htmlspecialchars($user['username']); ?></h5>
                        <p style="color:var(--text-muted);font-size:.85rem;"><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="badge-pill badge-user">User Account</span>

                        <div style="background:var(--pink-50);border-radius:12px;padding:14px;margin-top:20px;text-align:left;">
                            <div class="d-flex justify-content-between py-1">
                                <span style="font-size:.8rem;color:var(--text-muted);">Joined</span>
                                <span style="font-size:.8rem;font-weight:600;"><?php echo date('M j, Y',strtotime($user['created_at'])); ?></span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span style="font-size:.8rem;color:var(--text-muted);">Last login</span>
                                <span style="font-size:.8rem;font-weight:600;"><?php echo $user['last_login']?date('M j, Y',strtotime($user['last_login'])):'—'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Update profile -->
                <div class="card-box mb-3">
                    <div class="card-head"><h6><i class="bi bi-pencil me-2"></i>Edit Profile</h6></div>
                    <div class="card-body-p">
                        <form method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <button class="btn-pink">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change password -->
                <div class="card-box">
                    <div class="card-head"><h6><i class="bi bi-lock-fill me-2"></i>Change Password</h6></div>
                    <div class="card-body-p">
                        <form method="POST">
                            <input type="hidden" name="change_password" value="1">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="old_password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" required minlength="6">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <button class="btn-pink">Update Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
