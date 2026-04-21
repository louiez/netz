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
//include('../../auth.php');
include_once("../../site-monitor.conf.php");
?>
<html>

<head>
        <?php $style= $_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
        <link rel="stylesheet" href="<?php echo "../../".$style  ?>" type="text/css">
<script language="javascript" src="string_functions.js"></script>
<script type="text/javascript"  src="../../size_window.js"> </script>
<script type="text/javascript" src="../../jquery.js"></script>
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

      function countChar(val) {
        var len = val.value.length;
        if (len >= 1000) {
          val.value = val.value.substring(0, 1000);
	// Change color red when out of characters
	// database set for 1000
	$('#charNum').css('color', 'red');
	$('#charNum').text("NO More Characters Left");
        } else {
	// set color green
	$('#charNum').css('color', 'green');
          $('#charNum').text((1000 - len)+ " Characters Left");
        }
      };
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
<title>Select Image</title>
</head>

<body>
<div id="container">
	<form method="POST" enctype="multipart/form-data" action="upload_attachment.php?site=<?php echo $_GET['site'] ?>" name="myform"  id="savefrm">
  		<center>
    		<input type="hidden" name="MAX_FILE_SIZE" value="200000000" />
    		Choose a file to upload for <?php echo $_GET['site'] ?>:<br>
		MAX size <?php echo ini_get("upload_max_filesize");?><br> 
		<input name="userfile" type="file" />
  		<input type="hidden" name="spath" value="NetworkImages" size="20">
		<br>
      Display Name (Optional)<input type="text" name="display_name" value="" size="20"><br>
      Description (Optional)<textarea onkeyup="countChar(this)" name="description" value="" rows="4" cols="50"></textarea><br>
	<div id="charNum" style="color:green"></div><br>
		Limit access to 
		<select name="user_level" id="user_level">
			<option value="0" SELECTED>All Users </option>
			<option value="1">read only (1)</option>
			<option value="2">read only ops (2)</option>
			<option value="4">read/write order (4)</option>
			<option value="6">read/write ops (6)</option>
			<option value="7">read/write ops (7)</option>
			<option value="9">Admin (9)</option>
			<option value="10">Admin Full (10)</option>
		</select>
		<hr noshade color="#0000FF">
  		<br>
		<input type="button" value="Save" name="Save" onclick="return uploadIt();">&nbsp;
   		<input type="button" value="Cancel" name="B2" size="20" onclick="javascript:window.close()">&nbsp;
		<div id="message"></div>

   		</center>
   	</form>
<br>
</div>
</body>
<script type="text/javascript">
sizeToFit("container");
</script>
</html>
