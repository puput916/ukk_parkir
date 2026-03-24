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
    <title>Flam Parking - Login</title>
    <link rel="stylesheet" href="assets/css/style.css?v=6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-left">
            <img src="assets/gambar_login.png" alt="Flamingo Mascot">
        </div>
        <div class="login-right">
            <div class="login-form-box">
                <div class="login-logo">
                    <img src="assets/logo_web.png" alt="Flam Parking">
                </div>
                <h2>Masuk ke Sistem</h2>
                <p>Silahkan login untuk melanjutkan</p>

                <?php if(isset($error)): ?>
                    <div class="error-msg"><i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                    <button type="submit" name="login" class="btn-login"><i class="fa-solid fa-right-to-bracket" style="margin-right: 8px;"></i> MASUK</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>