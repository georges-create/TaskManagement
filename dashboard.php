<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$message = '';
$alert_type = '';

if(isset($_GET['msg']) && isset($_GET['type'])){
    $message = $_GET['msg'];
    $alert_type = $_GET['type']; // 'success' or 'danger'
}

// Fetch data
$lecturers = $pdo->query("SELECT id,name FROM users WHERE role='lecturer'")->fetchAll();
$venues = $pdo->query("SELECT id,name FROM venues")->fetchAll();
$programs = ['BCSIT','BBIT','BED','BBM'];
$years = ['Y1','Y2','Y3','Y4'];
$semesters = ['SEM1','SEM2'];
$units = $pdo->query("SELECT id,name,program,year,semester,lecturer_id FROM units ORDER BY id DESC")->fetchAll();

// Handle assignment
if(isset($_POST['assign'])) {
    $unit_id = $_POST['unit_id'];
    $lecturer_id = $_POST['lecturer_id'];
    $day = $_POST['day'];
    $time_slot = $_POST['time_slot'];
    $venue_id = $_POST['venue'];

    $slots = [
        '1' => ['08:00:00','11:00:00'],
        '2' => ['11:00:00','14:00:00'],
        '3' => ['14:00:00','17:00:00']
    ];
    $start = $slots[$time_slot][0] ?? '08:00:00';
    $end   = $slots[$time_slot][1] ?? '11:00:00';

    // Check duplicate assignment
    $stmt = $pdo->prepare("SELECT * FROM timetable WHERE unit_id=? AND day=? AND start_time=? AND end_time=? AND venue_id=?");
    $stmt->execute([$unit_id,$day,$start,$end,$venue_id]);
    if($stmt->rowCount() > 0){
        $message = "❌ This unit is already assigned for this day/time/venue!";
        $alert_type = 'danger';
    } else {
        // Update lecturer
        $pdo->prepare("UPDATE units SET lecturer_id=? WHERE id=?")->execute([$lecturer_id,$unit_id]);
        // Insert into timetable
        $pdo->prepare("INSERT INTO timetable (unit_id,day,start_time,end_time,venue_id) VALUES (?,?,?,?,?)")
            ->execute([$unit_id,$day,$start,$end,$venue_id]);
        $message = "✅ Unit assigned successfully!";
        $alert_type = 'success';
    }
}

// Fetch assigned units
$assigned_units = $pdo->query("
    SELECT 
        t.id AS assign_id,
        u.name AS unit_name,
        u.program,u.year,u.semester,
        l.name AS lecturer_name,
        t.day,t.start_time,t.end_time,
        v.name AS venue_name
    FROM timetable t
    JOIN units u ON u.id=t.unit_id
    LEFT JOIN users l ON l.id=u.lecturer_id
    JOIN venues v ON v.id=t.venue_id
    ORDER BY t.id DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assign Units</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/font.css" rel="stylesheet">
<style>
body { background:#fff0f6; min-height:100vh; display:flex; flex-direction:column; }
main { flex:1; }
.navbar-pink { background-color:#e91e63 !important; }
.navbar-pink .navbar-brand,.navbar-pink span,.navbar-pink .btn { color:#ffe4f0 !important; }
.btn-pink { background:#e91e63;color:#fff; }
.btn-pink:hover { background:#d81b60;color:#fff; }
.card { border-radius:12px;border:none;box-shadow:0 4px 8px rgba(0,0,0,0.1); }
footer { background:#e91e63;color:#ffe4f0;font-size:14px;text-align:center;width:100%;padding:10px 0;margin-top:20px; }
.table th, .table td { vertical-align: middle; }
.bg-pink { background:#e91e63 !important; color:#fff !important; }

/* Responsive table wrapper */
.table-responsive { overflow-x:auto; }

/* Hide less important columns on small screens */
@media (max-width: 768px) {
    th:nth-child(3), th:nth-child(4), th:nth-child(5),
    th:nth-child(8), th:nth-child(9),
    td:nth-child(3), td:nth-child(4), td:nth-child(5),
    td:nth-child(8), td:nth-child(9) {
        display: none;
    }
    .table th, .table td { font-size: 13px; padding: 6px; }
    .btn-sm { font-size: 12px; padding: 4px 8px; }
}

/* Large screen adjustments */
@media (min-width: 1400px) { .container { max-width: 1320px; } .table th, .table td { padding: 12px; font-size: 15px; } }
@media (min-width: 1600px) { .container { max-width: 1500px; } body { font-size: 17px; } .card { padding: 10px; } }
@media (min-width: 1920px) { .container { max-width: 1700px; } body { font-size: 18px; } h3 { font-size: 28px; } .btn { font-size: 16px; padding: 8px 18px; } }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-pink shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
        <div class="d-flex ms-auto">
            <span class="me-3">Assign Units</span>
            <a href="../logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<main class="container">
<div class="mb-3">
    <a href="../admin/dashboard.php" class="btn btn-outline-primary" style="background-color:#d81b60;color:#ffe4f0">← Back to Admin</a>
</div>

<h3 class="text-pink mb-3">Assign Units to Lecturers </h3>
<?php if($message): ?>
<div class="alert alert-<?= $alert_type ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Assignment Form -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<form method="post" class="row g-2">
    <div class="col-md-2">
        <select name="program" id="programSelect" class="form-select" required>
            <option value="">Program</option>
            <?php foreach($programs as $p) echo "<option value='$p'>$p</option>"; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="year" id="yearSelect" class="form-select" required>
            <option value="">Year</option>
            <?php foreach($years as $y) echo "<option value='$y'>$y</option>"; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="semester" id="semesterSelect" class="form-select" required>
            <option value="">Semester</option>
            <?php foreach($semesters as $s) echo "<option value='$s'>$s</option>"; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="unit_id" id="unitSelect" class="form-select" required>
            <option value="">Unit</option>
            <?php foreach($units as $u): 
                $assigned = $u['lecturer_id'] ?? null;
                $disabled = $assigned ? 'disabled' : '';
                $label = htmlspecialchars($u['name']);
                if($assigned) $label .= ' ⚠️ (Assigned)';
            ?>
                <option value="<?= $u['id'] ?>" 
                        data-program="<?= $u['program'] ?>" 
                        data-year="<?= $u['year'] ?>" 
                        data-semester="<?= $u['semester'] ?>"
                        <?= $disabled ?> >
                    <?= $label ?> (<?= $u['program'].'-'.$u['year'].' '.$u['semester'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="lecturer_id" class="form-select" required>
            <option value="">Lecturer</option>
            <?php foreach($lecturers as $l) echo "<option value='{$l['id']}'>" . htmlspecialchars($l['name']) . "</option>"; ?>
        </select>
    </div>
    <div class="col-md-1">
        <select name="day" class="form-select" required>
            <option value="">Day</option>
            <option value="Monday">Mon</option>
            <option value="Tuesday">Tue</option>
            <option value="Wednesday">Wed</option>
            <option value="Thursday">Thu</option>
            <option value="Friday">Fri</option>
        </select>
    </div>
    <div class="col-md-1">
        <select name="time_slot" class="form-select" required>
            <option value="">Time</option>
            <option value="1">08:00-11:00</option>
            <option value="2">11:00-14:00</option>
            <option value="3">14:00-17:00</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="venue" class="form-select" required>
            <option value="">Venue</option>
            <?php foreach($venues as $v) echo "<option value='{$v['id']}'>" . htmlspecialchars($v['name']) . "</option>"; ?>
        </select>
    </div>
    <div class="col-12 mt-3 d-flex justify-content-end">
    <button type="submit" name="assign" class="btn btn-pink" style="width:150px;">Assign Unit</button>
</div>
</form>
</div>
</div>

<!-- Assigned Units Table -->
<div class="card shadow-sm">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead class="bg-pink text-white">
<tr>
    <th>ID</th>
    <th>Unit</th>
    <th>Program</th>
    <th>Year</th>
    <th>Sem</th>
    <th>Lecturer</th>
    <th>Day</th>
    <th>Time</th>
    <th>Venue</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php
    $counter = 1;
    foreach($assigned_units as $a): ?>
<tr>
    <td><?= $counter++ ?></td>
    <td><?= htmlspecialchars($a['unit_name']) ?></td>
    <td><?= $a['program'] ?></td>
    <td><?= $a['year'] ?></td>
    <td><?= $a['semester'] ?></td>
    <td><?= htmlspecialchars($a['lecturer_name'] ?? '-') ?></td>
    <td><?= $a['day'] ?></td>
    <td><?= date('H:i',strtotime($a['start_time'])).'-'.date('H:i',strtotime($a['end_time'])) ?></td>
    <td><?= htmlspecialchars($a['venue_name']) ?></td>
    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-pink dropdown-toggle" type="button" data-bs-toggle="dropdown">Actions</button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="edit_assign.php?id=<?= $a['assign_id'] ?>">Edit</a></li>
                <li><a class="dropdown-item text-danger" href="delete_assign.php?id=<?= $a['assign_id'] ?>">Delete</a></li>
            </ul>
        </div>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
</div>

</main>
<footer class="text-center py-3 mt-auto">© <?= date('Y') ?> TMS. All rights reserved.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const programSelect = document.getElementById('programSelect');
const yearSelect = document.getElementById('yearSelect');
const semesterSelect = document.getElementById('semesterSelect');
const unitSelect = document.getElementById('unitSelect');

[programSelect, yearSelect, semesterSelect].forEach(el => {
    el.addEventListener('change', () => {
        const program = programSelect.value;
        const year = yearSelect.value;
        const sem = semesterSelect.value;

        Array.from(unitSelect.options).forEach(option => {
            if(option.value === "") return;
            const matchesProgram = !program || option.dataset.program === program;
            const matchesYear = !year || option.dataset.year === year;
            const matchesSem = !sem || option.dataset.semester === sem;
            option.style.display = (matchesProgram && matchesYear && matchesSem) ? 'block' : 'none';
        });

        unitSelect.value = "";
    });
});
</script>

</body>
</html>