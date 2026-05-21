<?php
session_start();
require 'db.php';
$message = '';

if (!isset($_GET['token'])) {
    header('Location: login.php');
    exit;
}

$token = $_GET['token'];

// Verify token
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token=? AND expires_at>=NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    $message = "❌ Invalid or expired token.";
}

if (isset($_POST['submit'])) {
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    if ($password !== $confirm) {
        $message = "❌ Passwords do not match.";
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Update user password
        $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->execute([$password_hashed, $reset['user_id']]);

        // Delete used token
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE id=?");
        $stmt->execute([$reset['id']]);

        $_SESSION['success_message'] = "✅ Password reset successful! Please login.";
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/font.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center" style="height:100vh; background:#f8f9fa;">
    <div class="card p-4 shadow-sm" style="width:100%; max-width:400px;">
        <h3 class="text-center mb-4" style="color:#e91e63;">Reset Password</h3>

        <?php if ($message) echo "<div class='toast-message'>$message</div>"; ?>

        <?php if ($reset): ?>
            <form method="post">
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="New Password" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" required>
                </div>
                <div class="d-grid">
                    <button class="btn btn-danger" name="submit">Reset Password</button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-center">Please request a new password reset.</p>
            <a href="forgot_password.php" class="btn btn-pink w-100 mt-3">Forgot Password</a>
        <?php endif; ?>

    </div>
</body>

</html>