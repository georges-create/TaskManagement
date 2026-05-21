<?php
session_start();
require '../db.php';
require_once '../TCPDF/tcpdf.php'; // Only for PDF page

/* ================= SESSION & ROLE GUARD ================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit;
}

$student_id = (int) $_SESSION['user_id'];
$name       = $_SESSION['name'];
$program    = $_SESSION['program'];

/* ================= TIMETABLE SETUP ================= */
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$time_slots = ['08:00:00-11:00:00', '11:00:00-14:00:00', '14:00:00-17:00:00'];

/* ================= FETCH DROPPED UNITS FROM NOTIFICATIONS ================= */
$notifStmt = $pdo->prepare("
    SELECT message 
    FROM notifications
    WHERE user_id = ? 
      AND role = 'student' 
      AND message LIKE '%drop request%'
");
$notifStmt->execute([$student_id]);
$messages = $notifStmt->fetchAll(PDO::FETCH_COLUMN);

$droppedUnits = [];
foreach ($messages as $msg) {
    if (preg_match("/'(.+?)'/", $msg, $matches)) {
        $unitName = $matches[1];
        $unitStmt = $pdo->prepare("SELECT id FROM units WHERE name=?");
        $unitStmt->execute([$unitName]);
        $unit = $unitStmt->fetch(PDO::FETCH_ASSOC);
        if ($unit) $droppedUnits[] = $unit['id'];
    }
}

/* Ensure $droppedUnits is not empty to prevent SQL error */
$droppedUnits = !empty($droppedUnits) ? $droppedUnits : [0];

/* ================= FETCH TIMETABLE EXCLUDING DROPPED UNITS ================= */
$stmt = $pdo->prepare("
    SELECT t.id, u.name AS unit_name, u.code, t.day, t.start_time, t.end_time,
           v.name AS venue_name, l.name AS lecturer_name
    FROM timetable t
    JOIN units u ON t.unit_id = u.id
    JOIN venues v ON t.venue_id = v.id
    JOIN users l ON u.lecturer_id = l.id
    WHERE u.program = ?
      AND u.id NOT IN (" . implode(',', array_map('intval', $droppedUnits)) . ")
    ORDER BY t.day, t.start_time
");
$stmt->execute([$program]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ================= ORGANIZE INTO DAY/TIME MATRIX ================= */
$timetable = [];
foreach ($rows as $r) {
    $slot = $r['start_time'] . '-' . $r['end_time'];
    $timetable[$r['day']][$slot] = $r;
}

/* ================= PDF GENERATION (Optional) ================= */
if (isset($_GET['pdf'])) {
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('TMS');
    $pdf->SetAuthor($name);
    $pdf->SetTitle('My Timetable');
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    $html = '<h2 style="color:#e91e63;">My Timetable</h2>';
    $html .= '<table border="1" cellpadding="4">';
    $html .= '<tr style="background-color:#f8bbd0; color:#e91e63;"><th>Day / Time</th>';
    foreach ($time_slots as $slot) {
        $html .= '<th>' . substr($slot,0,5) . ' - ' . substr($slot,9,5) . '</th>';
    }
    $html .= '</tr>';

    foreach ($days as $d) {
        $html .= '<tr>';
        $html .= '<td><strong>' . htmlspecialchars($d) . '</strong></td>';
        foreach ($time_slots as $slot) {
            if (isset($timetable[$d][$slot])) {
                $t = $timetable[$d][$slot];
                $html .= '<td><strong>' . htmlspecialchars($t['unit_name']) . '</strong><br>'
                       . 'Code: ' . htmlspecialchars($t['code']) . '<br>'
                       . 'Venue: ' . htmlspecialchars($t['venue_name']) . '<br>'
                       . 'Lecturer: ' . htmlspecialchars($t['lecturer_name'])
                       . '</td>';
            } else {
                $html .= '<td>-</td>';
            }
        }
        $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('student_timetable.pdf', 'I');
    exit;
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
    <a class="navbar-brand" href="../student/dashboard.php">Student Panel</a>
    <div class="d-flex ms-auto">
        <span class="me-3">Welcome, <?= htmlspecialchars($name) ?></span>
        <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</div>
</nav>

<main class="container">
<a href="../student/dashboard.php" class="btn btn-pink mb-3">← Back to Dashboard</a>
<h3 class="text-pink mb-3">My Timetable</h3>
<div class="mb-3 text-end">
    <a href="?pdf=1" class="btn btn-pink">📄 Download Timetable PDF</a>
</div>

<div class="card shadow-sm p-2">
<div class="table-responsive" style="overflow-x:auto;">
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
        <?php if(isset($timetable[$d][$slot])): $t=$timetable[$d][$slot]; ?>
            <td>
                <strong><?= htmlspecialchars($t['unit_name']) ?></strong><br>
                Code: <?= htmlspecialchars($t['code']) ?><br>
                Venue: <?= htmlspecialchars($t['venue_name']) ?><br>
                Lecturer: <?= htmlspecialchars($t['lecturer_name']) ?>
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
</div>

</main>

<footer>© <?= date('Y') ?> TMS. All rights reserved.</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
