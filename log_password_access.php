<?php
include_once("auth.php");
include_once("site-monitor.conf.php");
$logfile = $netzlogs."netz.log";
$err_msg= $_SESSION['user']." Accessed ". $_GET["view"]. " Password - ".$_GET["site"];
error_log(date('Y-m-d G:i:s')." ".$err_msg."\n", 3, $logfile);
?>
