-- phpMyAdmin SQL Dump
-- version 4.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 26, 2015 at 08:41 PM
-- Server version: 5.5.42-37.1
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cykoduck_TCAD07182014`
--
CREATE DATABASE IF NOT EXISTS cykoduck_TCAD DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE cykoduck_TCAD;

-- --------------------------------------------------------

--
-- Table structure for table `BATCH_PROP`
--

CREATE TABLE IF NOT EXISTS `BATCH_PROP` (
  `prop` varchar(10) NOT NULL,
  `completed` enum('false','true','error') NOT NULL,
  `pdfs` mediumblob NOT NULL,
  `prop_mktval` varchar(20) NOT NULL,
  `Median_Sale5` varchar(20) NOT NULL,
  `Median_Sale10` varchar(20) NOT NULL,
  `Median_Sale15` varchar(20) NOT NULL,
  `Median_Eq11` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BATCH_PROP_BKUP`
--

CREATE TABLE IF NOT EXISTS `BATCH_PROP_BKUP` (
  `prop` varchar(10) NOT NULL,
  `completed` enum('false','true','error') NOT NULL,
  `pdfs` mediumblob NOT NULL,
  `prop_mktval` varchar(20) NOT NULL,
  `Median_Sale5` varchar(20) NOT NULL,
  `Median_Sale10` varchar(20) NOT NULL,
  `Median_Sale15` varchar(20) NOT NULL,
  `Median_Eq11` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BATCH_PROP_SETTINGS`
--

CREATE TABLE IF NOT EXISTS `BATCH_PROP_SETTINGS` (
  `id` int(11) NOT NULL,
  `TrimIndicated` set('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL,
  `MultiHood` set('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL,
  `IncludeVU` set('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL,
  `IncludeMLS` set('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TRUE',
  `NumPrevYears` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `MLS_SALES`
--

CREATE TABLE IF NOT EXISTS `MLS_SALES` (
  `prop_id` int(11) NOT NULL,
  `sale_price` int(14) DEFAULT NULL,
  `sale_date` varchar(14) DEFAULT NULL,
  `addr` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `MLS_SALES_SINGLEYEAR`
--

CREATE TABLE IF NOT EXISTS `MLS_SALES_SINGLEYEAR` (
  `prop_id` int(11) NOT NULL,
  `sale_price` int(14) DEFAULT NULL,
  `sale_date` varchar(14) DEFAULT NULL,
  `addr` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PROP`
--

CREATE TABLE IF NOT EXISTS `PROP` (
  `prop_id` int(20) NOT NULL,
  `prop_type_cd` varchar(10) DEFAULT NULL,
  `prop_val_yr` int(10) DEFAULT NULL,
  `sup_num` int(10) DEFAULT NULL,
  `sup_action` varchar(4) DEFAULT NULL,
  `sup_cd` varchar(20) DEFAULT NULL,
  `sup_desc` varchar(255) DEFAULT NULL,
  `geo_id` int(10) unsigned zerofill DEFAULT NULL,
  `py_owner_id` int(6) DEFAULT NULL,
  `py_owner_name` varchar(24) DEFAULT NULL,
  `partial_owner` varchar(1) DEFAULT NULL,
  `udi_group` int(1) DEFAULT NULL,
  `filler1` varchar(10) DEFAULT NULL,
  `py_addr_line1` varchar(120) DEFAULT NULL,
  `py_addr_line2` varchar(120) DEFAULT NULL,
  `py_addr_line3` varchar(120) DEFAULT NULL,
  `py_addr_city` varchar(100) DEFAULT NULL,
  `py_addr_state` varchar(100) DEFAULT NULL,
  `py_addr_country` varchar(100) DEFAULT NULL,
  `py_addr_zip` int(10) DEFAULT NULL,
  `py_addr_zip_cass` varchar(8) DEFAULT NULL,
  `py_addr_zip_rt` varchar(4) DEFAULT NULL,
  `py_confidential_flag` varchar(1) DEFAULT NULL,
  `py_address_suppress_Flag` varchar(1) DEFAULT NULL,
  `filler2` varchar(10) DEFAULT NULL,
  `py_addr_ml_deliverable` varchar(1) DEFAULT NULL,
  `filler3` varchar(10) DEFAULT NULL,
  `situs_street_prefx` varchar(10) DEFAULT NULL,
  `situs_street` varchar(50) DEFAULT NULL,
  `situs_street_suffix` varchar(10) DEFAULT NULL,
  `situs_city` varchar(30) DEFAULT NULL,
  `situs_zip` int(5) DEFAULT NULL,
  `legal_desc` varchar(510) DEFAULT NULL,
  `legal_desc2` varchar(510) DEFAULT NULL,
  `legal_acreage` int(1) DEFAULT NULL,
  `abs_subdv_cd` varchar(20) DEFAULT NULL,
  `hood_cd` varchar(20) DEFAULT NULL,
  `block` varchar(100) DEFAULT NULL,
  `tract_or_lot` varchar(100) DEFAULT NULL,
  `land_hstd_val` int(10) DEFAULT NULL,
  `land_non_hstd_val` int(6) DEFAULT NULL,
  `imprv_hstd_val` int(1) DEFAULT NULL,
  `imprv_non_hstd_val` int(5) DEFAULT NULL,
  `ag_use_val` int(1) DEFAULT NULL,
  `ag_market` int(1) DEFAULT NULL,
  `timber_use` int(1) DEFAULT NULL,
  `timber_market` int(1) DEFAULT NULL,
  `appraised_val` int(6) DEFAULT NULL,
  `ten_percent_cap` int(1) DEFAULT NULL,
  `assessed_val` int(6) DEFAULT NULL,
  `filler4` varchar(10) DEFAULT NULL,
  `arb_protest_flag` varchar(1) DEFAULT NULL,
  `filler5` varchar(10) DEFAULT NULL,
  `deed_book_id` int(5) DEFAULT NULL,
  `deed_book_page` int(5) DEFAULT NULL,
  `deed_dt` varchar(17) DEFAULT NULL,
  `mortgage_co_id` int(1) DEFAULT NULL,
  `mortage_co_name` varchar(140) DEFAULT NULL,
  `mortgage_acct_id` varchar(100) DEFAULT NULL,
  `jan1_owner_id` int(1) DEFAULT NULL,
  `jan1_owner_name` varchar(140) DEFAULT NULL,
  `jan1_addr_line1` varchar(120) DEFAULT NULL,
  `jan1_addr_line2` varchar(120) DEFAULT NULL,
  `jan1_addr_line3` varchar(120) DEFAULT NULL,
  `jan1_addr_city` varchar(100) DEFAULT NULL,
  `jan1_addr_state` varchar(100) DEFAULT NULL,
  `jan1_addr_country` varchar(100) DEFAULT NULL,
  `jan1_addr_zip` varchar(10) DEFAULT NULL,
  `jan1_addr_zip_cass` varchar(8) DEFAULT NULL,
  `jan1_addr_zip_rt` varchar(4) DEFAULT NULL,
  `jan1_confidential_flag` varchar(10) DEFAULT NULL,
  `jan1_address_suppress_Flag` varchar(1) DEFAULT NULL,
  `filler6` varchar(10) DEFAULT NULL,
  `jan1_ml_deliverable` varchar(10) DEFAULT NULL,
  `hs_exempt` varchar(1) DEFAULT NULL,
  `ov65_exempt` varchar(1) DEFAULT NULL,
  `ov65_prorate_begin` varchar(10) DEFAULT NULL,
  `ov65_prorate_end` varchar(10) DEFAULT NULL,
  `ov65s_exempt` varchar(1) DEFAULT NULL,
  `dp_exempt` varchar(1) DEFAULT NULL,
  `dv1_exempt` varchar(1) DEFAULT NULL,
  `dv1s_exempt` varchar(1) DEFAULT NULL,
  `dv2_exempt` varchar(1) DEFAULT NULL,
  `dv2s_exempt` varchar(1) DEFAULT NULL,
  `dv3_exempt` varchar(1) DEFAULT NULL,
  `dv3s_exempt` varchar(1) DEFAULT NULL,
  `dv4_exempt` varchar(1) DEFAULT NULL,
  `dv4s_exempt` varchar(1) DEFAULT NULL,
  `ex_exempt` varchar(1) DEFAULT NULL,
  `ex_prorate_begin` varchar(10) DEFAULT NULL,
  `ex_prorate_end` varchar(10) DEFAULT NULL,
  `lve_exempt` varchar(1) DEFAULT NULL,
  `ab_exempt` varchar(1) DEFAULT NULL,
  `en_exempt` varchar(1) DEFAULT NULL,
  `fr_exempt` varchar(1) DEFAULT NULL,
  `ht_exempt` varchar(1) DEFAULT NULL,
  `pro_exempt` varchar(1) DEFAULT NULL,
  `pc_exempt` varchar(1) DEFAULT NULL,
  `so_exempt` varchar(1) DEFAULT NULL,
  `ex366_exempt` varchar(1) DEFAULT NULL,
  `ch_exempt` varchar(1) DEFAULT NULL,
  `imprv_state_cd` varchar(2) DEFAULT NULL,
  `land_state_cd` varchar(2) DEFAULT NULL,
  `personal_state_cd` varchar(10) DEFAULT NULL,
  `mineral_state_cd` varchar(10) DEFAULT NULL,
  `land_acres` int(4) DEFAULT NULL,
  `entity_agent_id` int(1) DEFAULT NULL,
  `entity_agent_name` varchar(140) DEFAULT NULL,
  `entity_agent_addr_line1` varchar(120) DEFAULT NULL,
  `entity_agent_addr_line2` varchar(120) DEFAULT NULL,
  `entity_agent_addr_line3` varchar(120) DEFAULT NULL,
  `entity_agent_city` varchar(100) DEFAULT NULL,
  `entity_agent_state` varchar(100) DEFAULT NULL,
  `entity_agent_country` varchar(100) DEFAULT NULL,
  `entity_agent_zip` varchar(10) DEFAULT NULL,
  `entity_agent_zip_cass` varchar(8) DEFAULT NULL,
  `entity_agent_zip_rt` varchar(4) DEFAULT NULL,
  `filler7` varchar(10) DEFAULT NULL,
  `ca_agent_id` int(1) DEFAULT NULL,
  `ca_agent_name` varchar(140) DEFAULT NULL,
  `ca_agent_addr_line1` varchar(120) DEFAULT NULL,
  `ca_agent_addr_line2` varchar(120) DEFAULT NULL,
  `ca_agent_addr_line3` varchar(120) DEFAULT NULL,
  `ca_agent_city` varchar(100) DEFAULT NULL,
  `ca_agent_state` varchar(100) DEFAULT NULL,
  `ca_agent_country` varchar(100) DEFAULT NULL,
  `ca_agent_zip` varchar(10) DEFAULT NULL,
  `ca_agent_zip_cass` varchar(8) DEFAULT NULL,
  `ca_agent_zip_rt` varchar(4) DEFAULT NULL,
  `filler8` varchar(10) DEFAULT NULL,
  `arb_agent_id` int(1) DEFAULT NULL,
  `arb_agent_name` varchar(140) DEFAULT NULL,
  `arb_agent_addr_line1` varchar(120) DEFAULT NULL,
  `arb_agent_addr_line2` varchar(120) DEFAULT NULL,
  `arb_agent_addr_line3` varchar(120) DEFAULT NULL,
  `arb_agent_city` varchar(100) DEFAULT NULL,
  `arb_agent_state` varchar(100) DEFAULT NULL,
  `arb_agent_country` varchar(100) DEFAULT NULL,
  `arb_agent_zip` varchar(10) DEFAULT NULL,
  `arb_agent_zip_cass` varchar(8) DEFAULT NULL,
  `arb_agent_zip_rt` varchar(4) DEFAULT NULL,
  `filler9` varchar(10) DEFAULT NULL,
  `mineral_type_of_int` varchar(10) DEFAULT NULL,
  `mineral_int_pct` int(15) DEFAULT NULL,
  `productivity_use_code` varchar(10) DEFAULT NULL,
  `filler10` varchar(10) DEFAULT NULL,
  `timber_78_market` int(1) DEFAULT NULL,
  `ag_late_loss` int(1) DEFAULT NULL,
  `late_freeport_penalty` int(1) DEFAULT NULL,
  `filler11` varchar(10) DEFAULT NULL,
  `filler12` varchar(10) DEFAULT NULL,
  `filler13` varchar(10) DEFAULT NULL,
  `dba` varchar(80) DEFAULT NULL,
  `filler14` varchar(10) DEFAULT NULL,
  `market_value` int(6) DEFAULT NULL,
  `mh_label` varchar(40) DEFAULT NULL,
  `mh_serial` varchar(40) DEFAULT NULL,
  `mh_model` varchar(40) DEFAULT NULL,
  `filler15` varchar(10) DEFAULT NULL,
  `filler16` varchar(10) DEFAULT NULL,
  `filler17` varchar(10) DEFAULT NULL,
  `ov65_deferral_date` varchar(10) DEFAULT NULL,
  `dp_deferral_date` varchar(10) DEFAULT NULL,
  `ref_id1` varchar(10) DEFAULT NULL,
  `ref_id2` int(14) DEFAULT NULL,
  `situs_num` int(4) DEFAULT NULL,
  `situs_unit` varchar(10) DEFAULT NULL,
  `appr_owner_id` int(6) DEFAULT NULL,
  `appr_owner_name` varchar(140) DEFAULT NULL,
  `appr_addr_line1` varchar(120) DEFAULT NULL,
  `appr_addr_line2` varchar(120) DEFAULT NULL,
  `appr_addr_line3` varchar(120) DEFAULT NULL,
  `appr_addr_city` varchar(100) DEFAULT NULL,
  `appr_addr_state` varchar(100) DEFAULT NULL,
  `appr_addr_country` varchar(100) DEFAULT NULL,
  `appr_addr_zip` int(10) DEFAULT NULL,
  `appr_addr_zip_cass` varchar(8) DEFAULT NULL,
  `appr_addr_zip_rt` varchar(4) DEFAULT NULL,
  `appr_ml_deliverable` varchar(1) DEFAULT NULL,
  `appr_confidential_flag` varchar(1) DEFAULT NULL,
  `appr_address_suppress_Flag` varchar(1) DEFAULT NULL,
  `appr_confidential_name` varchar(10) DEFAULT NULL,
  `py_confidential_name` varchar(10) DEFAULT NULL,
  `jan1_confidential_name` varchar(10) DEFAULT NULL,
  `sic_code` varchar(10) DEFAULT NULL,
  `rendition_filed` varchar(1) DEFAULT NULL,
  `rendition_date` varchar(10) DEFAULT NULL,
  `rendition_penalty` int(1) DEFAULT NULL,
  `rendition_penalty_date_paid` varchar(10) DEFAULT NULL,
  `rendition_fraud_penalty` int(1) DEFAULT NULL,
  `rendition_fraud_penalty_date_paid` varchar(10) DEFAULT NULL,
  `filler18` varchar(10) DEFAULT NULL,
  `entities` varchar(280) DEFAULT NULL,
  `eco_exempt` varchar(1) DEFAULT NULL,
  `dataset_id` int(4) DEFAULT NULL,
  `deed_num` varchar(100) DEFAULT NULL,
  `chodo_exempt` varchar(1) DEFAULT NULL,
  `local_option_pct_only_flag_hs` varchar(1) DEFAULT NULL,
  `local_option_pct_only_flag_ov65` varchar(1) DEFAULT NULL,
  `local_option_pct_only_flag_ov65s` varchar(1) DEFAULT NULL,
  `local_option_pct_only_flag_dp` varchar(1) DEFAULT NULL,
  `freeze_only_flag_ov65` varchar(1) DEFAULT NULL,
  `freeze_only_flag_ov65s` varchar(1) DEFAULT NULL,
  `freeze_only_flag_dp` varchar(1) DEFAULT NULL,
  `apply_percent_exemption_flag` varchar(1) DEFAULT NULL,
  `exemption_percentage` int(1) DEFAULT NULL,
  `vit_flag` varchar(1) DEFAULT NULL,
  `lih_exempt` varchar(1) DEFAULT NULL,
  `git_exempt` varchar(1) DEFAULT NULL,
  `dps_exempt` varchar(100) DEFAULT NULL,
  `dps_deferral_date` varchar(100) DEFAULT NULL,
  `local_option_pct_only_flag_dps` varchar(100) DEFAULT NULL,
  `freeze_only_flag_dps` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PROSPECT_LIST`
--

CREATE TABLE IF NOT EXISTS `PROSPECT_LIST` (
  `prop_id` int(11) NOT NULL,
  `prop_addr` varchar(100) NOT NULL,
  `prop_owner` varchar(100) NOT NULL,
  `computed_date` datetime NOT NULL,
  `market_val` int(11) NOT NULL,
  `mean_val` int(11) NOT NULL,
  `diff` float NOT NULL,
  `comps_csv` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PROSPECT_LIST_NONE`
--

CREATE TABLE IF NOT EXISTS `PROSPECT_LIST_NONE` (
  `prop_id` int(11) NOT NULL,
  `computed_date` datetime NOT NULL,
  `market_val` int(11) NOT NULL,
  `mean_val` int(11) NOT NULL,
  `comps_csv` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SALES_MLS_MERGED`
--

CREATE TABLE IF NOT EXISTS `SALES_MLS_MERGED` (
  `prop_id` int(12) NOT NULL,
  `sale_price` int(14) NOT NULL,
  `sale_date` date NOT NULL,
  `source` set('MLS','SPECIAL') NOT NULL,
  `sale_type` varchar(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SPECIAL_IMP`
--

CREATE TABLE IF NOT EXISTS `SPECIAL_IMP` (
  `prop_id` int(12) DEFAULT NULL,
  `prop_val_yr` int(4) DEFAULT NULL,
  `supp_num` int(4) DEFAULT NULL,
  `imprv_id` int(12) DEFAULT NULL,
  `improv_type_code` varchar(5) NOT NULL,
  `state_code` varchar(5) DEFAULT NULL,
  `homesite` varchar(1) DEFAULT NULL,
  `yr_built` int(4) DEFAULT NULL,
  `descript` varchar(255) DEFAULT NULL,
  `num_imprv` int(4) DEFAULT NULL,
  `comm` varchar(1000) DEFAULT NULL,
  `num_stories` varchar(5) DEFAULT NULL,
  `base_deprec_perc` float DEFAULT NULL,
  `base_deprec_comm` varchar(255) DEFAULT NULL,
  `phy_perc` float DEFAULT NULL,
  `phy_comm` varchar(255) DEFAULT NULL,
  `func_perc` float DEFAULT NULL,
  `func_comm` varchar(255) DEFAULT NULL,
  `eco_perc` float DEFAULT NULL,
  `eco_comm` varchar(255) DEFAULT NULL,
  `perc_complete` float DEFAULT NULL,
  `perc_complete_comm` varchar(255) DEFAULT NULL,
  `tot_det_val` int(14) DEFAULT NULL,
  `imprv_val` int(14) DEFAULT NULL,
  `imprv_val_src` varchar(1) DEFAULT NULL,
  `entity_perc` varchar(100) DEFAULT NULL,
  `adjust_perc` varchar(100) DEFAULT NULL,
  `det_id` int(12) DEFAULT NULL,
  `det_num_units` int(4) DEFAULT NULL,
  `det_num_stories` int(4) DEFAULT NULL,
  `det_class_code` varchar(10) DEFAULT NULL,
  `det_method` varchar(5) DEFAULT NULL,
  `det_subclass` varchar(10) DEFAULT NULL,
  `det_unitprice` float DEFAULT NULL,
  `det_add_fact` float DEFAULT NULL,
  `det_area` float DEFAULT NULL,
  `det_base_deprec_perc` float DEFAULT NULL,
  `det_base_deprec_comm` varchar(255) DEFAULT NULL,
  `det_phy_perc` float DEFAULT NULL,
  `det_phy_comm` varchar(255) DEFAULT NULL,
  `det_func_perc` float DEFAULT NULL,
  `det_func_comm` varchar(255) DEFAULT NULL,
  `det_eco_perc` float DEFAULT NULL,
  `det_eco_comm` varchar(255) DEFAULT NULL,
  `det_perc_complete` float DEFAULT NULL,
  `det_perc_complete_comm` varchar(255) DEFAULT NULL,
  `det_val` int(14) DEFAULT NULL,
  `det_val_src` varchar(1) DEFAULT NULL,
  `det_use_unit_price` varchar(1) DEFAULT NULL,
  `det_stories_mult` varchar(1) DEFAULT NULL,
  `det_calc_val` int(14) DEFAULT NULL,
  `det_cond_code` varchar(5) DEFAULT NULL,
  `det_yr_built` int(4) DEFAULT NULL,
  `det_eff_yr_built` int(4) DEFAULT NULL,
  `det_age` int(4) DEFAULT NULL,
  `det_adj_perc` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SPECIAL_PROPDATA`
--

CREATE TABLE IF NOT EXISTS `SPECIAL_PROPDATA` (
  `prop_id` int(12) NOT NULL DEFAULT '0',
  `prop_val_yr` int(4) DEFAULT NULL,
  `yr_built` int(4) DEFAULT NULL,
  `liv_area` int(14) DEFAULT NULL,
  `land_sqft` float DEFAULT NULL,
  `land_acre` float DEFAULT NULL,
  `region` varchar(5) DEFAULT NULL,
  `hood` varchar(10) DEFAULT NULL,
  `map_id` varchar(20) DEFAULT NULL,
  `land_num_lots` int(12) DEFAULT NULL,
  `eff_yr_built` int(4) DEFAULT NULL,
  `imprv_num_units` int(12) DEFAULT NULL,
  `last_appr` varchar(40) DEFAULT NULL,
  `next_appr` varchar(40) DEFAULT NULL,
  `land_appr` varchar(40) DEFAULT NULL,
  `val_appr` varchar(40) DEFAULT NULL,
  `hood_appr` varchar(40) DEFAULT NULL,
  `subd_appr` varchar(40) DEFAULT NULL,
  `cat_appr` varchar(40) DEFAULT NULL,
  `next_appr_reason` varchar(500) DEFAULT NULL,
  `prop_comm` varchar(3000) DEFAULT NULL,
  `prop_rem` varchar(3000) DEFAULT NULL,
  `value_meth_cd` varchar(5) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SPECIAL_SALE_EX_CONF`
--

CREATE TABLE IF NOT EXISTS `SPECIAL_SALE_EX_CONF` (
  `prop_id` int(12) NOT NULL,
  `sale_id` int(12) NOT NULL,
  `UNKNOWN` varchar(5) NOT NULL,
  `buyer_name` varchar(70) NOT NULL,
  `seller_name` varchar(70) NOT NULL,
  `deed_type` varchar(10) NOT NULL,
  `deed_vol` varchar(20) NOT NULL,
  `deed_date` varchar(10) NOT NULL,
  `deed_page` varchar(20) NOT NULL,
  `deed_num` varchar(50) NOT NULL,
  `rec_date` varchar(10) NOT NULL,
  `consideration` varchar(20) NOT NULL,
  `comment` varchar(500) NOT NULL,
  `buyer_id` int(12) NOT NULL,
  `seller_id` int(12) NOT NULL,
  `sale_price` int(14) NOT NULL,
  `sale_date` varchar(10) NOT NULL,
  `adj_code` varchar(5) NOT NULL,
  `adj_perc` float NOT NULL,
  `adj_amt` int(14) NOT NULL,
  `adj_reason` varchar(50) NOT NULL,
  `conf_by` varchar(30) NOT NULL,
  `conf_date` varchar(10) NOT NULL,
  `conf_src` varchar(30) NOT NULL,
  `conf_sale_price` int(18) NOT NULL,
  `buyer_conf_lvl` varchar(5) NOT NULL,
  `seller_conf_lvl` varchar(5) NOT NULL,
  `conf_comment` varchar(500) NOT NULL,
  `finance_code` varchar(5) NOT NULL,
  `amt_financed` int(18) NOT NULL,
  `int_rate` float NOT NULL,
  `finance_yrs` float NOT NULL,
  `sec_amt_financed` int(18) NOT NULL,
  `sec_int_rate` float NOT NULL,
  `sec_finance_years` float NOT NULL,
  `amt_down` int(18) NOT NULL,
  `finance_comment` varchar(50) NOT NULL,
  `sale_type` varchar(5) NOT NULL,
  `sale_ratio_type` varchar(5) NOT NULL,
  `sale_state_code` varchar(5) NOT NULL,
  `school_entity` int(10) NOT NULL,
  `city_entity` varchar(10) NOT NULL,
  `sale_class` varchar(10) NOT NULL,
  `sale_sub_class` varchar(10) NOT NULL,
  `sale_comment` varchar(500) NOT NULL,
  `sale_realtor` varchar(30) NOT NULL,
  `sale_yr_built` int(4) NOT NULL,
  `sale_imprv_unit_price` float NOT NULL,
  `sale_living_area` int(14) NOT NULL,
  `sale_land_acres` float NOT NULL,
  `sale_land_sqft` float NOT NULL,
  `sale_land_front_foot` float NOT NULL,
  `sale_land_depth` float NOT NULL,
  `sale_land_unit_price` float NOT NULL,
  `sale_land_type_code` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `BATCH_PROP`
--
ALTER TABLE `BATCH_PROP`
  ADD PRIMARY KEY (`prop`);

--
-- Indexes for table `BATCH_PROP_BKUP`
--
ALTER TABLE `BATCH_PROP_BKUP`
  ADD PRIMARY KEY (`prop`);

--
-- Indexes for table `BATCH_PROP_SETTINGS`
--
ALTER TABLE `BATCH_PROP_SETTINGS`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `IMP_DET`
--
ALTER TABLE `IMP_DET`
  ADD KEY `prop_id` (`prop_id`);

--
-- Indexes for table `MLS_SALES`
--
ALTER TABLE `MLS_SALES`
  ADD KEY `prop_id` (`prop_id`), ADD KEY `sale_date` (`sale_date`), ADD KEY `prop_id_2` (`prop_id`);

--
-- Indexes for table `MLS_SALES_SINGLEYEAR`
--
ALTER TABLE `MLS_SALES_SINGLEYEAR`
  ADD KEY `prop_id` (`prop_id`), ADD KEY `sale_date` (`sale_date`), ADD KEY `prop_id_2` (`prop_id`);

--
-- Indexes for table `PROP`
--
ALTER TABLE `PROP`
  ADD PRIMARY KEY (`prop_id`), ADD KEY `PropHood` (`hood_cd`), ADD KEY `prop_id` (`prop_id`), ADD KEY `prop_id_2` (`prop_id`);

--
-- Indexes for table `PROSPECT_LIST`
--
ALTER TABLE `PROSPECT_LIST`
  ADD UNIQUE KEY `prop_id` (`prop_id`);

--
-- Indexes for table `PROSPECT_LIST_NONE`
--
ALTER TABLE `PROSPECT_LIST_NONE`
  ADD UNIQUE KEY `prop_id` (`prop_id`);

--
-- Indexes for table `SALES_MLS_MERGED`
--
ALTER TABLE `SALES_MLS_MERGED`
  ADD UNIQUE KEY `UQ_propid_date` (`prop_id`,`sale_date`), ADD UNIQUE KEY `prop_id_2` (`prop_id`,`sale_price`), ADD KEY `prop_id` (`prop_id`);

--
-- Indexes for table `SPECIAL_IMP`
--
ALTER TABLE `SPECIAL_IMP`
  ADD KEY `prop_id` (`prop_id`);

--
-- Indexes for table `SPECIAL_PROPDATA`
--
ALTER TABLE `SPECIAL_PROPDATA`
  ADD PRIMARY KEY (`prop_id`);

--
-- Indexes for table `SPECIAL_SALE_EX_CONF`
--
ALTER TABLE `SPECIAL_SALE_EX_CONF`
  ADD KEY `prop_id` (`prop_id`), ADD KEY `prop_id_2` (`prop_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `BATCH_PROP_SETTINGS`
--
ALTER TABLE `BATCH_PROP_SETTINGS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
