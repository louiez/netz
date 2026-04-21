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

$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>

<?php
//	+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//	|     User Access code		|
// ============================================================================================++++++++=//
//$acl=$_SESSION['accesstype'];										//
//if ($acl != "rwa"){											//
if ($_SESSION['accesslevel'] <= 8){	
	echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';	//
	echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';			//
	}												//
// =============================================================================================++++++++//
?>

<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">

<title>User Admin</title>

<script type=text/javascript>
function deleteuser(un)
{
	ans=prompt("are you sure?\n Type YES in uppercase to confirm");
	if (ans == "YES")
	window.location="userdel.php?user=" + escape(un); 
}
if (screen.width < 400){window.location.href="useradmin-mini.php";}
</script>

</head>
<body>
<?php //echo $body; ?>
<script language="JavaScript1.2" type="text/javascript"  src="menulz.js"> </script>
<script type="text/javascript"  src="table-highlight.js"> </script>
<?php
// if ($acl == "rwa"){
if ($_SESSION['accesslevel'] >= 9){
        echo '<script language="JavaScript1.2" type="text/javascript" src="menu-data-rwa.js"> </script>';
 }elseif($_SESSION['accesslevel'] >= 4){
        echo '<script language="JavaScript1.2" type="text/javascript"  src="menu-data-rw.js"> </script>';
}else{
        echo '<script language="JavaScript1.2" type="text/javascript"  src="menu-data-ro.js"> </script>';
}
?>
	
	<h4 style="text-align:center">User Administration&nbsp;&nbsp;</h4>
<form action="" onsubmit="javascript:window.location='useradmin.php?search='+document.getElementById('txtsearch').value ; return false"  >
<!-- Links  -->
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="logoff.php">Logoff</a>&nbsp;&nbsp;
	<a href="main.php">NETz Home</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="useradd.php">Add User</a>
<!-- Search Box  -->
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="text" id="txtsearch" >&nbsp;&nbsp;
	<input class="button" type="button" value="Search" onclick="javascript:window.location='useradmin.php?search='+document.getElementById('txtsearch').value" TABINDEX="0">
</form>	
<hr>
<?php
//----------------------//
// get the sort field   //
//==============================================//
$sort=$_GET['sort'];                            //
switch ($sort) {                                //
        case "username":                        //
                $sort = "USERNAME";             //
                break;                          //
        case "fullname":                        //
                $sort = "FULL_NAME";            //
                break;                          //
        case "email":                           //
                $sort = "EMAIL";       		//
                break;                          //
        case "title":                           //
                $sort = "TITLE";                //
                break;                          //
        case "access":                          //
                $sort = "ACCESSLEVEL";           //
                break;                          //
        case "datecreated":                     //
                $sort = "CREATE_DATE";          //
                break;                          //
        case "lastaccess":                      //
                $sort = "LAST_LOGIN_DATE";      //
                break;                          //
	default:				//
		$sort = "USERNAME";             //
		break;				//
}                                               //
//==============================================//

//--------------------------------------//
// Get the sort order direction         //
//======================================================//
$sortdirection=$_GET['sd'];                             //
if ($_GET['paging']!=1){
switch ($sortdirection) {                               //
        case "ASC":                                     //
                $sd = "DESC";                           //
                $arimg = "down_pointer.png";            //
                break;                                  //
        case "DESC":                                    //
                $sd = "ASC";                            //
                $arimg = "up_pointer.png";              //
                break;                                  //
        default:                                        //
                $sd = "ASC";                            //
                $arimg = "up_pointer.png";              //
                $sortdirection="ASC";                   //
}                       
}else{
	$sd = $sortdirection;
	$arimg = "up_pointer.png"; 
}                                //
//======================================================//

if ($_GET['sort'] == "username" || $_GET['sort'] == ""){$userimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";}
else {$userimgtag = "";}

if ($_GET['sort'] == "fullname"){$fullimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";}
else {$fullimgtag = "";}

if ($_GET['sort'] == "email"){$emailimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";}
else {$emailimgtag = "";}

if ($_GET['sort'] == "title"){$titleimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";}
else {$titleimgtag = "";}

if ($_GET['sort'] == "access"){$accessimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";}
else {$datecreatedtypeimgtag = "";}

if ($_GET['sort'] == "datecreated"){$datecreatedtypeimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";}
else {$datecreatedtypeimgtag = "";}

if ($_GET['sort'] == "lastaccess"){$lastaccessimgtag = "&nbsp;&nbsp;<img style=\"border:none\" src=\"img/".$arimg."\" alt=\"arrow\">";}
else {$lastaccessimgtag = "";}





?>

<form method="POST" action="">
<table  width="100%" onMouseOut="javascript:highlightTableRowVersionA(0);">
<?php
//######################################################################################//#
//---------------------------------     Search    --------------------------------------//#
//######################################################################################//#
$search_str = addslashes($_GET['search']);
if ($_GET['search'] != ""){								//#
	$query ="SELECT count(*) as tmp FROM USERS ";					//#
	$query =$query ."WHERE USERNAME like '%".$search_str."%'";			//#
	$query =$query ."OR FULL_NAME like '%".$search_str."%'";			//#
        $query =$query ."OR EMAIL like '%".$search_str."%'";			//#
	$query =$query ." ORDER BY ".$sort. " " .$sd;					//#
											//#
        $sql ="SELECT * FROM USERS ";							//#
        $sql =$sql ."WHERE USERNAME like '%".$search_str."%'";			//#
        $sql =$sql ."OR FULL_NAME like '%".$search_str."%'";			//#
        $sql =$sql ."OR EMAIL like '%".$search_str."%'";				//#
        $sql =$sql ." ORDER BY ".$sort. " " .$sd;					//#
}else{											//#
	$query ="SELECT count(*) as tmp FROM USERS ORDER BY ".$sort. " " .$sd;		//#
	$sql = "SELECT * FROM USERS ORDER BY ".$sort. " " .$sd;				//#
}											//#
//######################################################################################//#
//-------------------------------     Pageination    -----------------------------------//#
//######################################################################################//#
$results = mysqli_query($conn,$query);								//#
$fetch= mysqli_fetch_assoc($results);							//#
$totalrecs=$fetch['tmp'];								//#
$page=$_GET['page'];									//#
$maxrecstodisplay="30";									//#
$totalpages=ceil($totalrecs/$maxrecstodisplay);						//#
$limit=$page*$maxrecstodisplay;								//#
//echo '   $totalrecs='.$totalrecs.'   $page '.$page.'   $totalpages '.$totalpages;	//#
//echo "<tr onMouseOver=\"javascript:highlightTableRowVersionA(this, '#8888FF');\">";	//#
echo "<tr><td colspan='3' align=\"center\" ></td>";					//##############################################//#
echo "<td colspan='2' style=\"text-align:center\">";											//#
if ($page > 0){																//#
	echo "<a href=\"useradmin.php?sd=".$sd."&amp;sort=".$_GET['sort']."&amp;page=".($page-1)."&amp;paging=1\">&lt;&lt; &nbsp;&nbsp;</a>";	//#
}//else{ echo "&lt;&lt;&nbsp;&nbsp;";}													//#
for ($i=0; $i < $totalpages; $i++){													//#
	if ($i != $page){														//#
		echo "<a href=\"useradmin.php?sd=".$sd."&amp;sort=".$_GET['sort']."&amp;page=".$i."&amp;paging=1\"> &nbsp;&nbsp;".($i+1)."</a>";	//#
	}else{echo "&nbsp;&nbsp;". ($i+1);}												//#
}																	//#
if ($page < ($totalpages-1)){														//#
	echo "<a href=\"useradmin.php?sd=".$sd."&amp;sort=".$_GET['sort']."&amp;page=".($page+1)."&amp;paging=1\"> &nbsp;&nbsp;&gt;&gt;</a>";	//#
}//else{ echo "&nbsp;&nbsp;&gt;&gt;";}													//#
echo "</td><td colspan='3' align=\"center\" ></td></tr>";										//#
//######################################################################################################################################//#
	//|-----------------------------||
	//|	Table Headings		||
	//|-----------------------------||
echo "<tr><td align=\"center\" ><b><a href=\"useradmin.php?sd=".$sd."&amp;sort=username&amp;page=".$page."\">Username".$userimgtag."</a></b></td>";
echo "<td align=\"center\" ><b><a href=\"useradmin.php?sd=".$sd."&amp;sort=fullname&amp;page=".$page."\">Full Name".$fullimgtag."</a</b></td>";
echo "<td align=\"center\" ><b><a href=\"useradmin.php?sd=".$sd."&amp;sort=email&amp;page=".$page."\">E-Mail".$emailimgtag."</a</b></td>";

echo "<td align=\"center\" ><b><a href=\"useradmin.php?sd=".$sd."&amp;sort=access&amp;page=".$page."\">Access Level".$accessimgtag."</a</b></td>";
echo "<td align=\"center\" ><b><a href=\"useradmin.php?sd=".$sd."&amp;sort=datecreated&amp;page=".$page."\">Date Created".$datecreatedtypeimgtag."</a</b></td>";
echo "<td align=\"center\" ><b><a href=\"useradmin.php?sd=".$sd."&amp;sort=lastaccess&amp;page=".$page."\">Last Access".$lastaccessimgtag."</a</b></td>";
echo "<td align=\"center\" ><b>Edit</b></td>";
echo "<td align=\"center\" ><b>Delete <br>User</b></td></tr>";
	
	$sql= $sql." LIMIT ".$limit.", ".$maxrecstodisplay;
	$results = mysqli_query($conn,$sql);
	while ($rows = mysqli_fetch_assoc($results))
 	{
		// Toggle Row back color
		if ($tdclass == "tdlight"){$tdclass = "tddark";}
		else{ $tdclass = "tdlight";}		
		$tdtag="<td class='" . $tdclass. "'>";

		// Init the row highlight code
		echo "<tr  onMouseOver=\"javascript:highlightTableRowVersionA(this, '#8888FF');\">";
		// Username
		echo $tdtag. $rows['USERNAME'] .'</td>';
		// Full Name
		echo $tdtag .$rows['FULL_NAME'] . '&nbsp;</td>';
		// Email address
		echo $tdtag. $rows['EMAIL'] .'&nbsp;</td>';
		// Access Type
		echo $tdtag .$rows['ACCESSLEVEL'] . '&nbsp;</td>';

		// Create date
        	if ($rows['CREATE_DATE'] == "" || $rows['CREATE_DATE'] == "0000-00-00 00:00:00") { $createdate="&nbsp;"; }
        	else { $createdate=date('m/d/Y h:i a',strtotime($rows['CREATE_DATE'])); }
		echo $tdtag .$createdate . '</td>';
		// Last logon Date
		if ($rows['LAST_LOGIN_DATE'] == "" || $rows['LAST_LOGIN_DATE'] == "0000-00-00 00:00:00") { 
			$lastlogondate="&nbsp;"; 
		}
        	else { $lastlogondate=date('m/d/Y h:i a',strtotime($rows['LAST_LOGIN_DATE'])); }
		echo $tdtag .$lastlogondate . '</td>';
		// Edit Button
		echo $tdtag.'<input class="button" type="button" value="Edit" ';
		echo 'onclick=\'javascript:window.location="usermod.php?user='.urlencode($rows['USERNAME']) .'"\'></td>';
		// Delete Button
		echo $tdtag.'<input class="button" type="button" value="Delete" ';
		echo 'onclick="deleteuser(\'' . addslashes($rows['USERNAME']) . '\')"></td>';

		echo '</tr>';

	}

?>

</table>
</form>
<?php include('netzfooter.php'); ?>
</body>
</html>
