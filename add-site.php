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
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL);

include_once("auth.php");
include_once("site-monitor.conf.php");
include('write_access_log.php');
require_once( 'db.class.php');
$db_class = new DB_Class();
//$site=$_GET['site'];
$site = htmlspecialchars($_GET['site'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<html><head>

<?php
//      +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//      |               User Access code                |
// =============================================================================================//
$acl=$_SESSION['accesstype'];                                                                                                                                   //

if ($_SESSION['accesslevel'] < 4){                                                                                                        //
        echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';       //
        echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';                                        //
        }                                                                                                                                                                                       //
// =============================================================================================//
	$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}

        $sql="SELECT * FROM SITEDATA WHERE SITE_ID = '".$site."'";
        $results = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($results);

?>

<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">

<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">

<title>NETz Site Config</title>
<script type="text/javascript">
function validate()
{
	message=""
	if (document.getElementById("txtstorenumber").value == "")
        {
                pmsg = "Please Enter Site ID\n";
                alert(pmsg+message);
 
			document.getElementById("txtstorenumber").style.color = "red";
			document.getElementById("txtstorenumber").style.backgroundColor = "yellow";
                	document.getElementById("txtstorenumber").focus();
                	return false;
		
        }
        if (document.getElementById("txtgroup").value == ""){
                pmsg = "Please enter Group site belongs to\n";
                alert(pmsg+message);

                        document.getElementById("txtgroup").focus();
                        return false;

        }

        if (document.getElementById("txtcity").value == ""){
                pmsg="Please enter City\n";
                alert(pmsg+message);
 
                	document.getElementById("txtcity").focus();
                	return false;
		
        }
        if (document.getElementById("txtst").value == ""){
                pmsg = "Please enter State\n";
                alert(pmsg+message);
 
                	document.getElementById("txtst").focus();
                	return false;
		
        }
        if (document.getElementById("txtstoretype").value == ""){
                pmsg = "Please enter site Type\n";
                alert(pmsg+message);
 
                	document.getElementById("txtstoretype").focus();
                	return false;
		
        }
        if (document.getElementById("txtdc").value == ""){
                pmsg = "Please enter site Region/DC\n";
                alert(pmsg+message);
 
                	document.getElementById("txtdc").focus();
                	return false;
		
        }
        if (document.getElementById("txtbsfsr").value == ""){
                pmsg = "Please enter Field service Rep\n";
                alert(pmsg+message);
  
                	document.getElementById("txtbsfsr").focus();
                	return false;
		
        }
        if (document.getElementById("txtadp").value == ""){
                pmsg = "Please enter Support Center\n";
                alert(pmsg+message);

                	document.getElementById("txtadp").focus();
                	return false;
		
        }
        if (document.getElementById("txtip").value == ""){
                pmsg = "Please enter Site IP\n";
                alert(pmsg+message);

                	document.getElementById("txtip").focus();
                	return false;
		
        }
        if (document.getElementById("txtrouterip").value == ""){
                pmsg = "Please enter Gateway\n";
                alert(pmsg+message);

                        document.getElementById("txtrouterip").focus();
                        return false;

        }

}
</script>
</head>

<body>
<script language="JavaScript1.2"  src="menulz.js"> </script>
<h2 style="text-align:center">Add Site </h2>
<form method="POST" action="write_ops.php" onsubmit="return validate()">
	 <input type="hidden" name="txtnew" id="txtnew" value="new">
        <fieldset style="padding: 2; width: 100%">
           <legend>Site Info</legend>
        	<table border="1" cellspacing="1" width="100%">
			<tr>
				<td style="text-align:right">Site ID&nbsp;<font color="red">&Dagger;</font></td>
				<td>
				<input type="text" name="txtstorenumber" id="txtstorenumber" size="20" value=""></td>
				<td style="text-align:right">Business Name</td>
				<td>
                                <input type="text" name="txtsitename" id="txtsitename" size="20" value=""></td>
                                <td style="text-align:right">Group Name&nbsp;<font color="red">&Dagger;</font></td>
                                <td><input type="text" name="txtgroup" id="txtgroup" size="20" value=""></td>
				<td>&nbsp;</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td style="text-align:right">Address</td>
				<td>
                		<input type="text" name="txtaddress" id="txtaddress" size="20" value=""></td>
				<td style="text-align:right">City&nbsp;<font color="red">&Dagger;</font></td>
				<td>
				<input type="text" name="txtcity" id="txtcity" size="20" value=""></td>
				<td style="text-align:right">State&nbsp;<font color="red">&Dagger;</font></td>
				<td>
<select id="txtst" title="State" name="txtst">
<Option value="<?php   print $row["ST"];?>"><?php   print $row["ST"];?></Option>
<Option value="AL">ALABAMA-AL</Option>
<Option value="AK">ALASKA-AK</Option>
<Option value="AZ">ARIZONA-AZ</Option>
<Option value="AR">ARKANSAS-AR</Option>
<Option value="CA">CALIFORNIA-CA</Option>
<Option value="CO">COLORADO-CO</Option>
<Option value="CT">CONNECTICUT-CT</Option>
<Option value="DE">DELAWARE-DE</Option>
<Option value="DC">DISTRICT OF COLUMBIA-DC</Option>
<Option value="FL">FLORIDA-FL</Option>
<Option value="GA">GEORGIA-GA</Option>
<Option value="GU">GUAM-GU</Option>
<Option value="HI">HAWAII-HI</Option>
<Option value="ID">IDAHO-ID</Option>
<Option value="IL">ILLINOIS-IL</Option>
<Option value="IN">INDIANA-IN</Option>
<Option value="IA">IOWA-IA</Option>
<Option value="KS">KANSAS-KS</Option>
<Option value="KY">KENTUCKY-KY</Option>
<Option value="LA">LOUISIANA-LA</Option>
<Option value="ME">MAINE-ME</Option>
<Option value="MH">MARSHALL ISLANDS-MH</Option>
<Option value="MD">MARYLAND-MD</Option>
<Option value="MA">MASSACHUSETTS-MA</Option>
<Option value="MI">MICHIGAN-MI</Option>
<Option value="MN">MINNESOTA-MN</Option>
<Option value="MS">MISSISSIPPI-MS</Option>
<Option value="MO">MISSOURI-MO</Option>
<Option value="MT">MONTANA-MT</Option>
<Option value="NE">NEBRASKA-NE</Option>
<Option value="NV">NEVADA-NV</Option>
<Option value="NH">NEW HAMPSHIRE-NH</Option>
<Option value="NJ">NEW JERSEY-NJ</Option>
<Option value="NM">NEW MEXICO-NM</Option>
<Option value="NY">NEW YORK-NY</Option>
<Option value="NC">NORTH CAROLINA-NC</Option>
<Option value="ND">NORTH DAKOTA-ND</Option>
<Option value="OH">OHIO-OH</Option>
<Option value="OK">OKLAHOMA-OK</Option>
<Option value="OR">OREGON-OR</Option>
<Option value="PW">PALAU-PW</Option>
<Option value="PA">PENNSYLVANIA-PA</Option>
<Option value="PR">PUERTO RICO-PR</Option>
<Option value="RI">RHODE ISLAND-RI</Option>
<Option value="SC">SOUTH CAROLINA-SC</Option>
<Option value="SD">SOUTH DAKOTA-SD</Option>
<Option value="TN">TENNESSEE-TN</Option>
<Option value="TX">TEXAS-TX</Option>
<Option value="UT">UTAH-UT</Option>
<Option value="VT">VERMONT-VT</Option>
<Option value="VI">VIRGIN ISLANDS-VI</Option>
<Option value="VA">VIRGINIA-VA</Option>
<Option value="WA">WASHINGTON-WA</Option>
<Option value="WV">WEST VIRGINIA-WV</Option>
<Option value="WI">WISCONSIN-WI</Option>
<Option value="WY">WYOMING-WY</Option>
</select>                   				

				</td>
				<td style="text-align:right">Zip</td>
				<td>
				<input type="text" name="txtzip" id="txtzip" size="20" value=""></td>
			</tr>
			<tr>
				<td style="text-align:right">Phone Number</td>
				<td>
                		<input type="text" name="txtstorephonenumber" id="txtstorephonenumber" size="20" value=""></td>
				<td style="text-align:right">Fax Number</td>
				<td>
				<input type="text" name="txtstorefaxnumber" id="txtstorefaxnumber" size="20" value=""></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="text-align:right">Site Type&nbsp;<font color="red">&Dagger;</font></td>
				<td>
                		<select size="1" name="txtstoretype" id="txtstoretype">
                        		<option selected value=""</option>
                        		<?php
                        		$filename=$basedir."site-type.txt";
                        		$fp= fopen($filename, "r");
                        		$contents= fread($fp,filesize($filename));
                        		fclose($fp);
                        		$file_lines= explode("\n",$contents);
                        		foreach ($file_lines as $line){
                                		echo '<option value="' . $line . '">' . $line . '</option>';
                        		}
                        		?>
                		</select>
				</td>
				<td style="text-align:right">Region/DC&nbsp;<font color="red">&Dagger;</font></td>
				<td>
                		<select size="1" name="txtdc" id="txtdc">
                        		<option selected value=""</option>
                        		<?php
                        		$filename=$basedir."region.txt";
                        		$fp= fopen($filename, "r");
                        		$contents= fread($fp,filesize($filename));
                        		fclose($fp);
                        		$file_lines= explode("\n",$contents);
                        		foreach ($file_lines as $line){
                                		echo '<option value="' . $line . '">' . $line . '</option>';
                        		}
                        		?>
                		</select>
				</td>
				<td style="text-align:right">FSR&nbsp;<font color="red">&Dagger;</font></td>
				<td>
                		<select size="1" name="txtbsfsr" id="txtbsfsr">
                        		<option selected value="" ></option>
                        		<?php
                        		$filename=$basedir."fsr.txt";
                        		$fp= fopen($filename, "r");
                        		$contents= fread($fp,filesize($filename));
                        		fclose($fp);
                        		$file_lines= explode("\n",$contents);
                        		foreach ($file_lines as $line){
                                		echo '<option value="' . $line . '">' . $line . '</option>';
                        		}
                        		?>
                		</select>
				</td>
				<td style="text-align:right">Support Center&nbsp;<font color="red">&Dagger;</font></td>
				<td>
                		<select size="1" name="txtadp" id="txtadp">
                        		<option selected value=<?php echo $row['SUPPORT_CENTER'];?> >
					<?php echo $row['SUPPORT_CENTER'];?></option>
                        		<?php
echo"fcl";
/*
                        		$filename=$basedir."support-centers.txt";
                        		$fp= fopen($filename, "r");
                        		$contents= fread($fp,filesize($filename));
                        		fclose($fp);
                        		$file_lines= explode("\n",$contents);
*/
$file_lines = $db_class->get_support_centers();
echo "file_lines = ".count($file_lines);
                        		foreach ($file_lines as $line){
                                		echo '<option value="' . $line . '">' . $line . '</option>';
                        		}
                        		?>
                		</select>
				</td>
			</tr>
			<tr>
				<td style="text-align:right">IP address&nbsp;<font color="red">&Dagger;</font></td>
				<td>
                			<input type="text" name="txtip" id="txtip" size="20" value="">
				</td>
                                <td style="text-align:right">Gateway&nbsp;<font color="red">&Dagger;</font></td>
                                <td>
                                        <input type="text" name="txtrouterip" id="txtrouterip" size="20" value="">
                                </td>
                                <td style="text-align:right">Netmask</td>
                                <td>
                                        <input type="text" name="txtlannetmask" id="txtlannetmask" size="20" value="">
                                </td>

				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align:right">Group Contact</td>
						<td><input type="text" name="txtgroupcontact" id="txtgroupcontact" size="20" value=""></td>
						<td style="text-align:right">Group Contact Number</td>
					<td><input type="text" name="txtgroupnumber" id="txtgroupnumber" size="20" value=""></td>
						<td style="text-align:right">Group Fax/Email</td>
						<td>
                <input type="text" name="txtgroupemail" id="txtgroupemail" size="20" value=""></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
&nbsp;
        </fieldset>
        <br>&nbsp;&nbsp;<font color="red">&Dagger;&nbsp;&nbsp;</font>Required Feilds
	<br>&nbsp;
<?php //if (strtoupper($_GET['provision']) != "VSAT"){ ?>
	<fieldset style="padding: 2">
                <legend>Inet Provider Info</legend>
                <table border="1" cellspacing="1" width="100%">
			<tr>
						<td style="text-align:right">Internet Provider</td>
						<td>
                <input type="text" name="txtbroadbandprovider" id="txtbroadbandprovider" size="20" value=""></td>
						<td style="text-align:right">Internet provider Number</td>
						<td>
                <input type="text" name="txtbroadbandnumber" id="txtbroadbandnumber" size="20" value=""></td>
						<td style="text-align:right">Inet provider Web</td>
						<td>
                <input type="text" name="txtbroadbandurl" id="txtbroadbandurl" size="20" value=""></td>
					</tr>
					<tr>
						<td style="text-align:right">Phone Number DSL Will connect to</td>
						<td>
                <input type="text" name="txtdslnumber" id="txtdslnumber" size="20" value=""></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align:right">PPPoE/PPPoA Username</td>
						<td>
                <input type="text" name="txtdslusername" id="txtdslusername" size="20" value=""></td>
						<td style="text-align:right">PPPoE/PPPoA Password</td>
						<td>
                <input type="text" name="txtdslpassword" id="txtdslpassword" size="20" value=""></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align:right">IP address</td>
						<td>
                <input type="text" name="txtpublicipaddress" id="txtpublicipaddress" size="20" value=""></td>
						<td style="text-align:right">Gateway</td>
						<td>
                <input type="text" name="txtdefaultgateway" id="txtdefaultgateway" size="20" value=""></td>
						<td style="text-align:right">Netmask</td>
						<td> <input type="text" name="txtnetmask" id="txtnetmask" size="20" value=""></td>
					</tr>
				</table>
				<p>
                <br>
                Admin Notes <br>
        <textarea cols="80" rows="5"name="txtnotes2" id="txtnotes2" ></textarea>
                &nbsp;</p>
        </fieldset>
<?php //} ?>
        <br>
        <input class="button" type="submit" value="Submit" name="B1">
	<input class="button" type="button"  value="Cancel" name="B2" onclick="window.location='main.php'">
</form>

</body>

</html>
