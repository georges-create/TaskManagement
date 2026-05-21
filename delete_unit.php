<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = (int)$_GET['id'];
    // Delete assignment
    $stmt = $pdo->prepare("DELETE FROM timetable WHERE id=?");
    $stmt->execute([$id]);
    $msg = "✅ Assignment deleted successfully!";
    header("Location: assign_units.php?msg=".urlencode($msg)."&type=success");
    exit;
} else {
    header("Location: assign_units.php");
    exit;
}
?>
