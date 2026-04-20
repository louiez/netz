<?php
include('../auth.php');?>
<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<!-- whois.php -->
<!-- Copyright 2005 Louie Zarrella -->

<html>
<head>
<title>NETz Whois</title>
<?php
include_once("../site-monitor.conf.php");
include('../write_access_log.php');

$style=$_SESSION['style']; if ($style==""){$style="../style/ultramarine.css";}
?>
<link rel="stylesheet" href="../<?php echo $style  ?>" type="text/css">
</head>
<html><body><a href="tools.php">NETz web Tools</a><br>
<?php
$host=$_REQUEST['ip'];
if ($host == "" ) $host=$_POST['ip'];
$host_escape = escapeshellcmd($host);
echo "<b>Whois interrogation result for $host</b><br><br>";
echo("<pre>");
system('/usr/bin/whois -H '.$host_escape);
echo("</pre>");


?>
