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

include_once('auth.php');
include("site-monitor.conf.php");

$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);
$results = mysqli_query($conn,"select * from SITEDATA");
if (!$results) {
   die('Query failed: ' . mysqli_error());
}


	$sql="SELECT * FROM USERS WHERE USERNAME = '".addslashes($_GET['user'])."'";
	$results = mysqli_query($conn,$sql);
	$rows = mysqli_fetch_assoc($results)
?>
<html>
<head>

<?php $style=$_SESSION['style']; if ($style==""){$style="style/ultramarine.css";}?>
<link rel="stylesheet" href="<?php echo $style  ?>" type="text/css">
<script type="text/javascript">
function checkpass()
{
        var pass1 ;
        var pass2 ;
	var message ;
	var minlength = 6;
	var maxlength = 12;
	message = "Password must be \nMinium "+minlength+ " characters \n Maximum  "+maxlength+" characters \nNo Spaces";
        pass1=document.getElementById('txtpassword').value;
        pass2=document.getElementById('txtpassword0').value;
        if (pass1==pass2)
        {
		if (pass1.length >= minlength && pass1.length <= maxlength)
		{
			if (pass1.indexOf(" ") > -1)
			{
				alert(message);
				return false;
			}else{
				return true;
			}
		}else{
		  	alert(message);
			return false;
		}
                
        }
        else
        {
                alert ("Paswords don\'t match");
                return false ;
        }
}

</script>
</head>
<body >
<form method="POST" action="write_user_pass_change.php" id="userformi" onsubmit="return checkpass()">
<br>
<fieldset>
<Legend>Change User <?php echo $rows['USERNAME'] ?> password </legend>
<input type="hidden" name="txtusername" id="txtusername" value="<?php echo addslashes($rows['USERNAME']) ?>">
Password &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input AUTOCOMPLETE="OFF" type="password" name="txtpassword" id="txtpassword" size="15" value="">
<br>
Verify Password <input AUTOCOMPLETE="OFF" type="password" name="txtpassword0" id="txtpassword0" size="15" value="">

<br>
</fieldset>
<br>
<input class="button" type="submit" value="Update" name="B2">

<?php if (!empty($_SESSION['passreset']))
{
 ?>
	<input class="button" type="button" value="Cancel" name="B3" onclick="javascript:window.location = 'logoff.php'"></p>
<?php
}
else
{ ?>
	<input class="button" type="button" value="Cancel" name="B3" onclick="javascript:window.close()"></p>
<?php
}
?>

</form>
</body></html>



