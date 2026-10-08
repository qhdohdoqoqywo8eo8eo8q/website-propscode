CREATE DATABASE IF NOT EXISTS absensi_propscode CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE absensi_propscode;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    role ENUM('admin', 'pegawai') NOT NULL DEFAULT 'pegawai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME DEFAULT NULL,
    jam_pulang TIME DEFAULT NULL,
    lokasi_masuk VARCHAR(255) DEFAULT NULL,
    latitude_masuk DECIMAL(10,7) DEFAULT NULL,
    longitude_masuk DECIMAL(10,7) DEFAULT NULL,
    foto_masuk TEXT DEFAULT NULL,
    lokasi_pulang VARCHAR(255) DEFAULT NULL,
    latitude_pulang DECIMAL(10,7) DEFAULT NULL,
    longitude_pulang DECIMAL(10,7) DEFAULT NULL,
    foto_pulang TEXT DEFAULT NULL,
    status ENUM('Hadir', 'Izin', 'Sakit', 'Alpha') DEFAULT 'Hadir',
    verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, password, fullname, email, phone, role) VALUES
('admin', '$2y$10$WYHB8vAgcS59NtvOveTpUen71TEDniA/WzQ3LbQmhs4TLOYy9Makq', 'Administrator', 'admin@propscode.com', '081234567890', 'admin'),
('pegawai1', '$2y$10$1c5q9jWXZvPifk.69bi/Y.OZcYaGg5GxUY82/tuiOIGLN8.wAYmYK', 'Budi Santoso', 'budi@propscode.com', '081234567891', 'pegawai');
