<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
//include('../../logon.php');
//include 'auth.php';
include_once("../../site-monitor.conf.php");
?>
<HTML>
<HEAD>
 <TITLE>Attachments</TITLE>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8">
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
        <?php $style= $_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
        <link rel="stylesheet" href="<?php echo "../../".$style  ?>" type="text/css">
<script type="text/javascript"  src="../../table_roll_over.js"> </script>
<script type="text/javascript"  src="../../size_window.js"> </script>
<script type="text/javascript">
function delete_request(id){
	x=document.getElementById('name').innerHTML;
	ans=confirm("delete \n"+x+" ?");
	if(ans == true) {
		document.getElementById('delete').value = id.name;
		document.getElementById('delete_form').submit();
		return false;
	}else{
		return true ;
	}
}
 </script>
</HEAD>
<BODY style="margin-left: 0px;"
	onunload="window.opener.reminder_child=false; "
	onload="self.focus();">
<div  id="show_div" style="z-index: 10;
        position: absolute; 
        left: 0px; 
        top: 0px; 
        padding: 10px;" > 
<form method="POST" action="upload_attachment.php" name="delete_form"  id="delete_form">
                <center><b> Attachments </b></center>
        <table id="show_table"  style="margin-left: -10px;color: white;border-width: 2px;border-color: white;border-style: inset; width: 100%" >
        <thead>
                <tr>
		<td style="text-align:center; font-weight:bold">
                        Name
                </td>
		<td style="text-align:center; font-weight:bold">
                        Description
                </td>
<!--
		<td style="text-align:center; font-weight:bold">
                        File Name
                </td>
-->
		<td style="text-align:center; font-weight:bold">
                        Date Uploaded
                </td>
		<td style="text-align:center; font-weight:bold">
                        Uploaded by
                </td>
		<td style="text-align:center; font-weight:bold">
                        Size
                </td>
		<td style="text-align:center; font-weight:bold">
                        Delete
                </td>
		<td style="text-align:center; font-weight:bold">
                        User Access Level
                </td></tr>
        </thead>
        <tbody>
<?php
$conn=mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
$foo="";
$first = 0;

// now go thru the reminders for a match
$SQL_item = "SELECT * FROM ATTACHMENTS WHERE SITE_ID = '".$_GET['site']."'";
$result_item=mysqli_query($conn,$SQL_item);
        while ($row_item = mysqli_fetch_assoc($result_item)){
		if ((int)$_SESSION['accesslevel'] >= (int)$row_item['MIN_USER_ACCESS_LEVEL'] ){
			echo "<tr>";
			/* Name Linked to filename*/
			if ($row_item["DISPLAY_NAME"] == ""){
				$display_name = $row_item["FILENAME"];
			}else{
				$display_name = $row_item["DISPLAY_NAME"];
			}
                        echo "<td style=\"white-space:nowrap;\">";
                        echo "<a id=\"name\" href=\"download_attachment.php?filename=".$row_item["FILENAME"];
                        echo "&amp;uid=".$row_item["UID"]."\">";
                        echo $display_name;
                        echo "</a></td>";


			/* Description */
                        echo "<td style=\"white-space:nowrap;\" >";
			// convert chars to html safe... then swap new line to <br>
                        echo nl2br(htmlentities($row_item["DESCRIPTION"]))."</td>";
                        /* Filename */
			//echo "<td style=\"white-space:nowrap;\">";
                        //echo $row_item["FILENAME"]."</td>";
			/* Upload date */
			echo "<td style=\"white-space:nowrap;\">";
			echo $row_item["DATE_UPLOADED"]."</td>";
			/* Uploaded By user... */
			echo "<td style=\"white-space:nowrap;text-align:center;\">";
                	echo $row_item["UPLOAD_USER"]."</td>";
			/* File Size */
                	echo "<td style=\"white-space:nowrap;\">";
			$file_location_name = $uploadDir .$row_item["UID"].".". $row_item["FILENAME"];
                	echo filesize($file_location_name)."</td>";
			/* Delete link */
                	echo "<td style=\"white-space:nowrap;text-align:center;\">";
			if ( $_SESSION['user'] == $row_item["UPLOAD_USER"] || (int)$_SESSION['accesslevel'] >= 7 ){
                		echo "<a name=\"".$row_item["UID"]."\" href=\"\" ";
				echo "onclick=\"javascript:return delete_request(this);\" > Delete </a>";
			}
			echo "</td>";
			/* User Access level */
                        echo "<td style=\"white-space:nowrap;text-align:center;\">";
			echo $row_item['MIN_USER_ACCESS_LEVEL'];
			echo "</td></tr>";
		}
	}
?>	
	</tbody>
	</table>
        <br><br>
                        <center>
		        <input type="hidden" name="delete" id="delete" value="">
                        <input class="button" 
                                type="button" 
                                name="close" 
                                value="Close" 
                                onclick="window.opener.reminder_child=false; self.close();">
                        <input class="button" 
                                type="button" 
                                name="add_attachment" 
                                value="Add Attachment" 
                                onclick="window.opener.open_message('attach','get_attachment.php?site=<?php echo $_GET['site'] ?>');
					self.close()">
	</form>
<?php
if ($foo != ""){
echo "<script type=\"text/javascript\">";
//echo "document.getElementById('show_div').style.left= \"450px\";";
echo $foo;
echo $first_dec;
echo "</script>";
}
?>
                        </center>
</div>
<div id="code_div"> </div>
<script type="text/javascript">
sizeToFit("show_div");
addTableRolloverEffect('show_table','tableRollOverEffect1','tableRowClickEffect1');

</script>
<?php
//print_r($_SESSION);
?>
</BODY>
</HTML>
