<?php
/*###############################################################
        NETz Network Management system                          #
        http://www.proedgenetworks.com/netz                     #
                                                                #
                                                                #
        Copyright (C) 2005-2006 Louie Zarrella                  #
        louiez@proedgenetworks.com                              #
                                                                #
        Released under the GNU General Public License           #
        Copy of License available at :                          #
        http://www.gnu.org/copyleft/gpl.html                    #
###############################################################*/

include('../../logon.php');
include_once("../../site-monitor.conf.php");
include('../../write_access_log.php');

// open connection to Database server
$conns = mysql_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD);
if (!$conns) {
   die('Could not connect: ' . mysql_error());
}
// Select database
mysql_select_db(NETZ_DATABASE);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>

<?php

//      +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//      |       User Access code        |
//	|  change access level below 	|
//	|  to control who can connect	|
//	|  ***************************	|
//	| $_SESSION['accesslevel'] == 0	|
//	| only keeps disabled user out	|
//	|  ***************************	|
// =====================================================================================================//
//$acl=$_SESSION['accesstype'];                                                                         //
if ($_SESSION['accesslevel'] == 0){                                                                     //
        echo '<script type="text/javascript">window.location.href="../../access_denied.html"</script>'; //
        echo '<meta http-equiv="refresh" content="0;url=../../access_denied.html" />';                  //
        }                                                                                               //
// =====================================================================================================//
?>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<?php $style=$_SESSION['style']; if ($style==""){$style="../../style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo "../../".$style  ?>" type="text/css">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/vnd.microsoft.icon" >
    <link rel="icon" href="../../favicon.ico" type="image/vnd.microsoft.icon" >

<title>Plugin Template</title>
</head>
<body >
<script language="JavaScript1.2" type="text/javascript" src="menulz.js"> </script>

<?php
// Adds menu items to side menu
 if ($_SESSION['accesslevel'] >= 9){
        echo '<script language="JavaScript1.2" type="text/javascript"  src="menu-data-rwa.js"> </script>';
 }elseif($_SESSION['accesslevel'] >= 4){
        echo '<script language="JavaScript1.2" type="text/javascript"  src="menu-data-rw.js"> </script>';
}else{
        echo '<script language="JavaScript1.2" type="text/javascript"  src="menu-data-ro.js"> </script>';
}

// create query string
$SQL="SELECT * FROM SITEDATA";
// Query database
$result=mysql_query($SQL);
	while ($row = mysql_fetch_assoc($result))
	{
		// print the site is....etc
		echo $row['SITE_ID']."<br>";
	}
?>

</body></html>
