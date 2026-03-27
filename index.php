<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MindStack</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: url('https://i.pinimg.com/1200x/e9/6a/ef/e96aeff8aca564a56685729278f89249.jpg') repeat;
    background-size: cover;
    font-family: 'Segoe UI', sans-serif;
}

.hero {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.card-home {
    background: #fff0f8;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 12px 30px rgba(255, 105, 180, 0.3);
    text-align: center;
    border: 2px solid #ff99c8;
}

.card-home h2 {
    color: #d63384;
    font-weight: 700;
}

.card-home p {
    color: #ff66a3;
}

.btn-pink {
    background: linear-gradient(135deg, #d63384, #ff99c8);
    color: #fff;
    border-radius: 10px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-pink:hover {
    background: linear-gradient(135deg, #ff66a3, #ffb3d9);
    color: #fff;
}

.btn-outline-pink {
    border: 2px solid #d63384;
    color: #d63384;
    border-radius: 10px;
    transition: 0.3s;
}

.btn-outline-pink:hover {
    background: #ffccdd;
    color: #d63384;
}
</style>
</head>

<body>

<div class="hero">
    <div class="card-home">
        <h2 class="mb-3">MindStack</h2>
        <p class="text-muted">Practice programming with interactive flashcards</p>

        <div class="mt-4">
            <a href="auth/login.php" class="btn btn-pink w-100 mb-2">Login</a>
            <a href="auth/register.php" class="btn btn-outline-pink w-100">Create Account</a>
        </div>
    </div>
</div>

</body>
</html>