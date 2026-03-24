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
    <link rel="stylesheet" href="../assets/css/style.css?v=6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo"><img src="../assets/logo_web.png" alt="Logo"></div>
            <div class="sidebar-label">Menu</div>
                        <ul class="sidebar-menu">
                <a href="dashboard.php" ><span class="icon-box"><i class="fa-solid fa-gauge-high"></i></span> <span>Dashboard</span></a>
                <a href="user_manage.php" class="active"><span class="icon-box"><i class="fa-solid fa-users"></i></span> <span>Kelola User</span></a>
                <a href="tarif_manage.php" ><span class="icon-box"><i class="fa-solid fa-tags"></i></span> <span>Kelola Tarif</span></a>
                <a href="area_manage.php" ><span class="icon-box"><i class="fa-solid fa-map-location-dot"></i></span> <span>Kelola Area</span></a>
                <a href="log_aktivitas.php" ><span class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></span> <span>Log Aktivitas</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1><i class="fa-solid fa-users"></i>Manajemen User</h1></div>

            <div style="display: flex; gap: 28px; align-items: flex-start;">
                <div class="form-card" style="flex: 1; max-width: 380px;">
                    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 700;"><i class="fa-solid fa-user-plus" style="margin-right: 8px; color: var(--pink-400);"></i>Tambah User Baru</h3>
                    <form method="POST">
                        <label><i class="fa-solid fa-id-card" style="margin-right: 5px; color: var(--text-muted);"></i> Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
                        <label><i class="fa-solid fa-at" style="margin-right: 5px; color: var(--text-muted);"></i> Username</label>
                        <input type="text" name="username" placeholder="Username" required>
                        <label><i class="fa-solid fa-lock" style="margin-right: 5px; color: var(--text-muted);"></i> Password</label>
                        <input type="password" name="password" placeholder="Password" required>
                        <label><i class="fa-solid fa-shield-halved" style="margin-right: 5px; color: var(--text-muted);"></i> Role</label>
                        <select name="role">
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="owner">Owner</option>
                        </select>
                        <button type="submit" name="tambah" class="btn-custom"><i class="fa-solid fa-plus"></i> Tambah User</button>
                    </form>
                </div>

                <div class="table-container" style="flex: 2;">
                    <table>
                        <thead><tr><th>Nama Lengkap</th><th>Username</th><th>Role</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach($users as $u): ?>
                            <tr>
                                <td style="font-weight: 600;"><?= $u['nama_lengkap'] ?></td>
                                <td><span class="plat-badge" style="font-size: 12px;"><?= $u['username'] ?></span></td>
                                <td>
                                    <?php
                                        $r = strtoupper($u['role']);
                                        $badge = $r == 'ADMIN' ? 'badge-purple' : ($r == 'PETUGAS' ? 'badge-blue' : 'badge-amber');
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= $r ?></span>
                                </td>
                                <td><span class="badge badge-green"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> Aktif</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>