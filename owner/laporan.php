<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'owner') header("Location: ../index.php");

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-6 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$query_str = "SELECT t.*, k.plat_nomor, a.nama_area FROM tb_transaksi t
              JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
              JOIN tb_area_parkir a ON t.id_area = a.id_area
              WHERE t.status = 'keluar' AND DATE(t.waktu_keluar) BETWEEN '$start_date' AND '$end_date'
              ORDER BY t.waktu_keluar DESC";
$laporan = query($query_str);

$total_pendapatan = 0;
foreach($laporan as $row) {
    $total_pendapatan += $row['biaya_total'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Owner - Laporan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo"><img src="../assets/logo_web.png" alt="Logo"></div>
            <div class="sidebar-label">Menu</div>
                        <ul class="sidebar-menu">
                <a href="laporan.php" class="active"><span class="icon-box"><i class="fa-solid fa-chart-pie"></i></span> <span>Laporan Pendapatan</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h2>Selamat Datang, <?= $_SESSION['user']['nama_lengkap'] ?>!</h2>
                    <p>Pantau terus laporan pendapatan parkir dari tanggal <strong><?= date('d M Y', strtotime($start_date)) ?></strong> hingga <strong><?= date('d M Y', strtotime($end_date)) ?></strong>.</p>
                </div>
                <div class="welcome-icon"><i class="fa-solid fa-sack-dollar"></i></div>
            </div>

            <div style="display: flex; gap: 24px; margin-bottom: 28px; flex-wrap: wrap;">
                <div class="form-card" style="flex: 2; padding: 20px 24px;">
                    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; margin: 0;">
                        <div style="flex: 1;">
                            <label><i class="fa-regular fa-calendar" style="margin-right: 5px;"></i> Mulai Tanggal</label>
                            <input type="date" name="start_date" value="<?= $start_date ?>" style="margin-bottom: 0;">
                        </div>
                        <div style="flex: 1;">
                            <label><i class="fa-regular fa-calendar-check" style="margin-right: 5px;"></i> Sampai Tanggal</label>
                            <input type="date" name="end_date" value="<?= $end_date ?>" style="margin-bottom: 0;">
                        </div>
                        <div>
                            <button type="submit" class="btn-custom" style="padding: 12px 24px; margin-bottom: 0;"><i class="fa-solid fa-filter"></i> Terapkan</button>
                        </div>
                    </form>
                </div>

                <div class="stat-card" style="flex: 1; margin: 0; background: #fdf2f8; border: 1px solid var(--pink-200);">
                    <div class="stat-icon" style="color: var(--pink-600); background: var(--pink-100);"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h3 style="color: var(--pink-600);">Total Pendapatan</h3>
                        <p style="color: var(--pink-700); font-size: 24px;">Rp <?= number_format($total_pendapatan) ?></p>
                    </div>
                </div>
            </div>

            <div class="section-title">
                <h2><i class="fa-solid fa-list-check"></i> Rincian Transaksi</h2>
                <button class="btn-custom btn-dark" style="width: auto; padding: 10px 20px; font-size: 13px;" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak Laporan</button>
            </div>

            <div class="table-container">
                <table>
                    <thead><tr><th style="width:50px;">No</th><th>Plat Nomor</th><th>Area</th><th>Waktu Keluar</th><th style="text-align:right;">Total Biaya</th></tr></thead>
                    <tbody>
                        <?php if(empty($laporan)): ?>
                        <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-folder-open"></i><p>Belum ada transaksi parkir keluar pada periode ini.</p></div></td></tr>
                        <?php else: ?>
                            <?php $no=1; foreach($laporan as $row) : ?>
                            <tr>
                                <td style="color: var(--text-muted); font-weight: 500;"><?= $no++; ?></td>
                                <td><span class="plat-badge"><i class="fa-solid fa-car-rear"></i><?= $row['plat_nomor']; ?></span></td>
                                <td><?= $row['nama_area']; ?></td>
                                <td style="font-size: 14px;"><?= date('d M Y, H:i', strtotime($row['waktu_keluar'])); ?></td>
                                <td style="font-weight: 700; color: var(--pink-600); text-align: right; font-size: 15px;">Rp <?= number_format($row['biaya_total']); ?></td>
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