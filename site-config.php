<?php
/*###############################################################
        NETz Network Management system                          #
        http://www.proedgenetworks.com/netz                     #
                                                                #
                                                                #
        Copyright (C) 2005-2026 Louie Zarrella                  #
        jwaldo85@gmail.com                             #
                                                                #
        Released under the GNU General Public License           #
        Copy of License available at :                          #
        http://www.gnu.org/copyleft/gpl.html                    #
###############################################################*/

ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL);

include_once("auth.php");
include_once("site-monitor.conf.php");
include('write_access_log.php');
include_once('lmz-functions.php');

$site=$_GET['site'];
?>
<html><head>

<?php
//----------------------+
//  User Access code	|
// =====================================================================================================//
//$acl=$_SESSION['accesstype'];                                                                           //
if ($_SESSION['accesslevel'] < 5){                                                                     //
        echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';	//
        echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';                        //
	exit();												//
        }                                                                                               //
// =====================================================================================================//
        $sql="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE SITE_ID = '".$site."'";
        $results = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($results);
	
?>

<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">

<title>NETz Site Config</title>
<script type="text/javascript" src="size_window.js"></script>
<script type="text/javascript">
function openhelp(url){
	window.open( url, "","resizable=1,HEIGHT=250,WIDTH=300");
}

function save_it(){
	// check if we have a "myform" (ops.php page) else just reload page (support.php)
	if (opener.document.myform){
		opener.document.myform.submit();
		document.confform.submit();
	}else{
		opener.location.reload(true);
	}
	return true;
}
function xset_checks(){
	if (document.getElementById('chkmonitorenable').checked){

		if (document.styleSheets[0].cssRules){
			theRules = document.styleSheets[0].cssRules;
		}else if (document.styleSheets[0].rules){
			theRules = document.styleSheets[0].rules;
		}

		document.body.style.color = theRules[0].style.backgroundColor ;
		document.getElementById('chkmonitorhttpenable').disabled = false;
		document.getElementById('chkmonitorhttpenable').style.backgroundColor = "yellow";
		document.getElementById('txtipfeild').style.color = "yellow";
	}
}

function toggleMenu(currMenu){
                if (document.getElementById) {
                        thisMenu = document.getElementById(currMenu).style;
                        if (thisMenu.display == "none") {
                                thisMenu.display = "block" ;
                        }else if (thisMenu.display == "block"){
                                thisMenu.display = "none" ;
                        }
			sizeToFit("show_div");
                        return false
                }else {
                        return true
                }
        }

function checkHTTP(){
	// get the selected ID for the IP select box
	id=document.getElementById('txthttpipfeild').selectedIndex;

	ip=document.getElementById('txthttpipfeild')[id].text;
	port=document.getElementById('txtmonhttpport').value;
	timeout=document.getElementById('txtmonhttptimeout').value;
	page=document.getElementById('txtmonhttppage').value;
	content=document.getElementById('txtmonhttpcontent').value;
	ssl=document.getElementById('chkssl').checked;
	// put it all together and  send
	testurl="site-http-test.php?ip="+ip+"&port="+port+"&timeout="+timeout+"&page="+page+"&content="+content+"&ssl="+ssl ;
	window.open(testurl,'','width=640,height=500,resizable=yes,scrollbars=yes,status=yes');
}
function onwindowclose(){
	b=opener.document.getElementsByTagName('body')[0];
	b.style.opacity = "1.0";
	b.style.filter = "alpha(opacity=1000)";
}
</script>
</head>

<body onunload="onwindowclose()">
<div  id="show_div" 
      style=" position: absolute; 
      left: 0px; 
      top: 0px; 
      padding: 10px;" >
	<form method="POST" action="write-site-config.php" name="confform"  id="confform">
		<br>
		<h2 class="center"><?php echo $site. " "; ?>Monitor/Alert Options</h2>
		<br>
		<fieldset>
		<legend>Monitoring</legend>
		<input type="hidden" name="txtsite" id="txtsite" value="<?php echo $site; ?>">
		<?php 
		if ($row['MONITOR_ENABLE'] == 1){
			$checked = "CHECKED";
			$disabled = "";
			$display="block";
		}else {
			$checked = "";
			//$disabled="DISABLED";
			$disabled = "";
			$display="none";
		} 
		?>
		Enable Monitor 
		<input type="checkbox" 
			name="chkmonitorenable" 
			id="chkmonitorenable" 
			value="ON" 
			<?php echo $checked; ?> onclick="toggleMenu('hideme')">&nbsp;&nbsp; 
		
		<p>
		IP to Monitor&nbsp; 
		<select size="1" name="txtipfeild" id="txtipfeild" <?php echo $disabled; ?>>
			<?php
			if ($row["MONITOR_IP_FIELD"] != ""){
				$monitoripfeild=$row["MONITOR_IP_FIELD"];
			}
			else{ $monitoripfeild = SITE_IP_DEFAULT ;}	
			?>
			<option value="<?php echo  $monitoripfeild; ?>" SELECTED><?php echo  Display_name($monitoripfeild);?></option>
			<option value="LAN_IP "><?php echo  Display_name("LAN_IP");?></option>
			<option value="LAN_GATEWAY"><?php echo  Display_name("LAN_GATEWAY");?></option>
			<option value="WAN_IP"><?php echo  Display_name("WAN_IP");?></option>
			<option value="WAN_GATEWAY"><?php echo  Display_name("WAN_GATEWAY");?></option>
		</select>
		&nbsp;&nbsp;&nbsp;
		TimeOut(ms) &nbsp; 
		<select size="1" name="txtmontimeout" id="txtmontimeout" <?php echo $disabled; ?>>
                <?php
		if ($row["MONITOR_TIMEOUT"] == "" || $row["MONITOR_TIMEOUT"] < 1000){$timeout="1001";}else{$timeout=$row["MONITOR_TIMEOUT"];}
                ?>
                        <option value="<?php echo  $timeout; ?>" SELECTED><?php echo  $timeout;?></option>
                        <option value="1000">1000</option>
                        <option value="2000">2000</option>
                        <option value="3000">3000</option>
                        <option value="4000">4000</option>
                        <option value="5000">5000</option>
                        <option value="6000">6000</option>
                        <option value="7000">7000</option>
                        <option value="8000">8000</option>
                        <option value="9000">9000</option>

                </select>

		<a href="javascript:openhelp('help/timeout.html')">?</a>
		<br>
		<hr>
		<span style="color:red">**** Current Monitor Cycle is <?php echo $moncycleinterval; ?> min **** </span><br>
                Monitor Cycles Failed before alerting &nbsp;
                <select size="1" name="txtmonalertcycles" id="txtmonalertcycles" <?php echo $disabled; ?>>
                <?php
                if ($row["MONITOR_ALERT_CYCLES"] == "" || $row["MONITOR_ALERT_CYCLES"] < 1){
			$timeout="1";
		}else{
			$monitoralertcycles=$row["MONITOR_ALERT_CYCLES"];
		}
                ?>
                        <option value="<?php echo  $monitoralertcycles; ?>" SELECTED><?php echo  $monitoralertcycles;?></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>

                </select>

                <a href="javascript:openhelp('help/monitorcycles_alert.html')">?</a>
                </p>
                </fieldset>		
		<br>
<div id="hideme" style="display:<?php echo $display; ?>">
 
               <fieldset>
                <legend>Web Server Checking</legend>

                <?php 	if ($row['MONITOR_HTTP_ENABLE'] == 1){
				$checked = "CHECKED";
				$displayhttp = "block";
			}else {
				$checked = "";
				$displayhttp = "none";} 
		?>
                Enable HTTP Checking
                <input style="border:none" type="checkbox" 
			name="chkmonitorhttpenable" 
			id="chkmonitorhttpenable" 
			value="ON" <?php echo $checked; ?> <?php echo $disabled; ?>
			onclick="toggleMenu('httpcheck')">&nbsp;&nbsp;
		<br><br>
		<div id="httpcheck" style="display:<?php echo $displayhttp; ?>">
		<p>
                HTTP IP address&nbsp;
                <select size="1" name="txthttpipfeild" id="txthttpipfeild" <?php echo $disabled; ?>>
                        <?php
                        if ($row["MONITOR_HTTP_IP_FIELD"] != ""){
                                $httpipfeild=$row["MONITOR_HTTP_IP_FIELD"];
                        }
                        else{ $httpipfeild = SITE_IP_DEFAULT ;}
                        ?>
                        <option value="<?php echo $httpipfeild; ?>" SELECTED><?php echo  $row[$httpipfeild];?></option>
                        <option value="LAN_IP"><?php echo $row["LAN_IP"] ?></option>
                        <option value="LAN_GATEWAY"><?php echo $row["LAN_GATEWAY"] ?></option>
                        <option value="WAN_IP"><?php echo $row["WAN_IP"] ?></option>
                        <option value="WAN_GATEWAY"><?php echo $row["WAN_GATEWAY"] ?></option>
                </select>
                <?php
echo '<input type="hidden" id="httprealipfeild" value="'.$row[trim($httpipfeild)].'">';
                        //echo $row[trim($httpipfeild)];
                ?>

		&nbsp;&nbsp;&nbsp;
		Port &nbsp;
		<?php if ($row['MONITOR_HTTP_PORT'] == ""){$hhtport="80";}else{$hhtport=$row['MONITOR_HTTP_PORT'];} ?> 
		<input type="text" name="txtmonhttpport" id="txtmonhttpport" size="3"  
			value = "<?php echo $hhtport ?>" <?php echo $disabled; ?>>
		<a href="javascript:openhelp('help/httpport.html')">?</a>
                Timeout(ms) &nbsp;
		<select size="1" name="txtmonhttptimeout" id="txtmonhttptimeout" <?php echo $disabled; ?>>
		<?php
		if ($row["MONITOR_HTTP_TIMEOUT"] == ""){$httptimeout="1000";}else{$httptimeout=$row["MONITOR_HTTP_TIMEOUT"];}
		?>
		        <option value="<?php echo  $httptimeout; ?>" SELECTED><?php echo  $httptimeout;?></option>
                        <option value="1000">1000</option>
                        <option value="2000">2000</option>
                        <option value="3000">3000</option>
                        <option value="4000">4000</option>
                        <option value="5000">5000</option>
                        <option value="6000">6000</option>
                        <option value="7000">7000</option>
                        <option value="8000">8000</option>
                        <option value="9000">9000</option>

		</select>

                <a href="javascript:openhelp('help/httpipport.html')">?</a>

		<br>
		Web check Page &nbsp; 
		<input type="text" name="txtmonhttppage" id="txtmonhttppage"size="40"  value = "<?php echo $row['MONITOR_HTTP_PAGE'] ?>" <?php echo $disabled; ?>>
                <a href="javascript:openhelp('help/httppage.html')">?</a>		
		<br>
		Page Content check text &nbsp;&nbsp;
		<input type="text" name="txtmonhttpcontent" id="txtmonhttpcontent"size="40"  
			value = "<?php echo $row['MONITOR_HTTP_CONTENT'] ?>" <?php echo $disabled; ?>>
                <a href="javascript:openhelp('help/httpcontent.html')">?</a>
		<br>
<?php if ($row['MONITOR_HTTP_SSL'] == 1){$checked = "CHECKED";}else {$checked = "";} ?>
Use SSL <input type="checkbox" name="chkssl" id="chkssl" value="ON"<?php echo $checked; ?> <?php echo $disabled; ?>>&nbsp;
<br>
		<input class="button" type="button" value="Test page checking"  onclick="checkHTTP();" <?php echo $disabled; ?>>
		</p>
		</div>
		</fieldset>
		<br>
		<hr>
		<fieldset style="padding: 2">
			<legend>Alerting</legend>

			Send Support 
			<font color="#FF0000"><b>offline</b></font> 
			Alert Email&nbsp;
        		<?php if ($row['SUPPORT_ALERT_OFFLINE'] == 1){$checked = "CHECKED";}else {$checked = "";} ?>
			<input type="checkbox" name="chkalertoffline" id="chkalertoffline" value="ON" <?php echo $checked; ?> <?php echo $disabled; ?>>&nbsp; 
			<a href="javascript:openhelp('help/supportalert.html')">?</a><br>

			Send Support <b>
			<font color="#008000">online</font></b>&nbsp; Alert Email
			<?php if ($row['SUPPORT_ALERT_ONLINE'] == 1){$checked = "CHECKED";}else {$checked = "";} ?>
			<input type="checkbox" name="chkalertonline" id="chkalertonline" value="ON" <?php echo $checked; ?> <?php echo $disabled; ?>>&nbsp;
			<a href="javascript:openhelp('help/supportalert.html')">?</a><br><br>

			Send VIP&nbsp; <b>
			<font color="#FF0000">offline</font></b> Alert&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php if ($row['VIP_ALERT_OFFLINE'] == 1){$checked = "CHECKED";}else {$checked = "";} ?>
			<input type="checkbox" name="chkvipoffline"  id="chkvipoffline" value="ON" <?php echo $checked; ?> <?php echo $disabled; ?>>
			<a href="javascript:openhelp('help/vipalert.html')">?</a><br>

			Send VIP&nbsp; <b>
			<font color="#008000">online</font></b> Alert&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php if ($row['VIP_ALERT_ONLINE'] == 1){$checked = "CHECKED";}else {$checked = "";} ?>
			<input type="checkbox" name="chkviponline"  id="chkviponline" value="ON" <?php echo $checked; ?> <?php echo $disabled; ?>>
			<a href="javascript:openhelp('help/vipalert.html')">?</a><br>
<!--
        VIP Email <input type="text" name="txtvipemail" id="txtvipemail" size="20">&nbsp;
        Name<input type="text" name="txtvipename" id="txtvipename" size="20" >&nbsp;<br>
-->
			<?php 
			$sql = "SELECT * FROM ALERTEMAILS WHERE LOCATION = '".$site."' ";
        		$result2 = mysqli_query($conn,$sql);
        		$row2 = mysqli_fetch_assoc($result2);
        //while ($row2 = @mysqli_fetch_assoc($result2))
        //{
	//	echo $row2['EMAIL']."  " .$row2['NAME'] . "<br>";
	//}
$email = isset($row2['EMAIL']) ? $row2['EMAIL'] : '';
			?>
        		VIP Email <input type="text" name="txtvipemail" id="txtvipemail" size="70" value = "<?php echo $email; ?>" <?php echo $disabled; ?>>&nbsp;
			<br>
			<h3>Enter Multiple addresses separated by a Semicolon <br>
			ie: first@somewhere.com ; second@nowhere.com </h3><br>
	<p><input class="button" type="button" value="Send Test email"  onclick="window.open('site-alert-email-test.php?email=' + document.getElementById('txtvipemail').value + '&site=<?php echo $site . "'"; ?>,'','width=200,height=100,resizable=yes,scrollbars=yes,status=yes'); return false" <?php echo $disabled; ?>></p>
<!--        Name<input type="text" name="txtvipename" id="txtvipename" size="20" >&nbsp;<br> -->

	</fieldset>
	
	<font color="#FF0000">
	***********************************************************************<br>
	</font>
	
	
	Flag Site as Alert Sent
	<?php if ($row['ALERT_SENT'] == 1){$checked = "CHECKED";}else {$checked = "";} ?>
	<input type="checkbox" name="chkalertsent" id="txtalertsent" value="ON" <?php echo $checked; ?> <?php echo $disabled; ?>><br>
        Selecting: 
        <font color="#FF0000">
        will cause no alerts to be sent for this site
        until the monitor detects Site Online<br>
        </font>
        Deslecting: 
        <font color="#FF0000">
        will cause Alets to be resent if site is OffLine<br>
        </font>

	<font color="#FF0000">
	***********************************************************************</font><br>
</div>
	<input class="button" type="submit" value="Submit" name="B1" onclick="return save_it();">
	<input class="button" type="button" value="Cancel" name="B3" onclick="javascript:window.close()"></form>
<script type="text/javascript">
	sizeToFit("show_div");
	window.focus();
	
</script>
</body></html>


