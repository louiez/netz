<?php
include_once('site-monitor.conf.php');
$ipl=$_GET['ip'];
$IPALIVE=exec("sudo ". $basedir . "ping-test-web.php ".$ipl);
echo $IPALIVE;
?>
