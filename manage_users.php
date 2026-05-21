<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get ID and fetch venue
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_venues.php?msg=" . urlencode("❌ Invalid venue ID") . "&type=danger");
    exit;
}
$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM venues WHERE id=?");
$stmt->execute([$id]);
$venue = $stmt->fetch();
if (!$venue) {
    header("Location: manage_venues.php?msg=" . urlencode("❌ Venue not found!") . "&type=danger");
    exit;
}

// Handle Update
if (isset($_POST['update_venue'])) {
    $name = trim($_POST['name']);
    if ($name === '') {
        $msg = "❌ Venue name cannot be empty!";
        header("Location: manage_venues.php?msg=" . urlencode($msg) . "&type=danger");
        exit;
    }
    $stmt = $pdo->prepare("SELECT id FROM venues WHERE name=? AND id!=?");
    $stmt->execute([$name, $id]);
    if ($stmt->rowCount() > 0) {
        $msg = "❌ Venue '$name' already exists!";
        header("Location: manage_venues.php?msg=" . urlencode($msg) . "&type=danger");
        exit;
    }
    $stmt = $pdo->prepare("UPDATE venues SET name=? WHERE id=?");
    $stmt->execute([$name, $id]);
    $msg = "✅ Venue '$name' updated successfully!";
    header("Location: manage_venues.php?msg=" . urlencode($msg) . "&type=success");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Venue</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/font.css" rel="stylesheet">
<style>
body { background: #fff0f6; min-height:100vh; display:flex; flex-direction:column; }
main { flex:1; }
.navbar-pink { background-color:#e91e63 !important; }
.navbar-pink .navbar-brand, .navbar-pink span, .navbar-pink .btn { color:#ffe4f0 !important; }
.btn-pink { background:#e91e63; color:#fff; }
.btn-pink:hover { background:#d81b60; color:#fff; }
.card { border-radius:12px; border:none; box-shadow:0 4px 8px rgba(0,0,0,0.1); max-width:600px; margin:auto; }
footer { background:#e91e63; color:#ffe4f0; font-size:14px; width:100%; text-align:center; padding:10px 0; margin-top:20px; }
    
/* Large screen adjustments */
@media (min-width: 1400px) { .container { max-width: 1320px; } .table th, .table td { padding: 12px; font-size: 15px; } }
@media (min-width: 1600px) { .container { max-width: 1500px; } body { font-size: 17px; } .card { padding: 10px; } }
@media (min-width: 1920px) { .container { max-width: 1700px; } body { font-size: 18px; } h3 { font-size: 28px; } .btn { font-size: 16px; padding: 8px 18px; } }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-pink shadow-sm mb-4">
<div class="container-fluid">
    <a class="navbar-brand" href="manage_venues.php">Admin Panel</a>
    <div class="d-flex ms-auto">
        <span class="me-3">Edit Venue</span>
        <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</div>
</nav>

<main class="container">
<div class="mb-3">
    <a href="manage_venues.php" class="btn btn-outline-primary" style="background-color:#d81b60;color:#ffe4f0">
        ← Back to Venues
    </a>
</div>

<div class="card shadow-sm mb-4">
<div class="card-body">
    <form method="post" class="row g-2">
        <div class="col-12 mb-2">
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($venue['name']) ?>" placeholder="Venue Name" required>
        </div>
        <div class="col-12">
            <button type="submit" name="update_venue" class="btn btn-pink w-100">Update Venue</button>
        </div>
    </form>
</div>
</div>

</main>

<footer class="text-center py-3 mt-auto">
    © <?= date('Y') ?> TMS. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
