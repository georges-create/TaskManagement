<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit;
}

$name       = $_SESSION['name'];
$program    = $_SESSION['program'];
$year       = $_SESSION['year'];
$semester   = $_SESSION['semester'];
$student_id = $_SESSION['user_id'];

// Fetch approved and pending drop requests for this student
$dropStmt = $pdo->prepare("
    SELECT unit_id, status 
    FROM drop_requests 
    WHERE user_id = ?
");
$dropStmt->execute([$student_id]);
$dropRequests = $dropStmt->fetchAll(PDO::FETCH_KEY_PAIR); // unit_id => status

// Fetch units for the student's program, current year, and semester
$stmt = $pdo->prepare("
    SELECT id, code, name 
    FROM units 
    WHERE program = ? 
      AND year = ? 
      AND semester = ?
    ORDER BY id DESC
");
$stmt->execute([$program, $year, $semester]);
$units = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Units</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="../assets/css/font.css" rel="stylesheet">
<style>
body {
    background:#fff0f6;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}
main { flex:1; }

.navbar-pink {
    background:#e91e63 !important;
}
.navbar-pink .navbar-brand,
.navbar-pink span {
    color:#ffe4f0 !important;
}

.text-pink { color:#e91e63 !important; }

.btn-pink {
    background:#e91e63;
    color:#fff;
}
.btn-pink:hover {
    background:#d81b60;
    color:#fff;
}

.card {
    border-radius:12px;
    border:none;
    box-shadow:0 4px 8px rgba(0,0,0,.1);
}

footer {
    background:#e91e63;
    color:#ffe4f0;
    font-size:14px;
    text-align:center;
}

.badge-pending {
    background:#ffc107;
    color:#000;
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
    <a class="navbar-brand" href="dashboard.php">Student Panel</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav">
        <span class="navbar-toggler-icon" style="filter:invert(1);"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="studentNav">
        <span class="me-3"><?= htmlspecialchars($name) ?> </span>
        <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</div>
</nav>

<main class="container">

<div class="mb-3">
    <a href="dashboard.php" class="btn btn-pink">
        ← Back to Dashboard
    </a>
</div>

<h4 class="text-pink mb-3">My Units (<?= htmlspecialchars($year . ' - ' . $semester) ?>)</h4>

<div class="card">
    <div class="card-header bg-danger text-white fw-semibold">
        Registered Units
    </div>

    <div class="card-body table-responsive">
        <table id="unitsTable" class="table table-bordered table-striped align-middle w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Unit Name</th>
                    <th>Status</th>
                </tr>
            </thead>
          <tbody>
<?php 
$counter = 1; // Initialize counter
foreach ($units as $u): 
    $status = '';
    if (isset($dropRequests[$u['id']])) {
        if ($dropRequests[$u['id']] === 'pending') {
            $status = '<span class="badge badge-pending">⚠️ Pending Drop</span>';
        } elseif ($dropRequests[$u['id']] === 'approved') {
            $status = '<span class="badge bg-success">✅ Dropped</span>';
        } elseif ($dropRequests[$u['id']] === 'rejected') {
            $status = '<span class="badge bg-danger">❌ Drop Rejected</span>';
        }
    }
?>
<tr>
    <td><?= $counter++ ?></td> <!-- Serial number -->
    <td><?= htmlspecialchars($u['code']) ?></td>
    <td><?= htmlspecialchars($u['name']) ?></td>
    <td><?= $status ?></td>
</tr>
<?php endforeach; ?>
</tbody>

        </table>
    </div>
</div>

</main>

<!-- FOOTER -->
<footer class="py-3 mt-auto">
© <?= date('Y') ?> TMS. All rights reserved.
</footer>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#unitsTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        ordering: false,
        searching: false
    });
});
</script>

</body>
</html>
