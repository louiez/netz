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
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL); 

include_once('auth.php');
include_once("site-monitor.conf.php");

$logfile = $netzlogs."db-change.log";
$errlogfile = $netzlogs."netz.log";
// Connect to server
	$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
		if (!$conn) {die('Could not connect: ' . mysqli_error());}
// Open database	
//	mysqli_select_db(NETZ_DATABASE);
	$results = mysqli_query($conn,"SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) ");
		if (!$results) {die('Query failed: ' . mysqli_error());}
function change_made($feild,$posttxt){
        // declare the global variables above as global here too
        global $logfile;
	global $conn;
        $sql="SELECT ".$feild." FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE SITE_ID = '".$_POST['txtsite']."'";
        $r=mysqli_query($conn,$sql);
        $rw = mysqli_fetch_assoc($r);
        if ($posttxt != $rw[$feild]){
                $err_msg=" ".$_POST['txtsite']." - " .$feild." Changed from (".$rw[$feild].") to (".$posttxt. ") By ".$_SESSION['user'];
                error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
        }
}
	
// Load variables with posted data
	$ipfeild=trim($_POST['txtipfeild']);
	$montimeout=trim($_POST['txtmontimeout']);
	$monalertcycles=trim($_POST['txtmonalertcycles']);
	$site=trim($_POST['txtsite']);
	$vipemail=trim($_POST['txtvipemail']);
	$vipename = isset($_POST['txtvipename']) ? $_POST['txtvipename'] : '';
	$vipename = is_string($vipename) ? trim($vipename) : '';
	$monenable=trim($_POST['chkmonitorenable']);
	if ($monenable == "ON") {$monenable = 1;} else {$monenable = 0; }
	$alertoffline=$_POST['chkalertoffline'];
	if ($alertoffline== "ON") {$alertoffline= 1;} else {$alertoffline= 0; }
	$alertonline=isset($_POST['chkalertonline']) ? $_POST['chkalertonline'] : '';
	if ($alertonline== "ON") {$alertonline= 1;} else {$alertonline= 0; }
	$vipoffline=isset($_POST['chkvipoffline']) ? $_POST['chkvipoffline'] : '';
	$vipoffline = is_string($vipoffline) ? trim($vipoffline) : '';
	if ($vipoffline== "ON") {$vipoffline= 1;} else {$vipoffline= 0; }
	$viponline=isset($_POST['chkviponline']) ? $_POST['chkviponline'] : '';
	$viponline = is_string($viponline) ? trim($viponline) : '';
	if ($viponline== "ON") {$viponline= 1;} else {$viponline= 0; }
	$alertsent = isset($_POST['chkalertsent']) ? $_POST['chkalertsent'] : 0;


	if ($alertsent== "ON") {$alertsent= 1;} else {$alertsent= 0; }
// Load HTTP Variables with posted data
	$httpenable=isset($_POST['chkmonitorhttpenable']) ? $_POST['chkmonitorhttpenable'] : '';
	if ($httpenable == "ON") {$httpenable = 1;} else {$httpenable = 0; }
	$httpipfeild=trim($_POST['txthttpipfeild']);  
	$httpport=$_POST['txtmonhttpport'];  
	$httpport = is_string($httpport) ? trim($httpport) : '';
	$httpssl=isset($_POST['chkssl']) ? $_POST['chkssl'] : '';
	$httpssl = is_string($httpssl) ? trim($httpssl) : '';
	if ($httpssl == "ON") {$httpssl = 1;} else {$httpssl = 0; }
	$httptimeout=trim($_POST['txtmonhttptimeout']);  
	$httppage=trim($_POST['txtmonhttppage']); 
	$httpcontent=trim($_POST['txtmonhttpcontent']); 
// format the date for mysqli
	//$date=date('Y-m-d G:i:s');

	// Update MONITOR_IP_FIELD
	change_made("MONITOR_IP_FIELD",$ipfeild);
	$sql = "UPDATE MONITORINFO SET MONITOR_IP_FIELD = '" ;
	$sql =$sql . $ipfeild. "' WHERE SITE_ID = '" .$site. "'";
	$query = mysqli_query($conn,$sql);

        // Update MONITOR_HTTP_IP_FIELD
	change_made("MONITOR_HTTP_IP_FIELD",$httpipfeild);
        $sql ="Update MONITORINFO Set MONITOR_HTTP_IP_FIELD = '";
        $sql =$sql .$httpipfeild ."' WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

	
	// Update MONITOR_ENABLE
	//******************************************************************************************************//
	//	Log if Monitor was enabled or disabled in a more human way... not zero and one geek crap	//
	//******************************************************************************************************//
        $sql="SELECT MONITOR_ENABLE FROM MONITORINFO WHERE SITE_ID = '".$_POST['txtsite']."'";			//
        $r=mysqli_query($conn,$sql);											//
        $rw = mysqli_fetch_assoc($r);										//
	// see if the monitor enable has changed 
        if ($monenable != $rw['MONITOR_ENABLE']){								//
		// start by clearing the ALERT_SENT flag
                $sql = "UPDATE MONITORINFO SET ALERT_SENT  = 0 WHERE SITE_ID = '" .$_POST['txtsite']. "'";
                $query = mysqli_query($conn,$sql);
		// clear the ALERT_SENT check mark so it don't remember
		$alertsent = 0;
                if ($monenable == 1){										//
                        $err_msg=" ".$_POST['txtsite']." - Monitor has been Enabled By ".$_SESSION['user'];	//
			error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);				//
		//******************************|								//
		//  Round Robin Database (RRD)	|								//
        	//**********************************************************************************************//
			// cleanup valid site names to valid filenames						//
			// NETz allows names that may not be legal as file names				//
  			$allowed = '/[^a-z0-9\\.\\-\\_\\\\]/i';							//
			$rrdfilename=preg_replace($allowed,"",$site);						//
			$rrdfilename= $basedir.'rrd/'.$rrdfilename.'.rrd';					//
                      	// check if there is an RRD database... create if not					//
			if ( ! is_readable($rrdfilename)) {							//
				$err_msg=" - ".$rrdfilename." Created By ".$_SESSION['user'];			//
				error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $errlogfile);			//
				$cmd='/usr/bin/rrdtool create '.$rrdfilename.' --step 900 \
				DS:rtime:GAUGE:1200:-5:5000 \
				RRA:AVERAGE:0.5:1:5000 \
				RRA:AVERAGE:0.5:6:5000 \
				RRA:AVERAGE:0.5:24:5000 \
				RRA:AVERAGE:0.5:288:5000 \
				RRA:MAX:0.5:1:5000 \
				RRA:MAX:0.5:6:5000 \
				RRA:MAX:0.5:24:5000 \
				RRA:MAX:0.5:288:5000';								//
				exec($cmd);									//
			}			
								//
                }else{												//
                        $err_msg=" ".$_POST['txtsite']." - Monitor has been Disabled By ".$_SESSION['user'];	//
			error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);				//
                }												//
        }													//
	//******************************************************************************************************//

        $sql ="Update MONITORINFO SET MONITOR_ENABLE = ";
        $sql =$sql .$monenable ." WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

	// Update MONITOR_HTTP_ENABLE
        //******************************************************************************//
        //      Log if HTTP Monitor was enabled or disabled in a more human way... 	//
        //**************************************************************************************************************//
        $sql="SELECT MONITOR_HTTP_ENABLE FROM MONITORINFO WHERE SITE_ID = '".$_POST['txtsite']."'";                	//
        $r=mysqli_query($conn,$sql);                                                                                   	//
        $rw = mysqli_fetch_assoc($r);                                                                            	//
        if ($httpenable != $rw['MONITOR_HTTP_ENABLE']){       								//
                if ($httpenable == 1){                                                                          	//
                        $err_msg=" ".$_POST['txtsite']." - HTTP Monitor has been Enabled By ".$_SESSION['user'];	//
                        error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);                              	//
                }else{                                                                                          	//
                        $err_msg=" ".$_POST['txtsite']." - HTTP Monitor has been Disabled By ".$_SESSION['user'];	//
                        error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);                              	//
                }                                                                                               	//
        }                                                                                                       	//
        //**************************************************************************************************************//
        $sql ="Update MONITORINFO Set MONITOR_HTTP_ENABLE = ";
        $sql =$sql .$httpenable ." WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

	// Update MONITOR_TIMEOUT
	change_made("MONITOR_TIMEOUT",$montimeout);
	$sql ="UPDATE MONITORINFO SET MONITOR_TIMEOUT = '";
        $sql =$sql .$montimeout ."' WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

        // Update MONITOR_ALERT_CYCLES
        change_made("MONITOR_ALERT_CYCLES",$montimeout);
        $sql ="UPDATE MONITORINFO SET MONITOR_ALERT_CYCLES = '";
        $sql =$sql .$monalertcycles ."' WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

        // Update MONITOR_HTTP_TIMEOUT
	change_made("MONITOR_HTTP_TIMEOUT",$httptimeout);
        $sql ="UPDATE MONITORINFO SET MONITOR_HTTP_TIMEOUT = '";
        $sql =$sql .$httptimeout ."' WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

        // Update MONITOR_HTTP_PORT
	change_made("MONITOR_HTTP_PORT",$httpport);
        $sql ="UPDATE MONITORINFO SET MONITOR_HTTP_PORT = '";
        $sql =$sql .$httpport ."' WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

        // Update MONITOR_HTTP_SSL
        change_made("MONITOR_HTTP_SSL",$httpssl);
        $sql ="UPDATE MONITORINFO SET MONITOR_HTTP_SSL = '";
        $sql =$sql .$httpssl ."' WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

        // Update MONITOR_HTTP_PAGE
	change_made("MONITOR_HTTP_PAGE",$httppage);
        $sql ="UPDATE MONITORINFO SET MONITOR_HTTP_PAGE = '";
        $sql =$sql .$httppage ."' WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

        // Update MONITOR_HTTP_CONTENT
	change_made("MONITOR_HTTP_CONTENT",$httpcontent);
	$sql ="UPDATE MONITORINFO SET MONITOR_HTTP_CONTENT = '";
        $sql =$sql .$httpcontent ."' WHERE SITE_ID = '".$site. "'";
        mysqli_query($conn,$sql);

	// Clear the Monitor status
	if ($monenable == 0){
		$sql = "UPDATE MONITORINFO SET  MONITOR_STATUS = 0 WHERE SITE_ID = '".$site. "'";
		mysqli_query($conn,$sql);
	}	
	
	// Clear the HTTP status
        if ($httpenable == 0){
                // clear the monitor status so alerts don't trigger
                $sql = "UPDATE MONITORINFO SET  MONITOR_HTTP_STATUS = 0 WHERE SITE_ID = '".$site. "'";
                mysqli_query($conn,$sql);
        }

	// Insert VIP Info if it don't already exist
		$sql="SELECT * FROM ALERTEMAILS WHERE LOCATION = '" .$site. "'"; 
		$query = @mysqli_query($conn,$sql);
		// Get the data for checks below
		$rw = mysqli_fetch_assoc($query);
		if (@mysqli_num_rows($query) == 0 && $vipemail != "") 
		{
			$sql = "INSERT INTO ALERTEMAILS SET EMAIL = '" . $vipemail. "' , LOCATION = '" .$site. "' , TYPE = 'VIP'";
			$query = mysqli_query($conn,$sql);
                        $err_msg=" ".$site." - VIP Email (".$vipemail. ")added  By ".$_SESSION['user'];
                        error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
		}elseif ($vipemail != "" && $vipemail != $rw['EMAIL'] ) {
			//$rw = mysqli_fetch_assoc($query);
			$err_msg=" ".$site." - VIP Email Changed from (".$rw['EMAIL'].") to (".$vipemail. ") By ".$_SESSION['user'];
			error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);	
			$sql = "UPDATE ALERTEMAILS SET EMAIL = '" . $vipemail. "' WHERE LOCATION = '" .$site. "'";
			$query = mysqli_query($conn,$sql);
			$sql = "UPDATE ALERTEMAILS SET TYPE = 'VIP' WHERE LOCATION = '" .$site. "'";
			$query = mysqli_query($conn,$sql);
		}elseif ($vipemail == "" && $rw['EMAIL'] != ""){
			//$rw = mysqli_fetch_assoc($query);
			$sql = "DELETE FROM ALERTEMAILS WHERE LOCATION = '" .$site. "'";
			$query = mysqli_query($conn,$sql);
                        $err_msg=" ".$site." - VIP Email (".$rw['EMAIL']. ") deleted  By ".$_SESSION['user'];
                        error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
		}
	
	// Update SUPPORT_ALERT_OFFLINE  
		change_made("SUPPORT_ALERT_OFFLINE",$alertoffline);
		$sql = "UPDATE MONITORINFO SET SUPPORT_ALERT_OFFLINE = " . $alertoffline. " WHERE SITE_ID = '" .$site. "'";
		$query = mysqli_query($conn,$sql);

	// Update SUPPORT_ALERT_ONLINE  
		change_made("SUPPORT_ALERT_ONLINE",$alertonline);
		$sql = "UPDATE MONITORINFO SET SUPPORT_ALERT_ONLINE  = " . $alertonline. " WHERE SITE_ID = '" .$site. "'";
		$query = mysqli_query($conn,$sql);
		
	// Update VIP_ALERT_OFFLINE  
		change_made("VIP_ALERT_OFFLINE",$vipoffline);
		$sql = "UPDATE MONITORINFO SET VIP_ALERT_OFFLINE = " . $vipoffline. " WHERE SITE_ID = '" .$site. "'";
		$query = mysqli_query($conn,$sql);

	// Update VIP_ALERT_ONLINE  
		change_made("VIP_ALERT_ONLINE",$viponline);
		$sql = "UPDATE MONITORINFO SET VIP_ALERT_ONLINE  = " . $viponline. " WHERE SITE_ID = '" .$site. "'";
		$query = mysqli_query($conn,$sql);

	// Update ALERT_SENT 
		change_made("ALERT_SENT",$alertsent);
		$sql = "UPDATE MONITORINFO SET ALERT_SENT  = " . $alertsent. " WHERE SITE_ID = '" .$site. "'";
		$query = mysqli_query($conn,$sql);

	// Update Last Change By
	        $sql = "UPDATE SITEDATA SET LAST_CHANGE_BY  = '" .$_SESSION["user"] . "' WHERE SITE_ID = '" .$site. "'";
                $query = mysqli_query($conn,$sql);
        // Update Date Last Change
//                $sql = "UPDATE SITEDATA SET LAST_CHANGE_DATE  = '" .strftime("%Y-%m-%d %H:%M:%S",time()) . "' WHERE SITE_ID = '" .$site. "'";
		$sql = "UPDATE SITEDATA SET LAST_CHANGE_DATE  = '" . date("Y-m-d H:i:s") . "' WHERE SITE_ID = '" . $site . "'";

                $query = mysqli_query($conn,$sql);

//$err_msg=" ".$site." Monitor/Alert Options changed By ".$_SESSION['user'];
//error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);


?>
<html>
<body onload="javascript: window.close()">
</body></html>
