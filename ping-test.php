#!/usr/bin/php -q
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
include_once('lmz-functions.php');
include_once('site-monitor.conf.php');
    // Checksum calculation function
function icmpChecksum($data)
{
	if (strlen($data)%2)
		$data .= "\x00";
    		$bit = unpack('n*', $data);
    		$sum = array_sum($bit);
    
    		while ($sum >> 16)
    			$sum = ($sum >> 16) + ($sum & 0xffff);
    			return pack('n*', ~$sum);
}


function pingsite($s)
{
	//$site=$argv[1];
	$site=$s;
    	// Making the package
    	$type= "\x08";
    	$code= "\x00";
	$checksum= "\x00\x00";
	$identifier = "\x01\x00";
	$seqNumber = "\x01\x01";
	$data= "MonPing";
	$package = $type.$code.$checksum.$identifier.$seqNumber.$data;
	$checksum = icmpChecksum($package); // Calculate the checksum
	$package = $type.$code.$checksum.$identifier.$seqNumber.$data;
    	// And off to the sockets
	$socket = socket_create(AF_INET, SOCK_RAW, 1);
	socket_set_block($socket);
	// set the socket read below to timeout
	socket_set_option(
  		$socket,
  		SOL_SOCKET,  // socket level
		SO_RCVTIMEO, // timeout option
		array(
			"sec"=>2, // Timeout in seconds
			"usec"=>0  // I assume timeout in microseconds
			)
		);
 

   	$socsucess=@socket_connect($socket, $site, null);
	//$socerr=socket_last_error($socket);
	//echo socket_strerror($socerr)."\n";
    	// If you're using below PHP 5, see the manual for the microtime_float
    	// function. Instead of just using the m
    	//     icrotime() function.
    	if ($socsucess)
	{
		$startTime = microtime_float(true);
    		socket_send($socket, $package, strLen($package), 0);
		$test=@socket_read($socket, 35, $PHP_BINARY_READ);
    		//if (@socket_read($socket, 255)) {
		if ($test) {
			//system('echo $site $test >> /usr/local/apache/htdocs/proedgenetworks/netz/datadelete');
			//$pos=strpos($test,'MonPing');
				//if ($pos)
				//{
					//echo "\n> $pos <\n";
					$endTime= round((microtime_float(true) - $startTime) * 1000);
    					//echo $endTime .' ms' . "\n";
					return $endTime;
				//}
				//else
				//	{return -2;
				//}
    		}
		//$socerr=socket_last_error($socket);
		//echo "error num ".$socerr."\n";
		//echo socket_strerror($socerr)."\n";
		return -1;
	}
	else
	{
		return -1;
	}
    socket_close($socket);
}
?>
