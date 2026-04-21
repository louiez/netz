<?php
/*###############################################################
        NETz Network Management system                          #
        http://www.proedgenetworks.com/netz                     #
                                                                #
                                                                #
        Copyright (C) 2005-2026 Louie Zarrella                  #
        jwaldo85@gmail.com                             #
                                                                #
        Released under the GNU General Public License           #
        Copy of License available at :                          #
        http://www.gnu.org/copyleft/gpl.html                    #
###############################################################*/
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
if (!isset($_SESSION['user'])){$_SESSION['user'] = "Guest";}
include_once("site-monitor.conf.php");
include('write_access_log.php');
        /*
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        * Check it we are using HTTP or HTTPS   *
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        */
        if(isset($_SERVER['HTTPS'])){
                if ($_SERVER["HTTPS"] == "on"){
                        $_SESSION['secure']= "https://";
                }else{
                        $_SESSION['secure']= "http://";
                }
        }else{
                        $_SESSION['secure']= "http://";
                }
        //+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+

$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);

echo "<html><head></head><body>";
//$region = "dallas";
//$SQL="SELECT * FROM SITEDATA WHERE ACTIVE_DATE is not NULL and REGION = '" . $region . "'";
//echo $SQL;
//$result=mysqli_query($conn,$SQL);
//echo "<form>";
echo '<h3> Active Sites</h3>';
echo '<form method="POST" action="'.$_SESSION['secure'] . $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'" name="myform" id ="myform" > Select Region';
?>
                                <select  size="1" name="txtregion"><option value="ALL">ALL</option>
                                <?php
                                // Load Regions from region.txt
                                $filename=$basedir."region.txt";
                                $fp= fopen($filename, "r");
                                $contents= fread($fp,filesize($filename));
                                fclose($fp);
                                $file_lines= explode("\n",$contents);
                                foreach ($file_lines as $line){
                                        if ($line != ""){
                                                echo '<option value="' . trim($line) . '">' . trim($line) . '</option>';
                                        }
                                }
                                ?>
                                </select><input type="submit" name="go!"></form>&nbsp;
<?php
$region = $_POST['txtregion'];
if ($region != ""){
	if ($region == "ALL"){
		$SQL="SELECT count(*) FROM SITEDATA WHERE ACTIVE_DATE is not NULL and CLOSE_DATE is NULL ORDER BY REGION, ACTIVE_DATE";
		$result=mysqli_query($conn,$SQL);
		$fetch=mysqli_fetch_row($result);
		$total=$fetch[0];
		$SQL="SELECT * FROM SITEDATA WHERE ACTIVE_DATE is not NULL and CLOSE_DATE is NULL ORDER BY REGION, ACTIVE_DATE";
		$result=mysqli_query($conn,$SQL);
	}else{
		$SQL="SELECT count(*) FROM SITEDATA WHERE ACTIVE_DATE is not NULL and CLOSE_DATE is NULL and REGION = '".$region."' ORDER BY ACTIVE_DATE";
        	$result=mysqli_query($conn,$SQL);
        	$fetch=mysqli_fetch_row($result);
        	$total=$fetch[0];
        	$SQL="SELECT * FROM SITEDATA WHERE ACTIVE_DATE is not NULL and CLOSE_DATE is NULL and REGION = '".$region."' ORDER BY ACTIVE_DATE";
        	$result=mysqli_query($conn,$SQL);
	}
	echo "Total sites for ".$region . " " .$total;
	echo '<table border="1" class="tablestyl"><tr>';
	echo '<td bgcolor="lightgrey" ><b>Site</b></td>';
	echo '<td bgcolor="lightgrey" ><b>CITY</b></td>';
	echo '<td bgcolor="lightgrey" ><b>Group</b></td>';
	echo '<td bgcolor="lightgrey" ><b>Site Type</b></td>';
	echo '<td bgcolor="lightgrey" ><b>Field Rep</b></td>';
	echo '<td bgcolor="lightgrey" ><b>REGION</b></td>';
	echo '<td bgcolor="lightgrey" ><b>Active Date</b></td>';
	echo '</tr>';
	while ($row = mysqli_fetch_assoc($result)){
        	echo '<tr>';
        	$TEMPSTR="<td>".$row['SITE_ID']."</td>";
        	$TEMPSTR=$TEMPSTR."<td>".$row['CITY']."</td>";
        	$TEMPSTR=$TEMPSTR."<td>".$row["GROUP_NAME"]."</td>";
        	$TEMPSTR=$TEMPSTR."<td>".$row["SITE_TYPE"]."</td>";
        	$TEMPSTR=$TEMPSTR."<td>".$row["FIELD_REP"]."</td>";
        	$TEMPSTR=$TEMPSTR."<td>".$row["REGION"]."</td>";
	//        $TEMPSTR=$TEMPSTR."<td>".$row["ACTIVE_DATE"]."</td>";
		$TEMPSTR=$TEMPSTR."<td>".date('m-d-Y',strtotime(trim($row["ACTIVE_DATE"])))."</td>";
        	echo $TEMPSTR;
        	echo '</tr>';
	}
echo "</table></body></html>";
}else{
echo "</body></html>";
}
?>

