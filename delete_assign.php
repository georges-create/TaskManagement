<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$name = $_SESSION['name'];

// KPIs
$totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$totalLecturers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='lecturer'")->fetchColumn();
$totalUnits = $pdo->query("SELECT COUNT(*) FROM units")->fetchColumn();
$totalDrops = $pdo->query("SELECT COUNT(*) FROM drop_requests WHERE status='pending'")->fetchColumn();

// Notifications
$notifications = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
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

        /* Navbar and Footer Pink Theme */
        .navbar-pink {
            background-color: #e91e63 !important;
        }

        .navbar-pink .navbar-brand,
        .navbar-pink span,
        .navbar-pink .btn {
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

        .bg-pink {
            background: #e91e63 !important;
            color: #fff;
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        footer {
            background: #e91e63;
            color: #ffe4f0;
            font-size: 14px;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        @media (max-width:768px) {
            .navbar-toggler {
                border-color: #ffe4f0;
            }

            .navbar-nav {
                text-align: center;
            }

            .card-body h5 {
                font-size: 1rem;
            }

            .card-body h3 {
                font-size: 1.5rem;
            }

            .row .col-md-2 {
                margin-bottom: 10px;
            }
        }

        /* Large Screen Responsiveness */
        @media (min-width: 1400px) {
            main.container-fluid {
                padding-left: 80px;
                padding-right: 80px;
            }

            .card-body h3 {
                font-size: 2.2rem;
            }

            .btn-pink {
                font-size: 1.05rem;
                padding: 18px 10px;
            }
        }

        @media (min-width: 1600px) {
            main.container-fluid {
                padding-left: 100px;
                padding-right: 100px;
            }

            .card-body h3 {
                font-size: 2.4rem;
            }

            .btn-pink {
                font-size: 1.1rem;
                padding: 20px 12px;
            }
        }

        @media (min-width: 1900px) {
            main.container-fluid {
                padding-left: 120px;
                padding-right: 120px;
            }

            .card-body h3 {
                font-size: 2.6rem;
            }

            .btn-pink {
                font-size: 1.15rem;
                padding: 22px 14px;
            }
        }
        
/* Large screen adjustments */
@media (min-width: 1400px) { .container { max-width: 1320px; } .table th, .table td { padding: 12px; font-size: 15px; } }
@media (min-width: 1600px) { .container { max-width: 1500px; } body { font-size: 17px; } .card { padding: 10px; } }
@media (min-width: 1920px) { .container { max-width: 1700px; } body { font-size: 18px; } h3 { font-size: 28px; } .btn { font-size: 16px; padding: 8px 18px; } }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-pink shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <span class="me-3">Welcome, <?= htmlspecialchars($name) ?> </span>
                <a href="../logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container-fluid px-4">

        <!-- KPI Cards -->
        <div class="row mb-4 g-4 justify-content-center">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5>Students</h5>
                        <h3 class="text-pink"><?= $totalStudents ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5>Lecturers</h5>
                        <h3 class="text-pink"><?= $totalLecturers ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5>Units</h5>
                        <h3 class="text-pink"><?= $totalUnits ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5>Pending Drops</h5>
                        <h3 class="text-pink"><?= $totalDrops ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mb-4 g-3 text-center justify-content-center">
            <div class="col-12 col-sm-6 col-md-2"><a href="manage_users.php" class="btn btn-pink w-100 py-3">👤 Manage Users</a></div>
            <div class="col-12 col-sm-6 col-md-2"><a href="add_unit.php" class="btn btn-pink w-100 py-3">📚 Add Units</a></div>
            <div class="col-12 col-sm-6 col-md-2"><a href="manage_venues.php" class="btn btn-pink w-100 py-3">🏫 Venues</a></div>
            <div class="col-12 col-sm-6 col-md-2"><a href="assign_units.php" class="btn btn-pink w-100 py-3">📚 Assign Units</a></div>
            <div class="col-12 col-sm-6 col-md-2"><a href="drop_requests.php" class="btn btn-pink w-100 py-3">📝 Drop Requests</a></div>
            <div class="col-12 col-sm-6 col-md-2"><a href="timetable.php" class="btn btn-pink w-100 py-3">📄 Timetable</a></div>
        </div>

        <!-- Notifications -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-pink">Notifications</div>
            <div class="card-body">
                <?php if ($notifications): ?>
                    <ul class="list-group">
                        <?php foreach ($notifications as $n): ?>
                            <li class="list-group-item"><?= htmlspecialchars($n['message']) ?> <small class="text-muted">(<?= $n['created_at'] ?>)</small></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No notifications yet 💌</p>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="text-center py-3 mt-auto">
        © <?= date('Y') ?> TMS. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>