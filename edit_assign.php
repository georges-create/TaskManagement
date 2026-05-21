<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Approve/Reject
if (isset($_GET['action'], $_GET['id'])) {
    $status = $_GET['action'] === 'approve' ? 'approved' : 'rejected';
    $stmt = $pdo->prepare("UPDATE drop_requests SET status=? WHERE id=?");
    $stmt->execute([$status, $_GET['id']]);
    $msg = "✅ Drop request has been " . ucfirst($status) . "!";
}

// Fetch pending requests
$requests = $pdo->query("
    SELECT dr.id,u.name as unit_name, us.name as student_name, dr.reason, dr.status, dr.created_at 
    FROM drop_requests dr
    JOIN users us ON dr.user_id=us.id
    JOIN units u ON dr.unit_id=u.id
    ORDER BY dr.created_at DESC
    LIMIT 10
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Drop Requests</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="../assets/css/font.css" rel="stylesheet">
<style>
    body {
        background: #fff0f6;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    main { flex: 1; }

    /* Navbar & Footer Pink Theme */
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

    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    footer {
        background: #e91e63;
        color: #ffe4f0;
        font-size: 14px;
        width: 100%;
        text-align: center;
    }

    @media(max-width:768px) {
        .table thead th:nth-child(4),
        .table thead th:nth-child(5),
        .table thead th:nth-child(6),
        .table tbody td:nth-child(4),
        .table tbody td:nth-child(5),
        .table tbody td:nth-child(6) {
            display: none;
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
        <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <span class="me-3">Drop Requests</span>
            <a href="../logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<main class="container">
    <div class="mb-3">
        <a href="../admin/dashboard.php" class="btn btn-outline-primary" style="background-color: #d81b60; color:#ffe4f0">
            ← Back to Admin
        </a>
    </div>

    <h3 class="text-pink mb-3">Drop Requests </h3>
    <?php if (isset($msg)): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table id="dropTable" class="table table-striped table-bordered" style="width:100%">
                <thead style="background-color:#f8bbd0; color:#e91e63;">
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Unit</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                 $counter = 1;
                    foreach ($requests as $r): ?>
                        <tr>
                         <td><?= $counter++ ?></td> 
                            <td><?= htmlspecialchars($r['student_name']) ?></td>
                            <td><?= htmlspecialchars($r['unit_name']) ?></td>
                            <td><?= htmlspecialchars($r['reason']) ?></td>
                            <td><?= ucfirst($r['status']) ?></td>
                            <td><?= $r['created_at'] ?></td>
                            <td>
                                <?php if ($r['status'] == 'pending'): ?>
                                    <a href="?action=approve&id=<?= $r['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                                    <a href="?action=reject&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- FOOTER -->
<footer class="text-center py-3 mt-auto">
    © <?= date('Y') ?> TMS. All rights reserved.
</footer>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dropTable').DataTable({
            "pageLength": 10,
            "lengthChange": false,
            responsive: true
        });
    });
</script>
</body>
</html>
