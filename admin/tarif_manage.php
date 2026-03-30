<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

$msg = '';

// TAMBAH
if(isset($_POST['tambah'])){
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $tarif = (int)$_POST['tarif_per_jam'];
    exec_query("INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES ('$jenis', '$tarif')");
    $msg = 'Tarif berhasil ditambahkan!';
}

// EDIT
if(isset($_POST['edit'])){
    $id = (int)$_POST['id_tarif'];
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $tarif = (int)$_POST['tarif_per_jam'];
    exec_query("UPDATE tb_tarif SET jenis_kendaraan='$jenis', tarif_per_jam='$tarif' WHERE id_tarif=$id");
    $msg = 'Tarif berhasil diperbarui!';
}

// HAPUS
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    exec_query("DELETE FROM tb_tarif WHERE id_tarif = $id");
    header("Location: tarif_manage.php?msg=deleted");
    exit;
}

// TOGGLE STATUS
if(isset($_GET['toggle'])){
    $id = (int)$_GET['toggle'];
    $current = query("SELECT status_aktif FROM tb_tarif WHERE id_tarif=$id");
    if(count($current) > 0){
        $new = $current[0]['status_aktif'] ? 0 : 1;
        exec_query("UPDATE tb_tarif SET status_aktif=$new WHERE id_tarif=$id");
    }
    header("Location: tarif_manage.php");
    exit;
}

if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') $msg = 'Tarif berhasil dihapus!';

$tarifs = query("SELECT * FROM tb_tarif ORDER BY id_tarif DESC");

// Ambil data edit jika ada
$edit_data = null;
if(isset($_GET['edit_id'])){
    $eid = (int)$_GET['edit_id'];
    $ed = query("SELECT * FROM tb_tarif WHERE id_tarif=$eid");
    if(count($ed) > 0) $edit_data = $ed[0];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Tarif</title>
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
                <a href="tarif_manage.php" class="active"><span class="icon-box"><i class="fa-solid fa-tags"></i></span> <span>Kelola Tarif</span></a>
                <a href="area_manage.php"><span class="icon-box"><i class="fa-solid fa-map-location-dot"></i></span> <span>Kelola Area</span></a>
                <a href="log_aktivitas.php"><span class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></span> <span>Log Aktivitas</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1><i class="fa-solid fa-tags"></i>Daftar Tarif Kendaraan</h1></div>

            <?php if($msg): ?>
                <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?= $msg ?></div>
            <?php endif; ?>

            <div style="display: flex; gap: 28px; align-items: flex-start;">
                <div class="form-card" style="flex: 1; max-width: 380px;">
                    <?php if($edit_data): ?>
                        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 700;"><i class="fa-solid fa-pen-to-square" style="margin-right: 8px; color: var(--pink-400);"></i>Edit Tarif</h3>
                        <form method="POST">
                            <input type="hidden" name="id_tarif" value="<?= $edit_data['id_tarif'] ?>">
                            <label><i class="fa-solid fa-car" style="margin-right: 5px; color: var(--text-muted);"></i> Jenis Kendaraan</label>
                            <input type="text" name="jenis_kendaraan" value="<?= htmlspecialchars($edit_data['jenis_kendaraan']) ?>" required>
                            <label><i class="fa-solid fa-money-bill" style="margin-right: 5px; color: var(--text-muted);"></i> Tarif Per Jam (Rp)</label>
                            <input type="number" name="tarif_per_jam" value="<?= $edit_data['tarif_per_jam'] ?>" required>
                            <button type="submit" name="edit" class="btn-custom"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                            <a href="tarif_manage.php" class="btn-custom btn-dark" style="text-align:center; margin-top:8px;"><i class="fa-solid fa-xmark"></i> Batal</a>
                        </form>
                    <?php else: ?>
                        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 700;"><i class="fa-solid fa-plus-circle" style="margin-right: 8px; color: var(--pink-400);"></i>Tambah Tarif Baru</h3>
                        <form method="POST">
                            <label><i class="fa-solid fa-car" style="margin-right: 5px; color: var(--text-muted);"></i> Jenis Kendaraan</label>
                            <input type="text" name="jenis_kendaraan" placeholder="Jenis Kendaraan" required>
                            <label><i class="fa-solid fa-money-bill" style="margin-right: 5px; color: var(--text-muted);"></i> Tarif Per Jam (Rp)</label>
                            <input type="number" name="tarif_per_jam" placeholder="Tarif Per Jam" required>
                            <button type="submit" name="tambah" class="btn-custom"><i class="fa-solid fa-plus"></i> Simpan Tarif</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="table-container" style="flex: 2;">
                    <table>
                        <thead><tr><th>ID</th><th>Kategori Kendaraan</th><th>Harga Per Jam</th><th>Status</th><th style="text-align: right;">Aksi</th></tr></thead>
                        <tbody>
                            <?php if(count($tarifs) == 0): ?>
                                <tr><td colspan="5" class="empty-state"><i class="fa-solid fa-tags"></i><p>Belum ada data tarif</p></td></tr>
                            <?php endif; ?>
                            <?php foreach($tarifs as $t): ?>
                            <tr class="<?= $t['status_aktif'] ? '' : 'row-nonaktif' ?>">
                                <td style="color: var(--text-muted); font-family: monospace;">#<?= $t['id_tarif'] ?></td>
                                <td style="font-weight: 600;"><?= htmlspecialchars($t['jenis_kendaraan']) ?></td>
                                <td><span class="badge badge-green" style="font-size: 13px;">Rp <?= number_format($t['tarif_per_jam']) ?></span></td>
                                <td>
                                    <?php if($t['status_aktif']): ?>
                                        <span class="badge badge-green"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-muted"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-buttons">
                                        <a href="?edit_id=<?= $t['id_tarif'] ?>" class="btn-action btn-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="?toggle=<?= $t['id_tarif'] ?>" class="btn-action <?= $t['status_aktif'] ? 'btn-warn' : 'btn-activate' ?>" title="<?= $t['status_aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>"><i class="fa-solid fa-<?= $t['status_aktif'] ? 'ban' : 'check-circle' ?>"></i></a>
                                        <a href="?hapus=<?= $t['id_tarif'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin hapus tarif <?= htmlspecialchars($t['jenis_kendaraan']) ?>?')"><i class="fa-solid fa-trash"></i></a>
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
