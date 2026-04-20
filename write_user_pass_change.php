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
include_once('auth.php');
include("site-monitor.conf.php");

$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);

// Load variables with posted data
$un=trim($_POST['txtusername']);
echo $un."un<br>";
echo $_SESSION['user']."ses<br>";
//$pw=md5($_POST['txtpassword']);
$pw=password_hash($_POST['txtpassword'], PASSWORD_DEFAULT);
//if ($_SESSION['user'] != stripslashes($un)){die('unauthorized access'); }
if ($_SESSION['user'] != stripslashes($un)){
    die('unauthorized access');
    exit();
}


// format the date for mysqli
$date=date('Y-m-d G:i:s');

	if (!empty($pw)) {
		$sql = "UPDATE USERS SET PASSWORD = '" . $pw. "' WHERE USERNAME = '" .$un . "'";
		//$query = mysqli_query($conn,$sql);
		$query = mysqli_query($conn, $sql) or die("Query Failed: " . mysqli_error($conn));
//		$_SESSION['pass']= $pw;
	}
	//$sql="SELECT * FROM USERS WHERE USERNAME = '".$_SESSION['user']."'";
//if (empty($_SESSION['passreset']))	
if (!isset($_SESSION['passreset']) || empty($_SESSION['passreset'])){
?>
	<html>
	<body onload="javascript: window.close()">
	</body></html>
<?php
exit(); // ✅ Stop script execution
}
else
{
	$sql = "UPDATE USERS SET FORCE_PASS_RESET = 0 WHERE USERNAME = '" .$un . "'";
	$query = mysqli_query($conn,$sql);
	$_SESSION['passreset'] = "";
?>
	<html>
        <body onload="javascript: window.location = 'main.php'">
        </body></html>
<?php
}

?>
