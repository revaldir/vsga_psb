-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.7.24 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for vsga_psb
CREATE DATABASE IF NOT EXISTS `vsga_psb` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `vsga_psb`;

-- Dumping structure for table vsga_psb.data_pendaftaran
CREATE TABLE IF NOT EXISTS `data_pendaftaran` (
  `pendaftaran_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `no_pendaftaran` varchar(255) DEFAULT NULL,
  `status_pendaftaran` enum('Diterima','Cadangan','Tidak Diterima','Menunggu') DEFAULT 'Menunggu',
  `pas_foto` varchar(50) DEFAULT NULL,
  `ijazah` varchar(50) DEFAULT NULL,
  `surat_pernyataan` varchar(50) DEFAULT NULL,
  `tgl_pendaftaran` datetime DEFAULT NULL,
  PRIMARY KEY (`pendaftaran_id`),
  KEY `user_fk` (`user_id`),
  CONSTRAINT `user_fk` FOREIGN KEY (`user_id`) REFERENCES `users_tb` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

-- Dumping structure for table vsga_psb.users_tb
CREATE TABLE IF NOT EXISTS `users_tb` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('P','L') DEFAULT 'L',
  `agama` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(255) DEFAULT NULL,
  `tempat_lahir` varchar(255) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `nama_ortu` varchar(255) DEFAULT NULL,
  `no_telepon_ortu` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status_user` int(11) DEFAULT '0',
  `role` int(11) DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
