<?php
include('../auth.php');?>
<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<!-- Traceroute.php 
     Copyright 2005 Louie Zarrella 
-->

<html>
<head>
<?php
include_once("../site-monitor.conf.php");
include('../write_access_log.php');
session_start();
session_write_close();
ob_implicit_flush(true);
ob_end_flush();
$style=$_SESSION['style']; if ($style==""){$style="../style/ultramarine.css";}
?>
<link rel="stylesheet" href="../<?php echo $style  ?>" type="text/css">
<title>NETz Trace Route</title>
</head>
<html><body><a href="tools.php">NETz web Tools</a><br>
<?php
$host=$_REQUEST['ip'];
if ($host == "" ) $host=$_POST['ip'];
if ($host != "" ){
	$host_escape = escapeshellcmd($host);
	echo("Tracing route to $host <br>");
	echo("<pre>");
	passthru('/bin/traceroute -I '.$host_escape);
	system("killall -q traceroute");
	echo("</pre>");
}else{
echo " No address entered";
}

?>
</body></html>



