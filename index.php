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