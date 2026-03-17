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
