<?php
session_start();
require '../db.php';
require '../functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header('Location: ../login.php');
    exit;
}

$lecturer_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

if (isset($_POST['submit_drop'])) {
    $unit_id = (int) $_POST['unit_id'];
    $reason  = trim($_POST['reason']);

    if ($unit_id && $reason !== '') {

        // Check if lecturer actually owns the unit
        $unitStmt = $pdo->prepare("SELECT name FROM units WHERE id=? AND lecturer_id=?");
        $unitStmt->execute([$unit_id, $lecturer_id]);
        $unit = $unitStmt->fetch();

        if ($unit) {

            // === Prevent duplicate drop requests ===
            $checkStmt = $pdo->prepare(
                "SELECT id FROM drop_requests WHERE user_id=? AND unit_id=? AND status='pending'"
            );
            $checkStmt->execute([$lecturer_id, $unit_id]);
            if ($checkStmt->rowCount() > 0) {
                $message = "⚠️ You have already submitted a drop request for '{$unit['name']}' which is still pending!";
            } else {
                // Insert drop request
                $stmt = $pdo->prepare(
                    "INSERT INTO drop_requests (user_id, unit_id, reason) VALUES (?,?,?)"
                );
                $stmt->execute([$lecturer_id, $unit_id, $reason]);

                $message = "✅ Drop request for '{$unit['name']}' submitted successfully!";

                // Notify admins
                $admins = $pdo->query("SELECT id FROM users WHERE role='admin'")->fetchAll();
                foreach ($admins as $admin) {
                    sendNotification(
                        $pdo,
                        $admin['id'],
                        'admin',
                        "Lecturer submitted a drop request for '{$unit['name']}'"
                    );
                }

                // Notify students
                $students = $pdo->query("SELECT id FROM users WHERE role='student'")->fetchAll();
                foreach ($students as $student) {
                    sendNotification(
                        $pdo,
                        $student['id'],
                        'student',
                        "Your lecturer submitted a drop request for '{$unit['name']}'"
                    );
                }
            }
        }
    }
}

/* ===== FETCH DATA ===== */
$dropsStmt = $pdo->prepare("
    SELECT dr.id, u.name AS unit_name, dr.reason, dr.status, dr.created_at
    FROM drop_requests dr
    JOIN units u ON dr.unit_id=u.id
    WHERE dr.user_id=?
    ORDER BY dr.created_at DESC
    LIMIT 10
");
$dropsStmt->execute([$lecturer_id]);
$drops = $dropsStmt->fetchAll();

$unitsStmt = $pdo->prepare("SELECT id, name FROM units WHERE lecturer_id=?");
$unitsStmt->execute([$lecturer_id]);
$units = $unitsStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Drop Requests</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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

.btn-pink {
    background:#e91e63;
    color:#fff;
}
.btn-pink:hover {
    background:#d81b60;
    color:#fff;
}

.text-pink { color:#e91e63 !important; }

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
    <a class="navbar-brand" href="../lecturer/dashboard.php">Lecturer Panel</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon" style="filter:invert(1);"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <span class="me-3">Welcome, <?= htmlspecialchars($name) ?> </span>
        <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</div>
</nav>

<main class="container">

<a href="../lecturer/dashboard.php" class="btn btn-pink mb-3"> ← Back to Lecturer </a>

<?php if (isset($message)): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= htmlspecialchars($message) ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- SUBMIT FORM -->
<div class="card mb-4">
<div class="card-body">
<form method="post" class="row g-2">
    <div class="col-md-4">
        <select name="unit_id" class="form-select" required>
            <option value="">Select Unit</option>
            <?php 
            // Fetch pending drops for this lecturer
            $pendingStmt = $pdo->prepare("SELECT unit_id FROM drop_requests WHERE user_id=? AND status='pending'");
            $pendingStmt->execute([$lecturer_id]);
            $pendingUnits = $pendingStmt->fetchAll(PDO::FETCH_COLUMN); // array of unit_ids

            foreach ($units as $u): 
                $disabled = in_array($u['id'], $pendingUnits) ? 'disabled' : '';
                $label = htmlspecialchars($u['name']);
                if($disabled) $label .= ' ⚠️ (Drop pending)';
            ?>
                <option value="<?= $u['id'] ?>" <?= $disabled ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <input type="text" name="reason" class="form-control"
               placeholder="Reason for dropping" required>
    </div>
    <div class="col-md-2">
        <button name="submit_drop" class="btn btn-pink w-100">Submit</button>
    </div>
</form>
</div>
</div>


<!-- TABLE -->
<div class="card">
<div class="card-body">
<table id="dropTable" class="table table-striped table-bordered">
<thead style="background:#f8bbd0;color:#e91e63;">
<tr>
    <th>ID</th>
    <th>Unit</th>
    <th>Reason</th>
    <th>Status</th>
    <th>Submitted</th>
</tr>
</thead>
<tbody>
<?php 
$counter = 1; // Initialize serial counter
foreach ($drops as $d): 
?>
<tr>
    <td><?= $counter++ ?></td> <!-- Serial number -->
    <td><?= htmlspecialchars($d['unit_name']) ?></td>
    <td><?= htmlspecialchars($d['reason']) ?></td>
    <td><?= ucfirst($d['status']) ?></td>
    <td><?= $d['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</tbody>

</table>
</div>
</div>

</main>

<footer class="py-3 mt-auto">
© <?= date('Y') ?> TMS. All rights reserved.
</footer>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function(){
    $('#dropTable').DataTable({
        pageLength:10,
        lengthChange:false,
        responsive:true
    });
});
</script>

</body>
</html>
