# LAMPIRAN KODE SUMBER APLIKASI PARKIR

Dokumen ini berisi kode inti (core logic) sistem untuk dilampirkan ke dalam laporan.

### Koneksi Database & Fungsi Inti (config/database.php)
```php
<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "db_parkir";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

function query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function exec_query($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}
?>

```

### Sistem Autentikasi / Login (index.php)
```php
<?php
session_start();
require 'config/database.php';

if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    if ($role == 'admin') header("Location: admin/dashboard.php");
    elseif ($role == 'petugas') header("Location: petugas/transaksi.php");
    else header("Location: owner/laporan.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $user = query("SELECT * FROM tb_user WHERE username = '$username' AND password = '$password' AND status_aktif = 1");

    if (count($user) > 0) {
        $_SESSION['user'] = $user[0];
        $role = $user[0]['role'];

        if ($role == 'admin') header("Location: admin/dashboard.php");
        elseif ($role == 'petugas') header("Location: petugas/transaksi.php");
        else header("Location: owner/laporan.php");
    } else {
        $error = "Akun tidak ditemukan atau tidak aktif!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Parkir UKK</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>PARKIR-PRO</h2>
        <p>Silahkan masuk ke sistem</p>
        <?php if(isset($error)) echo "<p style='color:yellow;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn-brown">MASUK</button>
        </form>
    </div>
</body>
</html>
```

### Logika Check-In Kendaraan (petugas/masuk.php)
```php
<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'petugas') header("Location: ../index.php");

if (isset($_POST['simpan'])) {
    $plat = $_POST['plat_nomor'];
    $area = $_POST['id_area'];
    $tarif = $_POST['id_tarif'];
    $user_id = $_SESSION['user']['id_user'];

    mysqli_query($conn, "INSERT INTO tb_kendaraan (plat_nomor, id_user) VALUES ('$plat', '$user_id')");
    $id_knd = mysqli_insert_id($conn);

    mysqli_query($conn, "INSERT INTO tb_transaksi (id_kendaraan, id_tarif, id_area, id_user, status) 
                         VALUES ('$id_knd', '$tarif', '$area', '$user_id', 'masuk')");
    
    mysqli_query($conn, "UPDATE tb_area_parkir SET terisi = terisi + 1 WHERE id_area = $area");

    header("Location: transaksi.php");
    exit();
}

$areas = query("SELECT * FROM tb_area_parkir WHERE kapasitas > terisi");
$tarifs = query("SELECT * FROM tb_tarif");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check-In Parkir</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fa-solid fa-square-parking"></i> PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Petugas Loket</span>
        </div>
        <div class="nav-links">
            <a href="transaksi.php">Parkir Aktif</a>
            <a href="masuk.php" class="active">Check-In Kendaraan</a>
            <a href="keluar.php">Check-Out Kendaraan</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><i class="fa-regular fa-circle-user" style="margin-right: 6px;"></i><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: center;">
            <div class="form-card" style="width: 100%; max-width: 500px;">
                <h2 style="margin-top: 0; margin-bottom: 24px; text-align: center;"><i class="fa-solid fa-id-card-clip" style="color: var(--primary); margin-right: 8px;"></i> Entry Parkir Kendaraan</h2>
                <form method="POST">
                    <label><i class="fa-solid fa-car-side" style="margin-right: 5px; color: var(--text-muted);"></i> Plat Nomor</label>
                    <input type="text" name="plat_nomor" placeholder="Contoh: B 1234 ABC" required style="font-family: monospace; font-size: 16px; font-weight: bold; text-transform: uppercase;">
                    
                    <label><i class="fa-solid fa-tag" style="margin-right: 5px; color: var(--text-muted);"></i> Jenis Kendaraan</label>
                    <select name="id_tarif" required>
                        <?php foreach($tarifs as $t): ?>
                            <option value="<?= $t['id_tarif'] ?>"><?= $t['jenis_kendaraan'] ?> - Rp <?= number_format($t['tarif_per_jam']) ?>/jam</option>
                        <?php endforeach; ?>
                    </select>

                    <label><i class="fa-solid fa-layer-group" style="margin-right: 5px; color: var(--text-muted);"></i> Area Parkir</label>
                    <select name="id_area" required>
                        <?php foreach($areas as $a): ?>
                            <option value="<?= $a['id_area'] ?>"><?= $a['nama_area'] ?> (Tersedia: <?= $a['kapasitas'] - $a['terisi'] ?>)</option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" name="simpan" class="btn-custom" style="margin-top: 24px; padding: 14px; font-size: 16px; border-radius: 12px;"><i class="fa-solid fa-check-circle" style="margin-right: 5px;"></i> Konfirmasi & Simpan Tiket</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
```

### Logika Kalkulasi Tarif & Check-Out (petugas/keluar.php)
```php
<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'petugas') header("Location: ../index.php");
include '../config/helper.php';

if (isset($_POST['proses_keluar'])) {
    $id = $_POST['id_parkir'];
    $now = date('Y-m-d H:i:s');

    $tr = query("SELECT t.*, r.tarif_per_jam FROM tb_transaksi t 
                 JOIN tb_tarif r ON t.id_tarif = r.id_tarif WHERE id_parkir = $id")[0];

    $kalkulasi = hitungBiaya($tr['waktu_masuk'], $now, $tr['tarif_per_jam']);

    mysqli_query($conn, "UPDATE tb_transaksi SET 
        waktu_keluar = '$now', 
        durasi_jam = '{$kalkulasi['durasi']}', 
        biaya_total = '{$kalkulasi['total']}', 
        status = 'keluar' 
        WHERE id_parkir = $id");

    mysqli_query($conn, "UPDATE tb_area_parkir SET terisi = terisi - 1 WHERE id_area = {$tr['id_area']}");

    header("Location: cetak_struk.php?id=$id");
    exit();
}

$aktif = query("SELECT t.id_parkir, k.plat_nomor, t.waktu_masuk, a.nama_area FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                JOIN tb_area_parkir a ON t.id_area = a.id_area 
                WHERE t.status = 'masuk' ORDER BY t.waktu_masuk ASC");

$selected_id = isset($_GET['id']) ? $_GET['id'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check-Out Parkir</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fa-solid fa-square-parking"></i> PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Petugas Loket</span>
        </div>
        <div class="nav-links">
            <a href="transaksi.php">Parkir Aktif</a>
            <a href="masuk.php">Check-In Kendaraan</a>
            <a href="keluar.php" class="active">Check-Out Kendaraan</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><i class="fa-regular fa-circle-user" style="margin-right: 6px;"></i><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: center;">
            <div class="form-card" style="width: 100%; max-width: 500px;">
                <h2 style="margin-top: 0; margin-bottom: 24px; text-align: center;"><i class="fa-solid fa-money-bill-wave" style="color: #10b981; margin-right: 8px;"></i> Check-Out & Pembayaran</h2>
                
                <?php if(empty($aktif)): ?>
                    <div style="text-align: center; color: var(--text-muted); padding: 20px;">
                        <i class="fa-solid fa-car-side fa-2x" style="margin-bottom: 10px; opacity: 0.5;"></i><br>
                        Tidak ada kendaraan parkir yang bisa di-check-out saat ini.
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <label><i class="fa-solid fa-magnifying-glass" style="margin-right: 5px; color: var(--text-muted);"></i> Pilih Kendaraan (Plat Nomor)</label>
                        <select name="id_parkir" required style="font-family: monospace; font-size: 15px;">
                            <option value="">-- Pilih Kendaraan --</option>
                            <?php foreach($aktif as $a): ?>
                                <?php $sel = ($a['id_parkir'] == $selected_id) ? 'selected' : ''; ?>
                                <option value="<?= $a['id_parkir'] ?>" <?= $sel ?>>
                                    <?= $a['plat_nomor'] ?> - <?= $a['nama_area'] ?> (Masuk: <?= date('d/m H:i', strtotime($a['waktu_masuk'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <br><br>
                        <div style="background: #eff6ff; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-bottom: 20px;">
                            <p style="margin: 0; font-size: 13.5px; color: #1e3a8a;"><i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i> Klik tombol di bawah untuk mengkalkulasi durasi dan total biaya parkir. Struk otomatis akan dicetak setelahnya.</p>
                        </div>
                        
                        <button type="submit" name="proses_keluar" class="btn-custom" style="padding: 14px; font-size: 16px; border-radius: 12px; background: #10b981;"><i class="fa-solid fa-check-double" style="margin-right: 5px;"></i> Hitung & Selesaikan Pembayaran</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
```

### Pembuatan Laporan Keuangan Owner (owner/laporan.php)
```php
<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'owner') header("Location: ../index.php");

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-6 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$query_str = "SELECT t.*, k.plat_nomor, a.nama_area FROM tb_transaksi t
              JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
              JOIN tb_area_parkir a ON t.id_area = a.id_area
              WHERE t.status = 'keluar' AND DATE(t.waktu_keluar) BETWEEN '$start_date' AND '$end_date'
              ORDER BY t.waktu_keluar DESC";
$laporan = query($query_str);

$total_pendapatan = 0;
foreach($laporan as $row) {
    $total_pendapatan += $row['biaya_total'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Owner - Laporan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fa-solid fa-square-parking"></i> PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Owner Panel</span>
        </div>
        <div class="nav-links">
            <a href="laporan.php" class="active">Laporan Pendapatan</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><i class="fa-regular fa-circle-user" style="margin-right: 6px;"></i><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Logout</a>
        </div>
    </nav>
    <div class="container">
        
        <!-- Welcome Banner for Owner -->
        <div class="welcome-banner" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="welcome-text">
                <h2 style="margin-bottom: 0;">Selamat Datang, <?= $_SESSION['user']['nama_lengkap'] ?>!</h2>
                <p style="margin-top: 10px;">Pantau terus laporan pendapatan parkir dari tanggal <strong><?= date('d M Y', strtotime($start_date)) ?></strong> hingga <strong><?= date('d M Y', strtotime($end_date)) ?></strong>.</p>
            </div>
            <div class="welcome-icon">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>

        <div style="display: flex; gap: 24px; margin-bottom: 30px; flex-wrap: wrap;">
            <!-- Filter box -->
            <div style="background: white; padding: 20px 24px; border-radius: 16px; border: 1px solid var(--border); flex: 2; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; margin: 0;">
                    <div style="flex: 1;">
                        <label style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;"><i class="fa-regular fa-calendar" style="margin-right: 5px;"></i> Mulai Tanggal</label>
                        <input type="date" name="start_date" value="<?= $start_date ?>" style="margin-bottom: 0;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;"><i class="fa-regular fa-calendar-check" style="margin-right: 5px;"></i> Sampai Tanggal</label>
                        <input type="date" name="end_date" value="<?= $end_date ?>" style="margin-bottom: 0;">
                    </div>
                    <div>
                        <button type="submit" style="padding: 12px 24px; background: var(--primary); margin-bottom: 0; border-radius: 8px; font-size: 14px;"><i class="fa-solid fa-filter" style="margin-right: 8px;"></i> Terapkan Filter</button>
                    </div>
                </form>
            </div>

            <!-- Summary Card -->
            <div class="stat-card" style="flex: 1; margin: 0; background: #f0fdf4; border: 1px solid #bbf7d0;">
                <div class="stat-icon" style="color: #047857; background: #d1fae5;">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3 style="color: #047857;">Total Pendapatan (Periode Ini)</h3>
                    <p style="color: #065f46; font-size: 26px;">Rp <?= number_format($total_pendapatan) ?></p>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 20px;"><i class="fa-solid fa-list-check" style="margin-right: 8px; color: var(--text-muted);"></i> Rincian Transaksi</h2>
            <button class="btn-custom" style="width: auto; padding: 10px 20px; background: #1f2937; border-radius: 8px; font-size: 14px;" onclick="window.print()"><i class="fa-solid fa-print" style="margin-right: 8px;"></i> Cetak Laporan</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Plat Nomor</th>
                        <th>Area</th>
                        <th>Waktu Keluar</th>
                        <th style="text-align: right;">Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($laporan)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-folder-open fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i><br>
                            Belum ada transaksi parkir keluar pada periode ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no=1; foreach($laporan as $row) : ?>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 500;"><?= $no++; ?></td>
                            <td><span style="font-weight: 600; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #e5e7eb; font-family: monospace; font-size: 14px;"><i class="fa-solid fa-car-rear" style="margin-right: 8px; color: var(--text-muted);"></i><?= $row['plat_nomor']; ?></span></td>
                            <td><?= $row['nama_area']; ?></td>
                            <td style="color: var(--text-dark); font-size: 14px;"><?= date('d M Y, H:i', strtotime($row['waktu_keluar'])); ?></td>
                            <td style="font-weight: 600; color: #10b981; text-align: right; font-size: 15px;">Rp <?= number_format($row['biaya_total']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <style>
        @media print {
            .top-nav, button, form { display: none !important; }
            body { background: white; }
            .container { max-width: 100%; margin: 0; padding: 0; }
            .stat-card, .table-container, .welcome-banner { box-shadow: none !important; border: 1px solid #ccc !important; }
        }
    </style>
</body>
</html>
```

