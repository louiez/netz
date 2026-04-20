<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <style type="text/css">
    v\:* {
      behavior:url(#default#VML);
    }
    </style>

    <title>Visual traceroute - Proedgenetworks.com</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAsuVQPOBSQxT_HvvMJLcE7hQyutGTqNmUV9klr0eerSDSzPRQNBSZClPSl21MnBNhOV8x5AcEe8OqUA"
            type="text/javascript"></script>
    <script type="text/javascript">
function clear_message(){
	document.getElementById('message').innerHTML = "Tracing Route..."
	return true;
}

<?php
include("geoip/geoipcity.inc");
include("geoip/geoipregionvars.php");
session_start();
session_write_close();
ob_implicit_flush(true);
ob_end_flush();
$contents = $_POST["ips"];
$ip=$_POST["ip"];
if (trim($contents) == ""){
if ($ip==""){$ip=$_GET["ip"];}
exec("/var/www/html/cgi-bin/sneaky_trace.sh ".escapeshellcmd($ip) . " > /var/www/html/netz/tracehops");
//exec("sudo /usr/local/apache/htdocs/proedgenetworks/trace.sh ".$ip . " > /usr/local/apache/htdocs/proedgenetworks/tracehops");
}
?>
// ***************************** 
//  GLOBAL VARIABLS  
// *****************************
 var testpoint = [];
var icon = [];
//var marker = [];
// ******************************

// Creates a marker at the given point with label
function createMarker(point, message, idx) {
	icon[idx] = new GIcon(G_DEFAULT_ICON);
	icon[idx].image = "http://www.proedgenetworks.com/img/2_" + idx + ".png";

  	var marker = new GMarker(point,icon[idx]);
  	GEvent.addListener(marker, "click", function() {
	marker.openInfoWindowHtml(message + "</b>");
  	});
  return marker;
}
function createMarker2(point, message, idx) {
        var icon = new GIcon(G_DEFAULT_ICON);
        icon.image = "http://www.proedgenetworks.com/img/3_" + idx + ".png";

        var marker = new GMarker(point,icon);
        GEvent.addListener(marker, "click", function() {
        marker.openInfoWindowHtml(message + "</b>");
        });
  return marker;
}

// this function allows clicking of the Hop links
function showHopOnMap(hop){
	// the map object is declaired at the bottom of the page so it will be global
	var zoomL = map.getZoom();
	map.setCenter(testpoint[hop],zoomL);
}
function load() {
	if (GBrowserIsCompatible()) {
		// needed the map object to be global so I load at the bottom of the page
		// because we need the the "map" element loaded before we init it
		//var map = new GMap2(document.getElementById("map"));
		var points = [];
		var MyMessage;
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());

        	map.setCenter(new GLatLng(37.85564,-97.43385),4);
		map.enableScrollWheelZoom();
		// Balloon window
		//        map.openInfoWindow(map.getCenter(),
		//                   document.createTextNode("Hello, world"));
	}
<?php

//$contents = $_POST["ips"];
if (trim($contents) == ""){
	// Load Regions from region.txt
	$filename="/var/www/html/netz/tracehops";
	//$filename=$basedir."region.txt";
	$fp= fopen($filename, "r");
	$contents= fread($fp,filesize($filename));
	fclose($fp);
}
$file_lines= split("\n",$contents);
$lastlat = 37.85564;
$lastlon = -97.43385;
$TTable = "<table border=\"1\" style =\"font-size:8pt;empty-cells: show;\">";
$TTable .= "<tr><td>Hop</td><td>IP Address</td><td>Time</td><td>City</td><td>State</td><td>Provider</td></tr>";
foreach ($file_lines as $hopip){
	$hopip = trim($hopip);
	if ($hopip != ""){
		if ($hopip != "unknown"){	
		$license_key="Vf7JB2RJ5uRl";
		//$query = "http://maxmind.com:8010/f?l=" . $license_key . "&i=" . trim($hopip);
		$query = "http://geoip3.maxmind.com/f?l=" . $license_key . "&i=" . trim($hopip);
	$url = parse_url($query);
		$host = $url["host"];
		$path = $url["path"] . "?" . $url["query"];
		//echo $path . "<br><br>";
		$timeout = 1;
		//$fp = fsockopen ($host, 8010, $errno, $errstr, $timeout)
		$fp = fsockopen ($host, 80, $errno, $errstr, $timeout)
        	or die('Can not open connection to server.');
		if ($fp) {
  			fputs ($fp, "GET $path HTTP/1.0\nHost: " . $host . "\n\n");
  			while (!feof($fp)) {
    				$buf .= fgets($fp, 128);
  			}
			// Strips the header
  			$lines = split("\n", $buf);
  			$data = $lines[count($lines)-1];
  			fclose($fp);
			$geoinfo=split(",",$data);
			if (($geoinfo[4] != "" && $geoinfo[5] != "") || ($geoinfo[10] != "")){
				$id++;
				// If the return was null give it some location close to the last and a city/state of unknown
				if ($geoinfo[1] == "(null)" || $geoinfo[10] != "" || $geoinfo[1] == ""){
					$geoinfo[4] = $lastlat + .10000;
					$geoinfo[5] = $lastlon;
					$geoinfo[1] = "Location Unknown";
					$geoinfo[2] = ""; 
				}
				// Load the Point data
				echo "var point = new GLatLng(". $geoinfo[4]."," .$geoinfo[5].");\n";
				echo "testpoint[".$id."] = new GLatLng(". $geoinfo[4]."," .$geoinfo[5].");\n";
				// grab the cords if needed on next pass for Null return
				$lastlat = $geoinfo[4];
				$lastlon = $geoinfo[5] ;
				// Add the Marker with Click balloons using createMarker() function
				if ($geoinfo[1] == "Location Unknown"){
					echo "map.addOverlay(createMarker2(point,\"".$hopip."<br>";
					echo  addslashes($geoinfo[8])."<br>" .$geoinfo[2].", ".$geoinfo[1]."\",".$id."));";
				}else{
                                        echo "map.addOverlay(createMarker(point,\"".$hopip."<br>";
                                        echo  addslashes($geoinfo[8])."<br>" .$geoinfo[2].", ".$geoinfo[1]."\",".$id."));";
				}
				echo "points.push(point);\n";
/*
				// get the sidebar info
				echo "MyMessage = document.getElementById(\"message\").innerHTML;\n";
				// add to the sidebar
				echo "document.getElementById(\"message\").innerHTML = MyMessage + \"";
				echo "(".$id.") ".$hopip." ".$geoinfo[2].", ".$geoinfo[1]." ".addslashes($geoinfo[8])."<br>\";\n";		
*/
				$rtime=shell_exec("sudo ping -n  -W 2 -c 1 ".$hopip." | grep \"^64 bytes\" | cut -d= -f4");
		$hostname=shell_exec("sudo dig  +time=2  +short -x  ".$hopip." 2>/dev/null | grep -v \"connection timed out\"");
				$TTable .= "<tr><td><a href=\"javascript:showHopOnMap('".$id."')\">".$id."</a>";
				$TTable .= "</td><td> ".$hopip." <br>".$hostname."</td>";
				$TTable .= "</td><td> ".$rtime."</td>";
				$TTable .= "<td>&nbsp; ".$geoinfo[2]."</td>";
				$TTable .= "<td>&nbsp; ".$geoinfo[1]."</td>";
				$TTable .= "<td> ".$geoinfo[8]."</td></tr>";
				//echo "map.openInfoWindow(point,document.createTextNode(\"".$hopip."\"));";
			}

			//echo $geoinfo[5]." ";

		}
		}
	}
}
$TTable .= "</table>";
echo "map.addOverlay(new GPolyline(points,\"#FF0000\",2))";
?>

  	//var point = new GLatLng(<?php echo $lat.",".$lon?>);

	//map.addOverlay(new GMarker(point));


}
 

    
</script>
  </head>
  <body onload="load()" onunload="GUnload()">
    <div id="map" style="height:95%;width:63%; margin-left:1%;margin-top:1%;position:absolute ; border-width:2px;border-style:solid;"></div>
	<div id="input" style="position:absolute ;width:25%;margin-left:66%;margin-top:0%;">
		<form method="POST" action="trace-gm.php">
		<input type="text" name="ip" value="<?php echo escapeshellcmd($ip);?>">
		<input type="submit" value="Traceit" onclick="return clear_message()">
		</form>
	</div>
	<div id="message" style="position:absolute ;margin-left:66%;margin-top:10%;border-width:2px;border-style:solid;padding:1px;font-size:10pt">
<?php
echo $ip;
echo $TTable;
echo $foo;
?>

</div>
<script type="text/javascript">
var map = new GMap2(document.getElementById("map"));
</script>
  </body>
</html>
