<!-- ############################################################
        NETz Network Management system				#
        http://www.proedgenetworks.com/netz			#
								#
								#
        Copyright (C) 2005-2026 Louie Zarrella			#
	louiez@proedgenetworks.com				#
								#
        Released under the GNU General Public License		#
	Copy of License available at :				#
	http://www.gnu.org/copyleft/gpl.html			#
############################################################# -->
<html>

<head>
<script type="text/javascript" src="string_functions.js"></script>
	
<script type="text/javascript">

function delete_image()
{	
	window.opener.document.getElementById('myform').txtgroupimage.value = "";
	window.opener.document.getElementById('myform').submit();
	self.close();
	
}

</script>
<meta http-equiv="Content-Language" content="en-us">

<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Select Image</title>
</head>

<body>
	<form method="POST" enctype="multipart/form-data" action="upload_group.php" name="myform"  id="savefrm">
  		<center>
    		<input type="hidden" name="MAX_FILE_SIZE" value="200000000" />
    		Choose an Image to upload: <input name="userfile" type="file" />
  		<input type="hidden" name="spath" value="NetworkImages" size="20">
		<hr noshade color="#0000FF">
  		<br>
		<input type="submit" value="Save" name="Save">&nbsp;
   		<input type="button" value="Cancel" name="B2" size="20" onclick="javascript:window.close()">&nbsp;

   		<input type="button" value="Delete current Imagel" name="B3" size="20" 
			style="color:white;background-color:red;font-weight:bold;font-size:8pt" onclick="delete_image()">
   		</center>
   	</form>

</body>

</html>
