<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

$users_count = count(query("SELECT id_user FROM tb_user"));
$area_count = count(query("SELECT id_area FROM tb_area_parkir"));
$transaksi_aktif = count(query("SELECT id_parkir FROM tb_transaksi WHERE status='masuk'"));

// Fetch recent 5 transactions
$query_recent = "
    SELECT t.*, k.plat_nomor, u.nama_lengkap as nama_petugas 
    FROM tb_transaksi t 
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
    JOIN tb_user u ON t.id_user = u.id_user 
    ORDER BY t.waktu_masuk DESC LIMIT 5
";
$recent_transactions = query($query_recent);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fa-solid fa-square-parking"></i> PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Admin</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="user_manage.php">Kelola User</a>
            <a href="tarif_manage.php">Kelola Tarif</a>
            <a href="area_manage.php">Kelola Area</a>
            <a href="log_aktivitas.php">Log Aktivitas</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><i class="fa-regular fa-circle-user" style="margin-right: 6px;"></i><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Logout</a>
        </div>
    </nav>
    <div class="container">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h2 style="margin-bottom: 0;">Selamat Datang, <?= $_SESSION['user']['nama_lengkap'] ?>!</h2>
            </div>
            <div class="welcome-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color: #3b82f6; background: #dbeafe;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total User</h3>
                    <p><?= $users_count ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #10b981; background: #d1fae5;">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="stat-info">
                    <h3>Area Parkir</h3>
                    <p><?= $area_count ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #f59e0b; background: #fef3c7;">
                    <i class="fa-solid fa-car"></i>
                </div>
                <div class="stat-info">
                    <h3>Parkir Aktif</h3>
                    <p><?= $transaksi_aktif ?></p>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; margin-top: 40px;">
            <h2 style="margin:0; font-size: 20px; color: var(--text-dark);"><i class="fa-solid fa-clock-rotate-left" style="margin-right: 8px; color: var(--primary);"></i> Transaksi Terakhir</h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Plat Nomor</th>
                        <th>Waktu Masuk</th>
                        <th>Status</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($recent_transactions)): ?>
                    <tr><td colspan="4" style="text-align: center; padding: 40px 0; color: var(--text-muted);"><i class="fa-solid fa-inbox fa-3x" style="margin-bottom:10px; opacity: 0.5;"></i><br>Belum ada transaksi saat ini.</td></tr>
                    <?php else: ?>
                        <?php foreach($recent_transactions as $tx): ?>
                        <tr>
                            <td><span style="font-weight: 600; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #e5e7eb; font-family: monospace; font-size: 14px;"><i class="fa-solid fa-car-side" style="margin-right: 8px; color: var(--text-muted);"></i><?= $tx['plat_nomor'] ?></span></td>
                            <td><?= date('d M Y, H:i', strtotime($tx['waktu_masuk'])) ?></td>
                            <td>
                                <?php if($tx['status'] == 'masuk'): ?>
                                    <span style="background: #dbeafe; color: #1d4ed8; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;"><i class="fa-solid fa-arrow-right-to-bracket" style="margin-right:4px;"></i> Masuk</span>
                                <?php else: ?>
                                    <span style="background: #d1fae5; color: #047857; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right:4px;"></i> Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td><i class="fa-regular fa-user" style="margin-right:6px; color: var(--text-muted);"></i> <?= $tx['nama_petugas'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
