<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
include('../../logon.php');
include_once("../../site-monitor.conf.php");
?>
<HTML>
<HEAD>
 <TITLE>Show Reminders</TITLE>
        <?php $style= $_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
        <link rel="stylesheet" href="<?php echo "../../".$style  ?>" type="text/css">

<script type="text/javascript"  src="../../table_roll_over.js"> </script>
<script type="text/javascript"  src="../../size_window.js"> </script>
 <script language='javascript'>
function addRowDOM( nm,dt,time,des,id, table, rat)
{
        var tblBody = document.getElementById(table).tBodies[0];
        var newRow = tblBody.insertRow(-1);
        switch(rat){
                case "0": ratText="On Time"; break;
                case "5": ratText="5 min before"; break;
                case "10": ratText="10 min before"; break;
                case "15": ratText="15 min before"; break;
                case "30": ratText="30 min before"; break;
                case "45": ratText="45 min before"; break;
                case "60": ratText="1 hr before"; break;
                case "120": ratText="2 hr before"; break;
                case "300": ratText="5 hr before"; break;
                case "900": ratText="15 hrs before"; break;
                case "1440": ratText="1 day before"; break;
                case "2880": ratText="2 days before"; break;
                case "10080": ratText="1 wk before"; break;
        }
        newRow.id=id;
        // create TD and add name
        var newCell0 = newRow.insertCell(0);
        newCell0.style.borderStyle="solid";
        newCell0.style.borderWidth="1px";
        newCell0.style.borderColor="blue";
	newCell0.style.whiteSpace="nowrap";
        newCell0.onmouseover=function(){document.body.style.cursor='pointer';}
        newCell0.onmouseout=function(){document.body.style.cursor='default';}
        newCell0.onclick=function(){document.getElementById('alert_description').value=des;}
        newCell0.appendChild(document.createTextNode(nm)); 
        // create td and add date time
        var newCell1 = newRow.insertCell(1);
        newCell1.appendChild(document.createTextNode(dt+' '+time)); 
        newCell1.style.borderStyle="solid";
        newCell1.style.borderWidth="1px";
        newCell1.style.borderColor="blue";
        newCell1.style.whiteSpace="nowrap";
        // create td and add the remind me time
        var newCell2 = newRow.insertCell(2);
        newCell2.appendChild(document.createTextNode(ratText)); 
        newCell2.style.borderStyle="solid";
        newCell2.style.borderWidth="1px";
        newCell2.style.borderColor="blue";
        newCell2.style.whiteSpace="nowrap";
        // create td and add dismiss
        var newCell3 = newRow.insertCell(3);
        newCell3.style.borderStyle="solid";
        newCell3.style.borderWidth="1px";
        newCell3.style.borderColor="blue";
        tag='<a onclick="javascript:return window.opener.dismiss_reminder(\''+id+'\')" href=\"@\">Dismiss</a>';
        newCell3.innerHTML=tag;
        // create td and add Edit
        var newCell4 = newRow.insertCell(4);
        newCell4.style.borderStyle="solid";
        newCell4.style.borderWidth="1px";
        newCell4.style.borderColor="blue";
	dese=escape(des);
	tag='<a href=\"\" onclick="javascript:window.opener.edit_reminder(\''+nm+'\',\''+dt+'\',\''+time+'\',\''+dese+'\',\''+id+'\',\''+rat+'\'); self.close()">edit</a>';
        newCell4.innerHTML=tag;
}                                     
function getRefToDivMod( divID, oDoc ) {
  if( !oDoc ) { oDoc = document; }
  if( document.layers ) {
	alert("document.layers");
    if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
      for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
        y = getRefToDivMod(divID,oDoc.layers[x].document); }
      return y; } }
  if( document.getElementById ) { return oDoc.getElementById(divID); }
  if( document.all ) { return oDoc.all[divID]; }
  return document[divID];
}
 </script>
</HEAD>
<BODY   topmargin="0"  
	marginheight="0" 
	leftmargin="0" 
	marginwidth="0"
	onunload="window.opener.reminder_child=false; "
	onload="self.focus();">
<div  id="show_div" style="z-index: 10;
        position: absolute; 
        left: 0px; 
        top: 0px; 
        padding: 10px; 
        border: black 3px solid" > 
                <center><b> Reminders </b></center>
        <table id="show_table"  style="margin-left:0;color: white;border-width: 2px;border-color: white;border-style: inset; width: 100%" >
        <thead>
                <tr><td style="text-align:center; font-weight:bold; white-space:nowrap;">
                        Reminder name
                </td><td style="text-align:center; font-weight:bold; white-space:nowrap;">
                        Date and Time
                </td><td style="text-align:center; font-weight:bold; white-space:nowrap;">
                        Remind me
		</td><td style="text-align:center; font-weight:bold; white-space:nowrap;">
                        &nbsp;
                </td><td style="text-align:center; font-weight:bold; white-space:nowrap;">
                        &nbsp;
                </td></tr>
        </thead>
        <tbody>
		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<!-- Dummy2 -->
        </tbody>
        </table>
<?php
$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
$foo="";
$first = 0;
// now go thru the reminders for a match
$SQL_item = "SELECT * FROM REMINDERS WHERE USERNAME = '".$_SESSION['user']."' ORDER BY REMINDER_DATE_TIME";
$result_item=mysqli_query($conn,$SQL_item);
	while ($row_item = mysqli_fetch_assoc($result_item)){
		// remove any CR/LF chars from description javascript don't like it
                $s=array(Chr(13),Chr(10),Chr(34),Chr(39),Chr(44),Chr(96));
		$r=array("\\n","\\n","\"","\'","\,",""); 
                $d=str_replace($s, $r,$row_item['DESCRIPTION']);
                if ($first == 0){
                	$first_dec="document.getElementById('alert_description').value='".$d."'; \n";$first =1;
                }
		$date = date('m/d/Y',strtotime(trim($row_item["REMINDER_DATE_TIME"])));
                $time = date('g:i A',strtotime(trim($row_item["REMINDER_DATE_TIME"])));
                $foo .= "addRowDOM('".htmlentities($row_item['REMINDER_NAME'])."','";
		$foo .= $date."','";
                $foo .= $time."','";
                $foo .= $d."','";
		$foo .=$row_item['REMINDER_ID']."','";
		$foo .= "show_table','";
		$foo .= $row_item['REMINDER_ADVANCE_TIME']."'); \n" ;
	}
?>
        <br>
        <center>Description</center>
        <br>
        <textarea onkeydown="this.blur();" rows="5"  cols="20" style="width:100%" id="alert_description"name="alert_description"> </textarea>
        <br><br>
                        <center>
                        <input class="button" 
                                type="button" 
                                name="close" 
                                value="Close" 
                                onclick="window.opener.reminder_child=false; self.close();">
                        <input class="button" 
                                type="button" 
                                name="new_reminder" 
                                value="New Reminder" 
                                onclick="window.opener.show_hide(window.opener.document.getElementById('reminder_div'),'');window.opener.init_boxes();self.close()">
<?php
if ($foo != ""){
echo "<script type=\"text/javascript\">";
echo $foo;
echo $first_dec;
echo "</script>";
}
?>
                        </center>
</div>
<div id="code_div"> </div>
<script type="text/javascript">
sizeToFit('show_div')
addTableRolloverEffect('show_table','tableRollOverEffect1','tableRowClickEffect1');

</script>
</BODY>
</HTML>

