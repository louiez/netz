#!/usr/bin/php -q
<?php
require('ping-test.php');
require('tcp-test.php');
include_once('site-monitor.conf.php');
// Check if the Monitor is enabled
if ($enablemonitor != "ON"){exit;}

// Check if upper and Lower arguments were passed
if ($argc < 2){exit;}

set_time_limit(600);
$logfile = $netzlogs."netz.log";
$conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//@mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");

$query="SELECT * FROM MONITORINFO ";
//$query .= "WHERE SITEDATA.SITE_ID = MONITORINFO.SITE_ID ";
$query .= "WHERE MONITOR_ENABLE = 1 and SITE_ID != ''";

$result=mysqli_query($conn,$query);
$total=mysqli_num_rows($result);
echo $total."\n";
$upper=round($total / $argv['2']) + 1;
$lower=round($upper * ($argv['1']));

$query="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) ";
//$query .= "WHERE SITEDATA.SITE_ID = MONITORINFO.SITE_ID ";
$query .= "WHERE MONITOR_ENABLE = 1 ";
$query .= "ORDER BY SITE_ID ASC LIMIT ".$lower." , ".$upper;
$result=mysqli_query($conn,$query);
add_plugin('site_check_start');
while ($row = mysqli_fetch_assoc($result))
{
	$ipfeild = $row['MONITOR_IP_FIELD'] ;
	if ($ipfeild == ""){$ipfeild = SITE_IP_DEFAULT;}
	$cfgServer = trim($row[$ipfeild]);
	$store=$row[SITE_ID_DEFAULT];
	$timeout=$row['MONITOR_TIMEOUT'];

	if ($cfgServer != "" )
	{

echo "---------------------------------------------\n";
	        // if the timeout feild is empty... set to the DB default 2000
	        // what a freaking problem this made.....
	        if ($timeout < 1000 || $timeout == ""){
	                $timeout="2000";
			$err_msg = date('Y-m-d G:i:s')."-".$store;
			$err_msg .= "- Invalid Monitor Timeout".$row['MONITOR_TIMEOUT']."\n";
	               	error_log($err_msg, 3, $logfile);
	        }
		// HTTP Monitor test
        	if ($row['MONITOR_HTTP_ENABLE'] == 1){
			if ($row['MONITOR_HTTP_PAGE'] == ""){$pg = "/";}else {$pg = $row['MONITOR_HTTP_PAGE'];}
                        // Get the IP for HTTP check
                        $httpipfeild = $row['MONITOR_HTTP_IP_FIELD'] ;
                        if ($httpipfeild == ""){$httpipfeild = SITE_IP_DEFAULT;}
			$httpcfgServer = $row[$httpipfeild];
                        $httpport = $row['MONITOR_HTTP_PORT'] ;
                        if ($httpport == ""){$httpport = "80";}
			// Get the HTTP Timeout in Miliseconds
                        $httptimeout = $row['MONITOR_HTTP_TIMEOUT'] ;
			// convert the miliseconds to seconds because the fsockopen call uses seconds
                        if ($httptimeout == ""){$httptimeout = 2;}else {$httptimeout = $httptimeout /1000;}
                        if ($httptimeout > 9){$httptimeout = 9;}
                        $rtn = tcp_mon($row['MONITOR_HTTP_SSL'],
					$httpcfgServer,$httpport,
					$row['MONITOR_HTTP_CONTENT'],
					$row['MONITOR_HTTP_PAGE'],
					$row['MONITOR_HTTP_TIMEOUT']);
		// $rtn[0] is the return trip time
		// $rtn[1] is the match to the conternt check or false
                // $rtn[2] is the error string from php
                // $rtn[3] is the error number from php
                // $rtn[4] is the length of data returned
			date_default_timezone_set('America/Chicago');
			//date_default_timezone_set('UTC');
			$date=date('Y-m-d G:i:s');
			$query = "INSERT INTO HTTPMONLOGS ";
			$query .= "VALUES ('$store','$cfgServer','$date','$rtn[0]','$rtn[1]','$rtn[4]','$rtn[2]')";
echo "\n".$query."\n";
			mysqli_query($conn,$query);
			$query = "UPDATE MONITORINFO";
			$query .= " SET MONITOR_HTTP_STATUS = $rtn[1] ";
			$query .= "WHERE SITE_ID = '".$store."'";
                	$time = $rtn[0];
/*
if ($rtn[1] == 1){
	$suctmp=explode("\r\n",$rtn[2]);
	for ($i=0; $i<count($suctmp); $i++){
		echo $i."  ". $suctmp[$i]."\n";
	}
}else{echo $rtn[2]."\n";}
*/
                	echo "Time: " . $rtn[0] . "\n";
                	echo "Sucess: " . $rtn[1] . "\n";
                	echo  $rtn[2] . "\n";
                	echo "Error Number: " . $rtn[3] . "\n";
		}
		$hit=0;
		for ($i=1; $i<=3; $i++){
			date_default_timezone_set('America/Chicago');
			$date=date('Y-m-d G:i:s');
                        $cmd=$basedir.'fping/fping -C 1 -t '.$timeout.' ';
                        $cmd.=$cfgServer.' 2>/dev/null  | cut -d " " -f 6';
                        $time = exec($cmd);

			//echo $timeout."-";
			if ($time == ""){
				 $time = -1;
			}elseif ($time >= 0){
				$time=round($time);
			}else {
				$err_msg = date('Y-m-d G:i:s')."-".$cfgServe;
				$err_msg .= "- Time returned from ping is unusable"."\n";
				error_log($err_msg, 3, $logfile);
				$time = -1;
			}
			// if connection was NOT sucessful
			if($time == -1){
		
               			$query = "INSERT INTO ".SITE_MON_TABLE;
				$query .= " VALUES ('$store','$cfgServer','$date',$time,0)";
                		mysqli_query($conn,$query);

				//*************************//
				// Add to RRDTOOL Database //***********************************//
				// cleanup valid site names to valid filenames                  //
				// NETz allows names that may not be legal as file names        //
				//**************************************************************//
				$allowed = '/[^a-z0-9\\.\\-\\_\\\\]/i';                         //
				$rrdfilename=preg_replace($allowed,"",$store);                  //
				$rrdfilename= $basedir.'rrd/'.$rrdfilename.'.rrd';              //
                	//******************************|                                                       //
                	//  Round Robin Database (RRD)  |                                                       //
                	//**************************************************************************************//
                        // cleanup valid site names to valid filenames                                          //
                        // NETz allows names that may not be legal as file names                                //
                        // check if there is an RRD database... create if not                                   //
                        if ( ! is_readable($rrdfilename)) {                                                     //
                                $err_msg=" - ".$rrdfilename." Created By site-check.php";                   //
                                error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);                   //
                                $cmd='/usr/bin/rrdtool create '.$rrdfilename.' --step 900 \
                                DS:rtime:GAUGE:1200:-5:5000 \
                                RRA:AVERAGE:0.5:1:5000 \
                                RRA:AVERAGE:0.5:6:5000 \
                                RRA:AVERAGE:0.5:24:5000 \
                                RRA:AVERAGE:0.5:288:5000 \
                                RRA:MAX:0.5:1:5000 \
                                RRA:MAX:0.5:6:5000 \
                                RRA:MAX:0.5:24:5000 \
                                RRA:MAX:0.5:288:5000';                                                          //
                                exec($cmd);                                                                     //
			}



				//								********//
				$ts=time();								//
				exec("/usr/bin/rrdtool update ".$rrdfilename." " .$ts.":".$time);	//
				//**********************************************************************//

				echo $store."-".$cfgServer."-".$date." - 0   ".$time." ms\n";
				
			}else{
				$query = "INSERT INTO ".SITE_MON_TABLE;
				$query .= " VALUES ('$store','$cfgServer','$date',$time,1)";
				mysqli_query($conn,$query);

                                //*************************//
                                // Add to RRDTOOL Database //***********************************//
                                // cleanup valid site names to valid filenames                  //
                                // NETz allows names that may not be legal as file names        //
                                //**************************************************************//
                                $allowed = '/[^a-z0-9\\.\\-\\_\\\\]/i';                         //
                                $rrdfilename=preg_replace($allowed,"",$store);                  //
                                $rrdfilename= $basedir.'rrd/'.$rrdfilename.'.rrd';              //
                        // check if there is an RRD database... create if not                                   //
                        if ( ! is_readable($rrdfilename)) {                                                     //

                                $err_msg=" - ".$rrdfilename." Created By site-check.php";                   //
                                error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);                   //
                                $cmd='/usr/bin/rrdtool create '.$rrdfilename.' --step 900 \
                                DS:rtime:GAUGE:1200:-5:5000 \
                                RRA:AVERAGE:0.5:1:5000 \
                                RRA:AVERAGE:0.5:6:5000 \
                                RRA:AVERAGE:0.5:24:5000 \
                                RRA:AVERAGE:0.5:288:5000 \
                                RRA:MAX:0.5:1:5000 \
                                RRA:MAX:0.5:6:5000 \
                                RRA:MAX:0.5:24:5000 \
                                RRA:MAX:0.5:288:5000';                                                          //
                                exec($cmd);                                                                     //
                        }
                                //                                                              ********//
                                $ts=time();                                                             //
                                exec("/usr/bin/rrdtool update ".$rrdfilename." " .$ts.":".$time);	//
                                //**********************************************************************//
				echo $store."-".$cfgServer."-".$date." - 1   ".$time." ms\n";
				$hit++;
			}
		}
		if ($hit > 0){
			// if we had at least one Ping come back (hit>0) 
			$query = "UPDATE MONITORINFO";
			$query .= " SET MONITOR_STATUS = 0 ";
			$query .= "WHERE SITE_ID = '".$store."'";
                        mysqli_query($conn,$query);

		}else{
			// we did not get any Pings back increment  MONITOR_STATUS 
			// to show how many failed monitor cycles were missed
			$query = "UPDATE MONITORINFO";
			$query .= " SET MONITOR_STATUS = MONITOR_STATUS + 1 ";
			$query .= "WHERE SITE_ID = '".$store."'";
			mysqli_query($conn,$query);
		}
	}else{
		error_log(date('Y-m-d G:i:s')."-".$store." set to monitor but IP is blank"."\n", 3, $logfile);
	}
}
add_plugin('site_check_end');
mysqli_close($conn);
?>
