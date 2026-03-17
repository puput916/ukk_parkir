<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

if(isset($_POST['tambah'])){
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $tarif = $_POST['tarif_per_jam'];
    exec_query("INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES ('$jenis', '$tarif')");
}
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    exec_query("DELETE FROM tb_tarif WHERE id_tarif = $id");
    header("Location: tarif_manage.php");
}

$tarifs = query("SELECT * FROM tb_tarif");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Tarif</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h3>ADMIN PANEL</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="user_manage.php">Kelola User</a>
        <a href="tarif_manage.php">Kelola Tarif</a>
        <a href="area_manage.php">Kelola Area Parkir</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="main-content">
        <h2>Manajemen Tarif</h2>
        <form method="POST" style="background:white; padding:20px; border-radius:10px;">
            <input type="text" name="jenis_kendaraan" placeholder="Jenis Kendaraan" required>
            <input type="number" name="tarif_per_jam" placeholder="Tarif Per Jam" required>
            <button type="submit" name="tambah" class="btn-custom">Tambah Tarif</button>
        </form>

        <table>
            <tr><th>ID</th><th>Jenis Kendaraan</th><th>Tarif Per Jam</th><th>Aksi</th></tr>
            <?php foreach($tarifs as $t): ?>
            <tr>
                <td><?= $t['id_tarif'] ?></td>
                <td><?= $t['jenis_kendaraan'] ?></td>
                <td>Rp <?= number_format($t['tarif_per_jam']) ?></td>
                <td>
                    <a href="?hapus=<?= $t['id_tarif'] ?>" style="color:red;" onclick="return confirm('Hapus tarif ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
