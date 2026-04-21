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



include 'logon.php';

include_once("site-monitor.conf.php");



$conns = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);

if (!$conns) {

   die('Could not connect: ' . mysqli_error());

}




// Load variables with posted data

$un=addslashes($_SESSION['user']);

$theme=trim($_POST['txttheme']);

$support=$_GET['support'];

// format the date for mysql

$date=date('Y-m-d G:i:s');

//$array = array($theme,$_SESSION['support']);

if ($theme != ""){ $array[STYLESHEET] = $theme;} else {$array[STYLESHEET] = $_SESSION['style'];}

if ($support != ""){$array[SUPPORT] = $support;} else {$array[SUPPORT] = $_SESSION['support'];}

$array[MENU1] = $_SESSION['menu1'];

$array[MENU2] = $_SESSION['menu2'];

$array[MENU3] = $_SESSION['menu3'];

$array[MENU4] = $_SESSION['menu4'];

$array[MENU5] = $_SESSION['menu5'];

$temp = implode(":",$array);



	if (!$theme == ""){

		$sql = "UPDATE USERS SET STYLE = '" . $temp. "' WHERE USERNAME = '" .$un . "'";

		$query = mysqli_query($conns,$sql);

	}

if (trim($_POST['txttheme']) != ""){

/*

	echo "<html>

	<body onload=\"javascript: window.opener.location.reload(true);window.close()\">

	</body></html>";

*/

        echo "<html><body ";

	echo " onload=\"javascript: window.opener.document.getElementById('css').href = '".$theme."';";

	echo "window.close()\">

        </body></html>";

}

?>
