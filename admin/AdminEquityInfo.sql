-- phpMyAdmin SQL Dump
-- version 4.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 06, 2015 at 01:23 PM
-- Server version: 5.5.42-37.1
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cykoduck_TCAD04272015`
--

-- --------------------------------------------------------

--
-- Table structure for table `IMP_DET`
--

CREATE TABLE IF NOT EXISTS `IMP_DET` (
  `prop_id` int(12) DEFAULT NULL,
  `prop_val_yr` int(4) DEFAULT NULL,
  `imprv_id` int(12) DEFAULT NULL,
  `imprv_det_id` int(12) DEFAULT NULL,
  `Imprv_det_type_cd` varchar(20) DEFAULT NULL,
  `Imprv_det_type_desc` varchar(50) DEFAULT NULL,
  `Imprv_det_class_cd` varchar(20) DEFAULT NULL,
  `yr_built` int(4) DEFAULT NULL,
  `depreciation_yr` int(4) DEFAULT NULL,
  `imprv_det_area` int(5) DEFAULT NULL,
  `imprv_det_val` int(5) DEFAULT NULL,
  `sketch_cmds` varchar(500) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Improvement Detail';

--
-- Dumping data for table `IMP_DET`
--

INSERT INTO `IMP_DET` (`prop_id`, `prop_val_yr`, `imprv_id`, `imprv_det_id`, `Imprv_det_type_cd`, `Imprv_det_type_desc`, `Imprv_det_class_cd`, `yr_built`, `depreciation_yr`, `imprv_det_area`, `imprv_det_val`, `sketch_cmds`) VALUES
(253870, 2015, 212761, 247763, '1ST       ', '1st Floor                ', 'WV        ', 1965, 1965, 1774, 118920, '                                                                                                                           \r'),
(253870, 2015, 212761, 247764, '2ND       ', '2nd Floor                ', 'WW        ', 1965, 1965, 1134, 64170, '                                                                                                                           \r'),
(253870, 2015, 212761, 1072318, '011       ', 'PORCH OPEN 1ST F         ', '*         ', 1965, 1965, 36, 353, '                                                                                                                           \r'),
(253870, 2015, 212761, 1072319, '041       ', 'GARAGE ATT 1ST F         ', 'WV        ', 1965, 1965, 594, 11463, '                                                                                                                           \r'),
(253870, 2015, 212761, 1072320, '095       ', 'HVAC RESIDENTIAL         ', '*         ', 1965, 1965, 2908, 5016, '                                                                                                                           \r'),
(253870, 2015, 212761, 1072321, '251       ', 'BATHROOM                 ', '*         ', 1965, 1965, 3, 0, '                                                                                                                           \r'),
(253870, 2015, 212761, 1072322, '522       ', 'FIREPLACE                ', '*         ', 1965, 1965, 1, 2588, '                                                                                                                           \r'),
(253870, 2015, 212761, 1072323, '612       ', 'TERRACE UNCOVERD         ', '*         ', 1965, 1965, 320, 1380, '                                                                                                                           \r'),
(253875, 2015, 212766, 247769, '1ST       ', '1st Floor                ', 'WV        ', 1965, 1965, 1701, 103367, '                                                                                                                           \r'),
(253875, 2015, 212766, 247770, '2ND       ', '2nd Floor                ', 'WS        ', 1965, 1965, 992, 50327, '                                                                                                                           \r'),
(253875, 2015, 212766, 1072365, '011       ', 'PORCH OPEN 1ST F         ', '*         ', 1965, 1965, 325, 2763, '                                                                                                                           \r'),
(253875, 2015, 212766, 1072366, '011       ', 'PORCH OPEN 1ST F         ', '*         ', 1965, 1965, 168, 1428, '                                                                                                                           \r'),
(253875, 2015, 212766, 1072367, '041       ', 'GARAGE ATT 1ST F         ', 'WV        ', 1965, 1965, 594, 9935, '                                                                                                                           \r'),
(253875, 2015, 212766, 1072368, '095       ', 'HVAC RESIDENTIAL         ', '*         ', 1965, 1965, 2693, 4026, '                                                                                                                           \r'),
(253875, 2015, 212766, 1072369, '251       ', 'BATHROOM                 ', '*         ', 1965, 1965, 2, 0, '                                                                                                                           \r'),
(253875, 2015, 212766, 1072371, '522       ', 'FIREPLACE                ', '*         ', 1965, 1965, 1, 2243, '                                                                                                                           \r'),
(253875, 2015, 212766, 1072373, '604       ', 'POOL RES CONC            ', '*         ', 1965, 1965, 1, 1150, '                                                                                                                           \r'),
(253877, 2015, 212768, 247772, '1ST       ', '1st Floor                ', 'WV        ', 1965, 1965, 2914, 221385, '                                                                                                                           \r'),
(253877, 2015, 212768, 1072386, '011       ', 'PORCH OPEN 1ST F         ', '*         ', 1965, 1965, 112, 1245, '                                                                                                                           \r'),
(253877, 2015, 212768, 1072387, '095       ', 'HVAC RESIDENTIAL         ', '*         ', 1965, 1965, 2914, 5697, '                                                                                                                           \r'),
(253877, 2015, 212768, 1072388, '251       ', 'BATHROOM                 ', '*         ', 1965, 1965, 2, 0, '                                                                                                                           \r'),
(253877, 2015, 212768, 1072389, '522       ', 'FIREPLACE                ', '*         ', 1965, 1965, 1, 2933, '                                                                                                                           \r'),
(253877, 2015, 212768, 1072390, '612       ', 'TERRACE UNCOVERD         ', '*         ', 1965, 1965, 304, 1486, '                                                                                                                           \r'),
(253882, 2015, 212773, 247777, '1ST       ', '1st Floor                ', 'WV        ', 1965, 1965, 2940, 197083, '                                                                                                                           \r'),
(253882, 2015, 212773, 1072429, '011       ', 'PORCH OPEN 1ST F         ', '*         ', 1965, 1965, 94, 923, '                                                                                                                           \r'),
(253882, 2015, 212773, 1072430, '041       ', 'GARAGE ATT 1ST F         ', 'WV        ', 1965, 1965, 420, 8105, '                                                                                                                           \r'),
(253882, 2015, 212773, 1072431, '095       ', 'HVAC RESIDENTIAL         ', '*         ', 1965, 1965, 2940, 5072, '                                                                                                                           \r'),
(253882, 2015, 212773, 1072432, '251       ', 'BATHROOM                 ', '*         ', 1965, 1965, 2, 0, '                                                                                                                           \r'),
(253882, 2015, 212773, 1072433, '522       ', 'FIREPLACE                ', '*         ', 1965, 1965, 1, 2588, '                                                                                                                           \r');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `IMP_DET`
--
ALTER TABLE `IMP_DET`
  ADD KEY `prop_id` (`prop_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


