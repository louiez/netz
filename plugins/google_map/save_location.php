<?php
include_once('../../logon.php');
include_once("../../site-monitor.conf.php");
// open connection to Database server
$conns = mysql_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD);
if (!$conns) {
   die('Could not connect: ' . mysql_error());
}
// Select database
mysql_select_db(NETZ_DATABASE);

$site=$_GET['site'];
$lat=$_GET['lat'];
$lon=$_REQUEST['lon'];
// update Site Latitude and longitude
$SQL="UPDATE SITEDATA SET LATITUDE = '".$lat."' where SITE_ID = '".$site."'";
echo $SQL;
$result=mysql_query($SQL);
$SQL="UPDATE SITEDATA SET LONGITUDE = '".$lon."' where SITE_ID = '".$site."'";
$result=mysql_query($SQL);

?>
<html><body><script type="text/javascript">alert("<?php echo $SQL; ?>");</script></body></html>
