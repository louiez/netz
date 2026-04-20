<?php
include('../auth.php');?>
<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<!-- Ping.php -->
<!-- Copyright 2005 Louie Zarrella -->

<head>
<title>Proedge Ping</title>
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

</head>

<html><body>
<a href="tools.php">NETz web Tools</a><br></body></html>
<?php
$host=$_REQUEST['ip'];
if ($host == "" ) $host=$_POST['ip'];
$host_escape = escapeshellcmd($host);
//$cmd=`ping -c 4 -A  $host_escape`;
//$cmd=`ping -c 500  -A  $host_escape`;
print "<b>Ping result for $host</b><br><br>";

//print "<pre>".$cmd."</pre>";
print "<pre>";
system('ping -c 10  -A  '.$host_escape);
#pageFooter();
?>
