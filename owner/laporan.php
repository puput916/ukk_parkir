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
    <div class="sidebar">
        <h3>OWNER PANEL</h3>
        <a href="laporan.php">Laporan Pendapatan</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="main-content">
        <h2>Rekap Pendapatan</h2>
        <table>
            <tr>
                <th>No</th><th>Plat Nomor</th><th>Area</th><th>Waktu Keluar</th><th>Total Biaya</th>
            </tr>
            <?php $no=1; $total_semua=0; foreach($laporan as $row) : 
                $total_semua += $row['biaya_total'];
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['plat_nomor']; ?></td>
                <td><?= $row['nama_area']; ?></td>
                <td><?= $row['waktu_keluar']; ?></td>
                <td>Rp <?= number_format($row['biaya_total']); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="4" style="text-align: right;">TOTAL PENDAPATAN KESELURUHAN:</th>
                <th>Rp <?= number_format($total_semua); ?></th>
            </tr>
        </table>
    </div>
</body>
</html>