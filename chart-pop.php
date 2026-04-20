<?php
include_once("auth.php");
include('write_access_log.php');
if (isset($_GET['site'])){ $site=$_GET['site']; }else{ $site=""; }

if (isset($_GET['back'])){ $daysback=$_GET['back']; }else{ $daysback=""; }
// see if someone passed something other than a number... and reset to blank
if (! is_numeric($daysback)){$daysback="";}

if (isset($_GET['hourly'])){ $hourly=$_GET['hourly']; }else{ $hourly=""; }

if (isset($_GET['size'])){ $chartsize=$_GET['size']; }else{ $chartsize=""; }
if ($chartsize== ""){$chartsize="normal";}

if (isset($_GET['days'])){ $window_days=$_GET['days']; }else{ $window_days=""; }
// see if someone passed something other than a number... and reset to blank
if (! is_numeric($window_days)){$window_days ="";}

// if days back is blank and the window is not... well... better at least make them the same
if ($daysback == "" && $window_days != ""){$daysback = $window_days;}
if ($daysback < $window_days){$window_days=$daysback;}

if (isset($_GET['scale'])){ $scale=$_GET['scale']; }else{ $scale=""; }
$s=$scale;
$s0="";
$s1="";
$s2="";
$s3="";
if ($s=="0"){$s0="CHECKED";}
elseif ($s=="1"){$s1="CHECKED";}
elseif ($s=="2"){$s2="CHECKED";}
elseif ($s=="3"){$s3="CHECKED";}

?>
<HTML>
<HEAD>
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
 <TITLE>Monitor Chart</TITLE>
        <?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
        <link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">
	<link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon" >
<script type="text/javascript" src="size_window.js"></script>
</HEAD>
<BODY onload='sizeToFit("show_div");'>
<!-- <a href="" onclick="window.close()">Close</a> -->
<div  id="show_div" 
      style=" position: absolute; 
      left: 0px; 
      top: 0px; 
      padding: 10px;" >

<img src='<?php echo "build-chart.php?hourly=".$hourly."&size=".$chartsize."&back=".$daysback."&days=".$window_days."&scale=".$scale."&site=". $site; ?>' >
<form action="chart-pop.php" method="GET">
Site ID<input id="text1" size="15" type="text" name="site" value="<?php echo $site ?>"> 
Days Back <input size="4" type="text" name="back" value="<?php echo $daysback ?>"> 
Window Days <input size="4" type="text" name="days" value="<?php echo $window_days ?>"> 
chart size <select name="size">
<option value="<?php echo $chartsize ?>" SELECTED><?php echo $chartsize ?></option>
<option value="small">Small</option>
<option value="normal">Normal</option>
<option value="large">Large</option>
<option value="xlarge">XLarge</option>
<option value="xxlarge">XXLarge</option>
</select><br>
<span style="text-weight:bold">Select Time scale </span>
<div style="border: white solid 1px; width:300;display : inline;">
<input type="radio" name="scale" value="0" <?php echo $s0; ?>> Day/Month
&nbsp;&nbsp;<input type="radio" name="scale" value="1" <?php echo $s1; ?>>Days
&nbsp;&nbsp;<input type="radio" name="scale" value="2" <?php echo $s2; ?>>Weeks
&nbsp;&nbsp;<input type="radio" name="scale" value="3" <?php echo $s3; ?>>Auto
</div>
&nbsp;&nbsp;<input id="button1" class="button" type="submit" >
</form> 
</div>
</BODY>
<script type="text/javascript">
//        sizeToFit("show_div");
 //       window.focus();
</script>        
</HTML>
