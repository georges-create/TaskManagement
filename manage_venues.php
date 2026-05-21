<?php 
session_start();
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Show messages from URL (after Add/Edit)
$msg = $_GET['msg'] ?? '';

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    $msg = "✅ User deleted successfully!";
    // redirect to remove ?delete= from URL
    header("Location: manage_users.php?msg=" . urlencode($msg));
    exit;
}

// Fetch all users
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="../assets/css/font.css" rel="stylesheet">
<style>
body { background:#fff0f6; min-height:100vh; display:flex; flex-direction:column; }
main { flex:1; }
.navbar-pink { background:#e91e63 !important; }
.navbar-pink .navbar-brand, .navbar-pink .btn, .navbar-pink span { color:#ffe4f0 !important; }
.btn-pink { background:#e91e63; color:#fff; }
.btn-pink:hover { background:#d81b60; }
.card { border-radius:12px; border:none; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
footer { background:#e91e63; color:#ffe4f0; font-size:14px; text-align:center; position:fixed; bottom:0; width:100%; padding:10px; }

/* Hide columns on smaller screens for better responsiveness */
@media(max-width:1200px){
    .table thead th.program-col,
    .table thead th.year-col,
    .table thead th.semester-col,
    .table tbody td.program-col,
    .table tbody td.year-col,
    .table tbody td.semester-col { display:none; }
}

@media(max-width:992px){
    .table thead th.email-col,
    .table tbody td.email-col { display:none; }
}

/* Actions dropdown always visible */
.table .dropdown { display:inline-block !important; width:auto !important; }
    
    
/* Large screen adjustments */
@media (min-width: 1400px) { .container { max-width: 1320px; } .table th, .table td { padding: 12px; font-size: 15px; } }
@media (min-width: 1600px) { .container { max-width: 1500px; } body { font-size: 17px; } .card { padding: 10px; } }
@media (min-width: 1920px) { .container { max-width: 1700px; } body { font-size: 18px; } h3 { font-size: 28px; } .btn { font-size: 16px; padding: 8px 18px; } }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-pink shadow-sm mb-3">
<div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
    <div class="d-flex ms-auto align-items-center">
        <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</div>
</nav>

<main class="container flex-grow-1 mb-5">
    <!-- Back & Add User buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <a href="../admin/dashboard.php" class="btn btn-outline-primary mb-2" style="background-color: #d81b60; color:#ffe4f0">
            ← Back to Admin
        </a>
        <a href="add_user.php" class="btn btn-pink mb-2">+ Add User</a>
    </div>

    <h3 class="text-pink mb-3">Manage Users</h3>

    <?php if($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-body p-2">
            <table id="usersTable" class="table table-striped table-bordered w-100 mb-0">
                <thead style="background:#f8bbd0; color:#e91e63;">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th class="email-col">Email</th>
                        <th>Role</th>
                        <th class="program-col">Program</th>
                        <th class="year-col">Year</th>
                        <th class="semester-col">Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $counter = 1;
                    foreach($users as $u): ?>
                    <tr>
                       <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td class="email-col"><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= ucfirst($u['role']) ?></td>
                        <td class="program-col"><?= $u['program'] ?? '-' ?></td>
                        <td class="year-col"><?= $u['year'] ?? '-' ?></td>
                        <td class="semester-col"><?= $u['semester'] ?? '-' ?></td>
                       <td>
    <div class="dropdown">
        <button class="btn btn-sm btn-pink dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Actions
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="edit_user.php?id=<?= $u['id'] ?>">Edit</a></li>
            <li><a class="dropdown-item text-danger" href="?delete=<?= $u['id'] ?>">Delete</a></li>
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

<footer>© <?= date('Y') ?> TMS. All rights reserved.</footer>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        pageLength:10,
        lengthChange:false,
        responsive:true
    });
});
</script>
</body>
</html>
