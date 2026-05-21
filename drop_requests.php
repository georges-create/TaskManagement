<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit;
}

$student_id = (int) $_SESSION['user_id'];
$name       = $_SESSION['name'];
$program    = $_SESSION['program'] ?? '';
$year       = $_SESSION['year'] ?? '';
$semester   = $_SESSION['semester'] ?? '';

/* ================= KPI COUNTS ================= */
// Total units for the student's program, year, and semester
$totalUnitsStmt = $pdo->prepare("SELECT COUNT(*) FROM units WHERE program=? AND year=? AND semester=?");
$totalUnitsStmt->execute([$program, $year, $semester]);
$totalUnits = $totalUnitsStmt->fetchColumn();

// Pending drop requests
$pendingDropsStmt = $pdo->prepare("SELECT COUNT(*) FROM drop_requests WHERE user_id=? AND status='pending'");
$pendingDropsStmt->execute([$student_id]);
$pendingDrops = $pendingDropsStmt->fetchColumn();

/* ================= FETCH NOTIFICATIONS FOR CURRENT USER ONLY ================= */
$notificationsStmt = $pdo->prepare("
    SELECT * FROM notifications
    WHERE user_id = :uid
    ORDER BY is_read ASC, created_at DESC
    LIMIT 5
");
$notificationsStmt->execute([':uid' => $student_id]);
$notifications = $notificationsStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

        .navbar-pink .navbar-brand,
        .navbar-pink span {
            color: #ffe4f0 !important;
        }

        .text-pink {
            color: #e91e63 !important;
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
        }

        footer {
            background: #e91e63;
            color: #ffe4f0;
            font-size: 14px;
            text-align: center;
        }

        /* Highlight unread notifications */
        .notification-unread {
            background-color: #ffe4f0;
            font-weight: 600;
        }
        
/* Large screen adjustments */
@media (min-width: 1400px) { .container { max-width: 1320px; } .table th, .table td { padding: 12px; font-size: 15px; } }
@media (min-width: 1600px) { .container { max-width: 1500px; } body { font-size: 17px; } .card { padding: 10px; } }
@media (min-width: 1920px) { .container { max-width: 1700px; } body { font-size: 18px; } h3 { font-size: 28px; } .btn { font-size: 16px; padding: 8px 18px; } }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-pink shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">Student Panel</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#studentNav">
                <span class="navbar-toggler-icon" style="filter:invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="studentNav">
                <span class="me-3">Welcome, <?= htmlspecialchars($name) ?></span>
                <a href="../logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">

        <!-- KPI CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h6>Total Units</h6>
                    <h3 class="text-pink"><?= $totalUnits ?></h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h6>Pending Drop Requests</h6>
                    <h3 class="text-pink"><?= $pendingDrops ?></h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h6>Notifications</h6>
                    <h3 class="text-pink"><?= count($notifications) ?></h3>
                </div>
            </div>
        </div>

        <!-- QUICK LINKS -->
        <div class="row g-3 mb-4 text-center">
            <div class="col-md-4">
                <a href="my_units.php" class="btn btn-pink w-100 py-3">📚 My Units</a>
            </div>
            <div class="col-md-4">
                <a href="drop_requests.php" class="btn btn-pink w-100 py-3">📝 Drop Requests</a>
            </div>
            <div class="col-md-4">
                <a href="student_timetable.php" class="btn btn-pink w-100 py-3">📄 Timetable</a>
            </div>
        </div>

        <!-- NOTIFICATIONS -->
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                Notifications
            </div>
            <div class="card-body">
                <?php if ($notifications): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($notifications as $n): ?>
                            <li class="list-group-item <?= $n['is_read'] == 0 ? 'notification-unread' : '' ?>">
                                <?= htmlspecialchars($n['message']) ?><br>
                                <small class="text-muted"><?= $n['created_at'] ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="mb-0">No notifications yet 💌</p>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="py-3 mt-auto">
        © <?= date('Y') ?> TMS. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
