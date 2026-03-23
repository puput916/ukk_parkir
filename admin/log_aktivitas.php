<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

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
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo"><img src="../assets/logo_web.png" alt="Logo"></div>
            <div class="sidebar-label">Menu</div>
                        <ul class="sidebar-menu">
                <a href="dashboard.php" ><span class="icon-box"><i class="fa-solid fa-gauge-high"></i></span> <span>Dashboard</span></a>
                <a href="user_manage.php" ><span class="icon-box"><i class="fa-solid fa-users"></i></span> <span>Kelola User</span></a>
                <a href="tarif_manage.php" ><span class="icon-box"><i class="fa-solid fa-tags"></i></span> <span>Kelola Tarif</span></a>
                <a href="area_manage.php" ><span class="icon-box"><i class="fa-solid fa-map-location-dot"></i></span> <span>Kelola Area</span></a>
                <a href="log_aktivitas.php" class="active"><span class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></span> <span>Log Aktivitas</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h2>Log Aktivitas Sistem</h2>
                    <p>Pantau seluruh pergerakan check-in dan check-out kendaraan oleh petugas.</p>
                </div>
                <div class="welcome-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            </div>

            <div class="section-title">
                <h2><i class="fa-solid fa-timeline"></i> 100 Aktivitas Terakhir</h2>
            </div>

            <div class="table-container">
                <table>
                    <thead><tr><th>No</th><th>Waktu Aktivitas</th><th>Jenis</th><th>Plat Nomor</th><th>Petugas</th></tr></thead>
                    <tbody>
                        <?php if(empty($logs)): ?>
                        <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-wind"></i><p>Belum ada riwayat aktivitas yang tercatat.</p></div></td></tr>
                        <?php else: ?>
                            <?php $no=1; foreach($logs as $log): ?>
                            <tr>
                                <td style="color: var(--text-muted); font-weight: 500;"><?= $no++; ?></td>
                                <td style="font-weight: 500;"><?= date('d M Y, H:i:s', strtotime($log['waktu'])) ?></td>
                                <td>
                                    <?php if($log['jenis_aktivitas'] == 'Masuk'): ?>
                                        <span class="badge badge-blue"><i class="fa-solid fa-arrow-right-to-bracket"></i> Check-In</span>
                                    <?php else: ?>
                                        <span class="badge badge-green"><i class="fa-solid fa-arrow-right-from-bracket"></i> Check-Out</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="plat-badge"><i class="fa-solid fa-car-side"></i><?= $log['plat_nomor'] ?></span></td>
                                <td><i class="fa-regular fa-user" style="margin-right:6px; color: var(--text-muted);"></i><?= $log['petugas'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
