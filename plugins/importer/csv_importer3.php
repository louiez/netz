<?php
include_once('../../site-monitor.conf.php');
include_once('../../logon.php');
include_once('../../lmz-functions.php');
mysql_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD);
@mysql_select_db(NETZ_DATABASE) or die( "Unable to select database");

/* ================================================================================
  some imports the fields might have CR or LF or both
  best solution I have found is to export the data using
  a different line terminator like "((||))" or something that is not
  going to be anywhere else in the data... then run thru some conversion
  for example :
  you have a file called test2.txt that is a CSV file with "((||))" line terminators
  we now can remove the CR and LF... 
  then replace the "((||))" with LF so it can be imported

                   remove CR     remove LF     replace with LF    new ready to import
   cat test2.txt | tr -d '\n' | tr -d '\r' | sed 's/((||))/\n/g' > test.csv


==================================================================================*/
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++
   file function creates an array of a a file contents
+++++++++++++++++++++++++++++++++++++++++++++++++++++*/
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

$filename =$_GET['filename'];
// load the file contents to a string $csv_data
if($filename != ""){ 
        $csv_data = file_get_contents($filename);
}
// set the delimiter from user selected
$delimiter=$_GET['del'];
if ($delimiter == "comma"){
        $delimiter = ",";
}elseif($delimiter == "semi"){
        $delimiter = ";";
}
// load the array with the csv line data
$lines = CSV2Array($csv_data,$delimiter);

// get the database field names
$sql="SHOW FIELDS FROM SITEDATA";
//$sql="SELECT * FROM `NAME_MAPING` WHERE 1";
$result=mysql_query($sql);
?>
<html>
<head>
        <title>
        </title>
<script type="text/javascript">
function deleteIdRow(s){
        var csvValue = s[s.selectedIndex].text;
        // table body
        var oTable 
        // ***  IE *** // *
        if (document.getElementById('import_mapping').childNodes.length == 2){
                oTable = document.getElementById('import_mapping').childNodes.item(1);

        }else{
                oTable = document.getElementById('import_mapping').childNodes.item(0);
        }
        var oTR= document.getElementsByTagName("TR")
//alert(s.selectedIndex)
//      for (var i=1; i <= oTR.length ; i++){
//              if (oTR.item(i).nodeName == "TR"){
        i=s.selectedIndex
//                      if (oTR.item(i).childNodes.item(0).firstChild.firstChild.data == csvValue){
        document.getElementById("csv_uid").value = csvValue;
        document.getElementById("csv_uid_col_num").value = (i-1);
        oTable.deleteRow(oTR.item(i).rowIndex);
        s.disabled = true;
        s.style.position = "absolute";
        s.style.left = "-300px";
        return true;
//                      }
//              }
//      }
}
function setDisplayName(n){
        // get the table row object
        var x = n.parentNode.parentNode.childNodes;
                current_val=x.item(1).firstChild.text
                toggle_selections(x.item(1).firstChild)
        return true
}

/****************************************************
  Have to reenable all the Items in the select boxes
  or their values will not be passed in the POST
****************************************************/
function enable_selects(){
        var sl = document.getElementsByTagName("select")
        for(ssii=0; ssii < sl.length ; ssii++){
                        for (i=0;i < sl.item(ssii).length; i++){
                                sl.item(ssii)[i].disabled = false
                        }
        }
        return true
}
/*****************************************************/
var current_val;
function toggle_selections(n){
        var name=n[n.selectedIndex].text;
        /* ===============================
          disable already selected Items
        ======================================================================*/
        /* get array of all the Select boxes */
        var sl = document.getElementsByTagName("select")
        /* roll through all the select boxes and disable the currently selected item*/
        for(ssii=0; ssii < sl.length ; ssii++){
                /* make sure it is not the Site_ID box or the default entry*/
                if (sl.item(ssii).name != "site_id" 
                                && name != "-- Select Field --"){
                        sl.item(ssii)[n.selectedIndex].disabled = true
                }
                /* if a field was already assigned to a DB field
                   reenable it in the select boxes 
                */
                if (current_val != ""){
                        /* sl.item(ssii) is the current select box in this loop*/
                        for(is=0; is<sl.item(ssii).length; is++){
                                if (sl.item(ssii)[is].text == current_val){
                                        /* sl.item(ssii)[is] is the item in the select box*/  
                                        sl.item(ssii)[is].disabled = false;
                                }
                        }
                }
        }
        /* this is set and used above when the select box is clicked... 
        so we reset it here
        */
        current_val = ""
        /*=====================================================================*/

        /* get the the list of children of the current table row (TR)
           x = list of TR children
        */
        var x = n.parentNode.parentNode.childNodes;
        if (name != "-- Select Field --"){
                if (x.item(2).firstChild.checked == false){
                        var xmlhttp ;
                        if (window.XMLHttpRequest){
                                // code for IE7+, Firefox, Chrome, Opera, Safari
                                xmlhttp=new XMLHttpRequest();
                        }else{
                                // code for IE6, IE5
                                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                        }
                        //Call a function when the state changes.
                        xmlhttp.onreadystatechange = function() {
                                if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                        // enable the "Change Display name to CSV name" check box
                                        x.item(2).firstChild.disabled = false
                                        // ***  IE *** // *
                                        if(x.length == 5){
                                                // Enable the "Display name" text box
                                                x.item(3).firstChild.disabled = false
                                                // change the "Display name" text box
                                                x.item(3).firstChild.value = xmlhttp.responseText
                                                // enable the Include check box
                                                x.item(4).firstChild.disabled = false
                                                x.item(4).firstChild.checked = true
                                        // ***  every other browser *** // *
                                        }else{
                                                // Enable the "Display name" text box
                                                x.item(4).firstChild.disabled = false
                                                // change the "Display name" text box
                                                x.item(4).firstChild.value = xmlhttp.responseText
                                                // enable the Include check box
                                                x.item(6).firstChild.disabled = false
                                                x.item(6).firstChild.checked = true
                                        }
                                }
                        }
                        url="get_displayName.php?name="+name+"&t="+((new Date()).valueOf());
                        xmlhttp.open("GET",url,true);
                        xmlhttp.send();
                        return true;
                }else{
                        // enable the "Change Display name to CSV name" check box
                        x.item(2).firstChild.disabled = false
                        // ***  IE *** // *
                        if (x.length == 5){
                                // Enable the "Display name" text box
                                x.item(3).firstChild.disabled = false
                                // enable the Include check box
                                x.item(4).firstChild.disabled = false
                                x.item(4).firstChild.checked = true
                                // change the "Display name" text box
                                x.item(3).firstChild.value = x.item(0).firstChild.firstChild.data
                        // ***  every other browser *** // *
                        }else{
                                // Enable the "Display name" text box
                                x.item(4).firstChild.disabled = false
                                // enable the Include check box
                                x.item(6).firstChild.disabled = false
                                x.item(6).firstChild.checked = true
                                // change the "Display name" text box
                                x.item(4).firstChild.value = x.item(0).firstChild.firstChild.data
                        }
                }
        /* the select box has "-- Select Field --" selected 
           so we disable everything
        */
        }else{
                // get the Table row object
                var x = n.parentNode.parentNode.childNodes;
                // disable the "Change Display name to CSV name" check box
                x.item(2).firstChild.disabled = true
                // ***  IE *** // *
                if (x.length == 5){
                        // disable the "Display name" text box
                        x.item(3).firstChild.disabled = true
                        // change the "Display name" text box
                        x.item(3).firstChild.value = ""
                        // Disable the Include check box
                        x.item(4).firstChild.disabled = true
                        x.item(4).firstChild.checked = false
                // ***  every other browser *** // *
                }else{
                        // disable the "Display name" text box
                        x.item(4).firstChild.disabled = true
                        // change the "Display name" text box
                        x.item(4).firstChild.value = ""
                        // Disable the Include check box
                        x.item(6).firstChild.disabled = true
                        x.item(6).firstChild.checked = false
                }
        }
}
</script>
</head>
<body>
<?php
// display the database and csv rows and fields info
if ($filename != ""){
        $num_db_fields = mysql_num_rows($result);
        $num_csv_rows=count($lines) - 1;
        $num_csv_fields=count($lines[0]);
        echo "Number of DB fields ". $num_db_fields ."<br>";
        echo "Number of CSV fields ". $num_csv_fields ."<br>";
        echo "Number of rows in CSV file ".$num_csv_rows."<br><br>";

/*+++++++++++++++++++++++++++++++++++++
  Create the select box data with DB fields
+++++++++++++++++++++++++++++++++++++*/
$db_header=array();
while ($row = mysql_fetch_assoc($result)){
        //$select .= "<option value=\"" . $row['Field']."\">".$row['Field']."</option>";
        //$db_header[$row['Field']] = Display_name($row['Field']);
        if ($row['Field'] != "SITE_ID"){
                $db_header[] = $row['Field'];
        }
}
sort($db_header);
foreach ($db_header as $db_field){
        $select .= "<option value=\"" . $db_field."\">".$db_field."</option>";

}

/*++++++++++++++++++++++++++++
  Grab the CSV header fields
++++++++++++++++++++++++++++*/
If ($_GET['header'] == "yes"){
	$csv_header=$lines[0];
}else{
	for ($z=0; $z < $num_csv_fields; $z++){
		$csv_header[$z]=$z;
	}
}

/*++++++++++++++++++++++++++++++++++
  Create the selection form Table
++++++++++++++++++++++++++++++++++*/
?>
<hr>
<p style="font-weight:bold;color:blue">
Each record has a unique ID ... like a site ID or a name.<br>
This ID cannot be duplicated in NETz  <br>
so select the field in your CSV file that meets this "no duplicate" ID
</p>
<form action="csv_importer3_step2.php?filename=<?php echo $filename; ?>&del=<?php echo $_GET['del']; ?>" method="POST">
<input id="csv_uid"  name="csv_uid"  type="text" value="">
<input id="csv_uid_col_num"  name="csv_uid_col_num"  type="hidden" value="">
<table id="import_mapping">
<tr>
<th style="border:solid ; border-width:1px">CSV Field Names</th>
<th style="border:solid ; border-width:1px">Database Fields</th>
<th style="border:solid ; border-width:1px">Change Display name<br> to CSV Field name</th>
<th style="border:solid ; border-width:1px">Name you want to display in NETz</th>
<th style="border:solid ; border-width:1px">Include</th>
</tr>

<?php
        echo "<select name=\"site_id\"onchange=\"return deleteIdRow(this)\">";
        $select_all = "<option value=\"\">-- Select Field --</option>";
                foreach($csv_header as $fld){
                        $select_all .= "<option value=\"" . $fld."\">".$fld."</option>";
                }
        $select_all .= "</select>";
        echo $select_all;
echo "<hr>";
/* Display message if csv file has more fields than DB */
if ($num_csv_fields > $num_db_fields){
        echo "<script type=\"text/javascript\"> ";
        echo "alert('CSV file has more fields than database')</script>";
}
for ($i =0; $i < $num_csv_fields; $i++){

        $select_all = "<select name=\"".$i."_select\" onchange=\"return toggle_selections(this)\"";
        $select_all .= "onclick=\"current_val = this.value\">";
        $select_all .= "<option value=\"\">-- Select Field --</option>";
        $select_all .= $select;
        $select_all .= "</select>";
        echo "<tr>";
        // CSV Fields
        echo "<td style=\"border:solid ; border-width:1px\">";
        echo "<span  name=\"".$i."_csv_name\">".$csv_header[$i]."</span></td>";
        // Database Fields
        echo "<td style=\"border:solid ; border-width:1px\">" . $select_all."</td>";
        // Change Display name to CSV name checkbox
        echo "<td style=\"border:solid ; border-width:1px\">";
        echo "<input type=\"checkbox\" name=\"" . $i."_change_display\"";
        echo "onclick=\"return setDisplayName(this)\"" . " DISABLED ></td> ";
        // Display Name
        echo "<td style=\"border:solid ; border-width:1px\">";
        echo "<input type=\"text\" name=\"" . $i."_display_name\" value=\"\" DISABLED></td> ";
        //Include checkbox
        echo "<td style=\"border:solid ; border-width:1px\">";
        echo "<input type=\"checkbox\" name=\"selected[]\" value=\"" . $i . "\" DISABLED></td> ";
        echo "</tr>\n";
}

?>
</table>
<input type="hidden" name="filename" value="<?php echo $fileName; ?>">
<input type="submit" onclick="return enable_selects()" >
</form>
<table>
<center><h2> Sample CSV Data </h2></center>
<?php
for ($i=0; $i < 5; $i++){

        $csvlinedata = $lines[$i];
        echo "<tr>";
        foreach ($csvlinedata as $key=>$v){
                if ($i == 0){
                        echo "<td style=\"border:solid ; border-width:1px ; background-color:lightgrey\"> ";
                        echo $v."</td>";
                }else{
                        echo "<td style=\"border:solid ; border-width:1px\"> ".$v."&nbsp; </td>";
                }
        }
        echo "</tr>\n";
}
}else{
        ?>
        <form action="get_attachment.php">
        <input type="submit" name="select file" value="select file" >
        </form>
<?php
}
?>
</table>
</body></html>
