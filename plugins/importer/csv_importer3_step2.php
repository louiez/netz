<?php
function CSV2Array($content, $delim = ',', $encl = '"', $optional = 1)
{
   $reg = '/(('.$encl.')'.($optional?'?(?(2)':'(').
'[^'.$encl.']*'.$encl.'|[^'.
$delim.'\r\n]*))('.$delim.'|\r\n|\n)/smi';

   preg_match_all($reg, $content, $treffer);
   $linecount = 0;
   for ($i = 0; $i<=count($treffer[3]);$i++)
   {
        // load array with each field data 
        // removing leading and trailing double quotes with trim
       $liste[$linecount][] = trim($treffer[1][$i],"\"");
       if ($treffer[3][$i] != $delim)
           $linecount++;
   }
   return $liste;
}

$csv_fields_selected=$_POST['selected'];
$delimiter=$_GET['del'];
if ($delimiter == "comma"){
        $delimiter = ",";
}elseif($delimiter == "semi"){
	$delimiter = ";";
}
$dbTocsviMap=array();
//echo "start";
$site_id = $_POST['csv_uid'];
$site_table_id = $_POST['csv_uid_col_num'];
echo $site_table_id." " .$site_id . "<br>";
$maping=array();
$name_mapping=array();
$maping[$site_table_id]=$site_id;
foreach ($csv_fields_selected as $csv_field){
        $csv_field_name=$_POST[$csv_field."_csv"];

        $db_field = $_POST[$csv_field."_select"];
        $db_display = $_POST[$csv_field."_display_name"];
        echo $csv_field." " .$csv_field_name." " . $db_field." " .$db_display. "<br>";
        $maping[$csv_field]=$db_field;
	$name_mapping[$db_field]=$db_display;

}
echo "<pre>";
$q = "INSERT INTO SITEDATA (";
/*
  Build the first part of the query
*/
foreach ($maping as $key=>$v){
        $cntr++;
        //$q = "INSERT INTO SITEDATA ("..") VALUES (".$APTR.$STRSTORENUM.$APTR.")"$v;
        if($key == $site_table_id ){$val="SITE_ID";}else{$val=$v;}
        if ($cntr < count($maping)){
                $q .= $val.",";
        }else{
		$q .= $val;
                //$q .= $val.", LAST_CHANGE_DATE";
        }
}
$q .= ") VALUES(";
//print_r($dbTocsvMap);
//echo "</pre>";
$filename=$_GET['filename'];
$csv_data = file_get_contents($filename);
// load the array with the csv line data
$lines = CSV2Array($csv_data,$delimiter);
//$lines =file($fileName);
$num_csv_rows=count($lines);
for ($i=1; $i <= $num_csv_rows; $i++){

//        $csvlinedata=explode($delimiter,trim($lines[$i]));
	$csvlinedata= $lines[$i];
        $cntr=0;
        foreach ($maping as $key=>$v){
                $cntr++;
                //echo $key." ".$v;
                if ($cntr < count($maping)){
                        $dbv .= "'".addslashes($csvlinedata[$key])."',";
                }else{
                        $dbv .= "'".addslashes($csvlinedata[$key])."'";
			//$dbv .= ", '".strftime("%Y-%m-%d %H:%M:%S %Z",time())."'";
                }
                //echo "INSERT INTO SITEDATE SET ".$maping[$key]." = '".$csvlinedata[$key+1]."' WHERE SITE_ID ="
        }
        echo $q.$dbv.");\n";
        $dbv="";
}
/*
  Build the name mappings
*/
foreach ($name_mapping as $key=>$v){
	echo "UPDATE NAME_MAPING SET DISPLAY_NAME = '".$v."' WHERE DB_FIELD_NAME = '".$key."';\n";
}
echo "</pre>";
?>
