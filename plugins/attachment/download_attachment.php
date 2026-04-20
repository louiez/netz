<?php 
ob_start();
//include('../../logon.php');
include_once("../../site-monitor.conf.php");
include('../../write_access_log.php');

// open connection to Database server
$conns = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conns) {
   die('Could not connect: ' . mysqli_error());
}
// Select database


// To get PHP to load a PDF (for example) from file, use the following code.

$filename = $uploadDir.$_GET['uid'].".". $_GET['filename']; 
        $SQL="SELECT * FROM ATTACHMENTS WHERE ";
        $SQL .= "UID = '".$_GET['uid']."'";
        $result=mysqli_query($conns,$SQL);
        $row = mysqli_fetch_assoc($result);
echo $row['FILE_TYPE'];	
header("Cache-Control: public"); 
header("Content-Description: File Transfer"); 
header('Content-disposition: attachment; filename='.$_GET['filename']); 
header("Content-Type: ".$row['FILE_TYPE']); 
header("Content-Transfer-Encoding: binary"); 
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: '. filesize($filename)); 
readfile($filename); 
ob_end_flush();
exit();
?>
