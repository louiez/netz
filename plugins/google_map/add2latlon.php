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
ob_start();
include('../../logon.php');
include_once("../../site-monitor.conf.php");
include('../../write_access_log.php');
$conns = mysql_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD);
if (!$conns) {
   die('Could not connect: ' . mysql_error());
}
$site=$_GET['site'];
$action=$_GET['action'];
if ($site != ""){
        mysql_select_db(NETZ_DATABASE);
	if ($action == "clear"){
        	$SQL="UPDATE SITEDATA SET LATITUDE = '' WHERE SITE_ID = '".$site . "'" ;
		mysql_query($SQL);
                $SQL="UPDATE SITEDATA SET LONGITUDE = '' WHERE SITE_ID = '".$site . "'" ;
		mysql_query($SQL);
		exit();
	}
        $SQL="SELECT * FROM SITEDATA WHERE SITE_ID = '".$site . "'" ;
        //echo $SQL;
        $result=mysql_query($SQL);
        $row = @mysql_fetch_assoc($result);
	// Just give the lat and long
	if ($action == "show"){
		echo ' <html><head></head>';
		echo '<body>';
		echo $row['LATITUDE'] . '<br>' . $row['LONGITUDE'];
		echo '</body></html>';
		exit();
	}
        // Check the site record for lat and lon values
        if ($row['LATITUDE'] == "" || $row['LONGITUDE'] == ""){
                $address= preg_replace('/ /',"+",$row['ADDRESS']);
                $city = preg_replace('/ /',"+",$row['CITY']);
                $state = preg_replace('/ /',"+",$row['ST']);
                $zip = preg_replace('/ /',"+",$row['ZIP']);
                $address_coded = $address ."+,".$city."+".$state ."+".$zip;
                $query = "http://rpc.geocoder.us/service/csv?address=" . $address_coded;
		//$query = "http://maps.google.com/maps/geo?q=".$address_coded;
		//$query = $query . "&output=json&oe=utf8&sensor=false&key=" . $google_map_key;
                $url = parse_url($query);
                $host = $url["host"];
                $path = $url["path"] . "?" . $url["query"];
                //echo $path . "<br><br>";
                $timeout = 1;
                $fp = fsockopen ($host, 80, $errno, $errstr, $timeout)
                or die('Can not open connection to server.');
                if ($fp) {
                        fputs ($fp, "GET $path HTTP/1.0\nHost: " . $host . "\n\n");
                        while (!feof($fp)) {
                                $buf .= fgets($fp, 256);
                        }
                        // Strips the header
                        $lines = split("\n", $buf);
                        $data = $lines[count($lines)-2];
                        fclose($fp);
                        $geoinfo=split(",",$data);
                }
		// no lat and long returned... 
		// lets try zip code by calling this page again with the zip code
		$by_zip="";
                if ($geoinfo[1] == ""){
			unset($geoinfo);
	                $query = "http://geocoder.us/service/csv/geocode?zip=" . $row['ZIP'];
	                $url = parse_url($query);
	                $host = $url["host"];
	                $path = $url["path"] . "?" . $url["query"];
	                //echo $path . "<br><br>";
	                $timeout = 1;
	                $fp = fsockopen ($host, 80, $errno, $errstr, $timeout)
		                or die('Can not open connection to server.');
	                if ($fp) {
				fputs ($fp, "GET $path HTTP/1.0\nHost: " . $host . "\n\n");
                        	while (!feof($fp)) {
					$buf .= fgets($fp, 256);
				}
             			// Strips the header
                        	$lines = split("\n", $buf);
                        	$data = $lines[count($lines)-1];
                        	fclose($fp);
                        	$geoinfo=split(",",$data);
                	}
			$by_zip="zip";
		}
		// lets test again
		if ($geoinfo[1] != ""){
                        // netz-gm.php?lat=32.809799&lon=-96.799301
			$theheader="Location: https://" . $_SERVER['HTTP_HOST'];
			$theheader .=  dirname($_SERVER['PHP_SELF']);
			$theheader .=  "/netz-gm.php?lat=".$geoinfo[0]."&lon=".$geoinfo[1];
			if ($by_zip=="zip"){
				$theheader .= "&address=".$row['ZIP'];
			}else{
				$SQL="UPDATE SITEDATA SET LATITUDE = '".trim($geoinfo[0])."' WHERE SITE_ID = '".$site . "'" ;
				mysql_query($SQL);
				$SQL="UPDATE SITEDATA SET LONGITUDE = '".trim($geoinfo[1])."' WHERE SITE_ID = '".$site . "'" ;
				mysql_query($SQL);
				$theheader .= "&address=".$row['ADDRESS']." ".$row['CITY'].", ".$row['ST']." ".$row['ZIP'];
			}
			header($theheader);
                        ob_end_flush();
		}

        }else{ // else from ($row['LATITUDE'] == "" || $row['LONGITUDE'] == "")
		$theheader="Location: https://" . $_SERVER['HTTP_HOST'];
		$theheader .= dirname($_SERVER['PHP_SELF']);
		$theheader .= "/netz-gm.php?lat=".$row['LATITUDE']."&lon=".$row['LONGITUDE'];
		$theheader .="&address=".$row['ADDRESS']." ".$row['CITY'].", ".$row['ST']." ".$row['ZIP'];
		header($theheader);
                ob_end_flush();
        }

}
?>
