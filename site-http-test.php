<?php
echo "<html><body>";

require('lmz-functions.php');

$testdata = "";
if ($monpage == ""){$monpage = "/";}



$stime = microtime_float(true);
                        $monhost= $_GET['ip'];
                        $monport= $_GET['port'];
                        $contenttest = $_GET['content'];
                        $page= $_GET['page'];
                        $timeout= $_GET['timeout'];
			$ssl=$_GET['ssl'];
	if ($timeout>0){$timeout=$timeout/1000;}else{$timeout=2000;}
if ($ssl=="true"){$ssl="ssl://";}else{$ssl="";}
if ($page==""){$page="/";}
echo "Testing  ".$ssl. $monhost.$page . " with Content ". $contenttest."<br>";
echo $ssl.$monhost.$page . " p=".$monport ;
                $fp = @fsockopen($ssl.$monhost, $monport, $errno, $errstr, $timeout);
                if (!$fp) {
                        //return array(-1,0,$errstr,$errno,strlen($testdata));
                        echo "Time: -1<br>";
                        echo "Success: 0<br>";
                        echo "Error String: " . $errstr . "<br>";
                        echo "Error Number: " . $errno . "<br>";
                        echo "Packet Length: " . strlen($testdata) . "<br>";
                } else {
                //stream_set_timeout($fp, 2);
                        //$out = "GET / HTTP/1.0\r\n";
                        $out = "GET ".$page." HTTP/1.0\r\n";
                        $out .= "Host: ".$monhost."\r\n";
                        $out .= "User-Agent: NETz monitor\r\n";

                        $out .= "Connection: Close\r\n\r\n";
echo "<pre>****** Header Sent ******\n" . $out . "*************************</pre>";

$data = "foo=" . urlencode("Value for Foo") . "&bar=" . urlencode("Value for Bar");
$out .= $data."\r\n\r\n";
                        fwrite($fp, $out);
                        stream_set_timeout($fp, 2);
                        while (!@feof($fp)) {
                                $testdata = $testdata . fgets($fp, 128);
                        }
                        $etime= round((microtime_float(true) - $stime) * 1000);
                        if ($contenttest != ""){
                                $contentcheckpos = stristr($testdata,$contenttest);
                        }else{
                                $contentcheckpos = "dummy data";
                        }
//echo "contentcheckpos: ".$contentcheckpos."\n";
                        fclose($fp);
                        //if ($errstr == ""){$errstr = substr(stristr($testdata,"http"),0,30);}
                        //$errstr = $errstr . substr(stristr($testdata,"http"),0,100); // . "XX";
                        if ($contentcheckpos === FALSE){ $contentcheckpos = 0;}else {$contentcheckpos = 1 ;}
                        // Return the time to complete, if content was found, any error string and number
                        //return array($etime,$contentcheckpos,$errstr,$errno,strlen($testdata));
                        echo "Time: " . $etime . "ms<br>";
                        echo "Success: " . $contentcheckpos . "<br>";
                        echo "Error String: " . $errstr . "<br>";
                        echo "Error Number: " . $errno . "<br>";
                        echo "Packet Length: " . strlen($testdata) . "<br>";
$Headerlen=strpos($testdata,"\r\n\r\n");
// add the 4 bytes for the return/new line abouve
$Headerlen=$Headerlen+4;
echo "Header Length ". $Headerlen;
 $headerreturn=substr($testdata,0,$Headerlen);
$cl=strpos($headerreturn,"\r\n");
$Statuscode=substr($headerreturn,0,$cl);
//echo $Statuscode;
$Statuscode2=explode(" ",$Statuscode);
echo "<br> Return Code ". $Statuscode2[1];
//echo "<pre>";
//print_r($Statuscode2);
//echo "</pre>";
//echo count($Statuscode2);
//echo "<pre>XXX". $Statuscode2[1]."XXX</pre>";
//echo "<pre>". $headerreturn ."</pre>";
//echo $headerreturn;
echo "<pre>****** Header Received ******\n" . $headerreturn  . "\n*************************</pre>";
// Page content
echo "<xmp>" .substr($testdata,$Headerlen, strlen($testdata)-$Headerlen). "</xmp>";


echo "</body></html>";

                }

?>
