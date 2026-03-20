<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

// Fetch activities chronologically using UNION
$query_log = "
    SELECT id_parkir, plat_nomor, nama_lengkap as petugas, waktu, jenis_aktivitas FROM (
        SELECT t.id_parkir, k.plat_nomor, u.nama_lengkap, t.waktu_masuk as waktu, 'Masuk' as jenis_aktivitas 
        FROM tb_transaksi t 
        JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
        JOIN tb_user u ON t.id_user = u.id_user
        
        UNION ALL
        
        SELECT t.id_parkir, k.plat_nomor, u.nama_lengkap, t.waktu_keluar as waktu, 'Keluar' as jenis_aktivitas 
        FROM tb_transaksi t 
        JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
        JOIN tb_user u ON t.id_user = u.id_user 
        WHERE t.waktu_keluar IS NOT NULL
    ) AS activity_log
    ORDER BY waktu DESC LIMIT 100
";

$logs = query($query_log);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Log Aktivitas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fa-solid fa-square-parking"></i> PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Admin</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="user_manage.php">Kelola User</a>
            <a href="tarif_manage.php">Kelola Tarif</a>
            <a href="area_manage.php">Kelola Area</a>
            <a href="log_aktivitas.php" class="active">Log Aktivitas</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><i class="fa-regular fa-circle-user" style="margin-right: 6px;"></i><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Logout</a>
        </div>
    </nav>
    <div class="container">
        <!-- Welcome Banner -->
        <div class="welcome-banner" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="welcome-text">
                <h2 style="margin-bottom: 0;">Log Aktivitas</h2>
                <p style="margin-top: 10px;">Pantau seluruh pergerakan check-in dan check-out kendaraan oleh petugas secara real-time.</p>
            </div>
            <div class="welcome-icon">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 20px;"><i class="fa-solid fa-timeline" style="margin-right: 8px; color: var(--text-muted);"></i> 100 Aktivitas Terakhir</h2>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Waktu Aktivitas</th>
                        <th>Jenis</th>
                        <th>Plat Nomor</th>
                        <th>Petugas Pelaksana</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($logs)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-wind fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i><br>
                            Belum ada riwayat aktivitas yang tercatat.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no=1; foreach($logs as $log): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 500;"><?= $no++; ?></td>
                            <td style="color: var(--text-dark); font-size: 14px; font-weight: 500;"><?= date('d M Y, H:i:s', strtotime($log['waktu'])) ?></td>
                            <td>
                                <?php if($log['jenis_aktivitas'] == 'Masuk'): ?>
                                    <span style="background: #dbeafe; color: #1d4ed8; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;"><i class="fa-solid fa-arrow-right-to-bracket" style="margin-right:4px;"></i> Check-In</span>
                                <?php else: ?>
                                    <span style="background: #d1fae5; color: #047857; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right:4px;"></i> Check-Out</span>
                                <?php endif; ?>
                            </td>
                            <td><span style="font-weight: 600; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #e5e7eb; font-family: monospace; font-size: 14px;"><i class="fa-solid fa-car-side" style="margin-right: 8px; color: var(--text-muted);"></i><?= $log['plat_nomor'] ?></span></td>
                            <td><i class="fa-regular fa-user" style="margin-right:6px; color: var(--text-muted);"></i> <?= $log['petugas'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
