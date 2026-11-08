-- Seeder Data KosManager

USE kostmanager;

-- 1. Tabel users (Admin Pengelola)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default login: admin / admin123
INSERT INTO users (id, username, password, nama_lengkap) VALUES
(1, 'admin', '$2y$12$x0GL76JkjCqxGsTxTnXhs.CpI8cH7m/Oxt35BX3XbS9Ddm3XO3hU6', 'Pengelola Kos Utama')
ON DUPLICATE KEY UPDATE
    password = VALUES(password),
    nama_lengkap = VALUES(nama_lengkap);

-- 2. Data Kos
INSERT INTO kos (id, nama_kos, alamat, lokasi, deskripsi, fasilitas, no_hp_pengelola, status_publikasi) VALUES
(1, 'Kos Putri Melati Asri', 'Jl. Bendungan Sigura-Gura Barat No. 18, Sumbersari, Lowokwaru', 'Dekat Kampus UB & ITN Malang', 'Kos putri bersih, nyaman, dan tenang. Sangat cocok bagi mahasiswi yang membutuhkan lingkungan belajar kondusif. Berada di lingkungan aman dengan akses mudah ke kuliner, laundry, dan minimarket 24 jam.', 'Wi-Fi 100 Mbps, Kamar Mandi Dalam, Kasur Springbed, Lemari Pakaian, Meja Belajar, Dapur Bersama, Kulkas, Parkir Motor Luas, CCTV 24 Jam', '081234567890', 'published'),
(2, 'Kos Putra Graha Soekarno Hatta', 'Jl. Soekarno Hatta Indah No. 42, Mojolangu, Lowokwaru', 'Dekat Polinema & UB Pintu Barat', 'Hunian eksklusif khusus putra dengan kamar luas ber-AC dan fasilitas lengkap. Dikelola profesional dengan sistem keamanan fingerprint dan penjaga kos siaga 24 jam.', 'AC, Kamar Mandi Dalam (Water Heater), Wi-Fi Fiber, Meja Kerja Ergonomis, Kasur King Size, Smart TV Ruang Bersama, Dapur & Dispenser, Parkir Mobil & Motor', '081298765432', 'published'),
(3, 'Kos Muslimah Griya Dinoyo', 'Jl. MT Haryono Gg. 9 No. 12, Dinoyo, Lowokwaru', 'Dekat UIN Maulana Malik Ibrahim & Unisma', 'Kos khusus mahasiswi muslimah dengan suasana asri dan kekeluargaan. Dekat gerbang belakang kampus, hemat ongkos transportasi harian.', 'Kamar Mandi Bersih, Kasur Busa Berkualitas, Lemari 2 Pintu, Wi-Fi Cepat, Tempat Cuci & Jemur Luas, Ruang Tamu Khusus Wanita, Garasi Terkunci', '085712345678', 'published'),
(4, 'Kos Eksklusif Paviliun Ijen', 'Jl. Retawu No. 5, Gading Kasri, Klojen', 'Dekat UM Kampus Induk & Alun-Alun Malang', 'Paviliun modern di kawasan prestisius Ijen Boulevard. Lingkungan rindang, sejuk, dekat berbagai cafe hits, perpustakaan kota, dan rumah sakit.', 'Fully Furnished, AC Daikin, Water Heater, High Speed Internet, Pembersihan Kamar Mingguan, Keamanan Security, Parkir Mobil Tersedia', '081399887766', 'published')
ON DUPLICATE KEY UPDATE 
    nama_kos = VALUES(nama_kos),
    alamat = VALUES(alamat),
    lokasi = VALUES(lokasi),
    deskripsi = VALUES(deskripsi),
    fasilitas = VALUES(fasilitas),
    no_hp_pengelola = VALUES(no_hp_pengelola),
    status_publikasi = VALUES(status_publikasi);

-- 3. Data Kamar
INSERT INTO kamar (id, kos_id, nomor_kamar, tipe, harga_bulanan, status) VALUES
(1, 1, 'M-01', 'Standar Single (KM Luar)', 750000.00, 'terisi'),
(2, 1, 'M-02', 'Standar Single (KM Luar)', 750000.00, 'kosong'),
(3, 1, 'M-03', 'Deluxe (KM Dalam)', 950000.00, 'terisi'),
(4, 1, 'M-04', 'VIP (KM Dalam + Balkon)', 1150000.00, 'kosong'),
(5, 2, 'SH-101', 'Deluxe AC (Lantai 1)', 1500000.00, 'terisi'),
(6, 2, 'SH-102', 'Deluxe AC (Lantai 1)', 1500000.00, 'kosong'),
(7, 2, 'SH-201', 'Executive King (Lantai 2)', 1800000.00, 'terisi'),
(8, 2, 'SH-202', 'Executive King (Lantai 2)', 1800000.00, 'kosong'),
(9, 3, 'D-01', 'Reguler Muslimah', 650000.00, 'terisi'),
(10, 3, 'D-02', 'Reguler Muslimah', 650000.00, 'kosong'),
(11, 3, 'D-03', 'Reguler Plus Jendela', 700000.00, 'kosong'),
(12, 4, 'IJN-A1', 'Studio AC + Water Heater', 2200000.00, 'kosong'),
(13, 4, 'IJN-A2', 'Studio AC + Water Heater', 2200000.00, 'terisi')
ON DUPLICATE KEY UPDATE 
    kos_id = VALUES(kos_id),
    nomor_kamar = VALUES(nomor_kamar),
    tipe = VALUES(tipe),
    harga_bulanan = VALUES(harga_bulanan),
    status = VALUES(status);

-- 4. Data Penghuni
INSERT INTO penghuni (id, nama, no_hp, nik, kamar_id) VALUES
(1, 'Aisyah Putri Rahmadani', '081211223344', '3573015504020001', 1),
(2, 'Nabila Zahra Syahrini', '081322334455', '3573024809030002', 3),
(3, 'Dimas Arya Pratama', '082144556677', '3573031201010003', 5),
(4, 'Fajar Kurniawan Santoso', '085266778899', '3573042508020004', 7),
(5, 'Siti Nur Azizah', '087812903456', '3573056011030005', 9),
(6, 'Rian Satria Wibawa', '089612349876', '3573061406000006', 13)
ON DUPLICATE KEY UPDATE
    nama = VALUES(nama),
    no_hp = VALUES(no_hp),
    nik = VALUES(nik),
    kamar_id = VALUES(kamar_id);

