<?php
/*###############################################################
        NETz Network Management system				#
        http://www.proedgenetworks.com/netz			#
								#
								#
        Copyright (C) 2005-2026 Louie Zarrella			#
	louiez@proedgenetworks.com				#
								#
        Released under the GNU General Public License		#
	Copy of License available at :				#
	http://www.gnu.org/copyleft/gpl.html			#
###############################################################*/
//include_once('logon.php');
//include_once("site-monitor.conf.php");
require_once( 'class.ConfigMagik.php');
include_once("site-monitor.conf.php");
function microtime_float()
{
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
}
function remove_code($text)
{
global $netzlogs;
// add slashes.....  
// after first checking if PHP is not doing it by default
$text=htmlspecialchars($text, ENT_QUOTES);
//if (!get_magic_quotes_gpc()) {
	$text = addslashes($text);
//}
return $text;

/*
$search = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                 '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
                 '@&(quot|#34);@i',                // Replace HTML entities
                 '@&(amp|#38);@i',
                 '@&(lt|#60);@i',
                 '@&(gt|#62);@i',
                 '@&(nbsp|#160);@i',
                 '@&(iexcl|#161);@i',
                 '@&(cent|#162);@i',
                 '@&(pound|#163);@i',
                 '@&(copy|#169);@i',
                 '@&#(\d+);@e');                    // evaluate as php
//'@([\r\n])[\s]+@',                // Strip out white space
$replace = array ('',
                 '',
                 '\1',
                 '&',
                 '<',
                 '>',
                 ' ',
                 chr(161),
                 chr(162),
                 chr(163),
                 chr(169),
                 'chr(\1)');
// '"', // from 4 line of replace array
//$stringreturn = preg_replace($search, $replace, $text, -1 , $count);
$slen=md5($text);
$stringreturn = preg_replace($search, $replace, $text);
$elen=md5($stringreturn);
if ($slen != $elen) { 
	$err_msg="  ****** Possible Code injection Attack ****** By ".$_SESSION['user']." (".$_SERVER['REMOTE_ADDR']. ") Has been deleted";
	error_log(date('Y-m-d G:i:s').$err_msg."\n", 3,$netzlogs. "netz.log");
	//error_log($text."\n", 3,$netzlogs. "netz.log");
}
return $stringreturn;
*/
}

function generatePassword ($length = 8)
{

  // start with a blank password
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
    
  // set up a counter
  $i = 0; 
    
  // add random characters to NETZ_DB_PASSWORD until $length is reached
  while ($i < $length) { 

    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    // we don't want this character if it's already in the password
    if (!strstr($password, $char)) { 
      $password .= $char;
      $i++;
    }

  }

  // done!
  return $password;

}
function add_plugin($page_section,$site_id="")
{
global $basedir;
@$user_level= (int) $_SESSION['accesslevel'];
//$plugins_ini = new ConfigMagik($basedir."plugins.ini", true, true );
$plugins_ini = new ConfigMagik($basedir."plugins.ini");
foreach ($plugins_ini->listSections() as $plugin){
	//if (! is_dir($basedir."plugins/".$secs))
	$level= (int) $plugins_ini->get( 'level', $plugin);
	//if ($level == 0 ){$level=10;}
	if ($user_level >= $level){
	
	$Config = new ConfigMagik($basedir."plugins/".$plugin."/plugin.ini", true, true );
	$pfile = $Config->get( 'file', $page_section);
	$plink = $Config->get( 'link', $page_section);
	$plink_name = $Config->get( 'link_name', $page_section);
	$ptarget=$Config->get( 'link_target', $page_section);
        if ($pfile != ""){
		
		include("plugins/".$plugin."/".$pfile);
	}
	
        if ($plink != ""){
		if ($site_id != ""){$query_string = "?site=".$site_id;}else{$query_string="";}
		if ($ptarget == "_new"){
			echo "<a href=\"@\" onclick=\"window.open('plugins/".$plugin."/".$plink.$query_string;
			echo "','','width=575,height=725,resizable=yes,scrollbars=yes,status=yes'); return false;\" >".$plink_name."</a><br>";
		}elseif ($ptarget == "_message"){
			echo "<a href=\"@\" onclick=\"return open_message('".$plink_name."',";
			echo "'plugins/".$plugin."/".$plink.$query_string ."')\" >".$plink_name."</a>";
		}else{
			//echo "<a target=\"".$ptarget."\" href=\"plugins/".$plugin."/".$plink.$query_string ."\" >".$plink_name."</a><br>";
			echo "<a target=\"".$ptarget."\" href=\"".$plink.$query_string ."\" >".$plink_name."</a><br>";
		}
        }
	}
}
	//echo "<pre>";
	//$dummy=exec('find /usr/local/apache/htdocs/proedgenetworks/netzlive/plugins -mount -maxdepth 1 -type d  -print 2>/dev/null');
//echo  $dummy ."</pre>";
//	$dum=preg_replace("/ /","/<br>/",$dummy);
//	echo $dum;
	//return "<a href=''>".$pluginname."</a>";
}
function verify_ip($ip){
	$error="";
	//$ip=$argv[1];
	if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip)){
	$ip_parts=explode("\.",$ip);
	$octet_count= count($ip_parts);
        	for ($i = 0; $i < $octet_count; $i++){
        	        if ($ip_parts[$i] > 254) {$error="Invalid ";}
        	}
        	if ($ip=="0.0.0.0"){$error="Invalid ";}
	

        	if ($error==""){
        	        echo "hit\n";
        	}else{
        	        echo $error;
        	}
	}else{
	        echo "Not valid\n";
	}
}
function Display_name($feild){
	$conns =  mysqli_connect(NETZ_DB_SERVER,  NETZ_DB_USERNAME,  NETZ_DB_PASSWORD,NETZ_DATABASE);
	if (!$conns) {
	   die('Could not connect: ' . mysqli_error());
	}

	$query="SELECT * FROM NAME_MAPING WHERE DB_FIELD_NAME = '".$feild."'";
	$result=mysqli_query($conns, $query);
	$row = mysqli_fetch_assoc($result);
	if (is_array($row)){
	//if (trim($row['DISPLAY_NAME'] == "")){
		if ($row['DISPLAY_NAME'] == ""){
			return $feild;
		}else{
			return $row['DISPLAY_NAME'];
		}
	}else{
		return $feild;
	}


}
/**
 * Calculates the difference for two given dates, and returns the result
 * in specified unit.
 *
 * @param string    Initial date (format: [dd-mm-YYYY hh:mm:ss], hh is in 24hrs format)
 * @param string    Last date (format: [dd-mm-YYYY hh:mm:ss], hh is in 24hrs format)
 * @param char    'd' to obtain results as days, 'h' for hours, 'm' for minutes, 's' for seconds, and 'a' to get an indexed array of days, hours, minutes, and seconds
 *
 * @return mixed    The result in the unit specified (float for all cases, except when unit='a', in which case an indexed array), or null if it could not be obtained
 */
function getDateDifference($dateFrom, $dateTo, $unit = 'd')
{
   $difference = null;

   $dateFromElements = explode(' ', $dateFrom);
   $dateToElements = explode(' ', $dateTo);

   $dateFromDateElements = explode('-', $dateFromElements[0]);
   $dateFromTimeElements = explode(':', $dateFromElements[1]);
   $dateToDateElements = explode('-', $dateToElements[0]);
   $dateToTimeElements = explode(':', $dateToElements[1]);

   // Get unix timestamp for both dates

   $date1 = mktime($dateFromTimeElements[0], $dateFromTimeElements[1], $dateFromTimeElements[2], $dateFromDateElements[1], $dateFromDateElements[0], $dateFromDateElements[2]);
   $date2 = mktime($dateToTimeElements[0], $dateToTimeElements[1], $dateToTimeElements[2], $dateToDateElements[1], $dateToDateElements[0], $dateToDateElements[2]);

   if( $date1 > $date2 )
   {
       return null;
   }

   $diff = $date2 - $date1;

   $days = 0;
   $hours = 0;
   $minutes = 0;
   $seconds = 0;

   if ($diff % 86400 <= 0)  // there are 86,400 seconds in a day
   {
       $days = $diff / 86400;
   }

   if($diff % 86400 > 0)
   {
       $rest = ($diff % 86400);
       $days = ($diff - $rest) / 86400;

       if( $rest % 3600 > 0 )
       {
           $rest1 = ($rest % 3600);
           $hours = ($rest - $rest1) / 3600;

           if( $rest1 % 60 > 0 )
           {
               $rest2 = ($rest1 % 60);
               $minutes = ($rest1 - $rest2) / 60;
               $seconds = $rest2;
           }
           else
           {
               $minutes = $rest1 / 60;
           }
       }
       else
       {
           $hours = $rest / 3600;
       }
   }

   switch($unit)
   {
       case 'd':
       case 'D':

           $partialDays = 0;

           $partialDays += ($seconds / 86400);
           $partialDays += ($minutes / 1440);
           $partialDays += ($hours / 24);

           $difference = $days + $partialDays;

           break;

       case 'h':
       case 'H':

           $partialHours = 0;

           $partialHours += ($seconds / 3600);
           $partialHours += ($minutes / 60);

           $difference = $hours + ($days * 24) + $partialHours;

           break;

       case 'm':
       case 'M':

           $partialMinutes = 0;

           $partialMinutes += ($seconds / 60);

           $difference = $minutes + ($days * 1440) + ($hours * 60) + $partialMinutes;

           break;

       case 's':
       case 'S':

           $difference = $seconds + ($days * 86400) + ($hours * 3600) + ($minutes * 60);

           break;

       case 'a':
       case 'A':

           $difference = array (
               "days" => $days,
               "hours" => $hours,
               "minutes" => $minutes,
               "seconds" => $seconds
           );

           break;
   }

   return $difference;
}
function get_column_list($table){
// Create connection
$conn = mysqli_connect(NETZ_DB_SERVER,  NETZ_DB_USERNAME,  NETZ_DB_PASSWORD,NETZ_DATABASE);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_error());
}

$sql = "Show columns FROM ".$table;
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        $name_map[$row["Field"]] = Display_name($row["Field"]);
    }
} else {
    return $name_map["empty"] = "error";
}
mysqli_close($conn);
return $name_map;
}
function run_netz_query($query){
	$conn = mysqli_connect(NETZ_DB_SERVER,  NETZ_DB_USERNAME,  NETZ_DB_PASSWORD,NETZ_DATABASE);
	// Check connection
	if (!$conn) {
	    die("Connection failed: " . mysqli_error());
	}
	$numrows=0;
	$rows = array();
	if ($result=mysqli_query($conn,$query)){
		while($rows[] = mysqli_fetch_assoc($result));
		array_pop($rows);  // pop the last row off, which is an empty row
		$numrows=mysqli_num_rows($result);
	}
	mysqli_close($conn);	
	// check if there is one return and return just that index
	if ($numrows < 2 && isset($rows[0])){return $rows[0];}else{return $rows;}
}

function query_num_rows($array) {
    if (is_array($array)) {
        // Check if it's a multi-row result (array of arrays)
        if (!empty($array) && isset($array[0]) && is_array($array[0])) {
            return count($array); // Correctly count rows
        }
        return 1; // Single row returned as associative array
    }
    return 0; // Not an array, return 0
}


function get_site_log_count($site, $daysback) {
    $conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD, NETZ_DATABASE);
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $back = mktime(0, 0, 0, date("m"), date("d") - $daysback, date("Y"));
    $back = date("Y-m-d G:i:s", $back);

    $query = "SELECT * FROM ALERTLOGS WHERE SITE_ID = '".$site."' AND CHECK_DATE_TIME >= '".$back."' ORDER BY CHECK_DATE_TIME DESC";
    $result = mysqli_query($conn, $query);

    $count = ($result) ? mysqli_num_rows($result) : 0;

    mysqli_close($conn);
    return $count;
}
?>
