<!-- ############################################################
        NETz Network Management system				#
        http://www.proedgenetworks.com/netz			#
								#
								#
        Copyright (C) 2005-2006 Louie Zarrella			#
	louiez@proedgenetworks.com				#
								#
        Released under the GNU General Public License		#
	Copy of License available at :				#
	http://www.gnu.org/copyleft/gpl.html			#
############################################################# -->
<html>

<head>
<script language="javascript" src="string_functions.js"></script>
<script language="javascript">
	function save_image_1()
	{
		var tmp = myform.B1.value;
		//alert(tmp);
		alert(get_file_from_path(tmp));
		//window.opener.myform.txtnetworkmapimage.value = myform.B1.value;
		//window.opener.myform.submit();
		//window.close();
	}

</script>
	
<script language="javascript">

function delete_image()
{	
	window.opener.document.getElementById('myform').txtnetworkmapimage.value = "";
	window.opener.document.getElementById('myform').submit();
	self.close();
	
}
function saveit()
{
	//alert("test");
	//alert(document.myform.R1[1].checked);
	//alert(window.opener.document.myform.txtnetworkmapimage.value);
//alert(window.opener.document.getElementById('myform').name);
	if (document.getElementById('savefrm').R1[0].checked == true) 
		{
			window.opener.document.getElementById('myform').txtnetworkmapimage.value = "Typical_Napa_store_GPC_WAN2.jpg"
			window.opener.document.myform.submit();
			self.close();
		}
	else if (document.getElementById('savefrm').R1[1].checked == true)  
		{
			window.opener.document.getElementById('myform').txtnetworkmapimage.value = "Basic_VSat_Site.jpg"
			window.opener.document.getElementById('myform').submit();
			self.close();
		}
	else
		{
			//alert(getElementId['savefrm'].txtnetworkmapimage.value);
			window.opener.document.getElementById('myform').txtnetworkmapimage.value = get_file_from_path(document.getElementById('savefrm').userfile.value);
			window.opener.document.getElementById('myform').submit();
			//alert(get_file_from_path(document.getElementById('savefrm').userfile.value));
			document.getElementById('savefrm').submit();
			//self.close();
		}
	return true ;
}
</script>
<meta http-equiv="Content-Language" content="en-us">

<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Select Image</title>
</head>

<body>
	<form method="POST" enctype="multipart/form-data" action="upload.php" name="myform"  id="savefrm">
  		<center>
    		<input type="hidden" name="MAX_FILE_SIZE" value="200000000" />
    		Choose a file to upload: <input name="userfile" type="file" />
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
