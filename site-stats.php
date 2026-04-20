<?php
include_once 'auth.php';
include_once('lmz-functions.php');
include_once('site-monitor.conf.php');
include('write_access_log.php');
echo '<html><head>';
 $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">
<script type="text/javascript" src="size_window.js"></script>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">';
echo '<title>Site Log</title>';
echo '</head><body>';
?>
 <div  id="show_div" 
        style=" position: absolute; 
        left: 0px; 
        top: 0px; 
        padding: 10px;" >
<?php
$mystart=microtime_float(true);

$site=$_GET['site'];
$daysback=$_GET['days'];
if ($daysback == "")$daysback=6;
//if ($daysback > 6)$daysback=6;
$daysinclude=$_GET['daysinclude'];
if ($daysinclude=="" && $daysback == 0){$daysinclude=1;}
//if ($daysinclude=="") {$daysinclude=($daysback*2);}
if ($daysinclude=="") {$daysinclude= 1;}
$graph=$_GET['graph'];
if ($graph == "")$graph = 1;
if ($graph > 1)$graph = 1;
if ($graph < 0 )$graph = 0;

$detail=$_GET['detail'];
if ($detail == "")$detail = 1;
if ($detail > 1)$detail = 1;
if ($detail < 0 )$detail = 0;

// display loading message and then delete after load at bottom
//echo "<div id='startmess'>Loading data for ".$site." this may take a minute... Please wait<br><img src='img/counter.gif'></div>";
echo "<div id='startmess' style=font-size:10pt;'font-weight:bold'>L<img src='img/circles.gif' weight='15' width='15'><img src='img/circles.gif' weight='15' width='15'>king at data for ".$site."   this may take a minute... Please wait...</div><br>";
flush();
sleep(2);

$conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//@mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");

if ($detail == 1){
        //           hour-----|  minute--|  second--|  month---|     day--|  |-days back-| year-|
        $back  = mktime("0", "0", "0", date("m"), date("d")-$daysback, date("Y"));
        $back  = date("Y-m-d G:i:s",$back);	
	$end = mktime("0", "0", "0", date("m"), date("d")-($daysback - $daysinclude), date("Y"));
	$end  = date("Y-m-d G:i:s",$end);
//	$query="SELECT * FROM ".SITE_MON_TABLE." WHERE ".SITE_ID_DEFAULT." = '".$site."' AND CHECK_DATE_TIME >= '".$back."' ORDER BY CHECK_DATE_TIME DESC";
$query="SELECT * FROM ".SITE_MON_TABLE." JOIN MONITORINFO USING(SITE_ID) WHERE ".SITE_ID_DEFAULT." = '".$site."' AND CHECK_DATE_TIME >= '".$back."' AND CHECK_DATE_TIME <= '".$end."' ORDER BY CHECK_DATE_TIME DESC";
echo $query;
//echo $query;
	$result=mysqli_query($conn,$query);
	echo "<table border='1'>";
	echo "<tr>";
	echo "<td class=\"monlogs\">Site</td>";
        echo "<td class=\"monlogs\">Monitored IP</td>";
	echo "<td class=\"monlogs\">Check Time</td>";
	echo "<td class=\"monlogs\">Response</td>";
	echo "<td class=\"monlogs\">State</td>";
	echo "</tr>";
	while ($row = mysqli_fetch_assoc($result)){
		if ($row['CHECK_STATE'] == 1) {
			$style='color:green;font-weight:bold';
			$state="Alive";
		}else {
			$style='color:red;font-weight:bold';
			$state="Down";
		}
		echo "<tr>";
		echo "<td class=\"monlogs\">".$row[SITE_ID_DEFAULT]."</td>";
                echo "<td class=\"monlogs\">".$row['SITE_IP']."</td>";
		echo "<td class=\"monlogs\" nowrap=\"nowrap\" >";
		echo date('D M j   g:i:s a  T',strtotime($row['CHECK_DATE_TIME']))."</td>";
		echo "<td class=\"monlogs\">".$row['RESPONSE_TIME']."</td>";
		//echo "<td style='".$style."'>".$row['CHECK_STATE']."</td>";
		echo "<td class=\"monlogs\" style='".$style."'>".$state."</td>";
	
		echo "</tr>";
	}
	echo "</table>"; 
}
mysqli_close($conn);
/*
$query="SELECT * FROM NAPAAll WHERE VPNActiveDate != '' and PublicIPaddress != '' ORDER BY `PublicIPaddress` ASC LIMIT ".$lower." , ".$upper;
$result=mysqli_query($conn,$query);
echo $query;
while ($row = mysqli_fetch_assoc($result))
               		$query = "INSERT INTO monlogs VALUES ('$store','$cfgServer','$date',$time,0)";
                	mysqli_query($conn,$query);
			$query = "INSERT INTO monlogs VALUES ('$store','$cfgServer','$date',$time,1)";
			mysqli_query($conn,$query);

mysqli_close();
*/
?>
</div>
<script type="text/javascript">sizeToFit("show_div");</script>
<?php
echo "</body>";
$myend=microtime_float(true);
$mytimetotal=round($myend-$mystart,4);
//echo "<script type='text/javascript'>  document.getElementById('startmess').innerHTML = ''; </script></html>";
//echo "<script type='text/javascript'>  document.getElementById('startmess').innerHTML = 'Stats for ".$site." Processed ".$mytotal." records in ".$mytimetotal." sec'; </script></html>";
echo "<script type='text/javascript'>  document.getElementById('startmess').innerHTML = '".$site." - $back '; </script></html>";

?>

