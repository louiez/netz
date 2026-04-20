<?php
/*###############################################################
        NETz Network Management system                          #
        http://www.proedgenetworks.com/netz                     #
                                                                #
                                                                #
        Copyright (C) 2005-2006 Louie Zarrella                  #
        louiez@proedgenetworks.com                              #
                                                                #
        Released under the GNU General Public License           #
        Copy of License available at :                          #
        http://www.gnu.org/copyleft/gpl.html                    #
###############################################################*/
//include 'logon.php';
include("site-monitor.conf.php");

?>
<html>

<head>
<META HTTP-EQUIV="REFRESH"  CONTENT="300">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<title> </title>
</head>

<body>
<?php
include('site-monitor.conf.php');
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
mysql_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD);
@mysql_select_db(NETZ_DATABASE) or die( "Unable to select database");

// get a list of sites in the DOWNSITES table
//$query="SELECT ".$site_id_field." FROM `$site_down_tb` GROUP BY ".$site_id_field." ORDER BY ".$site_id_field;

$query="SELECT * FROM ".SITE_INFO_TABLE. " WHERE MONITOR_STATUS > 0 AND MONITOR_ENABLE = 1";
$result=mysql_query($query);
$tdown= mysql_numrows($result);

$query="SELECT * FROM ".SITE_INFO_TABLE. " WHERE MONITOR_ENABLE = 1";

$result=mysql_query($query);
$total= mysql_numrows($result);
$percent=round((($total-$tdown)/ $total ) *100, 1);
if ($percent < 95){$percent=tagred($percent);}else {$percent=taggreen($percent);}
mysql_close();
//echo "<div id='startmess' style='font-size:14pt;font-weight:bold'>".$tdown." sites Down <br>".$total." Monitored<br>". $percent . "% Available <br>". date('D  g:i:s a T')."</div>";
echo "<div id='startmess' style='font-size:14pt;font-weight:bold'>".$total." Monitored <br>".$tdown." sites Down <br>". $percent . "% Available <br>". date('D  g:i:s a T')."</div>";

?>
<a href="main.php" style="font-size:8pt">NETz home</a>
</body>

</html>
