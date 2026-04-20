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
include_once('lmz-functions.php');
// This function was added for backwards compat to no longer available mysql_result function
function mysqli_result($res,$row=0,$col=0){ 
    $numrows = mysqli_num_rows($res); 
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($res,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
}
$excel="";
?>

<html>
<head>
<script language="javascript">
function whois(ip)
{
	window.open("../whois.php?ip=" + ip );
	return false;
}
function show_store(url)
{
        if (window.opener != null){
		window.opener.focus();
                window.opener.location= url;

        }else{
                window.location= url;
        }
        return false ;
}

<?php
/*
mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD);
mysqli_select_db(NETZ_DATABASE);
$result = mysqli_query($conn,"select * from SITEDATA where 0=1");
if (!$result) {
   die('Query failed: ' . mysqli_error());
}
*/
//mysqli_close();

//======================================================+
// Get the selected DB feild type... date, string...etc	+
//######################################################################################//
function get_field_type($myfield){							//
// Guess I should use globals but...had to include the config file in the function ???	//
//include('site-monitor.conf.php');							//
	global $conn; 
	 $conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);					//
//	mysqli_select_db(NETZ_DATABASE);							//
	$result = mysqli_query($conn,"select * from SITEDATA JOIN MONITORINFO USING(SITE_ID) where 0=1");			//
	if (!$result) {									//
	   die('Query failed: ' . mysqli_error());					//
	}										//
	$fields = mysqli_num_fields($result);						//
	for ($i=0; $i < $fields; $i++) {						//
//		$type  = mysqli_field_type($result, $i);					//
//		$name  = mysqli_field_name($result, $i);					//
$type = mysqli_fetch_field_direct($result,$i)->type;
$name = mysqli_fetch_field_direct($result,$i)->name;
		if (trim($myfield) == $name){						//
			return $type;							//
		}									//
	}										//
}											//
//######################################################################################//

//===============================================================
//      Save the query create form data to querys/<USER>.last   +
//##############################################################################################################//
function save_query($qname,$qid){											//
	global $basedir; 
	if ($qname == 'last'){											//
		$filename = $basedir."querys/".$_SESSION['user'].".last";      				//
	}else{	
               $filename = $basedir."querys/".$_SESSION['user']."/". $_SESSION['user'].".".$qid;       //
                if (!is_dir($basedir."querys/".$_SESSION['user'])){mkdir($basedir."querys/".$_SESSION['user']);}
                $keyfile = $basedir."querys/".$_SESSION['user']."/". $_SESSION['user'].".key"; 		
		if (trim($qid)!= ""){										//
       			if ($fp = fopen($keyfile, 'r')) {
                		$contents= fread($fp,filesize($keyfile));
                		fclose($fp);
                		$file_lines= split("\n",$contents);
        		}			
			if ($handle = fopen($keyfile, 'a')){
                	// Clear the file
                	ftruncate($handle,0);
                	// go through each line and check if it matches the delete query id
                	foreach ($file_lines as $line){
                	        $fieldata = split("\|",$line);
                	        if ((trim($fieldata[1]) != trim($qid)) && trim($fieldata[1]) != ""){
                	                // write back to file if it is not the deleted key
                	                fwrite($handle,$fieldata[0].'|'.$fieldata[1]."\n");
                	        }elseif (trim($fieldata[1]) == trim($qid)){
					fwrite($handle,$qname.'|'.$fieldata[1]."\n");
				}
                	}
			}
        	 fclose($handle);
		}else{
			// create a unique id
			$dynfilename=date("mdyhms");							
			$filename = $basedir."querys/".$_SESSION['user']."/". $_SESSION['user'].".".$dynfilename;
			if (!is_dir($basedir."querys/".$_SESSION['user'])){
				mkdir($basedir."querys/".$_SESSION['user']);
			}
			$keyfile = $basedir."querys/".$_SESSION['user']."/". $_SESSION['user'].".key";		
			if ($handle = fopen($keyfile, 'a')) {							
				fwrite($handle, $qname."|".$dynfilename."\n");					
				fclose($handle);								
				system('cat '.$keyfile. ' | sort > '.$keyfile.'.tmp');
				system('mv -f '.$keyfile.'.tmp '.$keyfile);
			}
		}												//
	}													//
	if ($handle = fopen($filename, 'w')) {                                  				//
		$stringdata ="";
        	for ($i=1; $i<=15; $i++){                                       				//
			$stringdata .= ($_POST["Q".$i] ?? '') . ",";
			$stringdata .= ($_POST["QS".$i] ?? '') . ",";
			$stringdata .= ($_POST["QL".$i] ?? '') . ",";
			$stringdata .= ($_POST["QC".$i] ?? '') . ",";
			$stringdata .= ($_POST["QCor".$i] ?? '') . ",";
			$stringdata .= ($_POST["C".$i] ?? '') . "\n";
/*
                	$stringdata = $stringdata . $_POST["Q".$i] ."," ;       				//
                	$stringdata = $stringdata . $_POST["QS".$i] .",";       				//
                	$stringdata = $stringdata .  $_POST["QL".$i] .",";      				//
                	$stringdata = $stringdata .  $_POST["QC".$i] .",";      				//
                	$stringdata = $stringdata .  $_POST["QCor".$i]."," ;    				//
                	$stringdata = $stringdata .  $_POST["C".$i]."\n" ;      				//
*/
        	}                                                               				//
	}                                                                       				//
	fwrite($handle, $stringdata);                                           				//
	fclose($handle);                                                        				//
}														//
//##############################################################################################################//

//======================================================+
//   Get the name of the query trying to save		+
//   then call the save_query() function		+
//##############################################################################################################//
$savedqueryname=$_POST['savedquery'];										//
$savedqueryid =$_POST['savedqueryid'];
if (trim($savedqueryname) != ""){										//
	save_query($savedqueryname,$savedqueryid);								//
	//header("Location:".$_SESSION['secure'] . $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']). "/querycreate.php");	//
	header("Location: querycreate.php");  
	ob_end_flush();												//
}														//
//##############################################################################################################//
?>
</script>

</head>
<body>
<?php

$Quote=chr(34);
$Single_Quote=chr(39);
$Space=" ";
$FirstSort=0;
$First=0;
// Q# = Selected field
// QS# = Sort type
//QC# = the Criteria
//QL# = Query logic
//QLor# = Query logic OR
// C# = Check mark to display col
//ORDER_BYSTring = " Order By "
$sql = "SELECT * FROM SITEDATA  JOIN MONITORINFO USING(SITE_ID) WHERE " ;
for ($i=1; $i<=15; $i++){
	if ($_POST["QL".$i]=="Like"){
    		if (strpos(strtolower($_POST["Q".$i]),"date") > 0){
      			$Single_Quote="";
      			$P="'";
    		}
    		elseif ($_POST["QC".$i]==""){
      			$P="";
      			$Single_Quote=chr(39);
    		}else{
      			$P="%";
      			$Single_Quote=chr(39);
    		}
  	}
   	elseif ($_POST["QL".$i] == "Not Like"){
    		if (strpos(strtolower($_POST["Q".$i]),"date") > 0){
      			$Single_Quote = "" ;
      			$P = "'" ;
    		}
    		elseif ($_POST["QC".$i] == ""){
      			$P = "" ;
      			$Single_Quote = chr(39);
    		}else{
      			$P = "%" ; 
			$Single_Quote = chr(39) ; 
    		}
	}else{
		if (strpos(strtolower($_POST["Q".$i]),"date") > 0){
        		$Single_Quote = "" ;
        		$P = "'" ;
      		}
      		elseif ($_POST["QC".$i] == ""){
        		$P = "" ;
        		$Single_Quote = chr(39);
      		}else{
        		$P = chr(34) ;
        		$Single_Quote = "" ;
      		}
    	}

	//~~ If a Field was selected
    	if ($_POST["Q".$i] != ""){
	//~~~~ If this is the first field in the Query we don't need the comma
      		if ($First < 1){
		//~~~~~~ Check to see if there is a Logic selection to query
        		if ($_POST["QL".$i] != ""){
          			$First = 1 ;
				//~~~~~~~~ Query Logic = "Is Null" or "Is Not Null"
          			if (strpos(strtolower($_POST["QL".$i]),"null")) {
            				$sql = $sql . " " . $_POST["Q".$i] . " " . $_POST["QL".$i] ;
					//~~~~~~~~ Regular Query with Criteria
          			}else{
					//~~~~~~~~~~ There is NOT an "OR" Criteria
            				if ($_POST["QCor".$i] ==""){
              					$sql = $sql . " " . $_POST["Q".$i] . " " . $_POST["QL".$i] ;
						//$sql = $sql . $Space . $Single_Quote . $P . $_POST["QC".$i] . $P . $Single_Quote ;
						//==============================================================+
						// Try to format the user entered date string to mysqli format	+
						//##############################################################################//
						$typetest=get_field_type($_POST["Q".$i]);					//
						if ($typetest == 'date'){							//
							$converteddate=date('Y-m-d',strtotime(trim($_POST["QC".$i])));		//
							$sql = $sql . $Space . $Single_Quote . $P . $converteddate . $P . $Single_Quote ;		//
						}elseif ($typetest == 'datetime'){						//
							$converteddate=date('Y-m-d h:i:s A',strtotime(trim($_POST["QC".$i])));  //
                                                        $sql = $sql . $Space . $Single_Quote . $P . $converteddate . $P . $Single_Quote ;              //
						}else{										//
							$sql = $sql . $Space . $Single_Quote . $P . $_POST["QC".$i] . $P . $Single_Quote ;		//
						}										//
						//##############################################################################//
					//~~~~~~~~~~~ There IS an "OR" Criteria
            				}else{
              					$sql = $sql . " (" . $_POST["Q".$i] . " " . $_POST["QL".$i] . $Space ;
                                                //$sql = $sql . $Single_Quote . $P . $_POST["QC".$i] . $P . $Single_Quote . " OR " . " " ;
                                                //==============================================================+
                                                // Try to format the user entered date string to mysqli format	+
                                                //##############################################################################//
                                                $typetest=get_field_type($_POST["Q".$i]);                           		//
                                                if ($typetest == 'date' ){                            				//
                                                        $converteddate=date('Y-m-d',strtotime(trim($_POST["QC".$i])));  	//
                                                        $sql = $sql . $Single_Quote . $P . $converteddate . $P . $Single_Quote . " OR " . " " ;    	//
                                                }elseif ($typetest == 'datetime'){						//
							$converteddate=date('Y-m-d h:i:s A',strtotime(trim($_POST["QC".$i])));  //
							$sql = $sql . $Single_Quote . $P . $converteddate . $P . $Single_Quote . " OR " . " " ;    	//
                                                }else{                                                                          //
                                                        $sql = $sql . $Single_Quote . $P . $_POST["QC".$i] . $P . $Single_Quote . " OR " . " " ;    //
                                                }                                                                               //
                                                //##############################################################################//

						$sql = $sql . $_POST["Q".$i] . " " . $_POST["QL".$i] . $Space . $Single_Quote ; 
						$sql = $sql . $P . $_POST["QCor".$i] . $P . $Single_Quote . ") " ;
            				}
				}
			}
        	}else{  //~~~~This is the second Query to build
			//~~~~~~~If there is a Query to build
        		if ($_POST["QL".$i] !=""){
				#echo strtolower($_POST["QL".$i]),"null";
          			if (strpos(strtolower($_POST["QL".$i]),"null") > 0) {
            				$sql=$sql." AND  ".$_POST["Q".$i] ." ".$_POST["QL".$i]  ;
          			}else{
            				if ($_POST["QCor".$i] ==""){
              					$sql = $sql . " AND  " . $_POST["Q".$i] . " " . $_POST["QL".$i] ;
						//$sql = $sql . $Space . $Single_Quote . $P . $_POST["QC".$i] . $P . $Single_Quote ;
                                                //==============================================================+
                                                // Try to format the user entered date string to mysqli format	+
                                                //==============================================================================//
                                                $typetest=get_field_type($_POST["Q".$i]);                           		//
                                                if ($typetest == 'date'){                            				//
                                                        $converteddate=date('Y-m-d',strtotime(trim($_POST["QC".$i])));  	//
                                                        $sql = $sql . $Space . $Single_Quote . $P . $converteddate . $P . $Single_Quote ;    		//
                                                }elseif ($typetest == 'datetime'){						//
							$converteddate=date('Y-m-d h:i:s A',strtotime(trim($_POST["QC".$i])));  //
							$sql = $sql . $Space . $Single_Quote . $P . $converteddate . $P . $Single_Quote ;              //
                                                }else{                                                                          //
                                                        $sql = $sql . $Space . $Single_Quote . $P . $_POST["QC".$i] . $P . $Single_Quote ;             //
                                                }                                                                               //
                                                //##############################################################################//
            				}else{

              					$sql = $sql . " AND  (" . $_POST["Q".$i] . " " . $_POST["QL".$i] . $Space ;
						$sql = $sql . $Single_Quote . $P . $_POST["QC".$i] . $P . $Single_Quote . " OR " . " " ;
						$sql = $sql . $_POST["Q".$i] . " " . $_POST["QL".$i] . $Space . $Single_Quote . $P ;
						$sql = $sql . $_POST["QCor".$i] . $P . $Single_Quote . ")" ;

            				}

          			}

        		}

      		}

    	}else{
      		break;
    	}
$ORDER_BYSTring="";
    if ($_POST["QS".$i] != ""){
      	if ($FirstSort < 1){
        	$SortComma = "" ;
        	$FirstSort = 1 ;
        	if ($_POST["QS".$i] == "ASC"){
          		$ORDER_BYSTring = " Order By" . $SortComma . " " . $_POST["Q".$i] . " " ;
        	}else{
          		$ORDER_BYSTring = " Order By" . $SortComma . " " . $_POST["Q".$i] . " " . $Space . $_POST["QS".$i] ;
        	}
     	}else{
        	$SortComma = "," ;
        	if ($_POST["QS".$i] == "ASC"){
          		$ORDER_BYSTring = $ORDER_BYSTring . $SortComma . " " . $_POST["Q".$i] . " ";
        	}else{
          		$ORDER_BYSTring = $ORDER_BYSTring . $SortComma . " " . $_POST["Q".$i] . " " . $Space . $_POST["QS".$i] ;
        	}
      	}
     }
  }

  if ($ORDER_BYSTring!=""){
    	$sql = $sql . $ORDER_BYSTring ;
  }
  	// DEBUG INFO
	print "<font size=1>".$sql."</font><br>";

  $conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//  @mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");
  $result=mysqli_query($conn,$sql);

  @$num=mysqli_num_rows($result);

  mysqli_close($conn);
$excel = isset($_POST['Excel']) ? $_POST['Excel'] : 0;

  if ($excel!="ON"){
  	echo "<b><center>" . $num . " Records found&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<a href=\"http://" . $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']);
	echo "/querycreate.php\">Edit/Back</a> </center></b><br><br>";
  }

  set_time_limit(180);

  if ($num){
 	echo "<Table border='1' cellspacing='1'  cellpadding='1' frame='box'";
	echo " bgcolor='#ffffff' bordercolorlight='#00FFFF' bordercolordark='#0000FF' style='font-size:10pt'>";   
	for ($i=1; $i<=15; $i++){
  		if ($_POST["Q".$i]!=""){
    			if ($_POST["C".$i]=="ON"){
      				print "<td BGColor='999999' width='12%'>".Display_name($_POST["Q".$i])."</td>";
    			}
  		}
	}
	// Init variables
	$r=0;
	$counterstr=0;
  	while ($r < $num){
  		$counterstr=$counterstr+1;
  		print "<tr>";
  		for ($i=1; $i<=15; $i=$i+1){
  			if ($_POST["Q".$i]!=""){
      				$Dummy=$_POST["Q".$i];
      				if ($_POST["C".$i]=="ON"){
        				if ($Dummy=="SITE_ID" && $excel !="ON"){
          					print "<td><A href=" . $Quote . "" . $Quote . " onclick=" . $Quote ;
						print  "return show_store('"  ;
						print "ops.php?site=".mysqli_result($result,$r,$Dummy) . "')";
						print  $Quote . ">" ;
						print mysqli_result($result,$r,$Dummy) . "</a></td>";
        				}else{
          					print "<td width='12%'>".mysqli_result($result,$r,$Dummy)."</td>";
        				}
      				}
    			}
 		}
 		$r++;
 		print "</tr>";
  	}
     	echo "</table>";
  }else{
  	echo "No record selected";
  }
echo "<br>";
save_query('last',$savedqueryid);

?>
</BODY>
</HTML>
