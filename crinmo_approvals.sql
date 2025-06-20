-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: crinmo_approvals
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `AdminId` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EmailAdmin` varchar(200) NOT NULL,
  `DepartmentID` int NOT NULL,
  `FullName` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_admin_to_department` (`DepartmentID`),
  CONSTRAINT `fk_admin_to_department` FOREIGN KEY (`DepartmentID`) REFERENCES `tbldepartments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','c51825ec93ebed0c2f5af7ea36f84d29','2025-03-05 21:35:31','juanpablo@gmail.com',1,'Juan Pablo'),(2,'1036251824','$2y$10$6OZuC3tVw2I83bHC6wQepukxxnP1vqTCIWG8LWd14wKvFsnA5v7Zm','2025-03-05 21:22:10','gerleytaborda645@gmail.com',2,'Deiby Taborda'),(4,'1076543224','$2y$10$KvWomptSEkPz5bYUajlfp.eGr10NuWWz2kFQ5K.VG0iTmKfw4468.','2025-02-25 19:38:21','susana@gmail.com',4,'Susana');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbldepartments`
--

DROP TABLE IF EXISTS `tbldepartments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbldepartments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `DepartmentName` varchar(150) DEFAULT NULL,
  `DepartmentShortName` varchar(100) NOT NULL,
  `DepartmentCode` varchar(50) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbldepartments`
--

LOCK TABLES `tbldepartments` WRITE;
/*!40000 ALTER TABLE `tbldepartments` DISABLE KEYS */;
INSERT INTO `tbldepartments` VALUES (1,'Seguridad y Salud en el Trabajo','SST','HR001','2017-11-01 07:16:25'),(2,'Sistemas','TIC\'S','IT001','2017-11-01 07:19:37'),(3,'Recibimientos e Inventarios','Recibimientos e Inventarios','OP1','2017-12-02 21:28:56'),(4,'Gestión Humana','GH','003223','2018-06-26 17:02:30'),(5,'Contabilidad','Contabilidad','032034','2018-06-26 17:15:19');
/*!40000 ALTER TABLE `tbldepartments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblemployees`
--

DROP TABLE IF EXISTS `tblemployees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblemployees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `EmpId` varchar(100) NOT NULL,
  `FirstName` varchar(150) NOT NULL,
  `LastName` varchar(150) NOT NULL,
  `EmailId` varchar(200) NOT NULL,
  `Password` varchar(180) NOT NULL,
  `Gender` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `Dob` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `Address` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `City` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `Country` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `Phonenumber` char(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `Status` int NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DepartmentID` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_employees_to_department` (`DepartmentID`),
  CONSTRAINT `fk_employees_to_department` FOREIGN KEY (`DepartmentID`) REFERENCES `tbldepartments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblemployees`
--

LOCK TABLES `tblemployees` WRITE;
/*!40000 ALTER TABLE `tblemployees` DISABLE KEYS */;
INSERT INTO `tblemployees` VALUES (1,'1036251824','Deiby','Taborda','gerleytaborda645@gmail.com','$2y$10$tN.lkf6sQy04ILEDyxMUbO6HS5hsXOICO9P2vY40Y4cN1JT8/s59.','Masculino','24 April, 2005','Cr66 #69-35','Bello','Colombia','3016479538',1,'2025-03-13 01:27:05',2);
/*!40000 ALTER TABLE `tblemployees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblleaves`
--

DROP TABLE IF EXISTS `tblleaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblleaves` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ToDate` datetime NOT NULL,
  `FromDate` datetime NOT NULL,
  `Description` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `AdminRemark` mediumtext,
  `AdminRemarkDate` varchar(120) DEFAULT NULL,
  `Status` int NOT NULL,
  `IsRead` int NOT NULL,
  `empid` int NOT NULL,
  `LeaveTypeID` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `UserEmail` (`empid`),
  KEY `fk_leaves_to_leavetype` (`LeaveTypeID`),
  CONSTRAINT `fk_leaves_to_employee` FOREIGN KEY (`empid`) REFERENCES `tblemployees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_leaves_to_leavetype` FOREIGN KEY (`LeaveTypeID`) REFERENCES `tblleavetype` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblleaves`
--

LOCK TABLES `tblleaves` WRITE;
/*!40000 ALTER TABLE `tblleaves` DISABLE KEYS */;
/*!40000 ALTER TABLE `tblleaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblleavetype`
--

DROP TABLE IF EXISTS `tblleavetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblleavetype` (
  `id` int NOT NULL AUTO_INCREMENT,
  `LeaveType` varchar(200) DEFAULT NULL,
  `Description` mediumtext,
  `CreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblleavetype`
--

LOCK TABLES `tblleavetype` WRITE;
/*!40000 ALTER TABLE `tblleavetype` DISABLE KEYS */;
INSERT INTO `tblleavetype` VALUES (1,'Licencia ocasional','Licencia ocasional\r\n','2017-11-01 12:07:56'),(2,'Licencia Médica','Licencia Médica','2017-11-06 13:16:09'),(3,'Vacaciones','Vacaciones','2017-11-06 13:16:38'),(4,'Cita Médica','Cita Médica','2018-06-26 17:03:29'),(5,'Estudio','Estudio','2018-06-26 17:18:05');
/*!40000 ALTER TABLE `tblleavetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'crinmo_approvals'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-13  6:29:09
