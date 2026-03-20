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
    <nav class="top-nav">
        <div class="nav-brand">
            PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Admin</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="user_manage.php" class="active">Kelola User</a>
            <a href="tarif_manage.php">Kelola Tarif</a>
            <a href="area_manage.php">Kelola Area</a>
            <a href="log_aktivitas.php">Log Aktivitas</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout">Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2>Kelola User</h2>
        </div>
        <div style="display: flex; gap: 30px; align-items: flex-start;">
            <div class="form-card" style="flex: 1; max-width: 400px;">
                <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px;">Tambah User Baru</h3>
                <form method="POST">
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
            </div>

            <div class="table-container" style="flex: 2; margin-top: 0;">
                <table>
                    <tr><th>Nama Lengkap</th><th>Username</th><th>Role</th><th>Status</th></tr>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td><?= $u['nama_lengkap'] ?></td>
                        <td><span style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 13px;"><?= $u['username'] ?></span></td>
                        <td>
                            <?php 
                                $r = strtoupper($u['role']); 
                                $c = $r == 'ADMIN' ? '#8b5cf6' : ($r == 'PETUGAS' ? '#3b82f6' : '#f59e0b');
                            ?>
                            <span style="background: <?= $c ?>20; color: <?= $c ?>; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;"><?= $r ?></span>
                        </td>
                        <td>
                            <span style="color: #10b981; font-weight: 500; font-size: 13px;">● Aktif</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>