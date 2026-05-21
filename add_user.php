<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$message = '';
$alert_type = '';

// Handle messages from GET (edit/delete redirects)
if (!empty($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
    $alert_type = !empty($_GET['type']) ? $_GET['type'] : 'success';
}

// Handle form submission
if (isset($_POST['add_unit'])) {
    $unit_name = trim($_POST['unit_name']);
    $unit_code = trim($_POST['unit_code']);
    $program   = $_POST['program'];
    $year      = $_POST['year'];
    $semester  = $_POST['semester'];

    // Prevent duplicate unit
    $stmt = $pdo->prepare("SELECT id FROM units WHERE name=? AND code=? AND program=? AND year=? AND semester=?");
    $stmt->execute([$unit_name, $unit_code, $program, $year, $semester]);

    if ($stmt->rowCount() > 0) {
        $message = "❌ Unit already exists!";
        $alert_type = "danger";
    } else {
        $stmt = $pdo->prepare("INSERT INTO units (name, code, program, year, semester) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$unit_name, $unit_code, $program, $year, $semester]);
        $message = "✅ Unit '$unit_name' added successfully!";
        $alert_type = "success";
    }
}

// Fetch existing units (latest 10)
$units = $pdo->query("SELECT * FROM units ORDER BY id DESC LIMIT 10")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Unit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="../assets/css/font.css" rel="stylesheet">
    <style>
        body {
            background: #fff0f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        .navbar-pink {
            background-color: #e91e63 !important;
        }

        .navbar-pink .navbar-brand,
        .navbar-pink .btn,
        .navbar-pink span {
            color: #ffe4f0 !important;
        }

        .btn-pink {
            background: #e91e63;
            color: #fff;
        }

        .btn-pink:hover {
            background: #d81b60;
            color: #fff;
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        footer {
            background: #e91e63;
            color: #ffe4f0;
            font-size: 14px;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .table .dropdown {
            display: inline-block;
        }

        @media(max-width:768px) {

            .table thead th:nth-child(3),
            .table thead th:nth-child(4),
            .table thead th:nth-child(5),
            .table thead th:nth-child(6),
            .table tbody td:nth-child(3),
            .table tbody td:nth-child(4),
            .table tbody td:nth-child(5),
            .table tbody td:nth-child(6) {
                display: none;
            }
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
            <div class="d-flex ms-auto align-items-center">
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

        <h2 class="mb-4 text-pink">Add Unit</h2>

        <!-- Alert Message -->
        <?php if ($message): ?>
            <div class="alert alert-<?= $alert_type ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Compact horizontal form -->
        <form method="post" class="mb-3 card p-3 shadow-sm">
            <div class="row g-2 align-items-center">
                <div class="col-md-2">
                    <input type="text" name="unit_name" class="form-control form-control-sm" placeholder="Unit Name" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="unit_code" class="form-control form-control-sm" placeholder="Unit Code" required>
                </div>
                <div class="col-md-2">
                    <select name="program" class="form-select form-select-sm" required>
                        <option value="">Program</option>
                        <option value="BCSIT">BCSIT</option>
                        <option value="BBIT">BBIT</option>
                        <option value="BED">BED</option>
                        <option value="BBM">BBM</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="year" class="form-select form-select-sm" required>
                        <option value="">Year</option>
                        <option value="Y1">Y1</option>
                        <option value="Y2">Y2</option>
                        <option value="Y3">Y3</option>
                        <option value="Y4">Y4</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="semester" class="form-select form-select-sm" required>
                        <option value="">Semester</option>
                        <option value="SEM1">SEM1</option>
                        <option value="SEM2">SEM2</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-pink btn-sm" name="add_unit">Add Unit</button>
                </div>
            </div>
        </form>

        <h4 class="text-pink">Existing Units - Latest 10</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-pink text-white">
                    <tr>
                        <th>ID</th>
                        <th>Unit Name</th>
                        <th>Unit Code</th>
                        <th>Program</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                    foreach ($units as $u): ?>
                        <tr>
                               <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($u['name']) ?></td>
                            <td><?= htmlspecialchars($u['code']) ?></td>
                            <td><?= $u['program'] ?></td>
                            <td><?= $u['year'] ?></td>
                            <td><?= $u['semester'] ?></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-pink dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="edit_unit.php?id=<?= $u['id'] ?>">Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="delete_unit.php?id=<?= $u['id'] ?>">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="text-center py-3 mt-auto">
        © <?= date('Y') ?> TMS. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>