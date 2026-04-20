<?php
//session_start();
include 'auth.php';
include_once("site-monitor.conf.php");
include('write_access_log.php');
include_once('lmz-functions.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8">
        <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
        <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
        <title>NETz home</title>
        <?php $style=$_SESSION['style']; if ($style==""){$style="/style/ultramarine.css";}?>
        <link rel="stylesheet" href="<?php echo $style  ?>" type="text/css" id="css">
        <link rel="shortcut icon" href="<?php echo dirname($_SERVER['PHP_SELF'])."/favicon.ico"; ?>" type="image/vnd.microsoft.icon" >
        <link rel="icon" href="<?php echo dirname($_SERVER['PHP_SELF'])."/favicon.ico"; ?>" type="image/vnd.microsoft.icon" >
        <script type="text/javascript">
                if (screen.width < 400){window.location.href="main-mini.php";}
        </script>
</head>

<body>
	<?php add_plugin('main_load'); ?>
        <p align="center">
        <img alt="netz img" src="netz.jpg" width="350" height="102" ><br><br>
        <?php
		// ****** Head menu ******
		// Hello user message
                echo "Hello " . htmlentities($_SESSION['name'],ENT_QUOTES);
                echo "&nbsp;&nbsp;<a href='logoff.php'> Logoff</a>";
        	echo "&nbsp;&nbsp;&nbsp;";
		// Change Password link
        	echo "<a href=\"@\" ";
		echo "onclick=\"window.open('user-pass-change.php?user=";
		echo urlencode($_SESSION['user'])."','','width=400,height=200,";
		echo "resizable=yes,scrollbars=yes,status=yes');";
		echo " return false\">";
		echo "Change Password</a>";
        	echo "&nbsp;&nbsp;&nbsp;";
		// Themes Link
        	echo "<a href=\"@\" ";
		echo "onclick=\"window.open('user-theme.php?user=";
		echo urlencode($_SESSION['user'])."','','width=325,height=30,";
		echo "resizable=yes,scrollbars=yes,status=yes'); ";
		echo "return false\">Themes</a>";
		echo "<br><br>";
        	echo "<center>";
		echo "<fieldset id=\"f_info\" style=\" width:50% ; ";
		echo "position:relative  ; text-align:center \">";
		// Info Menu
        	echo "<legend>Info</legend>";
        	if ($_SESSION['accesslevel'] >= 2){
			echo '<a href="ops.php">NETz Ops</a><br>';
		}
		if ($_SESSION['accesslevel'] >= 1){
                	echo '<a href="support.php">Support Panel</a><br>';
                	echo '<a href="site-monitor.php">Down Sites</a><br><br>';
			//echo '<a href="health.html">WAN Health</a><br>';
        	}
		if ($_SESSION['accesslevel'] >= 9){
                	echo '<a href="server-health.php">Server health</a><br>';
                	echo "<br>";
                	echo '<a href="add-site.php">Add New Site</a><br>';
			echo "<br>";
        	}
        	if ($_SESSION['accesslevel'] >= 2){
                	echo '<a TARGET="_blank" href="querycreate.php">Query Builder</a><br>';
        	}
		add_plugin('main_info');
        	echo '<br><a href="active-sites.php">Active Sites</a><br>';
        	echo '</fieldset><br>';
		// Admin Menu
        	if ($_SESSION['accesslevel'] == 10){
                	echo "<fieldset id=\"f_admin\" style=\" width:50% ; ";
			echo "position:relative  ; text-align:center\">";
                	echo '<legend>Admin</legend>';
			echo '<a href="add-site.php"> Add Site</a><br>';
                	echo '<a href="useradmin.php">UserAdmin</a><br>';
                	echo '<a href="netz-config.php">NETz Config</a><br>';
			add_plugin('main_admin');
        		echo "</fieldset><br>";
        	}
		// Tools Menu
        	echo '<fieldset id="f_tools" style=" width:50% ; position:relative  ; text-align:center">';
        	echo '<legend>Tools</legend>';
        	if ($_SESSION['accesslevel'] >= 1){
                	echo '<a href="tools/tools.php">NETz Tools</a><br>';
                	echo '<a href="tools/mac.php">MAC address search</a><br>';
		}
		add_plugin('main_tools');
        	echo "</fieldset></center>";
		// alert message
        	if (trim($alert_message) != ""){
                	echo '<marquee id="alertit" ';
                	echo 'style="font-size:14; font-weight:bold; color:#FF0000; background-color:cccccc" ';
                	echo 'scrolldelay="100" scrollamount="5"  behavior="scroll" loop=-1 ';
                	echo "onmouseover=\"javascript:this.stop()\" ";
			echo "onmouseout=\"javascript:this.start()\">". $alert_message ."</marquee>";
        	}

        	add_plugin('main_footer');
        	include('netzfooter.php');
	?>
<img
        src="img/valid-html401-blue.png"
        alt="Valid HTML 4.01 Transitional" height="23" width="66" title="Tested as Valid HTML 4.01 Transitional">
    <img style="border:0;width:66px;height:23px"
        src="img/vcss-blue" title="Tested Valid CSS"
        alt="Valid CSS!" >
<img style="border:0;width:80px;height:15px"
        src="img/php5-power-micro.png"
        alt="php powered" >
<p>

</body>

</html>
