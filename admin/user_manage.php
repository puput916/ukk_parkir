<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

$msg = '';

// TAMBAH
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];
    $role = $_POST['role'];
    // Cek username unik
    $cek = query("SELECT id_user FROM tb_user WHERE username='$user'");
    if(count($cek) > 0){
        $msg = 'Username sudah digunakan!';
    } else {
        exec_query("INSERT INTO tb_user (nama_lengkap, username, password, role) VALUES ('$nama', '$user', '$pass', '$role')");
        $msg = 'User berhasil ditambahkan!';
    }
}

// EDIT
if(isset($_POST['edit'])){
    $id = (int)$_POST['id_user'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    $sql = "UPDATE tb_user SET nama_lengkap='$nama', username='$user', role='$role'";
    // Update password hanya jika diisi
    if(!empty($_POST['password'])){
        $pass = $_POST['password'];
        $sql .= ", password='$pass'";
    }
    $sql .= " WHERE id_user=$id";
    exec_query($sql);
    $msg = 'User berhasil diperbarui!';
}

// HAPUS
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    // Jangan hapus diri sendiri
    if($id == $_SESSION['user']['id_user']){
        $msg = 'Tidak bisa menghapus akun sendiri!';
    } else {
        exec_query("DELETE FROM tb_user WHERE id_user = $id");
        header("Location: user_manage.php?msg=deleted");
        exit;
    }
}

// TOGGLE STATUS
if(isset($_GET['toggle'])){
    $id = (int)$_GET['toggle'];
    // Jangan toggle diri sendiri
    if($id == $_SESSION['user']['id_user']){
        header("Location: user_manage.php");
        exit;
    }
    $current = query("SELECT status_aktif FROM tb_user WHERE id_user=$id");
    if(count($current) > 0){
        $new = $current[0]['status_aktif'] ? 0 : 1;
        exec_query("UPDATE tb_user SET status_aktif=$new WHERE id_user=$id");
    }
    header("Location: user_manage.php");
    exit;
}

if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') $msg = 'User berhasil dihapus!';

$users = query("SELECT * FROM tb_user ORDER BY id_user DESC");

// Ambil data edit jika ada
$edit_data = null;
if(isset($_GET['edit_id'])){
    $eid = (int)$_GET['edit_id'];
    $ed = query("SELECT * FROM tb_user WHERE id_user=$eid");
    if(count($ed) > 0) $edit_data = $ed[0];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola User</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo"><img src="../assets/logo_web.png" alt="Logo"></div>
            <div class="sidebar-label">Menu</div>
            <ul class="sidebar-menu">
                <a href="dashboard.php"><span class="icon-box"><i class="fa-solid fa-gauge-high"></i></span> <span>Dashboard</span></a>
                <a href="user_manage.php" class="active"><span class="icon-box"><i class="fa-solid fa-users"></i></span> <span>Kelola User</span></a>
                <a href="tarif_manage.php"><span class="icon-box"><i class="fa-solid fa-tags"></i></span> <span>Kelola Tarif</span></a>
                <a href="area_manage.php"><span class="icon-box"><i class="fa-solid fa-map-location-dot"></i></span> <span>Kelola Area</span></a>
                <a href="log_aktivitas.php"><span class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></span> <span>Log Aktivitas</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1><i class="fa-solid fa-users"></i>Manajemen User</h1></div>

            <?php if($msg): ?>
                <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?= $msg ?></div>
            <?php endif; ?>

            <div style="display: flex; gap: 28px; align-items: flex-start;">
                <div class="form-card" style="flex: 1; max-width: 380px;">
                    <?php if($edit_data): ?>
                        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 700;"><i class="fa-solid fa-user-pen" style="margin-right: 8px; color: var(--pink-400);"></i>Edit User</h3>
                        <form method="POST">
                            <input type="hidden" name="id_user" value="<?= $edit_data['id_user'] ?>">
                            <label><i class="fa-solid fa-id-card" style="margin-right: 5px; color: var(--text-muted);"></i> Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($edit_data['nama_lengkap']) ?>" required>
                            <label><i class="fa-solid fa-at" style="margin-right: 5px; color: var(--text-muted);"></i> Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($edit_data['username']) ?>" required>
                            <label><i class="fa-solid fa-lock" style="margin-right: 5px; color: var(--text-muted);"></i> Password <small style="color: var(--text-light);">(kosongkan jika tidak diubah)</small></label>
                            <input type="password" name="password" placeholder="Password baru...">
                            <label><i class="fa-solid fa-shield-halved" style="margin-right: 5px; color: var(--text-muted);"></i> Role</label>
                            <select name="role">
                                <option value="admin" <?= $edit_data['role']=='admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="petugas" <?= $edit_data['role']=='petugas' ? 'selected' : '' ?>>Petugas</option>
                                <option value="owner" <?= $edit_data['role']=='owner' ? 'selected' : '' ?>>Owner</option>
                            </select>
                            <button type="submit" name="edit" class="btn-custom"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                            <a href="user_manage.php" class="btn-custom btn-dark" style="text-align:center; margin-top:8px;"><i class="fa-solid fa-xmark"></i> Batal</a>
                        </form>
                    <?php else: ?>
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
                    <?php endif; ?>
                </div>

                <div class="table-container" style="flex: 2;">
                    <table>
                        <thead><tr><th>Nama Lengkap</th><th>Username</th><th>Role</th><th>Status</th><th style="text-align: right;">Aksi</th></tr></thead>
                        <tbody>
                            <?php if(count($users) == 0): ?>
                                <tr><td colspan="5" class="empty-state"><i class="fa-solid fa-users"></i><p>Belum ada data user</p></td></tr>
                            <?php endif; ?>
                            <?php foreach($users as $u): ?>
                            <tr class="<?= $u['status_aktif'] ? '' : 'row-nonaktif' ?>">
                                <td style="font-weight: 600;"><?= htmlspecialchars($u['nama_lengkap']) ?></td>
                                <td><span class="plat-badge" style="font-size: 12px;"><?= htmlspecialchars($u['username']) ?></span></td>
                                <td>
                                    <?php
                                        $r = strtoupper($u['role']);
                                        $badge = $r == 'ADMIN' ? 'badge-purple' : ($r == 'PETUGAS' ? 'badge-blue' : 'badge-amber');
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= $r ?></span>
                                </td>
                                <td>
                                    <?php if($u['status_aktif']): ?>
                                        <span class="badge badge-green"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-muted"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-buttons">
                                        <a href="?edit_id=<?= $u['id_user'] ?>" class="btn-action btn-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <?php if($u['id_user'] != $_SESSION['user']['id_user']): ?>
                                            <a href="?toggle=<?= $u['id_user'] ?>" class="btn-action <?= $u['status_aktif'] ? 'btn-warn' : 'btn-activate' ?>" title="<?= $u['status_aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>"><i class="fa-solid fa-<?= $u['status_aktif'] ? 'ban' : 'check-circle' ?>"></i></a>
                                            <a href="?hapus=<?= $u['id_user'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin hapus user <?= htmlspecialchars($u['nama_lengkap']) ?>?')"><i class="fa-solid fa-trash"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
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