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

include_once("auth.php");
include_once("site-monitor.conf.php");
include('write_access_log.php');
include_once('lmz-functions.php');
$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>

<?php
//	+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//	|	User Access code	|
// =====================================================================================================//
//$acl=$_SESSION['accesstype'];										//
if ($_SESSION['accesslevel'] == 0){											//
	echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';			//
	echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';			//
	}												//
// =====================================================================================================//
?>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css" id="css">
    <link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon" >
    <link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon" >

<title>Site Info</title>
<script type="text/javascript" src="ts_picker.js"></script>
<script type="text/javascript"  src="table_roll_over.js"> </script>
<script type="text/javascript">
function show_image(url)
{
	var features;
	features='width=' + (screen.availWidth -30) + ',height=' + (screen.availHeight - 30) + ',scrollbars=yes' + ',left=0,top=0,resizable=yes'
	window.open(url,"",features);
	return false;
}
function show_group()
{
	var pingstring = "";
	if (document.getElementById('ping1').checked == true) {pingstring = "&Ping=ON"}
	window.location = "support.php?group=" + document.getElementById('txtgroup').value + pingstring ;
}
function ping_store(ip)
{
        window.open("ping.php?tamsip=" + ip,"","width=450,height=400");
}
function openhelp(url)
{
        window.open( url, "","resizable=1,height=300,width=300");
}

function verifyIP (IPvalue) {
	var ipPattern = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
	var ipArray = IPvalue.match(ipPattern);
	if (IPvalue == "0.0.0.0"){
		return false ;
	}else if (IPvalue == "255.255.255.255"){
		return false ;
	}
	if (ipArray == null){
		return false ;
	}else {
		for (i = 0; i < 4; i++) {
			thisSegment = ipArray[i];
			if (thisSegment > 255) {
				return false ;
				i = 4;
			}
		}
	}
	if ((i == 0) && (thisSegment > 255)) {
		return false ;
		i = 4;
 	}

	extensionLength = 3;
		return true;
}
var cnt = 0;
var xmlHttp = new Array();
var active = new Array();
function ajaxFunction(name,ip){
        if (!verifyIP(ip)){return ;}
        var idx;
        if (name == "lanipimg"){
                idx=0;
        }else if (name == "gatewayipimg"){
                idx=1;
        }else if (name == "wanipimg") {
                idx=2;;
        }else if (name == "wangatewayipimg") {
                idx=3;;
        }
        //Aborts to keep it running in loop
	xmlHttp[idx] = "";
        try{
                // Firefox, Opera 8.0+, Safari
                xmlHttp[idx]=new XMLHttpRequest();
        }
        catch (e){
                // Internet Explorer
                try{
                        xmlHttp[idx]=new ActiveXObject("Msxml2.XMLHTTP");
                }
                        catch (e){
                                try{
                                        xmlHttp[idx]=new ActiveXObject("Microsoft.XMLHTTP");
                                }
                                        catch (e){
                                                alert("Your browser does not support AJAX!");
                                                return false;
                                        }
                        }
        }

        xmlHttp[idx].onreadystatechange=function(){
                if(xmlHttp[idx].readyState==4){
			if (active[idx]== true){a='&nbsp;<span style="font-size:6pt;font-weight:bold;color:FF00FF">ON<\/span>';}else{a="";}
                        if (xmlHttp[idx].responseText != "-1"){
                                document.getElementById(name).src="img/online.gif";
                                document.getElementById(name+'x').innerHTML = "&nbsp;&nbsp;("+xmlHttp[idx].responseText+"ms)"+a;
                        }else{
                                document.getElementById(name).src="img/down.gif";
                                document.getElementById(name+'x').innerHTML = a;
                        }
			
                }
      }
        // had to add some dynamic info to the URL to keep IE from using cached info... IE suxs
        // t="+((new Date()).valueOf()) causes a random url and IE get new not cached page
        xmlHttp[idx].open("GET","ping-live.php?ip="+ip+"&t="+((new Date()).valueOf()),true);
        xmlHttp[idx].send(null);
	if (active[idx]== true){
        	setTimeout("ajaxFunction('"+name+"','"+ip+"')",5000);
	}
}
// =============================================================//
//      this is all part of the open_message function below     //
//==============================================================//
var message_window = [];

Array.prototype.has = function(value) {
    var i;
    for (var i in this) {
        if (i === value) {
                return true;
        }
    }
    return false;
};
function open_message(name,url,width,height,resizable,scrollbars){                                                                  
        var winRef;                                                                                                                 
        // Handle width, heith, resizable and scrollbarsas optional parms
        // If they are not passed the will default without error
        width = (typeof width === "undefined") ? "575" : width;
        height = (typeof height === "undefined") ? "775" : height;
        resizable = (typeof resizable === "undefined") ? "yes" : resizable;
        scrollbars = (typeof scrollbars === "undefined") ? "yes" : scrollbars;
        if (message_window.has(name)) {                                                                                             
                winRef = message_window[name];                                                                                      
        }                                                                                                                           
                                                                                                                                    
        if (winRef == null || winRef.closed                                                                                         
){                                                                                                                                  
                message_window[name] = window.open(url,'','width='+width+',height='+height+',resizable='+resizable+',scrollbars='+scrollbars);                                                                                                                          
        }else{                                                                                                                      
                winRef.focus();                                                                                                     
        }                                                                                                                           
        return false;                                                                                                               
}              
// =============================================================//
</script>
</head>
<body >
<script language="JavaScript1.2" type="text/javascript" src="menulz.js"> </script>
<script type="text/javascript" src="clipboardCopy.js"> </script>
<?php
add_plugin('support_load',$row["SITE_ID"]);
// Adds menu items to side menu
 if ($_SESSION['accesslevel'] >= 9){
        echo '<script language="JavaScript1.2" type="text/javascript"  src="menu-data-rwa.js"> </script>';
 }elseif($_SESSION['accesslevel'] >= 4){
        echo '<script language="JavaScript1.2" type="text/javascript"  src="menu-data-rw.js"> </script>';
}else{
        echo '<script language="JavaScript1.2" type="text/javascript"  src="menu-data-ro.js"> </script>';
}
/****************************************************************
		Get the search info
***************************************************************/
// first see if the search came from the URL
$sitequery=trim($_GET['site']);	
if ($sitequery == ""){
	// Search did not come from a URL.... 
	$sitequery=trim($_POST['site']);
	
	if ($sitequery != ""){
		// Not an empty string
		$SQL="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) ";
		$SQL .= "WHERE SITE_ID like '%".$sitequery . "%'"; $SQL=$SQL . " OR GROUP_NAME LIKE '%".$sitequery . "%'";
		$SQL .= " OR LAN_IP LIKE  '%".$sitequery . "%'";
		$SQL .= " OR CITY LIKE '%".$sitequery . "%'" ;
	}
}
else{
	$SQL="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE SITE_ID like '".$sitequery ."'" ; 
}
if ($sitequery == "")
{
	$groupquery = $_POST['group'];
	if ($groupquery == "")
	{
        	$groupquery=$_GET['group'];

	}
	if ($groupquery != "")$SQL="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE GROUP_NAME = '".$groupquery . "'" ;
}
$PING=$_REQUEST["Ping"];
//if ($PING == "") $PING=$_POST["Ping"];
$result=mysqli_query($conn,$SQL);
$row = @mysqli_fetch_assoc($result);
$num= @mysqli_num_rows($result);
//echo $num ."<br>";
$helpTag="<img style=\"border:none\" src=\"img/help2.png\" alt=\"help2.png\">";
if ($num < 2)
{
?>
<!-- Set a javascript Variable with the store number for use with Plugins -->
<script type="text/javascript">site="<?php echo $row["SITE_ID"]; ?>" </script>
        <script type="text/javascript"> document.title = site; </script>
<table border="1" width="100%" id="table1">
	<tr>
		<td rowspan="12" style="text-align: center" valign="top" width="205">
			<a target="_self" href="main.php" style="outline:none">
			<img border="0" 
				src="netz.jpg" 
				width="164" 
				height="49" 
				align="middle" 
				alt="netz">
			</a>
			<br>
			<br>
			<form name="myform2" method="POST" action="support.php">
				<p>
				<input type="text" name="site" id="site" size="20">
				<br><br>
				<input class="button" type="submit" value="Site Search" name="B14">
				&nbsp;
				<a title="Help" 
					style="outline:none"
					href="javascript:openhelp('help/supportsearch.html')">
				<?php echo $helpTag ; ?></a>
				</p>
			</form>
		<br>
		<?php 
        	// ***************************************************************************************************************
        	// If there is no store selected....just show search box                                        		//
		if (trim($row["SITE_ID"]) == "" || !isset($row["SITE_ID"])){
			echo "</td></tr></table><script type=\"text/javascript\">document.myform2.site.focus();</script></body></html>"; 
			exit; 
		}	//
		//****************************************************************************************************************
		?>
		<span style="font-weight:bold" id="pinger"></span><br>
		<?php 
                // Themes Link
                echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"@\" ";
                echo "onclick=\"window.open('user-theme.php?user=";
                echo $_SESSION['user']."','','width=325,height=30,";
                echo "resizable=yes,scrollbars=yes,status=yes'); ";
                echo "return false\">Themes</a><br>";
		// Monitor options link
		if ((trim($row["SITE_ID"]) != "" || isset($row["SITE_ID"])) && $_SESSION['accesslevel'] >= 5){
			// Monitor/Alert Options link start completed with $options below
			echo "<span id=\"mon_link\">";
			echo '<a href="@" onclick="window.open(\'site-config.php?site='.$row["SITE_ID"].'\',\'\',\'';

			// quick hack for changing the popup window size
			if (strpos($_SESSION['style'], "small")){
				$options = 'width=525,height=700,resizable=yes,scrollbars=yes,status=yes';
			}elseif (strpos($_SESSION['style'], "large")){
				$options = 'width=650,height=775,resizable=yes,scrollbars=yes,status=yes';
			}else{
				$options = 'width=575,height=725,resizable=yes,scrollbars=yes,status=yes';
			}

			$options .= '\'); b=document.getElementsByTagName(\'body\')[0];';
			// for Firefox using CSS standards
			$options .= ' b.style.opacity = \'.5\';';
			// Internet Exploder Suxs.... MS always going against the grain
			$options .= 'b.style.filter = \'alpha(opacity=50)\'; return false;">';
			$options .= 'Monitor/Alert Options</a></span>';
			// Finish the link
			echo $options;
		}
		echo "<br><br>";
                        
		// Monitor log link
		if ($row["MONITOR_ENABLE"] == 1){ ?>
		<a href="#" 
		onclick="window.open('site-stats.php?site=<?php print $row["SITE_ID"];?>&amp;days=10&amp;daysinclude=11&amp;detail=1','','width=640,height=480,resizable=yes,scrollbars=yes,status=yes'); return false" >
		Monitor Logs
                        </a><br>
		<a href="site-charts.php?site=<?php print $row["SITE_ID"];?>&amp;group=<?php print $row["GROUP_NAME"];?>&amp;return=support.php">Monitor Charts</a><br><br>
			<?php } ?>
			<?php
			if ($row["SITE_ID"] != ""){
				if ($row["MONITOR_ENABLE"] == 1){
					echo "Monitor<b> <font color='#339900'>ENABLED&nbsp;&nbsp;</font></b>";
 				}
 				else{
					echo "Monitor <b><font color='red'>Disabled&nbsp;&nbsp;</font></b>";
 				}
				if ($row["MONITOR_ENABLE"] == 1){
					echo "<br>Site 5 day availability %<br>";
					echo '<iframe width="100%" FRAMEBORDER="1" ';
					echo 'src="site-stats-graph.php?site=';
					echo $row["SITE_ID"].'&amp;days=4" SCROLLING=NO></iframe>';
					echo '<a title="Show 2 week Chart" ';
					echo 'href="@" onclick="window.open(\'site-stats-graph.php?site='.$row["SITE_ID"];
					echo '&amp;days=14 \',\'\',\'width=600,height=250,';
					echo 'resizable=yes,scrollbars=yes,status=yes\');';
					echo 'return false">2 Week Chart </a><br>';
					add_plugin('support_left',$row["SITE_ID"]);
				}
			}
			?>

		</td>
        </tr>
        <tr>
		<td>
    			<?php echo Display_name('SITE_ID');?> &nbsp;
			<span class="inputhidden" id="site2" ><?php   print $row["SITE_ID"];?></span>
		</td>
		<td>
			<?php echo Display_name('GROUP_NAME');?> &nbsp;
			<span class="inputhidden"><?php   echo $row['GROUP_NAME'] ?></span>
			<input type="hidden" id="txtgroup" name="txtgroup" value="<?php   echo $row['GROUP_NAME'] ?>">
			<input class="button" type="button" value="Show" name="B13" 
			onclick="show_group()" 
			style="color: #000080; font-weight: bold">
			&nbsp; Ping 
			<input type="checkbox" name="ping1" id="ping1" value="ON" >
                        <!-- Group Ping Help -->
			 &nbsp;<a title="Group Help" 
					style="outline:none"
					href="javascript:openhelp('help/groups.html')">
					<?php echo $helpTag ; ?>
				</a>
		</td>
		<td>
			<?php echo Display_name('TIME_ZONE');?> &nbsp;
			<span class="inputhidden"><?php   print $row["TIME_ZONE"];?></span>
		</td>
	</tr>
	<tr>
		<td>
			<!-- LAN IP  -->
			<STRONG><?php echo Display_name('LAN_IP');?>&nbsp; </STRONG>
			<!-- IP Text box -->
			<span class="inputhidden"><?php print $row["LAN_IP"];?></span>
                        <!-- Ping Image -->
			<img	id="lanipimg" 
				src="img/transparentpixel.gif" 
				width="12" 
				height="12" 
				alt="trans" 
				title="Click For Active Ping"
			onclick="javascript:active[0] = !(active[0]);ajaxFunction('lanipimg','<?php echo $row['LAN_IP']?>');" 
			>
			<!-- Ping Time -->
			<span id="lanipimgx"></span><br>
			<!-- Ping code  -->
			<script type="text/javascript" > 
				ajaxFunction('lanipimg','<?php echo $row['LAN_IP']?>'); 
			</script>
			<!-- Ping Button  -->
			<input class="button" type="button" value="P" title="Ping" 
			onclick="ping_store('<?php print $row['LAN_IP'];?>')">
			<!-- Telnet Button  -->
			<input class="button" type="button" value="T" title="Telnet" 
			onclick="window.location='telnet:<?php print $row['LAN_IP'];?>'">
                        <!-- SSH button -->
                         <input class="button"  type="button" value="S" title="SSH:"
                         onclick="window.location='ssh://<?php print $row['LAN_IP'];?>'">
			<!-- Web Button  -->
			<input class="button" type="button" value="W" title="Web Connect" 
			onclick="window.open('http://<?php print $row['LAN_IP'];?>' ,'','')" >
                   	<!-- LAN IP Help -->
                        &nbsp;<a title="Help"  
					href="javascript:openhelp('help/lanips.html')" 
					style="outline:none">
					<?php echo $helpTag ; ?>
				</a>
		</td>
		<td>
			<!-- Gateway IP  -->
			<strong><?php echo Display_name('LAN_GATEWAY');?>&nbsp;</strong>
			<!--  Text box -->
			<span class="inputhidden"><?php print $row["LAN_GATEWAY"];?></span>
                        <!-- Ping Image -->
                        <img	id="gatewayipimg" 
				src="img/transparentpixel.gif" 
				width="12" 
				height="12" 
				alt="trans" 
				title="Click For Active Ping"
			onclick="javascript:active[1]=!(active[1]);ajaxFunction('gatewayipimg','<?php echo $row['LAN_GATEWAY']?>');" 
			>
                        <!-- Ping Time -->
                        <span id="gatewayipimgx"></span><br>
                        <!-- Ping code  -->
                        <script type="text/javascript" >
                                ajaxFunction('gatewayipimg','<?php echo $row['LAN_GATEWAY']?>');
                        </script>

			<!--  Ping Button -->
			<input class="button" type="button" value="P" title="Ping" 
			onclick="ping_store('<?php print $row['LAN_GATEWAY'];?>')">
			<!--  Telnet Button -->
			<input class="button" type="button" value="T" title="Telnet" 
			onclick="window.location='telnet:<?php print $row['LAN_GATEWAY'];?>'">
                        <!-- SSH button -->
                         <input class="button"  type="button" value="S" title="SSH:"
                         onclick="window.location='ssh://<?php print $row['LAN_GATEWAY'];?>'">
			<!--  Web Button -->
			<input class="button" type="button" value="W" title="Web Connect" 
			onclick="window.open('http://<?php print $row['LAN_GATEWAY'];?>' ,'','')" >
                        <!-- LAN Gateway Help -->
                        &nbsp;<a title="Help"  
					style="outline:none"
					href="javascript:openhelp('help/lanips.html')">
					<?php echo $helpTag ; ?>
				</a>
		</td>
		<td>
			<?php echo Display_name('FIELD_REP');?> &nbsp;
			<input class="inputhidden" type="text" name="txtfieldrep" id="txtfieldrep" size="15"   
			value="<?php print $row["FIELD_REP"];?>"
			onkeydown="javascript:this.blur()" > 
		</td>
	</tr>
	<tr>
		<td>	
                        <STRONG><?php echo Display_name('WAN_IP');?>&nbsp;</STRONG>
			
                        <!-- WAN IP  -->
			<span class="inputhidden"><?php print $row["WAN_IP"];?></span>
			<!-- Ping Image -->
                        <img	id="wanipimg" 
				src="img/transparentpixel.gif" 
				width="12" 
				alt="trans" 
				height="12" 
				title="Click For Active Ping"
			onclick="javascript:active[2]=!(active[2]);ajaxFunction('wanipimg','<?php echo $row['WAN_IP']?>');" 
			>
			<!-- Ping Time -->
			<span id='wanipimgx'></span>
                        <!-- Ping code  -->
                        <script type="text/javascript" >
                                ajaxFunction('wanipimg','<?php echo $row['WAN_IP']?>');
                        </script>
			
		</td>
		<td>
                        <STRONG><?php echo Display_name('WAN_GATEWAY');?>&nbsp;</STRONG>
			<span class="inputhidden"><?php print $row["WAN_GATEWAY"];?></span>
                        <!-- Ping Image -->
                        <img	id="wangatewayipimg" 
				src="img/transparentpixel.gif" 
				width="12" 
				height="12" 
				alt="trans" 
				title="Click For Active Ping"
 		onclick="javascript:active[3]=!(active[3]);ajaxFunction('wangatewayipimg','<?php echo $row['WAN_GATEWAY']?>');" 
			>
                        <!-- Ping Time -->
                        <span id='wangatewayipimgx'></span>
                        <!-- Ping code  -->
                        <script type="text/javascript" >
                                ajaxFunction('wangatewayipimg','<?php echo $row['WAN_GATEWAY']?>');
                        </script>

		</td>
		<td>
			<?php echo Display_name('WAN_NETMASK');?> &nbsp;
			<span class="inputhidden"><?php print $row["WAN_NETMASK"];?></span>
    		</td>
	</tr>
	<tr>
		<td>
			<?php echo Display_name('SITE_NAME');?> &nbsp;
			<span class="inputhidden"><?php print $row["SITE_NAME"];?></span>
		</td>
		<td>
			<?php echo Display_name('REGION');?> &nbsp;
			<span class="inputhidden"><?php   print $row["REGION"];?></span>
		</td>
		<td>
			<?php echo Display_name('SUPPORT_CENTER');?> &nbsp; 
			<span class="inputhidden"><?php   print $row["SUPPORT_CENTER"];?></span>
				<a	title="Support Center Info" 
					style="outline:none"
                                        href="javascript:openhelp('support_info.php?support=<?php echo $row["SUPPORT_CENTER"];?>')">
                                        <img 	style="border:none" 
						src="img/info-icon.png" 
						alt="info-icon.png">
                                </a>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo Display_name('ADDRESS');?> &nbsp;
			<span class="inputhidden"><?php   print $row["ADDRESS"];?></span>
		</td>
		<td>
			<?php echo Display_name('CITY');?> &nbsp;
			<span class="inputhidden"><?php   print $row["CITY"];?></span>
    		</td>
		<td>
			&nbsp;<?php echo Display_name('ST');?> 
			<span class="inputhidden"><?php   print $row["ST"];?></span>
			&nbsp;<?php echo Display_name('ZIP');?> 
			<span class="inputhidden"><?php   print $row["ZIP"];?></span>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo Display_name('ORDER_DATE');?>  &nbsp; 
			<span class="inputhidden"><?php   print $row["ORDER_DATE"]?></span>
		</td>
		<td>
			<?php echo Display_name('ACTIVE_DATE');?> &nbsp;
			<span class="inputhidden"><?php   print $row["ACTIVE_DATE"]?></span>
		</td>
		<td>
			<?php echo Display_name('CLOSE_DATE');?> &nbsp;
			<span class="inputhidden"><?php   print $row["CLOSE_DATE"]?></span>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo Display_name('SERVICE_CODE');?> &nbsp;
			<span class="inputhidden"><?php print $row["SERVICE_CODE"];?></span>
    		</td>
		<td>		
			<?php echo Display_name('SERVICE_TYPE');?> &nbsp;
			<span class="inputhidden"><?php print $row["SERVICE_TYPE"];?></span>
    		</td>
		<td>
			<?php add_plugin('support_middle_1',$row["SITE_ID"]); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>
			<?php echo Display_name('INET_PROVIDER');?> &nbsp;
			<span class="inputhidden"><?php print $row["INET_PROVIDER"];?></span>
		</td>
		<td>
			<?php echo Display_name('INET_PROVIDER_SUPPORT_NUMBER');?> &nbsp;
			<span class="inputhidden"><?php print $row["INET_PROVIDER_SUPPORT_NUMBER"];?></span>
		</td>
		<td>
                        <?php echo Display_name('DSL_LINE_NUMBER');?> &nbsp;
			<span class="inputhidden"><?php print $row["DSL_LINE_NUMBER"];?></span>
	
		</td>
	</tr>
        <tr>
                <td>
                        <?php echo Display_name('GROUP_CONTACT');?>  &nbsp;
			<span class="inputhidden"><?php print $row["GROUP_CONTACT"];?></span>
                </td>
                <td>
                        <?php echo Display_name('GROUP_CONTACT_PHONE');?> &nbsp;
			<span class="inputhidden"><?php print $row["GROUP_CONTACT_PHONE"];?></span>
                </td>
                <td>
                         <?php echo Display_name('SITE_PHONE_NUMBER');?> &nbsp;
			<span class="inputhidden"><?php print $row["SITE_PHONE_NUMBER"];?></span>

                </td>
        </tr>
        <tr>
                <td>
                        <?php echo Display_name('ROUTER_MODEL');?> &nbsp;
			<span class="inputhidden"><?php print $row["ROUTER_MODEL"];?></span>
                </td>
                <td>
                        <?php echo Display_name('CPE_MODEM_MODEL');?> &nbsp;
			<span class="inputhidden"><?php print $row["CPE_MODEM_MODEL"];?></span>
                </td>
                <td>
			<?php add_plugin('support_middle_2',$row["SITE_ID"]); ?>
                         &nbsp;

                </td>
        </tr>
        <tr>
                <td>
                        <?php echo Display_name('T1_CIRCUIT');?> &nbsp;
			<span class="inputhidden"><?php print $row["T1_CIRCUIT"];?></span>
                </td>
                <td>
                        <?php echo Display_name('LEC_CIRCUIT');?> &nbsp;
			<span class="inputhidden"><?php print $row["LEC_CIRCUIT"];?></span>
                </td>
                <td>
                        <?php echo Display_name('DLCI_ID');?> &nbsp;
			<span class="inputhidden"><?php print $row["DLCI_ID"];?></span>
                </td>
        </tr>

	<tr>
			<?php
                                $IMAGEMAP=$row["SITE_IMAGE_MAP"];
                                // If there is no Image in DB don't show a link
                                if (!isset($IMAGEMAP) || $IMAGEMAP==""){
                                        echo '<td> ';
                                }else{
                                        echo '<td> ';
                                        echo '<a class="alinklightgreen" href="a.htm" ';
                                        echo 'onclick="return show_image(\'image_pop.php?image=';
					echo $IMAGEMAP . '\')">Site Network Image </a><br>';
                                }
                                $IMAGEMAP2=$row["GROUP_IMAGE_MAP"];
                                // If there is no Image in DB don't show a link
                                if (!isset($IMAGEMAP2) || trim($IMAGEMAP2) ==""){
                                        echo '&nbsp;</td>';
                                }else{
                                        echo '<a class="alinklightgreen" href="a.htm" ';
                                        echo 'onclick="return show_image(\'image_pop.php?image=' ;
                                        echo  $uploadDir .$IMAGEMAP2 . '\')">Group Network Image';
                                        echo '</a>';
					echo '</td>';
                                }
			?>		
		<td><textarea onkeydown="this.blur();" rows="20" cols="50" id="note" name="note" style="width:350;height:100"><?php print $row["NOTES_1"];?></textarea></td>
		<td>
 			<iframe height="50px" width="350px" name="myframe"
			frameborder="0" scrolling="no" src="sites-down.php"> </iframe>
		</td>
	</tr>
</table>



<?php 
                if (trim($alert_message) != ""){
                        echo '<marquee id="alertit" ';
                        echo 'style="font-size:14; font-weight:bold; color:#FF0000; background-color:cccccc" ';
                        echo 'scrolldelay="100" scrollamount="5"  behavior="scroll" loop=-1 ';
                        echo "onmouseover=\"javascript:this.stop()\" ";
                        echo "onmouseout=\"javascript:this.start()\">". $alert_message ."</marquee>";
                }
?>
        <?php
/*
        if ($row["SITE_ID"] != "" && $row["MONITOR_ENABLE"] == 1){
		echo '<img src="build-chart.php?site='.$row["SITE_ID"].'&amp;back=3&amp;days=3&amp;size=small" alt="Build-chart">';
	}
	*/
if ( $row["SITE_ID"] != "" && $row["MONITOR_ENABLE"] == 1){
echo '<a href="@" style="outline:none" onclick="window.open(\'chart-pop.php?back=3&amp;site='.$row["SITE_ID"].'\'';
echo ',\'\',\'width=640,height=480,resizable=yes\'); return false">';
echo '<img src="build-chart.php?site='.$row["SITE_ID"].'&amp;back=3&amp;days=3&amp;size=small" alt="Build-chart">';
                                echo '</a>';

                                }
	?>
 


<?php
echo '<br><img
        src="img/valid-html401-blue.png"
        alt="Valid HTML 4.01 Transitional" height="23" width="66" title="Tested as Valid HTML 4.01 Transitional">
    <img style="border:0;width:66px;height:23px"
        src="img/vcss-blue" title="Tested Valid CSS"
        alt="Valid CSS!" />
<img style="border:0;width:80px;height:15px"
        src="img/php5-power-micro.png"
        alt="php powered" />';

add_plugin('support_footer',$row["SITE_ID"]); 
 include('netzfooter.php'); ?>
</body>
</html>
<?php
}   
else  //We found more than one record from query
{
	$COUNTERSTR=mysqli_num_rows($result);
echo "<center>".$COUNTERSTR."</center>";
echo '<table id="site_list" border="1" class="tablestyl"><tr><td>Site</td><td>CITY</td><td>Group</td><td>Site Type</td><td>Service Type</td><td>IP ADDRESS</td><td>Order Date</td><td>Active Date</td></tr>';
	// Flush the output buffer
	ob_flush();
	$TEMPSTR="";
// mysqli_data_seek function needed to get back to beginning of result
mysqli_data_seek($result,0);
while ($row = mysqli_fetch_assoc($result))
    {
        if ($PING=="ON")
        {
			$IPALIVE=exec("sudo ". $basedir . "ping-test-web.php ".$row['LAN_IP']);
			//$IPALIVE=exec($basedir . "wrap.cgi " . $basedir ."ping-test-web.php ".$row['LAN_IP']);
        }
        else
        {
            $IPALIVE="";
        } 
if ($tdclass == "tdlight"){$tdclass = "tddark";}
else{ $tdclass = "tdlight";}
$tdtag = "<td class='".$tdclass."'>";

    	$TEMPSTR="<tr>".$tdtag."<A href=support.php?site=".$row['SITE_ID'].">".$row['SITE_ID']."</a> </td>";
        $TEMPSTR .= $tdtag.$row['CITY']."</td>";
        $TEMPSTR .= $tdtag.$row["GROUP_NAME"]."</td>";
        $TEMPSTR .= $tdtag.$row["SITE_TYPE"]."</td>";
        $TEMPSTR .= $tdtag.$row["SERVICE_TYPE"]."</td>";

    	if ($IPALIVE == "-1")
    	{
			$TEMPSTR .= $tdtag."<font color='red'>".$row["LAN_IP"]."</font></td>";
    	}
    	else
    	{
  			if ($IPALIVE != "")
  			{
           		$TEMPSTR .= $tdtag."<font color='#339900'>".$row["LAN_IP"]."</font></td>";
        	}
        	else
        	{ 
          		$TEMPSTR .= $tdtag.$row['LAN_IP']."</td>";
        	} 
        }
      		// handles empty date field
        	if ($row["ORDER_DATE"] == "" || $row["ORDER_DATE"] == null) { $vpnorderdate=""; }
        	else { $vpnorderdate=date('m/d/Y',strtotime($row["ORDER_DATE"])); }
        	$TEMPSTR .= $tdtag.$vpnorderdate."</td>";
      		// handles empty date field
        	if ($row["ACTIVE_DATE"] == "" || $row["ACTIVE_DATE"] == null) { $vpnactivedate=""; }
        	else { $vpnactivedate=date('m/d/Y',strtotime($row["ACTIVE_DATE"])); }

        	$TEMPSTR .= $tdtag.$vpnactivedate."</td>";
        	if ($ADVSELECTEDFIELD!="")
        	{
            	$TEMPSTR .= $tdtag.$row[$ADVSELECTEDFIELD]."</td>";
        	} 
        	$TEMPSTR .= "</tr>";
        	echo $TEMPSTR;
		// Flush the output buffer
		ob_flush();
        	$TEMPSTR="";

  } //END WHILE
  		echo '</table>';
		echo "<script type=\"text/javascript\"> \n";
		echo "addTableRolloverEffect('site_list','tableRollOverEffect1','tableRowClickEffect1');";
		echo "</script>\n";
		echo '</body></html>';

}	//END ELSE We found more than one record
?>
