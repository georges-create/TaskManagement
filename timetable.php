<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Initialize message and alert type
$message = $_GET['msg'] ?? '';
$alert_type = $_GET['type'] ?? '';

// Handle Add Venue
if (isset($_POST['add_venue'])) {
    $name = trim($_POST['name']);
    if ($name === '') {
        $msg = "❌ Venue name cannot be empty!";
        header("Location: manage_venues.php?msg=" . urlencode($msg) . "&type=danger");
        exit;
    }
    // Check duplicate
    $stmt = $pdo->prepare("SELECT id FROM venues WHERE name=?");
    $stmt->execute([$name]);
    if ($stmt->rowCount() > 0) {
        $msg = "❌ Venue '$name' already exists!";
        header("Location: manage_venues.php?msg=" . urlencode($msg) . "&type=danger");
        exit;
    } else {
        $stmt = $pdo->prepare("INSERT INTO venues (name) VALUES (?)");
        $stmt->execute([$name]);
        $msg = "✅ Venue '$name' added successfully!";
        header("Location: manage_venues.php?msg=" . urlencode($msg) . "&type=success");
        exit;
    }
}

// Handle Delete Venue (no confirm pop-up)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT name FROM venues WHERE id=?");
    $stmt->execute([$id]);
    $venue = $stmt->fetch();
    if ($venue) {
        $stmt = $pdo->prepare("DELETE FROM venues WHERE id=?");
        $stmt->execute([$id]);
        $msg = "✅ Venue '{$venue['name']}' deleted successfully!";
        header("Location: manage_venues.php?msg=" . urlencode($msg) . "&type=success");
        exit;
    } else {
        $msg = "❌ Venue not found!";
        header("Location: manage_venues.php?msg=" . urlencode($msg) . "&type=danger");
        exit;
    }
}

// Fetch venues
$venues = $pdo->query("SELECT * FROM venues ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Venues</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link href="../assets/css/font.css" rel="stylesheet">
<style>
body { background: #fff0f6; min-height:100vh; display:flex; flex-direction:column; }
main { flex:1; }
.navbar-pink { background-color:#e91e63 !important; }
.navbar-pink .navbar-brand, .navbar-pink span, .navbar-pink .btn { color:#ffe4f0 !important; }
.btn-pink { background:#e91e63; color:#fff; }
.btn-pink:hover { background:#d81b60; color:#fff; }
.card { border-radius:12px; border:none; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
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
    <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
    <div class="d-flex ms-auto">
        <span class="me-3">Manage Venues</span>
        <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</div>
</nav>

<main class="container">
<div class="mb-3">
    <a href="../admin/dashboard.php" class="btn btn-outline-primary" style="background-color:#d81b60;color:#ffe4f0">
        ← Back to Admin
    </a>
</div>

<h3 class="text-pink mb-3">Manage Venues</h3>

<?php if($message): ?>
<div class="alert alert-<?= htmlspecialchars($alert_type) ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Add Venue -->
<div class="card shadow-sm mb-4">
<div class="card-body">
    <form method="post" class="row g-2">
        <div class="col-md-10">
            <input type="text" name="name" class="form-control" placeholder="Venue Name" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="add_venue" class="btn btn-pink w-100">Add</button>
        </div>
    </form>
</div>
</div>

<!-- Venues Table -->
<div class="card shadow-sm">
<div class="card-body">
    <table id="venueTable" class="table table-striped table-bordered" style="width:100%">
        <thead style="background-color:#f8bbd0; color:#e91e63;">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $counter = 1;
            foreach($venues as $v): ?>
            <tr>
             <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($v['name']) ?></td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-pink dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="edit_venue.php?id=<?= $v['id'] ?>">Edit</a></li>
                            <li><a class="dropdown-item text-danger" href="manage_venues.php?delete=<?= $v['id'] ?>">Delete</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
</main>

<footer class="text-center py-3 mt-auto">
    © <?= date('Y') ?> TMS. All rights reserved.
</footer>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#venueTable').DataTable({
        "pageLength": 10,
        "lengthChange": false,
        responsive: true
    });
});
</script>
</body>
</html>
