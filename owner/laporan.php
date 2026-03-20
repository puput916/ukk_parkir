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
    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fa-solid fa-square-parking"></i> PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Owner Panel</span>
        </div>
        <div class="nav-links">
            <a href="laporan.php" class="active">Laporan Pendapatan</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><i class="fa-regular fa-circle-user" style="margin-right: 6px;"></i><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Logout</a>
        </div>
    </nav>
    <div class="container">
        
        <!-- Welcome Banner for Owner -->
        <div class="welcome-banner" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="welcome-text">
                <h2 style="margin-bottom: 0;">Selamat Datang, <?= $_SESSION['user']['nama_lengkap'] ?>!</h2>
                <p style="margin-top: 10px;">Pantau terus laporan pendapatan parkir dari tanggal <strong><?= date('d M Y', strtotime($start_date)) ?></strong> hingga <strong><?= date('d M Y', strtotime($end_date)) ?></strong>.</p>
            </div>
            <div class="welcome-icon">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>

        <div style="display: flex; gap: 24px; margin-bottom: 30px; flex-wrap: wrap;">
            <!-- Filter box -->
            <div style="background: white; padding: 20px 24px; border-radius: 16px; border: 1px solid var(--border); flex: 2; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; margin: 0;">
                    <div style="flex: 1;">
                        <label style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;"><i class="fa-regular fa-calendar" style="margin-right: 5px;"></i> Mulai Tanggal</label>
                        <input type="date" name="start_date" value="<?= $start_date ?>" style="margin-bottom: 0;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;"><i class="fa-regular fa-calendar-check" style="margin-right: 5px;"></i> Sampai Tanggal</label>
                        <input type="date" name="end_date" value="<?= $end_date ?>" style="margin-bottom: 0;">
                    </div>
                    <div>
                        <button type="submit" style="padding: 12px 24px; background: var(--primary); margin-bottom: 0; border-radius: 8px; font-size: 14px;"><i class="fa-solid fa-filter" style="margin-right: 8px;"></i> Terapkan Filter</button>
                    </div>
                </form>
            </div>

            <!-- Summary Card -->
            <div class="stat-card" style="flex: 1; margin: 0; background: #f0fdf4; border: 1px solid #bbf7d0;">
                <div class="stat-icon" style="color: #047857; background: #d1fae5;">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3 style="color: #047857;">Total Pendapatan (Periode Ini)</h3>
                    <p style="color: #065f46; font-size: 26px;">Rp <?= number_format($total_pendapatan) ?></p>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 20px;"><i class="fa-solid fa-list-check" style="margin-right: 8px; color: var(--text-muted);"></i> Rincian Transaksi</h2>
            <button class="btn-custom" style="width: auto; padding: 10px 20px; background: #1f2937; border-radius: 8px; font-size: 14px;" onclick="window.print()"><i class="fa-solid fa-print" style="margin-right: 8px;"></i> Cetak Laporan</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Plat Nomor</th>
                        <th>Area</th>
                        <th>Waktu Keluar</th>
                        <th style="text-align: right;">Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($laporan)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-folder-open fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i><br>
                            Belum ada transaksi parkir keluar pada periode ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no=1; foreach($laporan as $row) : ?>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 500;"><?= $no++; ?></td>
                            <td><span style="font-weight: 600; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #e5e7eb; font-family: monospace; font-size: 14px;"><i class="fa-solid fa-car-rear" style="margin-right: 8px; color: var(--text-muted);"></i><?= $row['plat_nomor']; ?></span></td>
                            <td><?= $row['nama_area']; ?></td>
                            <td style="color: var(--text-dark); font-size: 14px;"><?= date('d M Y, H:i', strtotime($row['waktu_keluar'])); ?></td>
                            <td style="font-weight: 600; color: #10b981; text-align: right; font-size: 15px;">Rp <?= number_format($row['biaya_total']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <style>
        @media print {
            .top-nav, button, form { display: none !important; }
            body { background: white; }
            .container { max-width: 100%; margin: 0; padding: 0; }
            .stat-card, .table-container, .welcome-banner { box-shadow: none !important; border: 1px solid #ccc !important; }
        }
    </style>
</body>
</html>