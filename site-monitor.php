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
include_once('alert-logs.class.php');
include_once('lmz-functions.php');
include('write_access_log.php');
require_once( 'db.class.php');
$db_class = new DB_Class();
$ALclass = new SiteLog();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>


<?php
//      +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//      |       User Access code        |
// =====================================================================================================//
$acl=$_SESSION['accesstype'];                                                                           //
if ($_SESSION['accesslevel'] == 0){                                                                     //
        echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';       //
        echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';                        //
        }                                                                                               //
// =====================================================================================================//
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
?>

<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="REFRESH"  CONTENT="300">
<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">
 <link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon" >
 <link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon" > 
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE"> 
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"> 
<title>site monitor</title>
<script type="text/javascript"  src="table_roll_over.js"> </script>
<script type="text/javascript">
function show_stats(site)
{

	<?php
	// quick hack for changing the popup window size
	if (strpos($_SESSION['style'], "small")){
 		$options = 'width=500,height=560,resizable=yes,scrollbars=yes,status=yes';
	}elseif (strpos($_SESSION['style'], "large")){
		$options = 'width=650,height=550,resizable=yes,scrollbars=yes,status=yes';
	}else{
		$options = 'width=550,height=550,resizable=yes,scrollbars=yes,status=yes';
	}
	?>
        window.open ("site-stats.php?site="+ site +"&days=7&daysinclude=8&detail=1","","<?php echo $options ?>")
        return false;
}
function show_store(url)
{
        if (window.opener != null){
                window.opener.focus();
                window.opener.location= url;

        }else{
                window.location= url;
        }
        return false ;
}
</script>
</head>

<body>
<script type="text/javascript"  src="menulz.js"> </script>
<?php
// Adds menu items to side menu
 if ($_SESSION['accesslevel'] >= 9){
        echo '<script type="text/javascript"  src="menu-data-rwa.js"> </script>';
 }elseif($_SESSION['accesslevel'] >= 4){
        echo '<script type="text/javascript"  src="menu-data-rw.js"> </script>';
}else{
        echo '<script type="text/javascript"  src="menu-data-ro.js"> </script>';
}


// Get support center filter choice
if (isset($_POST['support'])){ $supportcntr=trim($_POST['support']); }else{$supportcntr="";}
if ($supportcntr=="")$supportcntr=trim($_SESSION['support']);
if ($supportcntr=="")$supportcntr="all";

//Connect to server
$conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//@mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");
//----------------------------------------------//
// Update database with support center filter	//
//==============================================================================================//
$array[STYLESHEET] = $_SESSION['style'];							//
$array[SUPPORT] = $supportcntr;									//
$temp = implode(":",$array);									//
$sql = "UPDATE USERS SET STYLE = '" . $temp. "' WHERE USERNAME = '" .$_SESSION['user'] . "'";	//
$query = mysqli_query($conn,$sql);									//
//==============================================================================================//

//----------------------//
// get the sort field	//
//==============================================//
if (isset($_GET['sort'])){ $sort=$_GET['sort']; }else{$sort="";}				//
switch ($sort) {										//
        case "site":                            //
                $sort = "SITE_ID";              //
                break;                          //
	case "city":				//
		$sort = "CITY";			//
		break;				//
        case "state":                           //
                $sort = "ST";                 	//
                break;                          //
	case "loss":				//
		$sort = "MONITOR_STATUS";	//
		break;				//
        case "group":                           //
                $sort = "GROUP_NAME";       	//
                break;                          //
        case "servicetype":			//
                $sort = "SERVICE_TYPE";		//
                break;				//
        case "provider":			//
                $sort = "INET_PROVIDER";	//
                break;				//
        case "life":                     	//
                $sort = "TOTAL_ALERTS_SENT";    //
                break;                          //
}						//
//==============================================//

//--------------------------------------//
// Get the sort order direction		//
//======================================================//
$sortdirection=$_GET['sd'];				//
switch ($sortdirection) {                       	//
        case "ASC":                             	//
                $sd = "DESC";         			//
                $arimg = "down_pointer.png";            //
                break;                          	//
        case "DESC":                             	//
                $sd = "ASC";				//
                $arimg = "up_pointer.png";              //
                break;                          	//
	default:					//
		$sd = "ASC";				//
                $arimg = "up_pointer.png";              //
		$sortdirection="ASC";         		//
}							//
//======================================================//

if ($sort == "site" || $sort == ""){$siteimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";} 
else {$siteimgtag = "";}

if ($sort == "city"){$cityimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";} 
else {$cityimgtag = "";}

if ($sort == "state"){$stateimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";}
else {$stateimgtag = "";}

if ($sort == "loss"){$lossimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";} 
else {$lossimgtag = "";}

if ($sort == "group"){$groupimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";}
else {$groupimgtag = "";}

if ($sort == "servicetype"){$servicetypeimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";} 
else {$servicetypeimgtag = "";}

if ($sort == "provider"){$providerimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";} 
else {$providerimgtag = "";}

if ($sort == "life"){$lifeimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";} 
else {$lifeimgtag = "";}

//----------------------//
// Create Query string	//
//======================================================================================================================//
if ($sort == "")$sort = SITE_ID_DEFAULT;											//
if ($supportcntr=="" || $supportcntr== "all"){										//
        $query="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_STATUS > 0 AND MONITOR_ENABLE = 1 ORDER BY $sort $sortdirection";	//
}else{															//
        $query="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_STATUS > 0 AND MONITOR_ENABLE = 1 ";				//	
	$query= $query . "AND SUPPORT_CENTER = '$supportcntr' ORDER BY $sort $sortdirection";				//
	$queryt = "SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_ENABLE = 1 AND SUPPORT_CENTER = '$supportcntr'"; 
}															//
//=============================================================================================================//
// get total number of sites in group
if (isset($queryt)){
	$result_total_group=mysqli_query($conn,$queryt);
	$total_group_count= mysqli_num_rows($result_total_group);
	// percent of sites online for group
	$percent=round((($total_group_count-$total)/ $total_group_count ) *100, 1);

}else{$percent=0;}
// get total number of sites down in group
$result=mysqli_query($conn,$query);
mysqli_query($conn,"COMMIT");

//--------------//
// get Total	//
//==============//

// number of sites down for group
$total= mysqli_num_rows($result);

// percent of sites online for group
//$percent=round((($total_group_count-$total)/ $total_group_count ) *100, 1);

//--------------------------------------//
// create support option box entries	//
//======================================================================================================//
unset($file_lines);											//
$file_lines = $db_class->get_support_centers();								//
 foreach ($file_lines as $line){									//
        if ($line != ""){										//
                $optionbox = $optionbox. "<option value=\"" . $line . "\">" . $line . "</option>";	//
        }												//
}													//
//======================================================================================================//

//--------------------------------------------------------------//
// Write Page heading and support center filter option box	//
//==============================================================//
//if ($supportcntr == "all"){$supportheading = "";}else{$supportheading = $supportcntr;}
echo "<table style=\"width : 100%; padding: 5px;\"><tr><td style=\"text-align: center;width : 33%\">";
echo "<div style=\"text-align:center; font-size:14px; font-weight:bold\">";
if ($supportcntr != "all"){
//	echo $total." (".$percent."%) ".$supportcntr." sites unavailable";// -  ". date('D M j   g:i:s a T')."";
echo $total." ".$supportcntr." sites unavailable<br>";
// tag the percent font color green if over 94 and red if under
if ($percent < 95){$percent=tagred($percent);}else {$percent=taggreen($percent);}
echo $percent."% Available";
}
echo "<form action=\"site-monitor.php\" method=\"post\" id=\"supportform\"> ";
echo "<select class=\"inputstyl\" size=\"1\" name=\"support\" onchange=\"javascript:document.getElementById('supportform').submit()\">";
echo "<option value=\"".$supportcntr."\" SELECTED  >".$supportcntr."</option>".$optionbox;
echo "<option value=\"all\">all</option>";
echo "</select>&nbsp;Support center</form></div></td>";

echo "<td style=\"text-align: center;width : 33%; font-size:18; font-weight:bold\"> Down Sites<br>";
echo "<a href=\"@\" onclick=\"window.open('";
if ($supportcntr == "all"){$s="";}else{$s=$supportcntr;}
echo $_SESSION['secure'] . $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF'])."/plugins/google_map/netz-gm.php?site=".$s."&selection=offline&submit=Submit+Query";
echo "','','width=575,height=725,resizable=yes,scrollbars=yes,status=yes'); return false;\">Show on map</s></td>";
echo "<td style=\"text-align: center;width : 33%\">";
echo "<iframe height=\"50px\" width=\"350px\" name=\"myframe\" frameborder=\"0\" scrolling=\"no\" src=\"sites-down.php\"> </iframe>";
echo "</td></tr></table>";
//----------------------------------------------//
// Write Table headings with sort links		//
//==============================================//
$self = $_SERVER['PHP_SELF'];
//echo '<table style="width : 100%" onMouseOut="javascript:highlightTableRowVersionA(0);"><thead>';
echo '<table id="down_list" style="width : 100%"><thead>';
echo "<tr><td><a href=\"".$self."?sort=site&amp;sd=".$sd."\" >Site".$siteimgtag."</a></td>";
echo "<td><a href=\"".$self."?sort=city&amp;sd=".$sd."\" >City".$cityimgtag."</a></td>";
echo "<td><a href=\"".$self."?sort=state&amp;sd=".$sd."\" >ST".$stateimgtag."</a></td>";
echo "<td>Monitored IP</td>";
echo "<td><a href=\"".$self."?sort=group&amp;sd=".$sd."\" >Group".$groupimgtag."</a></td>";
echo "<td><a href=\"".$self."?sort=servicetype&amp;sd=".$sd."\" >Service Type".$servicetypeimgtag."</a></td>";
echo "<td><a href=\"".$self."?sort=provider&amp;sd=".$sd."\" >Provider".$providerimgtag."</a></td>";
        //echo "<td>Last Check</td>";
        //echo "<td>Logs</td>";
        //LMZecho "<td>Current</td>";
echo "<td><a href=\"".$self."?sort=loss&amp;sd=".$sd."\" >Down hrs (Missed Monitor Cycles)".$lossimgtag."</a></td>";
//echo "<td><a href=\"".$self."?sort=life&amp;sd=".$sd."\" >Life Time<br> alerts sent".$lifeimgtag."</a></td>";
echo "<td>Alert last<br> 30 days</td>";
echo "<td>Alert Sent</td>";
echo "</tr></thead>";
if (mysqli_num_rows($result)){echo "<tbody>";}
while ($row = mysqli_fetch_assoc($result)){
        if (mysqli_num_rows($result)){
                $totalhrsdown=round(($row['MONITOR_STATUS'] * $moncycleinterval) / 60);
                if ($totalhrsdown > 23){
                         $imageurl="img/red.gif";
                }
		elseif ($totalhrsdown >= 10){
                        $imageurl="img/yellow.gif";
                }else{
                        $imageurl="img/green.gif";
                }
                if ($tdclass == "tdlight"){$tdclass = "tddark";}
                else{ $tdclass = "tdlight";}
                if ($_SESSION['accesslevel'] >= 7){
                        $sitelink = "<a href='' \n";
			$sitelink .= "onclick=\"return show_store('ops.php?site=".$row['SITE_ID']."')\">";
			$sitelink .= $row['SITE_ID']."</a>";
                }else{
                        $sitelink = "<a href='' \n";
			$sitelink .= "onclick=\"return show_store('support.php?site=".$row['SITE_ID']."')\">";
			$sitelink .= $row['SITE_ID']."</a>";
                }
               $siteip= $row[$row['MONITOR_IP_FIELD']];
               if ($siteip == "") $siteip = $row['LAN_GATEWAY'];
		echo "<tr>\n";
		echo "<td  class=' " . $tdclass ."'>".$sitelink."</td>\n";
                echo "<td class=' " . $tdclass ."'>".$row['CITY']."</td>\n";
		echo "<td class=' " . $tdclass ."'>".$row['ST']."</td>\n";
                echo "<td  class=' " . $tdclass ."'>".$siteip."</td>\n";
		echo "<td  class=' " . $tdclass ."'>".htmlentities($row['GROUP_NAME'])."</td>\n";
                echo "<td  class=' " . $tdclass ."'>".htmlentities($row['SERVICE_TYPE'])."</td>\n";
                echo "<td  class=' " . $tdclass ."'>".htmlentities($row['INET_PROVIDER'])."</td>\n";
		// Down Hrs 
                echo "<td  class=' " . $tdclass ."'>\n";
		echo "<a href='@'><img style='border:none;height:10px; width:10px' alt='Click for stats' ";
		echo "src='".$imageurl."' ";
		echo "onclick=\"return show_stats('".$row['SITE_ID']."')\"></a>&nbsp;&nbsp;";		// end Image link
		echo "<span title='estimated Hours Down'>".$totalhrsdown."</span>";			// span estimated hrs down
		echo " <span title='Missed Monitor Cycles' ";						//>
		echo "style='color:white;background-color:black'>&nbsp;(";				//>> end cycles missed
		echo $row['MONITOR_STATUS'].")&nbsp;</span></td>\n";					//>
		// Alert last 30 days
		echo "<td  class=' " . $tdclass ."'>".$ALclass->get_count($row['SITE_ID'],"30")."</td>\n";
		// colorize Alert Sent 
                if ($row['ALERT_SENT']==1 )
                {
                        echo "<td  class=' " . $tdclass ."'><font color='red'><b>Yes</b></font></td>";
                }else{
                        echo "<td  class=' " . $tdclass ."'>No</td>";
                }
                echo "</tr>\n";
	}
	flush();
}
if (mysqli_num_rows($result)){echo "</tbody>";}
echo "</table>";

//##############################################//
//		Chronic Down sites		//
//##############################################//
$conn = mysqli_connect("localhost",NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");
echo '<br><h2 style="text-align:center" >Chronic Down sites</h2>';
//echo '<table style="width : 100%" onMouseOut="javascript:highlightTableRowVersionA(0);"><thead>';
echo '<table id="chronic_list" style="width : 100%" ><thead>';
//********************************
//      Write table Headings	//
//********************************
echo '<tr>';
echo '<td>Site</td>';
echo '<td>'.Display_name('INET_PROVIDER').'</td>';
echo '<td>Alerts last 6 days</td>';
echo '<td>Alerts last 4 days</td>';
echo '<td>Alerts  Today</td>';
echo '<td>Current Status</td>';
echo '</tr></thead>';
//if (mysqli_num_rows($result)){echo "<tbody>";}
//************************
//	Create Query	//
//************************
if ($supportcntr=="" || $supportcntr== "all"){ 
	$SQL="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_ENABLE='1'";
}else{
	$SQL="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE MONITOR_ENABLE='1' AND SUPPORT_CENTER = '".$supportcntr."'";
}
$alertscls= new SiteLog();
$result=mysqli_query($conn,$SQL);
mysqli_query($conn,"COMMIT");
if (mysqli_num_rows($result)){echo "<tbody>";}
while ($row = mysqli_fetch_assoc($result)) {
	//****************************************
	//      Get total alerts for site	//
	//****************************************
        $total_alerts=$alertscls->get_count($row["SITE_ID"],5);
	$total5day=$alertscls->get_count($row["SITE_ID"],3);
	$total3day=$alertscls->get_count($row["SITE_ID"],0);
	// ****  Create Site link  ****
        if ($total_alerts > 5 ){
		//****************************|
		// ****  Toggle Row color     |***************************************************	
		if ($tdclass == "tdlight"){$tdclass = "tddark";}else{ $tdclass = "tdlight";}	//
		//********************************************************************************

		// ****  Table Row start  ****
//		echo "<tr onMouseOver=\"javascript:highlightTableRowVersionA(this, '#8888FF');\" >";
		echo "<tr>";

		// ****  Site Link  ****
                if ($_SESSION['accesslevel'] >= 7){
			$sitelink = "<a href='' onclick=\"return show_store('ops.php?site=".$row['SITE_ID']."')\">".$row['SITE_ID']."</a>";
                }else{
			$sitelink = "<a href='' onclick=\"return show_store('support.php?site=".$row['SITE_ID']."')\">".$row['SITE_ID']."</a>";
                }
                echo "<td class=' " . $tdclass ."'>" .  $sitelink."</td>";
		// ****  Interbnet provider  ****
		echo "<td class=' " . $tdclass ."' style='text-align:center' >".$row['INET_PROVIDER']."</td>";
		// ****  Total Alerts  ****
		echo "<td class=' " . $tdclass ."' style='text-align:center' >".$total_alerts."</td>";
		echo "<td class=' " . $tdclass ."' style='text-align:center' >".$total5day."</td>";
		echo "<td class=' " . $tdclass ."' style='text-align:center' >".$total3day."</td>";

		// ****  Current Status  ****
		if ($row['MONITOR_STATUS'] > 0){
			$totalhrsdown=round(($row['MONITOR_STATUS'] * $moncycleinterval) / 60);
			echo "<td class=' " . $tdclass ."'><font color='red'>DOWN " .  $totalhrsdown." Hrs</font></td>";
		}else{
			echo "<td class=' " . $tdclass ."'><font color='green'>Online</font></td>";
		}
		echo "</tr>";
        }

}
if (mysqli_num_rows($result)){echo "</tbody>";}
echo "</table>";
mysqli_close($conn);
echo '<p><img
        src="img/valid-html401-blue.png"
        alt="Valid HTML 4.01 Transitional" height="23" width="66">
  </p>';
	echo "<script type=\"text/javascript\"> \n";
	echo "addTableRolloverEffect('down_list','tableRollOverEffect1','tableRowClickEffect1');";
	echo "addTableRolloverEffect('chronic_list','tableRollOverEffect1','tableRowClickEffect1');";
	echo "</script>\n";
 include('netzfooter.php');
echo "</body></html>";

?>
