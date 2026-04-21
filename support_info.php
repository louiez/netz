<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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
//include('logon.php');
include_once("site-monitor.conf.php");
include('write_access_log.php');
$support=$_GET['support'];
$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
$query="SELECT * FROM ALERTEMAILS WHERE LOCATION = '".$support."'";
$result=mysqli_query($conn,$query);
$row = mysqli_fetch_assoc($result)
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<title><?php echo $support; ?></title>
<script type="text/javascript" src="size_window.js"></script>
<style type="text/css">
.smallhead {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 10px;
        color: #016A9D;
        font-weight: bold;
        line-height: 20px;
}
.text {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 10px;
        color: #333333;
        font-weight: normal;
}

</style>
<script type="text/javascript">
function closeWindow()
{
	window.close();
}
</script>
</head><body>
<div id="show_div">
<table border="0" cellspacing="0" cellpadding="4" width="96%">
<tr><td align="right" class="text"><a href="javascript: closeWindow();">Close</a></td></tr>
<tr><td class="text"><p class="text" align="justify">
<?php
echo "<b>Support Center</b> ".$row['LOCATION']."<br>";
echo "<b>E-mail address</b> ".$row['EMAIL']."<br>";
echo "<b>Phone Number</b> ".$row['PHONE_NUMBER']."<br>";
echo "<b>Fax Number</b> ".$row['FAX_NUMBER']."<br>";
?>
</p></td></tr>

</table>
</div>
<script type="text/javascript">
	sizeToFit("show_div");
</script>

</body></html>
