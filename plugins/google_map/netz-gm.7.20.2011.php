<?php
include('../../logon.php');
include_once("../../site-monitor.conf.php");
include('../../write_access_log.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">  
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <style type="text/css">
    v\:* {
      behavior:url(#default#VML);
    }
#simple_example_window{
  width: 300px;
}
#simple_example_window_contents{
  background-color: #FFF;
  border: 3px solid  #900;
}
#simple_example_window_beak{
  width: 28px;
  height: 38px;
  background: url('images/simple_beak.png') top left no-repeat transparent;
}
* html #simple_example_window_beak{
  /* Alpha transparencies hack for IE */
  background-image:none;
  filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='images/red_beak.png', sizingMethod='crop');
}
#simple_example_window_tl, #simple_example_window_tr, #simple_example_window_bl, #simple_example_window_br,
#simple_example_window_t,#simple_example_window_l,#simple_example_window_r,#simple_example_window_b{
  height: 0px;
  width: 0px;
}

    </style>
    <title>NETz Map</title>
<?php
require_once( '../../class.ConfigMagik.php');
$current_dir = getcwd();
$Config = new ConfigMagik($current_dir."/plugins.ini", true, true );
$google_map_api_key= $Config->get('api_key', 'plugin_info');
?>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $google_map_api_key; ?>"
            type="text/javascript"></script>
<script src="dragzoom.js" type="text/javascript"></script>
<script src="extinfowindow.js" type="text/javascript"></script>
    <script type="text/javascript">
<?php
$zoom=$_GET['z'];
$address=$_GET['address'];
if ($zoom > 18 ){$zoom = 18;}elseif($zoom == ""){$zoom = 13;}
$latlon=$_POST["latlon"];
if ($latlon == ""){
        $lat=$_GET["lat"];
        $lon=$_GET["lon"];
}
if ($lat == ""){
        $lat="32.644193";
        $lon="-97.108827";
}

$conns = mysql_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD);
if (!$conns) {
   die('Could not connect: ' . mysql_error());
}
$site=$_GET['site'];
//$group=$_GET['group'];
$selection =$_GET['selection'];
        if ($selection == "group"){
                $SQL="SELECT * FROM SITEDATA WHERE SITE_ID = '".$site . "'" ;
                $result=mysql_query($SQL);
                $row = @mysql_fetch_assoc($result);
                $SQL="SELECT * FROM SITEDATA WHERE GROUP_NAME = '".$row['GROUP_NAME'] . "'" ;
                $result=mysql_query($SQL);
        }elseif  ($selection == "region"){
                $SQL="SELECT * FROM SITEDATA WHERE SITE_ID = '".$site . "'" ;
                $result=mysql_query($SQL);
                $row = @mysql_fetch_assoc($result);
                $SQL="SELECT * FROM SITEDATA WHERE REGION = '".$row['REGION'] . "'" ;
                $result=mysql_query($SQL);
        }elseif ($selection == "site"){
                 $SQL="SELECT * FROM SITEDATA WHERE SITE_ID = '".$site . "'" ;
                $result=mysql_query($SQL);
      //        $row = @mysql_fetch_assoc($result);
        }elseif ($selection == "offline"){
                 //$SQL="SELECT * FROM SITEDATA WHERE SITE_ID = '".$site . "'" ;
                if ($site != ""){$qe="AND SUPPORT_CENTER = '".$site . "'";}else{$qe="";}
                $SQL="SELECT * FROM SITEDATA WHERE MONITOR_ENABLE = 1 ";
                $SQL .= "and MONITOR_STATUS != 0 ".$qe;
                $result=mysql_query($SQL);
        }else{
                 $SQL="SELECT * FROM SITEDATA WHERE ".$selection." like '".$site . "' LIMIT 500" ;
                $result=mysql_query($SQL);

        }
//$result=mysql_query($SQL);

?>
    function StreetViewOverlay() {
    }
    StreetViewOverlay.prototype = new GControl();

    // Creates a one DIV for the button
    // DIV which is returned as our control element. We add the control to
    // to the map container and return the element for the map class to
    // position properly.
    StreetViewOverlay.prototype.initialize = function(map) {
      var container = document.createElement("div");

      var streetButton = document.createElement("div");
      this.setButtonStyle_(streetButton);
      container.appendChild(streetButton);
        streetButton.innerHTML = "Street view";

      GEvent.addDomListener(streetButton, "click", function() {
                if (streetButton.innerHTML == "Street view"){
                        streetButton.innerHTML = "Click blue overlay for street view<br><span style=\"color:red\"> HIDE</span>";
                        map.addOverlay(svOverlay);
                        mymy = GEvent.addListener(map,"click", function(overlay,latlng) {
                        myPano.setLocationAndPOV(latlng);
                        });
                }else{
                        streetButton.innerHTML = "Street view";
                        map.removeOverlay(svOverlay);
                        GEvent.removeListener(mymy);
                }
      });


      map.getContainer().appendChild(container);
      return container;
    }

    // By default, the control will appear in the top left corner of the
    // map with 7 pixels of padding.
    StreetViewOverlay.prototype.getDefaultPosition = function() {
      return new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(7, 110));
    }

    // Sets the proper CSS for the given button element.
    StreetViewOverlay.prototype.setButtonStyle_ = function(button) {
      button.style.textDecoration = "underline";
      button.style.color = "#0000cc";
      button.style.backgroundColor = "white";
      button.style.font = "8pt Arial";
      button.style.border = "1px solid black";
      button.style.padding = "2px";
      button.style.marginBottom = "3px";
      button.style.textAlign = "center";
      button.style.width = "6em";
      button.style.cursor = "pointer";
    }
function createMarker(point, waypoint_name, waypoint_info, source) {
          // Create a lettered icon for this point using our icon class
          //var letter = String.fromCharCode("A".charCodeAt(0) + index);
          //var letteredIcon = new GIcon(baseIcon);
          //letteredIcon.image = "http://www.google.com/mapfiles/marker" + letter + ".png";
          // Set up our GMarkerOptions object
          //markerOptions = { icon:letteredIcon };
          //var marker = new GMarker(point, markerOptions);
        var siteIcon = new GIcon(G_DEFAULT_ICON);
        markerOptions = { icon:siteIcon, draggable:true};
        //markerOptions = {draggable: true};
        if (source == "zip"){
                siteIcon.image = "http://gmaps-samples.googlecode.com/svn/trunk/markers/blue/blank.png";
        }else if(source == "latlon"){
                siteIcon.image = "http://gmaps-samples.googlecode.com/svn/trunk/markers/green/blank.png";
        }

        var marker = new GMarker(point,markerOptions);
        GEvent.addListener(marker, "click", function() {
                        marker.openInfoWindowHtml("<b>" + waypoint_name + "</b><br>" + waypoint_info);
        });
        GEvent.addListener(marker, "dragstart", function() {
                        marker.closeInfoWindow();
        });
        GEvent.addListener(marker, "dragend", function() {
                                myhtml= "<b>" + waypoint_name + "</b><br>" + waypoint_info;
                                myhtml= myhtml + "<br>" + marker.getLatLng().toUrlValue();
                                marker.openInfoWindowHtml(myhtml);
        });
        return marker;
}
var bounds = new GLatLngBounds(); 
function load() {
        if (GBrowserIsCompatible()) {
                myPano = new GStreetviewPanorama(document.getElementById("pano"));
                // street view
                GEvent.addListener(myPano, "error", handleNoFlash); 
                var map = new GMap2(document.getElementById("map"));
               // map.setMapType(G_HYBRID_MAP);
                map.addMapType(G_SATELLITE_3D_MAP);
                map.addMapType(G_PHYSICAL_MAP) ; 
                map.addControl(new GMapTypeControl());
                //map.setCenter(new GLatLng(<?php echo $lat.",".$lon?>), <?php echo $zoom ?>);


        <?php
        while ($row = mysql_fetch_assoc($result)){
                $by_zip="";
                $lat= "";
                $lon= "";
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
                        //$query = "http://rpc.geocoder.us/service/csv?address=" . $address_coded;
                        $query = "http://maps.google.com/maps/geo?q=".$address_coded;
                        //$query = $query . "&output=csv&sensor=false&key=" . $google_map_api_key;
			$query = $query . "&output=csv";
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
                                if ($by_zip != "zip"){
                                        $SQL="UPDATE SITEDATA SET LATITUDE = '".$lat."' ";
                                        $SQL .= "WHERE SITE_ID = '".$row['SITE_ID'] . "'" ;
                                        mysql_query($SQL);
                                        $SQL="UPDATE SITEDATA SET LONGITUDE = '".$lon."' ";
                                        $SQL .= "WHERE SITE_ID = '".$row['SITE_ID'] . "'" ;
                                        mysql_query($SQL);
                                }
                        }
                        //`sleep(1);
                }else{ // else from ($row['LATITUDE'] == "" || $row['LONGITUDE'] == "")
                        $lat= $row['LATITUDE'];
                        $lon= $row['LONGITUDE'];
                        $by_zip = "latlon";

                } // END ($row['LATITUDE'] == "" || $row['LONGITUDE'] == "")

                if ($lat != "" && $lon != ""){
                        $site_info = $row['GROUP_NAME']."<br>";
                        $site_info .= $row['ADDRESS']."<br>".$row['CITY'].",".$row['ST']." ".$row['ZIP']."<br>";
                        $site_info .= "By ".$by_zip ; 
                        echo "mylatlng = new GLatLng(".$lat.",".$lon.");\n";
                        $overlay_code="map.addOverlay(createMarker(mylatlng,\"" . $row['SITE_ID'] ."\",";
                        $overlay_code .= "\"".$site_info . "\",\"".$by_zip."\"));\n";
                        echo $overlay_code;
                        echo "bounds.extend(mylatlng);\n";
                        if ($selection == "site"){
                                echo "map.setCenter(new GLatLng(".$lat.",".$lon."), 15);";
                        }elseif ($selection == "group"){
                                echo "map.setCenter(bounds.getCenter(),8);";
                        }else{
                                echo "map.setCenter(bounds.getCenter(),5);";
                        }               
                }
                usleep(5000);
        }
                mysql_close();
                ?>
                // show the Lat/Lon if double clicked
                GEvent.addListener(map,"dblclick", function(overlay,latlng) {     
                        if (latlng) {   
                                var myHtml = "Lat " + latlng.lat() + "<br>Lon " + latlng.lng();
                                        map.openInfoWindow(latlng, myHtml);
                        }
                });
                // Create the street view overlay object
                svOverlay = new GStreetviewOverlay();
                // create the street view button
                map.addControl(new StreetViewOverlay());

                map.enableScrollWheelZoom();
                map.addControl(new GSmallMapControl());
                map.addControl(new GScaleControl());
                //map.addControl(new DragZoomControl());
                // Balloon window
                //        map.openInfoWindow(map.getCenter(),
                //                   document.createTextNode("Hello, world"));
        }
        //var point = new GLatLng(<?php echo $lat.",".$lon?>);

        //map.addOverlay(new GMarker(point));
        window.title="<?php echo $address; ?>";

}
    function handleNoFlash(errorCode) {
      if (errorCode == FLASH_UNAVAILABLE) {
        alert("Error: Flash doesn't appear to be supported by your browser");
        return;
      }
    }  
function resize() {
// alert(window.outerHeight);
mymap=document.getElementById("map");
mypano=document.getElementById("pano");
if (window.innerHeight){
        mymap.style.height = window.innerHeight *.58;
        mymap.style.width = window.innerWidth * .95;
        mypano.style.height = window.innerHeight *.38;
        mypano.style.width = window.innerWidth * .95;
}else if (document.body.clientHeight){
        mymap.style.height = document.body.clientHeight *.58;
        mymap.style.width = document.body.clientWidth * .95;
        mypano.style.height = document.body.clientHeight *.38;
        mypano.style.width = document.body.clientWidth * .95;
}
myPano.checkResize();
// load();

} 

    
</script>
  </head>
<?php
        if ($selection != ""){
?>
        <script type="text/javascript" src="../../size_window.js">  </script>
        <body onresize="resize()"  onload="load()" onunload="GUnload()">
                <div id="display">
                <script type="text/javascript"> window.title="<?php echo $address ?>" </script>
                <div id="map" style="width: 600px; height: 300px"></div>
                <div id="pano" style="width: 600px; height: 200px"></div>
                </div>
                <script type="text/javascript">sizeToFit('display'); resize(); </script>
        </body>
<?php
        }else{
?>
        <script type="text/javascript" src="../../size_window.js">  </script>
        <body>
        <div id="display">
        <form methon="get" action="netz-gm.php">
        <input type="hidden" name="site" id="site" value="<?php echo $site; ?>">
<!--    Region <input type="radio" name="selection" value="region"><br> -->
        Group <input type="radio" name="selection" value="group"><br>
        Store <input type="radio" name="selection" value="site"><br>
        <input type="submit" name="submit">
        </form> 
        </div>
        <script type="text/javascript"> sizeToFit('display'); </script>
        </body>
<?php
        }
?>

</html>
<script type="text/javascript">
//var myPano = new GStreetviewPanorama(document.getElementById("pano"));
</script>
