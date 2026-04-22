-- MariaDB dump 10.19  Distrib 10.11.3-MariaDB, for debian-linux-gnueabihf (armv7l)
--
-- Host: localhost    Database: NETz
-- ------------------------------------------------------
-- Server version	10.11.3-MariaDB-1+rpi1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `NETz`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `NETz` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;


--
-- Table structure for table `ACCESSLOG`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `ACCESSLOG` (
  `USERS_IP` varchar(50) DEFAULT NULL,
  `USERNAME` varchar(255) NOT NULL,
  `ACCESSLEVEL` int(11) NOT NULL DEFAULT 0,
  `ACCESS_DATE_TIME` datetime DEFAULT '0000-00-00 00:00:00',
  `QUERY_STRING` varchar(255) NOT NULL DEFAULT '',
  `PAGE` varchar(255) NOT NULL DEFAULT '',
  KEY `USERNAME` (`USERNAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ALERTEMAILS`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `ALERTEMAILS` (
  `LOCATION` varchar(50) NOT NULL DEFAULT '',
  `EMAIL` varchar(255) NOT NULL DEFAULT '',
  `TYPE` varchar(50) NOT NULL DEFAULT '',
  `NAME` varchar(50) NOT NULL DEFAULT '',
  `PHONE_NUMBER` varchar(25) NOT NULL DEFAULT '',
  `FAX_NUMBER` varchar(25) NOT NULL DEFAULT '',
  KEY `LOCATION` (`LOCATION`),
  KEY `EMAIL` (`EMAIL`),
  KEY `NAME` (`NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ALERTLOGS`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `ALERTLOGS` (
  `SITE_ID` varchar(50) NOT NULL DEFAULT '',
  `SITE_IP` varchar(15) NOT NULL DEFAULT '',
  `CHECK_DATE_TIME` datetime DEFAULT NULL,
  `TYPE` varchar(15) NOT NULL DEFAULT '',
  KEY `SITE_ID` (`SITE_ID`),
  KEY `SITE_IP` (`SITE_IP`),
  KEY `CHECK_DATE_TIME` (`CHECK_DATE_TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ATTACHMENTS`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `ATTACHMENTS` (
  `FILENAME` varchar(255) NOT NULL,
  `DISPLAY_NAME` varchar(255) NOT NULL,
  `DESCRIPTION` varchar(1000) NOT NULL,
  `SITE_ID` varchar(255) NOT NULL,
  `DATE_UPLOADED` datetime NOT NULL,
  `UPLOAD_USER` varchar(255) NOT NULL,
  `MIN_USER_ACCESS_LEVEL` varchar(10) NOT NULL,
  `FILE_TYPE` varchar(50) NOT NULL,
  `UID` varchar(255) NOT NULL,
  UNIQUE KEY `UID` (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `HTTPMONLOGS`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `HTTPMONLOGS` (
  `SITE_ID` varchar(50) NOT NULL DEFAULT '',
  `SITE_IP` varchar(50) NOT NULL DEFAULT '',
  `CHECK_DATE_TIME` datetime DEFAULT NULL,
  `RESPONSE_TIME` int(11) NOT NULL DEFAULT 0,
  `CHECK_STATE` int(11) NOT NULL DEFAULT 0,
  `DATA_LENGTH` int(11) NOT NULL DEFAULT 0,
  `ERROR_STRING` varchar(500) NOT NULL,
  KEY `SITE_ID` (`SITE_ID`),
  KEY `SITE_IP` (`SITE_IP`),
  KEY `CHECK_DATE_TIME` (`CHECK_DATE_TIME`),
  KEY `RESPONSE_TIME` (`RESPONSE_TIME`),
  KEY `CHECK_STATE` (`CHECK_STATE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MONITORINFO`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `MONITORINFO` (
  `SITE_ID` varchar(50) NOT NULL DEFAULT '',
  `MONITOR_ENABLE` tinyint(4) NOT NULL DEFAULT 0,
  `MONITOR_TIMEOUT` varchar(10) NOT NULL DEFAULT '2000',
  `MONITOR_IP_FIELD` varchar(50) NOT NULL DEFAULT '',
  `MONITOR_STATUS` int(11) NOT NULL DEFAULT 0,
  `MONITOR_HTTP_ENABLE` tinyint(4) NOT NULL DEFAULT 0,
  `MONITOR_HTTP_TIMEOUT` varchar(10) NOT NULL DEFAULT '2000',
  `MONITOR_HTTP_IP_FIELD` varchar(50) NOT NULL DEFAULT '',
  `MONITOR_HTTP_PAGE` varchar(255) NOT NULL DEFAULT '',
  `MONITOR_HTTP_CONTENT` varchar(255) NOT NULL DEFAULT '',
  `MONITOR_HTTP_PORT` varchar(5) NOT NULL DEFAULT '',
  `MONITOR_HTTP_SSL` tinyint(4) NOT NULL DEFAULT 0,
  `MONITOR_HTTP_STATUS` int(11) NOT NULL DEFAULT 0,
  `DATE_LAST_ALERT` datetime DEFAULT NULL,
  `ALERT_SENT` tinyint(4) NOT NULL DEFAULT 0,
  `TOTAL_ALERTS_SENT` int(11) NOT NULL DEFAULT 0,
  `VIP_ALERT_ONLINE` tinyint(4) NOT NULL DEFAULT 0,
  `VIP_ALERT_OFFLINE` tinyint(4) NOT NULL DEFAULT 0,
  `SUPPORT_ALERT_ONLINE` tinyint(4) NOT NULL DEFAULT 0,
  `SUPPORT_ALERT_OFFLINE` tinyint(4) NOT NULL DEFAULT 0,
  `MONITOR_ALERT_CYCLES` tinyint(4) DEFAULT 1,
  UNIQUE KEY `SITE_ID` (`SITE_ID`),
  KEY `MONITOR_HTTP_ENABLE` (`MONITOR_HTTP_ENABLE`),
  KEY `MONITOR_ENABLE` (`MONITOR_ENABLE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MONLOGS`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `MONLOGS` (
  `SITE_ID` varchar(50) NOT NULL DEFAULT '',
  `SITE_IP` varchar(50) NOT NULL DEFAULT '',
  `CHECK_DATE_TIME` datetime DEFAULT NULL,
  `RESPONSE_TIME` int(11) NOT NULL DEFAULT 0,
  `CHECK_STATE` int(11) NOT NULL DEFAULT 0,
  KEY `SITE_ID` (`SITE_ID`),
  KEY `SITE_IP` (`SITE_IP`),
  KEY `CHECK_DATE_TIME` (`CHECK_DATE_TIME`),
  KEY `RESPONSE_TIME` (`RESPONSE_TIME`),
  KEY `CHECK_STATE` (`CHECK_STATE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `NAME_MAPING`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `NAME_MAPING` (
  `DB_FIELD_NAME` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `DISPLAY_NAME` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `DISPLAY` tinyint(1) NOT NULL DEFAULT 0,
  `USER_LEVEL` int(11) NOT NULL DEFAULT 0,
  `EDITABLE` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PASSWORD_RESET`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `PASSWORD_RESET` (
  `USERNAME` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `PASSWORD` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `SECRET_STRING` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `REMINDERS`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `REMINDERS` (
  `USERNAME` varchar(255) NOT NULL,
  `REMINDER_NAME` varchar(100) DEFAULT NULL,
  `REMINDER_DATE_TIME` datetime DEFAULT NULL,
  `REMINDER_ADVANCE_TIME` varchar(20) DEFAULT NULL,
  `DESCRIPTION` longtext DEFAULT NULL,
  `REMINDER_ID` varchar(100) NOT NULL,
  UNIQUE KEY `REMINDER_ID` (`REMINDER_ID`),
  KEY `USERNAME` (`USERNAME`),
  KEY `REMINDER_NAME` (`REMINDER_NAME`),
  KEY `REMINDER_DATE_TIME` (`REMINDER_DATE_TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SITEDATA`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `SITEDATA` (
  `SITE_ID` varchar(50) NOT NULL DEFAULT '',
  `SITE_NAME` varchar(100) DEFAULT '',
  `SITE_TYPE` varchar(100) DEFAULT '',
  `GROUP_NAME` varchar(100) DEFAULT '',
  `LAN_IP` varchar(50) DEFAULT '',
  `LAN_GATEWAY` varchar(50) DEFAULT '',
  `LAN_NETMASK` varchar(50) DEFAULT '',
  `REGION` varchar(50) DEFAULT '',
  `FIELD_REP` varchar(50) DEFAULT NULL,
  `SERVICE_REQUEST_DATE` date DEFAULT NULL,
  `ORDER_DATE` date DEFAULT NULL,
  `ACTIVE_DATE` date DEFAULT NULL,
  `CLOSE_DATE` date DEFAULT NULL,
  `LAST_CHANGE_DATE` datetime DEFAULT NULL,
  `EDIT_FLAG` varchar(50) NOT NULL DEFAULT '',
  `SITE_FAX_NUMBER` varchar(50) DEFAULT '',
  `SITE_PHONE_NUMBER` varchar(50) DEFAULT '',
  `GROUP_CONTACT` varchar(50) DEFAULT '',
  `GROUP_CONTACT_EMAIL` varchar(50) DEFAULT '',
  `GROUP_CONTACT_PHONE` varchar(50) DEFAULT '',
  `SITE_CONTACT` varchar(50) DEFAULT '',
  `SITE_CONTACT_PHONE` varchar(50) DEFAULT '',
  `SITE_HOURS` varchar(50) DEFAULT '',
  `LATITUDE` varchar(15) NOT NULL DEFAULT '',
  `LONGITUDE` varchar(15) NOT NULL DEFAULT '',
  `ADDRESS` varchar(100) DEFAULT '',
  `CITY` varchar(50) DEFAULT '',
  `ST` varchar(50) DEFAULT '',
  `ZIP` varchar(50) DEFAULT '',
  `SITE_IMAGE_MAP` varchar(255) DEFAULT '',
  `GROUP_IMAGE_MAP` varchar(255) DEFAULT '',
  `SERVICE_CODE` varchar(50) DEFAULT '',
  `SERVICE_TYPE` varchar(50) DEFAULT '',
  `ORDER_FLAG` varchar(50) DEFAULT '',
  `ORDER_BY` varchar(50) DEFAULT '',
  `NOTES_1` longtext DEFAULT NULL,
  `NOTES_2` longtext DEFAULT NULL,
  `SUPPORT_CENTER` varchar(50) DEFAULT '',
  `TELCO_PROVIDER` varchar(50) DEFAULT '',
  `TELCO_SUPPORT` varchar(50) DEFAULT '',
  `T1_CIRCUIT` varchar(50) NOT NULL DEFAULT '',
  `LEC_CIRCUIT` varchar(50) NOT NULL DEFAULT '',
  `CPE_MODEM_MODEL` varchar(50) DEFAULT NULL,
  `CPE_MODEM_FIRMWARE_REV` varchar(50) DEFAULT NULL,
  `CPE_MODEM_SERIAL_NUM` varchar(50) DEFAULT NULL,
  `CPE_ASSET_NUM` varchar(50) DEFAULT NULL,
  `CPE_INSERVICE_DATE` date DEFAULT NULL,
  `CPE_ACCESS_USERNAME` varchar(50) DEFAULT NULL,
  `CPE_ACCESS_PASSWORD` varchar(50) DEFAULT NULL,
  `ROUTER_MODEL` varchar(50) DEFAULT '',
  `ROUTER_FIRMWARE_REV` varchar(50) DEFAULT '',
  `ROUTER_SERIAL_NUM` varchar(50) DEFAULT '',
  `ROUTER_ASSET_NUM` varchar(50) DEFAULT NULL,
  `ROUTER_INSERVICE_DATE` date DEFAULT NULL,
  `ROUTER_ACCESS_USERNAME` varchar(50) DEFAULT '',
  `ROUTER_ACCESS_PASSWORD` varchar(50) DEFAULT '',
  `DLCI_ID` varchar(50) NOT NULL DEFAULT '',
  `DSL_LINE_NUMBER` varchar(50) DEFAULT '',
  `DSL_CIRCUIT_NUMBER` varchar(50) DEFAULT '',
  `INET_PROVIDER` varchar(50) DEFAULT '',
  `INET_PROVIDER_SUPPORT_NUMBER` varchar(50) DEFAULT '',
  `INET_PROVIDER_WEB` varchar(255) DEFAULT '',
  `WAN_IP_RANGE` varchar(50) DEFAULT '',
  `WAN_IP` varchar(50) DEFAULT '',
  `WAN_NETMASK` varchar(50) DEFAULT '',
  `WAN_GATEWAY` varchar(50) DEFAULT '',
  `WAN_AUTHENTICATION_TYPE` varchar(50) DEFAULT '',
  `DSL_USERNAME` varchar(50) DEFAULT '',
  `DSL_PASSWORD` varchar(50) DEFAULT '',
  `DIAL_UP_NUMBER` varchar(50) DEFAULT '',
  `TIME_ZONE` varchar(50) DEFAULT '',
  `LAST_CHANGE_BY` varchar(50) DEFAULT '',
  UNIQUE KEY `SITE_ID` (`SITE_ID`),
  KEY `SITE_NAME` (`SITE_NAME`),
  KEY `GROUP_NAME` (`GROUP_NAME`),
  KEY `LAN_IP` (`LAN_IP`),
  KEY `REGION` (`REGION`),
  KEY `ACTIVE_DATE` (`ACTIVE_DATE`),
  KEY `CLOSE_DATE` (`CLOSE_DATE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `USERS`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `USERS` (
  `USERNAME` varchar(255) NOT NULL DEFAULT '',
  `FULL_NAME` varchar(255) NOT NULL DEFAULT '',
  `PASSWORD` varchar(255) NOT NULL DEFAULT '',
  `ACCESSTYPE` varchar(50) DEFAULT 'ro',
  `ACCESSLEVEL` int(11) NOT NULL DEFAULT 0,
  `EMAIL` varchar(100) NOT NULL,
  `TITLE` varchar(100) NOT NULL DEFAULT '',
  `DEPARTMENTGROUP` varchar(100) NOT NULL DEFAULT '',
  `LAST_LOGIN_DATE` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `USER_GROUP` varchar(100) NOT NULL,
  `CREATE_DATE` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `FORCE_PASS_RESET` tinyint(4) NOT NULL DEFAULT 0,
  `AGREEMENT_ACCEPTED` varchar(250) NOT NULL DEFAULT '',
  `STYLE` varchar(250) NOT NULL DEFAULT 'ultramarine.css',
  UNIQUE KEY `USERNAME` (`USERNAME`),
  KEY `PASSWORD` (`PASSWORD`),
  KEY `EMAIL` (`EMAIL`),
  KEY `LAST_LOGIN_DATE` (`LAST_LOGIN_DATE`),
  KEY `USER_GROUP` (`USER_GROUP`),
  KEY `CREATE_DATE` (`CREATE_DATE`),
  KEY `FULL_NAME` (`FULL_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `foofoo`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `foofoo` (
  `SITE_ID` varchar(50) NOT NULL DEFAULT '',
  `SITE_NAME` varchar(100) DEFAULT '',
  `SITE_TYPE` varchar(100) DEFAULT '',
  `GROUP_NAME` varchar(100) DEFAULT '',
  `LAN_IP` varchar(50) DEFAULT '',
  `LAN_GATEWAY` varchar(50) DEFAULT '',
  `LAN_NETMASK` varchar(50) DEFAULT '',
  `REGION` varchar(50) DEFAULT '',
  `FIELD_REP` varchar(50) DEFAULT NULL,
  `SERVICE_REQUEST_DATE` date DEFAULT NULL,
  `ORDER_DATE` date DEFAULT NULL,
  `ACTIVE_DATE` date DEFAULT NULL,
  `CLOSE_DATE` date DEFAULT NULL,
  `LAST_CHANGE_DATE` datetime DEFAULT NULL,
  `EDIT_FLAG` varchar(50) NOT NULL,
  `SITE_FAX_NUMBER` varchar(50) DEFAULT '',
  `SITE_PHONE_NUMBER` varchar(50) DEFAULT '',
  `GROUP_CONTACT` varchar(50) DEFAULT '',
  `GROUP_CONTACT_EMAIL` varchar(50) DEFAULT '',
  `GROUP_CONTACT_PHONE` varchar(50) DEFAULT '',
  `SITE_CONTACT` varchar(50) DEFAULT '',
  `SITE_CONTACT_PHONE` varchar(50) DEFAULT '',
  `SITE_HOURS` varchar(50) DEFAULT '',
  `LATITUDE` varchar(15) NOT NULL DEFAULT '',
  `LONGITUDE` varchar(15) NOT NULL DEFAULT '',
  `ADDRESS` varchar(100) DEFAULT '',
  `CITY` varchar(50) DEFAULT '',
  `ST` varchar(50) DEFAULT '',
  `ZIP` varchar(50) DEFAULT '',
  `SITE_IMAGE_MAP` varchar(255) DEFAULT '',
  `GROUP_IMAGE_MAP` varchar(255) DEFAULT '',
  `SERVICE_CODE` varchar(50) DEFAULT '',
  `SERVICE_TYPE` varchar(50) DEFAULT '',
  `ORDER_FLAG` varchar(50) DEFAULT '',
  `ORDER_BY` varchar(50) DEFAULT '',
  `NOTES_1` longtext DEFAULT NULL,
  `NOTES_2` longtext DEFAULT NULL,
  `SUPPORT_CENTER` varchar(50) DEFAULT '',
  `TELCO_PROVIDER` varchar(50) DEFAULT '',
  `TELCO_SUPPORT` varchar(50) DEFAULT '',
  `T1_CIRCUIT` varchar(50) NOT NULL DEFAULT '',
  `LEC_CIRCUIT` varchar(50) NOT NULL DEFAULT '',
  `CPE_MODEM_MODEL` varchar(50) DEFAULT NULL,
  `CPE_MODEM_FIRMWARE_REV` varchar(50) DEFAULT NULL,
  `CPE_MODEM_SERIAL_NUM` varchar(50) DEFAULT NULL,
  `CPE_ASSET_NUM` varchar(50) DEFAULT NULL,
  `CPE_INSERVICE_DATE` date DEFAULT NULL,
  `CPE_ACCESS_USERNAME` varchar(50) DEFAULT NULL,
  `CPE_ACCESS_PASSWORD` varchar(50) DEFAULT NULL,
  `ROUTER_MODEL` varchar(50) DEFAULT '',
  `ROUTER_FIRMWARE_REV` varchar(50) DEFAULT '',
  `ROUTER_SERIAL_NUM` varchar(50) DEFAULT '',
  `ROUTER_ASSET_NUM` varchar(50) DEFAULT NULL,
  `ROUTER_INSERVICE_DATE` date DEFAULT NULL,
  `ROUTER_ACCESS_USERNAME` varchar(50) DEFAULT '',
  `ROUTER_ACCESS_PASSWORD` varchar(50) DEFAULT '',
  `DLCI_ID` varchar(50) NOT NULL DEFAULT '',
  `DSL_LINE_NUMBER` varchar(50) DEFAULT '',
  `DSL_CIRCUIT_NUMBER` varchar(50) DEFAULT '',
  `INET_PROVIDER` varchar(50) DEFAULT '',
  `INET_PROVIDER_SUPPORT_NUMBER` varchar(50) DEFAULT '',
  `INET_PROVIDER_WEB` varchar(255) DEFAULT '',
  `WAN_IP_RANGE` varchar(50) DEFAULT '',
  `WAN_IP` varchar(50) DEFAULT '',
  `WAN_NETMASK` varchar(50) DEFAULT '',
  `WAN_GATEWAY` varchar(50) DEFAULT '',
  `WAN_AUTHENTICATION_TYPE` varchar(50) DEFAULT '',
  `DSL_USERNAME` varchar(50) DEFAULT '',
  `DSL_PASSWORD` varchar(50) DEFAULT '',
  `DIAL_UP_NUMBER` varchar(50) DEFAULT '',
  `TIME_ZONE` varchar(50) DEFAULT '',
  `LAST_CHANGE_BY` varchar(50) DEFAULT '',
  UNIQUE KEY `SITE_ID` (`SITE_ID`),
  KEY `SITE_NAME` (`SITE_NAME`),
  KEY `GROUP_NAME` (`GROUP_NAME`),
  KEY `LAN_IP` (`LAN_IP`),
  KEY `REGION` (`REGION`),
  KEY `ACTIVE_DATE` (`ACTIVE_DATE`),
  KEY `CLOSE_DATE` (`CLOSE_DATE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-21 19:50:07
