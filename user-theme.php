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
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL);

include_once('auth.php');
include_once("site-monitor.conf.php");
include_once('write_access_log.php');

$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);
$results = mysqli_query($conn,"select * from SITEDATA");
if (!$results) {
   die('Query failed: ' . mysqli_error());
}


        $sql="SELECT * FROM USERS WHERE USERNAME = '".addslashes($_GET['user'])."'";
        $results = mysqli_query($conn,$sql);
        $rows = mysqli_fetch_assoc($results);
// Get just the filename from the style path and change to upper case
$path_parts = pathinfo($_SESSION['style']);
$stylename=strtoupper(preg_replace("/\.".$path_parts['extension'] ."/", '', $path_parts['basename']));
?>
<html>
<head>

<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css" id="css">
</head>
<body>
<form method="POST" action="write-user-theme.php" id="styleform">
<?php
	$styledir=$basedir."style";
	$sav= glob($styledir. "/*.css");
	$stylesavail= array();
	foreach($sav as $key=>$style_name){
			$stylesavail[$key] = str_ireplace(".css","",basename($style_name));
	}
	//$stylesavail= explode(",",$rtn);
	//echo '<select size="1" name="txttheme" id="txttheme" onchange="javascript:document.getElementById(\'styleform\').submit();">';
	echo '<select size="1" name="txttheme" id="txttheme" >';
	echo "<option value='".$style."'>".$stylename."</option>";
	foreach ($stylesavail as $sty)
	{
		if (trim($sty) != "") {
			echo "<option value='style/". trim($sty).".css'>".strtoupper($sty)."</option>";
		}
	}
	echo "</select> <input class='button' type='button' value='save' onclick='javascript:document.getElementById(\"styleform\").submit();'></form>";
	//echo $stylesavail[1];
?>

</body></html>


