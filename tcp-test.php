#!/usr/bin/php -q
<?php
//require('ping-test.php');
//include('site-monitor.conf.php');
//$monhost = $argv['1'];
//$monport = $argv['2'];
function safe_feof($fp, &$start = NULL) {
 $start = microtime(true);

 return feof($fp);
}
function tcp_mon($ssl=0,$monhost,$monport,$contenttest,$monpage,$montimeout)
{
$testdata = "";
if ($monpage == ""){$monpage = "/";}
//$monport = "8081";
//echo $monhost."\n";
//echo $monport."\n";
if ($ssl==1){$ssl="ssl://";}else{$ssl="";}
$stime = microtime_float(true);
                $fp = @fsockopen($ssl.$monhost, $monport, $errno, $errstr, $montimeout);
                if (!$fp) {
                        return array(-1,0,$errstr,$errno,strlen($testdata));
                } else {
                //stream_set_timeout($fp, 2);
                       	//$out = "GET / HTTP/1.0\r\n";
                        $out = "GET ".$monpage." HTTP/1.0\r\n";
                        $out .= "Host: ".$monhost."\r\n";
			$out .= "User-Agent: NETz monitor\r\n";
                        $out .= "Connection: Close\r\n\r\n//\r\n";
                        fwrite($fp, $out);
			stream_set_blocking($fp, FALSE );
                        stream_set_timeout($fp, 2);
			$info = stream_get_meta_data($fp);
			$timeout = (microtime(true) + 2);
                        while (!@feof($fp)&& microtime(true) < $timeout) {
                                $testdata = $testdata . fgets($fp, 128);
                        }
			if (!@feof($fp)){
				return array(-1,0,"Function Forced Timeout ".$errstr,$errno,"strlen ".strlen($testdata));
			}else{
                        	$etime= round((microtime_float(true) - $stime) * 1000);
				if ($contenttest != ""){
                        		$contentcheckpos = stristr($testdata,$contenttest);
				}else{
					$contentcheckpos = "";
				}
			}
                        fclose($fp);
			//if ($errstr == ""){$errstr = substr(stristr($testdata,"http"),0,30);}
			//$errstr = $errstr . substr(stristr($testdata,"http"),0,100) ;
		//======================//
		// Extract the header	//
		//==============================================//
			$xn=strpos($testdata,"\r\n\r\n");	//
			$errstr = substr($testdata,0,$xn) ;	//
		//==============================================//	
                        if ($contentcheckpos === FALSE){ $contentcheckpos = 0;}else {$contentcheckpos = 1 ;}
                        // Return the time to complete, if content was found, any error string and number
                        return array($etime,$contentcheckpos,$errstr,$errno,strlen($testdata));
                }
}
?>
