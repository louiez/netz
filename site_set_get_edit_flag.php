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
include_once("auth.php");
include_once("site-monitor.conf.php");
include_once('lmz-functions.php');
$site=$_GET['site'];
// edit = 1 = set edit flag in db
// edit = 2 = clear edit flag
if (isset($_GET['edit'])){
	$edit=$_GET['edit'];
}else{
	$edit="";
}
// open connection to Database server
$conns = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conns) {
   die('Could not connect: ' . mysql_error());
}



if ($edit== "1"){
	$SQL="UPDATE SITEDATA SET EDIT_FLAG='".strftime("%Y-%m-%d %H:%M:%S",time())."|".$_SESSION['name']."' WHERE SITE_ID = '".$site."'";
	mysqli_query($conns,$SQL);
}elseif ($edit== "2"){
	$SQL="UPDATE SITEDATA SET EDIT_FLAG='' WHERE SITE_ID = '".$site."'";
	mysqli_query($conns,$SQL);
}else{
	// create query string
	$SQL="SELECT * FROM SITEDATA WHERE SITE_ID = '".$site."'";
	// Query database
	$result=mysqli_query($conns,$SQL);
	$row = mysqli_fetch_assoc($result);
	// Checks for bad last change date and sets it to something before NETz was born
	// it will cause the browser to refresh with this new safe date
//        if ($row['LAST_CHANGE_DATE'] == null || $row['LAST_CHANGE_DATE'] == "0000-00-00 00:00:00"){
//                $SQL="UPDATE SITEDATA SET LAST_CHANGE_DATE='1999-01-01 00:00:00' WHERE SITE_ID='".$site."'";
//                $result=mysql_query($SQL);
                
//        }
	$edit_tmp=explode('|',$row['EDIT_FLAG']);
	if (!isset($edit_tmp[1])){$edit_tmp[1]="";}
	echo trim($row['LAST_CHANGE_DATE']) ."|".trim($edit_tmp[0])."|".strftime("%Y-%m-%d %H:%M:%S",time())."|".trim($edit_tmp[1]);
        //echo trim($row['LAST_CHANGE_DATE']) ."|".trim($row['EDIT_FLAG'])."|".strftime("%Y-%m-%d %H:%M:%S",time());
}
?>
