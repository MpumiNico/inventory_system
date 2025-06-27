<?php
session_start();
include 'config.php';

if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['username'] = $u;
        header('Location: index.php');
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }
        .card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            background: rgba(255,255,255,0.95);
        }
        .btn-primary {
            background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 16px rgba(106,17,203,0.15);
        }
        a {
            color: #2575fc;
            transition: color 0.2s;
        }
        a:hover {
            color: #6a11cb;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow p-4 animate__animated animate__fadeInUp" style="width: 100%; max-width: 400px;">
        <h2 class="mb-4 text-center"><i class="bi bi-box-arrow-in-right me-2"></i>Login</h2>
        <?php if (isset($error)) echo "<div class='alert alert-danger animate__animated animate__shakeX'>$error</div>"; ?>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" name="username" class="form-control" required autocomplete="username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>
            <button name="login" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
        </form>
        <div class="mt-3 text-center">
            <a href="registration.php"><i class="bi bi-person-plus me-1"></i>Don't have an account? Register</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
