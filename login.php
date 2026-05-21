<?php
session_start();
require 'db.php';

$message = '';

// Handle success message from registration or password reset
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Handle login
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['name'];

        // For students, store program info in session
        if ($user['role'] === 'student') {
            $_SESSION['program']  = $user['program'];
            $_SESSION['year']     = $user['year'];
            $_SESSION['semester'] = $user['semester'];
        }

        // Redirect by role
        switch ($user['role']) {
            case 'admin':
                header('Location: admin/dashboard.php');
                break;
            case 'lecturer':
                header('Location: lecturer/dashboard.php');
                break;
            case 'student':
                header('Location: student/dashboard.php');
                break;
        }
        exit;
    } else {
        $message = '❌ Invalid email or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/font.css" rel="stylesheet">
    <style>
        body {
            background: #fff0f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .navbar-pink {
            background: #e91e63 !important;
        }

        .navbar-pink .navbar-brand,
        .navbar-pink span {
            color: #ffe4f0 !important;
        }

        .btn-pink {
            background: #e91e63;
            color: #fff;
        }

        .btn-pink:hover {
            background: #d81b60;
            color: #fff;
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
            max-width: 400px;
            width: 100%;
        }

        .toast-message {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .toast-success {
            background: #d4edda;
            color: #155724;
        }

        .toast-danger {
            background: #f8d7da;
            color: #721c24;
        }

        footer {
            background: #e91e63;
            color: #ffe4f0;
            text-align: center;
            padding: 10px 0;
            font-size: 14px;
        }
    </style>
</head>

<body>

    

    <main>
        <div class="card p-4">
            <h3 class="text-center mb-4" style="color:#e91e63;">Login</h3>

            <?php if ($success_message): ?>
                <div class="toast-message toast-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="toast-message toast-danger"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="d-grid mb-2">
                    <button class="btn btn-pink" name="login">Login</button>
                </div>
                <p class="text-center mb-1">
                    <a href="forgot_password.php">Forgot Password?</a>
                </p>
                <p class="text-center">Don't have an account? <a href="register.php">Register</a></p>
            </form>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        © <?= date('Y') ?> TMS. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>