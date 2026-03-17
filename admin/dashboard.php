<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

$users_count = count(query("SELECT id_user FROM tb_user"));
$area_count = count(query("SELECT id_area FROM tb_area_parkir"));
$transaksi_aktif = count(query("SELECT id_parkir FROM tb_transaksi WHERE status='masuk'"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            🅿️ PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Admin</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="user_manage.php">Kelola User</a>
            <a href="tarif_manage.php">Kelola Tarif</a>
            <a href="area_manage.php">Kelola Area</span></a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout">Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2>Dashboard Admin</h2>
        </div>
        <div class="stats-grid">
            <div style="background: white; padding: 20px; border-radius: 10px; flex: 1; text-align: center;">
                <h3>Total User</h3>
                <p style="font-size: 24px; font-weight: bold;"><?= $users_count ?></p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; flex: 1; text-align: center;">
                <h3>Area Parkir</h3>
                <p style="font-size: 24px; font-weight: bold;"><?= $area_count ?></p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; flex: 1; text-align: center;">
                <h3>Parkir Aktif</h3>
                <p style="font-size: 24px; font-weight: bold;"><?= $transaksi_aktif ?></p>
            </div>
        </div>
    </div>
</body>
</html>
