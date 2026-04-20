<?php
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL);

require_once('site-monitor.conf.php');
require_once('lmz-functions.php');
require_once( 'class.ConfigMagik.php');
$user=addslashes($_GET['user']);                
$security_string_test=$_GET['ss']; 

// connect to dabase server
        $conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
        if (!$conn) {
                die('Could not connect: ' . mysqli_error());
        }

// Grab info from password reset database
$sql_get="SELECT * FROM PASSWORD_RESET WHERE USERNAME = '".$user."'";
$res_get= mysqli_query($conn,$sql_get);
$rows = mysqli_fetch_assoc($res_get);

// get secret string from database
$security_string = $rows['SECRET_STRING'];

// test the string passed is the same as the one created during the reset request
if ($security_string == $security_string_test){
	$newpass=$rows['PASSWORD'];
	// create the query
	$sql="Update USERS SET PASSWORD = '".$newpass."' WHERE USERNAME = '".$user."'";
	mysqli_query($conn,$sql);
	$sql="Update USERS SET FORCE_PASS_RESET = 1 WHERE USERNAME = '".$user."'";
	mysqli_query($conn,$sql);
	// remove the password request from file
	$sql="DELETE FROM PASSWORD_RESET WHERE USERNAME = '".$user."'";
	mysqli_query($conn,$sql);
	// write to log
	$err_message=date('Y-m-d G:i:s')." - Password reset confirmed for ".$user;
	$err_message .= " From IP ".$_SERVER['REMOTE_ADDR']." \n";
	error_log($err_message, 3, $netzlogs .'netz.log');
	echo "<html><body><span style=\"color : blue\">***  Password Reset confirmed ***</span> <br>";
	echo "Thank You !<br>";
	echo "You can now logon with your new password <br>";
	echo " <a href=\"main.php\"> Logon page</a></body></html>";
}else{
	echo "<html><body> No password request pending <br> <a href=\"main.php\"> Logon page</a></body></html>";

}
?>
