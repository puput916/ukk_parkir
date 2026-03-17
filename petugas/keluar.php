<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'petugas') header("Location: ../index.php");
include '../config/helper.php';

$id = $_GET['id'];
$now = date('Y-m-d H:i:s');

$tr = query("SELECT t.*, r.tarif_per_jam FROM tb_transaksi t 
             JOIN tb_tarif r ON t.id_tarif = r.id_tarif WHERE id_parkir = $id")[0];

$kalkulasi = hitungBiaya($tr['waktu_masuk'], $now, $tr['tarif_per_jam']);

mysqli_query($conn, "UPDATE tb_transaksi SET 
    waktu_keluar = '$now', 
    durasi_jam = '{$kalkulasi['durasi']}', 
    biaya_total = '{$kalkulasi['total']}', 
    status = 'keluar' 
    WHERE id_parkir = $id");

mysqli_query($conn, "UPDATE tb_area_parkir SET terisi = terisi - 1 WHERE id_area = {$tr['id_area']}");

header("Location: cetak_struk.php?id=$id");
?>