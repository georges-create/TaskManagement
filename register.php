<?php
session_start();
require 'db.php';

$message = '';
$redirect = false;

if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = $_POST['role'];

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $message = '❌ Email already in use. Please login or use a different email.';
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // For students, collect program, year, semester
        $program  = $role === 'student' ? $_POST['program'] : null;
        $year     = $role === 'student' ? $_POST['year'] : null;
        $semester = $role === 'student' ? $_POST['semester'] : null;

        $stmt = $pdo->prepare("
            INSERT INTO users (name,email,password,role,program,year,semester)
            VALUES (?,?,?,?,?,?,?)
        ");
        $stmt->execute([$name, $email, $password_hashed, $role, $program, $year, $semester]);

        // Registration successful, redirect to login with message
        $_SESSION['success_message'] = '✅ Registration successful! Please login.';
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
    <title>Register</title>
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
        }

        .navbar-pink {
            background: #e91e63 !important;
        }

        .navbar-pink .navbar-brand {
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
            border: none;
        }

        .toast-message {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
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

    

    <main class="container d-flex align-items-center justify-content-center" style="flex:1;">
        <div class="card p-4 shadow-sm" style="width: 100%; max-width: 450px;">
            <h3 class="text-center mb-4" style="color:#e91e63;">Register</h3>

            <?php if ($message): ?>
                <div class="toast-message <?= strpos($message, '❌') !== false ? 'toast-danger' : 'toast-success' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Full Name" required></div>
                <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>

                <div class="mb-3">
                    <select name="role" class="form-select" id="roleSelect" required>
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                    </select>
                </div>

                <!-- Student specific fields -->
                <div id="studentFields" style="display:none;">
                    <div class="mb-3">
                        <select name="program" class="form-select">
                            <option value="">Select Program</option>
                            <option value="BCSIT">BCSIT</option>
                            <option value="BBIT">BBIT</option>
                            <option value="BED">BED</option>
                            <option value="BBM">BBM</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="year" class="form-select">
                            <option value="">Select Year</option>
                            <option value="Y1">Y1</option>
                            <option value="Y2">Y2</option>
                            <option value="Y3">Y3</option>
                            <option value="Y4">Y4</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="semester" class="form-select">
                            <option value="">Select Semester</option>
                            <option value="SEM1">SEM1</option>
                            <option value="SEM2">SEM2</option>
                        </select>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-pink" name="register">Register</button>
                </div>
                <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>© <?= date('Y') ?> TMS. All rights reserved.</footer>

    <script>
        document.getElementById('roleSelect').addEventListener('change', function() {
            document.getElementById('studentFields').style.display = this.value === 'student' ? 'block' : 'none';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>