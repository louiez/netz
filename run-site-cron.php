#!/usr/bin/php -q
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

include_once('site-monitor.conf.php');
set_time_limit(1200);
//==============================================================+
//      grab new copy of MAC address info from ieee site        |
//==============================================================================================================//
system('rm '.$basedir.'tools/oui.txt');                                                 			//
system('wget -q http://standards-oui.ieee.org/oui/oui.txt -O '.$basedir.'tools/oui.txt https://standards.ieee.org/develop/regauth/oui/oui.txt');	//
//==============================================================================================================//
// log to Netz system log
system('echo "'.date('Y-m-d G:i:s'). ' - downloaded latest MAC address file from IEEE" >> '. $netzlogs .'netz.log');

$conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//@mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");

//======================+
// Get Monitor totals	|
//======================================================================================================//
$query="SELECT count(*) as cnt, AVG(MONITOR_TIMEOUT) as avg  FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_ENABLE = 1";	//
$result=mysqli_query($conn,$query);										//
$rows = mysqli_fetch_assoc($result);									//
$montotal=$rows['cnt'];											//
//monmilisec= Adverage * 3 pings per site								//
$monmilisec=$rows['avg'] * 3;										//
echo "\$montotal sites: ". $montotal . "\n";								//
echo "\$monmilisec (ms): ". $monmilisec . "\n\n";							//
//======================================================================================================//

//==============================+
//	Get HTTP Monitor totals	|
//==============================================================================================================//
$query="SELECT count(*) as cnt, AVG(MONITOR_HTTP_TIMEOUT) as avg  FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_HTTP_ENABLE = 1";	//
$result=mysqli_query($conn,$query);											//
$rows = mysqli_fetch_assoc($result);										//
// Total number of sites flagged for HTTP Monitor								//
$httptotal=$rows['cnt'];											//
// Average miliseconds per site for HTTP Monitor								//
$httpmilisec=$rows['avg'];											//
// there is 2 timeouts for HTTP monitor, Connect time out and Read Timeout					//
// this calculates the fixed read timeout set in the tcp-test.php script					//
$http_read_milisec= 2000 ;											//
$http_total_milisec = $httpmilisec + $http_read_milisec;							//
echo "\$httptotal sites: ". $httptotal . "\n";									//
echo "\$httpmilisec (ms): ".$httpmilisec . "\n\n\n";								//
echo "\$http_total_milisec: ". $http_total_milisec . "\n";							//
//==============================================================================================================//

// Get the total seconds from all the milisecond variables above
//$totalseconds=(($montotal * $monmilisec) + ($httptotal * ($httpmilisec + $http_read_milisec))) / 1000 ;
$totalseconds=(($montotal * $monmilisec) + ($httptotal * $http_total_milisec)) / 1000 ;
echo "total seconds needed to monitor all sites: ".$totalseconds . "\n";
$total = $montotal;
/*##############################################################################################//
	This takes the $moncycleinterval value from the config file and multipy by 60 seconds	//
	then multipy that by .8 (80%) to get total time we have to complete the monitor cycle	//
	this gives us a 20% margin for other processing						*/
$cycleinterval=round(($moncycleinterval * 60) * .80);						//
//##############################################################################################//

/*#######################################################################################
         Calculate total numbers of sites to run per cron that should safetly complete  #
         before the next configurable monitor cycle starts                            	#
         total seconds to complete all sites ($totalseconds) + 15% cush			#
#######################################################################################*/
//$upper = round(($totalseconds * 1.15) / $cycleinterval) + 1 ;
$upper = ceil(($totalseconds * 1.15) / $cycleinterval) ;
//echo $upper;
// =====================================================+
//      create temp crontab removing old monitor crons  |
// =====================================================+
exec("crontab -l | egrep -v 'netz_mon.tmp'  > /tmp/cron.tmp");
// =============================================================+
//      Calculate cron times string                             |
// =============================================================+
$cnt=60 - $moncycleinterval;
$crontimestring= $cnt;
while($cnt > 0)
{
echo ".";
        $cnt= $cnt - $moncycleinterval;
        if ($cnt >= 0){$crontimestring= $cnt . ",".$crontimestring;}
}

// =============================================================+
//      Add new monitor cron entries and add to cron.tmp	|
// =============================================================+=======//
// Clear the mon.tmp file
exec("cat /dev/null > ".$basedir."netz_mon.tmp");
echo "// Add the server health job\n";
$server_health = $basedir."servers-info.sh > ".$basedir."server-info.html";
exec("echo '".$server_health."' > ".$basedir."netz_mon.tmp");
$cron_cmd = 'echo "'.$crontimestring.' * * * * ';
$cron_cmd .= $basedir . 'netz_mon.tmp';
$cron_cmd .= ' >/dev/null 2>&1" >> /tmp/cron.tmp'; 
exec($cron_cmd); 
for ($i=0; $i<=$upper; $i++){						//
	#$cron_cmd = 'echo "'.$crontimestring.' * * * * ';		//
	#$cron_cmd .= $basedir . 'site-check.php '.$i.' '.$upper ;	//
	#$cron_cmd .= ' 1>&2>/dev/null" >> /tmp/cron.tmp';		//
	#exec($cron_cmd);						//
	$cron_cmd = 'echo "'.$basedir . 'site-check.php '.$i.' '.$upper .'&" >> '.$basedir.'netz_mon.tmp';
	exec($cron_cmd); 
}									//
//======================================================================//
echo "Load crontab \n";
// =============================+
//      Load new crontab	|
// =============================+=======//
exec("crontab /tmp/cron.tmp");		//
sleep(2);				//
//======================================//
echo "Log \n";
//======================+
// Netz system logs	|
//======================+===============================================================================================//
system('echo "'.date('Y-m-d G:i:s'). ' - ' . $total. ' Total sites for monitoring'. '" >> ' . $netzlogs .'netz.log');	//
system('echo "'.date('Y-m-d G:i:s'). ' - ' . $upper. ' cron jobs created'. '" >> ' . $netzlogs .'netz.log');		//
//======================+===============================================================================================//

// =============================================+
//      Get date to delete old monitor logs     |
//      $logdays set in site-monitor.conf.php   |
// =============================================+===============================//
//        hour-----|  |--|min/sec |--month   |--day         year---|		//
$expired  = mktime(0, 0, 0, date("m"), date("d") - $logdays, date("Y"));	//
$expired  = date("Y-m-d G:i:s",$expired);					//
//==============================================================================//
echo "get count of deleted Monitor records\n";
echo "SELECT COUNT(*) FROM MONLOGS WHERE CHECK_DATE_TIME < '".$expired."'\n";
//==============================================+
//      Get count of deleted Monitor records    |
//==============================================+===============================================//
$result=mysqli_query($conn,"SELECT COUNT(*) FROM MONLOGS WHERE CHECK_DATE_TIME < '".$expired."'");	//
$fetch=mysqli_fetch_row($result);								//
$total=$fetch[0];										//
//==============================================================================================//
echo "Delete old monitor logs\n";
//======================================+
//      Delete old monitor logs         |
//======================================+===============================================================================//
system('echo "'.date('Y-m-d G:i:s'). ' - Deleting '.$total .' Monitor records" >> ' . $netzlogs .'netz.log');		//
$query="DELETE LOW_PRIORITY FROM MONLOGS WHERE CHECK_DATE_TIME < '".$expired."'";					//
//$query="DELETE FROM MONLOGS WHERE CHECK_DATE_TIME < '".$expired."'";
$result=mysqli_query($conn,$query);												//
system('echo "'.date("Y-m-d G:i:s"). ' - ' . $total . ' Monitor log Records Deleted" >> ' . $netzlogs .'netz.log');	//
system('echo "'.date('Y-m-d G:i:s'). ' - '. $logdays . ' Days of Monitor logs Retained" >> ' . $netzlogs .'netz.log');	//
//======================================+===============================================================================//

//======================================================+
//	Get count of deleted HTTP Monitor records	|
//======================================================+=======================================//
$result=mysqli_query($conn,"SELECT COUNT(*) FROM HTTPMONLOGS WHERE CHECK_DATE_TIME < '".$expired."'");	//
$fetch=mysqli_fetch_row($result);								//
$total=$fetch[0];										//
//======================================================+=======================================//
echo "Delete old HTTP monitor logs\n";

//======================================+
//	Delete old HTTP monitor logs	|
//======================================+=======================================================================================//
system('echo "'.date('Y-m-d G:i:s'). ' - Deleting '.$total .' HTTP Monitor records" >> ' . $netzlogs .'netz.log');		//
$query="DELETE LOW_PRIORITY FROM HTTPMONLOGS WHERE CHECK_DATE_TIME < '".$expired."'";						//
$result=mysqli_query($conn,$query);													//
system('echo "'.date("Y-m-d G:i:s"). ' - ' . $total . ' HTTP Monitor log Records Deleted" >> ' . $netzlogs .'netz.log');	//
system('echo "'.date('Y-m-d G:i:s'). ' - '. $logdays . ' Days of HTTP Monitor logs Retained" >> ' . $netzlogs .'netz.log');	//
//==============================================================================================================================//

//==============================================+
//	Get count of deleted Alert records	|
//==============================================+===============================================//
$result=mysqli_query($conn,"SELECT COUNT(*) FROM ALERTLOGS WHERE CHECK_DATE_TIME < '".$expired."'");	//
$fetch=mysqli_fetch_row($result);								//
$total=$fetch[0];										//
//==============================================================================================//

//==============================+
//	Delete old Alert logs	|
//==============================+=======================================================================================//
system('echo "'.date('Y-m-d G:i:s'). ' - Deleting '.$total .' Alert records" >> ' . $netzlogs .'netz.log');		//
$query="DELETE LOW_PRIORITY FROM ALERTLOGS WHERE CHECK_DATE_TIME < '".$expired."'";					//
$result=mysqli_query($conn,$query);												//
system('echo "'.date("Y-m-d G:i:s"). ' - ' . $total . ' Alert log Records Deleted" >> ' . $netzlogs .'netz.log');	//
system('echo "'.date('Y-m-d G:i:s'). ' - '. $logdays . ' Days of Alert logs Retained" >> ' . $netzlogs .'netz.log');	//
//======================================================================================================================//

//======================================+
//      Optimize and check database     |
//======================================================================================================================//
#echo "Database Check and Optimizing";                                                                   		//
#exec("cat /dev/null > ".$netzlogs."mysqlichk.tmp");                                                                      //
#exec("mysqlcheck -o -u ".NETZ_DB_USERNAME." -p".NETZ_DB_PASSWORD." NETz > ".$netzlogs."mysqlichk.tmp");  		//
#exec("echo \"\n******\" `date` \"******\" >> ".$netzlogs."mysqlichk.log");                                               //
#exec("cat ".$netzlogs."mysqlichk.tmp >> ".$netzlogs."mysqlichk.log");                                                     //
//======================================================================================================================//

//echo $total;
mysqli_close($conn);



?>
