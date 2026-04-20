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
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL);

include_once("auth.php");
include_once("site-monitor.conf.php");
include('write_access_log.php');
//      +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//      |     User Access code          |
// ============================================================================================++++++++=//
if ($_SESSION['accesslevel'] <= 8){
        echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';       //
        echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';                        //
	die("unauthorized user");
        }                                                                                               //
// =============================================================================================++++++++//
$logfile = $netzlogs."netz.log";
$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);
$results = mysqli_query($conn,"select * from SITEDATA");
if (!$results) {
   die('Query failed: ' . mysqli_error());
}
// Load variables with posted data
$un=addslashes(trim($_POST['txtusername']));
$fn=addslashes(trim($_POST['txtfullname']));
//$pw=md5(trim($_POST['txtpassword']));
$pw=trim($_POST['txtpassword']);
$pwr=$_POST['chkforcereset'];
if ($pwr == "ON"){$pwr=1;}else{$pwr=0;}
$level=$_POST['txtuserlevel'];
//$type=$_POST['txtusertype'];
switch ($level) {
	case 0:
        	$type="Disabled (0)";
		break;
	case 1:
        	$type="read only (1)";
		break;
	case 2:
        	$type="read only ops (2)";
		break;
	case 3:
        	$type="read only unused (3)";
		break;
	case 4:
        	$type="read/write order (4)";
		break;
	case 5:
        	$type="read/write unused (5)";
		break;
	case 6:
        	$type="read/write unused (6)";
		break;
	case 7:
        	$type="read/write ops (7)";
		break;
	case 8:
        	$type="read/write unused (8)";
		break;
	case 9:
        	$type="Admin (9)";
		break;
	case 10:
        	$type="Admin Full (10)";
		break;
	default:
                $type="Disabled (0)";
                break;
}
//$level=$_POST['txtuserlevel'];
$email=addslashes(trim($_POST['txtemail']));
$title=trim($_POST['txtjobtitle']);
$departmentgroup=trim($_POST['txtdepartmentgroup']);
// format the date for mysqli
$date=date('Y-m-d G:i:s');
//see what page posted the write
$refpage=strpos($_SERVER['HTTP_REFERER'], 'usermod.php');
if (!$refpage)  // Insert from useradmin.php add section
{
	// Check that there was a username and password entered	
	if (!($un == "" || $pw == "")){
	$sql = "INSERT INTO USERS (USERNAME, FULL_NAME, PASSWORD, ACCESSTYPE,ACCESSLEVEL, EMAIL, TITLE, DEPARTMENTGROUP, ";
	$sql = $sql . "LAST_LOGIN_DATE, USER_GROUP, CREATE_DATE, FORCE_PASS_RESET, STYLE  )";
	$sql = $sql . "VALUES (";
	$sql = $sql . "'".$un."',";
	$sql = $sql ."'".$fn."', ";
	$sql = $sql ."'".password_hash($pw, PASSWORD_DEFAULT)."', ";
	$sql = $sql ."'".$type."',";
	$sql = $sql .$level.", ";
	$sql = $sql ."'".$email."', ";
	$sql = $sql ."'".$title."', ";
	$sql = $sql ."'".$departmentgroup."', ";
	$sql = $sql ."'0000-00-00 00:00:00', ";
	$sql = $sql ."'', ";
	$sql = $sql ."'" . $date . "', ";
	$sql = $sql .$pwr.",";
	$sql = $sql ."'style/midnight-small.css:all')";
	$query = mysqli_query($conn,$sql);
	$err_msg=" - New user ".$fn." added as ".$un." By ".$_SESSION['user'];
	error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
	}
}
else  // Update User from usermod.php
{
	if (!$pw == ""){
		$sql = "UPDATE USERS SET PASSWORD = '" . password_hash($pw, PASSWORD_DEFAULT). "' WHERE USERNAME = '" .$un . "'";
		$query = mysqli_query($conn,$sql);
	}
        $sql = "UPDATE USERS SET FULL_NAME = '" . $fn. "' WHERE USERNAME = '" .$un . "'";
        $query = mysqli_query($conn,$sql);
	$sql = "UPDATE USERS SET ACCESSTYPE = '" .$type. "' WHERE USERNAME = '" .$un . "'";
	$query = mysqli_query($conn,$sql);
        $sql = "UPDATE USERS SET ACCESSLEVEL = " .$level. " WHERE USERNAME = '" .$un . "'";
        $query = mysqli_query($conn,$sql);
	$sql = "UPDATE USERS SET EMAIL = '" .$email. "' WHERE USERNAME = '" .$un . "'";
	$query = mysqli_query($conn,$sql);

        $sql = "UPDATE USERS SET TITLE = '" .$title. "' WHERE USERNAME = '" .$un . "'";
        $query = mysqli_query($conn,$sql);
        $sql = "UPDATE USERS SET DEPARTMENTGROUP = '" .$departmentgroup. "' WHERE USERNAME = '" .$un . "'";
        $query = mysqli_query($conn,$sql);

	$sql = "UPDATE USERS SET USER_GROUP = '' WHERE USERNAME = '" .$un . "'";
	$query = mysqli_query($conn,$sql);
        $sql = "UPDATE USERS SET FORCE_PASS_RESET = ".$pwr." WHERE USERNAME = '" .$un . "'";
        $query = mysqli_query($conn,$sql);
	// Log the Add
	$err_msg=" - User Properties for ".$fn." (".$un.") Changed  By ".$_SESSION['user'];
	error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);

}
	//$sql="SELECT * FROM USERS WHERE USERNAME = '".$_SESSION['user']."'";
	
?>
<html>
<body onload="javascript: window.location='useradmin.php'">
</body></html>
