<?php
include('../auth.php');?>
<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<!-- Traceroute.php -->
<!-- Copyright 2005 Louie Zarrella -->

<html>
<head>
<title>Proedge DNS</title>
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
if ($host != "" ) {
$host_escape = escapeshellcmd($host);
echo "<b>DNS interrogation result for $host</b><br><br>";
$rev=`dig +short -x $host_escape`;
$ip=`dig +short a $host_escape`;
if ($rev == "")
{
        $rev=`dig +short -x $ip`;
}

$soa=`dig +short soa $host_escape`;
$mx=`dig +short mx $host_escape`;
$ns=`dig +short ns $host_escape`;
$txt=`dig +short txt $host_escape`;
//$soa=system('dig +short soa '.$host_escape);

echo("<pre style=\"font-weight:bold\">");
echo "Reverse look up<br>".$rev."<br><br>";
echo "IPs<br>".$ip."<br><br>";
echo "SOA<br>".$soa."<br><br>";
echo "Mail servers<br>".$mx."<br><br>";
echo "Name servers<br>".$ns."<br><br>";
echo "Text records<br>".$txt."<br><br>";
echo("</pre>");
}else{
echo " No address entered";
}

?>
</body></html>
