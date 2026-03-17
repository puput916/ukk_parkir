<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_area']);
    $kapasitas = $_POST['kapasitas'];
    exec_query("INSERT INTO tb_area_parkir (nama_area, kapasitas) VALUES ('$nama', '$kapasitas')");
}
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    exec_query("DELETE FROM tb_area_parkir WHERE id_area = $id");
    header("Location: area_manage.php");
}

$areas = query("SELECT * FROM tb_area_parkir");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Area Parkir</title>
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
        <h2>Manajemen Area Parkir</h2>
        <form method="POST" style="background:white; padding:20px; border-radius:10px;">
            <input type="text" name="nama_area" placeholder="Nama Area" required>
            <input type="number" name="kapasitas" placeholder="Kapasitas" required>
            <button type="submit" name="tambah" class="btn-custom">Tambah Area</button>
        </form>

        <table>
            <tr><th>ID</th><th>Nama Area</th><th>Kapasitas</th><th>Terisi</th><th>Aksi</th></tr>
            <?php foreach($areas as $a): ?>
            <tr>
                <td><?= $a['id_area'] ?></td>
                <td><?= $a['nama_area'] ?></td>
                <td><?= $a['kapasitas'] ?></td>
                <td><?= $a['terisi'] ?></td>
                <td>
                    <a href="?hapus=<?= $a['id_area'] ?>" style="color:red;" onclick="return confirm('Hapus area ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
