-- EcoBank / BankSampah Digital - skema MySQL 8 (setara dengan migrasi Laravel)
CREATE DATABASE IF NOT EXISTS ecobank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecobank;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','nasabah','petugas') NOT NULL DEFAULT 'nasabah',
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE TABLE nasabah (
  id_nasabah BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_user BIGINT UNSIGNED NOT NULL UNIQUE,
  alamat VARCHAR(255) NOT NULL,
  nomor_telepon VARCHAR(20) NOT NULL,
  saldo DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE petugas (
  id_petugas BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_user BIGINT UNSIGNED NOT NULL UNIQUE,
  nomor_telepon VARCHAR(20) NOT NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE jenis_sampah (
  id_jenis BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_jenis VARCHAR(60) NOT NULL UNIQUE,
  harga_per_kg DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE TABLE setoran (
  id_setoran BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_nasabah BIGINT UNSIGNED NOT NULL,
  id_jenis BIGINT UNSIGNED NOT NULL,
  berat DECIMAL(8,2) NOT NULL,
  harga_per_kg DECIMAL(10,2) NOT NULL,
  total_nilai DECIMAL(12,2) NOT NULL,
  tanggal_setoran DATE NOT NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (id_nasabah) REFERENCES nasabah(id_nasabah),
  FOREIGN KEY (id_jenis) REFERENCES jenis_sampah(id_jenis)
);
CREATE TABLE penjemputan (
  id_penjemputan BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_nasabah BIGINT UNSIGNED NOT NULL,
  id_petugas BIGINT UNSIGNED NULL,
  alamat_penjemputan VARCHAR(255) NOT NULL,
  tanggal DATE NOT NULL,
  catatan VARCHAR(255) NULL,
  status ENUM('diajukan','ditugaskan','dijemput','selesai') NOT NULL DEFAULT 'diajukan',
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (id_nasabah) REFERENCES nasabah(id_nasabah),
  FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas) ON DELETE SET NULL
);

CREATE TABLE penarikan (
  id_penarikan BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_nasabah BIGINT UNSIGNED NOT NULL,
  jumlah DECIMAL(12,2) NOT NULL,
  tanggal_penarikan DATE NOT NULL,
  catatan VARCHAR(255) NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (id_nasabah) REFERENCES nasabah(id_nasabah)
);
ALTER TABLE setoran ADD COLUMN id_penjemputan BIGINT UNSIGNED NULL AFTER id_jenis,
  ADD FOREIGN KEY (id_penjemputan) REFERENCES penjemputan(id_penjemputan) ON DELETE SET NULL;

-- Data awal. Akun admin dan petugas hanya dibuat di sini. GANTI kata sandi sebelum dipakai sungguhan.
INSERT INTO jenis_sampah (nama_jenis, harga_per_kg) VALUES
 ('Plastik PET',3500),('Kardus',2000),('Kertas HVS',2500),('Botol kaca',800),('Kaleng',6000);
INSERT INTO users (name,email,password,role) VALUES
 ('Admin Pengelola','admin@ecobank.id','$2y$10$pjEEaOIc3UOangJ0OsdFTuqLznT1e8dEYiVh7DHBV0mnLt8LMtVxC','admin'),
 ('Joko','joko@mail.com','$2y$10$he6.fnJCt4.lbmsgAxYxBOiM3DTJ6gbC1dYtU3/1wFyZy.PAWt4PW','petugas'),
 ('Siti Aminah','siti@mail.com','$2y$10$3Zp2Km0C6.RjjTAVp3.GiOqgybMEroEGjded45r/v/szk7Z0FZ9om','nasabah');
INSERT INTO petugas (id_user,nomor_telepon) VALUES (2,'081200000001');
INSERT INTO nasabah (id_user,alamat,nomor_telepon) VALUES (3,'Jl. Melati 12, RT 03','081234567890');
