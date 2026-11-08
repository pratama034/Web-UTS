-- ============================================================
-- File: database/seeder.sql
-- KosManager - Seeder Dummy Data
-- ============================================================

USE kostmanager;

-- 1. Insert Data Akun Admin (Pengelola Kos)
-- Login default: username: admin | password: admin123
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO users (id, username, password, nama_lengkap) VALUES
(1, 'admin', '$2y$12$x0GL76JkjCqxGsTxTnXhs.CpI8cH7m/Oxt35BX3XbS9Ddm3XO3hU6', 'Pengelola Kos Malang')
ON DUPLICATE KEY UPDATE
    nama_lengkap = VALUES(nama_lengkap);

-- 2. Insert Data Kos (2 data kos dengan status 'published')
INSERT INTO kos (id, nama_kos, alamat, lokasi, deskripsi, fasilitas, no_hp_pengelola, status_publikasi) VALUES
(1, 'Kos Putri Melati Asri', 'Jl. Bendungan Sigura-gura Barat No. 15, Sumbersari, Lowokwaru', 'Dekat Kampus UB & ITN Malang', 'Kos putri yang nyaman, aman, dan tenang. Sangat strategis dekat minimarket, laundry, dan pusat kuliner mahasiswa.', 'Wi-Fi Cepat, Kamar Mandi Dalam, Kasur Springbed, Meja Belajar, Lemari Pakaian, Dapur Bersama, Parkir Motor Aman', '081234567890', 'published'),
(2, 'Kos Putra Graha Soekarno Hatta', 'Jl. Soekarno Hatta Indah No. 28, Mojolangu, Lowokwaru', 'Dekat Kampus Polinema & UB', 'Kos putra eksklusif dengan fasilitas lengkap, akses 24 jam, lingkungan sejuk, dan keamanan CCTV.', 'Wi-Fi Fiber, AC, Kamar Mandi Dalam, Kasur King Size, Water Heater, Dapur Bersama, Parkir Mobil & Motor', '081298765432', 'published')
ON DUPLICATE KEY UPDATE
    nama_kos = VALUES(nama_kos),
    alamat = VALUES(alamat),
    lokasi = VALUES(lokasi),
    deskripsi = VALUES(deskripsi),
    fasilitas = VALUES(fasilitas),
    no_hp_pengelola = VALUES(no_hp_pengelola),
    status_publikasi = VALUES(status_publikasi);

-- 2. Insert Data Kamar (5 data kamar dengan status 'kosong' berelasi ke kos_id)
INSERT INTO kamar (id, kos_id, nomor_kamar, tipe, harga_bulanan, status) VALUES
(1, 1, 'M-01', 'Standar Single', 750000.00, 'kosong'),
(2, 1, 'M-02', 'Standar Single', 750000.00, 'kosong'),
(3, 1, 'M-03', 'Deluxe (KM Dalam)', 950000.00, 'kosong'),
(4, 2, 'SH-101', 'Deluxe AC', 1500000.00, 'kosong'),
(5, 2, 'SH-102', 'Executive AC', 1750000.00, 'kosong')
ON DUPLICATE KEY UPDATE
    kos_id = VALUES(kos_id),
    nomor_kamar = VALUES(nomor_kamar),
    tipe = VALUES(tipe),
    harga_bulanan = VALUES(harga_bulanan),
    status = VALUES(status);

