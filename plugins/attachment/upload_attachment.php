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

//include('../../logon.php');
include_once("../../site-monitor.conf.php");
include('../../write_access_log.php');

// open connection to Database server
$conns = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conns) {
   die('Could not connect: ' . mysqli_error());
}
// Select database

echo"<html><body>";
$site = $_GET['site'];
$display_name=$_POST['display_name'];
$description=$_POST['description'];
// see if we are calling to delete and get the UID to delete
$delete_uid=$_POST['delete'];
echo "<pre>";
if ($delete_uid==""){
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
        $UID= md5(date("U")); // md5 hash of Seconds since the Unix Epoch... no special reason.. :-)  
	$file_location_name = $uploadDir .$UID.".". $filename;
	//$filetype=explode('/',$_FILES['userfile']['type']);
	$filetype=$_FILES['userfile']['type'];
	$temp_file=$_FILES['userfile']['tmp_name'];
//$fi = new finfo(FILEINFO_MIME, "/etc/httpd/conf/magic");

	if (move_uploaded_file($temp_file, $file_location_name))
	{
		$upload_user = addslashes($_SESSION['user']);	
		$upload_date_time = date("Y-m-d G:i:s");
		$min_user_level = $_POST['user_level'];
		// create query string
		$SQL="INSERT INTO ATTACHMENTS SET ";
		$SQL .= "FILENAME = '".$filename."', ";
		$SQL .= "DISPLAY_NAME = '".$display_name."', ";
		$SQL .= "DESCRIPTION = '".addslashes($description)."', ";
		$SQL .= "SITE_ID = '".$site."', ";
		$SQL .= "DATE_UPLOADED = '".$upload_date_time."', ";
		$SQL .= "UPLOAD_USER = '".$upload_user."', ";
		$SQL .= "MIN_USER_ACCESS_LEVEL = '".$min_user_level."', ";
                $SQL .= "FILE_TYPE = '".$filetype."', ";
		//$SQL .= "FILE_TYPE = '".mime_content_type($file_location_name)."', ";
		$SQL .= "UID = '".$UID."'";
		// Query database
		$result=mysqli_query($conns,$SQL);
		//echo $SQL;
		echo $filetype ."\n";
		//echo $fi->buffer(file_get_contents( $filename));."\n";
    		//print "File is valid, and was successfully uploaded. ";
		echo $_FILES['userfile']['name'] . " \nsuccessfully uploaded\n";
    		echo "<center><a href=@ onclick='javascript:self.close()'>Close </a></center>";
		//print "Here's some more debugging info:\n";
    		//print_r($_FILES). "<pre>";
		// write to ops form and submit it to save to DB
		echo '<script type="text/javascript">';
		//echo 'var opsPage = window.opener.document.getElementById(\'myform\');';
		//echo 'opsPage.txtnetworkmapimage.value = \'' . $filename.'\';';
		//echo 'opsPage.submit();';
		echo 'self.close();';
		echo '</script>';		
	}
	else
	{
    		print "Possible file upload attack!  Here's some debugging info:\n";
    		print_r($_FILES);
		print $_FILES['userfile']['size']. "<pre>";
		
	}
}else{
	$SQL="SELECT * FROM ATTACHMENTS WHERE ";
	$SQL .= "UID = '".$delete_uid."'";
	$result=mysqli_query($conns,$SQL);
	$row = mysqli_fetch_assoc($result);
	$filename=$row['FILENAME'];
	$delete_file = $uploadDir .$delete_uid.".". $filename;
	unlink($delete_file);
	$SQL="DELETE FROM ATTACHMENTS WHERE ";
	$SQL .= "UID = '".$delete_uid."'";
	mysqli_query($conns,$SQL);
echo $filename." deleted\n";
echo "<center><a href=@ onclick='javascript:self.close()'>Close </a></center>";
echo '<script type="text/javascript">';
echo 'self.close();';
echo '</script>';
}
print "</body></html>";
?>
