<?php
include_once("auth.php");
include('site-monitor.conf.php');
?>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">

<title>Site Charts</title>
<?php
$site=$_GET['site'];
$chart=$_GET['chart'];
$group=$_GET['group'];
$size=$_GET['size'];
$return_to=$_GET['return'];
//if ($size=""){$size=$_POST['size'];}
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>New Page 1</title>
<script type="text/javascript">
function displayimage()
{
	back = document.myform.back.value;
	B1 = document.myform.B1[0].checked;
	days = document.myform.days.value;
	D1 = document.myform.D1[0].checked;
	document.getElementById('img').src ="";
	
	if (B1){	
		document.getElementById('img').src = 'build-chart.php?back='+back+'&site=<?php echo $site ; ?>';
		if (days != ""){
			document.getElementById('img').src = 'build-chart.php?back='+back+'&days='+days+'&site=<?php echo $site ; ?>';
		}
	}else{
		document.getElementById('img').src = 'build-chart.php?hourly='+back+'&days='+days+'&site=<?php echo $site ; ?>';
	}

}
</script>
</head>

<body>
<script language="JavaScript1.2"  src="menulz.js"> </script>
<div style="margin-left:20px">
<h2> Monitor Charts for <?php echo $site ; ?> </h2>
<p>
<?php
echo "<a href='".$return_to."?site=".$site."'>Back to ".$site."</a>";
/*
	if ($_SESSION['accesslevel'] >= 7 ){
             echo "<a href='ops.php?SITE_ID=".$site."'>Back to ".$site."</a>";
      	}else{
        	echo  "<a href='support.php?site=".$site."'>Back to ".$site."</a>";
        }
*/
?>
<br>
<br>
<?php
echo '<a href="@" onclick="window.open(\'chart-pop.php?hourly=1&site='.$site;
echo '\',\'\',\'width=640,height=480,resizable=yes\'); return false"';
echo '>Hour<br>';
echo '<img id="img" src="build-chart.php?size=tiny&hourly=1&site='.$site.'"></a><br><br>';
?>

<?php
echo '<a href="@" onclick="window.open(\'chart-pop.php?hourly=4&site='.$site;
echo '\',\'\',\'width=640,height=480,resizable=yes\'); return false"';
echo '>4 Hours<br>';
echo '<img id="img" src="build-chart.php?size=tiny&hourly=4&site='.$site.'"></a><br><br>';
?>

<?php
echo '<a href="@" onclick="window.open(\'chart-pop.php?hourly=24&site='.$site;
echo '\',\'\',\'width=640,height=480,resizable=yes\'); return false"';
echo '>24 Hours<br>';
echo '<img id="img" src="build-chart.php?size=tiny&hourly=24&site='.$site.'"></a><br><br>';
?>

<?php
echo '<a href="@" onclick="window.open(\'chart-pop.php?back=7&site='.$site;
echo '\',\'\',\'width=640,height=480,resizable=yes\'); return false"';
echo '>Week<br>';
echo '<img id="img" src="build-chart.php?size=tiny&back=7&site='.$site.'"></a><br><br>';
?>
<br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="site-charts.php?chart=2g&group=<?php echo $group ; ?>&site=<?php echo $site ; ?>&return=<?php echo $return_to ; ?>">Group Last 4 Hours</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="site-charts.php?chart=3g&group=<?php echo $group ; ?>&site=<?php echo $site ; ?>&return=<?php echo $return_to ; ?>">Group Today</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="site-charts.php?chart=4g&group=<?php echo $group ; ?>&site=<?php echo $site ; ?>&return=<?php echo $return_to ; ?>">Group week</a>
<!--
<form action="site-charts.php" method="post">
<br>Large <input type="radio" name="size" value="large" id="large" onclick='alert(this.value)'>
<br>XLarge <input type="radio" name="size" value="xlarge"  id="large" onclick='alert(this.value)'>
<br>XXLarge <input type="radio" name="size" value="xxlarge"  id="large" onclick='alert(this.value)'>
<form>
-->
</p>
</div>
<br><br>
<!-- Testing user selectable charts - issues with hours back in build-chart.php
<form method="POST" action="build-chart.php" name="myform">
	<p align="left">Go Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="back" id="back" size="7">
	<input type="radio" value="days" name="B1" CHECKED> Day(s)&nbsp;&nbsp;
	<input type="radio" name="B1" value="hours">Hours&nbsp; <br>
	
	Enter number of Days/hours to display&nbsp;&nbsp;
	<input type="text" name="days" id="days" size="7">
	<input type="radio" value="days" checked name="D1"> Day(s)&nbsp;&nbsp;
	<input type="radio" name="D1" value="hours">Hours </p>
	<p><input  class="button" type="button" value="Submit" name="B1" onclick="return displayimage();"></p>
</form>
-->

<div style="margin-left:20px">

<?php
if ($chart == "1"){ ?>
        <img id="img" src="build-chart.php?hourly=1&site=<?php echo $site ; ?>&size=<?php echo $size ; ?>">
<?php } ?>
<?php
if ($chart == "1g"){ ?>
        <img id="img" src="build-chart-group.php?hourly=1&group=<?php echo $group ; ?>&size=<?php echo $size ; ?>">
<?php } ?>


<?php
if ($chart == "2"){ ?>
        <img id="img" src="build-chart.php?hourly=4&site=<?php echo $site ; ?>&size=<?php echo $size ; ?>">
<?php } ?>
<?php
if ($chart == "2g"){ ?>
        <img id="img" src="build-chart-group.php?hourly=4&group=<?php echo $group ; ?>&size=<?php echo $size ; ?>">
<?php } ?>


<?php
if ($chart == "3"){ ?>
        <img id="img" src="build-chart.php?back=0&site=<?php echo $site ; ?>&size=<?php echo $size ; ?>">
<?php } ?>
<?php
if ($chart == "3g"){ ?>
        <img id="img" src="build-chart-group.php?back=0&group=<?php echo $group ; ?>&size=<?php echo $size ; ?>">
<?php } ?>

<?php
if ($chart == "4"){ ?>
        <img id="img" src="build-chart.php?back=7&site=<?php echo $site ; ?>&size=<?php echo $size ; ?>">
<?php } ?>
<?php
if ($chart == "4g"){ ?>
        <img id="img" src="build-chart-group.php?back=7&group=<?php echo $group ; ?>&size=<?php echo $size ; ?>">
<?php } ?>

</div>
</body>

</html>
