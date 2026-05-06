<?php
session_start();
include("config/database.php");

if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/home.php' : 'user/home.php'));
    exit;
}

$error = $success = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (!$username || !$email || !$password || !$confirm) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $eu = mysqli_real_escape_string($conn, $username);
        $ee = mysqli_real_escape_string($conn, $email);
        $check = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT id FROM users WHERE username='$eu' OR email='$ee' LIMIT 1"
        ));
        if ($check) {
            $error = "Username or email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            mysqli_query($conn,
                "INSERT INTO users (username,email,password,role) VALUES ('$eu','$ee','$hash','user')"
            );
            $success = "Account created! You can now <a href='login.php' style='color:var(--pink-500);font-weight:600;'>log in</a>.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register – QuizSystem</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo"><i class="bi bi-person-plus-fill"></i></div>
        <h3>Create Account</h3>
        <p class="auth-sub">Join QuizSystem today</p>

        <?php if ($error): ?>
        <div class="alert alert-pink mb-3 py-2 px-3 small"><i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success-pink mb-3 py-2 px-3 small"><i class="bi bi-check-circle me-2"></i><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Choose a username"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="your@email.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min. 6 characters" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm" class="form-control"
                       placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn-pink w-100 py-2">Create Account <i class="bi bi-arrow-right ms-1"></i></button>
        </form>

        <p class="text-center mt-4 small" style="color:var(--text-muted);">
            Already have an account? <a href="login.php" style="color:var(--pink-500);font-weight:600;">Sign in</a>
        </p>
    </div>
</div>
</body>
</html>
