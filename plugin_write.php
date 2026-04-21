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

//include("site-monitor.conf.php");
include 'logon.php';
require_once( 'class.ConfigMagik.php');

$section=$_GET['section'];
$level=$_POST['level'];
$enabled=$_GET['enabled'];
// get the current level from a GET (URL)
//$currect_level=$_GET['currentlevel'];

$config = new ConfigMagik($basedir."plugins.ini", true, true );
if ($enabled == "false"){
	$config->removeSection($section);
	echo "<script type=\"text/javascript\">window.location=\"netz-config.php\"</script>";
}elseif($enabled == "true"){
	if ($level != ""){
		$config->set( "level", $level, $section);
		echo "<script type=\"text/javascript\">window.location=\"netz-config.php\"</script>";
	}else{
		echo "<html><body>";
		echo "<form method=\"post\" action=\"plugin_write.php?section=".$section."&enabled=".$enabled."\" >";
		echo "Select user level for this Plugin<br>";
		echo "<select size=\"1\" name=\"level\">";
	//	echo "<option value=\"".$currect_level."\" SELECTED  >".$currect_level."</option>";        
		echo "<option value=\"0\">Everybody</option>";
		echo "<option value=\"1\" >read only (1)</option>";
		echo "<option value=\"2\">read only ops (2)</option>";
		echo "<option value=\"3\">read only unused (3)</option>";
		echo "<option value=\"4\">read/write order (4)</option>";
		echo "<option value=\"5\">read/write unused (5)</option>";
		echo "<option value=\"6\">read/write ops (6)</option>";
		echo "<option value=\"7\">read/write ops (7)</option>";
		echo "<option value=\"8\">read/write unused (8)</option>";
		echo "<option value=\"9\">Admin (9)</option>";
		echo "<option value=\"10\">Admin Full (10)</option>";
		echo "</select><br><input type=\"submit\"></form>";
	}
}

?>

