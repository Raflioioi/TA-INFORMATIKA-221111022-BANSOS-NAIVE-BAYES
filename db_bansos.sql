-- Database: `db_bansos`
CREATE DATABASE IF NOT EXISTS `db_bansos`;
USE `db_bansos`;

-- Tabel: `admin`
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Admin (Default Login: admin / admin123)
INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$.CZJxGw1VZFJ5PWlVr3JSePWPv9L8.U0eIsY3cLo0fPnBZND6MzyS');

-- Tabel: `warga`
CREATE TABLE IF NOT EXISTS `warga` (
  `id_warga` int(11) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `no_kk` varchar(16) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tempat_tanggal_lahir` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `status_perkawinan` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `pekerjaan` varchar(100) NOT NULL,
  `penghasilan` varchar(50) NOT NULL,
  `jumlah_tanggungan` varchar(50) NOT NULL,
  `kondisi_lantai` varchar(100) DEFAULT NULL,
  `kondisi_dinding` varchar(100) DEFAULT NULL,
  `kondisi_atap` varchar(100) DEFAULT NULL,
  `status_kepemilikan_rumah` varchar(100) DEFAULT NULL,
  `pendidikan_terakhir` varchar(100) DEFAULT NULL,
  `jumlah_anak_sekolah` varchar(50) DEFAULT NULL,
  `kepemilikan_aset` varchar(100) DEFAULT NULL,
  `pengeluaran_bulanan` varchar(50) DEFAULT NULL,
  `akses_listrik` varchar(100) DEFAULT NULL,
  `akses_air` varchar(100) DEFAULT NULL,
  `kondisi_kesehatan` varchar(100) DEFAULT NULL,
  `status_bantuan` enum('Proses','Layak','Tidak Layak','Disalurkan') NOT NULL DEFAULT 'Proses',
  PRIMARY KEY (`id_warga`),
  UNIQUE KEY `nik` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
