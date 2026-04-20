<?php
/*###############################################################
        NETz Network Management system                          #
        http://www.proedgenetworks.com/netz                     #
                                                                #
                                                                #
        Copyright (C) 2005-2006 Louie Zarrella                  #
        louiez@proedgenetworks.com                              #
                                                                #
        Released under the GNU General Public License           #
        Copy of License available at :                          #
        http://www.gnu.org/copyleft/gpl.html                    #
###############################################################*/
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL);

ob_start();
include_once('auth.php');
include_once('site-monitor.conf.php');
include_once('write_access_log.php');
include_once('lmz-functions.php');
$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);
$result = mysqli_query($conn,"select * from SITEDATA JOIN MONITORINFO USING(SITE_ID)");
if (!$result) {
   die('Query failed: ' . mysqli_error());
}

//mysqli_free_result($result);
?>

<?php
//+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-//
//	User Access code	//
// =====================================================================================================//
if ($_SESSION['accesslevel'] <= 1){									//
	echo "<html><head>";										//
	echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';	//
	echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';			//
	exit();												//
	}												//						
// =====================================================================================================//
/*
  * Get the last query serch criteria
*/
//$recallsaved=$_GET['queryname'];
$recallsaved = isset($_GET['queryname']) ? $_GET['queryname'] : "";

//echo "Saved = " . $recallsaved;
// Open last query
if (!isset($_GET['reset']) && $recallsaved == ""){
	$filename = $basedir."querys/". $_SESSION['user'].".last";
	$fp= @fopen($filename, "rw");
	if (!$fp){
		//echo "ouch";
	}else{
	$contents= fread($fp,filesize($filename));
	fclose($fp);
	$file_lines= explode("\n",$contents);
	$cnt=0;

	foreach ($file_lines as $line){
		$fieldata[$cnt] = explode(",",$line);
		$cnt++;
	}
	}
   // open saved query from link
}elseif ($recallsaved != ""){
	$filename = $basedir."querys/".$_SESSION['user']."/". $_SESSION['user'].".".$recallsaved;
//echo $filename;exit();
        $fp= fopen($filename, "rw");
        $contents= fread($fp,filesize($filename));
        fclose($fp);
        $file_lines= explode("\n",$contents);
        $cnt=0;

        foreach ($file_lines as $line){
                $fieldata[$cnt] = explode(",",$line);
                $cnt++;
        }
  // Else Reset the <username>.last file and reload the page
}else{
	$filename = $basedir."querys/". $_SESSION['user'].".last";
	if ($handle = fopen($filename, 'w')) {
        	for ($i=1; $i<=15; $i++){
			$stringdata = $stringdata .",,,,,\n";
        	}
	}
	fwrite($handle, $stringdata);
	fclose($handle);
	//header("Location:".$_SESSION['secure'] . $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	header("Location:".$_SERVER['PHP_SELF']);
	ob_end_flush();
	exit();
}
// See if the Delete was set with a query ID
//$deletequery=$_GET['delete'];
$deletequery= isset($_GET['delete']) ? $_GET['delete'] : "";
if ($deletequery != ""){
	// open the Key file and grab the contents
	$filename = $basedir."querys/".$_SESSION['user']."/". $_SESSION['user'].".key";
	if ($fp = fopen($filename, 'r')) {
        	$contents= fread($fp,filesize($filename));
        	fclose($fp);
        	$file_lines= explode("\n",$contents);
	}
	// now open the file in Append Mode
	if ($handle = fopen($filename, 'a')){
		// Clear the file
		ftruncate($handle,0);
		// go through each line and check if it matches the delete query id
        	foreach ($file_lines as $line){
                	$fieldata = explode("|",$line);
                	if ((trim($fieldata[1]) != trim($deletequery)) && trim($fieldata[1]) != ""){
				// write back to file if it is not the deleted key
			 	fwrite($handle,$fieldata[0].'|'.$fieldata[1]."\n");
			}
        	}
	}
	fclose($handle);
        // Delete Query criteria file
        unlink($basedir."querys/".$_SESSION['user']."/". $_SESSION['user'] . "." . trim($deletequery));

	//header("Location:".$_SESSION['secure'] . $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	header("Location: ".$_SERVER['PHP_SELF']);
	ob_end_flush();
}

// *********************************************//
//	Get the Field names			//
//	Only allow level 7 up see passwords	//
// *********************************************************************************************************************//
$i=0;
// store the number of cols
$num_cols=mysqli_num_fields($result);
// load all the col names int an array for sorting
$col_list=array();
/*
while ($i < $num_cols) {										//
	// Load an associative array with the Index as the Database field name
	// and the value the User defined DisplayName 
	// then sort this array by the DisplayName
	$col_list[mysqli_fetch_field($result, $i)->name]= Display_name(mysqli_fetch_field($result, $i)->name);
	$i++;
}
*/
$site_list=get_column_list("SITEDATA");
$mon_list=get_column_list("MONITORINFO");
$col_list= array_merge($site_list,$mon_list);

// sort the names
asort($col_list);

$i=0;
// create the option list
$option_lines="";
foreach($col_list as $key=>$value){
	// init variable
	//$option_lines="";
	// Check if it is a Password feild and if the user has high enough accesss to view them
	if (strpos(trim($key), 'PASSWORD')){
		if ($_SESSION['accesslevel'] >= 7 ){
			$option_lines .= '<option value="'.$key.'">';
			$option_lines .= $value.' </option>'; 
		}
	}else{
		$option_lines .= '<option value="'.$key.'">';
		$option_lines .= $value.' </option>';
	}
	$i++;
 }
//$col_list[$num_cols+1]="foo";
// ********************************************************************************
/*
  * Convert the Logic Values to human readable
*/
function convert_logic($value)
{
	switch ($value) {
		case "Like":
   			return "Like";
   			break;
		case "Not Like":
   			return "Not Like";
   			break;
		case "=":
   			return "= - Equals";
   			break;
		case "<>":
   			return "&lt;&gt; - Not Equal";
   			break;
		case "<":
   			return "&lt; -  Less than";
   			break;
		case ">":
   			return "&gt; -  Greater than";
   			break;
		case "<=":
   			return "&lt;= less or Equal to";
   			break;
		case ">=":
   			return "&gt;= Greater or Equal to";
   			break;
		case "Is Null":
   			return "Is Null";
   			break;
		case "Is Not Null":
   			return "Is Not Null";
   			break;
	}
}
function convert_sort($value)
{
        switch ($value) {
                case "DESC":
                        return "Descending";
                        break;
                case "ASC":
                        return "Ascending";
                        break;
	}
}
?>
<html><head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">

<title>Query Builder</title>

<script type="text/javascript">
	function CheckVisible(obj)
	{
		//alert(obj.name);
	}
function get_save_name(queryname)
{
	ans = prompt("enter Name",queryname);
	if ((ans != "") && (ans != null)){
		document.getElementById('savedquery').value = ans;
		if (ans != queryname){
			document.getElementById('savedqueryid').value = "";
		}
		document.myform.submit();
		return true;
	}else{ return false;}
	 return false;
}
</script>

</head>

<body style="font-size: 10px; font-family: Comic Sans MS" bgcolor="#C0C0C0">
<script type="text/javascript"  src="menulz.js"> </script>

<?php
// Adds menu items to side menu
 if ($_SESSION['accesslevel'] >= 9){
        echo '<script language="JavaScript1.2"  src="menu-data-rwa.js"> </script>';
 }elseif($_SESSION['accesslevel'] >= 4){
        echo '<script language="JavaScript1.2"  src="menu-data-rw.js"> </script>';
}else{
        echo '<script language="JavaScript1.2"  src="menu-data-ro.js"> </script>';
}
?>
<form method="POST" action="QueryDisplay.php" name="myform">
<font color="#0000FF" size="2"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
NETz Query Builder </b></font>
  <table border="1" cellspacing="1" width="100%" id="AutoNumber1" style="font-size: 10px" height="136">
  
    <tr>
      <td width="5%" height="18" align="center"></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">1</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">2</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">3</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">4</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">5</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">6</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">7</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">8</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">9</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">10</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">11</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">12</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">13</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">14</font></td>
      <td width="10%" height="18" align="center"> 
  <font color="#0000FF">15</font></td>
    </tr>
  
	<tr>
      		<td width="5%" height="10"><font color="#0000FF" size="2"><b>Field</b></font></td>
      		<td width="10%" height="10"> 
  			<font color="#0000FF">
			<select  size="1" name="Q1" id="Q1" onchange="javascript:document.myform.C1.checked = true"  >
				<option value="<?php echo $fieldata[0][0];?>">
					<?php if($fieldata[0][0]!=""){
						echo Display_name($fieldata[0][0]);
					}else{echo "-- Select Field --";}?>
				</option>
				<option value="">-- Select Field --</option>
				<?php
					echo $option_lines;
				?>
			</select></font>
		</td>
      		<td width="10%" height="10"> 
  			<font color="#0000FF">
			<select  size="1" name="Q2" id="Q2" onchange="javascript:document.myform.C2.checked = true"  > 
                                <option value="<?php echo $fieldata[1][0];?>"><?php if($fieldata[1][0]!=""){
                                                                                        echo Display_name($fieldata[1][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines; 
     				?>
			</select></font>
		</td>
      		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q3" id="Q3" onchange="javascript:document.myform.C3.checked = true" >
                                <option value="<?php echo $fieldata[2][0];?>"><?php if($fieldata[2][0]!=""){
                                                                                        echo Display_name($fieldata[2][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q4" id="Q4" onchange="javascript:document.myform.C4.checked = true" >
                                <option value="<?php echo $fieldata[3][0];?>"><?php if($fieldata[3][0]!=""){
                                                                                        echo Display_name($fieldata[3][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q5" id="Q5" onchange="javascript:document.myform.C5.checked = true" >
                                <option value="<?php echo $fieldata[4][0];?>"><?php if($fieldata[4][0]!=""){
                                                                                        echo Display_name($fieldata[4][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>

        		</select>
			</font>
		</td>
		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q6" id="Q6" onchange="javascript:document.myform.C6.checked = true" >
                                <option value="<?php echo $fieldata[5][0];?>"><?php if($fieldata[5][0]!=""){
                                                                                        echo Display_name($fieldata[5][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q7" id="Q7" onchange="javascript:document.myform.C7.checked = true" >
                                <option value="<?php echo $fieldata[6][0];?>"><?php if($fieldata[6][0]!=""){
                                                                                        echo Display_name($fieldata[6][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<td width="10%" height="10">
  			<font color="#0000FF"> 
  			<select  size="1" name="Q8" id="Q8"  onchange="javascript:document.myform.C8.checked = true">
                                <option value="<?php echo $fieldata[7][0];?>"><?php if($fieldata[7][0]!=""){
                                                                                        echo Display_name($fieldata[7][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<td width="10%" height="10">
  			<font color="#0000FF">
  				<select  size="1" name="Q9" id="Q9"  onchange="javascript:document.myform.C8.checked = true">
                                <option value="<?php echo $fieldata[8][0];?>"><?php if($fieldata[8][0]!=""){
                                                                                        echo Display_name($fieldata[8][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
					<option value="">-- Select Field --</option>
        				<?php
						echo $option_lines;
        				?>
				</select>
				</font>
		</td>
		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q10" id="Q10" onchange="javascript:document.myform.C10.checked = true" >
                                <option value="<?php echo $fieldata[9][0];?>"><?php if($fieldata[9][0]!=""){
                                                                                        echo Display_name($fieldata[9][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
  		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q11" id="Q11" onchange="javascript:document.myform.C11.checked = true"  >
                                <option value="<?php echo $fieldata[10][0];?>"><?php if($fieldata[10][0]!=""){
                                                                                        echo Display_name($fieldata[10][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q12" id="Q12"  onchange="javascript:document.myform.C12.checked = true">
                                <option value="<?php echo $fieldata[11][0];?>"><?php if($fieldata[11][0]!=""){
                                                                                        echo Display_name($fieldata[11][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q13" id="Q13"  onchange="javascript:document.myform.C13.checked = true">
                                <option value="<?php echo $fieldata[12][0];?>"><?php if($fieldata[12][0]!=""){
                                                                                        echo Display_name($fieldata[12][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q14" id="Q14" onchange="javascript:document.myform.C14.checked = true" >
                                <option value="<?php echo $fieldata[13][0];?>"><?php if($fieldata[13][0]!=""){
                                                                                        echo Display_name($fieldata[13][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<td width="10%" height="10"> 
  			<font color="#0000FF"> 
  			<select  size="1" name="Q15" id="Q15"  onchange="javascript:document.myform.C15.checked = true">
                                <option value="<?php echo $fieldata[14][0];?>"><?php if($fieldata[14][0]!=""){
                                                                                        echo Display_name($fieldata[14][0]);
                                                                                        }else{echo "-- Select Field --";}?></option>
				<option value="">-- Select Field --</option>
        			<?php
					echo $option_lines;
        			?>
			</select>
			</font>
		</td>
		<?php mysqli_free_result($result); ?>
    	</tr>
    	<tr>
      		<td width="5%" height="1"><font color="#800080" size="2"><b>Sort</b></font></td>
      		<td width="10%" height="1"> 
  			<font color="#800080"> 
  			<select  size="1" name="QS1" id="QS1">
        <option value="<?php echo $fieldata[0][1];?>"><?php if($fieldata[0][1]!=""){
                                                                echo convert_sort($fieldata[0][1]);
                                                                }else{echo "-- Select --";}?></option>
 
  				<option value="">-- Select --</option>
  				<option value="DESC">Descending</option>
  				<option value="ASC">Ascending</option>
 			</select></font>
		</td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS2" id="QS2">
        <option value="<?php echo $fieldata[1][1];?>"><?php if($fieldata[1][1]!=""){
                                                                echo convert_sort($fieldata[1][1]);
                                                                }else{echo "-- Select --";}?></option>  
	 <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS3" id="QS3">
        <option value="<?php echo $fieldata[2][1];?>"><?php if($fieldata[2][1]!=""){
                                                                echo convert_sort($fieldata[2][1]);
                                                                }else{echo "-- Select --";}?></option>

   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS4" id="QS4">
        <option value="<?php echo $fieldata[3][1];?>"><?php if($fieldata[3][1]!=""){
                                                                echo convert_sort($fieldata[3][1]);
                                                                }else{echo "-- Select --";}?></option>

   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS5" id="QS5">
        <option value="<?php echo $fieldata[4][1];?>"><?php if($fieldata[4][1]!=""){
                                                                echo convert_sort($fieldata[4][1]);
                                                                }else{echo "-- Select --";}?></option>

   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select size="1" name="QS6" id="QS6">
        <option value="<?php echo $fieldata[5][1];?>"><?php if($fieldata[5][1]!=""){
                                                                echo convert_sort($fieldata[5][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS7" id="QS7">
        <option value="<?php echo $fieldata[6][1];?>"><?php if($fieldata[6][1]!=""){
                                                                echo convert_sort($fieldata[6][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS8" id="QS8">
        <option value="<?php echo $fieldata[7][1];?>"><?php if($fieldata[7][1]!=""){
                                                                echo convert_sort($fieldata[7][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS9" id="QS9">
        <option value="<?php echo $fieldata[8][1];?>"><?php if($fieldata[8][1]!=""){
                                                                echo convert_sort($fieldata[8][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS10" id="QS10">
        <option value="<?php echo $fieldata[9][1];?>"><?php if($fieldata[9][1]!=""){
                                                                echo convert_sort($fieldata[9][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS11" id="QS11">
        <option value="<?php echo $fieldata[10][1];?>"><?php if($fieldata[10][1]!=""){
                                                                echo convert_sort($fieldata[10][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS12" id="QS12">
        <option value="<?php echo $fieldata[11][1];?>"><?php if($fieldata[11][1]!=""){
                                                                echo convert_sort($fieldata[11][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS13" id="QS13">
        <option value="<?php echo $fieldata[12][1];?>"><?php if($fieldata[12][1]!=""){
                                                                echo convert_sort($fieldata[12][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS14" id="QS14">
        <option value="<?php echo $fieldata[13][1];?>"><?php if($fieldata[13][1]!=""){
                                                                echo convert_sort($fieldata[13][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
      <td width="10%" height="1"> 
  <font color="#800080"> 
  <select  size="1" name="QS15" id="QS15">
        <option value="<?php echo $fieldata[14][1];?>"><?php if($fieldata[14][1]!=""){
                                                                echo convert_sort($fieldata[14][1]);
                                                                }else{echo "-- Select --";}?></option>
   <option value="">-- Select --</option>
  <option value="DESC">Descending</option>
  <option value="ASC">Ascending</option>
 
  </select></font></td>
    </tr>
    <tr>
      <td width="5%" height="22"><font color="#008000" size="2"><b>Logic</b></font></td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL1" id="QL1" onchange="CheckVisible(this)">
	<option value="<?php echo $fieldata[0][2];?>"><?php if($fieldata[0][2]!=""){
								echo convert_logic($fieldata[0][2]);
								}else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
 	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
 
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL2" id="QL2">
        <option value="<?php echo $fieldata[1][2];?>"><?php if($fieldata[1][2]!=""){
                                                                echo convert_logic($fieldata[1][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL3" id="QL3">
        <option value="<?php echo $fieldata[2][2];?>"><?php if($fieldata[2][2]!=""){
                                                                echo convert_logic($fieldata[2][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL4" id="QL4">
        <option value="<?php echo $fieldata[3][2];?>"><?php if($fieldata[3][2]!=""){
                                                                echo convert_logic($fieldata[3][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL5" id="QL5">
        <option value="<?php echo $fieldata[4][2];?>"><?php if($fieldata[4][2]!=""){
                                                                echo convert_logic($fieldata[4][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL6" id="QL6">
        <option value="<?php echo $fieldata[5][2];?>"><?php if($fieldata[5][2]!=""){
                                                                echo convert_logic($fieldata[5][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL7" id="QL7">
        <option value="<?php echo $fieldata[6][2];?>"><?php if($fieldata[6][2]!=""){
                                                                echo convert_logic($fieldata[6][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL8" id="QL8">
        <option value="<?php echo $fieldata[7][2];?>"><?php if($fieldata[7][2]!=""){
                                                                echo convert_logic($fieldata[7][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL9" id="QL9">
        <option value="<?php echo $fieldata[8][2];?>"><?php if($fieldata[8][2]!=""){
                                                                echo convert_logic($fieldata[8][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL10" id="QL10">
        <option value="<?php echo $fieldata[9][2];?>"><?php if($fieldata[9][2]!=""){
                                                                echo convert_logic($fieldata[9][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL11" id="QL11">
        <option value="<?php echo $fieldata[10][2];?>"><?php if($fieldata[10][2]!=""){
                                                                echo convert_logic($fieldata[10][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL12" id="QL12">
        <option value="<?php echo $fieldata[11][2];?>"><?php if($fieldata[11][2]!=""){
                                                                echo convert_logic($fieldata[11][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL13" id="QL13">
        <option value="<?php echo $fieldata[12][2];?>"><?php if($fieldata[12][2]!=""){
                                                                echo convert_logic($fieldata[12][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL14" id="QL14">
        <option value="<?php echo $fieldata[13][2];?>"><?php if($fieldata[13][2]!=""){
                                                                echo convert_logic($fieldata[13][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
      <td width="10%" height="22"> 
  <font color="#008000"> 
  <select  size="1" name="QL15" id="QL15">
        <option value="<?php echo $fieldata[14][2];?>"><?php if($fieldata[14][2]!=""){
                                                                echo convert_logic($fieldata[14][2]);
                                                                }else{echo "-- Select --";}?></option>
	<option value="">-- Select --</option>
   	<option value="Like" >Like</option>
   	<option value="Not Like" >Not Like</option>
   	<option value="=">= - Equals</option>
  	<option value="&lt;">&lt; -  Less than</option>
  	<option value="&gt;">&gt; - Greater Than</option>
   	<option value="&lt;&gt;">&lt;&gt; - Not Equal to</option>
   	<option value="&lt;=">&lt;= less than or Equal to</option>
   	<option value="&gt;=">&gt;= Greater than or Equal to</option>
   	<option value="Is Null">Is Null</option>
   	<option value="Is Not Null">Is Not Null</option>
  </select> </font>
  </td>
    </tr>
    <tr>
      <td width="5%" height="22"><b><font color="#808000" size="2">Criteria</font></b></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC1" size="18" value="<?php echo $fieldata[0][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC2" size="18" value="<?php echo $fieldata[1][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC3" size="18" value="<?php echo $fieldata[2][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC4" size="18" value="<?php echo $fieldata[3][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC5" size="18" value="<?php echo $fieldata[4][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC6" size="18" value="<?php echo $fieldata[5][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC7" size="18" value="<?php echo $fieldata[6][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC8" size="18" value="<?php echo $fieldata[7][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC9" size="18" value="<?php echo $fieldata[8][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC10" size="18" value="<?php echo $fieldata[9][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC11" size="18" value="<?php echo $fieldata[10][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC12" size="18" value="<?php echo $fieldata[11][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC13" size="18" value="<?php echo $fieldata[12][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC14" size="18" value="<?php echo $fieldata[13][3];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QC15" size="18" value="<?php echo $fieldata[14][3];?>"></font></td>
    </tr>
    <tr>
      <td width="5%" height="22" align="center"><b><font color="#808000" size="2">Or</font></b></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor1" size="18" value="<?php echo $fieldata[0][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor2" size="18" value="<?php echo $fieldata[1][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor3" size="18" value="<?php echo $fieldata[2][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor4" size="18" value="<?php echo $fieldata[3][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor5" size="18" value="<?php echo $fieldata[4][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor6" size="18" value="<?php echo $fieldata[5][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor7" size="18" value="<?php echo $fieldata[6][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor8" size="18" value="<?php echo $fieldata[7][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor9" size="18" value="<?php echo $fieldata[8][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor10" size="18" value="<?php echo $fieldata[9][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor11" size="18" value="<?php echo $fieldata[10][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor12" size="18" value="<?php echo $fieldata[11][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor13" size="18" value="<?php echo $fieldata[12][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor14" size="18" value="<?php echo $fieldata[13][4];?>"></font></td>
      <td width="10%" height="22"><font color="#808000"><input type="text" name="QCor15" size="18" value="<?php echo $fieldata[14][4];?>"></font></td>
    </tr>
    <tr>
      <td width="5%" height="20" align="center"><b><font color="#FF0000" size="2">Show</font></b></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C1" value="ON" <?php if($fieldata[0][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C2" value="ON" <?php if($fieldata[1][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C3" value="ON" <?php if($fieldata[2][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C4" value="ON" <?php if($fieldata[3][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C5" value="ON" <?php if($fieldata[4][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C6" value="ON" <?php if($fieldata[5][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C7" value="ON" <?php if($fieldata[6][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C8" value="ON" <?php if($fieldata[7][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C9" value="ON" <?php if($fieldata[8][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C10" value="ON" <?php if($fieldata[9][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C11" value="ON" <?php if($fieldata[10][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C12" value="ON" <?php if($fieldata[11][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C13" value="ON" <?php if($fieldata[12][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C14" value="ON" <?php if($fieldata[13][5] == "ON"){echo "CHECKED";}?>></td>
      <td width="10%" height="20" align="center">
      <input type="checkbox" name="C15" value="ON" <?php if($fieldata[14][5] == "ON"){echo "CHECKED";}?>></td>
    </tr>
  </table>
	<input  class="button" 
		type="submit" 
		value="Submit Query" 
		name="B1" 
		onclick="document.getElementById('savedquery').value=''">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input  class="button" 
		type="button" 
		value="Reset All" 
		name="B2" 
		onclick="javascript:window.location ='<?php echo $_SERVER['PHP_SELF']."?reset=1"; ?>'">
	<br>
<input type="checkbox" name="Excel" value="ON"> Excel Friendly<br><br>
<input type="hidden" name="savedquery" id="savedquery" value="">
<input type="hidden" name="savedqueryid" id="savedqueryid" value="<?php echo $recallsaved; ?>">
<?php
$filename = $basedir."querys/".$_SESSION['user']."/". $_SESSION['user'].".key";
$contents = file_get_contents($filename);
$file_lines= explode("\n",$contents);
$q_name="";
foreach ($file_lines as $key => $line){
        $query_line = explode("|",$line);
	// Ensure array has at least 2 elements before accessing index [1]
	if (isset($query_line[1]) && $recallsaved == $query_line[1]) {
		$q_name = $query_line[0];
	}
/*
        if ($recallsaved == $query_line[1]){
		$q_name=$query_line[0];
	}
*/
}
?>
<input class="button" type="button" value="save" onclick="return get_save_name('<?php echo $q_name; ?>')">

</form>
<?php
$filename = $basedir."querys/".$_SESSION['user']."/". $_SESSION['user'].".key";
//if ($fp= @fopen($filename, "r")){
        //$contents= @fread($fp,filesize($filename));
        //fclose($fp);
        //$file_lines= explode("\n",$contents);
        //$cnt=0;
if ($contents = file_get_contents($filename)){
	$file_lines= explode("\n",$contents);
	echo "<table><tr><td>Name</td><td>Delete</td></tr>";
        foreach ($file_lines as $line){
                $fieldata = explode("|",$line);
		if ($fieldata[0] != ""){
			echo "<tr><td>";
			echo "<a href=\"querycreate.php?queryname=". $fieldata[1]. "\">";
			echo  $fieldata[0] . "</a>";
			echo "&nbsp;&nbsp;&nbsp;";
			echo "</td><td>";
			echo "<a href=\"querycreate.php?delete=". $fieldata[1]. "\">";
			echo "Delete</a>";
			echo "</td></tr>";
		}
          //      $cnt++;
        }
}

?>
</body>

</html>
<?php ob_end_flush(); ?>
