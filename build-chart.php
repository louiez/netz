<?php
ob_start();
include_once('site-monitor.conf.php');
if (isset($_GET['site'])){ $site=$_GET['site']; }else{ $site=""; }
if (isset($_GET['back'])){ $daysback=$_GET['back'];}else{ $daysback=""; }
if (isset($_GET['hourly'])){ $hourly=$_GET['hourly']; }else{ $hourly=""; }
if (isset($_GET['scale'])){ $scale= $_GET['scale']; }else{ $scale=""; }

switch ($scale) {
    case "0":
        $time_scale="%m/%d";
        break;
    case "1":
        $time_scale="%j";
        break;
    case "2":
        $time_scale="wk%W";
        break;
    case "3":
        $time_scale="AUTO";
        break;
    default:
	$time_scale="AUTO";
	break;
}




//if ($daysback == "")$daysback=1;
//if ($daysback == "0")$daysback="1";
if (isset($_GET['days'])){ $numdays=$_GET['days']; }else{ $numdays=""; }
if (isset($_GET['size'])){ $chartsize=$_GET['size']; }else{ $chartsize=""; }

        if ($daysback == "" && $hourly == ""){
		$backlen  =86400; // 86400 seconds 
        }
	elseif ($hourly != ""){
		 $backlen = $hourly * 3600;
	}
        else{
		$backlen  = $daysback * 86400;
        }
//if ($numdays==""){$numdays=$backlen/3600;}
$display_days= $numdays;
if ($display_days == "" || $display_days == 0){$display_days=$daysback;}
$options="";
if ($time_scale != "AUTO"){
        if ($display_days < 20){$options= "--x-grid HOUR:8:DAY:1:DAY:1:0:".$time_scale;}
        elseif ($display_days < 50){$options= "--x-grid HOUR:8:DAY:1:DAY:4:0:".$time_scale;}
        elseif ($display_days < 100){$options= "--x-grid HOUR:16:DAY:2:DAY:6:0:".$time_scale;}
        elseif ($display_days < 200){$options= "--x-grid DAY:1:DAY:4:DAY:10:0:".$time_scale;}
        elseif ($display_days < 250){$options= "--x-grid DAY:1:DAY:5:DAY:12:0:".$time_scale;}
        elseif ($display_days < 300){$options= "--x-grid DAY:1:DAY:6:DAY:14:0:".$time_scale;}
        elseif ($display_days < 350){$options= "--x-grid DAY:1:DAY:7:DAY:16:0:".$time_scale;}
	elseif ($display_days < 400){$options= "--x-grid DAY:1:DAY:8:DAY:18:0:".$time_scale;}
        elseif ($display_days < 450){$options= "--x-grid DAY:1:DAY:8:DAY:20:0:".$time_scale;}
        elseif ($display_days < 500){$options= "--x-grid DAY:1:DAY:9:DAY:22:0:".$time_scale;}
        elseif ($display_days < 550){$options= "--x-grid DAY:1:DAY:10:DAY:24:0:".$time_scale;}
        elseif ($display_days < 650){$options= "--x-grid DAY:1:DAY:10:DAY:26:0:".$time_scale;}
	else   {$options= "--x-grid MONTH:1:MONTH:1:MONTH:2:0:".$time_scale;}
}
        if ($chartsize == "small"){
		$height = "150";
		$width = "800";     
		$options='-l -1  -X 0 -r';
	}  
	elseif ($chartsize == "tiny"){
                $height = "21";
                $width = "200";	
		$options='-l -1 -X 0 --no-legend --font DEFAULT:4:';
        }elseif ($chartsize == "normal"){
                $height = "400";
                $width = "800";
        }elseif ($chartsize == "large"){
                $height = "600";
                $width = "1024";
        }elseif ($chartsize == "xlarge"){
                $height = "700";
                $width = "1280";
        }elseif ($chartsize == "xxlarge"){
                $height = "700";
                $width = "1600";
        }else{
                $height = "400";
                $width = "800";
        }

/*

        }else{
                $height = "300";
                $width = "800";		
		$options='-l -1  -X 0 -r';
        }
*/

// set the number of days to include
if ((float) $numdays != 0) {
        $endtime= $backlen - ((float) $numdays*24*60*60);
}else{
        $endtime=((float) $moncycleinterval*60*2);
}

//**************************************************************//
// cleanup valid site names to valid filenames                  //
// NETz allows names that may not be legal as file names        //
//**************************************************************//
$allowed = '/[^a-z0-9\\.\\-\\_\\\\]/i';                         //
$rrdfilename=preg_replace($allowed,"",$site);                   //
$rrdfilename= $basedir.'rrd/'.$rrdfilename.'.rrd';              //
//**************************************************************//

if ($chartsize == "tiny"){
$ccmd='/usr/bin/rrdtool graph - \
         --start -'.$backlen.' \
        --end "now-'.$endtime.'" \
        --height="'.$height.'" \
        --width="'.$width.'" '.$options.' \
        "DEF:rtime='.$rrdfilename.':rtime:AVERAGE" \
        "LINE.75:rtime#0000FF" \
        "CDEF:low=rtime,300,LE,rtime,300,IF" "AREA:low#009911" \
        "CDEF:high=rtime,300,GT,rtime,300,-,0,IF" "STACK:high#FF3300" \
        "CDEF:down=rtime,-1,EQ,-1,0,IF" "AREA:down#FF0000"';
}else{
$ccmd='/usr/bin/rrdtool graph - \
        -t "'.$site.' Ping" -v "Time in ms" \
	--imgformat=PNG \
         --start -'.$backlen.' \
        --end "now-'.$endtime.'" \
        --height="'.$height.'" \
        --width="'.$width.'" '.$options.' \
        "DEF:rtime='.$rrdfilename.':rtime:AVERAGE" \
        "GPRINT:rtime:LAST:Last\: %5.2lf ms\n" \
        "GPRINT:rtime:MIN:Min\: %5.2lf ms\n" \
        "GPRINT:rtime:MAX:Max\: %5.2lf ms\n" \
        "GPRINT:rtime:AVERAGE:Avg\: %5.2lf ms" \
	"LINE1.5:rtime#000099" \
	"CDEF:low=rtime,300,LE,rtime,300,IF" "AREA:low#009911" \
        "CDEF:high=rtime,300,GT,rtime,300,-,0,IF" "STACK:high#FF3300" \
	"CDEF:down=rtime,-1,EQ,-1,0,IF" "AREA:down#FF0000"';
}
header("Content-type: image/png"); 
ob_end_clean();
passthru($ccmd);
?>
