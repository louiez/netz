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

//include("site-monitor.conf.php");
include_once('auth.php');
include('write_access_log.php');
include_once('lmz-functions.php');
require_once( 'class.ConfigMagik.php');
require_once( 'db.class.php');
$db_class = new DB_Class();
function get_config_file($filename){
        $filename = $basedir . $filename;
        $contentsreturn = "";
        if ($fp= @fopen($filename, "r")){
                $contents= @fread($fp,filesize($filename));
                fclose($fp);
                $file_lines= explode("\n",$contents);
                foreach ($file_lines as $line){
                        if (trim($line) != ""){
                                $firstfeild = explode(" ",$line);
                                $contentsreturn .=  '<input type="checkbox" name="';
                                $contentsreturn .= $firstfeild[0].'" value="'.$firstfeild[0].'">&nbsp;&nbsp;'.$line.'<br>';
                        }
                }
        }
echo $contentsreturn;

}
function get_config_file2($filename){
        $filename = $basedir . $filename;
        $contentsreturn = "";
        if ($fp= @fopen($filename, "r")){
                $contents= @fread($fp,filesize($filename));
                fclose($fp);
            $file_lines= explode("\n",$contents);
            sort($file_lines,SORT_STRING);
            foreach ($file_lines as $line){
               $contents_sorted .= $line;
            }  
      echo '<textarea rows="20" name="'.$filename.'" cols="40">'.$contents_sorted.'</textarea>';
/*
                $file_lines= explode("\n",$contents);
                foreach ($file_lines as $line){
                        if (trim($line) != ""){
                                $firstfeild = explode(" ",$line);
                                $contentsreturn .=  '<input type="checkbox" name="';
                                $contentsreturn .= $firstfeild[0].'" value="'.$firstfeild[0].'">&nbsp;&nbsp;'.$line.'<br>';
                        }
                }
        }
echo $contentsreturn;
*/
	}
}

function scandirr($dir){
	$plugins_dir=$basedir."plugins";
	$dh  = opendir($dir);
	while (false !== ($filename = readdir($dh))) {
    	$files[] = $filename;
	}

	//sort($files);
	return $files;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><head>

<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">

<?php
//      +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//      |       User Access code        |
// =====================================================================================================//
$acl=$_SESSION['accesstype'];                                                                           //
if ($_SESSION['accesslevel'] != 10){                                                                     //
        echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';       //
        echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';                        //
        }                                                                                               //
// =====================================================================================================//
if ($_POST['username'] != "" && 
	$_POST['password']!= "" && 
	  $_POST['database']!= "" && 
	    $_POST['apacheusername']!= "" && 
	      $_POST['server']!= ""){
                if (file_exists('site-monitor.conf.php')){
                        exec("cat site-monitor.conf.php | sed 's/netzvvv1/".$_POST['server']."/g' > site-monitor.conf.tmp");
                }else{
                        exec("cat site-monitor.conf.example.php | sed 's/netzvvv1/".$_POST['server']."/g' > site-monitor.conf.tmp");
                }
                exec("cat site-monitor.conf.tmp | sed 's/netzvvv3/".$_POST['username']."/g' > site-monitor.conf.tmp1");
                exec("cat site-monitor.conf.tmp1 | sed 's/netzvvv2/".$_POST['password']."/g' > site-monitor.conf.tmp");
                exec("cat site-monitor.conf.tmp | sed 's/netzvvv4/".$_POST['database']."/g' > site-monitor.conf.tmp1");
                 exec("cat site-monitor.conf.tmp1 > site-monitor.conf.php");

        }
if (file_exists('site-monitor.conf.php')) {
//echo "config yup";
        include_once('site-monitor.conf.php');
}
?>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8">
        <TITLE>NETz Config</TITLE>
        <meta http-equiv="Content-Language" content="en-us">

        <?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
        <link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">
<link rel="stylesheet" href="tabber.css" TYPE="text/css" MEDIA="screen">


<title>NETz Config</title>
<STYLE TYPE="TEXT/CSS">
	.menu {
		display:none;
		width:100%;
		height:600px;
		border:solid 1px #ffffff;
		position:absolute;
		top:75;
		left:0;
		}
        </STYLE>
<script type="text/javascript">
function openhelp(url,width,height)
{
	if (!height){height="300";}
	if (!width){width="300";}
//        window.open( url, "","resizable=1,HEIGHT=250,WIDTH=300");
	window.open( url,"mywindow"," scrollbars=1, resizable=1,HEIGHT="+height+",WIDTH="+width);
}
function toggle_upload(){
	if (document.getElementById('allowuploads').checked == true){
		document.getElementById('allowuploads').value = "ON";
	}else{
		document.getElementById('allowuploads').value = "OFF";
	}
}
function toggle_smtp_auth(){
        if (document.getElementById('smtpauth').checked == true){
                document.getElementById('smtpauth').value = "true";
        }else{
                document.getElementById('smtpauth').value = "false";
        }
}
function add_box(){
        var td = document.getElementById('support_td');
	var anchor = document.getElementById('anchor');
	var br = document.createElement('br');

	// Create the support name text box
	var txt = document.createElement('input');
	txt.type = "text";
	txt.setAttribute('name','support_name[]');
	txt.setAttribute('size','25');

	// create the support email text box
        var txtemail = document.createElement('input');
        txtemail.type = "text";
        txtemail.setAttribute('name','support_email[]');	
	txtemail.setAttribute('size','75');

        // create the support phone text box
        var txtphone = document.createElement('input');
        txtphone.type = "text";
        txtphone.setAttribute('name','support_phone[]');        
        txtphone.setAttribute('size','25');

        // create the support fax text box
        var txtfax = document.createElement('input');
        txtfax.type = "text";
        txtfax.setAttribute('name','support_fax[]');        
        txtfax.setAttribute('size','25');


	// insert into the TD before the 'add' link
	td.insertBefore(txt,anchor);
	td.insertBefore(txtemail,anchor);
        td.insertBefore(txtphone,anchor);
        td.insertBefore(txtfax,anchor);
	td.insertBefore(br,anchor);

}
function foo(){
//	var x = document.getElementById('support_name');
var x = document.forms[0].elements['support_name[]']
	for(b in x){
	alert(x[b].nodeName);
	}
	return true;
}
function size_email_box(){
	var x = document.forms[0].elements['support_email[]']
	var spantext = document.createElement('span');
	spantext.setAttribute('id','temptext');
//	spantext.setAttribute('style','position:absolute; z-index:-1');
spantext.style.position = "absolute";
spantext.style.zIndex = "-1";
spantext.style.fontFamily = "Arial Black";
alert(document.body.style.fontFamily);
document.body.appendChild(spantext);
	for(b in x){
		if (x[b].nodeName == "INPUT"){
        		//alert(x[b].value);
			spantext.innerHTML = x[b].value;
			alert(spantext.clientWidth);
			x[b].style.width = spantext.clientWidth +"px"
		}
        }
	return true;
}
</script>

<script type="text/javascript" src="tabber.js">
</script>


</head>

<body>
<h1 class="center"> NETz Config </h1>
<form method="POST" action="write-netz-config.php" name="netzconfig" id="netzconfig">
<div class="tabber" id="mytab1">
<div class="tabbertab" title="Config">
<!-- <form method="POST" action="">-->
<table border="1" cellspacing="1" width="100%">
<!--		
	</table>

</div>
<div class="menu" id="idconfig" >
	<table border="1" cellspacing="1" width="100%">
-->
		<tr>
			<td>
				Base Directory
				<a href="javascript:openhelp('help/basedir.html')">&nbsp;&nbsp;(?)</a>
			</td>
			<td><input type="text" name="basedir" size="80"
			value="<?php if (isset($basedir)){ echo $basedir ;} ?>"></td>
		</tr>
                <tr>
                        <td>
                                Log Directory
                                <a href="javascript:openhelp('help/logdir.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td><input type="text" name="netzlogs" size="80"
                        value="<?php if (isset($netzlogs)){ echo $netzlogs ;} ?>"></td>
                </tr>

		<tr>
			<td>
				Allow Document uploads
				<a href="javascript:openhelp('help/allowuploads.html')">&nbsp;&nbsp;(?)</a>
			</td>
	<?php if (isset($allowuploads) && $allowuploads == "ON"){ $uploadcheck = "CHECKED" ;}else {$uploadcheck = "";} ?>
			<td>Enable <input 
				type="checkbox" 
				id="allowuploads" 
				name="allowuploads" 
				value="<?php echo $allowuploads ?>" <?php echo $uploadcheck ?>
				onclick="toggle_upload()"></td>
			
		</tr>
		<tr>
			<td>
				Upload Directory
				<a href="javascript:openhelp('help/uploaddir.html')">&nbsp;&nbsp;(?)</a>
			</td>
			<td><input type="text" name="uploaddir" size="80"
			value="<?php if (isset($uploadDir)){ echo $uploadDir ;} ?>"></td>
		</tr>
                <tr>
                        <td>Scrolling message</td>
                        <td><input type="text" name="txtalertmessage" id="txtalertmessage" size="100"  
                                value="<?php if (isset($alert_message)){ echo $alert_message ;} ?>">
                        </td>
                </tr>
		<tr><td colspan=2 style="font-size:large">&nbsp;&nbsp;Monitor</td></tr>
                <tr>
                        <td>Enable Monitor</td>
	<?php if (isset($enablemonitor) && $enablemonitor == "ON"){ $uploadcheck = "CHECKED" ;}else {$uploadcheck = "";} ?>
                        <td>Enable <input type="checkbox" name="enablemon" value="ON" <?php echo $uploadcheck; ?>></td>
                </tr>
                <tr>
                        <td>Enable Alerts</td>
        <?php if (isset($alert_enable) && $alert_enable == "ON"){ $uploadcheck = "CHECKED" ;}else {$uploadcheck = "";} ?>
                        <td>Enable <input type="checkbox" name="alertenable" value="ON" <?php echo $uploadcheck; ?>></td>
                </tr>
                <tr>
                        <td>
                                Monitor Cycle Interval
                                <a href="javascript:openhelp('help/monitorcycles.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td>  
			<select  size="1" name="cycleinterval" >
				<?php if (isset($moncycleinterval)){ 
					echo "<option value=\"".$moncycleinterval."\">".$moncycleinterval."</option> SELECTED" ;} ?>
 				<option value="5">5</option> 
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="35">35</option>
                                <option value="40">40</option>
                                <option value="45">45</option>
                                <option value="50">50</option>
                                <option value="55">55</option>
                                <option value="60">60</option>
			</select>&nbsp;&nbsp; (Min)
			</td>
                </tr>

		<tr>
			<td>
				Number of monitor Cycles missed before Alert sent (not used in Mass alert)
				<a href="javascript:openhelp('help/alertcycles.html')">&nbsp;&nbsp;(?)</a>
			</td>
			<td>
			<select  size="1" name="alertcycles" >

			<?php if (isset($alert_cycles)){
				 echo "<option value=\"". $alert_cycles."\">".$alert_cycles."</option> SELECTED" ;} ?>

			        <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
			</select>&nbsp;&nbsp; 
				</td>
		</tr>
                <tr>
                        <td>
                                Monitor online check IPs
                                <a href="javascript:openhelp('help/monitoronline.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td>
			<input type="text" name="monitoronline1" size="15"
                        value="<?php if (isset($mononline1)){ echo $mononline1 ;} ?>">
			<input type="text" name="monitoronline2" size="15"
                        value="<?php if (isset($mononline2)){ echo $mononline2 ;} ?>">
			<input type="text" name="monitoronline3" size="15"
                        value="<?php if (isset($mononline3)){ echo $mononline3 ;} ?>">

			</td>
                </tr>
                <tr>
                        <td>
                                Number of sites down in one cycle to trigger mass Alerts
                                <a href="javascript:openhelp('help/massalert.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td><input type="text" name="massalert" size="5"
                        value="<?php if (isset($mass_alert_threshold)){ echo $mass_alert_threshold ;} ?>"></td>
                </tr>
		<tr>
			<td>
				Days to keep Monitor Logs
				<a href="javascript:openhelp('help/daystokeeplogs.html')">&nbsp;&nbsp;(?)</a>
			</td>
			<td><input type="text" name="logdays" size="5"
			value="<?php if (isset($logdays)){ echo $logdays ;} ?>"></td>
		</tr>
                <tr>
                        <td>Admin Email address</td>
                        <td>
			<input type="text" name="adminemail" size="80"
                        value="<?php if (defined('SITE_ADMIN_EMAIL')){ echo SITE_ADMIN_EMAIL ;} ?>">
			</td>
                </tr>
                <tr>
<!--
                <tr>            
                        <td>Google Map Key</td>
                        <td><input type="text" name="txtgooglemapkey" id="txtgooglemapkey" size="100"  
                                value="<?php if (isset($google_map_key)){ echo $google_map_key ;} ?>">
                        </td>
                </tr>       
-->
<!-- </form>-->
</table>
</div>
<!--
	</table>
</div>
<div class="menu" id="idemail" >
        <table border="1" cellspacing="1" width="100%">
-->
<div class="tabbertab" title="Email Server">
<!-- <form method="POST" action=""> -->
<table border="1" cellspacing="1" width="100%">
		<tr>
			<td>
				Email server address
				<a href="javascript:openhelp('help/emailserver.html')">&nbsp;&nbsp;(?)</a>
			</td>
			<td><input type="text" name="emailserver" size="30"
			value="<?php if (isset($email_server)){ echo $email_server ;} ?>"></td>
		</tr>
                <tr>
                        <td>
                                Email server port
                                <a href="javascript:openhelp('help/emailserver.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td><input type="text" name="emailserverport" size="5"
                        value="<?php if (isset($email_server_port)){ echo $email_server_port ;} ?>"></td>
                </tr>
		<tr>
                        <td>
                                From Address
                                <a href="javascript:openhelp('help/smtpauth.html','600','300')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td><input type="text" name="from_address" size="30"
                        value="<?php if (isset($smtp_from_address)){ echo $smtp_from_address ;} ?>"></td>
                </tr>
                <tr>
                       <?php if (isset($smtp_auth) && $smtp_auth == "true"){ $smtpcheck = "CHECKED" ;} ?>
                        <td>Use SMTP AUTH
				<a href="javascript:openhelp('help/smtpauth.html','600','300')">&nbsp;&nbsp;(?)</a>
				</td>
				<td> <input 
                                type="checkbox" 
                                id="smtpauth" 
                                name="smtpauth" 
                                value="<?php echo $smtp_auth ?>" <?php echo $smtpcheck ?>
                                onclick="toggle_smtp_auth()"></td>
                </tr>
                <tr>
                        <td>
                                Use Secure connection
                                <a href="javascript:openhelp('help/snmp_secure.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td>
                        <select  size="1" name="smtp_secure" >
                                <?php 
				if (isset($smtp_secure) && $smtp_secure != ""){
					echo "<option value=\"".$smtp_secure."\">".strtoupper($smtp_secure)."</option> SELECTED" ;} 
				else{echo "<option value=\"\">None</option> SELECTED" ;}
				?>
				<option value="">None</option>
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                        </select>&nbsp;&nbsp;
                        </td>
                </tr>
                <tr>
                        <td>
                                SMTP Username
                                <a href="javascript:openhelp('help/smtpauth.html','600','300')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td><input type="text" name="smtpuser" size="30"
                        value="<?php if (isset($smtp_user)){ echo $smtp_user ;} ?>"></td>
                </tr>
                <tr>
                        <td>
                                SMTP password
                                <a href="javascript:openhelp('help/smtpauth.html','600','300')">&nbsp;&nbsp;(?)</a>
                        </td>
			<!-- Strange... if password box was empty in firefox..on linux.. it would fill
				the password box and the username box above with data
				the fix was to add  AUTOCOMPLETE="OFF".....  WTF? -->
                        <td><input type="password"  AUTOCOMPLETE="OFF" name="smtppass" size="20"
                        value="<?php if (isset($smtp_pass)){ echo $smtp_pass ;} ?>"></td>
                </tr> 
</table>
<!-- </form> -->
</div>
<!--
        </table>
</div>
<div class="menu" id="idoption" >
        <table border="1" cellspacing="1" width="100%">
-->
<div class="tabbertab" title="Support Centers">
<!-- <form method="POST" action=""> -->
<table border="1" cellspacing="1" width="100%">
		<tr>
			<td>Support centers</td>
			<td id="support_td">
			<!-- I used text boxes to get them to align and not rewrite Add_box code... lazy I guess -->
			<input class="inputhidden" type="text" size="25" value="Support Center">
			<input class="inputhidden" type="text" size="75" value="Email address">
			<input class="inputhidden" type="text" size="25" value="Phone Number">
			<input class="inputhidden" type="text" size="25" value="Fax Number">
			<br>
                                <?php
                                // Load Support Centers
                $conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
                //mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");
                $query="select * from ALERTEMAILS where TYPE = 'support' ORDER BY LOCATION;";
                $result=mysqli_query($conn,$query);
                $i =0;
                while ($row = mysqli_fetch_assoc($result)){
                        //$this->support_list[$i] = $row['LOCATION'];
			echo '<input type="text" size="25" name="support_name[]"  value="';
			echo  $row['LOCATION'] . '">' ;
			echo '<input type="text" size="75" name="support_email[]"  value="';
			echo  $row['EMAIL'] . '">';
                        echo '<input type="text" size="25" name="support_phone[]"  value="';
                        echo  $row['PHONE_NUMBER'] . '">';
                        echo '<input type="text" size="25" name="support_fax[]"  value="';
                        echo  $row['FAX_NUMBER'] . '">';
			echo '<br>' ;
                        $i++;

                }
		echo "<a id=\"anchor\" href=\"#\" onclick=\"add_box();\"> Add</a>";
		
                mysqli_close($conn);
                                ?>

			</td>
		</tr>
	
</table>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 To remove an entry... clear the "Support Center" field
<!-- </form> -->
</div>
<div class="tabbertab" title="Site Types">
<!-- <form method="POST" action=""> -->
<table border="1" cellspacing="1" width="100%">
                <tr>
                        <td>Site Types</td>
                        <td>
                        <?php get_config_file2("site-type.txt"); ?>
			</td>
                </tr>

</table>
<!-- </form> -->
</div>

<div class="tabbertab" title="Field Reps">
<!-- <form method="POST" action=""> -->
<table border="1" cellspacing="1" width="100%">
                <tr>
			<td>Field Service Reps (FSR)</td>
			<td>
			<?php get_config_file2("fsr.txt"); ?>
			</td>
		</tr>
</table>
<!-- </form> -->
</div>
<div class="tabbertab" title="Service Types">
<!-- <form method="POST" action=""> -->
<table border="1" cellspacing="1" width="100%">
		<tr>
			<td>Service Types</td>
			<td>
			<?php get_config_file2("service-type.txt"); ?>
			<br>
			</td>
		</tr>
</table>
<!-- </form> -->
</div>
<div class="tabbertab" title="Regions">
<!-- <form method="POST" action=""> -->
<table border="1" cellspacing="1" width="100%">
		<tr>
			<td>Regions</td>
			<td>
			<?php get_config_file2("region.txt"); ?>
			</td>
		</tr>
        </table>
</div>
<div class="tabbertab" title="Database">
<!-- <form method="POST" action="" name="database" id="database">-->
        <p>&nbsp;</p>
        <table border="1" cellspacing="1" width="100%">
                <tr>
                        <td>
                                Database Name
                                <a href="javascript:openhelp('help/databasename.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td><input type="text" name="database" size="20"
                        value="<?php if (defined('NETZ_DATABASE')){ echo NETZ_DATABASE ;} ?>"></td>
                </tr>
                <tr>
                        <td>
                                Database username
                                <a href="javascript:openhelp('help/databaseusername.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td><input type="text" name="username" size="20"
                        value="<?php if (defined('NETZ_DB_USERNAME')){echo NETZ_DB_USERNAME ;}else{echo "crap";} ?>"></td>
                </tr>
                <tr>
                        <td>
                                Database Password
                                <a href="javascript:openhelp('help/databasepassword.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td><input type="password" name="password" size="20"
                        value="<?php if (defined('NETZ_DB_PASSWORD')){ echo NETZ_DB_PASSWORD ;} ?>"></td>
                </tr>
                <tr>
                        <td>
                                Mysql Server Address
                                <a href="javascript:openhelp('help/databaseserver.html')">&nbsp;&nbsp;(?)</a>
                        </td>
                        <td><input type="text" name="server" size="20"
                        value="<?php if (defined('NETZ_DB_SERVER')){ echo NETZ_DB_SERVER ;} ?>"></td>
                </tr>
</table>
<!-- </form>-->
</div>
<div class="tabbertab" title="Plugins" id="plugins">
	<?php 
	$plugins_dir=$basedir."plugins";
	// scan the plugin directory for directory names... assume they are plugins
	$plugins=scandirr($plugins_dir);
	
	$plugins_ini = new ConfigMagik($basedir."plugins.ini", true, true );
	echo "<table style=\"width:95%\">";
	echo "<tr>";
	echo "<td>Name</td>";
	echo "<td>Description</td>";
	echo "<td>version</td>";
	echo "<td>Status</td>";
	echo "<td style=\"text-align:center\">Lowest User Level</td>";
	echo "<td>&nbsp;</td>";
	echo "</tr>";
	// process each directory in the plugin directory
	foreach ($plugins as $plugin_dir_name){
		// clear the variables
		$name= "";
		$desc="";
		$version="";
		// make sure it is a directory and not . or ..
		if (is_dir($plugins_dir."/".$plugin_dir_name) 
				&& $plugin_dir_name != "." 
				&& $plugin_dir_name != ".."
				&& $plugin_dir_name != "plugin-template"){
			// start the table row
			echo "<tr style=\"border:1px solid black\">";
			// check if there is a ini for this plugin
			if (is_file($plugins_dir."/".$plugin_dir_name."/plugin.ini")){
				// Load up the Plugin ini file
			$Config = new ConfigMagik($plugins_dir."/".$plugin_dir_name."/plugin.ini", true, true );
				// see if we can grab the plugin name, description and version
				$name= $Config->get('name', 'plugin_info');
				$desc=$Config->get('description', 'plugin_info');
				$version=$Config->get('version', 'plugin_info');
				if ($desc==""){$desc="&nbsp;";}
				// see if we got a name from the ini file.. if not use the directory name
				//if (trim($name)==""){$name= "<font color=\"blue\" >" . $plugin_dir_name . "</font>";}
				if (trim($name)==""){$name= $plugin_dir_name;}
				// if the version is empty add a space so the table cell displays correct
				if ($version==""){$version=" &nbsp;";}
			}else{
				//$name="<font color=\"blue\" >". $plugin_dir_name ."</font>";
				$name=$plugin_dir_name;
				$desc="<b><font color=\"red\" >****** Missing Config File ******</font></b> ".$plugins_dir."/".$plugin_dir_name."/plugin.ini";
				
			}
			//$name_tag="<font color=\"blue\" >".$name."</font>";
			$name_tag=$name;
			foreach ($plugins_ini->listSections() as $plugin){
				if (trim($plugin_dir_name) == trim($plugin)){
				//if (trim($name) == trim($plugin)){
					echo " <td style=\"border:1px solid grey\">". $name_tag . "</td>";
					echo " <td style=\"border:1px solid grey\">". $desc . "</td>";
					echo " <td style=\"border:1px solid grey\">". $version . "</td>";
					echo "<td style=\"border:1px solid grey\">";
					echo "<a style=\"color:green\" href=\"plugin_write.php";
					echo "?section=".urlencode($plugin_dir_name);
					echo "&enabled=false\"> Disable</a>";
					echo "</td>"; 
					$user_level=$plugins_ini->get('level',$plugin);
					echo "<td style=\"border:1px solid grey ; text-align:center\">";
					echo "<a href=\"plugin_write.php?section=".urlencode($plugin_dir_name);
					echo "&enabled=true\">".$user_level."</a>";
					echo "</td><td style=\"border:1px solid grey\">";
					/* Check if there is a config page and display button */
					if (is_file($plugins_dir."/".$plugin_dir_name."/config.php")){
					    echo "<input class=\"button\" type=\"button\" value=\"Config\" ";
					    echo "onclick=\"javascript:openhelp('plugins/".$plugin_dir_name;
					    echo "/config.php','800','400')\">";
					echo "<a href='plugin_config.php?plugin_dir=".$plugin_dir_name."'";
					echo "> Config</a>";
					}	
					echo "</td>";
					$hit = "true" ;
				}	
			}
			if ($hit != "true"){
				echo " <td style=\"border:1px solid grey\">". $name_tag."</td>";
				echo " <td style=\"border:1px solid grey\">". $desc . "</td>";
				echo " <td style=\"border:1px solid grey\">". $version . "</td>";
				echo "<td style=\"border:1px solid grey\">";
				echo "<a style=\"color:red\" href=\"plugin_write.php?section=";
				echo urlencode($plugin_dir_name);
				echo "&enabled=true\">Enable</a> ";
				echo "</td>";
				$user_level=$plugins_ini->get('level',$plugin);
				echo "<td style=\"border:1px solid grey; text-align:center\">".$user_level;
				echo "</td><td style=\"border:1px solid grey\">&nbsp;";
				echo "</td>";
			}else{ 
				$hit = "false";
			}
			echo "</tr>";
        	}
	}
	echo "</table>";
	echo "<br><br><a href=\"javascript:openhelp('help/plugin.html','300','600')\">";
	echo "&nbsp;&nbsp; NETz Plugin building help</a><br>";
	?>
</div>
<div class="tabbertab" title="Form field Names" id="form_field_names">
<!-- <form method="POST" action="" name="database" id="database">-->
        <table border="1" cellspacing="1" width="65%">
	<tr>
	<th>Database field Name</th>
	<th>Display Name</th>
	</tr>

<?php
$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);
$query ="SELECT * FROM NAME_MAPING WHERE EDITABLE = 1 Order By DISPLAY_NAME";
$result=mysqli_query($conn,$query);
while ($row = mysqli_fetch_assoc($result)){

	//echo "<tr><td>";	
	//echo $row['DB_FIELD_NAME'];
	echo "<tr>\n";
		echo "\t<td>\n";
		echo "\t\t<span> ".$row['DB_FIELD_NAME']."</span>\n";
		echo "\t</td>\n";
		echo "\t<td>\n\t\t<input type=\"text\" style=\"width:200px\" ";
		echo "\t\tname=\"".$row['DB_FIELD_NAME']."\" ";
		echo "\t\tvalue=\"".$row['DISPLAY_NAME']."\">\n";
		echo "\t</td>\n";
	
		echo "</tr>\n";	
}
?>
</table>
<!-- </form>-->
</div>
<script type="text/javascript" >
//document.getElementById('plugins').tabShow();
</script>
	<input class="button" type="submit" value="Submit" name="B1">
        <input class="button" type="button" value="Cancel" name="B2" onclick="window.location='main.php'">
	<br>

</form>

</div>
</body>
<script type="text/javascript" >
//size_email_box()
</script>

</html>
