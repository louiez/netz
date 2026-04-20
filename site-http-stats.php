<?php
include 'logon.php';
include('lmz-functions.php');
include('site-monitor.conf.php');
echo '<html><head>';
 $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">
<script type="text/javascript"  src="size_window.js"> </script>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">';
echo '<title>Site Stats</title>';
echo '</head><body>';
echo '<div  id="show_div" 
	style=" position: absolute; 
        left: 0px; 
        top: 0px; 
        padding: 10px;" >';
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
echo "<div id='startmess' style='font-size:12pt;font-weight:bold'>L<img src='img/circles.gif' weight='20' width='20'><img src='img/circles.gif' weight='20' width='20'>king at data for ".$site."   this may take a minute... Please wait...</div>";
flush();
sleep(2);

$conn=mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);

if ($detail == 1){
        //           hour-----|  minute--|  second--|  month---|     day--|  |-days back-| year-|
        $back  = mktime("0", "0", "0", date("m"), date("d")-$daysback, date("Y"));
        $back  = date("Y-m-d G:i:s",$back);
        $query="SELECT * FROM HTTPMONLOGS WHERE SITE_ID = '".$site."' AND CHECK_DATE_TIME >= '".$back."' ORDER BY CHECK_DATE_TIME DESC";
        $result=mysqli_query($conn,$query);
        echo "<table border='1'><tr>";
        echo "<td class=\"monlogs\">Site</td>";
        echo "<td class=\"monlogs\">Check Time</td>";
        echo "<td class=\"monlogs\">Response</td>";
        echo "<td class=\"monlogs\">Data Length</td>";
        echo "<td class=\"monlogs\">Returned Header</td>";
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
                echo "<td class=\"monlogs\">".$row['SITE_ID']."</td>";
                echo "<td class=\"monlogs\"nowrap=\"nowrap\">".date('D M j   g:i:s a  T',strtotime($row['CHECK_DATE_TIME']))."</td>";
                echo "<td class=\"monlogs\">".$row['RESPONSE_TIME']."</td>";
                echo "<td class=\"monlogs\">".$row['DATA_LENGTH']."</td>";
                echo "<td class=\"monlogs\" style=\"text-align:left\"><pre>".$row['ERROR_STRING']."</pre></td>";
                //echo "<td style='".$style."'>".$row['CHECK_STATE']."</td>";
                echo "<td class=\"monlogs\" style='".$style."'>".$state."</td>";

                echo "</tr>";
        }
        echo "</table>";
}
mysqli_close();
/*
$query="SELECT * FROM NAPAAll WHERE VPNActiveDate != '' and PublicIPaddress != '' ORDER BY `PublicIPaddress` ASC LIMIT ".$lower." , ".$upper;
$result=mysql_query($query);
echo $query;
while ($row = mysql_fetch_assoc($result))
                        $query = "INSERT INTO monlogs VALUES ('$store','$cfgServer','$date',$time,0)";
                        mysql_query($query);
                        $query = "INSERT INTO monlogs VALUES ('$store','$cfgServer','$date',$time,1)";
                        mysql_query($query);

mysql_close();
*/
echo '</div><script type="text/javascript">sizeToFit("show_div");</script>';
echo "</body>";
$myend=microtime_float(true);
$mytimetotal=round($myend-$mystart,4);
//echo "<script type='text/javascript'>  document.getElementById('startmess').innerHTML = ''; </script></html>";
echo "<script type='text/javascript'>  document.getElementById('startmess').innerHTML = 'Stats for ".$site." Processed ".$mytotal." records in ".$mytimetotal." sec'; </script></html>";


?>

