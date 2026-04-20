<?php
/*###############################################################
        NETz Network Management system                          #
        http://www.proedgenetworks.com/netz                     #
                                                                #
                                                                #
        Copyright (C) 2005-2006 Louie Zarrella                  #
        louiez@proedgenetworks.com                              #
                                                                #
        Released under the GNU General Public License           #
        Copy of License available at :                          #
        http://www.gnu.org/copyleft/gpl.html                    #
###############################################################*/

include('../../logon.php');
include_once("../../site-monitor.conf.php");
include('../../write_access_log.php');

// open connection to Database server
$conns = mysql_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD);
if (!$conns) {
   die('Could not connect: ' . mysql_error());
}
// Select database
mysql_select_db(NETZ_DATABASE);

echo"<html><body>";
$site = $_GET['site'];
// see if we are calling to delete and get the UID to delete
//$delete_dduid=$_POST['delete'];
echo "<pre>";
//if ($delete_uid==""){
	// if not deleting... get the file to save
        $filename=$_FILES['userfile']['name'];
	// clean up the file names # & + ; are ok for filesnmas in windows and linux... 
	// but the web don't like it
        $filename=str_replace(" ", "_",$filename);
        $filename=str_replace("'", "",$filename);
	$filename=str_replace("#", "",$filename);
        $filename=str_replace("&", "",$filename);
        $filename=str_replace("+", "",$filename);
        $filename=str_replace(";", "",$filename);
//        $UID= md5(date("U")); // md5 hash of Seconds since the Unix Epoch... no special reason.. :-)  
	$file_location_name = $uploadDir . $filename;
	//$filetype=explode('/',$_FILES['userfile']['type']);
	$filetype=$_FILES['userfile']['type'];
	$temp_file=$_FILES['userfile']['tmp_name'];
//$fi = new finfo(FILEINFO_MIME, "/etc/httpd/conf/magic");

	if (move_uploaded_file($temp_file, $file_location_name))
	{
    		//echo "<center><a href='csv_importer3.php?filename=".$uploadDir . $filename."' >Close </a></center>";
		echo '<script type="text/javascript">';
		echo 'window.location.assign("csv_importer3.php?filename='.$uploadDir . $filename.'&del='.$_POST['delimiter'].'&header='.$_POST['has_header'].'")';
		echo '</script>';
		//print "Here's some more debugging info:\n";
    		//print_r($_FILES). "<pre>";
		// write to ops form and submit it to save to DB
		//echo '<script type="text/javascript">';
		//echo 'var opsPage = window.opener.document.getElementById(\'myform\');';
		//echo 'opsPage.txtnetworkmapimage.value = \'' . $filename.'\';';
		//echo 'opsPage.submit();';
		//echo 'self.close();';
		//echo '</script>';		
	}
	else
	{
    		print "Possible file upload attack!  Here's some debugging info:\n";
    		print_r($_FILES);
		print $_FILES['userfile']['size']. "<pre>";
		
	}
/*
}else{
	$SQL="SELECT * FROM ATTACHMENTS WHERE ";
	$SQL .= "UID = '".$delete_uid."'";
	$result=mysql_query($SQL);
	$row = mysql_fetch_assoc($result);
	$filename=$row['FILENAME'];
	$delete_file = $uploadDir .$delete_uid.".". $filename;
	unlink($delete_file);
	$SQL="DELETE FROM ATTACHMENTS WHERE ";
	$SQL .= "UID = '".$delete_uid."'";
	mysql_query($SQL);
echo $filename." deleted\n";
echo "<center><a href=@ onclick='javascript:self.close()'>Close </a></center>";
}
*/
print "</body></html>";
?>
