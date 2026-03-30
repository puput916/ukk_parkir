<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

$msg = '';

// TAMBAH
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_area']);
    $kapasitas = (int)$_POST['kapasitas'];
    exec_query("INSERT INTO tb_area_parkir (nama_area, kapasitas) VALUES ('$nama', '$kapasitas')");
    $msg = 'Area berhasil ditambahkan!';
}

// EDIT
if(isset($_POST['edit'])){
    $id = (int)$_POST['id_area'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_area']);
    $kapasitas = (int)$_POST['kapasitas'];
    exec_query("UPDATE tb_area_parkir SET nama_area='$nama', kapasitas='$kapasitas' WHERE id_area=$id");
    $msg = 'Area berhasil diperbarui!';
}

// HAPUS
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    exec_query("DELETE FROM tb_area_parkir WHERE id_area = $id");
    header("Location: area_manage.php?msg=deleted");
    exit;
}

// TOGGLE STATUS
if(isset($_GET['toggle'])){
    $id = (int)$_GET['toggle'];
    $current = query("SELECT status_aktif FROM tb_area_parkir WHERE id_area=$id");
    if(count($current) > 0){
        $new = $current[0]['status_aktif'] ? 0 : 1;
        exec_query("UPDATE tb_area_parkir SET status_aktif=$new WHERE id_area=$id");
    }
    header("Location: area_manage.php");
    exit;
}

if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') $msg = 'Area berhasil dihapus!';

$areas = query("SELECT * FROM tb_area_parkir ORDER BY id_area DESC");

// Ambil data edit jika ada
$edit_data = null;
if(isset($_GET['edit_id'])){
    $eid = (int)$_GET['edit_id'];
    $ed = query("SELECT * FROM tb_area_parkir WHERE id_area=$eid");
    if(count($ed) > 0) $edit_data = $ed[0];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Area Parkir</title>
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
                <a href="user_manage.php"><span class="icon-box"><i class="fa-solid fa-users"></i></span> <span>Kelola User</span></a>
                <a href="tarif_manage.php"><span class="icon-box"><i class="fa-solid fa-tags"></i></span> <span>Kelola Tarif</span></a>
                <a href="area_manage.php" class="active"><span class="icon-box"><i class="fa-solid fa-map-location-dot"></i></span> <span>Kelola Area</span></a>
                <a href="log_aktivitas.php"><span class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></span> <span>Log Aktivitas</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1><i class="fa-solid fa-map-location-dot"></i>Pemetaan Area Parkir</h1></div>

            <?php if($msg): ?>
                <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?= $msg ?></div>
            <?php endif; ?>

            <div style="display: flex; gap: 28px; align-items: flex-start;">
                <div class="form-card" style="flex: 1; max-width: 380px;">
                    <?php if($edit_data): ?>
                        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 700;"><i class="fa-solid fa-pen-to-square" style="margin-right: 8px; color: var(--pink-400);"></i>Edit Area</h3>
                        <form method="POST">
                            <input type="hidden" name="id_area" value="<?= $edit_data['id_area'] ?>">
                            <label><i class="fa-solid fa-location-dot" style="margin-right: 5px; color: var(--text-muted);"></i> Nama Area</label>
                            <input type="text" name="nama_area" value="<?= htmlspecialchars($edit_data['nama_area']) ?>" required>
                            <label><i class="fa-solid fa-hashtag" style="margin-right: 5px; color: var(--text-muted);"></i> Kapasitas</label>
                            <input type="number" name="kapasitas" value="<?= $edit_data['kapasitas'] ?>" required>
                            <button type="submit" name="edit" class="btn-custom"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                            <a href="area_manage.php" class="btn-custom btn-dark" style="text-align:center; margin-top:8px;"><i class="fa-solid fa-xmark"></i> Batal</a>
                        </form>
                    <?php else: ?>
                        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 700;"><i class="fa-solid fa-plus-circle" style="margin-right: 8px; color: var(--pink-400);"></i>Tambah Lokasi Baru</h3>
                        <form method="POST">
                            <label><i class="fa-solid fa-location-dot" style="margin-right: 5px; color: var(--text-muted);"></i> Nama Area</label>
                            <input type="text" name="nama_area" placeholder="Nama Area" required>
                            <label><i class="fa-solid fa-hashtag" style="margin-right: 5px; color: var(--text-muted);"></i> Kapasitas</label>
                            <input type="number" name="kapasitas" placeholder="Kapasitas" required>
                            <button type="submit" name="tambah" class="btn-custom"><i class="fa-solid fa-plus"></i> Simpan Area</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="table-container" style="flex: 2;">
                    <table>
                        <thead><tr><th>Nama Area</th><th>Kapasitas Maks</th><th>Terisi</th><th>Status</th><th style="text-align: right;">Aksi</th></tr></thead>
                        <tbody>
                            <?php if(count($areas) == 0): ?>
                                <tr><td colspan="5" class="empty-state"><i class="fa-solid fa-map-location-dot"></i><p>Belum ada data area</p></td></tr>
                            <?php endif; ?>
                            <?php foreach($areas as $a): ?>
                            <tr class="<?= $a['status_aktif'] ? '' : 'row-nonaktif' ?>">
                                <td style="font-weight: 600;"><?= htmlspecialchars($a['nama_area']) ?></td>
                                <td><?= $a['kapasitas'] ?> Kendaraan</td>
                                <td><span class="badge badge-pink"><?= $a['terisi'] ?> Unit</span></td>
                                <td>
                                    <?php if($a['status_aktif']): ?>
                                        <span class="badge badge-green"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-muted"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-buttons">
                                        <a href="?edit_id=<?= $a['id_area'] ?>" class="btn-action btn-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="?toggle=<?= $a['id_area'] ?>" class="btn-action <?= $a['status_aktif'] ? 'btn-warn' : 'btn-activate' ?>" title="<?= $a['status_aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>"><i class="fa-solid fa-<?= $a['status_aktif'] ? 'ban' : 'check-circle' ?>"></i></a>
                                        <a href="?hapus=<?= $a['id_area'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Hapus area <?= htmlspecialchars($a['nama_area']) ?> permanen?')"><i class="fa-solid fa-trash"></i></a>
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
