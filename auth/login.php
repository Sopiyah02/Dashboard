<?php
session_start();
include("../config/database.php");

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$error = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Get user by email
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (!$query) {
        die("Query Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($query) > 0) {

        $user = mysqli_fetch_assoc($query);

        // 👉 CHANGE THIS depending on your password type

        // ✅ If passwords are PLAIN TEXT (temporary)
        if ($user['password'] == $password) {

        // ✅ If passwords are HASHED (recommended)
        // if (password_verify($password, $user['password'])) {

            $_SESSION['user'] = $user;

            if ($user['role'] == 'admin') {
                header("Location: ../admin/index.php");
                exit();
            } else {
                header("Location: ../user/home.php");
                exit();
            }

        } else {
            $error = "Invalid email or password!";
        }

    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #ffe6f0;
    font-family: 'Segoe UI', sans-serif;
}

.card-login {
    width: 350px;
    margin: 100px auto;
    padding: 30px;
    border-radius: 20px;
    background: #fff0f8;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
}

.btn-dark-custom {
    background: linear-gradient(135deg, #d63384, #ff99c8);
    color: #fff;
}

.btn-dark-custom:hover {
    background: linear-gradient(135deg, #ff66a3, #ffb3d9);
    color: #fff;
}

.alert-danger {
    background-color: #ffc0dd;
    border-color: #ff99c8;
    color: #800040;
}
</style>
</head>

<body>

<div class="card-login">
    <h3 class="mb-3 text-center" style="color:#d63384;">Login</h3>

    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST">
        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

        <button type="submit" name="login" class="btn btn-dark-custom w-100">Login</button>
    </form>

    <p class="mt-3 text-center">
        No account? <a href="register.php" style="color:#d63384;">Register</a>
    </p>
</div>

</body>
</html>