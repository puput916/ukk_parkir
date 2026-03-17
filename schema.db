CREATE DATABASE IF NOT EXISTS db_parkir;
USE db_parkir;

CREATE TABLE tb_user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(150) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'petugas', 'owner') NOT NULL,
    status_aktif TINYINT(1) DEFAULT 1
);

CREATE TABLE tb_area_parkir (
    id_area INT AUTO_INCREMENT PRIMARY KEY,
    nama_area VARCHAR(100) NOT NULL,
    kapasitas INT NOT NULL DEFAULT 0,
    terisi INT NOT NULL DEFAULT 0
);

CREATE TABLE tb_tarif (
    id_tarif INT AUTO_INCREMENT PRIMARY KEY,
    jenis_kendaraan VARCHAR(50) NOT NULL,
    tarif_per_jam INT NOT NULL
);

CREATE TABLE tb_kendaraan (
    id_kendaraan INT AUTO_INCREMENT PRIMARY KEY,
    plat_nomor VARCHAR(20) NOT NULL,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES tb_user(id_user)
);

CREATE TABLE tb_transaksi (
    id_parkir INT AUTO_INCREMENT PRIMARY KEY,
    id_kendaraan INT NOT NULL,
    id_tarif INT NOT NULL,
    id_area INT NOT NULL,
    id_user INT NOT NULL,
    waktu_masuk DATETIME DEFAULT CURRENT_TIMESTAMP,
    waktu_keluar DATETIME NULL,
    durasi_jam INT NULL,
    biaya_total INT NULL,
    status ENUM('masuk', 'keluar') DEFAULT 'masuk',
    FOREIGN KEY (id_kendaraan) REFERENCES tb_kendaraan(id_kendaraan),
    FOREIGN KEY (id_tarif) REFERENCES tb_tarif(id_tarif),
    FOREIGN KEY (id_area) REFERENCES tb_area_parkir(id_area),
    FOREIGN KEY (id_user) REFERENCES tb_user(id_user)
);

-- Insert Dummy Data for initial testing
INSERT INTO tb_user (nama_lengkap, username, password, role) VALUES 
('Administrator', 'admin', 'admin123', 'admin'),
('Petugas Jaga', 'petugas', 'petugas123', 'petugas'),
('Bapak Owner', 'owner', 'owner123', 'owner');

INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi) VALUES 
('Lantai 1 - Motor', 100, 0),
('Lantai 1 - Mobil', 50, 0),
('Lantai 2 - VIP', 20, 0);

INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES 
('Motor', 2000),
('Mobil', 5000),
('Truk/Bus', 10000);
