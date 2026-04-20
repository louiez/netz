<?php
ob_start();
include_once("auth.php");
include_once("site-monitor.conf.php");
include('write_access_log.php');
//$rtn=print_r($_POST);
//$rtn=preg_replace("\n", '<br>',$rtn);
//echo $rtn;
function save_config_file($Nfilename,$Ndata)
{
	if ($handle = fopen($basedir.$Nfilename, 'w')) {                                                        
		fwrite($handle, $Ndata);  
		fclose($handle);                                           
		system('cat '.$basedir.$Nfilename. ' | sort > '.$basedir.$Nfilename.'.tmp');
		system('mv -f '.$basedir.$Nfilename.'.tmp '.$basedir.$Nfilename);
	}
}
?>
<html><body>
<?php
$logfile = $netzlogs."db-change.log";
//save_config_file('support-centers.txt',$_POST['support-centers_txt']);
/* ========================================================================= 
			save the Support Center info
  =========================================================================*/
	$supp_con = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
	if (!$supp_con) {
	   die('Could not connect: ' . mysqli_error());
	}
	//mysqli_select_db(NETZ_DATABASE,$supp_con) or die( "Unable to select database") ;
	// Delete all the support entries
	$supp_query ="DELETE FROM ALERTEMAILS WHERE TYPE = 'support'";
	mysqli_query($supp_con,$supp_query);
	// loop throught the support name and email array passed from netz-config.php
	foreach($_POST['support_name'] as $key=>$support){
		// if support name is not empty ... save it
		if (trim($support) != ""){
			$supp_query ="INSERT INTO ALERTEMAILS (LOCATION,EMAIL,TYPE,PHONE_NUMBER,FAX_NUMBER) ";
			$supp_query .= "VALUES(";				
			$supp_query .= "'".$support."',";			// LOCATION
			$supp_query .= "'".$_POST['support_email'][$key]."',";	// EMAIL
			$supp_query .= "'support',";				// TYPE
			$supp_query .= "'".$_POST['support_phone'][$key]."',";	// PHONE_NUMBER
			$supp_query .= "'".$_POST['support_fax'][$key]."')";	// FAX_NUMBER
			mysqli_query($supp_con,$supp_query);
		}
	}
/* =========================================================================*/
save_config_file('site-type.txt',$_POST['site-type_txt']);
save_config_file('fsr.txt',$_POST['fsr_txt']);
save_config_file('service-type.txt',$_POST['service-type_txt']);
save_config_file('region.txt',$_POST['region_txt']);
//print_r($_POST);
//ob_flush();
$newline="\n";

$configData = '<?php'.$newline;
$configData .= 'define("SITE_INFO_TABLE","SITEDATA");			// Main table with site info'.$newline;
$configData .= 'define("SITE_MON_TABLE","MONLOGS");			// table used to store monitor data'.$newline;
$configData .= 'define("SITE_ID_DEFAULT","SITE_ID");			// site id field must be unique'.$newline;

$configData .= 'define("SITE_IP_DEFAULT","LAN_GATEWAY");		// Default feild name in main table that stores IP to monitor'.$newline;

$configData .='define("NETZ_DB_SERVER","'.$_POST['server'].'");'.$newline;
$configData .= 'define("NETZ_DB_USERNAME","'.$_POST['username'].'");'.$newline;
$configData .= 'define("NETZ_DB_PASSWORD","'.$_POST['password'].'");'.$newline;
$configData .= 'define("NETZ_DATABASE","'.$_POST['database'].'");'.$newline;


$configData .= 'define("ALLOW_DOCUMENT_UPLOADS",1);                     // allow siet and group image uploads to server'.$newline;
$configData .= '$site_down_tb="DOWNSITES";                              // table to store down and or cronic sites'.$newline;

$configData .= '$netzlogs= "'.$_POST['netzlogs'].'";                                         // Directory where netz logs'.$newline;
$configData .= '$basedir= "'.$_POST['basedir'].'";    					// Directory where netz lives'.$newline;
$configData .= '$uploadDir = "'.$_POST['uploaddir'].'";        				// Directory to same uploaded images'.$newline;
$configData .= '$allowuploads = "'.$_POST['allowuploads'].'"; 				// Allow Upload og Images'.$newline;         
$configData .= 'define("SITE_ADMIN_EMAIL","'.$_POST['adminemail'].'");        // Site admin email'.$newline;
$configData .= '$montype="icmp";                                        // connect with ICMP ping'.$newline;
$configData .= '$icmpcount=4;'.$newline;

$configData .= '$monitor_timeout=2;                                     // seconds to timeout each connection try'.$newline;
$configData .= '$alert_cycles='.$_POST['alertcycles'].';                                        // number of ping sets to fail before alert is sent'.$newline;
if ($_POST['massalert'] == ""){$massalert=100;}else{$massalert=$_POST['massalert'];}
$configData .= '$mass_alert_threshold='.$massalert.';                                        // Number of sites down to trigger mass alerts'.$newline;
$configData .=  '$alert_message="'.$_POST['txtalertmessage'].'";                                        // Message to display on pages'.$newline;

// Check if the cycle interval was changed.... if it was run the site cron script at the bottom
if ($moncycleinterval != $_POST['cycleinterval']){$newinterval = "yes";}
$configData .= '$moncycleinterval='.$_POST['cycleinterval'].';'.$newline;
$configData .= '$logdays='.$_POST['logdays'].';                                            // Number of days to keep monitor logs'.$newline;
$configData .= '$enablemonitor="'.$_POST['enablemon'].'";'.$newline;
$configData .= '$alert_enable="'.$_POST['alertenable'].'";'.$newline;
$configData .= '$email_server="'.$_POST['emailserver'].'";                      	// email server to forward alerts'.$newline;
$configData .= '$email_server_port="'.$_POST['emailserverport'].'";                                // email server port'.$newline;

$configData .= '$mononline1="'.$_POST['monitoronline1'].'";                                // Monitor Online IP 1'.$newline;
$configData .= '$mononline2="'.$_POST['monitoronline2'].'";                                // Monitor Online IP 2'.$newline;
$configData .= '$mononline3="'.$_POST['monitoronline3'].'";                                // Monitor Online IP 3'.$newline;
$configData .= '$smtp_auth="'.$_POST['smtpauth'].'";                                // Use SMTP AUTH'.$newline;
$configData .= '$smtp_secure="'.$_POST['smtp_secure'].'";                                // Use SMTP SECURE'.$newline;
$configData .= '$smtp_user="'.$_POST['smtpuser'].'";                                // SMTP Username'.$newline;
$configData .= '$smtp_pass="'.$_POST['smtppass'].'";                                // SMTP password'.$newline;
$configData .= '$smtp_from_address="'.$_POST['from_address'].'";                                // From Address'.$newline;
$configData .= 'define("STYLESHEET",0);'.$newline;
$configData .= 'define("SUPPORT",1);'.$newline;
$configData .= 'define("MENU1",2);'.$newline;
$configData .= 'define("MENU2",3);'.$newline;
$configData .= 'define("MENU3",4);'.$newline;
$configData .= 'define("MENU4",5);'.$newline;
$configData .= 'define("MENU5",6);'.$newline;
//$configData = $configData . '$google_map_key="'.$_POST['txtgooglemapkey'].'";                                
// key to use google map API http://code.google.com/apis/maps/signup.html'.$newline;
$configData .= '?>'. $newline ;
?>
<?php

        if ($handle = fopen($basedir."site-monitor.conf.php", 'w')) {
                fwrite($handle, $configData);
                fclose($handle);
	}
// run site cron if cycle interval was changed
	if ($newinterval == "yes"){
		$dummy=exec('sudo '. $basedir .'run-site-cron.php');
	}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// save the name mappings	|
// ++++++++++++++++++++++++++++++
$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);
$query ="SELECT * FROM NAME_MAPING WHERE EDITABLE = 1";
$result=mysqli_query($conn,$query);
while ($row = mysqli_fetch_assoc($result)){
        $sql = "UPDATE NAME_MAPING SET DISPLAY_NAME = '" ;
        $sql .= $_POST[$row['DB_FIELD_NAME']]. "' WHERE DB_FIELD_NAME = '" .$row['DB_FIELD_NAME']. "'";
        $query = mysqli_query($conn,$sql);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//****************************************
//	Log that Change was made	**
//**********************************************************************//                
	$err_msg=" - NETz config  Changed By ".$_SESSION['user'];	//
	error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);	//
//**********************************************************************//
ob_clean();
//echo $_POST['support-centers.txt'];
//print_r($_POST);
//header("Location:".$_SESSION['secure'] . $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']). "/" . "main.php");
header("Location: main.php");
ob_end_flush();

?>

</body></html>
