<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header('Location: ../login.php');
    exit;
}

$lecturer_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
$time_slots = ['08:00:00-11:00:00','11:00:00-14:00:00','14:00:00-17:00:00'];

/* 🚫 EXCLUDE APPROVED DROPPED UNITS */
$stmt = $pdo->prepare("
    SELECT 
        t.id,
        u.name AS unit_name,
        u.code,
        t.day,
        t.start_time,
        t.end_time,
        v.name AS venue_name
    FROM timetable t
    JOIN units u ON t.unit_id = u.id
    JOIN venues v ON t.venue_id = v.id
    WHERE u.lecturer_id = ?
      AND NOT EXISTS (
            SELECT 1 FROM drop_requests dr
            WHERE dr.unit_id = u.id
            AND dr.status = 'approved'
      )
    ORDER BY t.day, t.start_time
");

$stmt->execute([$lecturer_id]);
$rows = $stmt->fetchAll();

/* Organize into day/time matrix */
$timetable = [];
foreach ($rows as $r) {
    $slot = $r['start_time'] . '-' . $r['end_time'];
    $timetable[$r['day']][$slot] = $r;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Timetable</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/font.css" rel="stylesheet">
<style>
body { background:#fff0f6; min-height:100vh; display:flex; flex-direction:column; }
main { flex:1; }
.navbar-pink { background:#e91e63 !important; }
.navbar-pink .navbar-brand, .navbar-pink span { color:#ffe4f0 !important; }
.btn-pink { background:#e91e63; color:#fff; }
.btn-pink:hover { background:#d81b60; color:#fff; }
.card { border-radius:12px; border:none; box-shadow:0 4px 8px rgba(0,0,0,.1); }
table th, table td { text-align:center; vertical-align:middle; }
footer { background:#e91e63; color:#ffe4f0; font-size:14px; text-align:center; padding:10px 0; }
    
    
/* Large screen adjustments */
@media (min-width: 1400px) { .container { max-width: 1320px; } .table th, .table td { padding: 12px; font-size: 15px; } }
@media (min-width: 1600px) { .container { max-width: 1500px; } body { font-size: 17px; } .card { padding: 10px; } }
@media (min-width: 1920px) { .container { max-width: 1700px; } body { font-size: 18px; } h3 { font-size: 28px; } .btn { font-size: 16px; padding: 8px 18px; } }
</style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-pink shadow-sm mb-4">
<div class="container-fluid">
    <a class="navbar-brand" href="../lecturer/dashboard.php">Lecturer Panel</a>
    <div class="d-flex ms-auto">
        <span class="me-3">Welcome, <?= htmlspecialchars($name) ?></span>
        <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</div>
</nav>

<main class="container">

<a href="../lecturer/dashboard.php" class="btn btn-pink mb-3">← Back to Lecturer</a>

<h3 class="text-pink mb-3">My Timetable </h3>

<div class="mb-3 text-end">
    <a href="lecturer_timetable_pdf.php" class="btn btn-pink">
        📄 Download Timetable PDF
    </a>
</div>

<div class="card shadow-sm p-2">
<table class="table table-bordered table-striped">
<thead style="background:#f8bbd0; color:#e91e63;">
<tr>
    <th>Day / Time</th>
    <?php foreach ($time_slots as $slot): ?>
        <th><?= substr($slot,0,5) ?> - <?= substr($slot,9,5) ?></th>
    <?php endforeach; ?>
</tr>
</thead>
<tbody>
<?php foreach ($days as $d): ?>
<tr>
    <td><strong><?= $d ?></strong></td>
    <?php foreach ($time_slots as $slot): ?>
        <?php if (isset($timetable[$d][$slot])): $t = $timetable[$d][$slot]; ?>
            <td>
                <strong><?= htmlspecialchars($t['unit_name']) ?></strong><br>
                Code: <?= htmlspecialchars($t['code']) ?><br>
                Venue: <?= htmlspecialchars($t['venue_name']) ?>
            </td>
        <?php else: ?>
            <td>-</td>
        <?php endif; ?>
    <?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

</main>

<footer>© <?= date('Y') ?> TMS. All rights reserved.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
