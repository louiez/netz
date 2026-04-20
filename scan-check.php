#!/usr/bin/php -q
<?php
include('site-monitor.conf.php');
set_time_limit(600);

mysql_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD);
@mysql_select_db(NETZ_DATABASE) or die( "Unable to select database");

$query="SELECT * FROM ".SITE_INFO_TABLE." WHERE ACTIVE_DATE = 0 OR ACTIVE_DATE IS NULL AND CLOSE_DATE IS NULL ORDER BY SITE_ID ASC";
$header="<html><head><title>New Sites</title></head><body>";
$result=mysql_query($query);
$returnstr = "";
while ($row = mysql_fetch_assoc($result))
{

        //$ipfeild =$row['LAN_GATEWAY'] ;
        //if ($ipfeild == ""){$ipfeild=$db_ip_feild;}
        $cfgServer = $row['LAN_GATEWAY'];
        $store=$row['SITE_ID'];
        if ($cfgServer != "" )
        {
//                echo "checking....". $store." ".$cfgServer."\n";
                $sitertn = exec($basedir.'fping/fping -a -t 1000 '.$cfgServer.' 2>/dev/null');
                if ($sitertn != ""){
                        //$returnstr .= $row['SITE_ID']." ".$cfgServer." Alive<br>";
                        $returnstr .="<a href=\"ops.php?site=". $row['SITE_ID']."\">".$row['SITE_ID']."</a> ".$cfgServer." Alive<br>";
                        //echo "<a href=\"ops.php?site=". $row['SITE_ID']."\">".$row['SITE_ID']."</a> ".$cfgServer." Alive\n";
                }

        }
}
mysql_close();
echo $returnstr;
$trailer="</body></html>";
$filename = $basedir."sites-new.html";


if (!$handle = fopen($filename, 'w')) {
        echo "Cannot open file ($filename)";
        exit;
}

fwrite($handle, $header);
fwrite($handle, $returnstr); 
fwrite($handle, $trailer);

fclose($handle);
?>
