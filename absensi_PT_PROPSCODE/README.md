PT PROPSCODE Absensi
====================

Proyek ini adalah aplikasi absensi berbasis PHP/MySQL untuk PT PROPSCODE Studio Teknologi.
Fitur utama meliputi:
	- Login untuk admin dan pegawai
	- Dashboard admin untuk manajemen pegawai, admin, absensi, dan laporan
	- Modul pegawai untuk absen masuk/pulang, profil, dan riwayat
	- Integrasi webcam dan GPS untuk validasi bukti absen
	- Laporan harian, bulanan, dan tahunan

Struktur folder:
	- `auth/` : autentikasi dan proteksi sesi
	- `config/` : konfigurasi database dan helper umum
	- `admin/` : panel admin dan manajemen data
	- `pegawai/` : halaman pegawai dan proses absensi
	- `assets/` : CSS, JavaScript, dan aset gambar
	- `database/` : file SQL untuk skema dan sample data

Cara cepat menjalankan:
1. Buat database MySQL baru, lalu impor `database/absensi_propscode.sql`
2. Sesuaikan `config/koneksi.php` jika diperlukan (host, user, password, nama database)
3. Jalankan XAMPP dan arahkan browser ke `http://localhost/absensi_PT_PROPSCODE`
4. Akses `auth/login.php` untuk login sebagai admin atau pegawai

Catatan:
	- `assets/css/bootstrap.min.css` hanya placeholder; Bootstrap sebenarnya dimuat dari CDN.
	- Gambar `assets/img/bg-login.jpg`, `assets/img/logo.png`, dan `assets/img/user.png` adalah placeholder kecil.
