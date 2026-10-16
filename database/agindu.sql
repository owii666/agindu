-- AGINDU (Agribisnis Indonesia Mendunia)
-- Skema Database & Data Awal (Seed)
-- Kompatibel dengan MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.x di Laragon

CREATE DATABASE IF NOT EXISTS `agindu` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `agindu`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `export_doc_checklists`;
DROP TABLE IF EXISTS `temperature_logs`;
DROP TABLE IF EXISTS `timeline_events`;
DROP TABLE IF EXISTS `batches`;
DROP TABLE IF EXISTS `destination_countries`;
DROP TABLE IF EXISTS `commodities`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- 1. TABEL PENGGUNA (USERS)
-- ============================================================================
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) NOT NULL DEFAULT 'producer',
    `company_name` VARCHAR(150) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. TABEL KOMODITAS (COMMODITIES)
-- ============================================================================
CREATE TABLE `commodities` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `temp_min` DECIMAL(5,2) NOT NULL,
    `temp_max` DECIMAL(5,2) NOT NULL,
    `temp_tolerance` DECIMAL(4,2) NOT NULL DEFAULT 1.50,
    `unit` VARCHAR(20) NOT NULL DEFAULT 'kg',
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. TABEL NEGARA TUJUAN EKSPOR (DESTINATION_COUNTRIES)
-- ============================================================================
CREATE TABLE `destination_countries` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(10) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `region` VARCHAR(50) NOT NULL,
    `requirements_summary` TEXT NOT NULL,
    `regulations_summary` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. TABEL BATCH EKSPOR (BATCHES)
-- ============================================================================
CREATE TABLE `batches` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `batch_code` VARCHAR(30) NOT NULL UNIQUE,
    `public_token` VARCHAR(64) NOT NULL UNIQUE,
    `commodity_id` INT UNSIGNED NOT NULL,
    `producer_name` VARCHAR(150) NOT NULL,
    `origin_regency` VARCHAR(100) NOT NULL,
    `origin_province` VARCHAR(100) NOT NULL,
    `harvest_date` DATE NOT NULL,
    `volume_kg` DECIMAL(10,2) NOT NULL,
    `destination_country_id` INT UNSIGNED NOT NULL,
    `status` VARCHAR(30) NOT NULL DEFAULT 'processing',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_batch_token` (`public_token`),
    INDEX `idx_batch_status` (`status`),
    INDEX `idx_batch_commodity` (`commodity_id`),
    INDEX `idx_batch_country` (`destination_country_id`),
    CONSTRAINT `fk_batch_commodity` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_batch_country` FOREIGN KEY (`destination_country_id`) REFERENCES `destination_countries` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. TABEL LINIMASA PERJALANAN (TIMELINE_EVENTS)
-- ============================================================================
CREATE TABLE `timeline_events` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `batch_id` INT UNSIGNED NOT NULL,
    `stage` VARCHAR(30) NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `recorded_at` DATETIME NOT NULL,
    `created_by` VARCHAR(100) NOT NULL DEFAULT 'Sistem AGINDU',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_timeline_batch` (`batch_id`),
    CONSTRAINT `fk_timeline_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. TABEL LOG SUHU IOT (TEMPERATURE_LOGS)
-- ============================================================================
CREATE TABLE `temperature_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `batch_id` INT UNSIGNED NOT NULL,
    `temperature` DECIMAL(5,2) NOT NULL,
    `humidity` DECIMAL(5,2) NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'normal',
    `is_anomaly` TINYINT(1) NOT NULL DEFAULT 0,
    `recorded_at` DATETIME NOT NULL,
    INDEX `idx_temp_batch` (`batch_id`),
    INDEX `idx_temp_time` (`recorded_at`),
    CONSTRAINT `fk_temp_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. TABEL CHECKLIST DOKUMEN EKSPOR (EXPORT_DOC_CHECKLISTS)
-- ============================================================================
CREATE TABLE `export_doc_checklists` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `batch_id` INT UNSIGNED NOT NULL,
    `doc_key` VARCHAR(50) NOT NULL,
    `doc_name` VARCHAR(150) NOT NULL,
    `is_mandatory` TINYINT(1) NOT NULL DEFAULT 1,
    `is_checked` TINYINT(1) NOT NULL DEFAULT 0,
    `checked_at` DATETIME NULL,
    `notes` TEXT NULL,
    INDEX `idx_checklist_batch` (`batch_id`),
    CONSTRAINT `fk_checklist_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DATA SEED: PENGGUNA DEFAULT
-- Username: admin | Password: admin123
-- ============================================================================
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password_hash`, `role`, `company_name`) VALUES
(1, 'Admin Ekspor AGINDU', 'admin', 'admin@agindu.test', '$2y$10$gpBw4X4arp2/h2RYd/GUYOTGLdEFcBQdwpZcI39BcVZvts20/znDu', 'admin', 'PT Agro Nusantara Global');

-- ============================================================================
-- DATA SEED: KOMODITAS & RENTANG SUHU AMAN
-- ============================================================================
INSERT INTO `commodities` (`id`, `code`, `name`, `category`, `temp_min`, `temp_max`, `temp_tolerance`, `unit`, `description`) VALUES
(1, 'KOP-GAYO', 'Kopi Arabika Gayo Specialty', 'Perkebunan', 18.00, 24.00, 2.00, 'kg', 'Kopi Arabika proses basah (wet hulled) kadar air maks 12% dari dataran tinggi Aceh.'),
(2, 'KAK-KLK', 'Kakao Fermentasi Kolaka Grade A', 'Perkebunan', 16.00, 22.00, 2.00, 'kg', 'Biji kakao terfermentasi sempurna dengan kadar air stabil dan aroma cokelat pekat dari Sulawesi Tenggara.'),
(3, 'PAL-BND', 'Pala Organik Banda Whole', 'Rempah', 20.00, 26.00, 2.00, 'kg', 'Biji pala utuh tanpa cangkang, kadar aflatoksin rendah, sertifikasi organik Kepulauan Banda.'),
(4, 'MNG-TBN', 'Manggis Super Fresh Tabanan', 'Hortikultura', 8.00, 12.00, 1.50, 'kg', 'Buah manggis segar kualitas ekspor, kelopak hijau segar, bebas semut dan kutu putih dari Bali.'),
(5, 'UDG-TRK', 'Udang Vaname Beku Tarakan (IQF)', 'Perikanan', -22.00, -18.00, 1.00, 'kg', 'Udang beku Individual Quick Frozen (IQF) standar HACCP tinggi untuk pasar ekspor Jepang dan AS.');

-- ============================================================================
-- DATA SEED: NEGARA TUJUAN & REGULASI
-- ============================================================================
INSERT INTO `destination_countries` (`id`, `code`, `name`, `region`, `requirements_summary`, `regulations_summary`) VALUES
(1, 'JPN', 'Jepang', 'Asia Timur', 
'Uji Maximum Residue Limits (MRL) pestisida ketat (Positive List System), Sertifikat Fitosanitari MAFF, Bebas serangga hidup, Certificate of Analysis (CoA) laboratorium terakreditasi.',
'Food Sanitation Act Japan dan Plant Protection Act. Pemeriksaan fisik dan karantina di pelabuhan Tokyo / Yokohama.'),

(2, 'NLD', 'Belanda (Uni Eropa)', 'Eropa', 
'Kepatuhan EU Deforestation Regulation (EUDR) dengan koordinat geolokasi lahan panen, EU Phytosanitary Certificate, Uji aflatoksin di bawah 10 ppb, Dokumen EUR-1 / REX.',
'Regulasi Pangan Uni Eropa (EC) No 178/2002 dan regulasi karantina (EU) 2016/2031. Pelabuhan masuk utama Rotterdam Port.'),

(3, 'USA', 'Amerika Serikat', 'Amerika Utara', 
'FDA Prior Notice of Imported Foods, Registrasi Fasilitas Pangan FDA (Food Facility Registration), Kepatuhan FSMA (Foreign Supplier Verification Programs), USDA APHIS Permit.',
'Bioterrorism Act 2002 dan Food Safety Modernization Act. Wajib melampirkan manifes elektronik sebelum kapal bertolak.'),

(4, 'CHN', 'Tiongkok', 'Asia Timur', 
'Registrasi GACC (General Administration of Customs China) berdasarkan Decree 248 dan 249, Sertifikat Fitosanitari CIQ, Label bilingual Mandarin pada kemasan primer dan sekunder.',
'Pemeriksaan bea cukai dan karantina ketat GACC. Produsen dan fasilitas packing house wajib terdaftar resmi di sistem CIFER.'),

(5, 'SAU', 'Arab Saudi', 'Timur Tengah', 
'Sertifikasi Halal yang diakui SFDA (Saudi Food and Drug Authority), Sertifikat Kesehatan Karantina, Bebas kontaminasi kimia/biologis, Tanggal kedaluwarsa minimal 12 bulan.',
'SFDA Technical Regulation dan standar SASO. Wajib registrasi produk pada platform SABER / GHAD sebelum pengiriman.');

-- ============================================================================
-- DATA SEED: BATCH EKSPOR CONTOH
-- ============================================================================
INSERT INTO `batches` (`id`, `batch_code`, `public_token`, `commodity_id`, `producer_name`, `origin_regency`, `origin_province`, `harvest_date`, `volume_kg`, `destination_country_id`, `status`, `notes`) VALUES
(1, 'AGD-2026-0001', 'a1f8b3c9d2e4a6f7b8c9d0e1f2a3b4c5', 1, 'Koperasi Petani Kopi Gayo Mandiri', 'Aceh Tengah', 'Aceh', '2026-09-10', 18500.00, 1, 'in_transit', 'Pengiriman 1 kontainer 20ft menuju Pelabuhan Yokohama Jepang.'),
(2, 'AGD-2026-0002', 'b2e9c4d0a3f5b7c8d9e0f1a2b3c4d5e6', 2, 'PT Kolaka Agro Lestari', 'Kolaka', 'Sulawesi Tenggara', '2026-09-14', 24000.00, 2, 'shipped', 'Kontainer reefer 40ft melalui Pelabuhan Tanjung Priok menuju Rotterdam.'),
(3, 'AGD-2026-0003', 'c3d0e5f1b4a6c8d9e0f1a2b3c4d5e6f7', 4, 'Gapoktan Manggis Sari Tabanan', 'Tabanan', 'Bali', '2026-10-01', 4500.00, 4, 'in_transit', 'Kargo udara direct flight Denpasar ke Guangzhou, cold chain ketat 9-11 C.'),
(4, 'AGD-2026-0004', 'd4e1f6a2c5b7d9e0f1a2b3c4d5e6f7a8', 5, 'PT Tarakan Marine Nusantara', 'Tarakan', 'Kalimantan Utara', '2026-09-25', 16000.00, 3, 'in_transit', 'Udang beku kemasan master carton, tujuan Los Angeles Port via Tanjung Perak.'),
(5, 'AGD-2026-0005', 'e5f2a7b3d6c8e0f1a2b3c4d5e6f7a8b9', 3, 'Perkumpulan Petani Rempah Banda Neira', 'Maluku Tengah', 'Maluku', '2026-09-05', 6200.00, 5, 'completed', 'Pengapalan telah tiba di Pelabuhan Jeddah Islamic Port dan lolos verifikasi SFDA.'),
(6, 'AGD-2026-0006', 'f6a3b8c4e7d9f1a2b3c4d5e6f7a8b9c0', 1, 'CV Takengon Mountain Highland', 'Bener Meriah', 'Aceh', '2026-10-05', 9200.00, 2, 'packed', 'Proses pengemasan hermetic bag selesai di gudang Medan, persiapan surat jalan pelabuhan.'),
(7, 'AGD-2026-0007', 'a7b4c9d5e8f0a2b3c4d5e6f7a8b9c0d1', 2, 'Koperasi Tani Kakao Sejahtera', 'Kolaka Utara', 'Sulawesi Tenggara', '2026-10-08', 12000.00, 1, 'processing', 'Tahap grading dan uji kadar air laboratorium sebelum pengemasan goni ekspor.'),
(8, 'AGD-2026-0008', 'b8c5d0e6f9a1b3c4d5e6f7a8b9c0d1e2', 4, 'Bhuana Giri Agro Ekspor', 'Bangli', 'Bali', '2026-10-11', 3800.00, 4, 'processing', 'Tahap pembersihan mahkota dan perlakuan uap panas (Vapour Heat Treatment) sebelum packing.');

-- ============================================================================
-- DATA SEED: LINIMASA PERJALANAN LOGISTIK
-- ============================================================================
INSERT INTO `timeline_events` (`batch_id`, `stage`, `title`, `location`, `description`, `recorded_at`, `created_by`) VALUES
-- Batch 1: Kopi Arabika Gayo ke Jepang
(1, 'harvest', 'Panen Kopi Cherry Merah Petik Pilihan', 'Kecamatan Bebesen, Aceh Tengah', 'Pemetikan buah kopi cherry matang sempurna dari kebun mitra elevasi 1.450 mdpl.', '2026-09-10 08:30:00', 'Koordinator Lapangan Kebun'),
(1, 'sorting', 'Pengupasan Kulit & Fermentasi Basah', 'Fasilitas Wet Mill Takengon', 'Pemisahan pulp dan mucilage, fermentasi terkontrol 18 jam, pencucian air sumber pegunungan.', '2026-09-12 14:00:00', 'Quality Control Wet Mill'),
(1, 'packaging', 'Hulling & Pengemasan GrainPro Export', 'Gudang Ekspor Medan, Sumatera Utara', 'Pengeringan kadar air 11.8%, sortasi triple pick, pengemasan dalam kantong GrainPro 60 kg.', '2026-09-20 11:15:00', 'Supervisor Pengemasan'),
(1, 'warehouse', 'Inspeksi Karantina & Cold Storage Belawan', 'Depot Logistik Pelabuhan Belawan', 'Inspeksi karantina tumbuhan, penerbitan Phytosanitary Certificate oleh Karantina Sumut.', '2026-09-24 16:45:00', 'Petugas Karantina Tumbuhan'),
(1, 'port', 'Pemuatan Kontainer ke Kapal Feeder', 'Terminal Peti Kemas Belawan', 'Kontainer dinaikkan ke MV Meratus Belawan menuju hub Singapura untuk alih muat.', '2026-09-28 09:20:00', 'Perusahaan Logistik Samudera'),
(1, 'shipping', 'Berlayar Menuju Pelabuhan Yokohama', 'Selat Malaka - Pasifik Barat', 'Kapal induk Ocean Network Express mengangkut kargo dengan pemantauan suhu telemetri aktif.', '2026-10-02 06:00:00', 'Otoritas Pelayaran'),

-- Batch 2: Kakao Kolaka ke Belanda
(2, 'harvest', 'Panen Buah Kakao Matang Pohon', 'Perkebunan Rakyat Kolaka', 'Pemetikan buah kakao klon unggul dengan kematangan seragam.', '2026-09-14 07:45:00', 'Kelompok Tani Kakao'),
(2, 'sorting', 'Fermentasi Kotak Kayu Berlapis 5 Hari', 'Pusat Pengolahan Kakao Kolaka', 'Fermentasi mikroba alami dengan pembalikan teratur tiap 48 jam untuk aroma maksimal.', '2026-09-17 10:00:00', 'Analis Pasca Panen'),
(2, 'packaging', 'Penjemuran Matahari & Uji Cut Test', 'Pusat Pengolahan Kakao Kolaka', 'Kadar air akhir 7.2%, persentase biji cacat di bawah 2%, pengemasan karung goni ekspor bersertifikat.', '2026-09-24 15:30:00', 'Kepala Gudang Ekspor'),
(2, 'warehouse', 'Konsolidasi di Gudang Marunda Jakarta', 'Kawasan Berikat Marunda, DKI Jakarta', 'Penyimpanan dalam gudang berventilasi kelembapan terkontrol, audit sertifikat EUDR.', '2026-09-29 13:00:00', 'Manajer Operasional Gudang'),
(2, 'port', 'Clearance Kepabeanan & Bea Cukai', 'Jakarta International Container Terminal (JICT)', 'Penerbitan PEB (Pemberitahuan Ekspor Barang) dan verifikasi dokumen karantina ekspor.', '2026-10-04 10:40:00', 'Agen Kepabeanan'),
(2, 'shipping', 'Kapal Kargo Berlayar ke Rotterdam', 'Laut Jawa Menuju Samudera Hindia', 'Kapal kontainer berlayar rute Tanjung Priok - Terusan Suez - Rotterdam.', '2026-10-07 18:00:00', 'Operator Pengapalan Internasional'),

-- Batch 3: Manggis Super Tabanan ke Tiongkok
(3, 'harvest', 'Pemetikan Buah Manggis Indeks Kematangan 2', 'Kebun Manggis Pupuan, Tabanan', 'Panen tangkai utuh menggunakan gunting khusus tanpa dijatuhkan ke tanah.', '2026-10-01 07:00:00', 'Kelompok Tani Pupuan'),
(3, 'sorting', 'Pembersihan Kompresor Angin & Sortir Super', 'Packing House Terdaftar GACC Tabanan', 'Pembersihan calyx dengan hembusan udara bertekanan tinggi untuk sterilisasi kutu putih.', '2026-10-02 09:30:00', 'Tim Inspeksi Mutu Buah'),
(3, 'packaging', 'Pengemasan Corrugated Box Berlubang Ventilasi', 'Packing House Terdaftar GACC Tabanan', 'Kemas isi 8 kg per karton berlabel GACC CIQ bilingual Mandarin-Inggris.', '2026-10-03 16:00:00', 'Supervisor Packing House'),
(3, 'warehouse', 'Pra-Pendinginan (Pre-cooling) 10 Derajat Celsius', 'Cold Storage Bandara I Gusti Ngurah Rai', 'Penurunan suhu lapangan buah secara bertahap menuju suhu cold chain 10 C.', '2026-10-05 11:00:00', 'Manajer Cold Chain'),
(3, 'port', 'Inspeksi Karantina Bandara & Muat Pesawat', 'Terminal Kargo Bandara Denpasar', 'Pemeriksaan fisik oleh BKHIT Bali dan pemuatan ke pallet pesawat kargo charter Guangzhou.', '2026-10-07 20:15:00', 'Petugas Karantina Hewan Ikan Tumbuhan'),

-- Batch 4: Udang Vaname Beku ke Amerika Serikat
(4, 'harvest', 'Panen Tambak Ramah Lingkungan', 'Tambak Terbina Tarakan Barat', 'Panen malam hari bersuhu rendah, udang segar langsung direndam air es higienis.', '2026-09-25 22:30:00', 'Kepala Budidaya Tambak'),
(4, 'sorting', 'Pencucian, De-heading & Size Grading', 'Unit Pengolahan Ikan (UPI) Tarakan', 'Pemotongan kepala dan pemilahan ukuran otomatis sesuai standar grade internasional.', '2026-09-26 08:00:00', 'Quality Assurance UPI'),
(4, 'packaging', 'Pembekuan Cepat IQF -40 Derajat Celsius', 'Ruang Pembekuan Spiral IQF Tarakan', 'Pembekuan individual flash freezing, penimbangan netto, kemasan master carton berlapis polybag.', '2026-09-27 15:45:00', 'Teknisi Pembekuan'),
(4, 'warehouse', 'Penyimpanan Cold Storage -22 Derajat Celsius', 'Depot Cold Storage Pelabuhan Malundung', 'Penyimpanan suhu beku stabil di bawah minus 20 C dengan log pencatat data digital.', '2026-09-30 10:00:00', 'Supervisor Cold Storage'),
(4, 'port', 'Stuffing ke Kontainer Reefer Bersertifikat FDA', 'Pelabuhan Malundung Tarakan', 'Pemuatan ke kontainer pendingin dengan genset hidup menuju pelabuhan transshipment Surabaya.', '2026-10-04 14:00:00', 'Petugas Logistik Pelabuhan'),

-- Batch 5: Pala Organik Banda ke Arab Saudi (Tiba di tujuan)
(5, 'harvest', 'Pemanenan Buah Pala Pohon Tua Banda', 'Perkebunan Pulau Banda Besar', 'Pemetikan buah pala tua merekah, pemisahan fuli (mace) merah dan biji berkulit.', '2026-09-05 08:00:00', 'Petani Tradisional Banda'),
(5, 'sorting', 'Pengeringan Alami & Pemecahan Tempurung', 'Rumah Jemur Rempah Banda Neira', 'Pengeringan bertahap hingga biji berbunyi nyaring, pemisahan tempurung manual.', '2026-09-10 11:30:00', 'Pengawas Sortir Biji'),
(5, 'packaging', 'Pengemasan Karung Vakum Food Grade', 'Fasilitas Rempah Ambon', 'Uji bebas aflatoksin lab dan pengemasan karung kedap udara dengan sertifikat Halal.', '2026-09-18 14:20:00', 'Penanggung Jawab Mutu'),
(5, 'warehouse', 'Inspeksi Ekspor Pelabuhan Ambon', 'Pelabuhan Yos Sudarso Ambon', 'Penerbitan dokumen karantina dan izin ekspor Kementerian Perdagangan.', '2026-09-22 09:00:00', 'Staf Karantina Ambon'),
(5, 'shipping', 'Pelayaran Kargo Jalur Timur Tengah', 'Samudera Hindia - Laut Merah', 'Pengangkutan laut reguler menuju pelabuhan transit Colombo lalu Jeddah.', '2026-09-26 04:00:00', 'Nakhoda Kapal Kargo'),
(5, 'arrival', 'Tiba di Jeddah Islamic Port & Lolos Karantina', 'Jeddah Port, Arab Saudi', 'Bongkar kontainer, pemeriksaan dokumen SFDA dan uji sampel acak dinyatakan memenuhi standar.', '2026-10-10 11:00:00', 'Agen Penerima di Arab Saudi');

-- ============================================================================
-- DATA SEED: LOG SUHU IOT
-- ============================================================================
-- Log Suhu Batch 1 (Kopi Gayo, Rentang Aman: 18 - 24 C)
INSERT INTO `temperature_logs` (`batch_id`, `temperature`, `humidity`, `status`, `is_anomaly`, `recorded_at`) VALUES
(1, 20.50, 62.00, 'normal', 0, '2026-10-12 00:00:00'),
(1, 20.30, 62.50, 'normal', 0, '2026-10-12 02:00:00'),
(1, 19.80, 63.00, 'normal', 0, '2026-10-12 04:00:00'),
(1, 19.50, 63.20, 'normal', 0, '2026-10-12 06:00:00'),
(1, 21.00, 61.50, 'normal', 0, '2026-10-12 08:00:00'),
(1, 22.40, 60.80, 'normal', 0, '2026-10-12 10:00:00'),
(1, 23.20, 59.90, 'normal', 0, '2026-10-12 12:00:00'),
(1, 23.80, 59.50, 'warning', 0, '2026-10-12 14:00:00'),
(1, 23.50, 60.10, 'normal', 0, '2026-10-12 16:00:00'),
(1, 22.10, 61.00, 'normal', 0, '2026-10-12 18:00:00'),
(1, 21.20, 61.70, 'normal', 0, '2026-10-12 20:00:00'),
(1, 20.70, 62.20, 'normal', 0, '2026-10-12 22:00:00'),
(1, 20.40, 62.00, 'normal', 0, '2026-10-13 00:00:00'),
(1, 19.90, 62.80, 'normal', 0, '2026-10-13 03:00:00'),
(1, 21.50, 61.20, 'normal', 0, '2026-10-13 06:00:00'),
(1, 22.80, 60.30, 'normal', 0, '2026-10-13 09:00:00'),
(1, 23.40, 59.80, 'normal', 0, '2026-10-13 12:00:00'),
(1, 22.60, 60.50, 'normal', 0, '2026-10-13 15:00:00'),
(1, 21.30, 61.40, 'normal', 0, '2026-10-13 18:00:00'),
(1, 20.80, 62.00, 'normal', 0, '2026-10-13 21:00:00'),
(1, 20.20, 62.30, 'normal', 0, '2026-10-14 00:00:00'),
(1, 20.90, 61.90, 'normal', 0, '2026-10-14 03:00:00'),
(1, 21.70, 61.10, 'normal', 0, '2026-10-14 06:00:00'),
(1, 22.30, 60.70, 'normal', 0, '2026-10-14 09:00:00'),
(1, 21.80, 61.00, 'normal', 0, '2026-10-14 12:00:00');

-- Log Suhu Batch 3 (Manggis Tabanan, Rentang Aman: 8 - 12 C, dengan 1 Anomali Suhu Ruang Kargo Sempat Naik)
INSERT INTO `temperature_logs` (`batch_id`, `temperature`, `humidity`, `status`, `is_anomaly`, `recorded_at`) VALUES
(3, 9.80, 88.00, 'normal', 0, '2026-10-13 01:00:00'),
(3, 10.10, 87.50, 'normal', 0, '2026-10-13 03:00:00'),
(3, 9.90, 88.20, 'normal', 0, '2026-10-13 05:00:00'),
(3, 10.40, 86.80, 'normal', 0, '2026-10-13 07:00:00'),
(3, 11.20, 85.00, 'normal', 0, '2026-10-13 09:00:00'),
(3, 11.80, 84.20, 'warning', 0, '2026-10-13 11:00:00'),
-- Anomali: Suhu melompat ke 14.50 C saat transfer unit reefer bandara
(3, 14.50, 78.50, 'danger', 1, '2026-10-13 12:30:00'),
(3, 12.80, 81.00, 'warning', 0, '2026-10-13 13:30:00'),
(3, 10.80, 86.00, 'normal', 0, '2026-10-13 14:30:00'),
(3, 9.90, 87.80, 'normal', 0, '2026-10-13 17:00:00'),
(3, 9.50, 88.50, 'normal', 0, '2026-10-13 20:00:00'),
(3, 9.70, 88.00, 'normal', 0, '2026-10-13 23:00:00'),
(3, 10.00, 87.20, 'normal', 0, '2026-10-14 03:00:00'),
(3, 10.20, 87.00, 'normal', 0, '2026-10-14 07:00:00'),
(3, 9.80, 88.10, 'normal', 0, '2026-10-14 11:00:00');

-- Log Suhu Batch 4 (Udang Vaname Beku, Rentang Aman: -22 s/d -18 C)
INSERT INTO `temperature_logs` (`batch_id`, `temperature`, `humidity`, `status`, `is_anomaly`, `recorded_at`) VALUES
(4, -20.80, 92.00, 'normal', 0, '2026-10-13 02:00:00'),
(4, -20.50, 92.10, 'normal', 0, '2026-10-13 05:00:00'),
(4, -19.80, 91.80, 'normal', 0, '2026-10-13 08:00:00'),
(4, -19.20, 91.00, 'normal', 0, '2026-10-13 11:00:00'),
(4, -18.40, 90.20, 'warning', 0, '2026-10-13 14:00:00'),
(4, -19.50, 91.50, 'normal', 0, '2026-10-13 17:00:00'),
(4, -20.40, 92.20, 'normal', 0, '2026-10-13 20:00:00'),
(4, -21.10, 92.80, 'normal', 0, '2026-10-13 23:00:00'),
(4, -20.60, 92.00, 'normal', 0, '2026-10-14 04:00:00'),
(4, -19.90, 91.40, 'normal', 0, '2026-10-14 08:00:00'),
(4, -20.10, 91.90, 'normal', 0, '2026-10-14 12:00:00');

-- Log Suhu Batch 2 (Kakao Kolaka, Rentang Aman: 16 - 22 C)
INSERT INTO `temperature_logs` (`batch_id`, `temperature`, `humidity`, `status`, `is_anomaly`, `recorded_at`) VALUES
(2, 18.50, 68.00, 'normal', 0, '2026-10-13 00:00:00'),
(2, 18.20, 68.20, 'normal', 0, '2026-10-13 04:00:00'),
(2, 19.10, 67.50, 'normal', 0, '2026-10-13 08:00:00'),
(2, 20.30, 66.80, 'normal', 0, '2026-10-13 12:00:00'),
(2, 19.70, 67.20, 'normal', 0, '2026-10-13 16:00:00'),
(2, 18.80, 68.00, 'normal', 0, '2026-10-13 20:00:00'),
(2, 18.40, 68.40, 'normal', 0, '2026-10-14 02:00:00'),
(2, 19.50, 67.00, 'normal', 0, '2026-10-14 08:00:00'),
(2, 19.20, 67.30, 'normal', 0, '2026-10-14 12:00:00');

-- Log Suhu Batch 5 (Pala Banda, Rentang Aman: 20 - 26 C)
INSERT INTO `temperature_logs` (`batch_id`, `temperature`, `humidity`, `status`, `is_anomaly`, `recorded_at`) VALUES
(5, 23.10, 64.00, 'normal', 0, '2026-10-13 01:00:00'),
(5, 22.80, 64.50, 'normal', 0, '2026-10-13 06:00:00'),
(5, 24.20, 63.20, 'normal', 0, '2026-10-13 12:00:00'),
(5, 23.50, 63.80, 'normal', 0, '2026-10-13 18:00:00'),
(5, 22.90, 64.20, 'normal', 0, '2026-10-14 04:00:00'),
(5, 24.00, 63.50, 'normal', 0, '2026-10-14 10:00:00');

-- ============================================================================
-- DATA SEED: CHECKLIST DOKUMEN EKSPOR PER BATCH
-- ============================================================================
-- Checklist Batch 1 (Tujuan Jepang)
INSERT INTO `export_doc_checklists` (`batch_id`, `doc_key`, `doc_name`, `is_mandatory`, `is_checked`, `checked_at`, `notes`) VALUES
(1, 'phyto', 'Phytosanitary Certificate (Badan Karantina Indonesia)', 1, 1, '2026-09-24 16:30:00', 'Nomor registrasi karantina: 04.2/KT/2026/09214'),
(1, 'coo', 'Certificate of Origin (Form IJEPA / SKA Elektronik)', 1, 1, '2026-09-25 10:00:00', 'Diterbitkan Dinas Perindustrian dan Perdagangan'),
(1, 'pack_list', 'Packing List & Net Weight Specification', 1, 1, '2026-09-22 14:00:00', 'Terverifikasi 308 goni GrainPro @60kg'),
(1, 'inv', 'Commercial Invoice', 1, 1, '2026-09-22 14:00:00', 'Incoterm CIF Yokohama Port'),
(1, 'bl', 'Bill of Lading (Clean on Board)', 1, 1, '2026-09-28 11:00:00', 'B/L No: ONEY-JKT-YOK-20260901'),
(1, 'coa_mrl', 'Certificate of Analysis (Residu Pestisida MRL Jepang)', 1, 1, '2026-09-21 09:00:00', 'Lab Saraswanti Indo Genetech terakreditasi KAN');

-- Checklist Batch 2 (Tujuan Belanda / Uni Eropa)
INSERT INTO `export_doc_checklists` (`batch_id`, `doc_key`, `doc_name`, `is_mandatory`, `is_checked`, `checked_at`, `notes`) VALUES
(2, 'eudr', 'EUDR Due Diligence Statement (Geolokasi Lahan)', 1, 1, '2026-09-28 10:00:00', 'Polygon koordinat 42 petani Kolaka tervalidasi'),
(2, 'phyto', 'EU Phytosanitary Certificate', 1, 1, '2026-10-02 11:30:00', 'Diterbitkan Karantina Tanjung Priok'),
(2, 'coo', 'Certificate of Origin (Form A / REX System)', 1, 1, '2026-10-03 14:00:00', 'Nomor REX Eksportir terdaftar'),
(2, 'pack_list', 'Packing List & Fumigation Certificate', 1, 1, '2026-09-26 16:00:00', 'Fumigasi Phosphine bersertifikat AFAS'),
(2, 'inv', 'Commercial Invoice', 1, 1, '2026-09-27 09:00:00', 'Incoterm FOB Tanjung Priok'),
(2, 'bl', 'Ocean Bill of Lading', 1, 1, '2026-10-06 17:00:00', 'B/L No: MSC-SUB-RTM-88219');

-- Checklist Batch 3 (Tujuan Tiongkok)
INSERT INTO `export_doc_checklists` (`batch_id`, `doc_key`, `doc_name`, `is_mandatory`, `is_checked`, `checked_at`, `notes`) VALUES
(3, 'gacc_reg', 'GACC Packaging House & Orchard Registration', 1, 1, '2026-10-02 08:00:00', 'ID GACC: ID-PH-DPS-0042'),
(3, 'phyto', 'CIQ Phytosanitary Certificate (Bilingual)', 1, 1, '2026-10-06 15:00:00', 'Bebas hama Pseudococcus cryptus'),
(3, 'coo', 'Form E (ASEAN-China Free Trade Area)', 1, 0, NULL, 'Pengajuan online e-SKA sedang proses tanda tangan digital'),
(3, 'pack_list', 'Packing List & Air Waybill (AWB)', 1, 1, '2026-10-07 18:00:00', 'AWB No: 888-29182012 Cargo Charter'),
(3, 'inv', 'Commercial Invoice', 1, 1, '2026-10-04 10:00:00', 'Incoterm CIP Guangzhou Baiyun');

-- Checklist Batch 4 (Tujuan Amerika Serikat)
INSERT INTO `export_doc_checklists` (`batch_id`, `doc_key`, `doc_name`, `is_mandatory`, `is_checked`, `checked_at`, `notes`) VALUES
(4, 'fda_prior', 'FDA Prior Notice Confirmation Number', 1, 1, '2026-10-03 13:00:00', 'PNC No: 2026-US-FDA-9182901'),
(4, 'haccp_cert', 'HACCP & Health Certificate (BKIPM KKP)', 1, 1, '2026-10-02 16:00:00', 'Nomor Sertifikat Mutu KKP: 09/HC/TRK/X/2026'),
(4, 'coo', 'Certificate of Origin (Form D / COO KADIN)', 1, 0, NULL, 'Sedang proses verifikasi berkas'),
(4, 'pack_list', 'Packing List & Temperature Log Manifest', 1, 1, '2026-10-03 11:00:00', 'Total 1.000 master carton'),
(4, 'inv', 'Commercial Invoice', 1, 1, '2026-10-01 10:00:00', 'Incoterm CIF Los Angeles Port'),
(4, 'bl', 'Bill of Lading', 1, 0, NULL, 'Menunggu penerbitan setelah feeder berlabuh di Surabaya');
