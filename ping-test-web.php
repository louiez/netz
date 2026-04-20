#!/usr/bin/php -q
<?php
include('lmz-functions.php');
    // Checksum calculation function
//$site=$argv[1];
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


//function pingsite($s)
//{
	//  If IP is empty just exit and return the -1
        if ($argc > 1){
        	$site=$argv[1];
	}else {
		echo "-1"; 
		exit();
	}
	$site=$argv[1];
	//echo $site;
	//echo $argv[0];
	//$site=$s;
    	// Making the package
    	$type= "\x08";
    	$code= "\x00";
	$checksum= "\x00\x00";
	$identifier = "\x01\x00";
	// Create a random seq number
	$seqNumber = dechex (rand(0, 14)).dechex (rand(0, 14));
	//$seqNumber = "\x03\x01";
	$data= "netzmonitor";
	$package = $type.$code.$checksum.$identifier.$seqNumber.$data;
	$checksum = icmpChecksum($package); // Calculate the checksum
	$package = $type.$code.$checksum.$identifier.$seqNumber.$data;
    	// And off to the sockets
	$socket = socket_create(AF_INET, SOCK_RAW, 1);
	//socket_set_block($socket);
	// set the socket read below to timeout
	socket_set_option(
  		$socket,
  		SOL_SOCKET,  // socket level
		SO_RCVTIMEO, // timeout option
		array(
			"sec"=>4, // Timeout in seconds
			"usec"=>0  // I assume timeout in microseconds
			)
		);
 

   	//$socsucess=@socket_connect($socket, $site, null);
	$socsucess=@socket_connect($socket, $site, 1000);
	//$socerr=socket_last_error($socket);
	//echo socket_strerror($socerr)."\n";
    	// If you're using below PHP 5, see the manual for the microtime_float
    	// function. Instead of just using the m
    	//     icrotime() function.
    	if ($socsucess === true)
	{
		$startTime = microtime_float(true);
    		socket_send($socket, $package, strLen($package), 0);
    		if (@socket_read($socket, 100)) {
			$endTime= round((microtime_float(true) - $startTime) * 1000);
    			//echo $endTime .' ms' . "\n";
			//return $endTime;
			echo $endTime;
    		}
		else
		{
		$socerr=socket_last_error($socket);
		//echo "error num ".$socerr."\n";
		$socerrstr=socket_strerror($socerr);
		//return "-1";
		echo "-1";

		}
	}
	else
	{
		//return "-1";
		echo "-1";
	}
    socket_close($socket);
//}
//pingsite($argv[1]);
?>
