<?php
//include('../../logon.php');
include_once("../../site-monitor.conf.php");
mysql_select_db(NETZ_DATABASE);
mysql_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD);
$foo="";
$first = 0;

// now go thru the reminders for a match
$SQL_item = "SELECT * FROM ATTACHMENTS WHERE SITE_ID = '".$_GET['site']."' ";
$SQL_item .= "AND MIN_USER_ACCESS_LEVEL <= '".$_SESSION['accesslevel']."'";
$result_item=mysql_query($SQL_item);
$count=mysql_num_rows($result_item);
if ($count = 0){
	echo "<a onclick=\"return open_message('Attachments',";
	echo "'plugins/attachment/get_attachment.php?site=";
	echo $_GET['site']."')\" ";
	echo "href=\"@\">Add File Attachment</a>";

}else{
        echo "<a style=\"color:#00FF00\" onclick=\"return open_message('Attachments',";
        echo "'plugins/attachment/show_attachments.php?site=";
        echo $_GET['site']."')\" ";
        echo "href=\"@\">Attachments</a>";	
}

