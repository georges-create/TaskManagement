<?php
session_start();
require 'db.php';
$message = '';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate token & expiry
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)");
        $stmt->execute([$user['id'], $token, $expires]);

        // Send email (for demo, we just display link)
        $resetLink = "http://localhost/reset_password.php?token=$token";
        $message = "✅ Password reset link: <a href='$resetLink'>$resetLink</a> (valid for 1 hour)";
    } else {
        $message = "❌ Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/font.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center" style="height:100vh; background:#f8f9fa;">
    <div class="card p-4 shadow-sm" style="width:100%; max-width:400px;">
        <h3 class="text-center mb-4" style="color:#e91e63;">Forgot Password</h3>

        <?php if ($message) echo "<div class='toast-message'>$message</div>"; ?>

        <form method="post">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="d-grid">
                <button class="btn btn-danger" name="submit">Send Reset Link</button>
            </div>
            <p class="mt-3 text-center"><a href="login.php">Back to Login</a></p>
        </form>
    </div>
</body>

</html>