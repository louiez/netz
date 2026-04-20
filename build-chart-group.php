<?php
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL);

ob_start();
include_once('site-monitor.conf.php');
$site = isset($_GET['site']) ? $_GET['site'] : '';
$group = isset($_GET['group']) ? $_GET['group'] : '';
$daysback = isset($_GET['back']) ? $_GET['back'] : '';
$hourly = isset($_GET['hourly']) ? $_GET['hourly'] : '';
if ($daysback == "")$daysback=1;
if ($daysback == "0")$daysback="1";
$numdays = isset($_GET['days']) ? $_GET['days'] : '';
$chartsize = isset($_GET['size']) ? $_GET['size'] : '';
$conn=mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//@mysql_select_db(NETZ_DATABASE) or die( "Unable to select database");
$colors=array("#993399","#FFFF77","#006600","#FF3333","#FFBB99","#FFAABB","#FFaa22","#FF99FF","#FF77EE","#FF6600","#FF3399","#77CC00","#FF22EE","#FF2277","#DDDD00","#CCCCFF","#CCCC00","#CC9999","#CC88FF","#CC0000","#BB77FF","#FFFFFF","#99CC66","#AAFF55","#990033","#88EEFF","#88BBFF","#77FFBB","#FFDD99","#66FF88","#6699FF","#6666FF","#663399","#660000","#3399FF","#11DDFF","#00EE33","#00CCFF","#00CC00","#0066FF","#FFEE22","#0033FF");

if ($daysback == ""){
	$backlen  =86400;
}
elseif ($hourly != ""){
	$backlen = $hourly * 3600;
}
else{
	$backlen  = $daysback * 86400;
}

if ($chartsize == "small"){
	$height = "150";
	$width = "800";     
}elseif ($chartsize == "tiny"){
	$height = "50";
	$width = "100";	
}elseif ($chartsize == "large"){
	$height = "768";
	$width = "1024";
}elseif ($chartsize == "xlarge"){
	$height = "1024";
	$width = "1280";
}elseif ($chartsize == "xxlarge"){
	$height = "1200";
	$width = "1600";
}else{
	$height = "400";
	$width = "800";		
}

$ccmd='/usr/bin/rrdtool graph -  -t "'.$site.' Ping" -v "Time in ms" ';
$ccmd= $ccmd.' --imgformat PNG --start -'.$backlen.' --end="now" --height="'.$height.'" --width="'.$width.'" ';
$ccmd= $ccmd.' -l -10 -X 0 -r -c "BACK#000000" -c "SHADEA#000000" -c "SHADEB#000000" -c "FONT#DDDDDD" ';
$ccmd= $ccmd.'-c "CANVAS#404040" -c "GRID#666666" -c "MGRID#AAAAAA" -c "FRAME#202020" -c "ARROW#FFFFFF" ';
echo $ccmd;
//$query="SELECT * FROM SITEDATA WHERE GROUP_NAME = '".$group."' AND MONITOR_ENABLE = 1";
$query="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE GROUP_NAME = '".$group."' AND MONITOR_ENABLE = 1";
$result1=mysqli_query($conn,$query);
$mycnt=0;
$rrd_site_defs = ''; // Initialize the variable
$rrd_site_lines = ''; // Initialize the variable
while ($row1 = mysqli_fetch_assoc($result1)){

	$site=$row1['SITE_ID'];
	//		$filename = $basedir.'rrd/'.$site.'.rrd';

	$defname=$site;
	/*
	   $patterns[0] = '/\./';
	   $patterns[1] = '/\ /';
	   $replacements[0] = "x" ;
	   $replacements[1] = "x" ;

	   $defname=preg_replace($patterns, $replacements,$site) ;
	 */
	//**************************************************************//		
	// cleanup valid site names to valid filenames			//
	// NETz allows names that may not be legal as file names	//
	//**************************************************************//
	$allowed = '/[^a-z0-9\\.\\-\\_\\\\]/i';				//
	$rrdfilename=preg_replace($allowed,"",$site);			//
	$rrdfilename= $basedir.'rrd/'.$rrdfilename.'.rrd';		//
									//**************************************************************//
	if (is_readable($rrdfilename)) {
		// Creates the Definition for the next store
		$rrd_site_defs = $rrd_site_defs . '"DEF:'.$defname.'='.$rrdfilename.':rtime:AVERAGE" ';
		$rrd_site_lines = $rrd_site_lines . '"LINE1.2:'.$defname.$colors[$mycnt].':'.$site.'" ';
		$rrd_site_lines = $rrd_site_lines .'"GPRINT:'.$defname.':AVERAGE:Avg\: %5.2lf ms\n" ';
		$mycnt++;if ($mycnt==42){$mycnt=0;}
	}
}
// Close the connection properly
mysqli_close($conn);

$ccmd= $ccmd . $rrd_site_defs . $rrd_site_lines ;

header('Content-Type: image/png');
ob_end_clean();
passthru($ccmd);

//echo $ccmd;
?>
