<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'owner') header("Location: ../index.php");

$laporan = query("SELECT t.*, k.plat_nomor, a.nama_area FROM tb_transaksi t
                  JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                  JOIN tb_area_parkir a ON t.id_area = a.id_area
                  WHERE t.status = 'keluar' ORDER BY t.waktu_keluar DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Owner - Laporan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            🅿️ PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Owner Panel</span>
        </div>
        <div class="nav-links">
            <a href="laporan.php" class="active">Laporan Pendapatan</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout">Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2>Rekap Pendapatan Keseluruhan</h2>
            <button class="btn-custom" style="width: auto; padding: 10px 20px; background: #10b981;" onclick="window.print()">🖨️ Cetak Laporan</button>
        </div>
        <div class="table-container">
            <table>
            <tr>
                <th>No</th><th>Plat Nomor</th><th>Area</th><th>Waktu Keluar</th><th>Total Biaya</th>
            </tr>
            <?php $no=1; $total_semua=0; foreach($laporan as $row) : 
                $total_semua += $row['biaya_total'];
            ?>
            <tr>
                <td style="color: var(--text-muted); font-weight: 500;"><?= $no++; ?></td>
                <td style="font-weight: 600;"><?= $row['plat_nomor']; ?></td>
                <td><?= $row['nama_area']; ?></td>
                <td style="color: var(--text-muted); font-size: 13px;"><?= date('d M Y, H:i', strtotime($row['waktu_keluar'])); ?></td>
                <td style="font-weight: 600; color: #10b981; text-align: right;">Rp <?= number_format($row['biaya_total']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(count($laporan) == 0): ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada riwayat transaksi parkir.</td>
            </tr>
            <?php endif; ?>
            <tr style="background-color: #f9fafb;">
                <th colspan="4" style="text-align: right; font-size: 16px; border-bottom: none;">TOTAL PENDAPATAN BULAN INI:</th>
                <th style="font-size: 18px; color: #10b981; border-bottom: none; text-align: right;">Rp <?= number_format($total_semua); ?></th>
            </tr>
        </table>
        </div>
    </div>
    <style>
        @media print {
            .top-nav, button { display: none !important; }
            body { background: white; }
            .container { box-shadow: none; margin: 0; border: none; }
        }
    </style>
</body>
</html>