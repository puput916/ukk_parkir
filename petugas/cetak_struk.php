<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'petugas') header("Location: ../index.php");

$id = $_GET['id'];
$tr = query("SELECT t.*, k.plat_nomor, a.nama_area, r.jenis_kendaraan FROM tb_transaksi t
             JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
             JOIN tb_area_parkir a ON t.id_area = a.id_area
             JOIN tb_tarif r ON t.id_tarif = r.id_tarif
             WHERE t.id_parkir = $id");

if(count($tr) == 0) die("Struk tidak ditemukan");
$data = $tr[0];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk Parkir</title>
    <style>
        body { font-family: monospace; width: 300px; margin: auto; padding: 20px; border: 1px dashed black; }
        .text-center { text-align: center; }
        .dashed { border-top: 1px dashed black; margin: 10px 0; }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <h2>Flam Parking</h2>
        <p>Struk Bukti Pembayaran Parkir</p>
    </div>
    <div class="dashed"></div>
    <p>ID Parkir : <?= $data['id_parkir'] ?></p>
    <p>Plat Nomor: <?= $data['plat_nomor'] ?></p>
    <p>Kendaraan : <?= $data['jenis_kendaraan'] ?></p>
    <p>Area Parkir: <?= $data['nama_area'] ?></p>
    <p>Masuk  : <?= $data['waktu_masuk'] ?></p>
    <p>Keluar : <?= $data['waktu_keluar'] ?></p>
    <p>Durasi : <?= $data['durasi_jam'] ?> jam</p>
    <div class="dashed"></div>
    <h3 class="text-center">TOTAL: Rp <?= number_format($data['biaya_total']) ?></h3>
    <div class="dashed"></div>
    <div class="text-center" style="margin-top:20px;">
        <p>Terima Kasih</p>
        <button onclick="window.location.href='transaksi.php'" class="no-print">Kembali</button>
    </div>
    <style> @media print { .no-print { display: none; } } </style>
</body>
</html>
