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

include_once("auth.php");
include_once("site-monitor.conf.php");
include('write_access_log.php');

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
<?php
//      +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
//      |     User Access code                |
// =====================================================================================================//
//$acl=$_SESSION['accesstype'];                                                                   	//
//if ($acl != "rwa"){                                                                                     //
if ($_SESSION['accesslevel'] <= 8){
        echo '<script type="text/javascript">window.location.href="access_denied.html"</script>';       //
        echo '<meta http-equiv="refresh" content="0;url=access_denied.html" />';                        //
        }                                                                                               //
// =====================================================================================================//
?>

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
			// if there is a space reject
                        if (pass1.indexOf(" ") > -1)
                        {
                                alert(message);
                                return false;
                        }else{
                                return true;
                        }
		}else if (pass1 == ""){  // empty pass assume no change - handle in write page
			return true;
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

function echeck(str) {

                var at="@"
                var dot="."
                var lat=str.indexOf(at)
                var lstr=str.length
                var ldot=str.indexOf(dot)
                if (str.indexOf(at)==-1){
                   alert("Invalid E-mail ID")
                   return false
                }

                if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
                   alert("Invalid E-mail ID")
                   return false
                }

                if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
                    alert("Invalid E-mail ID")
                    return false
                }

                 if (str.indexOf(at,(lat+1))!=-1){
                    alert("Invalid E-mail ID")
                    return false
                 }

                 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
                    alert("Invalid E-mail ID")
                    return false
                 }

                 if (str.indexOf(dot,(lat+2))==-1){
                    alert("Invalid E-mail ID")
                    return false
                 }

                 if (str.indexOf(" ")!=-1){
                    alert("Invalid E-mail ID")
                    return false
                 }

                 return true
        }

function ValidateForm(){
        var emailID=document.getElementById('txtemail')
	var rtn;
	emailID.value = Trim(emailID.value);
        if ((emailID.value==null)||(emailID.value=="")){
                alert("Please Enter your Email ID")
                emailID.focus()
                return false
        }
        if (echeck(emailID.value)==false){
                //emailID.value=""
                emailID.focus()
                return false
        }
        rtn = checkpass();
//alert("done");
	if (rtn == true){
        	return true;
	}else{
		return false;
	}
 }
function Trim(STRING){
STRING = LTrim(STRING);
return RTrim(STRING);
}

function RTrim(STRING){
while(STRING.charAt((STRING.length -1))==" "){
STRING = STRING.substring(0,STRING.length-1);
}
return STRING;
}


function LTrim(STRING){
while(STRING.charAt(0)==" "){
STRING = STRING.replace(STRING.charAt(0),"");
}
return STRING;
}

</script>
</head>
<body >
<form method="POST" action="write_usermod.php" id="userformi" onsubmit="return ValidateForm()" autocomplete="off">
<center><h2><font size="4">Modify User</font></h2>
</center>
	<p>Username&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="txtusername" size="16" value="<?php echo $rows['USERNAME'] ?> ">&nbsp;
	</p>
<p>Full name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="txtfullname" size="16" value="<?php echo $rows['FULL_NAME'] ?> ">&nbsp;
	</p>
<br>
<p>Job Title&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 <input type="text" name="txtjobtitle" size="30" value="<?php echo trim($rows['TITLE']) ?>">
<br>
Department/Group&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="txtdepartmentgroup" size="30" value="<?php echo trim($rows['DEPARTMENTGROUP']) ?>">&nbsp;
 </p>
<br>

<hr style="color:DarkBlue; background: DarkBlue; border: 0; Height:2px;">
<br>
<p>Password&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input AUTOCOMPLETE="OFF" type="password" name="txtpassword" id="txtpassword" size="15" value="">&nbsp;&nbsp;&nbsp;&nbsp; 
	</p>
<p>Verify Password <input autocomplete="OFF" type="password" name="txtpassword0" id="txtpassword0" size="15" value="">&nbsp;
</p>
<p>
	<input type="checkbox" name="chkforcereset" id="chkforcereset" value="ON">User must change password</p>
<br>
<hr style="color:DarkBlue; background: DarkBlue; border: 0; Height:2px; ">
<br>
<p>E-Mail&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
<input type="text" name="txtemail" id="txtemail" size="28" value="<?php echo trim($rows['EMAIL']) ?> ">&nbsp;&nbsp;&nbsp;</p>
<p>
	<?php echo "&nbsp; Access Type <b><font color=\"red\">&nbsp;&nbsp;".  $rows["ACCESSTYPE"]."</font></b>"; ?>
	<br>
	<br>
&nbsp;Access Level&nbsp;&nbsp;
        <select size="1" name="txtuserlevel">
        <?php echo "<option value='" .$rows["ACCESSLEVEL"]. "' SELECTED  >" .$rows['ACCESSLEVEL']. "</option>"; ?>
        <option value="0">Disabled (0)</option>
        <option value="1" >read only (1)</option>
        <option value="2">read only ops (2)</option>
        <option value="3">read only unused (3)</option>
        <option value="4">read/write order (4)</option>
        <option value="5">read/write unused (5)</option>
        <option value="6">read/write ops (6)</option>
        <option value="7">read/write ops (7)</option>
        <option value="8">read/write unused (8)</option>
        <option value="9">Admin (9)</option>
        <option value="10">Admin Full (10)</option>
        </select><br>
        <br>
<pre>
0 = Disabled - gives access to nothing
1 = ro (support page) (Wan Health) (down Sites)
2 = ro ops (support page) (Wan Health) (down Sites) (ro ops/query page no pass)
3 = UNUSED
4 = rw order (support page) (Wan Health) (down Sites) (ro ops no pass) (query page no pass) (order page)
5 = UNUSED
6 = UNUSED
7 = rw (support page) (Wan Health) (down Sites) (ops) (query builder) (order page)
8 = UNUSED
9 = admin user ( Everything except Netz config )
10 = admin ( everything )

</pre>	
	<input class="button" type="submit" value="Update" name="B2">&nbsp;
<input class="button" type="button" value="Cancel" name="B3" onclick="javascript:window.location ='useradmin.php'"></p>
</form>
</body></html>
