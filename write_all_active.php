<?php
  extract($GLOBALS);

include('site-monitor.conf.php');
  $OBJCONN1=mysql_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD);
  mysql_select_db(NETZ_DATABASE,$OBJCONN1);
//grab the whole database
  $SQL1="SELECT * FROM SITEDATA";
//sql1 = sql1 & " WHERE [NAPA All].[VPN Active Date] Is Not Null ORDER BY [NAPA All].[STore Number]"

  $SQL1=$SQL1." WHERE ((ACTIVE_DATE Is Not Null) OR (ORDER_DATE Is Not Null)) ORDER BY SITE_ID";


  $result=mysql_query($SQL1);


//  $FILENAME= $basedir . "wan_all.txt";
$FILENAME="/usr/netz/wan_all.txt"
//~~ create the File


$MyFile=fopen($FILENAME, "w");

exec('chmod 777 '.$FILENAME);

while ($row = mysql_fetch_assoc($result))
  {

    if ((substr($row["LAN_IP"],strlen($row["LAN_IP"])-(4))!="1.99")
        && ($row["LAN_IP"]!="")
        && (substr($row["LAN_IP"],strlen($row["LAN_IP"])-(4))!="2.99")
        && (substr($row["SITE_ID"],strlen($row["SITE_ID"])-(4))!="vsat"))
    {

$mystr=substr($row["SITE_ID"],0,3)."00".substr($row["SITE_ID"],strlen($row["SITE_ID"])-(4)).chr(124).$row["LAN_IP"]."\n";
$mylen=strlen($mystr);

fputs($MyFile,$mystr,$mylen);
    }


  }
//fs.CopyFile "D:\InetPub\wwwroot\GPC_WAN\vpn_ops\wan_all.txt", "h:\wan_all.txt"




?>
