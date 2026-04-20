<?php
ob_start();
include_once("auth.php");
include_once("site-monitor.conf.php");
$ext = pathinfo($_GET["image"], PATHINFO_EXTENSION);
if ($ext == "pdf"){
        header('Content-type: application/pdf');
}else{
        header('Content-Type: image/png');
}
$IMGSTR=$uploadDir . $_GET["image"];
//header('Content-Type: image/png');
ob_end_flush;
readfile($IMGSTR)
?>
