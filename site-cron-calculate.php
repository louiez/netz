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
//**************************************************************//
//      grab new copy of MAC address info from ieee site        //
//==============================================================//======================//
system('rm '.$basedir.'tools/oui.txt');                                                 //
system('wget -O tools/oui.txt http://standards.ieee.org/regauth/oui/oui.txt');          //
//======================================================================================//
// log to Netz system log
system('echo "'.date('Y-m-d G:i:s'). ' - downloaded latest MAC address file from IEEE" >> '. $netzlogs .'netz.log');

mysql_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD);
@mysql_select_db(NETZ_DATABASE) or die( "Unable to select database");

// Get Monitor totals
$query="SELECT count(*) as cnt, AVG(MONITOR_TIMEOUT) as avg  FROM SITEDATA WHERE MONITOR_ENABLE = 1";
$result=mysql_query($query);
$rows = mysql_fetch_assoc($result);
$montotal=$rows['cnt'];
//monmilisec= Adverage * 3 pings per site
$monmilisec=$rows['avg'] * 3;

echo "\$montotal sites: ". $montotal . "\n";
echo "\$monmilisec (ms): ". $monmilisec . "\n\n";

// Get HTTP Monitor totals


$query="SELECT count(*) as cnt, AVG(MONITOR_HTTP_TIMEOUT) as avg  FROM SITEDATA WHERE MONITOR_HTTP_ENABLE = 1";
$result=mysql_query($query);
$rows = mysql_fetch_assoc($result);
$httptotal=$rows['cnt'];
$httpmilisec=$rows['avg'];

echo "\$httptotal sites: ". $httptotal . "\n";
echo "\$httpmilisec (ms): ".$httpmilisec . "\n\n\n";

$totalseconds=(($montotal * $monmilisec) + ($httptotal * $httpmilisec)) / 1000 ;
echo "total seconds: : ".$totalseconds . "\n";
$total = $montotal;
/*###############################################################################################
	This takes the $moncycleinterval value from the config file and multipy by 60 seconds	#
	then multipy that by .8 (80%) to get total time we have to complete the monitor cycle	#
	this gives us a 20% margin for other processing						#
###############################################################################################*/

$cycleinterval=round(($moncycleinterval * 60) * .80);
//echo "--------> ". $cycleinterval;
/*#######################################################################################
         Calculate total numbers of sites to run per cron that should safetly complete  #
         before the next 15min(900sec) round starts                                     #
         720 = the 900 sec less 20% cush                                                #
         3 pings per round * the configurable timeout                                   #
         * 10% is the estimate time to complete one site safetly                        #
*/                                                                              //      #
//$sites_per_cron = round($cycleinterval / ((3 * $monitor_timeout) * 1.10));                      //      #
//$upper=round($total / $sites_per_cron)+1;                                       //      #
//#######################################################################################
$upper = round(($totalseconds * 1.15) / $cycleinterval) + 1 ;
echo "Number of jobs ". $upper . "\n";
system('echo "'.trim($upper). '" > /etc/netz.conf');

// Netz system logs
system('echo "'.date('Y-m-d G:i:s'). ' - ' . $total. ' Total sites for monitoring'. '" >> ' . $netzlogs .'netz.log');
//system('echo "'.date('Y-m-d G:i:s'). ' - ' . $sites_per_cron. ' sites per cron'. '" >> ' . $basedir .'/logs/netz.log');
system('echo "'.date('Y-m-d G:i:s'). ' - ' . $upper. ' cron jobs created'. '" >> ' . $netzlogs .'netz.log');




// =============================================+
//      Get date to delete old monitor logs     |
//      $logdays set in site-monitor.conf.php   |
// =============================================+
//              hour-----|  minute--|  second--|  month---|     day--|          year-|
$expired  = mktime(0, 0, 0, date("m"), date("d") - $logdays, date("Y"));
$expired  = date("Y-m-d G:i:s",$expired);
// =====================================+
//      Get count of deleted records    |
// =====================================+
system('echo "'.date('Y-m-d G:i:s'). ' - Getting count of deleted records" >> ' . $netzlogs .'netz.log');
$query="SELECT * FROM MONLOGS WHERE CHECK_DATE_TIME < '".$expired."'";
$result=mysql_query($query);
$total=@mysql_numrows($result);
// =====================================+
//      Delete old monitor logs         |
// =====================================+
system('echo "'.date('Y-m-d G:i:s'). ' - Deleting '.$total .' Monitor records" >> ' . $netzlogs .'netz.log');
$query="DELETE FROM MONLOGS WHERE CHECK_DATE_TIME < '".$expired."'";
$result=mysql_query($query);
system('echo "'.date("Y-m-d G:i:s"). ' - ' . $total . ' Monitor log Records Deleted" >> ' . $netzlogs .'netz.log');
system('echo "'.date('Y-m-d G:i:s'). ' - '. $logdays . ' Days of Monitor logs Retained" >> ' . $netzlogs .'netz.log');
// =====================================+
//      Get count of deleted Alert records    |
// =====================================+
system('echo "'.date('Y-m-d G:i:s'). ' - Getting count of deleted Alert records" >> ' . $netzlogs .'netz.log');
$query="SELECT * FROM ALERTLOGS WHERE CHECK_DATE_TIME < '".$expired."'";
$result=mysql_query($query);
$total=@mysql_numrows($result);
// =====================================+
//      Delete old Alert logs         |
// =====================================+
system('echo "'.date('Y-m-d G:i:s'). ' - Deleting '.$total .' Alert records" >> ' . $netzlogs .'netz.log');
$query="DELETE FROM ALERTLOGS WHERE CHECK_DATE_TIME < '".$expired."'";
$result=mysql_query($query);
system('echo "'.date("Y-m-d G:i:s"). ' - ' . $total . ' Alert log Records Deleted" >> ' . $netzlogs .'netz.log');
system('echo "'.date('Y-m-d G:i:s'). ' - '. $logdays . ' Days of Alert logs Retained" >> ' . $netzlogs .'netz.log');



/*
// =====================================+
//      Reset the master binary logs    |
// =====================================+

$query="RESET MASTER";
$result=mysql_query($query);
system('echo "'.date('Y-m-d G:i:s'). ' - Reset Master Binary Logs" >> ' . $basedir .'/logs/netz.log');
//echo $query;
*/

mysql_close();



?>
