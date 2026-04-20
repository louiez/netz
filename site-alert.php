#!/usr/bin/php -q
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
require('/usbdrive_2tb/var/www/html/netzlive/ping-test.php');
require_once('/usbdrive_2tb/var/www/html/netzlive/site-monitor.conf.php');

// Mail header Optional but required to stop being flagged as spam by some servers
$headers = "From: ".$smtp_from_address."\r\n" .
           "Reply-To: ".$smtp_from_address."\r\n" .
           "X-Mailer: PHP/" . phpversion();
// Init email $to variable
$to="";

function monalive($ip){
        global $basedir;
        global $netzlogs;
	// if the IP is not blank test it is alive else just return it true
        if (trim($ip) != ""){
                $time = exec($basedir.'fping/fping -C 1 -t 2000 '.$ip.' 2>/dev/null  | cut -d " " -f 6');
                if ($time == ""){
echo "false";
                        return "unknown";
                }elseif ($time >= 0){
echo "true";
                        return "up";
                }else {
                        error_log(date('Y-m-d G:i:s')."- monitor test ip ".$ip." is unusable"."\n", 3, $netzlogs."alert.log");
                        return "down";
                }
        }else{
                return "not set";
        }
}

$logfile = $netzlogs."alert.log";
set_time_limit(600);
$alerttype="";

$conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//@mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");

//--------------------------------------------------------------//
//	Check if NETz is online before checking down sites	//
//--------------------------------------------------------------//
$mononline1_check=monalive($mononline1);
$mononline2_check=monalive($mononline2);
$mononline3_check=monalive($mononline3);
if ($mononline1_check=="down" || $mononline2_check=="down" || $mononline3_check=="down"){	
echo "hit";
	echo "Massive down sites";										
	$sql = "SELECT * FROM USERS WHERE ACCESSLEVEL >= 9 ";							
	$result2 = mysqli_query($conn,$sql);												
	while ($row2 = mysqli_fetch_assoc($result2)) {                    
		$email = trim($row2['EMAIL'] ?? '');
		echo $email;                                 
		if ($email !== '') {           
			$to .= $email . ',';  // Add comma *after* trimming
		}                                   
	}
	// Clean up trailing comma at the end
	$to = rtrim($to, ',');

echo "91 ".$to;
	$message=" No reply from one or more of the Monitor Check IPs  \n";
	$message .= $mononline1." $mononline1_check\n";
	$message .= $mononline2."$mononline2_check\n";
	$message .= $mononline3."$mononline3_check\n";
	$subject = "Monitor not online"; 
	if(!mail($to, $subject, $message, $headers)) {
		echo 'Email sent successfully.';
	} else {
		echo 'Failed to send email.';
	}
	// Build error message
	$err_msg = 'echo "'.date('Y-m-d G:i:s');
	$err_msg .= ' - No reply from one or more of the Monitor Check IPs " >> ';
	$err_msg .= $netzlogs .'alert.log';
	system($err_msg); 
	die;													
}														
//---------------------------------------------------------------------------------//


//--------------------------------------------------------------#
// Find down sites that have not had an alert sent              #
//--------------------------------------------------------------#
$query="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_ENABLE = 1 and MONITOR_STATUS != 0 AND ALERT_SENT = 0";
$result=mysqli_query($conn,$query);
$mailaddresstest = "";
//--------------------------------------------------------------------------------------------------#
//    Less than $mass_alert_threshold sites down for this ping cycle - no major failure             #
//--------------------------------------------------------------------------------------------------#
if (mysqli_num_rows($result) <= $mass_alert_threshold) {
        while ($row = mysqli_fetch_assoc($result)){
		$alerttype="";
		$mailaddresstest = "";
		if ($row['ALERT_SENT'] != 1 && $row['MONITOR_STATUS'] > $row['MONITOR_ALERT_CYCLES'] && $alert_enable == "ON"){
                        // VIP email
                        $sql = "SELECT * FROM ALERTEMAILS WHERE LOCATION = '".trim($row['SITE_ID'])."' ";
                        $result2 = mysqli_query($conn,$sql);
                        while ($row2 = @mysqli_fetch_assoc($result2)){
                                //if ($row['SUPPORT_ALERT_OFFLINE'] == 1){
                                if ($row['VIP_ALERT_OFFLINE'] == 1){
echo "VIP ";
					$alerttype=" VIP "; // for logging
					//$emails=explode(";",$row2['EMAIL']);
					$emails = explode(";", $row2['EMAIL'] ?? '');
					foreach ($emails as $email) {
						$email = trim($email);
						if ($email !== '') {
							$to .= $email . ','; // append just the current email
							$mailaddresstest .= ' ' . $email;
						}
					}
					// remove trailing comma from $to
					$to = rtrim($to, ',');
                                }
                        }
                        // Support email
                        $sql = "SELECT * FROM ALERTEMAILS WHERE LOCATION = '".trim($row['SUPPORT_CENTER'])."' ";
                        $result2 = mysqli_query($conn,$sql);
                        while ($row2 = @mysqli_fetch_assoc($result2)){
                                //if ($row['VIP_ALERT_OFFLINE'] == 1){
                                if ($row['SUPPORT_ALERT_OFFLINE'] == 1){
echo "support ";
					$alerttype=$alerttype."Support "; // for logging
					$emails = explode(";", $row2['EMAIL'] ?? '');
					foreach ($emails as $email) {
						$email = trim($email);
						if ($email !== '') {
							$to .= $email . ',';                   // Append just the current email
							$mailaddresstest .= ' ' . $email;      // Append to test string
						}
					}
					// remove trailing comma from $to
					$to = rtrim($to, ',');


                                }
                        }
			if (trim($mailaddresstest) == ""){$mailaddresstest = "empty";}
                        if ($row['MONITOR_IP_FIELD'] != "") {
				$ip = $row[$row['MONITOR_IP_FIELD']];
			} else {
				$ip = $row[SITE_IP_DEFAULT];
			}
                        $store=$row[SITE_ID_DEFAULT];
                        echo $store."  ". $row['MONITOR_STATUS']."\n";
                        $message = "Site ". $store . "\n";
                        $message .= "IP   ". $ip. "\n";
                        $message .= "Service Type   ". $row['SERVICE_TYPE']. "\n";
                        $message .= "Total Alerts   ". $row['TOTAL_ALERTS_SENT']. "\n";
			$message .= "Group   ". $row['GROUP_NAME']. "\n";
                        $subject=" ". $store. " OffLine";
                        sleep(1);
			// If the address is not empty
			if ($mailaddresstest != "empty"){
				if(!mail($to, $subject, $message, $headers)) {
                                	echo "ERROR - ";
					// Log error
					$err_msg=" - error sending the message ";
					$err_msg .= $alerttype." - " . $subject ;
					error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
                        	}else{
					$err_msg = " - Message sent for ".$alerttype . $subject ;
					$err_msg = $err_msg . " email ".$mailaddresstest;
					error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
                                	//Set the date of last Alert
                                	$date= date('Y-m-d G:i:s');
                                	$query="UPDATE MONITORINFO SET DATE_LAST_ALERT = '";
					$query .= $date."' WHERE ".SITE_ID_DEFAULT." = '".$store."'";
                                	mysqli_query($conn,$query);
                			// Log to ALERTLOGS
					$query ="INSERT INTO ALERTLOGS VALUES('".$store."', '";
					$query .=$row[SITE_IP_DEFAULT]."', '".$date."', 'email')";
					mysqli_query($conn,$query);
		                	// flag Alert sent
                                	$query="UPDATE MONITORINFO SET ALERT_SENT = 1 WHERE ";
					$query .= SITE_ID_DEFAULT." = '$store'";
                                	mysqli_query($conn,$query);
                                	// add to Total Alerts sent
                                	$query="UPDATE MONITORINFO ";
					$query .= "SET TOTAL_ALERTS_SENT = TOTAL_ALERTS_SENT + 1 WHERE ";
					$query .= SITE_ID_DEFAULT." = '$store'";
                                	mysqli_query($conn,$query);

                        	}
			//******************************************************************************//
			// Site is set not to send Alert email but we want to track when it Alerts down	//
			//******************************************************************************//
			}elseif ($mailaddresstest == "empty" && $row['ALERT_SENT'] == 0 ){
				// Log to the Alert log if no alerting enabled or email was empty
				$err_msg=" - No Alert Email sent - " . $subject . "  ".$row['ALERT_SENT'] ;
				error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
                                // flag Alert sent as 2 
                                $query="UPDATE MONITORINFO SET ALERT_SENT = 2 WHERE ";
				$query .= SITE_ID_DEFAULT." = '$store'";
                                mysqli_query($conn,$query);
				// add to Total Alerts sent
                                $query="UPDATE MONITORINFO";
				$query .= " SET TOTAL_ALERTS_SENT = TOTAL_ALERTS_SENT + 1 WHERE ";
				$query .= SITE_ID_DEFAULT." = '$store'";	
				mysqli_query($conn,$query);
				//Set the date of last Alert
				$date= date('Y-m-d G:i:s');
				$query="UPDATE MONITORINFO SET DATE_LAST_ALERT = '";
				$query .= $date."' WHERE ".SITE_ID_DEFAULT." = '".$store."'";
				mysqli_query($conn,$query);
				// Log to ALERTLOGS
				$date= date('Y-m-d G:i:s');
                                $query="INSERT INTO ALERTLOGS VALUES('".$store."', '";
				$query .= $row[SITE_IP_DEFAULT]."', '".$date."', 'alert')";
                                mysqli_query($conn,$query);
			}


                }
        }
}
//-----------------------------------------------------------------------------------------#
//      Major Failure - more than $mass_alert_threshold sites down for this ping cycle     #
//-----------------------------------------------------------------------------------------#
else{
	// only if mass alert is enabled
	if ($alert_enable == "ON"){
        echo "Massive down sites";
	$sql = "SELECT * FROM USERS WHERE ACCESSLEVEL >= 9 ";
	$result2 = mysqli_query($conn,$sql);
	while ($row2 = mysqli_fetch_assoc($result2)) {
		$email = trim($row2['EMAIL'] ?? '');
		echo $email;
		if ($email !== '') {
			$to .= $email . ',';
		}
	}
	// remove trailing comma from $to
	$to = rtrim($to, ',');

        // Add list of all sites down
        $message= mysqli_num_rows($result). " sites offline\n\n";
        $message .= "Site ID  \tIP Address  \tService Type  \tInternet Provider\n";
        while ($row = mysqli_fetch_assoc($result)){
                $message .= $row[SITE_ID_DEFAULT]."\t". $row[SITE_IP_DEFAULT];
                $message .=  "\t" . $row['SERVICE_TYPE'] ;
                $message .=  "\t" . $row['INET_PROVIDER']. "\n";
        }
        $subject= mysqli_num_rows($result). " sites offline";
	if (!mail($to, $subject, $message, $headers)) {
		$error_message='echo "'.date('Y-m-d G:i:s'). ' - error sending Message ';
		$error_message .= $subject . '" >> ' . $netzlogs .'alert.log';	
                system($error_message);
        }
        else{
		$error_message= 'echo "'.date('Y-m-d G:i:s'). ' - Message sent for ';
		$error_message .= $subject . '" >> ' . $netzlogs .'alert.log';
                system($error_message);
        }
	}
}

//###############################################################
//###############################################################
//								#
// Check for sites that are back up after sending alert     	#
//								#
//###############################################################
//###############################################################



// Check for sites that are back online and flagged as alert sent
$query="SELECT * FROM ".SITE_INFO_TABLE." JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_ENABLE = 1 ";
$query= $query . "and ALERT_SENT > 0 and MONITOR_STATUS = 0 ";
$query= $query . "AND (SUPPORT_ALERT_ONLINE = 1 OR VIP_ALERT_ONLINE = 1)";
$result=mysqli_query($conn,$query);

$from='NETz '.php_uname("n") .'<'. $smtp_from_address.'>';


// roll through all sites online flagged as alert sent from query above
while ($row = mysqli_fetch_assoc($result)){
	$alerttype="";
        // VIP email 
        $sql = "SELECT * FROM ALERTEMAILS WHERE LOCATION = '".$row['SITE_ID']."' ";
        $result2 = mysqli_query($conn,$sql);
        while ($row2 = @mysqli_fetch_assoc($result2)){
                if ($row['VIP_ALERT_ONLINE'] == 1){
			$alerttype=" VIP "; // for logging
			$emails=explode(";",$row2['EMAIL']?? '');
			foreach ($emails as $email){
				if ( trim($email) != ""){
					$to.=trim($email). ','; 		 // add comma
					$mailaddresstest = $mailaddresstest ." ". trim($email);
                                }
			}
                }
        }
        // Support email
        $sql = "SELECT * FROM ALERTEMAILS WHERE LOCATION = '".$row['SUPPORT_CENTER']."' ";
        $result2 = mysqli_query($conn,$sql);
        while ($row2 = @mysqli_fetch_assoc($result2)){
		if ($row['SUPPORT_ALERT_ONLINE'] == 1){
			$alerttype= $alerttype."support "; // for logging
			$emails = explode(";", $row2['EMAIL'] ?? '');
			foreach ($emails as $email) {
				$email = trim($email);
				if ($email !== '') {
					$to .= $email . ',';                     // add comma
					$mailaddresstest .= ' ' . $email;
				}
			}
			

                }
        }
	if (trim($mailaddresstest) == ""){$mailaddresstest = "empty";}
	if ($row['MONITOR_IP_FIELD'] == "")$ip=$row[SITE_IP_DEFAULT]; else $ip = $row[$row['MONITOR_IP_FIELD']];
	$store=$row[SITE_ID_DEFAULT];
        echo $store."  ". $row['MONITOR_STATUS']."\n";
        $message = "Site ". $store . "\n";
        $message .= "IP   ". $ip. "\n";
	$message .= "Group   ". $row['GROUP_NAME']. "\n";
        $subject=$store. " Back online";
        sleep(1);
	if ($mailaddresstest != "empty"){
		if(!mail($to, $subject, $message, $headers)) {
			$err_msg=' - Error sending message - '.$alerttype.$subject.' - ' ;
			error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
        	}else{
			$err_msg=' - Message sent for '.$alerttype . $subject  ;
                	error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
			$query="UPDATE MONITORINFO SET ALERT_SENT = 0 WHERE ".SITE_ID_DEFAULT." = '$store'";
			mysqli_query($conn,$query);
        	}
	}else{
		// Log to the Alert log if no alerting enabled or email was empty
		$err_msg=" - No Alert Email sent - " . $subject ;
		error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);			
		$query="UPDATE MONITORINFO SET ALERT_SENT = 0 WHERE ".SITE_ID_DEFAULT." = '$store'";
		mysqli_query($conn,$query);
	}
}
// Cleanup alert sent flag for sites removed from alerting.... but are back online
// if alerting was reenabled and the site went down no alert was sent 
$query="SELECT * FROM ".SITE_INFO_TABLE." JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_ENABLE = 1 ";
$query= $query . "and ALERT_SENT > 0 and MONITOR_STATUS = 0 ";
$result=mysqli_query($conn,$query);

while ($row = mysqli_fetch_assoc($result)){
	$query="UPDATE MONITORINFO SET ALERT_SENT = 0 WHERE ";
	$query .= SITE_ID_DEFAULT." = '".$row[SITE_ID_DEFAULT]."'";
	mysqli_query($conn,$query);
}
mysqli_close($conn);
?>

