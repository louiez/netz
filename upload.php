<?php
include ('site-monitor.conf.php');
echo"<html><body>";
//$filename=$_FILES['userfile']['name'];
//$file_location_name = $uploadDir . $filename;
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
        //$uploadFile = $uploadDir .$UID.".". $filename;
        $uploadFile =$uploadDir .$filename;

print "<pre>";
$filetype=explode('/',$_FILES['userfile']['type']);
//echo "***".$filetype[0] . "****";
$temp_file=$_FILES['userfile']['tmp_name'];
if ($filetype[0] == "image" || $filetype[1] == "pdf")
{
	if (move_uploaded_file($temp_file, $uploadFile))
	{
    		//print "File is valid, and was successfully uploaded. ";
		echo $_FILES['userfile']['name'] . " \nsuccessfully uploaded\n";
    		echo "<center><a href=@ onclick='javascript:self.close()'>Close </a></center>";
		//print "Here's some more debugging info:\n";
    		//print_r($_FILES). "<pre>";
		// write to ops form and submit it to save to DB
		echo '<script type="text/javascript">';
		echo 'var opsPage = window.opener.document.getElementById(\'myform\');';
		echo 'opsPage.txtnetworkmapimage.value = \'' . $filename.'\';';
		echo 'opsPage.submit();';
		echo 'self.close();';
		echo '</script>';		
	}
	else
	{
    		print "Possible file upload attack!  Here's some debugging info:\n";
    		print_r($_FILES);
		print $_FILES['userfile']['size']. "<pre>";
		
	}
}
elseif ($filetype[0] == "application")  // Of Application type .php .exe.....etc
{
	echo "I don't freaking think so\n\n Sorry not this time....".$filetype[1];
	echo "<center><a href=@ onclick='javascript:self.close()'>Close </a></center><pre>";
}
else
{
	echo "file type ".$filetype[1]. " is not a supported upload<br>";
	echo "Only Image or PDF files...<br>";
	echo "use the File Attachment";
	echo "<center><a href=@ onclick='javascript:self.close()'>Close </a></center><pre>";
}
print "</body></html>";
?>
