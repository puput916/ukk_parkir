<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'petugas') header("Location: ../index.php");

$aktif = query("SELECT t.*, k.plat_nomor, a.nama_area FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                JOIN tb_area_parkir a ON t.id_area = a.id_area 
                WHERE t.status = 'masuk'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Petugas - Transaksi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h3>PETUGAS PANEL</h3>
        <a href="transaksi.php">Parkir Aktif</a>
        <a href="masuk.php">Check-In</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="main-content">
        <h2>Kendaraan Sedang Parkir</h2>
        <table>
            <tr>
                <th>Plat Nomor</th><th>Area</th><th>Waktu Masuk</th><th>Aksi</th>
            </tr>
            <?php foreach($aktif as $row): ?>
            <tr>
                <td><?= $row['plat_nomor'] ?></td>
                <td><?= $row['nama_area'] ?></td>
                <td><?= $row['waktu_masuk'] ?></td>
                <td><a href="keluar.php?id=<?= $row['id_parkir'] ?>" style="color:red;" onclick="return confirm('Proses Check-Out kendaraan ini?')">Check-Out</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>