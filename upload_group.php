<?php
include ('site-monitor.conf.php');
echo"<html><body>";
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
$appfile=explode('/',$_FILES['userfile']['type']);
//echo "***".$appfile[0] . "****";
if ($appfile[0] == "image")
{
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile))
	{
    		//print "File is valid, and was successfully uploaded. ";
		echo $_FILES['userfile']['name'] . " \nsuccessfully uploaded\n";
		echo "as ".$filename."\n";
    		echo "<center><a href=@ onclick='javascript:self.close()'>Close </a></center>";
		//print "Here's some more debugging info:\n";
    		print_r($_FILES);
		echo  "</pre>";
		// write to ops form and submit it to save to DB
		echo '<script type="text/javascript">';
		echo 'window.opener.document.getElementById(\'myform\').txtgroupimage.value = \'' . $filename.'\';';
		echo 'window.opener.document.getElementById(\'myform\').submit();';
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
elseif ($appfile[0] == "application")  // Of Application type .php .exe.....etc
{
	echo "I don't freaking think so\n\n Sorry not this time....";
	echo "<center><a href=@ onclick='javascript:self.close()'>Close </a></center><pre>";
}
else
{
	echo "File don't seem to be a supported image type";
	echo "<center><a href=@ onclick='javascript:self.close()'>Close </a></center><pre>";
}
print "</body></html>";
?>
