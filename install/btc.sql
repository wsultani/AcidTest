-- MySQL dump 10.13  Distrib 5.1.44, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: btc_db
-- ------------------------------------------------------
-- Server version	5.1.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `btc_bivio_inventory`
--

DROP TABLE IF EXISTS `btc_bivio_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_bivio_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Testbed` varchar(45) NOT NULL DEFAULT '',
  `Platform` varchar(45) DEFAULT NULL,
  `Name` varchar(45) DEFAULT NULL,
  `IPAddress` varchar(45) DEFAULT NULL,
  `Login` varchar(45) DEFAULT NULL,
  `Password` varchar(45) DEFAULT NULL,
  `Console` varchar(45) DEFAULT NULL,
  `Connection` varchar(45) DEFAULT NULL,
  `Class` varchar(45) DEFAULT NULL,
  `Port` varchar(45) DEFAULT NULL,
  `RemotePower` varchar(45) DEFAULT NULL,
  `Outlet` varchar(45) DEFAULT NULL,
  `Slot0` varchar(45) DEFAULT NULL,
  `Slot1` varchar(45) DEFAULT NULL,
  `RPLogin` varchar(45) DEFAULT NULL,
  `RPPassword` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `btc_builds`
--

DROP TABLE IF EXISTS `btc_builds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_builds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Version` varchar(65) DEFAULT NULL,
  `PassRate` varchar(45) DEFAULT '0',
  `Log` varchar(65) DEFAULT NULL,
  `Date` datetime DEFAULT '0000-00-00 00:00:00',
  `Total` int(11) DEFAULT '0',
  `PassCount` int(11) DEFAULT '0',
  `FailCount` int(11) DEFAULT '0',
  `DefectCount` int(11) DEFAULT '0',
  `Platform` varchar(45) DEFAULT NULL,
  `Testbed` varchar(45) DEFAULT NULL,
  `FPGA` varchar(45) DEFAULT NULL,
  `ROM` varchar(20) DEFAULT NULL,
  `Comments` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `btc_pc_inventory`
--

DROP TABLE IF EXISTS `btc_pc_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_pc_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Testbed` varchar(45) NOT NULL DEFAULT '',
  `OS` varchar(45) DEFAULT NULL,
  `Name` varchar(45) DEFAULT NULL,
  `IPAddress` varchar(45) DEFAULT NULL,
  `Login` varchar(45) DEFAULT NULL,
  `Password` varchar(45) DEFAULT NULL,
  `Connection` varchar(45) DEFAULT NULL,
  `Class` varchar(45) DEFAULT NULL,
  `Interface` varchar(45) DEFAULT NULL,
  `InterfaceIP` varchar(45) DEFAULT NULL,
  `RemotePower` varchar(45) DEFAULT NULL,
  `Outlet` varchar(45) DEFAULT NULL,
  `RPLogin` varchar(45) DEFAULT NULL,
  `RPPassword` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `btc_perf`
--

DROP TABLE IF EXISTS `btc_perf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_perf` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL DEFAULT '',
  `Build` varchar(45) NOT NULL DEFAULT '',
  `CFG` varchar(45) NOT NULL DEFAULT '',
  `Testbed` varchar(45) DEFAULT NULL,
  `FPGA` varchar(45) DEFAULT NULL,
  `ROM` varchar(20) DEFAULT NULL,
  `Platform` varchar(45) NOT NULL DEFAULT '',
  `Sbypass` varchar(45) NOT NULL DEFAULT '',
  `Speed` varchar(45) NOT NULL DEFAULT '',
  `Jumbo` varchar(45) NOT NULL DEFAULT '',
  `Port` varchar(45) NOT NULL DEFAULT '',
  `Traffic` varchar(60) NOT NULL DEFAULT '',
  `Qos` varchar(45) NOT NULL DEFAULT '',
  `CPU` int(10) unsigned NOT NULL DEFAULT '0',
  `64` float DEFAULT NULL,
  `128` float DEFAULT NULL,
  `200` float DEFAULT NULL,
  `256` float DEFAULT NULL,
  `350` float DEFAULT NULL,
  `384` float DEFAULT NULL,
  `440` float DEFAULT NULL,
  `448` float DEFAULT NULL,
  `500` float DEFAULT NULL,
  `512` float DEFAULT NULL,
  `1024` float DEFAULT NULL,
  `1280` float DEFAULT NULL,
  `1500` float DEFAULT NULL,
  `1518` float DEFAULT NULL,
  `2000` float DEFAULT NULL,
  `2500` float DEFAULT NULL,
  `3000` float DEFAULT NULL,
  `3500` float DEFAULT NULL,
  `4000` float DEFAULT NULL,
  `4500` float DEFAULT NULL,
  `5000` float DEFAULT NULL,
  `5500` float DEFAULT NULL,
  `6000` float DEFAULT NULL,
  `6500` float DEFAULT NULL,
  `7000` float DEFAULT NULL,
  `7500` float DEFAULT NULL,
  `8000` float DEFAULT NULL,
  `8500` float DEFAULT NULL,
  `9000` float DEFAULT NULL,
  `9500` float DEFAULT NULL,
  `9600` float DEFAULT NULL,
  `1522` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1039 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `btc_procs`
--

DROP TABLE IF EXISTS `btc_procs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_procs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL,
  `Description` text,
  `Arguments` text,
  `Return` varchar(50) DEFAULT NULL,
  `Suite` varchar(100) DEFAULT NULL,
  `Comments` text,
  `Author` varchar(50) DEFAULT NULL,
  `Required` varchar(50) DEFAULT NULL,
  `Syntax` text,
  `Created` date DEFAULT NULL,
  `EOL` date DEFAULT NULL,
  `Count` int(11) DEFAULT NULL,
  `LastUsed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `btc_scheduler`
--

DROP TABLE IF EXISTS `btc_scheduler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_scheduler` (
  `Job` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Script` text NOT NULL,
  `Testbed` varchar(45) NOT NULL DEFAULT '',
  `Status` varchar(45) NOT NULL DEFAULT '',
  `Priority` varchar(45) NOT NULL DEFAULT '',
  `Log` varchar(155) NOT NULL DEFAULT '',
  `Start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `PID` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`Job`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `btc_smb_inventory`
--

DROP TABLE IF EXISTS `btc_smb_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_smb_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Testbed` varchar(45) NOT NULL DEFAULT '',
  `Name` varchar(45) DEFAULT NULL,
  `IPAddress` varchar(45) NOT NULL DEFAULT '',
  `SMBSlot` varchar(45) NOT NULL DEFAULT '',
  `BVSlot` varchar(45) NOT NULL DEFAULT '',
  `Class` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `btc_sw_inventory`
--

DROP TABLE IF EXISTS `btc_sw_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_sw_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Testbed` varchar(45) NOT NULL DEFAULT '',
  `Type` varchar(45) DEFAULT NULL,
  `Name` varchar(45) DEFAULT NULL,
  `IPAddress` varchar(45) DEFAULT NULL,
  `Login` varchar(45) DEFAULT NULL,
  `Password` varchar(45) DEFAULT NULL,
  `Console` varchar(45) DEFAULT NULL,
  `Connection` varchar(45) DEFAULT NULL,
  `Class` varchar(45) DEFAULT NULL,
  `Port` varchar(45) DEFAULT NULL,
  `RemotePower` varchar(45) DEFAULT NULL,
  `Outlet` varchar(45) DEFAULT NULL,
  `RPLogin` varchar(45) DEFAULT NULL,
  `RPPassword` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `btc_testbeds`
--

DROP TABLE IF EXISTS `btc_testbeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_testbeds` (
  `Testbed` varchar(45) DEFAULT NULL,
  `DevName` varchar(45) NOT NULL DEFAULT '',
  `DevClass` varchar(45) DEFAULT NULL,
  `DevNum` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `btc_tests`
--

DROP TABLE IF EXISTS `btc_tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `btc_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) DEFAULT NULL,
  `Description` text,
  `Suite` varchar(45) DEFAULT NULL,
  `Comments` text,
  `Author` varchar(45) DEFAULT NULL,
  `Required` varchar(100) DEFAULT NULL,
  `Created` date DEFAULT NULL,
  `EOL` date DEFAULT NULL,
  `Count` int(11) DEFAULT NULL,
  `AvgTime` int(11) DEFAULT NULL,
  `Result` varchar(20) DEFAULT NULL,
  `Failure` text,
  `DefectID` varchar(20) DEFAULT NULL,
  `LastUsed` datetime DEFAULT NULL,
  `Version` varchar(45) DEFAULT NULL,
  `PassCount` int(11) DEFAULT NULL,
  `FailCount` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-04-06 15:56:52
