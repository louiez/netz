<?php
define("SITE_INFO_TABLE","SITEDATA");				// Main table with site info
define("SITE_ID_DEFAULT","SITE_ID");				// site id field must be unique
define("SITE_IP_DEFAULT","LAN_GATEWAY");				// Default feild name in main table that stores IP to monitor

define("NETZ_DB_SERVER","");
define("NETZ_DB_USERNAME","");
define("NETZ_DB_PASSWORD","");
define("NETZ_DATABASE","");

define("SITE_MON_TABLE","MONLOGS");					// table used to store monitor data
define("ALLOW_DOCUMENT_UPLOADS","yes");			// allow siet and group image uploads to server
$site_down_tb="DOWNSITES";				// table to store down and or cronic sites
$netzlogs= "";                                         // Directory where netz logs

$basedir= '';    // Directory where netz lives
$uploadDir = '/usr/netz/';	// Directory to same uploaded images
$allowuploads = "";                             // Allow Upload og Images
$montype="icmp";                                        // connect with ICMP ping
$icmpcount=4;

$mass_alert_threshold=10;                                        // Number of sites down to trigger mass alerts
$enablemonitor="ON";

$monitor_timeout=2;                                     // seconds to timeout each connection try
$alert_cycles=4;                                        // number of ping sets to fail before alert is sent

$moncycleinterval=5;
$logdays=30;						// Number of days to keep monitor logs
$email_server="localhost";			// email server to forward alerts
$email_server_port="25";				// email server port

define("STYLESHEET",0);
define("SUPPORT",1);
define("MENU1",2);
define("MENU2",3);
define("MENU3",4);
define("MENU4",5);
define("MENU5",6);


?>
