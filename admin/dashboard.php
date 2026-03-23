<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

$users_count = count(query("SELECT id_user FROM tb_user"));
$area_count = count(query("SELECT id_area FROM tb_area_parkir"));
$transaksi_aktif = count(query("SELECT id_parkir FROM tb_transaksi WHERE status='masuk'"));

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
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="../assets/logo_web.png" alt="Logo">
            </div>
            <div class="sidebar-label">Menu</div>
                        <ul class="sidebar-menu">
                <a href="dashboard.php" class="active"><span class="icon-box"><i class="fa-solid fa-gauge-high"></i></span> <span>Dashboard</span></a>
                <a href="user_manage.php" ><span class="icon-box"><i class="fa-solid fa-users"></i></span> <span>Kelola User</span></a>
                <a href="tarif_manage.php" ><span class="icon-box"><i class="fa-solid fa-tags"></i></span> <span>Kelola Tarif</span></a>
                <a href="area_manage.php" ><span class="icon-box"><i class="fa-solid fa-map-location-dot"></i></span> <span>Kelola Area</span></a>
                <a href="log_aktivitas.php" ><span class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></span> <span>Log Aktivitas</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info">
                    <div class="avatar"><i class="fa-solid fa-user"></i></div>
                    <div class="user-detail">
                        <span><?= $_SESSION['user']['nama_lengkap'] ?></span>
                        <small><?= $_SESSION['user']['role'] ?></small>
                    </div>
                </div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h2>Selamat Datang, <?= $_SESSION['user']['nama_lengkap'] ?>!</h2>
                    <p>Kelola sistem parkir dengan mudah dari dashboard admin.</p>
                </div>
                <div class="welcome-icon"><i class="fa-solid fa-chart-line"></i></div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="color: var(--pink-600); background: var(--pink-100);"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info"><h3>Total User</h3><p><?= $users_count ?></p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color: #059669; background: #d1fae5;"><i class="fa-solid fa-layer-group"></i></div>
                    <div class="stat-info"><h3>Area Parkir</h3><p><?= $area_count ?></p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color: #d97706; background: #fef3c7;"><i class="fa-solid fa-car"></i></div>
                    <div class="stat-info"><h3>Parkir Aktif</h3><p><?= $transaksi_aktif ?></p></div>
                </div>
            </div>

            <div class="section-title" style="margin-top: 12px;">
                <h2><i class="fa-solid fa-clock-rotate-left"></i> Transaksi Terakhir</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead><tr><th>Plat Nomor</th><th>Waktu Masuk</th><th>Status</th><th>Petugas</th></tr></thead>
                    <tbody>
                        <?php if(empty($recent_transactions)): ?>
                        <tr><td colspan="4"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>Belum ada transaksi saat ini.</p></div></td></tr>
                        <?php else: ?>
                            <?php foreach($recent_transactions as $tx): ?>
                            <tr>
                                <td><span class="plat-badge"><i class="fa-solid fa-car-side"></i><?= $tx['plat_nomor'] ?></span></td>
                                <td><?= date('d M Y, H:i', strtotime($tx['waktu_masuk'])) ?></td>
                                <td>
                                    <?php if($tx['status'] == 'masuk'): ?>
                                        <span class="badge badge-blue"><i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk</span>
                                    <?php else: ?>
                                        <span class="badge badge-green"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</span>
                                    <?php endif; ?>
                                </td>
                                <td><i class="fa-regular fa-user" style="margin-right:6px; color: var(--text-muted);"></i><?= $tx['nama_petugas'] ?></td>
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
