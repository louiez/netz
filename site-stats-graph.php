<?php
include_once 'auth.php';
include_once('lmz-functions.php');
include_once('site-monitor.conf.php');
echo '<html><head>';
 $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">';
echo '<title>Site Stats</title>';
echo '</head><body>';


$site=$_GET['site'];
$daysback=$_GET['days'];
if ($daysback == "")$daysback=6;

// display loading message and then delete after load at bottom
//echo "<div id='startmess'>Loading data for ".$site." this may take a minute... Please wait<br><img src='img/counter.gif'></div>";
/*
echo "<div id='startmess' style='font-size:12pt;font-weight:bold'>L<img src='img/circles.gif' weight='20' width='20'><img src='img/circles.gif' weight='20' width='20'>king at data for ".$site."   this may take a minute... Please wait...</div>";
*/
echo "<div id='startmess'>Loading....</div>";
sleep(2);

$conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//@mysql_select_db(NETZ_DATABASE) or die( "Unable to select database");

//echo "Getting stats for $site  please wait....";
echo '<table border="0" width="100%">';
echo '<tr>';
for ($d=$daysback;$d>=0;$d--){
	$days_back = $d;
	//           hour-----|  minute--|  second--|  month---|     day--|  |-days back-| year-|
	$back  = mktime("0", "0", "0", date("m"), date("d")-$days_back, date("Y"));
	$back  = date("Y-m-d G:i:s",$back);

	$days_back = $d-1;
	//         hour-----|  minute--|  second--|  month---|     day--|  |-days back-| year-|
	$backlen  = mktime("0", "0", "0", date("m"), date("d")-$days_back, date("Y"));
	$backlen  = date("Y-m-d G:i:s",$backlen);

	//$query="SELECT * FROM ".SITE_MON_TABLE." WHERE ".SITE_ID_DEFAULT." = '".$site."' AND CHECK_DATE_TIME >= '".$back."' AND CHECK_DATE_TIME < '".$backlen."'";
        $query="SELECT CHECK_STATE FROM ".SITE_MON_TABLE." WHERE ";
	$query .= SITE_ID_DEFAULT." = '".$site."' ";
	$query .= "AND CHECK_DATE_TIME >= '".$back."' ";
	$query .= "AND CHECK_DATE_TIME < '".$backlen."'";
//$query="SELECT * FROM SITE_MON_TABLE WHERE CHECK_DATE_TIME >= '".$back."' AND CHECK_DATE_TIME < '".$backlen."'";
	//echo $query."<br>";
	$result=mysqli_query($conn,$query);
	if (!$result) {
   		die('Database error -  ' . mysqli_error());
	}
	$total=mysqli_num_rows($result);
	$alive=0;
	$mytotal+=$total;
	$myday =mktime("0", "0", "0", date("m"), date("d")-$d,date("Y"));
//echo "<div>";
	if ($total > 0)
	{
		//$myday =mktime("0", "0", "0", date("m"), date("d")-$d,date("Y"));
		while ($row = mysqli_fetch_assoc($result)){
			if ($row['CHECK_STATE'] == 1)	$alive++;
		}
		echo "<td valign='bottom' align='center' style=\"font-size:9px;text-align:center\">";
		// seems if you say 100% they expect 100% not 99.9% but don't matter if it is 98.9%
		// so if less then 100 and greater then 99 round with decimal 99.? %
		$percent_tmp=($alive / $total) * 100;
		if ( $percent_tmp > 99 && $percent_tmp < 100){
			$percent=round($percent_tmp,1);
		}else{
			$percent=round($percent_tmp);
		}
		//$percent=round(($alive / $total) * 100);
		echo $percent."%<br>";
//echo "<a class=\"imgg\" href=\"@\" ";
// quick hack for changing the popup window size
if (strpos($_SESSION['style'], "small")){
        $sizeoption="width=640,height=660";
}elseif (strpos($_SESSION['style'], "large")){
        $sizeoption="width=700,height=800";
}else{
       $sizeoption="width=640,height=740";
}
$url="site-stats.php?site=".$site."&days=".$d;
$options=$sizeoption.",resizable=yes,scrollbars=yes";

echo "<a class=\"imgg\" href=\"@\" onclick=\"window.open('".$url."','','".$options."');return false\">";
		if ($percent < 75)
		{
			if ($percent < 1){$percent = 1;}
			echo "<img src='img/red_small.gif' style='width:10px;height:".$percent."px;border:none'><br>";
		}
		elseif ($percent >= 75 && $percent < 95)
		{
			echo "<img src='img/yellow_small.gif' style='width:10px;height:".$percent."px;border:none'><br>";
		}
		else
		{
			echo "<img src='img/green_small.gif' style='width:10px;height:".$percent."px;border:none'><br>";
		}
echo"</a>";
		if ($d != 0){
			//echo "day ".$d;
			//$myday =mktime("0", "0", "0", date("m"), date("d")-$d,date("Y"));
			echo date("D",$myday)."<br>".date("d",$myday);
		}
		else{
			echo "Today<br><br>";
		}
		echo "</td>";
		flush();
		ob_flush();
	}
	else
	{
		echo "<td  valign='bottom' align='center'>No data<br><img src='img/transparentpixel.gif' width='10px' height='100px'><br>".date("D",$myday). "</td>";
	}
//echo "</div>";
	               flush();
                ob_flush();
} // End For
echo "</tr></table>";
echo "</body>";
echo "<script type='text/javascript'>  document.getElementById('startmess').innerHTML = '';</script>";
echo "</html>";
?>
