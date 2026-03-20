# 🅿️ PARKIR-PRO (Aplikasi Pengelolaan Parkir Berbasis Web)
Aplikasi berbasis web untuk mengelola dan memonitor aktivitas parkir kendaraan. Dibuat menggunakan PHP Native dan MySQL/MariaDB.

## 🚀 Panduan Menjalankan Project di Laptop Lain/Baru

Berikut adalah langkah-langkah untuk meng-\*install\* dan menjalankan project ini di komputer atau server lain:

### 1. Persiapan Awal (Prerequisites)
Pastikan laptop/pc tersebut sudah terinstal web server lokal (seperti **XAMPP**, **MAMP**, atau **Laragon**) yang mencakup **PHP** dan **MySQL/MariaDB**.

### 2. Mengambil Kode Sumber
* Lakukan Clone Repository ini menggunakan Git:
  ```bash
  git clone https://github.com/puput916/ukk_parkir.git
  ```
* Atau *Download ZIP* repository ini dan ekstrak foldernya.

### 3. Konfigurasi Database (Penting!)
Project ini membutuhkan database agar bisa berjalan.
1. Buka aplikasi XAMPP/MAMP dan pastikan modul **Apache** dan **MySQL** telah di-*Start*.
2. Buka browser dan akses phpMyAdmin di `http://localhost/phpmyadmin`
3. Buat database baru dengan nama persis: **`db_parkir`**
4. Pilih database `db_parkir`, lalu klik menu **Import** atau **SQL**.
5. Buka file `schema.db` yang ada di dalam folder project ini, *copy* seluruh isinya, lalu *paste* dan eksekusi (Go) di phpMyAdmin untuk otomatis membuat semua tabel beserta data *dummy*-nya.

> **Catatan**: Jika settingan default MySQL di komputer barumu menggunakan password untuk akun `root`, jangan lupa sesuaikan pengaturan koneksi di dalam file `config/database.php` (baris `$pass = "";`).

### 4. Menjalankan Aplikasi
Kamu punya dua opsi untuk menjalankan aplikasi:

**Opsi A: Menggunakan XAMPP/htdocs (Cara Biasa)**
Pindahkan seluruh folder `ukk_parkir` ke dalam folder `C:/xampp/htdocs/` (Windows) atau `/Applications/XAMPP/xamppfiles/htdocs/` (Mac). Akses melalui browser:
`http://localhost/ukk_parkir`

**Opsi B: Menggunakan PHP Built-in Server (Cara Cepat Terminal)**
Buka terminal/CMD, arahkan (*cd*) ke dalam folder project `ukk_parkir`, lalu jalankan:
```bash
php -S localhost:8000
```
Buka browser dan akses: `http://localhost:8000`

### 5. Akun Login Bawaan
Tiga tingkatan peran (*role*) sudah tersedia di dalam database untuk langsung kamu pakai login saat ujicoba/presentasi:

* **Admin** 
  * Username: `admin`
  * Password: `admin123`
* **Petugas Loket**
  * Username: `petugas`
  * Password: `petugas123`
* **Owner / Pemilik**
  * Username: `owner`
  * Password: `owner123`

---
*Dikembangkan untuk keperluan Ujian Kompetensi Keahlian (UKK).*