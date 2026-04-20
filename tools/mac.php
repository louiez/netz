<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<!-- Traceroute.php -->
<!-- Copyright 2005 Louie Zarrella -->

<html>
<head>
<title>NETz Mac address search</title>
<link rel="stylesheet" href="../style/grey-large.css" type="text/css">
<script type="text/javascript">
function openhelp(url)
{
        window.open( url, "","resizable=1,HEIGHT=250,WIDTH=300");
}

</script>

</head>

<html><body><a href="tools.php">NETz web Tools</a><br>
<h6> MAC address Manufacture search </h6>
<form action="mac.php" method=POST>
<input type=text name="mac" width="100" size="35">
<input class="button"  type=submit value="MacSearch">
&nbsp;<a href="javascript:openhelp('../help/macsearch.html')">?</a>
</form>

<?php
$mac=$_REQUEST['mac'];
if ($mac == "" ) $mac=$_POST['mac'];
$host_escape = escapeshellcmd($mac);
echo "<b>MAC address search $host_escape</b><br><br>";
echo("<pre>");
$nmac=system("echo " . $host_escape . " | sed 's/\://g' | sed 's/\-//g' | sed 's/\ //g' | cut -c 1-6");
//echo $nmac;
system("grep -i -A 4 -B 1 " . $nmac. " oui.txt");
//system('/usr/bin/whois -H '.$host_escape);
echo("</pre>");


?>
