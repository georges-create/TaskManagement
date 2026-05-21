<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: add_unit.php');
    exit;
}

$id = $_GET['id'];

// Fetch the unit
$stmt = $pdo->prepare("SELECT * FROM units WHERE id = ?");
$stmt->execute([$id]);
$unit = $stmt->fetch();

if (!$unit) {
    header('Location: add_unit.php?msg=' . urlencode("Unit not found") . '&type=danger');
    exit;
}

// Handle update
if (isset($_POST['update_unit'])) {
    $unit_name = trim($_POST['unit_name']);
    $unit_code = trim($_POST['unit_code']);
    $program   = $_POST['program'];
    $year      = $_POST['year'];
    $semester  = $_POST['semester'];

    $stmt = $pdo->prepare("SELECT id FROM units WHERE name=? AND code=? AND program=? AND year=? AND semester=? AND id!=?");
    $stmt->execute([$unit_name, $unit_code, $program, $year, $semester, $id]);

    if ($stmt->rowCount() > 0) {
        header('Location: add_unit.php?msg=' . urlencode('❌ Another unit with the same details exists!') . '&type=danger');
        exit;
    } else {
        $stmt = $pdo->prepare("UPDATE units SET name=?, code=?, program=?, year=?, semester=? WHERE id=?");
        $stmt->execute([$unit_name, $unit_code, $program, $year, $semester, $id]);
        header('Location: add_unit.php?msg=' . urlencode("✅ Unit '$unit_name' updated successfully!") . '&type=success');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Unit</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/font.css" rel="stylesheet">
<style>
body { background: #fff0f6; min-height:100vh; display:flex; flex-direction:column; }
main { flex:1; }
.navbar-pink { background-color: #e91e63 !important; color:#ffe4f0; }
.card { border-radius:12px; border:none; box-shadow:0 4px 8px rgba(0,0,0,0.1); max-width:600px; margin:auto; }
.btn-pink { background:#e91e63; color:#fff; }
.btn-pink:hover { background:#d81b60; color:#fff; }
footer { background:#e91e63; color:#ffe4f0; font-size:14px; text-align:center; padding:10px 0; margin-top:20px; }

/* Stack inputs vertically */
.form-stack .row { flex-direction: column; }
.form-stack .col-md-12 { width: 100%; margin-bottom: 10px; }
    
   
/* Large screen adjustments */
@media (min-width: 1400px) { .container { max-width: 1320px; } .table th, .table td { padding: 12px; font-size: 15px; } }
@media (min-width: 1600px) { .container { max-width: 1500px; } body { font-size: 17px; } .card { padding: 10px; } }
@media (min-width: 1920px) { .container { max-width: 1700px; } body { font-size: 18px; } h3 { font-size: 28px; } .btn { font-size: 16px; padding: 8px 18px; } }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-pink shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="add_unit.php">Admin Panel</a>
        <div class="d-flex ms-auto">
            <a href="../logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<main class="container">
    <div class="mb-3 ">
        <a href="add_unit.php" class="btn btn-outline-primary" style="background-color:#d81b60;color:#ffe4f0">
            ← Back to Units
        </a>
    </div>

    <h2 class="mb-4 text-pink text-center">Edit Unit</h2>

    <form method="post" class="card p-3 shadow-sm form-stack">
        <div class="row g-2">
            <div class="col-md-12">
                <input type="text" name="unit_name" value="<?= htmlspecialchars($unit['name']) ?>" class="form-control form-control-sm" placeholder="Unit Name" required>
            </div>
            <div class="col-md-12">
                <input type="text" name="unit_code" value="<?= htmlspecialchars($unit['code']) ?>" class="form-control form-control-sm" placeholder="Unit Code" required>
            </div>
            <div class="col-md-12">
                <select name="program" class="form-select form-select-sm" required>
                    <?php foreach(['BCSIT','BBIT','BED','BBM'] as $p): ?>
                        <option value="<?= $p ?>" <?= $unit['program']==$p?'selected':'' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12">
                <select name="year" class="form-select form-select-sm" required>
                    <?php foreach(['Y1','Y2','Y3','Y4'] as $y): ?>
                        <option value="<?= $y ?>" <?= $unit['year']==$y?'selected':'' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12">
                <select name="semester" class="form-select form-select-sm" required>
                    <?php foreach(['SEM1','SEM2'] as $s): ?>
                        <option value="<?= $s ?>" <?= $unit['semester']==$s?'selected':'' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12 d-grid">
                <button type="submit" class="btn btn-pink btn-sm" name="update_unit">Update Unit</button>
            </div>
        </div>
    </form>
</main>

<footer>
    © <?= date('Y') ?> TMS. All rights reserved.
</footer>

</body>
</html>
