#!/usr/bin/php -q
<?php
require('ping-test.php');
require('tcp-test.php');
include_once('site-monitor.conf.php');
set_time_limit(600);

mysql_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD);
@mysql_select_db(NETZ_DATABASE) or die( "Unable to select database");

$query="SELECT * FROM ".SITE_INFO_TABLE." WHERE MONITOR_ENABLE = 1 and ".SITE_IP_DEFAULT." != ''";
$result=mysql_query($query);
$total=mysql_numrows($result);
//$upper=round($total / $argv['2']) + 1;
$maxjobs=system('cat /etc/netz.conf');
$upper=round($total / $maxjobs) + 1;
$lower=round($upper * ($argv['1']));

//$query="SELECT * FROM ".SITE_INFO_TABLE." WHERE MONITOR_ENABLE = 1 and ".SITE_IP_DEFAULT." != '' ORDER BY `".SITE_IP_DEFAULT."` ASC LIMIT ".$lower." , ".$upper;
$query="SELECT * FROM ".SITE_INFO_TABLE." WHERE MONITOR_ENABLE = 1 ORDER BY SITE_ID ASC LIMIT ".$lower." , ".$upper;

$result=mysql_query($query);
while ($row = mysql_fetch_assoc($result))
{
	
	$ipfeild = $row['MONITOR_IP_FIELD'] ;
	if ($ipfeild == ""){$ipfeild = SITE_IP_DEFAULT;}
	$cfgServer = $row[$ipfeild];
	$store=$row[SITE_ID_DEFAULT];
	$timeout=$row['MONITOR_TIMEOUT'];
	if ($cfgServer != "" )
	{
                // HTTP Monitor test
                if ($row['MONITOR_HTTP_ENABLE'] == 1){
                        if ($row['MONITOR_HTTP_PAGE'] == ""){$pg = "/";}else {$pg = $row['MONITOR_HTTP_PAGE'];}
                        // Get the IP for HTTP check
                        $httpipfeild = $row['MONITOR_HTTP_IP_FIELD'] ;
                        if ($httpipfeild == ""){$httpipfeild = SITE_IP_DEFAULT;}
                        $httpcfgServer = $row[$httpipfeild];
//echo $httpcfgServer;
                        $rtn = tcp_mon($httpcfgServer,$row['MONITOR_HTTP_PORT'],$row['MONITOR_HTTP_CONTENT'],$row['MONITOR_HTTP_PAGE'],$row['MONITOR_HTTP_TIMEOUT']);

                        $date=date('Y-m-d G:i:s');
                        $query = "INSERT INTO HTTPMONLOGS VALUES ('$store','$cfgServer','$date','$rtn[0]','$rtn[1]','$rtn[4]','$rtn[2]')";
                        mysql_query($query);
                        $query = "UPDATE ".SITE_INFO_TABLE." SET MONITOR_HTTP_STATUS = $rtn[1] WHERE ".SITE_ID_DEFAULT." = '$store'";
                        $time = $rtn[0];
                        echo "Time: " . $rtn[0] . "\n";
                        echo "Sucess: " . $rtn[1] . "\n";
                        echo "Error String: " . $rtn[2] . "\n";
                        echo "Error Number: " . $rtn[3] . "\n";
                }


		$hit=0;
		//for ($i=0; $i<=$monitor_timeout; $i++)
		for ($i=1; $i<=3; $i++)
		{
			$date=date('Y-m-d G:i:s');
			//$time=pingsite($cfgServer);
			//unset($time);
			$time = exec($basedir.'fping/fping -C 1 -t '.$timeout.' '.$cfgServer.' 2>/dev/null  | cut -d " " -f 6');
			echo $timeout;
			if ($time == ""){ $time = -1;}else {$time=round($time);}
			// if connection was sucessful
			if($time == -1)
			{
		
               			$query = "INSERT INTO ".SITE_MON_TABLE." VALUES ('$store','$cfgServer','$date',$time,0)";
                		mysql_query($query);
				//$query = "INSERT INTO $site_down_tb VALUES ('$store','$cfgServer','$date',$time,0)";
				// mysql_query($query);
				//echo $store."-".$cfgServer."-".$date." - 0   ".$time." ms\n";
				
			}
			else
			{
				$query = "INSERT INTO ".SITE_MON_TABLE." VALUES ('$store','$cfgServer','$date',$time,1)";
				mysql_query($query);
				//$query = "INSERT INTO $site_down_tb VALUES ('$store','$cfgServer','$date',$time,1)";
			//echo $query."\n";
				//mysql_query($query);
				echo $store."-".$cfgServer."-".$date." - 1   ".$time." ms\n";
				$hit++;
			}
		}
		echo $hit."\n";
		//if ($hit == 3)
		if ($hit > 0)
		{
			//usleep(100);	
			//$query = "DELETE FROM $site_down_tb WHERE SITE_ID_DEFAULT = '$store'";
			//mysql_query($query);
//		}
//		if ($hit > 1)
//		{
			$query = "UPDATE ".SITE_INFO_TABLE." SET MONITOR_STATUS = 0 WHERE ".SITE_ID_DEFAULT." = '$store'";
                        mysql_query($query);

		}
		else
		{
			$query = "UPDATE ".SITE_INFO_TABLE." SET MONITOR_STATUS = MONITOR_STATUS + 1 WHERE ".SITE_ID_DEFAULT." = '$store'";
			mysql_query($query);
		}
	}
}
mysql_close();
?>
