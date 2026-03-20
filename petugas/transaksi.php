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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fa-solid fa-square-parking"></i> PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Petugas Loket</span>
        </div>
        <div class="nav-links">
            <a href="transaksi.php" class="active">Parkir Aktif</a>
            <a href="masuk.php">Check-In Kendaraan</a>
            <a href="keluar.php">Check-Out Kendaraan</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><i class="fa-regular fa-circle-user" style="margin-right: 6px;"></i><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Logout</a>
        </div>
    </nav>
    <div class="container">
        <!-- Welcome Banner -->
        <div class="welcome-banner" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <div class="welcome-text">
                <h2 style="margin-bottom: 0;">Selamat Datang, <?= $_SESSION['user']['nama_lengkap'] ?>!</h2>
                <p style="margin-top: 10px;">Selamat bertugas hari ini, <?= date('d F Y') ?>. Tetap semangat melayani!</p>
            </div>
            <div class="welcome-icon">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="margin: 0;"><i class="fa-solid fa-car-side" style="margin-right: 8px; color: var(--primary);"></i> Kendaraan Sedang Parkir</h2>
            <a href="masuk.php" class="btn-custom" style="width: auto; padding: 10px 20px;"><i class="fa-solid fa-plus"></i> Check-In Baru</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Plat Nomor</th>
                        <th>Area</th>
                        <th>Waktu Masuk</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($aktif)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-car-tunnel fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i><br>
                            Tidak ada kendaraan yang sedang parkir saat ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no=1; foreach($aktif as $row): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 500;"><?= $no++; ?></td>
                            <td><span style="font-weight: 600; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #e5e7eb; font-family: monospace; font-size: 14px;"><i class="fa-solid fa-car-rear" style="margin-right: 8px; color: var(--text-muted);"></i><?= $row['plat_nomor'] ?></span></td>
                            <td><?= $row['nama_area'] ?></td>
                            <td style="color: var(--text-muted); font-size: 14px;"><?= date('d M Y, H:i', strtotime($row['waktu_masuk'])) ?></td>
                            <td style="text-align: right;">
                                <a href="keluar.php?id=<?= $row['id_parkir'] ?>" class="btn-custom btn-danger" style="padding: 6px 16px; font-size: 13px; width: auto; background: #ef4444;" onclick="return confirm('Proses Check-Out kendaraan ini?')"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Check-Out</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>