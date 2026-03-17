<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

if(isset($_POST['tambah'])){
    $nama = $_POST['nama_lengkap'];
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role'];
    exec_query("INSERT INTO tb_user (nama_lengkap, username, password, role) VALUES ('$nama', '$user', '$pass', '$role')");
}

$users = query("SELECT * FROM tb_user");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola User</title>
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
        <h2>Manajemen User</h2>
        <form method="POST" style="background:white; padding:20px; border-radius:10px;">
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role">
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
                <option value="owner">Owner</option>
            </select>
            <button type="submit" name="tambah" class="btn-custom">Tambah User</button>
        </form>

        <table>
            <tr><th>Nama</th><th>Username</th><th>Role</th><th>Status</th></tr>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?= $u['nama_lengkap'] ?></td>
                <td><?= $u['username'] ?></td>
                <td><?= strtoupper($u['role']) ?></td>
                <td><?= $u['status_aktif'] ? 'Aktif' : 'Nonaktif' ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>