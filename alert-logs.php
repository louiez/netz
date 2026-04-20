<?php
include 'auth.php';
include('lmz-functions.php');
include('site-monitor.conf.php');
echo '<html><head>';
 $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">';
echo '<title>Site Stats</title>';
echo '</head><body>';

$mystart=microtime_float(true);

$site=$_GET['site'];
$daysback=$_GET['days'];
if ($daysback == "")$daysback=6;
//if ($daysback > 6)$daysback=6;
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
echo "<div id='startmess' style='font-size:12pt;font-weight:bold'>Alerts sent last ".$daysback." Days for ".$site." </div>";
flush();
sleep(2);

$conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//@mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");
/*
*/
if ($detail == 1){
        //           hour-----|  minute--|  second--|  month---|     day--|  |-days back-| year-|
        $back  = mktime("0", "0", "0", date("m"), date("d")-$daysback, date("Y"));
        $back  = date("Y-m-d G:i:s",$back);	
	$query="SELECT * FROM ALERTLOGS WHERE ".SITE_ID_DEFAULT." = '".$site."' AND CHECK_DATE_TIME >= '".$back."' ORDER BY CHECK_DATE_TIME DESC";
	$result=mysqli_query($conn,$query);
	echo "<table border='1'>";
	echo "<tr><td>Site</td><td>Alert Sent Time</td></tr>";
	while ($row = mysqli_fetch_assoc($result)){
/*
*/
		echo "<tr>";
		echo "<td>".$row[SITE_ID_DEFAULT]."</td>";
		echo "<td cellpadding='2px'>".date('D  M  j   g:i:s a  T',strtotime($row['CHECK_DATE_TIME']))."</td>";
		//echo "<td>".$row['RESPONSE_TIME']."</td>";
		//echo "<td style='".$style."'>".$row['CHECK_STATE']."</td>";
		//echo "<td style='".$style."'>".$state."</td>";
	
		echo "</tr>";
	}
	echo "</table>"; 
}
mysqli_close($conn);
/*
*/
echo "</body>";
//$myend=microtime_float(true);
//$mytimetotal=round($myend-$mystart,4);
//echo "<script type='text/javascript'>  document.getElementById('startmess').innerHTML = ''; </script></html>";
//echo "<script type='text/javascript'>  document.getElementById('startmess').innerHTML = 'Alert Sent for ".$site."'; </script></html>";
echo "</html>";

?>

