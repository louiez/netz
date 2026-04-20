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
include_once("auth.php");
include_once("site-monitor.conf.php");
echo'<HTML><HEAD><TITLE>System</TITLE>';
//      +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//      |       User Access code        |
// =====================================================================================================//
//$acl=$_SESSION['accesstype'];                                                                         //
//if ($acl != "rwa" && $acl != "rw"){                                                                   //
$acl=$_SESSION['accesslevel'];
if ($acl <= 1){
        echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';       //
        echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';                        //
        }                                                                                               //
// =====================================================================================================//
?>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8">
        <TITLE>NETz Ops</TITLE>
        <meta http-equiv="Content-Language" content="en-us">
        <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
        <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
        <?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
        <link rel="stylesheet" href="<?php echo $style  ?>" type="text/css" id="css">
       <link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon" >
        <link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon" >
<?php echo'</HEAD>'; ?>

<script type="text/javascript">
function PopupPic(sPicURL) {
                window.open( "popup.htm?"+sPicURL, "","resizable=1,HEIGHT=400,WIDTH=600");
        return false;
   }
</script>


</HEAD>

<?php
echo '<BODY><h1 style="text-align:center;">Servers</h1>';
echo '<TABLE WIDTH=100% BORDER=2 BORDERCOLOR="#000080" CELLPADDING=4 CELLSPACING=4 FRAME=HSIDES RULES=NONE" >';
echo '<TR VALIGN=TOP>';
include('server-info.html');

echo '<TD BGCOLOR="#000000">';
?>
<!-- rem
        <a href="@" onclick="return PopupPic('chart-images/cpus.png')" ><IMG src="chart-images/cpus-small.png" alt="CPU 0 nad 1 current" ></a>
        <BR>
        <a href="@" onclick="return PopupPic('chart-images/cpus2.png')" ><IMG src="chart-images/cpus2-small.png" alt="CPU 2 and 3 current"></a>
        <BR>
        <a href="@" onclick="return PopupPic('chart-images/cpus-week.png')" ><IMG src="chart-images/cpus-week-small.png" alt="CPU weekr"></a>
        <BR>
        <a href="@" onclick="return PopupPic('chart-images/load.png')" ><IMG src="chart-images/load-small.png" alt="Load Day"></a>
        <BR>
        <a href="@" onclick="return PopupPic('chart-images/load-week.png')" ><IMG src="chart-images/load-week-small.png" alt="Load Week" ></a>
-->
<?php
echo '</td></tr>';
echo '</table>';

  echo '</BODY></HTML>';

?>
