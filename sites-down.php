<?php
/*###############################################################
        NETz Network Management system				#
        http://www.proedgenetworks.com/netz			#
								#
								#
        Copyright (C) 2005-2026 Louie Zarrella			#
	louiez@proedgenetworks.com				#
								#
        Released under the GNU General Public License		#
	Copy of License available at :				#
	http://www.gnu.org/copyleft/gpl.html			#
###############################################################*/
include('auth.php');
include_once("site-monitor.conf.php");
include_once("lmz-functions.php");

//include('write_access_log.php');
?>
<html>

<head>
<META HTTP-EQUIV="REFRESH"  CONTENT="300"> 
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<?php $style=$_SESSION['style']; if ($style==""){$style="style/grey.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css" id="css">
<title> </title>
</head>

<body>
<?php
function tagred($str)
{
	$rtn="<font color='#Ff0000'>".$str."</font>";
	return $rtn;
}

function taggreen($str)
{
	$rtn="<font color='#339900'>".$str."</font>";
	return $rtn;
}

/*
10-12-2024
It seems the below querys were using functions in lmz_functions. but those functions will not work here.
The lmz_functions are used by the ops page and are needed there for reasons 19 years ago I forgot.
I changed the querys here to use standard query and count functions
*/

$conn = mysqli_connect(NETZ_DB_SERVER,  NETZ_DB_USERNAME,  NETZ_DB_PASSWORD,NETZ_DATABASE);
// get a list of sites in the DOWNSITES table
$active_sql="ACTIVE_DATE Is Not Null AND CLOSE_DATE Is Null";

$query="SELECT * FROM ".SITE_INFO_TABLE. " JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_STATUS > 0 AND MONITOR_ENABLE = 1 and ".$active_sql;
//$tdown = query_num_rows(run_netz_query($query));
$result=mysqli_query($conn,$query);
$tdown=mysqli_num_rows($result);


$query="SELECT * FROM ".SITE_INFO_TABLE. " JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_ENABLE = 1 and ".$active_sql;
//$total= query_num_rows(run_netz_query($query));
//$conn = mysqli_connect(NETZ_DB_SERVER,  NETZ_DB_USERNAME,  NETZ_DB_PASSWORD,NETZ_DATABASE);
$result=mysqli_query($conn,$query);
$total=mysqli_num_rows($result);
if ($total != 0 || $tdown != 0){
        $percent=round((($total-$tdown)/ $total ) *100, 1);
}else{
        $percent=100;
        $tdown=0;
        $total=0;
}

if ($percent < 95){$percent=tagred($percent);}else {$percent=taggreen($percent);}

echo "<div class=\"tdlight\" id='startmess' style='font-size:10pt;font-weight:bold'>".$total." Monitored ".$tdown." Down <br>". $percent . "% Available ". date('D  g:i:s a T');
?>
<script type="text/javascript">
var x="";
x=window.parent.location;
foo=String(x);
x=foo;
if (x.indexOf("ops.php") == -1 && x.indexOf("support.php") == -1 && x.indexOf("site-monitor.php") == -1){
        netz="<a href=\"main.php\"> NETz</a>";
        document.write(netz);
        
}
</script>
</div>
</body>

</html>
