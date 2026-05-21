<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: manage_users.php');
    exit;
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    header('Location: manage_users.php');
    exit;
}

$msg = '';
if (isset($_POST['submit'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $role     = $_POST['role'];
    $program  = $_POST['program'] ?? null;
    $year     = $_POST['year'] ?? null;
    $semester = $_POST['semester'] ?? null;
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    // Check if email exists for another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=? AND id!=?");
    $stmt->execute([$email, $id]);
    if ($stmt->rowCount() > 0) {
        $msg = "❌ Email already exists!";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, password=?, role=?, program=?, year=?, semester=? WHERE id=?");
        $stmt->execute([$name, $email, $password, $role, $program, $year, $semester, $id]);
        // Redirect after successful update
        header("Location: manage_users.php?msg=" . urlencode("✅ User updated successfully!"));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/font.css" rel="stylesheet">
    <style>
        body {
            background: #fff0f6;
        }

        .btn-pink {
            background: #e91e63;
            color: #fff;
        }

        .btn-pink:hover {
            background: #d81b60;
        }

        .text-pink {
            color: #e91e63;
        }

        .navbar-pink {
            background: #e91e63 !important;
            color: #ffe4f0 !important;
        }

        .navbar-pink .navbar-brand,
        .navbar-pink .btn,
        .navbar-pink span {
            color: #ffe4f0 !important;
        }

        footer {
            background: #e91e63;
            color: #ffe4f0;
            text-align: center;
            padding: 10px;
        }
        
        
/* Large screen adjustments */
@media (min-width: 1400px) { .container { max-width: 1320px; } .table th, .table td { padding: 12px; font-size: 15px; } }
@media (min-width: 1600px) { .container { max-width: 1500px; } body { font-size: 17px; } .card { padding: 10px; } }
@media (min-width: 1920px) { .container { max-width: 1700px; } body { font-size: 18px; } h3 { font-size: 28px; } .btn { font-size: 16px; padding: 8px 18px; } }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-pink shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <a href="logout.php" class="btn btn-outline-light ms-auto">Logout</a>
        </div>
    </nav>

    <main class="container flex-grow-1 mb-5">
        <div class="mb-3">
            <a href="../admin/dashboard.php" class="btn btn-outline-primary" style="background-color:#d81b60;color:#ffe4f0">
                ← Back to Admin
            </a>
        </div>
        <h3 class="text-pink mb-3">Edit User</h3>
        <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

        <div class="card shadow-sm p-4">
            <form method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Password (leave blank to keep)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" id="roleSelect" required>
                            <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>Student</option>
                            <option value="lecturer" <?= $user['role'] == 'lecturer' ? 'selected' : '' ?>>Lecturer</option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <!-- Student specific fields -->
                    <div id="studentFields" style="display:<?= $user['role'] == 'student' ? 'flex' : 'none' ?>;">
                        <div class="col-md-4">
                            <label class="form-label">Program</label>
                            <select name="program" class="form-select">
                                <option value="">Select Program</option>
                                <option value="BCSIT" <?= $user['program'] == 'BCSIT' ? 'selected' : '' ?>>BCSIT</option>
                                <option value="BBIT" <?= $user['program'] == 'BBIT' ? 'selected' : '' ?>>BBIT</option>
                                <option value="BED" <?= $user['program'] == 'BED' ? 'selected' : '' ?>>BED</option>
                                <option value="BBM" <?= $user['program'] == 'BBM' ? 'selected' : '' ?>>BBM</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Year</label>
                            <select name="year" class="form-select">
                                <option value="">Select Year</option>
                                <option value="Y1" <?= $user['year'] == 'Y1' ? 'selected' : '' ?>>Y1</option>
                                <option value="Y2" <?= $user['year'] == 'Y2' ? 'selected' : '' ?>>Y2</option>
                                <option value="Y3" <?= $user['year'] == 'Y3' ? 'selected' : '' ?>>Y3</option>
                                <option value="Y4" <?= $user['year'] == 'Y4' ? 'selected' : '' ?>>Y4</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select">
                                <option value="">Select Semester</option>
                                <option value="SEM1" <?= $user['semester'] == 'SEM1' ? 'selected' : '' ?>>SEM1</option>
                                <option value="SEM2" <?= $user['semester'] == 'SEM2' ? 'selected' : '' ?>>SEM2</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" name="submit" class="btn btn-pink">Update User</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <footer>© <?= date('Y') ?> TMS. All rights reserved.</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const roleSelect = document.getElementById('roleSelect');
        const studentFields = document.getElementById('studentFields');

        roleSelect.addEventListener('change', function() {
            studentFields.style.display = this.value === 'student' ? 'flex' : 'none';
        });
    </script>
</body>

</html>