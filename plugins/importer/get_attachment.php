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
<?php
include('../../logon.php');
include_once("../../site-monitor.conf.php");
?>
<html>

<head>
        <?php $style= $_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
        <link rel="stylesheet" href="<?php echo "../../".$style  ?>" type="text/css">
<script language="javascript" src="../../string_functions.js"></script>
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
	
<script type="text/javascript">

function delete_image()
{	
	window.opener.document.getElementById('myform').txtnetworkmapimage.value = "";
	window.opener.document.getElementById('myform').submit();
	self.close();
	
}
function uploadIt(){
	document.getElementById('message').innerHTML = "<span style='color:red;font-weight:bold' >Uploading...... </span>"
	document.getElementById('savefrm').submit();
}
</script>
<meta http-equiv="Content-Language" content="en-us">

<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Select CSV file</title>
</head>

<body>
<div id="container">
	<form method="POST" enctype="multipart/form-data" action="upload_attachment.php?site=<?php echo $_GET['site'] ?>" name="myform"  id="savefrm">
  		<center>
    		<input type="hidden" name="MAX_FILE_SIZE" value="200000000" />
    		Choose a file to upload for <?php echo $_GET['site'] ?>:<br> <input name="userfile" type="file" />
  		<input type="hidden" name="spath" value="NetworkImages" size="20">
		<br>
		<hr noshade color="#0000FF">
  		<br>
                Select the character the feilds are separated with <br>
                Comma (,) <input type="radio" value="comma" name="delimiter" CHECKED >&nbsp; &nbsp;<br>
                Semicolon (;)<input type="radio" value="semi" name="delimiter" ><br>
		first line Has Header data <input type="checkbox" value="yes" name="has_header" ><br><br>
		<input type="button" value="Upload" name="upload" onclick="return uploadIt();">&nbsp;
   		<input type="button" value="Cancel" name="B2" size="20" onclick="javascript:window.close()">&nbsp;
		<div id="message"></div>

   		</center>
   	</form>
<br>
</div>
</body>
<script type="text/javascript">
document.getElementById('message').innerHTML = "";
</script>
</html>
