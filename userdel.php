<?php
/*###############################################################
        NETz Network Management system                          #
        http://www.proedgenetworks.com/netz                     #
                                                                #
                                                                #
        Copyright (C) 2005-2026 Louie Zarrella                  #
        jwaldo85@gmail.com                             #
                                                                #
        Released under the GNU General Public License           #
        Copy of License available at :                          #
        http://www.gnu.org/copyleft/gpl.html                    #
###############################################################*/

include_once('auth.php');
include("site-monitor.conf.php");
//      +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//      |     User Access code          |
// ============================================================================================++++++++=//
if ($_SESSION['accesslevel'] <= 8){
        echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';       //
        echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';                        //
	die('unauthorized access');
        }                                                                                               //
// =============================================================================================++++++++//
$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}


$logfile = $netzlogs."netz.log";
$results = mysqli_query($conn,"select * from SITEDATA");
if (!$results) {
   die('Query failed: ' . mysqli_error());
}

$un = addslashes($_GET['user']) ;

	$sql = "DELETE FROM USERS WHERE USERNAME = '" . $un . "'" ;
	$query = mysqli_query($conn,$sql);
        $err_msg=" - User (".$un.") Deleted  By ".$_SESSION['user'];
        error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
?>
<html>
<body onload="javascript: window.location='useradmin.php'">
</body></html>
