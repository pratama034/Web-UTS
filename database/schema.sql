CREATE DATABASE IF NOT EXISTS kostmanager
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE kostmanager;

CREATE TABLE IF NOT EXISTS kos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kos VARCHAR(150) NOT NULL,
    alamat TEXT NOT NULL,
    lokasi VARCHAR(150) NOT NULL,
    deskripsi TEXT NULL,
    fasilitas TEXT NULL,
    no_hp_pengelola VARCHAR(20) NULL,
    status_publikasi ENUM('draft', 'published') NOT NULL DEFAULT 'draft'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kamar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kos_id INT NOT NULL,
    nomor_kamar VARCHAR(50) NOT NULL,
    tipe VARCHAR(100) NOT NULL,
    harga_bulanan DECIMAL(12, 2) NOT NULL,
    status ENUM('kosong', 'terisi') NOT NULL DEFAULT 'kosong',
    CONSTRAINT fk_kamar_kos
        FOREIGN KEY (kos_id)
        REFERENCES kos(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT unique_nomor_kamar
        UNIQUE (kos_id, nomor_kamar),
    CONSTRAINT chk_kamar_harga_positif
        CHECK (harga_bulanan > 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS penghuni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    nik CHAR(16) NOT NULL,
    kamar_id INT NULL,
    CONSTRAINT unique_penghuni_nik
        UNIQUE (nik),
    CONSTRAINT unique_penghuni_kamar
        UNIQUE (kamar_id),
    CONSTRAINT fk_penghuni_kamar
        FOREIGN KEY (kamar_id)
        REFERENCES kamar(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
