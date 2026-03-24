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
    <title>Petugas - Parkir Aktif</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo"><img src="../assets/logo_web.png" alt="Logo"></div>
            <div class="sidebar-label">Menu</div>
                        <ul class="sidebar-menu">
                <a href="transaksi.php" class="active"><span class="icon-box"><i class="fa-solid fa-car-side"></i></span> <span>Parkir Aktif</span></a>
                <a href="masuk.php" ><span class="icon-box"><i class="fa-solid fa-right-to-bracket"></i></span> <span>Check-In</span></a>
                <a href="keluar.php" ><span class="icon-box"><i class="fa-solid fa-right-from-bracket"></i></span> <span>Check-Out</span></a>
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
                    <p>Selamat bertugas hari ini, <?= date('d F Y') ?>. Tetap semangat melayani!</p>
                </div>
                <div class="welcome-icon"><i class="fa-solid fa-clipboard-user"></i></div>
            </div>

            <div class="section-title">
                <h2><i class="fa-solid fa-car-side"></i> Kendaraan Sedang Parkir</h2>
                <a href="masuk.php" class="btn-custom" style="width: auto; padding: 10px 20px;"><i class="fa-solid fa-plus"></i> Check-In Baru</a>
            </div>
            <div class="table-container">
                <table>
                    <thead><tr><th style="width:50px;">No</th><th>Plat Nomor</th><th>Area</th><th>Waktu Masuk</th><th style="text-align:right;">Aksi</th></tr></thead>
                    <tbody>
                        <?php if(empty($aktif)): ?>
                        <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-car-tunnel"></i><p>Tidak ada kendaraan yang sedang parkir saat ini.</p></div></td></tr>
                        <?php else: ?>
                            <?php $no=1; foreach($aktif as $row): ?>
                            <tr>
                                <td style="color: var(--text-muted); font-weight: 500;"><?= $no++; ?></td>
                                <td><span class="plat-badge"><i class="fa-solid fa-car-rear"></i><?= $row['plat_nomor'] ?></span></td>
                                <td><?= $row['nama_area'] ?></td>
                                <td style="color: var(--text-muted); font-size: 14px;"><?= date('d M Y, H:i', strtotime($row['waktu_masuk'])) ?></td>
                                <td style="text-align: right;">
                                    <a href="keluar.php?id=<?= $row['id_parkir'] ?>" class="btn-custom btn-danger" style="padding: 6px 14px; font-size: 12px; width: auto;" onclick="return confirm('Proses Check-Out kendaraan ini?')"><i class="fa-solid fa-arrow-right-from-bracket"></i> Check-Out</a>
                                </td>
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