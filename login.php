<?php
session_start();
include("config/database.php");

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/home.php' : 'user/home.php'));
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = trim($_POST['password']   ?? '');

    if ($identifier && $password) {
        $esc = mysqli_real_escape_string($conn, $identifier);
        $row = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT * FROM users WHERE email='$esc' OR username='$esc' LIMIT 1"
        ));
        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email']    = $row['email'];
            $_SESSION['role']     = $row['role'];
            // Update last login
            mysqli_query($conn, "UPDATE users SET last_login=NOW() WHERE id={$row['id']}");
            header("Location: " . ($row['role'] === 'admin' ? 'admin/home.php' : 'user/home.php'));
            exit;
        } else {
            $error = "Invalid username/email or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – QuizSystem</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo"><i class="bi bi-mortarboard-fill"></i></div>
        <h3>Welcome Back</h3>
        <p class="auth-sub">Sign in to your QuizSystem account</p>

        <?php if ($error): ?>
        <div class="alert alert-pink mb-3 py-2 px-3 small"><i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username or Email</label>
                <input type="text" name="identifier" class="form-control"
                       placeholder="Enter username or email"
                       value="<?php echo htmlspecialchars($_POST['identifier'] ?? ''); ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="passInput" class="form-control"
                           placeholder="Enter password" required>
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePass()" style="border-radius:0 10px 10px 0;border-color:var(--border);">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-pink w-100 py-2">Sign In <i class="bi bi-arrow-right ms-1"></i></button>
        </form>

        <p class="text-center mt-4 small" style="color:var(--text-muted);">
            Don't have an account? <a href="register.php" style="color:var(--pink-500);font-weight:600;">Register here</a>
        </p>
    </div>
</div>
<script>
function togglePass(){
    const i=document.getElementById('passInput');
    const e=document.getElementById('eyeIcon');
    if(i.type==='password'){i.type='text';e.className='bi bi-eye-slash';}
    else{i.type='password';e.className='bi bi-eye';}
}
</script>
</body>
</html>
