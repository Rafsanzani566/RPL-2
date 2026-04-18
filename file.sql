-- ============================================================
--  DATABASE: webdb_rpl2
--  Project : Professional Class Portal - X-RPL 2 SMKN 1 Majalengka
--  Author  : Senior Fullstack Developer
-- ============================================================

CREATE DATABASE IF NOT EXISTS `webdb_rpl2`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `webdb_rpl2`;

-- ------------------------------------------------------------
-- Table: users
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id`        INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `username`  VARCHAR(80)     NOT NULL UNIQUE,
  `password`  VARCHAR(255)    NOT NULL COMMENT 'bcrypt hashed via password_hash()',
  `full_name` VARCHAR(150)    NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: username=admin | password=Admin@2025
INSERT INTO `users` (`username`, `password`, `full_name`) VALUES
('admin', '$2y$12$eImiTXuWVxfM37uY4JANjOe5XwtTsRuFZBwNiPYXrFYH5D4pYgNqe', 'Administrator');
-- NOTE: Re-generate this hash in PHP with:
--   echo password_hash('Admin@2025', PASSWORD_BCRYPT, ['cost' => 12]);

-- ------------------------------------------------------------
-- Table: siswa
-- ------------------------------------------------------------
CREATE TABLE `siswa` (
  `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `nisn`           VARCHAR(20)     NOT NULL UNIQUE,
  `nama`           VARCHAR(150)    NOT NULL,
  `jabatan`        ENUM(
                     'Ketua Kelas',
                     'Sekretaris',
                     'Bendahara',
                     'Anggota'
                   )               NOT NULL DEFAULT 'Anggota',
  `instagram_user` VARCHAR(100)    DEFAULT NULL COMMENT 'without @ symbol',
  `foto`           VARCHAR(255)    DEFAULT NULL COMMENT 'relative path inside /assets/img/siswa/',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data
INSERT INTO `siswa` (`nisn`, `nama`, `jabatan`, `instagram_user`, `foto`) VALUES
('0071234501', 'Ahmad Fajar Ramadhan',  'Ketua Kelas', 'fajarr.amd',   NULL),
('0071234502', 'Siti Nurhaliza',        'Sekretaris',  'sitinur_hza',  NULL),
('0071234503', 'Dendi Firmansyah',      'Bendahara',   'dendi.firm',   NULL),
('0071234504', 'Rina Apriyanti',        'Anggota',     'rina_apri',    NULL),
('0071234505', 'Gilang Pratama',        'Anggota',     'gilang.ptr',   NULL),
('0071234506', 'Dewi Rahayu',           'Anggota',     'dewi.rhy',     NULL),
('0071234507', 'Rizky Maulana',         'Anggota',     'rizkymln',     NULL),
('0071234508', 'Putri Handayani',       'Anggota',     'putrihdyn',    NULL);

-- ------------------------------------------------------------
-- Table: jadwal
-- ------------------------------------------------------------
CREATE TABLE `jadwal` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `hari`        ENUM(
                  'Senin','Selasa','Rabu',
                  'Kamis','Jumat','Sabtu'
                )               NOT NULL,
  `mapel`       VARCHAR(150)    NOT NULL,
  `jam_mulai`   TIME            NOT NULL,
  `jam_selesai` TIME            NOT NULL,
  `guru`        VARCHAR(150)    NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hari` (`hari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample schedule
INSERT INTO `jadwal` (`hari`, `mapel`, `jam_mulai`, `jam_selesai`, `guru`) VALUES
('Senin',    'Matematika',                 '07:00', '08:30', 'Bpk. Asep Suryana, S.Pd'),
('Senin',    'Bahasa Indonesia',           '08:30', '10:00', 'Ibu. Nani Rohaeni, S.Pd'),
('Senin',    'Pemrograman Berorientasi Objek', '10:15', '12:00', 'Bpk. Dede Rosman, S.Kom'),
('Selasa',   'Basis Data',                '07:00', '09:00', 'Bpk. Hendra Gunawan, S.Kom'),
('Selasa',   'Jaringan Komputer',         '09:00', '10:30', 'Bpk. Yudi Hermawan, S.T'),
('Selasa',   'Bahasa Inggris',            '10:30', '12:00', 'Ibu. Rini Susanti, S.Pd'),
('Rabu',     'Fisika',                    '07:00', '08:30', 'Bpk. Agus Hidayat, S.Pd'),
('Rabu',     'Pemrograman Web',           '08:30', '11:00', 'Ibu. Yuyun Yulianti, S.Kom'),
('Kamis',    'Pendidikan Agama Islam',    '07:00', '08:30', 'Bpk. Drs. Kusnadi'),
('Kamis',    'PKK (Produk Kreatif)',      '08:30', '11:00', 'Ibu. Sari Dewi, S.Kom'),
('Jumat',    'Penjaskes',                 '07:00', '08:30', 'Bpk. Yana Suryana, S.Pd'),
('Jumat',    'PPKn',                      '08:30', '10:00', 'Ibu. Lilis Sumiati, S.Pd'),
('Sabtu',    'Seni Budaya',               '07:00', '08:30', 'Ibu. Wulan Sari, S.Pd'),
('Sabtu',    'BK (Bimbingan Konseling)',  '08:30', '10:00', 'Ibu. Fitri Handayani, S.Pd');

-- ------------------------------------------------------------
-- Table: galeri
-- ------------------------------------------------------------
CREATE TABLE `galeri` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `judul`      VARCHAR(200)    NOT NULL,
  `file_foto`  VARCHAR(255)    NOT NULL COMMENT 'relative path inside /assets/img/galeri/',
  `tgl_upload` DATE            NOT NULL DEFAULT (CURRENT_DATE),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
