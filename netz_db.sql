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

-- Dump completed on 2026-04-21 21:36:09
INSERT INTO `USERS` (`USERNAME`, `FULL_NAME`, `PASSWORD`, `ACCESSTYPE`, `ACCESSLEVEL`, `EMAIL`, `LAST_LOGIN_DATE`, `USER_GROUP`, `CREATE_DATE`, `FORCE_PASS_RESET`, `AGREEMENT_ACCEPTED`, `STYLE`) VALUES ('admin', 'Admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'Admin Full (10)', 10, '%ADMIN_USER%', '2006-10-21 21:53:40', '', '0000-00-00 00:00:00', 1, '2006-03-19 9:21:14', 'style/midnight-small.css:all');
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
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `NAME_MAPING`
--

LOCK TABLES `NAME_MAPING` WRITE;
/*!40000 ALTER TABLE `NAME_MAPING` DISABLE KEYS */;
INSERT INTO `NAME_MAPING` VALUES
('VIP_ALERT_OFFLINE','VIP_ALERT_OFFLINE',1,1,1),
('SUPPORT_ALERT_ONLINE','Support Alert Online',1,1,1),
('SUPPORT_ALERT_OFFLINE','Support Alert Offline',1,1,1),
('VIP_ALERT_ONLINE','VIP_ALERT_ONLINE',1,1,1),
('MONITOR_HTTP_PORT','MONITOR_HTTP_PORT',1,1,0),
('MONITOR_HTTP_STATUS','MONITOR_HTTP_STATUS',1,1,0),
('DATE_LAST_ALERT','Date of last Alert',1,1,0),
('ALERT_SENT','Alert Sent Flag',1,1,0),
('TOTAL_ALERTS_SENT','Total Alerts sent',1,1,0),
('MONITOR_HTTP_CONTENT','MONITOR_HTTP_CONTENT',1,1,0),
('MONITOR_HTTP_TIMEOUT','MONITOR_HTTP_TIMEOUT',1,1,0),
('MONITOR_HTTP_IP_FIELD','MONITOR_HTTP_IP_FIELD',1,1,0),
('MONITOR_HTTP_PAGE','MONITOR_HTTP_PAGE',1,1,0),
('MONITOR_HTTP_ENABLE','MONITOR_HTTP_ENABLE',1,1,0),
('MONITOR_IP_FIELD','MONITOR_IP_FIELD',1,1,0),
('MONITOR_STATUS','MONITOR_STATUS',1,1,0),
('SITE_ID','Device Name',1,1,1),
('SITE_NAME','Site Name',1,1,1),
('SITE_TYPE','SiteType',1,1,1),
('GROUP_NAME','Group Name',1,1,1),
('LAN_IP','LAN IP',1,1,1),
('LAN_GATEWAY','LAN Gateway',1,1,1),
('LAN_NETMASK','LAN Netmask',1,1,1),
('REGION','Region',1,1,1),
('FIELD_REP','Field Rep',1,1,1),
('SERVICE_REQUEST_DATE','Service Request Date',1,1,1),
('ORDER_DATE','Order Date',1,1,1),
('ACTIVE_DATE','Active Date',1,1,1),
('CLOSE_DATE','Close Date',1,1,1),
('LAST_CHANGE_DATE','Last Change Date',1,1,0),
('SITE_FAX_NUMBER','Site Fax Number',1,1,1),
('SITE_PHONE_NUMBER','Site Phone Number',1,1,1),
('GROUP_CONTACT','Group Contact',1,1,1),
('GROUP_CONTACT_EMAIL','Group Contact Email',1,1,1),
('GROUP_CONTACT_PHONE','Group Contact Phone',1,1,1),
('SITE_CONTACT','Site Contact',1,1,1),
('SITE_CONTACT_PHONE','Site Contact Phone',1,1,1),
('SITE_HOURS','Site Hours',1,1,1),
('LATITUDE','LATITUDE',1,1,0),
('LONGITUDE','LONGITUDE',1,1,0),
('ADDRESS','Address',1,1,1),
('CITY','City',1,1,1),
('ST','ST',1,1,1),
('ZIP','ZIP',1,1,1),
('SITE_IMAGE_MAP','Site Image Diagram',1,1,1),
('GROUP_IMAGE_MAP','Group Image Map',1,1,1),
('SERVICE_CODE','Service Code',1,1,1),
('SERVICE_TYPE','Service Type',1,1,1),
('ORDER_FLAG','Order Flag',1,1,1),
('ORDER_BY','Ordered By',1,1,1),
('NOTES_1','Public Notes',1,1,1),
('NOTES_2','Admin Notes',1,1,1),
('SUPPORT_CENTER','Support Center',1,1,1),
('TELCO_PROVIDER','TELCO_PROVIDER',1,1,1),
('TELCO_SUPPORT','TELCO_SUPPORT',1,1,1),
('T1_CIRCUIT','AP SSID',1,1,1),
('LEC_CIRCUIT','AP Password',1,1,1),
('CPE_MODEM_MODEL','CPE Model',1,1,1),
('CPE_MODEM_FIRMWARE_REV','CPE Firmware Revision',1,1,1),
('CPE_MODEM_SERIAL_NUM','CPE Serial Number',1,1,1),
('CPE_ASSET_NUM','CPE Asset Number',1,1,1),
('CPE_INSERVICE_DATE','CPE In Service date',1,1,1),
('CPE_ACCESS_USERNAME','Admin Access Username',1,1,1),
('CPE_ACCESS_PASSWORD','Admin Access Password',1,1,1),
('ROUTER_MODEL','Model',1,1,1),
('ROUTER_FIRMWARE_REV','Software Ver',1,1,1),
('ROUTER_SERIAL_NUM','serial Number',1,1,1),
('ROUTER_ASSET_NUM','Asset Number',1,1,1),
('ROUTER_INSERVICE_DATE','in-Service date',1,1,1),
('ROUTER_ACCESS_USERNAME','Access Username',1,1,1),
('ROUTER_ACCESS_PASSWORD','Access Password',1,1,1),
('DLCI_ID','DLCI ID',1,1,1),
('DSL_LINE_NUMBER','WIFI Client SSID',1,1,1),
('DSL_CIRCUIT_NUMBER','WIFI Client Password',1,1,1),
('INET_PROVIDER','Inet Provider',1,1,1),
('INET_PROVIDER_SUPPORT_NUMBER','ISP Support Number',1,1,1),
('INET_PROVIDER_WEB','ISP Web Site',1,1,1),
('WAN_IP_RANGE','WAN IP Range',1,1,1),
('WAN_IP','Wan IP',1,1,1),
('WAN_NETMASK','WAN Netmask',1,1,1),
('WAN_GATEWAY','WAN Gateway',1,1,1),
('WAN_AUTHENTICATION_TYPE','MAC Address',1,1,1),
('DSL_USERNAME','DSL Username',1,1,1),
('DSL_PASSWORD','DSL Password',1,1,1),
('DIAL_UP_NUMBER','Dial Up Number',1,1,1),
('TIME_ZONE','Time Zone',1,1,0),
('CPE_MODEM_FIRMWARE_REV','CPE Firmware Revision',1,1,1),
('MONITOR_ENABLE','MONITOR_ENABLE',1,1,0),
('MONITOR_TIMEOUT','MONITOR_TIMEOUT',1,1,0),
('LAST_CHANGE_BY','Last Change By',1,1,0);
/*!40000 ALTER TABLE `NAME_MAPING` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-21 21:36:09
INSERT INTO ALERTEMAILS VALUES ('Atlanta','support1@nowhere.com','support','','770-555-1212','');
INSERT INTO ALERTEMAILS VALUES ('Dallas','support2@nowhere.com','support','','817-555-1212','');
