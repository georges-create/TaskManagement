<?php
session_start();
require '../db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
    header('Location: ../login.php'); exit;
}

if(!isset($_GET['id'])){
    header('Location: assign_units.php'); exit;
}

$assign_id = $_GET['id'];
$message = '';
$alert_type = '';

// Fetch the assignment
$stmt = $pdo->prepare("
    SELECT t.*, u.name AS unit_name, u.program, u.year, u.semester, u.lecturer_id
    FROM timetable t
    JOIN units u ON u.id = t.unit_id
    WHERE t.id = ?
");
$stmt->execute([$assign_id]);
$assignment = $stmt->fetch();
if(!$assignment){
    header('Location: assign_units.php?msg=Assignment+not+found&type=danger'); exit;
}

// Fetch dropdown data
$lecturers = $pdo->query("SELECT id,name FROM users WHERE role='lecturer'")->fetchAll();
$venues = $pdo->query("SELECT id,name FROM venues")->fetchAll();
$units = $pdo->query("SELECT id,name,program,year,semester FROM units ORDER BY id DESC")->fetchAll();

// Handle update
if(isset($_POST['update'])){
    $unit_id = $_POST['unit_id'];
    $lecturer_id = $_POST['lecturer_id'];
    $day = $_POST['day'];
    $time_slot = $_POST['time_slot'];
    $venue_id = $_POST['venue'];

    $slots = ['1'=>['08:00:00','11:00:00'],'2'=>['11:00:00','14:00:00'],'3'=>['14:00:00','17:00:00']];
    $start = $slots[$time_slot][0] ?? '08:00:00';
    $end   = $slots[$time_slot][1] ?? '11:00:00';

    // Prevent duplicates
    $stmt = $pdo->prepare("SELECT * FROM timetable WHERE unit_id=? AND day=? AND start_time=? AND end_time=? AND venue_id=? AND id<>?");
    $stmt->execute([$unit_id,$day,$start,$end,$venue_id,$assign_id]);
    if($stmt->rowCount()>0){
        $message = "❌ Duplicate assignment exists!";
        $alert_type = 'danger';
    } else {
        // Update lecturer
        $pdo->prepare("UPDATE units SET lecturer_id=? WHERE id=?")->execute([$lecturer_id,$unit_id]);
        // Update timetable
        $pdo->prepare("UPDATE timetable SET unit_id=?, day=?, start_time=?, end_time=?, venue_id=? WHERE id=?")
            ->execute([$unit_id,$day,$start,$end,$venue_id,$assign_id]);
        header("Location: assign_units.php?msg=Assignment+updated+successfully&type=success"); exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Assignment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/font.css" rel="stylesheet">
<style>
body { background:#fff0f6; min-height:100vh; display:flex; flex-direction:column; }
main { flex:1; }
.navbar-pink { background-color:#e91e63 !important; color:#ffe4f0 !important; }
.btn-pink { background:#e91e63; color:#fff; }
.btn-pink:hover { background:#d81b60; color:#fff; }
.card { border-radius:12px; border:none; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
footer { background:#e91e63; color:#ffe4f0; text-align:center; padding:10px 0; }
    
    
/* Large screen adjustments */
@media (min-width: 1400px) { .container { max-width: 1320px; } .table th, .table td { padding: 12px; font-size: 15px; } }
@media (min-width: 1600px) { .container { max-width: 1500px; } body { font-size: 17px; } .card { padding: 10px; } }
@media (min-width: 1920px) { .container { max-width: 1700px; } body { font-size: 18px; } h3 { font-size: 28px; } .btn { font-size: 16px; padding: 8px 18px; } }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-pink mb-4 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
        <div class="d-flex ms-auto"><span class="me-3">Edit Assignment</span><a href="../logout.php" class="btn btn-outline-light">Logout</a></div>
    </div>
</nav>

<main class="container">
<div class="mb-3">
    <a href="assign_units.php" class="btn btn-outline-primary" style="background:#d81b60;color:#ffe4f0">← Back to Assignments</a>
</div>

<div class="card shadow-sm">
<div class="card-body">
<?php if($message): ?>
<div class="alert alert-<?= $alert_type ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post" class="row g-2">
    <div class="col-md-3">
        <select name="unit_id" class="form-select" required>
            <option value="">Unit</option>
            <?php foreach($units as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $u['id']==$assignment['unit_id']?'selected':'' ?>>
                    <?= htmlspecialchars($u['name']) ?> (<?= $u['program'].'-'.$u['year'].' '.$u['semester'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="lecturer_id" class="form-select" required>
            <option value="">Lecturer</option>
            <?php foreach($lecturers as $l): ?>
                <option value="<?= $l['id'] ?>" <?= $l['id']==$assignment['lecturer_id']?'selected':'' ?>>
                    <?= htmlspecialchars($l['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="day" class="form-select" required>
            <option value="Monday" <?= $assignment['day']=='Monday'?'selected':'' ?>>Monday</option>
            <option value="Tuesday" <?= $assignment['day']=='Tuesday'?'selected':'' ?>>Tuesday</option>
            <option value="Wednesday" <?= $assignment['day']=='Wednesday'?'selected':'' ?>>Wednesday</option>
            <option value="Thursday" <?= $assignment['day']=='Thursday'?'selected':'' ?>>Thursday</option>
            <option value="Friday" <?= $assignment['day']=='Friday'?'selected':'' ?>>Friday</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="time_slot" class="form-select" required>
            <?php 
            $times = ['1'=>'08:00-11:00','2'=>'11:00-14:00','3'=>'14:00-17:00'];
            $current_slot = array_search($assignment['start_time'],$times) ?: '';
            foreach($times as $k=>$v):
            ?>
            <option value="<?= $k ?>" <?= ($assignment['start_time']==substr($v,0,5))?'selected':'' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="venue" class="form-select" required>
            <?php foreach($venues as $v): ?>
                <option value="<?= $v['id'] ?>" <?= $v['id']==$assignment['venue_id']?'selected':'' ?>><?= htmlspecialchars($v['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12 mt-2">
        <button type="submit" name="update" class="btn btn-pink w-100">Update Assignment</button>
    </div>
</form>
</div>
</div>
</main>
<footer class="text-center py-3 mt-auto">© <?= date('Y') ?> TMS. All rights reserved.</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
