<?php
include("../config/database.php");

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    mysqli_query($conn,"INSERT INTO users (name,email,password,role) 
                        VALUES ('$name','$email','$password','user')");

    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #ffe6f0;
    font-family: 'Segoe UI', sans-serif;
}

.card-register {
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
</style>
</head>

<body>

<div class="card-register">
    <h3 class="mb-3 text-center" style="color:#d63384;">Register</h3>

    <form method="POST">
        <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>
        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

        <button name="register" class="btn btn-dark-custom w-100">Create Account</button>
    </form>

    <p class="mt-3 text-center">
        Already have an account? <a href="login.php" style="color:#d63384;">Login</a>
    </p>
</div>

</body>
</html>