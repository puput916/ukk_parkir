<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') { header("Location: ../index.php"); exit; }

$r1 = mysqli_query($conn, "SHOW COLUMNS FROM tb_tarif LIKE 'status_aktif'");
if(mysqli_num_rows($r1) == 0) {
  mysqli_query($conn, 'ALTER TABLE tb_tarif ADD COLUMN status_aktif TINYINT(1) DEFAULT 1');
  echo "Added status_aktif to tb_tarif<br>";
} else { echo "tb_tarif already has status_aktif<br>"; }

$r2 = mysqli_query($conn, "SHOW COLUMNS FROM tb_area_parkir LIKE 'status_aktif'");
if(mysqli_num_rows($r2) == 0) {
  mysqli_query($conn, 'ALTER TABLE tb_area_parkir ADD COLUMN status_aktif TINYINT(1) DEFAULT 1');
  echo "Added status_aktif to tb_area_parkir<br>";
} else { echo "tb_area_parkir already has status_aktif<br>"; }

echo "<br><strong>Migrasi selesai!</strong> <a href='dashboard.php'>Kembali ke Dashboard</a>";
