<?php
include_once("site-monitor.conf.php");
function get_zone($site){
require_once( 'class.ConfigMagik.php');
$current_dir = getcwd();
$Config = new ConfigMagik($current_dir."/plugins.ini", true, true );
$google_map_api_key= $Config->get('api_key', 'plugin_info');
$conns = mysql_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD);
if (!$conns) {  
   die('Could not connect: ' . mysql_error());
}       
mysql_select_db(NETZ_DATABASE);
                $SQL="SELECT * FROM SITEDATA WHERE SITE_ID = '".$site . "'" ;
                $result=mysql_query($SQL);
                $row = @mysql_fetch_assoc($result);
$timeout = 1;

                if ($row['LATITUDE'] == "" || $row['LONGITUDE'] == ""){
                        if ($row['ADDRESS']!="" && $row['ZIP']!=""){
                                $address= preg_replace('/ /',"+",$row['ADDRESS']);
                                $city = preg_replace('/ /',"+",$row['CITY']);
                                $state = preg_replace('/ /',"+",$row['ST']);
                                $zip = preg_replace('/ /',"+",$row['ZIP']);
                                $address_coded = $address ."+,".$city."+".$state ."+".$zip;
                                $by_zip="lookup";
                        }elseif($row['ZIP']!=""){
                                $address_coded = $row['ZIP'];
                                $by_zip="zip";
                        }elseif($row['CITY'] != "" && $row['ST'] != ""){
                                $address_coded = preg_replace('/ /',"+",$row['ST']);
                                $by_zip="city";
                        }elseif($row['ST'] != ""){
                                $address_coded = preg_replace('/ /',"+",$row['ST']);
                                $by_zip="state";
                        }
                        $query = "http://maps.google.com/maps/geo?q=".$address_coded;
                        $query = $query . "&output=csv&sensor=false&key=" . $google_map_api_key;
                        $url = parse_url($query);
                        $host = $url["host"];
                        $path = $url["path"] . "?" . $url["query"];
                        //echo $path . "<br><br>";
                        $timeout = 1;
                        $fp = fsockopen ($host, 80, $errno, $errstr, $timeout)
                        or die('Can not open connection to server.');
                        if ($fp) {
                                stream_set_timeout($fp,1);
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
                        echo "// ".$data ."\n";
                        // check if status code is "200 success
                       if ($geoinfo[0] == "200"){
                                $lat= $geoinfo[2];
                                $lon= $geoinfo[3];
                        }
                }else{
                        $lat= $row['LATITUDE'];
                        $lon= $row['LONGITUDE'];
                }
if ($lat != ""){
        $query = "http://www.earthtools.org/timezone/".$lat."/".$lon;
	// Set the timeout for 5 seconds in case site takes too long it will not hold up OPS.php loading
	ini_set('default_socket_timeout', 5);
        $str = file_get_contents($query);
        $lines = split("\n", $str);

        if (preg_match("/<offset>([^<]+)<\/offset>/i", $str, $match)) {  
                $timezone = $match[1];  
                }  
}
        switch ($timezone) {
            case "-4" : return "AST-Atlantic (UTC -4)"; break;
            case "-5" : return "EST-Eastern (UTC -5)"; break;
            case "-6" : return "CST-Central (UTC -6)"; break;
            case "-7" : return "MST-Mountain (UTC -7)"; break;
            case "-8" : return "PST-Pacific (UTC -8)"; break;
            case "-9" : return "AKST-Alaska (UTC -9)"; break;
            case "-10" : return "HST-Hawaiian (UTC -10)"; break;
            case "-11" : return "SST-SAMOA (UTC -11)"; break;
            case "+10" : return "CHST-CHAMORRO (UTC +10)"; break;
        }
}


?>
