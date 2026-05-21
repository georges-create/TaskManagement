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

// Fetch unit name
$stmt = $pdo->prepare("SELECT name FROM units WHERE id=?");
$stmt->execute([$id]);
$unit = $stmt->fetch();
$unit_name = $unit ? $unit['name'] : '';

// Delete unit
$stmt = $pdo->prepare("DELETE FROM units WHERE id=?");
$stmt->execute([$id]);

// Redirect back with success message
header('Location: add_unit.php?msg=' . urlencode("✅ Unit '$unit_name' deleted successfully!") . '&type=success');
exit;
?>
