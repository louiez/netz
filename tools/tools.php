<?php
include('../auth.php');?>
<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<!-- tools.php -->
<!-- Copyright 2005 Louie Zarrella -->

<html>
<head>
<?php
include_once("../site-monitor.conf.php");
include('../write_access_log.php');

$style=$_SESSION['style']; if ($style==""){$style="../style/ultramarine.css";}
?>
<link rel="stylesheet" href="../<?php echo $style  ?>" type="text/css">
<title>NETz Web Tools </title>
<script type="text/javascript">
function openhelp(url)
{
        window.open( url, "","resizable=1,HEIGHT=250,WIDTH=300");
}
function visualTrace(){
	ip=document.getElementById("ip").value;
	//if (ip == ""){alert("Enter an IP address");return 0;}
        window.open( "trace-gm.php?ip="+ip, "","resizable=1");
}
</script>
</head>
<body>
<a target="_self" href="../main.php"><img height="49" border="0" align="middle" width="164" src="../netz.jpg" alt="netz.jpg"></a>
<h5> Whois Lookup </h5>
<form action="whois.php" method=POST>
<input type=text name="ip" width="100" size="35">
<input class="button" type=submit name="query whois" value="Query Whois">
</form>
<p> Enter a domain ie: thedomain.com  , Not www.thedomain.com <br>
or enter an IP address to see if there is a reverse lookup for it</p>
<hr>
<h5> Ping host </h5>
<form action="ping.php" method=POST>
<input type=text name="ip" width="100" size="35">
<input class="button"  type=submit value="ping">
</form>

<h5> Trace Route to host </h5>
<form action="trace.php" method=POST >
<input type=text id="ip" name="ip" width="100" size="35">
<input class="button"  type=submit value="Trace">
<input class="button"  type=button value="Visual Trace Route" onclick="visualTrace()">
</form>

<h5> DNS query </h5>
<form action="dns.php" method=POST>
<input type=text name="ip" width="100" size="35">
<input class="button"  type=submit value="QueryDNS">
</form>

<h5> MAC address Manufacture search </h5>
<form action="mac.php" method=POST>
<input type=text name="mac" width="100" size="35">
<input class="button"  type=submit value="MacSearch">
&nbsp;<a href="javascript:openhelp('../help/macsearch.html')">?</a>
</form>


</body>
</html>
