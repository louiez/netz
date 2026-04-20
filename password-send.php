<!DOCTYPE HTML>
<?php
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL); 

require_once('site-monitor.conf.php');
//require_once("phpmailer/class.phpmailer.php");
require_once('lmz-functions.php');
require_once( 'class.ConfigMagik.php');
// Mail header Optional but required to stop being flagged as spam by some servers
$headers = "From: ".$smtp_from_address."\r\n" .
           "Reply-To: ".$smtp_from_address."\r\n" .
           "X-Mailer: PHP/" . phpversion();


// generatePassword
$email=$_POST['email'];

if (trim($email) != ""){
	$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
	if (!$conn) {
   		die('Could not connect: ' . mysqli_error());
	}
	//mysql_select_db(NETZ_DATABASE);
	$sql="SELECT * FROM USERS WHERE EMAIL = '".addslashes($email)."'";
//echo "<br>".$sql."<br>";
        $results = mysqli_query($conn,$sql);
        $rows = mysqli_fetch_assoc($results);
	$numrows = mysqli_num_rows($results);
	// Generate new password
        //$newpass = generatePassword('8');
       	//$sql2="Update USERS SET PASSWORD = '".md5($newpass)."' WHERE EMAIL = '".$email."'";
	//@mysql_query($sql2);
	$sucessmsg="";
	if ($numrows == 1){ 	

        	// Generate new password
	        $newpass = generatePassword('8');
		// generate a security string
		$security_string= generatePassword('25');
		// first Delete any old pending password requests
		$sql="DELETE FROM PASSWORD_RESET WHERE USERNAME = '".addslashes($rows['USERNAME'])."'";
        	mysqli_query($conn,$sql);
        	// write new data to password reset database for verify
		$sql2="INSERT INTO PASSWORD_RESET (USERNAME, PASSWORD, SECRET_STRING) ";
//		$sql2 .= "VALUES('".addslashes($rows['USERNAME']) ."','".md5($newpass)."','".$security_string."')";
		$sql2 .= "VALUES('".addslashes($rows['USERNAME']) ."','".password_hash($newpass, PASSWORD_DEFAULT)."','".$security_string."')";

        	@mysqli_query($conn,$sql2);

		// create the url string to send to user
		//if ($_SERVER['HTTPS']) { $https="https://";}else{$https="http://";}
		$reset_url= "https://" . $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']);
		$reset_url .=  "/password_verify.php?user=".urlencode($rows['USERNAME']);
		$reset_url .=  "&ss=".$security_string;
		// split the users real name so we can get just the first name
		$fn = explode(" ", addslashes($rows['FULL_NAME']));

		// Create the body of the message
		$body = $fn[0]. ",\n\n";
		$body .= "We have received a password reset request for your NETz user account\n";
		$body .= "***** If you did not request this password change you can Ignore this email *****\n\n";
		$body .= "Please go to the link below to verify the Password reset\n";
		$body .= $reset_url;
		$body .= "\n\n********** PASSWORD WILL NOT BE RESET UNTIL YOU GO TO THE LINK ABOVE ****** ";
		$body .= "\n\nUsername: ".$rows['USERNAME']. "\nPassword: ".$newpass;
		$body .= "\n\nIt is recommended that you change your password to something that is easier to\n";
		$body .= "remember, which can be done by going to the Change Password link after signing in.\n\n";
		$body .= "Thanks\nNETz Administrator";
$subject="Netz password request";
$to=$email;
if (mail($to, $subject, $body, $headers)) {
    echo 'Email sent successfully.';
} else {
    echo 'Failed to send email.';
}
	}elseif ($numrows > 1){
		echo "seems to be ".$numrows." users with the same e-mail address<br>";
		echo "Contact the NETz administrator  <br><a href='main.php'>NETz Home</a>";	
		$sucessmsg=$numrows." users with the same e-mail address";
		error_log(date('Y-m-d G:i:s')." - password-send.php  seems to be ".$numrows." users with the same e-mail address ".$email." \n", 3, $netzlogs .'netz.log');
	}else{
		echo "No user for ".$email;
		echo "<br> Contact NETz System admin at ". SITE_ADMIN_EMAIL ;
		$sucessmsg="Failed - No such email";
		error_log(date('Y-m-d G:i:s')." - password-send.php No user for ".$email." From IP ".$_SERVER['REMOTE_ADDR']." \n", 3, $netzlogs .'netz.log');
	}
	//*********************************************************************************
	// Log the request to Access Log	|					//*
	//--------------------------------------|
	$post="Password reset for ".$email. " " .$sucessmsg;				//*
	$sql3="Insert into ACCESSLOG set PAGE = '" . $_SERVER['SCRIPT_NAME']."',";	//*
	$sql3= $sql3 . " ACCESS_DATE_TIME = '" . date("Y-m-d G:i:s")."',";		//*
	$sql3= $sql3 . " USERS_IP = '" . $_SERVER['REMOTE_ADDR']."',";			//*
	$sql3= $sql3 . " QUERY_STRING = '" . $_SERVER['QUERY_STRING']. $post ."',";	//*
	$sql3= $sql3 . " USERNAME = 'Guest',";						//*
	$sql3= $sql3 . " ACCESSLEVEL = 0";						//*
	$query2 = @mysqli_query($conn,$sql3);							//*
	//*********************************************************************************

}else{
?>

	<html>
	<head>
	<meta http-equiv="Content-Language" content="en-us">
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

        <title>Request Password Reset</title>
	</head>
	<body bgcolor="#000066" text="#FFFFFF" >
	<div style="text-align:center">
	<img src="netz.jpg" width="216" height="64">
	<br>
	<table align="center">
	<tr><td align="left">
	<form action="password-send.php" method="post">
	<b><font size="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Request Password Reset</font></b><br><br>
	Enter Email address <input type="email" name="email"><br><br>
	
	<input type="submit" value="Send Request">

</td></tr></table>
<br>
</div>
</form>




</body>
</html>

<?php	
}

?>
