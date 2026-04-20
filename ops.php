<?php
/*###############################################################
  NETz Network Management system				#
  http://www.proedgenetworks.com/netz			#
#
#
Copyright (C) 2005-2006 Louie Zarrella			#
louiez@proedgenetworks.com				#
#
Released under the GNU General Public License		#
Copy of License available at :				#
http://www.gnu.org/copyleft/gpl.html			#
###############################################################*/
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL);
//session_start();

include 'auth.php';

include_once ("site-monitor.conf.php");

//include_once ('alert-logs.class.php');

include ('write_access_log.php');

include_once ('lmz-functions.php');

require_once ('class.ConfigMagik.php');

require_once ('db.class.php');

include ('get_time_zone.php');
/*
   echo"<pre> --";
   print_r($_SESSION);
   echo "</pre>";
 */
$config = new ConfigMagik($basedir . "field_mapping.ini", true, true);
$db_class = new DB_Class();

//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
?>
<!DOCTYPE html>
<html><head>

<?php
//	+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//	|	User Access code	|
// =====================================================================================================//
$acl = $_SESSION['accesslevel'];
if ($acl <= 1) {
	echo '<script>window.location.href="access_denied.html"</script>'; //
	echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />'; //
} //
  // =====================================================================================================//

$menu1 = $_SESSION['menu1'];
if ($menu1 == "block"){$span1 = "[-]";}elseif($menu1 == "none"){$span1 = "[+]";}else{$span1 = "[-]";}
if ($menu1 == ""){$menu1 = "block";}

$menu2 = $_SESSION['menu2'];
if ($menu2 == "block"){$span2 = "[-]";}elseif($menu2 == "none"){$span2 = "[+]";}else{$span2 = "[-]";}
if ($menu2 == ""){$menu2 = "block";}

$menu3 = $_SESSION['menu3'];
if ($menu3 == "block"){$span3 = "[-]";}elseif($menu3 == "none"){$span3 = "[+]";}else{$span3 = "[-]";}
if ($menu3 == ""){$menu3 = "block";}

$menu4 = $_SESSION['menu4'];
if ($menu4 == "block"){$span4 = "[-]";}elseif($menu4 == "none"){$span4 = "[+]";}else{$span4 = "[-]";}
if ($menu4 == ""){$menu4 = "block";}

$menu5 = $_SESSION['menu5'];
if ($menu5 == "block"){$span5 = "[-]";}elseif($menu5 == "none"){$span5 = "[+]";}else{$span5 = "[-]";}
if ($menu5 == ""){$menu5 = "block";}

?>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8">
<TITLE>NETz Ops</TITLE>
<meta http-equiv="Content-Language" content="en-us">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<?php
$style = $_SESSION['style'];
if ($style == "") {
	 echo $style ;
	$style = "style/midnight-large";
	echo $style ;
} ?>
<link rel="stylesheet" href="<?= $style ?>" type="text/css" id="css">
<link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon" >
<link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon" >


<STYLE TYPE="TEXT/CSS">
.menu {display:block;}
</STYLE>
<script src="ts_picker.js"></script>
<script src="valip.js"></script>
<script src="table_roll_over.js"> </script>
<script src="jquery.js"></script>
<script src="ops_functions.js" ></script> <!-- Link to your JavaScript file -->
<script>
var cnt = 0;
var xmlHttp = new Array();


function add_note(){
	var mydate = new Date();
	var myday = mydate.getDate();
	var mymonth = mydate.getMonth() + 1;
	var myyear = mydate.getFullYear();
	var mydatetext = mymonth + "/" + myday + "/" + myyear;
	var myminutes = mydate.getMinutes();
	if (myminutes<10) { myminutes="0"+myminutes;}
	var mytime = mydate.getHours() + ":" + myminutes;
	var mynote = new String();
	var oldnote;
	var curuser;
	oldnote = document.getElementsByName("txtnotes")[0].value;
	<?php
		if ($_SESSION["name"] == "") {
			$tmpname = $_SESSION["user"];
		}
		else {
			$tmpname = $_SESSION["name"];
		} ?>
	curuser="<?php
		echo htmlentities($tmpname, ENT_QUOTES); ?>";
	oldnote = oldnote + "\n\n" + "**** " + mydatetext +" " + mytime + " " + curuser +  " ****\n" ; // + mynote;

	document.getElementsByName("txtnotes")[0].focus();
	document.getElementsByName("txtnotes")[0].value = oldnote;
}
function set_time_stamp(){
	var mydate = new Date();
	var myday = mydate.getDate();
	var mymonth = mydate.getMonth() + 1;
	var myyear = mydate.getFullYear();
	var mydatetext = mymonth + "/" + myday + "/" + myyear;
	var myminutes = mydate.getMinutes();
	if (myminutes<10) { myminutes="0"+myminutes;}
	var mytime = mydate.getHours() + ":" + myminutes;
	var mynote = new String();
	var oldnote;
	var curuser;
	oldnote = document.getElementsByName("txtnotes2")[0].value;
	<?php
		if ($_SESSION["name"] == "") {
			$tmpname = $_SESSION["user"];
		}
		else {
			$tmpname = $_SESSION["name"];
		} ?>
	curuser='<?php
		echo htmlentities($tmpname, ENT_QUOTES); ?>';
	oldnote = oldnote + "\n\n" + "**** " + mydatetext +" " + mytime + " " + curuser +  " ****\n";

	document.getElementsByName("txtnotes2")[0].focus();
	document.getElementsByName("txtnotes2")[0].value = oldnote;

}

var noWrite=true;
function validateinfo(){
	var i;
	var ans;
	<?php
		if ($_SESSION['accesslevel'] >= 7) {
			echo "noWrite=false;";
		} ?>

	if (noWrite){return 0;}
	if (change_made) {
		ans = confirm("Changes made \n Save Changes ?");
		if (ans == false) {return false;}else {document.myform.submit();return false;}
	}
}
</script>

<?php
if (isset($_POST["txtselectedsearch"])){
	$ADVSELECTEDFIELD = trim($_POST["txtselectedsearch"]);
}else{
	$ADVSELECTEDFIELD = "";
}
if ($ADVSELECTEDFIELD == "") {
	if (isset($_GET["selectedfield"])){
		$ADVSELECTEDFIELD = trim($_GET["selectedfield"]);
	}
}
if (isset($_POST["txtadvancedsearch"])){
	$ADVSEARCHSTR = trim($_POST["txtadvancedsearch"]);
}else{
	$ADVSEARCHSTR = "";
}
if ($ADVSEARCHSTR == "") {
	if (isset($_GET["advsearch"])){
		$ADVSEARCHSTR = $_GET["advsearch"];
	}
}
// Set the default table title colors
$STORECLS = "alink";
$CITYCLS = "alink";
$GROUPCLS = "alink";
$SERVICECLS = "alink";
$IPCLS = "alink";
$MONCLS = "alink";
$ACTIVECLS = "alink";
$modCLS = "alink";
$SELECTEREGIONLS = "alink";
$STORETYPECLS = "alink";
if (isset($_GET["Sort"])){ $SORTFIELD = $_GET["Sort"];} else{ $SORTFIELD = ""; }


// Get ping GET string
if (isset( $_GET["Ping"])){
	$PING = $_GET["Ping"];
}else{ $PING = "";}
// If no ping GET try POST
if ($PING == ""){
	if (isset($_POST["Ping"])){
		$PING = $_POST["Ping"];
	}
}
// Get group GET string
if (isset($_GET["group"])){
	$STRGROUP = $_GET["group"];
}else{$STRGROUP = "";}
// sets to a double quote
$Q = chr(34);
// set Search field and title color
if ($SORTFIELD == "city") {
	$SORTFIELD = "CITY";
	$CITYCLS = "alinkgreen";
}
elseif ($SORTFIELD == "group") {
	$SORTFIELD = "GROUP_NAME";
	$GROUPCLS = "alinkgreen";
}
elseif ($SORTFIELD == "ip") {
	$SORTFIELD = "LAN_IP";
	$IPCLS = "alinkgreen";
}
elseif ($SORTFIELD == "service") {
	$SORTFIELD = "SERVICE_TYPE";
	$SERVICECLS = "alinkgreen";
}
elseif ($SORTFIELD == "monitored") {
	$SORTFIELD = "MONITOR_ENABLE";
	$MONCLS = "alinkgreen";
}
elseif ($SORTFIELD == "active") {
	$SORTFIELD = "ACTIVE_DATE";
	$ACTIVECLS = "alinkgreen";
}
elseif ($SORTFIELD == "model") {
	$SORTFIELD = "ROUTER_MODEL";
	$modCLS = "alinkgreen";
}
elseif ($SORTFIELD == "selected") {
	$SORTFIELD = $ADVSELECTEDFIELD;
	$SELECTEREGIONLS = "alinkgreen";
}
elseif ($SORTFIELD == "storetype") {
	$SORTFIELD = "SITE_TYPE";
	$STORETYPECLS = "alinkgreen";
}
else {
	$SORTFIELD = "SITE_ID";
	$STORECLS = "alinkgreen";
}
// echo $SORTFIELD;
if ($STRGROUP != "") {
	if ($SORTFIELD == "LAN_IP"){
		// This will sort ip addresse correctly along with domain names
		$SQL = "SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID)";
		$SQL .= " WHERE GROUP_NAME = '" . $STRGROUP . "' ORDER BY ";
		$SQL .= "CASE 
            		WHEN " . $SORTFIELD . " REGEXP '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$' THEN INET_ATON(" . $SORTFIELD . ")
            		ELSE " . $SORTFIELD . "
        		END";

	}else{
		$SQL = "SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID)";
		$SQL = $SQL . " WHERE GROUP_NAME = " . $Q . $STRGROUP . $Q . " ORDER BY " . $SORTFIELD;
	}
}
else {
	if (isset($_POST["txtSearch"])){
		$STRSTORENUM = trim($_POST["txtSearch"]);
	}else{ $STRSTORENUM = "";}
	if ($STRSTORENUM == "") {
		$STRSTORENUM = isset($_GET["site"]) ? trim($_GET["site"]) : '';
	}
	if ($STRSTORENUM == "") {
		$SQL = "SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID)";
		if ($ADVSELECTEDFIELD != "") {
			if (strpos(strtolower($ADVSELECTEDFIELD) , "date") > 0) {
				$converteddate = date('Y-m-d', strtotime(trim($ADVSEARCHSTR)));
				echo $converteddate;
				echo $converteddate_ceiling;
				$SQL.= " WHERE " . $ADVSELECTEDFIELD . " LIKE '" . $converteddate . "'";
				$SQL.= " ORDER BY " . $SORTFIELD;
			}elseif($SORTFIELD == "LAN_IP"){
                		// This will sort ip addresse correctly along with domain names
				$SQL = $SQL . " WHERE " . $ADVSELECTEDFIELD;
				$SQL = $SQL . " Like '%" . $ADVSEARCHSTR . "%' ORDER BY ";
                		$SQL .= "CASE 
                        		WHEN " . $SORTFIELD . " REGEXP '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$' THEN INET_ATON(" . $SORTFIELD . ")
                        		ELSE " . $SORTFIELD . "
                        		END";
			}else {
				$SQL = $SQL . " WHERE " . $ADVSELECTEDFIELD;
				$SQL = $SQL . " Like '%" . $ADVSEARCHSTR . "%'" . " ORDER BY " . $SORTFIELD;
			}
		}
		else {
			if ($ADVSEARCHSTR == "") {
				$SQL = "SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE 1=0";
			}
			else {
				$SQL = "SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID)";
				$SQL = $SQL . "WHERE  CITY LIKE '%" . $ADVSEARCHSTR . "%'";
				$ADVSELECTEDFIELD = "CITY";
			}
		}
	}
	else {
		$SQL = "SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID)";
		if (isset($_GET["site"]) && !isset($_GET["Sort"])) {
			$SQL = $SQL . " WHERE SITE_ID = '" . $STRSTORENUM . "'" . " ORDER BY " . $SORTFIELD . "";
		}
		else {
			$SQL = $SQL . " WHERE SITE_ID Like '%" . $STRSTORENUM . "%'" . " ORDER BY " . $SORTFIELD . "";
		}
	}
}
?>
<?php
// End Head tag
echo "</head>";
// Check to be sure there is an entry in MONITOR INFO that matches SITEDATA - not sure if this is needed anymore
// ======================================================================================================//
/*
   $test_sql = "select SITEDATA.SITE_ID from SITEDATA "; //
   $test_sql.= "left join MONITORINFO on SITEDATA.SITE_ID = MONITORINFO.SITE_ID "; //
   $test_sql.= "where MONITORINFO.SITE_ID is null;"; //
   $test_rows = run_netz_query($test_sql);
   foreach($test_rows as $test_row) {
   $fix_sql = "INSERT INTO MONITORINFO (SITE_ID) VALUES('" . $test_row['SITE_ID'] . "')"; //
   run_netz_query($fix_sql);
   } 
 */
// =====================================================================================================//
/************************
  Query Database	/
 ***********************/

$rows = run_netz_query($SQL);
$num = query_num_rows($rows);
//$conn = mysqli_connect(NETZ_DB_SERVER,  NETZ_DB_USERNAME,  NETZ_DB_PASSWORD,NETZ_DATABASE);
//$result=mysqli_query($conn,$SQL);
//$num=mysqli_num_rows($result);
echo "X".$num;
// if it finds one record fron query or a new record from the "WHERE 1=0"
// This "if" statement closing bracket is further
if ($num == 1 || $num == 0) {
	// Because we use the one query for one site or many
	// we just set the result $rows to $row to align with the code below
	$row = $rows;
	if ($num == 0) {
		$newrecord = "new";
	}else{ $newrecord = "";}

	// Lets set some variables so we can check if they are set and stop php warnings
	$site_id = isset($row["SITE_ID"]) ? trim($row["SITE_ID"]) : '';
	$monitor_enable = isset($row["MONITOR_ENABLE"]) ? $row["MONITOR_ENABLE"] : '';
	$monitor_http_enable = isset($row["MONITOR_HTTP_ENABLE"]) ? $row["MONITOR_HTTP_ENABLE"] : '';

	// Print the body tag for single or new record
	echo "<body  id=\"bodyid\" onload=\"return on_load_check()\" onunload=\"window_close()\">";
	add_plugin('ops_load', $site_id);
	// } closing bracket is further down the page
	// Side Menu
/*
	echo '<script  src="menulz.js"> </script>';
	// Check access rights and load menu items for that level
	if ($_SESSION['accesslevel'] >= 9) {
		echo '<script  src="menu-data-rwa.js"> </script>';
	}elseif ($_SESSION['accesslevel'] >= 4) {
		echo '<script  src="menu-data-rw.js"> </script>';
	}else {
nn		echo '<script  src="menu-data-ro.js"> </script>';
	}
*/
?>
<script>
function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  sidebar.classList.toggle('open');
}

</script>

<div class="sidebar">
<button class="toggle-button" onclick="toggleSidebar()">☰ Menu</button>
  <div class="menu-content">
    <ul>
      <li><a href="main.php">NETz Home</a></li>
      <li><a href="support.php">Support Dashboard</a></li>
      <li><a href="site-monitor.php">Down Sites</a></li>
	<li><a href="querycreate.php">Query Builder</a></li>
	<li><a href="useradmin.php">User Admin</a></li>
	<li><a href="netz-config.php">NETz Config</a></li>
	<li><a href="tools/tools.php">NETz Tools</a></li>
	<li><a href="tools/mac.php">MAC Address Search</a></li>
    </ul>
  </div>
</div>

<script src="string_functions.js"> </script>
<!-- Set a javascript Variable with the store number for use with Plugins -->
<script>
site="<?php echo $site_id; ?>" </script>
<script> document.title = site; </script>
<!-- Page Header --> 
<input type="hidden" name="txtdelete" size="20"> <input id="conn-type-text" type="hidden" name="txtconnectiontype" size="20"> <table	style=" height:18px ; width:99%" id="AutoNumber4" > <tr>
<td style="border:none;" width="11%" height="18" valign="bottom">
	<a target="_self" href="main.php" style="outline:none">
		<img border="0" alt="netz.jpg" src="netz.jpg" width="164" height="49" align="middle">
	</a>
</td>
<td style="border:none;" width="38%" height="18" valign="bottom" >
<script> getBrowserInfo(); </script>
<?php
if ($site_id != "") {
	$dla = $row["DATE_LAST_ALERT"];
	if ($dla == "" || $dla == "0000-00-00") {
		$dla = "";
	}
	else {
		$dla = date("m/d/Y h:m a", strtotime($row["DATE_LAST_ALERT"]));
	}
	/*
	 * Alerts last 30 days
	 */
	//$alertscls = new SiteLog();
	echo "<font color='white'>";
	echo "Alerts last 30 days </font>";
	echo "<font color ='red'>" . get_site_log_count($site_id, 30) . "</font>";
	echo "&nbsp;&nbsp;";
	echo "<a href=\"@\" ";
	echo "onclick=\"window.open('alert-logs.php?site=" . $site_id . "&amp;days=30','','";
	echo "width=400,height=400,resizable=yes,scrollbars=yes,status=yes'); ";
	echo "return false;\"> Show</a>";
	/*
	 * Last atert
	 */
	echo "<br />last alert " . $dla . "";
	// Themes Link
	echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"@\" ";
	echo "onclick=\"window.open('user-theme.php?user=";
	echo urlencode($_SESSION['user']) . "','','width=325,height=30,";
	echo "resizable=yes,scrollbars=yes,status=yes'); ";
	echo "return false\">Themes</a>";
}
?>
</td>
<td style="border:none;" width="30%" height="18" valign="bottom">		
<!-- Show the sites down banner -->
<iframe height="50px" width="350px" name="myframe" frameborder="0" scrolling="no" src="sites-down.php"> </iframe>
</td>
<td style="border:none;" width="21%" height="18" align="left">&nbsp;
<?php
add_plugin('ops_top_right', $site_id); ?>
<?php
if ($site_id != "" && $monitor_enable == 1) {
	echo '<a href="@" style="outline:none"';
	echo 'onclick="window.open(\'chart-pop.php?back=3&amp;site=' . $site_id . '\'';
	echo ',\'\',\'width=640,height=480,resizable=yes\'); return false">';
	echo '<img alt="." ';
	echo 'src="build-chart.php?back=3&amp;size=tiny&amp;site=' . $site_id . '">';
	echo '</a>';
}
?>
</td>
</tr>
</table>
<!-- Ebd Page Header -->

<table id="monitortable" 
style="border-top:2px inset #008000; 
width: 99%;
height:26;
font-size:8pt; 
border-collapse:collapse; 
border-left-color:#008000; 
border-left-width:1; 
border-right-width:1; 
border-bottom-width:1" >
<!-- Row 1  -->
<tr>
<td width="25%" style="border-top: 2px solid #000080" height="24">
<?php
// Shows the Monitor config page link if user level 5 or greater
if (($site_id != "" ) && $_SESSION['accesslevel'] >= 5) {
	// Monitor/Alert Options link start completed with $options below
	echo "<span id=\"mon_link\">";
	echo '<a href="@" onclick="window.open(\'site-config.php?site=' . $site_id . '\',\'\',\'';
	// quick hack for changing the popup window size
	if (strpos($_SESSION['style'], "small")) {
		$options = 'width=525,height=700,resizable=yes,scrollbars=yes,status=yes';
	}
	elseif (strpos($_SESSION['style'], "large")) {
		$options = 'width=650,height=775,resizable=yes,scrollbars=yes,status=yes';
	}
	else {
		$options = 'width=575,height=725,resizable=yes,scrollbars=yes,status=yes';
	}
	$options.= '\'); b=document.getElementsByTagName(\'body\')[0];';
	// for Firefox using CSS standards
	$options.= ' b.style.opacity = \'.5\';';
	// Internet Exploder Suxs.... MS always going against the grain
	$options.= 'b.style.filter = \'alpha(opacity=50)\'; return false;">';
	$options.= 'Monitor/Alert Options</a></span>';
	echo $options;
}
?>
</td>


<td width="25%" style="border-top: 2px solid #000080" height="24">
<?php
// Monitor Charts link
if ($monitor_enable == 1) {
	echo '<a href="site-charts.php?site=' . $site_id . '&amp;return=ops.php';
	echo '&amp;group=' . $row["GROUP_NAME"] . '">Monitor Charts</a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	// Monitor Bar graph link
	echo '<a href="@" onclick="window.open(\'site-stats-graph.php?site=' . $site_id;
	echo '&amp;days=14 \',\'\',\'width=600,height=250,resizable=yes,scrollbars=yes,status=yes\');';
	echo 'return false">Bar graph </a>';
}
?>

</td>
<td width="25%" style="border-top: 2px solid #000080" height="24">
<?php
// Monitor Logs link
if ($monitor_enable == 1) {
	echo '<a href="@" onclick="window.open(\'site-stats.php?site=' . $site_id;
	echo '&amp;days=7&amp;daysinclude=8&amp;detail=1\',\'\',\'width=640,height=480,resizable=yes,scrollbars=yes,status=yes\');';
	echo ' return false" >Monitor Logs</a>';
}
?>
</td>
<td width="25%" style="border-top: 2px solid #000080" height="24">
<?php
// HTTP Monitor Logs link
if ($monitor_http_enable == 1 && $monitor_enable == 1) {
	echo '<a href="@" onclick="window.open(\'site-http-stats.php?site=' . $site_id;
	echo '&amp;days=7&amp;daysinclude=8&amp;detail=1\',\'\',\'width=640,height=480,resizable=yes,scrollbars=yes,status=yes\');';
	echo ' return false" >HTTP Logs</a>';
}
?>

</td>
</tr>
</table>


<!-- Search Form  -->
<!-- Table 6  -->
<form method="POST" action="ops.php"  name="myform2" onsubmit="return validateinfo()">
<table style="border:none; width: 99%;
font-size:8pt;" >

<!-- Row 1  -->
<tr>
<td style="width: 45%" >&nbsp;&nbsp;
<!-- Site Search -->
<?php
if ($site_id != "") {
	// Monitor/Alerting status
	if ($monitor_enable == 1) {
		echo "Monitor <font color='#339900'>ENABLED&nbsp;&nbsp;</font>";
	}
	else {
		echo "Monitor <font color='red'>Disabled&nbsp;&nbsp;</font>";
	}
	if ($row["SUPPORT_ALERT_OFFLINE"] == 1 || $row["SUPPORT_ALERT_ONLINE"] == 1) {
		echo "Support Alert &nbsp;<font color='#339900'>ENABLED&nbsp;&nbsp;</font>";
	}
	else {
		echo "Support Alert &nbsp;<font color='red'>Disabled&nbsp;&nbsp;</font>";
	}
	if ($row["VIP_ALERT_OFFLINE"] == 1 || $row["VIP_ALERT_ONLINE"] == 1) {
		echo "&nbsp;VIP Alert &nbsp;<font color='#339900'>ENABLED</font>";
	}
	else {
		echo "&nbsp;VIP Alert &nbsp;<font color='red'>Disabled</font>";
	} ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span id="temp_message" ></span>
		<?php
}
add_plugin('ops_monitor_panel', $site_id);
?>
</td>

<td style="width: 55%" align="left">&nbsp;
<b>Field Search</b>&nbsp;
<select  size="1" name="txtselectedsearch" onchange="myform2.txtadvancedsearch.focus()" >
	<option value="SITE_ID"><?= Display_name('SITE_ID') ?></option>
<?php
/************************************************
 * Load up all the DB feilds for advanced search
 *************************************************/
$site_list = get_column_list("SITEDATA");
$mon_list = get_column_list("MONITORINFO");
$col_list = array_merge($site_list, $mon_list);
// sort the names
asort($col_list);
$i = 0;
$option_lines="";	// Init variable
			// create the option list
foreach($col_list as $key => $value) {
	/* Check if it is a Password feild
	   and if the user has high enough
	   accesss to view them */
	if (strpos(trim($key) , 'PASSWORD')) {
		if ($_SESSION['accesslevel'] >= 7) {
			$option_lines.= '<option value="' . $key . '">';
			$option_lines.= $value . ' </option>';
		}
	}
	else {
		$option_lines.= '<option value="' . $key . '">';
		$option_lines.= $value . ' </option>';
	}
	$i++;
}
// ********************************************************************************
echo $option_lines;
?>
</select>&nbsp;
<!-- Advanced Search field -->
<input  type="text" name="txtadvancedsearch" size="17">&nbsp;&nbsp;&nbsp; <!-- Advanced Search Button --> <input style="font-size: 8pt" class="button" type="submit" value="Search" name="B5"> </td> </tr>
</table>
</form>
<?php
// ***********************************************************************************************
// If there is no store selected....just show search box					//
if ($site_id == "" ) {
	echo "</body></html>";
	exit;
} //
  // ************************************************************************************************
?>
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>

	<form method="POST" 
		action="write_ops.php" 
		name="myform" 
		id ="myform" 
		onsubmit="return validateinfo()">
		<?php
}
elseif ($_SESSION['accesslevel'] >= 2) { ?>
	<form method="POST" 
		action="" 
		name="myform" 
		id ="myform" 
		onsubmit="javascript:return false;">

		<?php
} ?>
<input type="hidden" name="txtdelete" size="20"> <table id="AutoNumber1" style="width: 99%;font-size:8pt;border-collapse:collapse;" > <?php $helpTag = "<img style=\"border:none\" src=\"img/help2.png\" alt=\"help2.png\">"; ?>
<!-- Row 2  -->
<tr style="border:3px solid #5555ff;">
<td width="25%" >
	<input type="hidden" name="txtnewrecord" value="<?= $newrecord ?>" > <!-- Site ID  --> 
	<input class="inputhidden" type="text" title="Store Number" name="txtstorenumber" id="txtstorenumber" size="18" onchange="return on_change_made(this)"  value="<?= $site_id; ?>" 
<?php
if (query_num_rows($row)) {
	print "onkeydown=\"javascript:this.blur()\"";
}
?> 
onmouseover="javascript:window.status='To change Store Number Paste new value'" 
onmouseout="javascript:window.status=''">
&nbsp; <?php
echo Display_name('SITE_ID') ?> &nbsp;&nbsp; 
<input type="hidden" name="txtstorenumberbac" value="<?= $site_id; ?>"> </td> <td  width="25%"> <!-- Group  --> 
<input  type="text" title="Group" id="txtgroup"  name="txtgroup" size="15" value="<?= $row['GROUP_NAME']; ?>" onchange="return on_change_made(this)"  onkeypress="return autoC('GROUP_NAME',this.value,this,event)"> &nbsp; <?php echo Display_name('GROUP_NAME') ?>
(<a title="Show Group"  href="#" onclick="show_group(); return false;"> Show </a>)
Ping
<input type="checkbox" id="ping1" name="ping1" value="ON" size="20"><!-- Group Ping Help -->&nbsp; <a title="Group Help" href="javascript:openhelp('help/groups.html')"><?= $helpTag; ?></a> </td> <td width="25%" >
<!-- Store Hours  -->
<input  type="text" name="txtstorehours" size="20" onchange="return on_change_made(this)" value="<?= $row["SITE_HOURS"]; ?>">&nbsp; <?= Display_name('SITE_HOURS') ?> </td> <td width="25%" > <!-- Time zone  -->
<select size="1" name="txttimezone" id="txttimezone"> <?php $tzone = $row['TIME_ZONE']; ?>
<option selected value="<?php echo $tzone; ?> "><?php echo $tzone; ?></option>
<option value="AST-Atlantic (UTC -4)">AST-Atlantic (UTC -4)</option>
<option value="EST-Eastern (UTC -5)">EST-Eastern (UTC -5)</option>
<option value="CST-Central (UTC -6)">CST-Central (UTC -6)</option>
<option value="MST-Mountain (UTC -7)">MST-Mountain (UTC -7)</option>
<option value="PST-Pacific (UTC -8)">PST-Pacific (UTC -8)</option>
<option value="AKST-Alaska (UTC -9)">AKST-Alaska (UTC -9)</option>
<option value="HST-Hawaiian (UTC -10)">HST-Hawaiian (UTC -10)</option>
<option value="SST-SAMOA (UTC -11)">SST-SAMOA (UTC -11)</option>
<option value="CHST-CHAMORRO (UTC +10)">CHST-CHAMORRO (UTC +10)</option>
</select>
<?php
echo Display_name('TIME_ZONE') ?>
</td>
</tr>
</table>
<a href="@" onclick="return toggleMenu('menu1','span1');"> <span id="span1" style="font-size:8px"><?= $span1; ?></span></a>
<span style="font-size:8px; font-weight:bold">IP Info</span><br />
<div class="menu" ID="menu1" style="display:<?= $menu1; ?>" >
<table  style="border-top:2px inset #008000;
width: 99%;
font-size:8pt;
border-collapse:collapse;
border-left-color:#008000;
border-left-width:1;
border-right-width:1;
border-bottom-width:1" >		

<!-- Row 3  -->
<tr style="border-top:3px solid #555555;border-left:3px solid #555555;border-right:3px solid #555555;">
<td width="25%" >
<!--******************
LAN IP
*******************-->
<input  type="text" title="LAN IP" name="txtip" size="14" onchange="return on_change_made(this)"  value="<?= $row["LAN_IP"]; ?>"> <?php // Title echo "<STRONG>" . Display_name('LAN_IP') . "&nbsp;</STRONG>";
if ($row['MONITOR_IP_FIELD'] == "") {
	$monempty = SITE_IP_DEFAULT;
}else{ 
	$monempty = "";
}
if (($row['MONITOR_IP_FIELD'] == "LAN_IP" || $monempty == "LAN_IP") && ($row['MONITOR_ENABLE'] == 1)) {
	echo "<span style=\"color:#00ff00 ; ";
	echo "background-color:#000000; ";
	echo "font-weight: bold ; ";
	echo "font-size:6pt\" ";
	echo "title=\"Monitored IP\">";
	echo "&nbsp;Mon&nbsp;</span>&nbsp;";
}
// Status Image
echo "<img id=\"lanipimg\" src=\"img/transparentpixel.gif\" ";
echo "width=\"12\" height=\"12\" alt=\"transparentpixel.gif\">";
echo "<span id='lanipimgx'></span>";
echo "<br />";
// Status Ping Code
?>
<script >
setInterval("ping_lights('lanipimg','<?php
echo $row['LAN_IP'] ?>','<?php
echo $monitor_enable ?>')",8000);
</script>
<?php
// Ping button
echo '<input class="button"  type="button" value="P" ';
echo 'title="Ping" onclick="ping_store(\'' . $row['LAN_IP'] . '\')">';
// Telnet button
echo '<input class="button"  type="button" value="T" ';
echo 'title="Telnet" onclick="window.location=\'telnet://';
echo $row['LAN_IP'] . '\'">';
// SSH button
echo '<input class="button"  type="button" value="S" ';
echo 'title="SSH" onclick="window.location=\'ssh://' . $row['LAN_IP'] . '\'">';
// Web connect button
echo '<input class="button"  type="button" value="W" ';
echo 'title="Web Connect" ';
echo 'onclick="window.open(\'http://' . $row['LAN_IP'] . '\' ,\'\',\'\')" >';
?>
<!-- LAN IP Help -->
&nbsp;<a title="Help" 
href="javascript:openhelp('help/lanips.html')">
<?php
echo $helpTag; ?></a>
</td>
<td width="25%">
<!--******************
Gateway IP  
*******************-->
<input  type="text" name="txtrouterip" size="12" onchange="return on_change_made(this)"  value="<?php print $row["LAN_GATEWAY"]; ?>">&nbsp;
<b>&nbsp;
<?php
// Title
echo "<strong>" . Display_name('LAN_GATEWAY') . "</strong>&nbsp;";
if (($row['MONITOR_IP_FIELD'] == "LAN_GATEWAY" || $monempty == "LAN_GATEWAY") && ($row['MONITOR_ENABLE'] == 1)) {
	echo "<span style=\"color:#00ff00 ;background-color:#000000; ";
	echo "font-weight: bold ; ";
	echo "font-size:6pt\" ";
	echo "title=\"Monitored IP\">";
	echo "&nbsp;";
	echo "Mon &nbsp;</span>&nbsp;";
}
// Status Image
echo "<img id=\"gatewayipimg\" src=\"img/transparentpixel.gif\" ";
echo "width=\"12\" height=\"12\" alt=\"transparentpixel.gif\">";
echo "<span id='gatewayipimgx'></span>";
// Ping Code
?>
<script >
setInterval("ping_lights('gatewayipimg','<?php
echo $row['LAN_GATEWAY'] ?>','<?php
echo $monitor_enable ?>')",8000);
</script>
<?php
echo "<br />";
// Ping button
echo '<input class="button"   type="button" value="P" ';
echo 'title="Ping" onclick="ping_store(\'' . $row['LAN_GATEWAY'] . '\')">';
// Telnet button
echo '<input class="button"   type="button" value="T" ';
echo 'title="Telnet" onclick="window.location=\'';
echo 'telnet://' . $row['LAN_GATEWAY'] . '\'">';
	     // SSH button
echo '<input class="button"  type="button" value="S" ';
echo 'title="SSH" onclick="window.location=\'';
echo 'ssh://' . $row['LAN_GATEWAY'] . '\'">';
	  // Web connect button
echo '<input class="button" type="button" value="W" ';
echo 'title="Web Connect" ';
echo 'onclick="window.open(\'';
echo 'http://' . $row['LAN_GATEWAY'] . '\' ,\'\',\'\')" >';
?>
</b>
<!-- LAN Gateway Help -->
&nbsp;	
<a title="Help" 
href="javascript:openhelp('help/lanips.html')"><?php
echo $helpTag; ?>
</a>
</td>
<td width="25%" >
<!--******************
LAN Netmask
*******************-->
<input	type="text" name="txtlannetmask" size="12" onchange="return on_change_made(this)"  
value="<?php
print $row["LAN_NETMASK"]; ?>">&nbsp;
<?php
echo Display_name('LAN_NETMASK') ?></td>
<td width="25%" >
<input  type="text" title="WAN Type" name="txtwantype" size="18" onchange="return on_change_made(this)" value="<?php print $row["WAN_AUTHENTICATION_TYPE"]; ?>">&nbsp;
<?php
echo Display_name('WAN_AUTHENTICATION_TYPE') ?>
</td>
</tr>
<!-- Row 4  -->
<tr style="border-bottom:3px solid #555555;border-left:3px solid #555555;border-right:3px solid #555555;">
<td width="25%">
<input	type="text" title="Public IP" name="txtpublicipaddress" id="txtpublicipaddress" 
size="15" 
onchange="return on_change_made(this)"  
value="<?php
print $row["WAN_IP"]; ?>">&nbsp; 
<?php
echo Display_name('WAN_IP') ?>&nbsp;<?php
if (($row['MONITOR_IP_FIELD'] == "WAN_IP" || $monempty == "WAN_IP") && ($row['MONITOR_ENABLE'] == 1)) {
	echo "<span style=\"color:#00FF00 ;";
	echo "background-color:#000000; ";
	echo "font-weight: bold ; ";
	echo "font-size:6pt\" ";
	echo "title=\"Monitored IP\">";
	echo "&nbsp;Mon&nbsp;</span>&nbsp;";
}
// Status Image
echo "<img id=\"wanipimg\" src=\"img/transparentpixel.gif\" ";
echo "width=\"12\" height=\"12\" alt=\"transparentpixel.gif\" >";
echo "<span id='wanipimgx'></span>";
echo "<br />";
// Ping Code
?>
<script >
setInterval("ping_lights('wanipimg','<?php
echo $row['WAN_IP'] ?>','<?php
echo $monitor_enable ?>')",8000);
</script>
<?php
// Ping button
echo '<input class="button" type="button" value="P" ';
echo 'title="Ping" onclick="ping_store(\'' . $row['WAN_IP'] . '\')">';
// Telnet button
echo '<input class="button" type="button" value="T" ';
echo 'title="Telnet" onclick="window.location=\'';
echo 'telnet:' . $row['WAN_IP'] . '\'">';
// SSH button
echo '<input class="button"  type="button" value="S" ';
echo 'title="SSH" onclick="window.location=\'';
echo 'ssh://' . $row['WAN_IP'] . '\'">';
	  // Web connect button
echo '<input class="button" type="button" value="W" ';
echo 'title="Web Connect" ';
echo 'onclick="window.open(\'';
echo 'http://' . $row['WAN_IP'] . '\' ,\'_blank\',\'\')" >';
?>

</td>
<td width="25%">
<input	type="text" title="Default Gateway" name="txtdefaultgateway" id="txtdefaultgateway" size="15" onchange="return on_change_made(this)"  value="<?= $row["WAN_GATEWAY"]; ?>">&nbsp; <?php echo Display_name('WAN_GATEWAY') ?>&nbsp;
<?php
if (($row['MONITOR_IP_FIELD'] == "WAN_GATEWAY" || $monempty == "WAN_GATEWAY") && ($row['MONITOR_ENABLE'] == 1)) {
	echo "<span style=\"color:#00FF00 ; ";
	echo "background-color:#000000; ";
	echo "font-weight: bold ; font-size:6pt\" ";
	echo "title=\"Monitored IP\">";
	echo "&nbsp;Mon&nbsp;</span>&nbsp;";
}
// Status Image
echo "<img id=\"wangatewayipimg\" src=\"img/transparentpixel.gif\" ";
echo "width=\"12\" height=\"12\" alt=\"transparentpixel.gif\" >";
echo "<span id='wangatewayipimgx'></span>";
echo "<br />";
// Ping code
?>
<script >
setInterval("ping_lights('wangatewayipimg','<?php
echo $row['WAN_GATEWAY'] ?>','<?php
echo $monitor_enable ?>')",8000);
</script>
<?php
// Ping button
echo '<input  class="button" type="button" value="P" ';
echo 'title="Ping" onclick="ping_store(\'' . $row['WAN_GATEWAY'] . '\')">';
// Telnet button
echo '<input class="button" type="button" value="T" ';
echo 'title="Telnet" ';
echo 'onclick="window.location=\'telnet:' . $row['WAN_GATEWAY'] . '\'">';
// SSH button
echo '<input class="button"  type="button" value="S" ';
echo 'title="SSH" ';
echo 'onclick="window.location=\'ssh://' . $row['WAN_GATEWAY'] . '\'">';
// Web connect button
echo '<input class="button"  type="button" value="W" ';
echo 'title="Web Connect" ';
echo 'onclick="window.open(\'http://';
echo $row['WAN_GATEWAY'] . '\' ,\'\',\'\')" >';
?>
</td>
<td width="25%" >
<input	type="text" title="Netmask" name="txtnetmask" id="txtnetmask" size="16" onchange="return on_change_made(this)"  value="<?= $row["WAN_NETMASK"]; ?>">&nbsp; <?= Display_name('WAN_NETMASK') ?> <input	class="button"  type="button" value="Validate IP" name="B10" onclick="validate_ip()" > </td> <td width="25%" >
<input	type="text" title="Public IP Range" name="txtstaticiprange" size="20" onchange="return on_change_made(this)"  value="<?= $row["WAN_IP_RANGE"]; ?>">&nbsp; <?= Display_name('WAN_IP_RANGE') ?> </td> </tr>
<!-- Row 5  -->
</table>
</div>
<!-- End Table site info  -->
<a href="@" onclick="return toggleMenu('menu2','span2');"> <span id="span2" style="font-size:8px"><?= $span2; ?></span></a>
<span style="font-size:8px; font-weight:bold">Site Info</span><br />
<div class="menu" ID="menu2" style="display:<?php
print $menu2; ?>" >
<table  style="border-top:2px inset #008000;
width: 99%;
font-size:8pt;
border-collapse:collapse;
border-left-color:#008000;
border-left-width:1;
border-right-width:1;
border-bottom-width:1" >
<tr style="border-top:3px solid #008855;border-left:3px solid #008855;border-right:3px solid #008855;">
<?php
$IMAGEMAP = isset($row["SITE_IMAGE_MAP"]) ? $row["SITE_IMAGE_MAP"] : '';
// If there is no Image in DB don't show a link
if ($IMAGEMAP == "") {
	echo '<td >&nbsp; ';
	echo '<b><font color="#FF0000">No Site Image</font></b>';
	// don't display Add button if no site is selected
	if (($site_id != "") && ALLOW_DOCUMENT_UPLOADS == "1" && $_SESSION['accesslevel'] >= 7) {
		echo '&nbsp;&nbsp;<a title="Add Image or PDF"  href="@" ';
		echo 'onclick="javascript:window.open(\'get_image.php\',\'\'';
		echo ',\'width=350,height=175\') ;';
		echo ' return false;">Add/Delete</a>';
		// <!-- Image Help -->
		echo '&nbsp;<a title="Help" ';
		echo "href=\"javascript:openhelp('help/imageadd.html')\">";
		echo $helpTag . " </a>";
		echo '<input  type="hidden" name="txtnetworkmapimage" ';
		echo 'id="txtnetworkmapimage" value="">';
	}
	echo '</td>';
}
else {
	echo '<td >&nbsp; ';
	echo '<a  href="a.htm" ';
	echo 'onclick="return show_image(\'image_pop.php?image=';
	echo $IMAGEMAP . '\')">Site Image &nbsp;';
	echo '</a>';
	if (($site_id != "") && ALLOW_DOCUMENT_UPLOADS == "1" && $_SESSION['accesslevel'] >= 7) {
		echo '&nbsp;&nbsp;<a title="Add Image or PDF"  href="@" ';
		echo 'onclick="javascript:window.open';
		echo '(\'get_image.php\',\'\',\'width=350,height=175\') ;';
		echo ' return false;">Add/Delete</a>';
		// <!-- Image Help -->
		echo '&nbsp;<a title="Help" ';
		echo "href=\"javascript:openhelp('help/imageadd.html')\">";
		echo $helpTag . " </a>";
		echo '<input  type="hidden" ';
		echo 'name="txtnetworkmapimage" id="txtnetworkmapimage" size="20" ';
		echo 'onchange="return on_change_made(this)"  ';
		echo 'value="' . $row["SITE_IMAGE_MAP"] . '">&nbsp</td>';
	}
}
$IMAGEMAP2 = $row["GROUP_IMAGE_MAP"];
// If there is no Image in DB don't show a link
if (!isset($IMAGEMAP2) || trim($IMAGEMAP2) == "") {
	echo '<td >&nbsp; ';
	echo '<b><font color="#FF0000">No Group Image</font></b>';
	// don't display Add button if no site is selected
	if (($site_id != "") && ALLOW_DOCUMENT_UPLOADS == "1" && $_SESSION['accesslevel'] >= 7) {
		echo '&nbsp;&nbsp;<a title="Add Image or PDF"  href="@" ';
		echo 'onclick="javascript:window.open';
		echo '(\'get_group_image.php\',\'\',\'width=350,height=175\') ;';
		echo ' return false;">Add/Delete</a>';
		// <!-- Image Help -->
		echo '&nbsp;<a title="Help" ';
		echo "href=\"javascript:openhelp('help/imageadd.html')\">";
		echo $helpTag . " </a>";
		echo '<input  type="hidden" name="txtgroupimage" ';
		echo 'id="txtgroupimage"size="20" value="" >';
	}
	echo '</td>';
}
else {
	echo '<td >&nbsp; ';
	echo '<a  href="a.htm" ';
	echo 'onclick="return show_image(\'image_pop.php?image=';
	echo $IMAGEMAP2 . '\')">Group Image &nbsp;';
	echo '</a>';
	if (($site_id != "") && ALLOW_DOCUMENT_UPLOADS == "1" && $_SESSION['accesslevel'] >= 7) {
		// Add Delete link
		echo '&nbsp;&nbsp;<a title="Add Image or PDF"  href="@" ';
		echo 'onclick="javascript:window.open';
		echo '(\'get_group_image.php\',\'\',\'width=350,height=175\') ;';
		echo ' return false;">Add/Delete</a>';
		// <!-- Image Help -->
		echo '&nbsp;<a title="Help" ';
		echo "href=\"javascript:openhelp('help/imageadd.html')\">";
		echo $helpTag . " </a>";
		echo '<input  type="hidden" name="txtgroupimage" id="txtgroupimage"size="20" ';
		echo 'onchange="return on_change_made(this)"  ';
		echo 'value="' . $row["GROUP_IMAGE_MAP"] . '">&nbsp;</td>';
	}
}
?>
<td >
<?php
add_plugin('ops_middle_1', $site_id); ?>
&nbsp;
LAT
<input  type="text" title="Latitude" name="txtlatitude" size="8" 
onchange="return on_change_made(this)"  
value="<?php
print $row["LATITUDE"]; ?>">&nbsp;
LON
<input  type="text" title="Longitude" name="txtlongitude" size="8" 
onchange="return on_change_made(this)"  
value="<?php
print $row["LONGITUDE"]; ?>">&nbsp;
</td>
<td>
<?php
add_plugin('ops_middle_2', $site_id); ?>
&nbsp;
</td>
</tr>
<!-- Row 1  -->
<tr style="border-left:3px solid #008855;border-right:3px solid #008855;">
<td width="25%" >
<input  type="text" title="Site Name" id="txtsitename" name="txtsitename" size="28" onchange="return on_change_made(this)" value="<?php print $row["SITE_NAME"]; ?>">&nbsp; 
<?php
echo Display_name('SITE_NAME') ?>
</td>
<td width="25%" >
<select	size="1" 
style="position:relative" 
title="REGION" 
id="txtdc" 
name="txtdc" onchange="return on_change_made(this)" >
<option value="<?php
print $row["REGION"]; ?>" SELECTED  >
<?php
print $row["REGION"]; ?>
</option>
<?php
// Load Regions from region.txt
$filename = $basedir . "region.txt";
$fp = fopen($filename, "r");
$contents = fread($fp, filesize($filename));
fclose($fp);
$file_lines = explode("\n", $contents);
sort($file_lines, SORT_STRING);
foreach($file_lines as $line) {
	if ($line != "") {
		echo '<option value="' . $line . '">' . $line . '</option>';
	}
}
?>
</select>&nbsp; 
<?php
echo Display_name('REGION') ?>
</td>
<td width="25%">
<select	title="Support Center" 
name="txtadp" 
onchange="return on_change_made(this)">
<option value="<?php
echo $row["SUPPORT_CENTER"]; ?>" SELECTED  >
<?php
echo $row["SUPPORT_CENTER"]; ?>
</option>
<?php
// Load Support Centers from support-centers.txt
unset($file_lines);
$file_lines = $db_class->get_support_centers();
foreach($file_lines as $line) {
	if ($line != "") {
		echo '<option value="' . $line . '">' . $line . '</option>';
	}
}
?>
</select>&nbsp; 
<?php
echo Display_name('SUPPORT_CENTER') ?>
<a title="Support Center Info" 
style="outline:none"
href="javascript:openhelp('support_info.php?support=<?php
echo $row["SUPPORT_CENTER"]; ?>')">
<img style="border:none" src="img/info-icon.png" alt="info-icon.png">
</a>
</td>
<td width="25%" >
<select	id="txtbsfsr"
style="position:relative"
title="Feild Rep" 
name="txtbsfsr" onchange="return on_change_made(this)">
<option value="<?php
print $row["FIELD_REP"]; ?>" SELECTED  >
<?php
print $row["FIELD_REP"]; ?>
</option>
<?php
// Load Feild service reps from fsr.txt
$filename = $basedir . "fsr.txt";
$fp = fopen($filename, "r");
$contents = fread($fp, filesize($filename));
fclose($fp);
$file_lines = explode("\n", $contents);
sort($file_lines, SORT_STRING);
foreach($file_lines as $line) {
	if ($line != "") {
		echo "<option value=\"" . trim($line) . "\">";
		echo trim($line) . "</option>\n";
	}
}
?>
</select>&nbsp;  
<?php
echo Display_name('FIELD_REP') ?>
</td>
</tr>
<!-- Row 2  -->
<tr style="border-left:3px solid #008855;border-right:3px solid #008855;">
<td >
<!-- Address  -->
<input	type="text" title="Street Address" name="txtaddress" size="25" 
onchange="return on_change_made(this)"  
value="<?php
print $row["ADDRESS"]; ?>">&nbsp; 
<?php
echo Display_name('ADDRESS') ?> 
</td>
<td >
<!-- City  -->
<input	type="text" title="City" name="txtcity" size="20" 
onchange="return on_change_made(this)"  
value="<?php
print $row["CITY"]; ?>">&nbsp; 
<?php
echo Display_name('CITY') ?>
</td>
<td >
<!-- State  -->
<select	id="txtst" 
title="State" 
name="txtst">
<option value="<?php
print $row["ST"]; ?>"><?php
print $row["ST"]; ?></option>
<option value="AL">ALABAMA-AL</option>
<option value="AK">ALASKA-AK</option>
<option value="AZ">ARIZONA-AZ</option>
<option value="AR">ARKANSAS-AR</option>
<option value="BZ">Belize-BZ</option>
<option value="CA">CALIFORNIA-CA</option>
<option value="CO">COLORADO-CO</option>
<option value="CT">CONNECTICUT-CT</option>
<option value="DE">DELAWARE-DE</option>
<option value="DC">DISTRICT OF COLUMBIA-DC</option>
<option value="FL">FLORIDA-FL</option>
<option value="GA">GEORGIA-GA</option>
<option value="GU">GUAM-GU</option>
<option value="HI">HAWAII-HI</option>
<option value="ID">IDAHO-ID</option>
<option value="IL">ILLINOIS-IL</option>
<option value="IN">INDIANA-IN</option>
<option value="IA">IOWA-IA</option>
<option value="KS">KANSAS-KS</option>
<option value="KY">KENTUCKY-KY</option>
<option value="LA">LOUISIANA-LA</option>
<option value="ME">MAINE-ME</option>
<option value="MH">MARSHALL ISLANDS-MH</option>
<option value="MD">MARYLAND-MD</option>
<option value="MA">MASSACHUSETTS-MA</option>
<option value="MX">MEXICO-MX</option>
<option value="MI">MICHIGAN-MI</option>
<option value="MN">MINNESOTA-MN</option>
<option value="MS">MISSISSIPPI-MS</option>
<option value="MO">MISSOURI-MO</option>
<option value="MT">MONTANA-MT</option>
<option value="NE">NEBRASKA-NE</option>
<option value="NV">NEVADA-NV</option>
<option value="NH">NEW HAMPSHIRE-NH</option>
<option value="NJ">NEW JERSEY-NJ</option>
<option value="NM">NEW MEXICO-NM</option>
<option value="NY">NEW YORK-NY</option>
<option value="NC">NORTH CAROLINA-NC</option>
<option value="ND">NORTH DAKOTA-ND</option>
<option value="OH">OHIO-OH</option>
<option value="OK">OKLAHOMA-OK</option>
<option value="OR">OREGON-OR</option>
<option value="PW">PALAU-PW</option>
<option value="PA">PENNSYLVANIA-PA</option>
<option value="PR">PUERTO RICO-PR</option>
<option value="RI">RHODE ISLAND-RI</option>
<option value="SC">SOUTH CAROLINA-SC</option>
<option value="SD">SOUTH DAKOTA-SD</option>
<option value="TN">TENNESSEE-TN</option>
<option value="TX">TEXAS-TX</option>
<option value="UT">UTAH-UT</option>
<option value="VT">VERMONT-VT</option>
<option value="VI">VIRGIN ISLANDS-VI</option>
<option value="VA">VIRGINIA-VA</option>
<option value="WA">WASHINGTON-WA</option>
<option value="WV">WEST VIRGINIA-WV</option>
<option value="WI">WISCONSIN-WI</option>
<option value="WY">WYOMING-WY</option>
</select>
<?php
echo Display_name('ST') ?>
</td>
<td >
<!-- Zip  -->
<input	type="text" title="Zip" name="txtzip" size="20" 
onchange="return on_change_made(this)"  
value="<?php
print $row["ZIP"]; ?>">&nbsp; 
<?php
echo Display_name('ZIP') ?>
</td>
</tr>
<tr style="border-left:3px solid #008855;border-right:3px solid #008855;">
<td >
<input	type="text" title="Site Phone Number" name="txtstorephonenumber" size="20" 
onchange="return on_change_made(this)"  
value="<?php
print $row["SITE_PHONE_NUMBER"]; ?>">&nbsp; 
<?php
echo Display_name('SITE_PHONE_NUMBER') ?>
</td>
<td >
<input	type="text" title="Site Fax Number" name="txtstorefaxnumber" size="20" 
onchange="return on_change_made(this)"  
value="<?php
print $row["SITE_FAX_NUMBER"]; ?>">&nbsp; 
<?php
echo Display_name('SITE_FAX_NUMBER') ?>
</td>
<td >
<input	type="text" title="Site manager" name="txtstoremanager" size="20" 
onchange="return on_change_made(this)"  
value="<?php
print $row["SITE_CONTACT"]; ?>">&nbsp; 
<?php
echo Display_name('SITE_CONTACT') ?>
</td>
<td >
<input	type="text" title="Site manager Number" name="txtstoremanagernumber" size="20" 
onchange="return on_change_made(this)"  
value="<?php
print $row["SITE_CONTACT_PHONE"]; ?>">&nbsp; 
<?php
echo Display_name('SITE_CONTACT_PHONE') ?>
</td>
</tr>
<tr style="border-bottom:3px solid #008855;border-left:3px solid #008855;border-right:3px solid #008855;">
<td>
<input	type="text" title="Group Contact" name="txtgroupcontact" size="20" 
onchange="return on_change_made(this)"  
value="<?php
print $row['GROUP_CONTACT']; ?>">&nbsp; 
<?php
echo Display_name('GROUP_CONTACT') ?>
</td>
<td >
<input	type="text" title="Group Contact Number" name="txtgroupnumber" size="15" 
onchange="return on_change_made(this)"  
value="<?php
print $row["GROUP_CONTACT_PHONE"]; ?>">
<?php
echo Display_name('GROUP_CONTACT_PHONE') ?>
</td>
<td >
<input	type="text" title="Group Contact Email" name="txtgroupemail" size="17" 
onchange="return on_change_made(this)"  
value="<?php
print $row["GROUP_CONTACT_EMAIL"]; ?>">
<?php
echo Display_name('GROUP_CONTACT_EMAIL') ?>
</td>
<td >
<select	size="1" 
title="Store Type" 
name="txtstoretype"
onchange="return on_change_made(this)" >
<option value="<?php
print $row["SITE_TYPE"]; ?>" SELECTED >
<?php
print $row["SITE_TYPE"]; ?>
</option>
<?php
$filename = $basedir . "site-type.txt";
$fp = fopen($filename, "r");
$contents = fread($fp, filesize($filename));
fclose($fp);
$file_lines = explode("\n", $contents);
sort($file_lines, SORT_STRING);
foreach($file_lines as $line) {
	if ($line != "") {
		echo '<option value="' . $line . '">' . $line . '</option>';
	}
}
?>

</select>&nbsp;
<?php
echo Display_name('SITE_TYPE') ?>

&nbsp;
</td>
</tr>
</table>
</div>
<a href="@" onclick="return toggleMenu('menu3','span3');"> <span id="span3" style="font-size:8px"><?php
print $span3; ?></span></a>
<span style="font-size:8px; font-weight:bold">Order Info</span><br />
<div class="menu" ID="menu3" style="display:<?php
print $menu3; ?>" >
<!-- Table 3  -->
<table style="width: 99%;
height: 53;
font-size:8pt;
border-collapse:collapse;
border-left-color:#008000;
border-left-width:1;
border-right-width:1;
border-bottom-width:1" >
<!-- Row 1  -->
<tr style="border-top:3px solid #0000FF;border-left:3px solid #0000FF;border-right:3px solid #0000FF;">
<td width="25%">
<?php // handles empty date field
if ($row["SERVICE_REQUEST_DATE"] == "" || $row["SERVICE_REQUEST_DATE"] == "0000-00-00") {
	$vpnrequestdate = "";
}
else {
	$vpnrequestdate = date('m/d/Y', strtotime(trim($row["SERVICE_REQUEST_DATE"])));
}
?>
<input  type="text" title="Request Date" name="txtrequestdate" size="12" onkeydown="javascript:this.blur()"  onchange="return on_change_made(this)" value="<?php
print $vpnrequestdate; ?>">
<!-- Hide the Calander and clear box options  -->
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<a href="javascript:show_calendar('document.myform.txtrequestdate', '');"
		style="outline:none">
		<img src="img/cal.gif" 
		width="16" 
		height="16" 
		border="0" 
		alt="Click Here to Pick up the timestamp">
		</a>
		(<a title="Clear Request date"  
		 href="@" onclick="javascript:document.getElementById('myform').txtrequestdate.value = ''; return false;">X</a>)
		<?php
} ?>
<b> 
<?php
echo Display_name('SERVICE_REQUEST_DATE') ?>
</b>
</td>
<td width="25%" >
<?php // handles empty date field
if ($row["ORDER_DATE"] == "" || $row["ORDER_DATE"] == "0000-00-00") {
	$vpnorderdate = "";
}
else {
	$vpnorderdate = date('m/d/Y', strtotime($row["ORDER_DATE"]));
}
?>
<input  type="text" title="Order date" name="txtvpnorderdate" size="12" onkeydown="javascript:this.blur()"  onchange="return on_change_made(this)" 	value="<?php
print $vpnorderdate; ?>">
<!-- Hide the Calander and clear box options  -->
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<a href="javascript:show_calendar('document.myform.txtvpnorderdate', '');"
		style="outline:none">
		<img src="img/cal.gif" width="16" height="16" border="0" 
		alt="Click Here to Pick up the timestamp"></a>
		(<a title="Clear Order date"  
		 href="@" onclick="javascript:document.getElementById('myform').txtvpnorderdate.value = ''; return false;">X</a>)
		<?php
} ?>
<b>
<?php
echo Display_name('ORDER_DATE') ?> 
</b>
</td>
<td width="25%" > 
<?php // handles empty date field
if ($row["ACTIVE_DATE"] == "" || $row["ACTIVE_DATE"] == "0000-00-00") {
	$vpnactivedate = "";
}
else {
	$vpnactivedate = date('m/d/Y', strtotime($row["ACTIVE_DATE"]));
}
?>
<input  type="text" title="Active Date" name="txtvpnactivedate" size="12" onkeydown="javascript:this.blur()"  onchange="return on_change_made(this)" value="<?php print $vpnactivedate; ?>">
<!-- Hide the Calander and clear box options  -->
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<a href="@" 
		style="outline:none"
		onclick="javascript:show_calendar('document.myform.txtvpnactivedate', '');return false;">
		<img src="img/cal.gif" width="16" height="16" border="0" 
		alt="Click Here to Pick up the timestamp"></a>&nbsp; 
	(<a title="Clear Active date"  
	 href="@" onclick="javascript:document.getElementById('myform').txtvpnactivedate.value = ''; return false;">X</a>)
		<?php
} ?>
<b>
<?php
echo Display_name('ACTIVE_DATE') ?>
</b>
</td>
<td width="25%" >
<?php // handles empty date field
if ($row["CLOSE_DATE"] == "" || $row["CLOSE_DATE"] == "0000-00-00") {
	$storeclosedate = "";
}
else {
	$storeclosedate = date('m/d/Y', strtotime($row["CLOSE_DATE"]));
}
?>
<input  type="text" title="Close date" name="txtstoreclosedate" size="12" 
onkeydown="javascript:this.blur()"  
onchange="return on_change_made(this)" value="<?php
print $storeclosedate; ?>">
<!-- Hide the Calander and clear box options  -->
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<a href="javascript:show_calendar('document.myform.txtstoreclosedate', '');"
		style="outline:none">
		<img src="img/cal.gif" width="16" height="16" border="0" 
		alt="Click Here to Pick up the timestamp"></a>
		(<a title="Clear Close date" 
		 href="@" onclick="javascript:document.getElementById('myform').txtstoreclosedate.value = ''; return false;">X</a>)
		<?php
} ?>
<b>
<?php
echo Display_name('CLOSE_DATE') ?>
</b>
</td>
</tr>
<!-- Row 2  -->
<tr style="border-bottom:3px solid #0000FF;border-left:3px solid #0000FF;border-right:3px solid #0000FF;">
<td style="border-bottom: 3px solid #0000FF" >
<input  type="text" title="Order Flag" name="txtorderflag" size="20" 
onchange="return on_change_made(this)"  
value="<?php
print $row["ORDER_FLAG"]; ?>">&nbsp; 
<?php
echo Display_name('ORDER_FLAG') ?>
</td>
<td style="border-bottom: 3px solid #0000FF" >
<input  type="text" title="Ordered By" name="txtorderby" size="20" 
onchange="return on_change_made(this)"  
value="<?php
print $row["ORDER_BY"]; ?>">&nbsp; 
<?php
echo Display_name('ORDER_BY') ?>
</td>
<td style="border-bottom: 3px solid #0000FF"  >
<input  type="text" title="Service Code" name="txtservicecode" size="20" onchange="return on_change_made(this)"  
value="<?php
print $row["SERVICE_CODE"]; ?>">&nbsp; 
<?php
echo Display_name('SERVICE_CODE') ?>
</td>
<td style="border-bottom: 3px solid #0000FF" >
<select	size="1" 
title="Service Type" 
name="txtservicetype" 
onchange="return on_change_made(this)">
<option value="<?php
print $row["SERVICE_TYPE"]; ?>" SELECTED  >
<?php
print $row["SERVICE_TYPE"]; ?>
</option>
<?php
$filename = $basedir . "service-type.txt";
$fp = fopen($filename, "r");
$contents = fread($fp, filesize($filename));
fclose($fp);
$file_lines = explode("\n", $contents);
sort($file_lines, SORT_STRING);
foreach($file_lines as $line) {
	if ($line != "") {
		echo '<option value="' . $line . '">' . $line . '</option>';
	}
}
?>
</select>&nbsp;
<?php
echo Display_name('SERVICE_TYPE') ?>
</td>
</tr>
</table>
</div>
<a href="@" onclick="return toggleMenu('menu4','span4');"> <span id="span4" style="font-size:8px"><?php
print $span4; ?></span></a>
<span style="font-size:8px;; font-weight:bold">Internet Info</span><br />
<div class="menu" ID="menu4" style="display:<?php
print $menu4; ?>" >
<!-- Table 4  -->
<table style="width: 99%;
height: 58;
font-size:8pt;
border-collapse:collapse;
border-left-color:#FFFFFF;
border-left-width:1;
border-right-width:1;
border-bottom-width:1">
<!-- Row 1  -->
<tr style="border-top:3px solid #555555;border-left:3px solid #555555;border-right:3px solid #555555;">
<td width="25%">
	<input  type="text" title="Telco Provider" name="txttelcoserviceprovider" size="20" onchange="return on_change_made(this)"  
		value="<?= $row["TELCO_PROVIDER"]; ?>">&nbsp; <?= Display_name('TELCO_PROVIDER') ?>
</td>
<td width="25%" >
	<input  type="text" title="Telco Support Number" name="txttelcosupportnumber" size="20" onchange="return on_change_made(this)" 
		value="<?= $row["TELCO_SUPPORT"]; ?>">&nbsp;<?= Display_name('TELCO_SUPPORT') ?>
</td>
<td width="25%"  >
	<input  type="text" name="txtt1circuit" size="20" onchange="return on_change_made(this)" 
		value="<?= $row["T1_CIRCUIT"]; ?>">&nbsp; <?= Display_name('T1_CIRCUIT') ?>
</td>
<td  width="25%" >
	<input  type="text" title="LEC Circuit"  name="txtleccircuit" size="25" onchange="return on_change_made(this)" 
		value="<?= $row["LEC_CIRCUIT"]; ?>">&nbsp; <?= Display_name('LEC_CIRCUIT') ?>
</td>
</tr>
<!-- Row 2  -->
<tr style="border-left:3px solid #555555;border-right:3px solid #555555;">
<td width="25%">
	<input  type="text" title="Internet Provider" name="txtbroadbandprovider" size="20" onchange="return on_change_made(this)"  
		value="<?= $row["INET_PROVIDER"]; ?>"><?= Display_name('INET_PROVIDER') ?>
</td>
<td width="25%">
	<input type="text" title="Internet Support Number" name="txtbroadbandnumber" size="20" onchange="return on_change_made(this)" 
		value="<?= $row["INET_PROVIDER_SUPPORT_NUMBER"]; ?>">&nbsp; <?= Display_name('INET_PROVIDER_SUPPORT_NUMBER') ?>
</td>
<td width="25%">
	<input  type="text" title="Internet Provider URL" name="txtbroadbandurl" size="20" onchange="return on_change_made(this)"  
		value="<?= $row["INET_PROVIDER_WEB"]; ?>">&nbsp; <?= Display_name('INET_PROVIDER_WEB') ?>
</td>
<td width="25%">
	<input  type="text" title="DLCI ID" name="txtdlciid" size="20" onchange="return on_change_made(this)" 
		value="<?= $row["DLCI_ID"]; ?>">&nbsp; <?= Display_name('DLCI_ID') ?>
</td>
</tr>
<!-- Row 3  -->
<tr style="border-bottom:3px solid #555555;border-left:3px solid #555555;border-right:3px solid #555555;">
<td width="25%" >
<!-- used to stop browsers from displaying browser saved User passwords -->

<input style="display:none" type="text"> <input style="display:none"type="password"> <!-- *************************************************************** -->
<input  type="text" title="DSL Username" name="txtdslusername" size="20" onchange="return on_change_made(this)" value="<?php print $row["DSL_USERNAME"]; ?>">&nbsp;
<?php
echo Display_name('DSL_USERNAME') ?>
</td>
<td width="25%" >
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<!-- show password in password box -->
		<div id="dslpasshidden" style="display:block">
		<input class="inputhidden" AUTOCOMPLETE="OFF" readonly type="text" 
		name="txtdslpassword_hidden"
		id="txtdslpassword_hidden"
		size="15"
		onkeyup="javascript:document.getElementById('txtdslpassword').value=this.value"
		onchange="return on_change_made(this)"
		value="<?php ($row["DSL_PASSWORD"] == '') ? print '' : print '*****';  ?>">&nbsp;
	<!-- Link to show password -->
		<a href="@"
		onclick="javascript:return toggleDSLPasswordView()">
		Show</a>
		<?php
		echo Display_name('DSL_PASSWORD') ?>
		</div>
		<!-- show password in text box -->
		<!-- Hide this div until the show link above is clicked -->
		<div id="dslpassshow" style="display:none">
		<input  autocomplete="off" type="text"
		name="txtdslpassword"
		id="txtdslpassword"
		size="15"
		onchange="return on_change_made(this)"
		value="<?php
		print $row["DSL_PASSWORD"]; ?>">&nbsp;
	<!-- Link to hide password -->
		<a href="@" onclick="javascript:return toggleDSLPasswordView()">Hide</a>
		<?php
		echo Display_name('DSL_PASSWORD') ?>
		</div>

		<?php
} ?>
</td>
<td width="25%" >

<input  type="text" title="DSL Line Number" name="txtdslnumber" size="20" onchange="return on_change_made(this)" value="<?php print $row["DSL_LINE_NUMBER"]; ?>">
<font color="#FF0000">&nbsp;
<?php
echo Display_name('DSL_LINE_NUMBER') ?>
</font>
</td>
<td width="25%" >
<input  type="text" name="txtdslcircuit" size="20" onchange="return on_change_made(this)" value="<?php print $row["DSL_CIRCUIT_NUMBER"]; ?>">&nbsp;
<?php
echo Display_name('DSL_CIRCUIT_NUMBER') ?>
</td>
</tr>
</table>
</div>
<!-- Row 4  -->
<a href="@" onclick="return toggleMenu('menu5','span5');"> <span id="span5" style="font-size:8px"><?php
print $span5; ?></span></a>
<span style="font-size:8px; font-weight:bold">Equipment</span><br />
<div class="menu" ID="menu5" style="display:<?php
print $menu5; ?>" >
<!-- Table 5  -->
<table style="width: 99%;">
<tr style="border-top:3px solid #FF0000;border-left:3px solid #FF0000;border-right:3px solid #FF0000;">
<td width="25%" >
<input  type="text"  name="txtroutermodel" size="20" onchange="return on_change_made(this)"  value="<?php print $row["ROUTER_MODEL"]; ?>">&nbsp; 
<?php
echo Display_name('ROUTER_MODEL') ?>
</td>
<td width="25%">
<input  type="text" name="txtrouterfirmwarerev" size="18" onchange="return on_change_made(this)"  value="<?php print $row["ROUTER_FIRMWARE_REV"]; ?>"> 
<?php
echo Display_name('ROUTER_FIRMWARE_REV') ?>
</td>
<td width="25%" >
<input  type="text" name="txtrouterserialnumber" size="20" onchange="return on_change_made(this)"  value="<?php print $row["ROUTER_SERIAL_NUM"]; ?>">&nbsp; 
<?php
echo Display_name('ROUTER_SERIAL_NUM') ?>
</td>
<td width="25%"  >
<input  type="text" name="txtrouterassetnumber" size="14" onchange="return on_change_made(this)"  value="<?php print $row["ROUTER_ASSET_NUM"]; ?>">
<?php
echo Display_name('ROUTER_ASSET_NUM') ?>
</td>
</tr>
<!-- Row 5  -->
<tr style="border-bottom:3px solid #FF0000;border-left:3px solid #FF0000;border-right:3px solid #FF0000;">
<td width="25%" >
<input  type="text" name="txtrouteraccessusername" size="20" onchange="return on_change_made(this)"  value="<?php print $row["ROUTER_ACCESS_USERNAME"]; ?>">&nbsp;
<?php
echo Display_name('ROUTER_ACCESS_USERNAME') ?>		
</td>
<td width="25%"  >
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<!-- show password in password box -->
		<div id="routerpasshidden" style="display:block">
		<input class="inputhidden"  autocomplete="off"readonly type="text" 
		name="txtrouteraccesspassword_hidden" 
		size="15"
		onkeyup="javascript:document.getElementById('txtrouteraccesspassword').value=this.value" 
		onchange="return on_change_made(this)"  
		value="<?php ($row["ROUTER_ACCESS_PASSWORD"] == '') ? print '' : print '*****'; ?>">&nbsp; 
	<!-- Link to show password -->
		<a href="@" 
		onclick="javascript:return toggleRouterPasswordView()">
		Show</a>
		<?php
		echo Display_name('ROUTER_ACCESS_PASSWORD') ?>
		</div>
		<!-- show password in text box -->
		<!-- Hide this div until the show link above is clicked -->
		<div id="routerpassshow" style="display:none">
		<input  type="text" 
		name="txtrouteraccesspassword" 
		id="txtrouteraccesspassword"
		size="15" 
		onchange="return on_change_made(this)"  
		value="<?php
		print $row["ROUTER_ACCESS_PASSWORD"]; ?>">&nbsp; 
		<!-- Link to hide password -->
		<a href="@" onclick="javascript:return toggleRouterPasswordView()">Hide</a>
		<?php
		echo Display_name('ROUTER_ACCESS_PASSWORD') ?>
		</div>
		<?php
} ?>
</td>
<td width="25%">
<?php // handles empty date field
if ($row["ROUTER_INSERVICE_DATE"] == "" || $row["ROUTER_INSERVICE_DATE"] == "0000-00-00") {
	$routerinservicedate = "";
}
else {
	$routerinservicedate = date('m/d/Y', strtotime($row["ROUTER_INSERVICE_DATE"]));
}
?>
<input  type="text" name="txtrouterinservicedate" size="12" onkeydown="javascript:this.blur()" onchange="return on_change_made(this)" value="<?php print $routerinservicedate; ?>">
<!-- Hide the Calander and clear box options  -->
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<a href="@" 
		style="outline:none"
		onclick="javascript:show_calendar('document.myform.txtrouterinservicedate', '');return false;">
		<img src="img/cal.gif" width="16" height="16" border="0"
		alt="Click Here to Pick up the timestamp"></a>&nbsp;
	(<a title="Clear InService date"  
	 href="@" onclick="javascript:document.getElementById('myform').txtrouterinservicedate.value =''; return false;">X</a>)
		<?php
} ?>
<b>
<?php
echo Display_name('ROUTER_INSERVICE_DATE') ?>
</b>
</td>
<td width="25%">
<input  type="text" name="txtdialupnumber" size="20" onchange="return on_change_made(this)" value="<?php
print $row["DIAL_UP_NUMBER"]; ?>">&nbsp;
<?php
echo Display_name('DIAL_UP_NUMBER') ?>
</td>
</tr>
<!-- Row 6  -->
<tr style="border-top:3px solid #0000AA;border-left:3px solid #0000AA;border-right:3px solid #0000AA;">
<td width="25%" >
<input  type="text" name="txtdslmodemmodel" size="20" onchange="return on_change_made(this)"  value="<?php print $row["CPE_MODEM_MODEL"]; ?>">&nbsp; 
<?php
echo Display_name('CPE_MODEM_MODEL') ?>
</td>
<td width="25%">
<input  type="text" name="txtcpemodemfirmwarerev" size="20" onchange="return on_change_made(this)"  value="<?php print $row["CPE_MODEM_FIRMWARE_REV"]; ?>">&nbsp; 
<?php
echo Display_name('CPE_MODEM_FIRMWARE_REV') ?>
</td>
<td width="25%">
<input  type="text" name="txtcpemodemserialnumber" size="20" onchange="return on_change_made(this)"  value="<?php print $row["CPE_MODEM_SERIAL_NUM"]; ?>">&nbsp; 
<?php
echo Display_name('CPE_MODEM_SERIAL_NUM') ?>
</td>
<td width="25%" >
<input  type="text" name="txtcpemodemassetnumber" size="20" onchange="return on_change_made(this)"  value="<?php print $row["CPE_ASSET_NUM"]; ?>">&nbsp; 
<?php
echo Display_name('CPE_ASSET_NUM') ?>
</td>
</tr>
<!-- Row 7  -->
<tr style="border-bottom:3px solid #0000AA;border-left:3px solid #0000AA;border-right:3px solid #0000AA;">
<td width="25%"  >
<input  type="text" name="txtcpemodemaccessusername" size="20" onchange="return on_change_made(this)"  value="<?php print $row["CPE_ACCESS_USERNAME"]; ?>">&nbsp; 
<?php
echo Display_name('CPE_ACCESS_USERNAME') ?>
</td>
<td width="25%">
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<!-- show password in password box -->
		<div id="cpepasshidden" style="display:block">
		<input  class="inputhidden" AUTOCOMPLETE="OFF" readonly type="text"
		name="txtcpeaccesspassword_hidden"
		size="15"
		onkeyup="javascript:document.getElementById('txtcpemodemaccesspassword').value=this.value"	
		onchange="return on_change_made(this)"
		value="<?php
		($row["CPE_ACCESS_PASSWORD"] == '') ? print '' : print '*****'; ?>">&nbsp;
	<!-- Link to show password -->
		<a href="@"
		onclick="javascript:return toggleCPEPasswordView()">
		Show</a>
		<?php
		echo Display_name('CPE_ACCESS_PASSWORD') ?>
		</div>
		<!-- show password in text box -->
		<!-- Hide this div until the show link above is clicked -->
		<div id="cpepassshow" style="display:none">
		<input  type="text"
		name="txtcpemodemaccesspassword"
		id="txtcpemodemaccesspassword"
		size="15"
		onchange="return on_change_made(this)"
		value="<?php
		print $row["CPE_ACCESS_PASSWORD"]; ?>">&nbsp;
	<!-- Link to hide password -->
		<a href="@" onclick="javascript:return toggleCPEPasswordView()">Hide</a>
		<?php
		echo Display_name('CPE_ACCESS_PASSWORD') ?>
		</div>

		<?php
} ?>
</td>
<td width="25%">
<?php // handles empty date field
if ($row["CPE_INSERVICE_DATE"] == "" || $row["CPE_INSERVICE_DATE"] == "0000-00-00") {
	$cpeinservicedate = "";
}
else {
	$cpeinservicedate = date('m/d/Y', strtotime($row["CPE_INSERVICE_DATE"]));
}
?>
<input  type="text" name="txtcpeinservicedate" size="12" onkeydown="javascript:this.blur()" onchange="return on_change_made(this)" value="<?php print $cpeinservicedate; ?>">
<!-- Hide the Calander and clear box options  -->
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<a href="@" 
		style="outline:none"
		onclick="javascript:show_calendar('document.myform.txtcpeinservicedate', '');return false;">
		<img    src="img/cal.gif" 
		width="16" 
		height="16" 
		border="0"
		alt="Click Here to Pick up the timestamp"> </a>&nbsp;
	(<a     title="Clear InService date"
	 href="@" 
	 onclick="javascript:document.getElementById('myform').txtcpeinservicedate.value = ''; return false;">X</a>)
		<?php
} ?>
<b>
<?php
echo Display_name('CPE_INSERVICE_DATE') ?>
</b>
</td>
<td  width="25%">
&nbsp;
</td>
</tr>
</table>
</div>
<!-- Table 5  -->
<table style="border-color: #0000FF;
border-style: solid;
border-width: 1px;
width: 99%;
font-size:8pt;" >

<!-- Row 1  -->
<tr>
<td style="text-align: center; width:25%">
<?php
echo Display_name('NOTES_1') ?>
<!-- Hide Add Time Stamp Option -->
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	(<a 	title="Set Time Stamp"  
	 href="@" onclick="javascript:add_note(); return false;">
	 Add Time Stamp</a>)
		<?php
} ?>
</td>
<td style="text-align: center; width:25%">
<!-- Hide admin notes title if not high level -->
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<?php
		echo Display_name('NOTES_2') ?>
		(<a	title="Set Time Stamp"  
		 href="@" onclick="javascript:set_time_stamp(); return false;">
		 Add Time Stamp</a>)
		<?php
} ?>
&nbsp;
</td>
<td style="text-align: center; width:25%">
<?php
if (trim($alert_message) != "") {
	echo '<marquee id="alertit" ';
	echo 'style="font-size:14; font-weight:bold; color:#FF0000; background-color:cccccc" ';
	echo 'scrolldelay="100" scrollamount="5"  behavior="scroll" loop=-1 ';
	echo "onmouseover=\"javascript:this.stop()\" ";
	echo "onmouseout=\"javascript:this.start()\">" . $alert_message . "</marquee>";
}
?>		
</td>
</tr>
<tr>
<td style="text-align: center; color: #FF0000">
<!-- NOTES 1 -->
<textarea 	rows="8" 
name="txtnotes" 
cols="80" 
onchange="return on_change_made(this)"><?php
print $row["NOTES_1"]; ?>
</textarea>
</td>
<td style="text-align: center">
<!-- Hide admin notes if not high level -->
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<!-- NOTES 2 -->
		<textarea	rows="8" 
		name="txtnotes2" 
		cols="80"
		onchange="return on_change_made(this)" ><?php
		print $row["NOTES_2"]; ?>
		</textarea> 

		<?php
} ?>
&nbsp;
</td>
<td style="text-align: center">
<?php
if ($_SESSION['accesslevel'] >= 7) { ?>
	<input 	class="button" 
		type="submit" 
		value="Save" 
		name="save" 
		id="save"
		style="color: #00FF00; 
		font-weight: bold; 
	background-color: #000000"><br />
		<input 	class="button"  
		type="button" 
		value="Add New Record"
		id="addnew"
		name="addnew" 
		style="color: #008000"
		onclick="add_new()"><br />
		<br />
		<input 	class="button"  
		type="button" 
		value="Delete Record" 
		id="deleterecord" 
		name="deleterecord" 
		style="color: #FF0000"
		onclick="return delete_record()"
		tabindex="0"><br />
		<?php
		echo '<a href="@" ';
	echo 'onclick="window.open(\'site-copy.php?site=';
	echo $site_id . '\',\'\',\'';
	$options = 'width=525,';
	$options.= 'height=700,';
	$options.= 'resizable=yes,';
	$options.= 'scrollbars=yes,';
	$options.= 'status=yes';
	echo $options . '\'); return false">Create Duplicate record</a><br />';
}
add_plugin('ops_bottom_right', $site_id); ?>&nbsp;
</td>
</tr>
<tr>
<td style="text-align: center">
&nbsp;
</td>
<td><?php
echo Display_name('LAST_CHANGE_BY') ?> :
<?php
print $row["LAST_CHANGE_BY"]; ?>
</td>
<td><?php
echo Display_name('LAST_CHANGE_DATE') ?>
<?php
echo date('m/d/Y h:i:s A T', strtotime($row["LAST_CHANGE_DATE"])); ?>
<input 	type="hidden" name="pageLoadedDate" id="pageLoadedDate" value="<?php
echo trim($row["LAST_CHANGE_DATE"]); ?>">
</td>
</tr>
</table>

</form>
<?php
echo '<img
src="img/valid-html401-blue.png"
alt="Valid HTML 4.01 Transitional" height="23" width="66" title="Tested as Valid HTML 4.01 Transitional">
<img style="border:0;width:66px;height:23px"
src="img/vcss-blue" title="Tested Valid CSS"
alt="Valid CSS!" >
<img style="border:0;width:80px;height:15px"
src="img/php5-power-micro.png"
alt="php powered" >';
add_plugin('ops_footer', $site_id);
include ('netzfooter.php');
?>
<!-- starts the timer to check if record is being edited -->
<script>
check_edit('<?php
		echo $site_id ?>')       
</script>
</body>
<?php
echo "</html>";
/**********************************************************************************************
 *    Ending Brace fron ($num == 1 || $num  == 0)
 ***********************************************************************************************/
}
else
// We found more than one record from query
{
	$monitor_enable = isset($row["MONITOR_ENABLE"]) ? $row["MONITOR_ENABLE"] : '';
	// search results Table
	$table_head="";
	echo "<body onload=\"window.focus();\"> ";
	// Displays row count at top od table 
	/*
	   $COUNTERSTR = query_num_rows($rows);
	   echo "<center><h4 id='count'>" . $COUNTERSTR . "</h4></center>";
	 */

	// Side Menu
/*
	echo '<script  src="menulz.js"> </script>';
	// Check access rights and load menu items for that level
	if ($_SESSION['accesslevel'] >= 9) {
		echo '<script  src="menu-data-rwa.js"> </script>';
	}
	elseif ($_SESSION['accesslevel'] >= 4) {
		echo '<script  src="menu-data-rw.js"> </script>';
	}
	else {
		echo '<script  src="menu-data-ro.js"> </script>';
	}
*/
	$table_head.= "<table id=\"site_list\" class=\"list\">\n<Thead>\n<tr>\n";
	// Store Table link
	$table_head.= "<td  align=\"center\">\n";
	$table_head.= "<a class=\"" . $STORECLS . "\" href=\"ops.php?Sort=store&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
	$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
	$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "&amp;Ping=" . $PING . "\">SiteID</a></td>\n";
	// City Table link
	$table_head.= "<td  align=\"center\">\n<a class=\"" . $CITYCLS . "\"";
	$table_head.= " href=\"ops.php?Sort=city&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
	$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
	$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "&amp;Ping=" . $PING . "\">CITY</a>\n</td>\n";
	// Group Table link
	$table_head.= "<td align=\"center\">\n<a class=\"" . $GROUPCLS . "\"";
	$table_head.= " href=\"ops.php?Sort=group&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
	$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
	$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "&amp;Ping=" . $PING . "\">Group</a>\n</td>\n";
	// Store Type Table link
	$table_head.= "<td  align=\"center\">\n<a class=\"" . $STORETYPECLS . "\"";
	$table_head.= " href=\"ops.php?Sort=storetype&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
	$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
	$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "&amp;Ping=" . $PING . "\">Store Type</a>\n</td>\n";
	// Service Type Table link
	$table_head.= "<td  align=\"center\">\n<a class=\"" . $SERVICECLS . "\"";
	$table_head.= " href=\"ops.php?Sort=service&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
	$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
	$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "&amp;Ping=" . $PING . "\">Service Type</a>\n</td>\n";
	// IP ADDRESS Table link
	$table_head.= "<td  align=\"center\">\n<a class=\"" . $IPCLS . "\"";
	$table_head.= " href=\"ops.php?Sort=ip&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
	$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
	$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "&amp;Ping=" . $PING . "\">IP ADDRESS</a>\n</td>\n";
	// Active Date Table link
	$table_head.= "<td align=\"center\">\n<a class=\"" . $ACTIVECLS . "\"";
	$table_head.= " href=\"ops.php?Sort=active&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
	$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
	$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "&amp;Ping=" . $PING . "\">Active Date</a>\n</td>\n";
	// ModelTable link
	$table_head.= "<td align=\"center\">\n<a class=\"" . $modCLS . "\"";
	$table_head.= " href=\"ops.php?Sort=model&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
	$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
	$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "&amp;Ping=" . $PING . "\">Model</a>\n</td>\n";
	// Monitor Enable Table link
	$table_head.= "<td align=\"center\">\n<a class=\"" . $MONCLS . "\"";
	$table_head.= " href=\"ops.php?Sort=monitored&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
	$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
	$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "&amp;Ping=" . $PING . "\">Monitored</a>\n</td>\n";
	// Advanced Feild  Table link - If selected
	if ($ADVSELECTEDFIELD != "") {
		$table_head.= "<td align=\"center\">\n<a class=\"" . $SELECTEREGIONLS . "\"";
		$table_head.= " href=\"ops.php?Sort=selected&amp;site=" . urlencode(isset($STRSTORENUM) ? $STRSTORENUM : '');
		$table_head.= "&amp;group=" . $STRGROUP . "&amp;advsearch=" . $ADVSEARCHSTR;
		$table_head.= "&amp;selectedfield=" . $ADVSELECTEDFIELD . "\" >" . $ADVSELECTEDFIELD . "</a>\n</td>\n";
	}
	$table_head.= "</tr></thead><tbody>";
	// Build the Rows of the search results Table
	echo $table_head;
	$tdclass = "tdlight";
	foreach($rows as $row) {
		// Ping the LAN IP if selected
		if ($PING == "ON") {
			$IPALIVE = exec("sudo " . $basedir . "ping-test-web.php " . $row['LAN_IP']);
		}
		else {
			$IPALIVE = "";
		}
		if ($tdclass == "tdlight") {
			$tdclass = "tddark";
		}
		else {
			$tdclass = "tdlight";
		}
		$tdtag = "<td class='" . $tdclass . "'>";
		$TEMPSTR = "<tr>\n" . $tdtag . "<a href=\"ops.php?site=" . urlencode($row['SITE_ID']) . "\">";
		$TEMPSTR.= $row['SITE_ID'] . "</a> </td>\n";
		$TEMPSTR.= $tdtag . $row['CITY'] . "&nbsp;</td>\n";
		$TEMPSTR.= $tdtag . $row["GROUP_NAME"] . "&nbsp;</td>\n";
		$TEMPSTR.= $tdtag . $row["SITE_TYPE"] . "&nbsp;</td>\n";
		$TEMPSTR.= $tdtag . $row["SERVICE_TYPE"] . "&nbsp;</td>\n";
		if ($IPALIVE == "-1") {
			$TEMPSTR.= $tdtag . "<font color='red'>" . $row["LAN_IP"] . "</font>&nbsp;</td>\n";
		}
		else {
			if ($IPALIVE != "") {
				$TEMPSTR.= $tdtag . "<font color='#339900'>" . $row["LAN_IP"] . "</font>&nbsp;</td>\n";
			}
			else {
				$TEMPSTR.= $tdtag . $row['LAN_IP'] . "&nbsp;</td>\n";
			}
		}
		// handles empty date field
		if ($row["ACTIVE_DATE"] == "" || $row["ACTIVE_DATE"] == null || $row["ACTIVE_DATE"] == "0000-00-00") {
			$vpnactivedate = "";
		}
		else {
			$vpnactivedate = date('m/d/Y', strtotime($row["ACTIVE_DATE"]));
		}
		$TEMPSTR.= $tdtag . $vpnactivedate . "&nbsp;</td>\n";
		$TEMPSTR.= $tdtag . $row["ROUTER_MODEL"] . "&nbsp;</td>\n";
		if ($row["MONITOR_ENABLE"] == 1) {
			$TEMPSTR.= $tdtag . "<font color='green'>Yes</font></td>\n";
		}
		else {
			$TEMPSTR.= $tdtag . "<font color='red'>No</font></td>\n";
		}
		if ($ADVSELECTEDFIELD != "") {
			$TEMPSTR.= $tdtag . $row[$ADVSELECTEDFIELD] . "</td>\n";
		}
		$TEMPSTR.= "</tr>\n";
		echo $TEMPSTR;
		// Flush the output buffer
		if (ob_get_length()) ob_flush();
		$TEMPSTR = "";
		echo $TEMPSTR;
		flush();
	} // Closing foreach($rows as $row)
	echo "</TBODY></table>\n";
	echo "<form name=\"mysearch\" action=\"\">";
	echo "<input type=\"hidden\" name=\"txtselectedsearch2\" value=\"" . $ADVSEARCHSTR . "\" >";
	echo "</form>";
	echo "<script type=\"text/javascript\"> \n";
	echo "addTableRolloverEffect('site_list','tableRollOverEffect1','tableRowClickEffect1');";
	echo "</script>\n";
	echo "</body>";
	include ('netzfooter.php');

	echo "</html>";
} // Closing else  //We found more than one record from queryi

?>
