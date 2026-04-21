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
include_once("auth.php");
include_once("site-monitor.conf.php");
include('write_access_log.php');
?>
<html>

<head>
<META HTTP-EQUIV="REFRESH"  CONTENT="300">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">

<title>User Stats </title>
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

//Connect to server
$conn=mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
#@mysql_select_db(NETZ_DATABASE) or die( "Unable to select database");
if ($_GET['user'] != ""){
	if ($_GET['time'] == "Week"){
		$midnight  = mktime( "0", "0", "0",date("m"),date("d")-5,date("Y"));
	}elseif ($_GET['time'] == "Day"){
		$midnight  = mktime( "0", "0", "0",date("m"),date("d"),date("Y"));
	}
	$midnight  = date("Y-m-d G:i:s",$midnight);
	$query = "SELECT PAGE, count( PAGE ) AS cnt FROM `ACCESSLOG` WHERE `USERNAME` = '".addslashes($_GET['user'])."' ";
	$query .= " AND ACCESS_DATE_TIME >'".$midnight."'GROUP BY `PAGE` ORDER BY cnt; ";
	$result=mysqli_query($conn,$query);
	echo "<h3>User Hits by page for ".$_GET['user']." (".$_GET['time'].")</h3>";
	echo "<table><tr><td>User</td><td>Hits</td></tr>";
	while ($row = mysqli_fetch_assoc($result)){
        		echo "<tr><td>". $row['PAGE']. "</td><td>  ".$row['cnt']."</td></tr>";
	}
	echo "</table>";
	echo "<a href=\"user-stats.php\">Back to All Users</a>";	
	exit();

}

// day
$midnight  = mktime( "0", "0", "0",date("m"),date("d"),date("Y"));
$midnight  = date("Y-m-d G:i:s",$midnight);
//echo $midnight ;
//$query="SELECT `USERNAME`, count(USERNAME) as num FROM `ACCESSLOG` WHERE `ACCESS_DATE_TIME` >'".$midnight."'  GROUP BY `USERNAME` ORDER BY `num` ASC ";
$query="SELECT  USERNAME, (select TITLE FROM USERS WHERE USERS.USERNAME = ACCESSLOG.USERNAME ) as title, (select FULL_NAME FROM USERS WHERE USERS.USERNAME = ACCESSLOG.USERNAME ) as name, count(USERNAME) as num FROM `ACCESSLOG` WHERE `ACCESS_DATE_TIME` >'".$midnight."' GROUP BY name ORDER BY `num` ASC";
//echo $query;

$result=mysqli_query($conn,$query);
$total= mysqli_num_rows($result);

echo "<h3>User Hits since Midnight</h3>";
echo "<h3>".$total." Users </h3>";
echo "<table><tr><td>User</td><td>title</td><td>Hits</td></tr>";
while ($row = mysqli_fetch_assoc($result)){
        echo "<tr><td>";
        echo "<a href=\"user-stats.php?user=".urlencode($row['USERNAME'])."&time=Day\">";
        if (trim($row['name']) != ""){
                echo  $row['name']. "</a></td>";
        }else{
                echo  $row['USERNAME']. "</a></td>";
        }
        echo "<td>  ".$row['title']."</td>";
        echo "<td>  ".$row['num']."</td></tr>";
}
echo "</table>";

// week
$midnight  = mktime( "0", "0", "0",date("m"),date("d")-5,date("Y"));
$midnight  = date("Y-m-d G:i:s",$midnight);
//echo $midnight ;
//$query="SELECT `USERNAME`, count(USERNAME) as num FROM `ACCESSLOG` WHERE `ACCESS_DATE_TIME` >'".$midnight."'  GROUP BY `USERNAME` ORDER BY `num` ASC ";
$query="SELECT USERNAME,(select TITLE FROM USERS WHERE USERS.USERNAME = ACCESSLOG.USERNAME ) as title, (select FULL_NAME FROM USERS WHERE USERS.USERNAME = ACCESSLOG.USERNAME ) as name, count(USERNAME) as num FROM `ACCESSLOG` WHERE `ACCESS_DATE_TIME` >'".$midnight."' GROUP BY name ORDER BY `num` ASC";


$result=mysqli_query($conn,$query);
$total= mysqli_num_rows($result);
echo "<h3>User Hits 5 Days </h3>";
echo "<h3>".$total." Users </h3>";
echo "<table><tr><td>User</td><td>title</td><td>Hits</td></tr>";
while ($row = mysqli_fetch_assoc($result)){
        echo "<tr><td>";
	echo "<a href=\"user-stats.php?user=".urlencode($row['USERNAME'])."&time=Week\">";
	if (trim($row['name']) != ""){
		echo  $row['name']. "</a></td>";
	}else{
		echo  $row['USERNAME']. "</a></td>";
	}
	echo "<td>  ".$row['title']."</td>";
	echo "<td>  ".$row['num']."</td></tr>";

//echo "<tr><td>". $row['name']. "</td><td>  ".$row['num']."</td></tr>";
}
echo "</table>";



?>


</body>

</html>
