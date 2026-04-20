<?php
include_once("site-monitor.conf.php");
include('write_access_log.php');

$conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//@mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");
$site=$_GET["site"];

// create temp database foofoo
//$query="create table foofoo like SITEDATA";
//mysqli_query($conn,$query);
// Copy to temp Table
$query="insert into foofoo select * from SITEDATA where SITE_ID = '".$site."'";
mysqli_query($conn,$query);

// rename to [SITE]-copy
$query="UPDATE foofoo SET SITE_ID = '".$site."-COPY'  WHERE  SITE_ID = '".$site."' ";
mysqli_query($conn,$query);

// Now copy to SITEDATA table
$query="insert into SITEDATA select * from foofoo where SITE_ID = '".$site."-COPY' ";
mysqli_query($conn,$query);
// delete the record from temp table
$query="DELETE  from foofoo where SITE_ID = '".$site."-COPY' ";
mysqli_query($conn,$query);

// Create record in MONITORINFO
$query="INSERT INTO MONITORINFO (SITE_ID) VALUES('".$site."-COPY')";
mysqli_query($conn,$query);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8">

<title>Closing</title>
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
<script type="text/javascript"> window.resizeTo(300,150) </script>
<table border="0" cellspacing="0" cellpadding="4" width="96%">
<tr><td align="right" class="smallhead">Record Copied as  <?php echo $site."-COPY"; ?> </td></tr>
<tr><td class="text"><p class="text" align="justify">



</td></tr>
<tr>
<td align="left" class="text"></td>
<td align="right" class="text"><a href="@" onclick="closeWindow();">Close</a></td>
<td align="right" class="text"></td></tr>
</table>

</body></html>
