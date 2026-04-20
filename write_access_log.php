<?php
//include('logon.php');
include_once("site-monitor.conf.php");

$conn = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conn) {
   die('Could not connect: ' . mysqli_error());
}
//mysqli_select_db(NETZ_DATABASE);
        $sql2="Insert into ACCESSLOG set PAGE = '" . $_SERVER['SCRIPT_NAME']."',";
        $sql2= $sql2 . " ACCESS_DATE_TIME = '" . date("Y-m-d G:i:s")."',";
        $sql2= $sql2 . " USERS_IP = '" . $_SERVER['REMOTE_ADDR']."',";

// if $_POST['txtsitename'] is not set set it to "" to stop errors
if (! isset($_POST['txtsitename'])){
        $post="";
}else{
        $post=$_POST['txtsitename'];
}
if ($post == ""){ if (isset($_GET['site'])){ $post=$_GET['site']; }else{ $post="";} }
if ($post == ""){ if (isset($_POST['site'])){ $post=$_POST['site']; }else{ $post="";} }
if ($post == ""){ if (isset($_POST["txtSearch"])){ $post=$_POST["txtSearch"]; }else{ $post="";} }
if ($post == ""){ if (isset($_POST["txtadvancedsearch"])){ $post=$_POST["txtadvancedsearch"]; }else{ $post="";} }
if ($post == ""){ if (isset($_POST['txtgroup'])){ $post=$_POST['txtgroup']; }else{ $post="";} }
if ($post == ""){ if (isset($_GET['group'])){ $post=$_GET['group']; }else{ $post="";} }
if ($post == ""){

if (isset($_POST['txtuserlevel'])){ $userlevel=$_POST['txtuserlevel']; }else{$userlevel="";}
if (isset($_POST['txtusername'])){ $username=$_POST['txtusername']; }else{$username="";}
if (isset($_POST['txtpassword'])){ $password=$_POST['txtpassword']; }else{$password="";}
if (isset($_POST['chkforcereset'] )){ $check_for_reset=$_POST['chkforcereset']; }else{$check_for_reset="";}
        if ($userlevel != ""){
                $post= "User=" . $username;
                $post= $post ." Level=" . $userlevel;
                if ($password != ""){
                        $post= $post . " *Password Changed*";
                }
                if ($check_for_reset != ""){
                                $post= $post . " Force Pass Reset=" . $check_for_reset;
                }
        }
}
if ($post != ""){$post= " ".$post;}
/*
$be_var = $_POST;
foreach ( $be_var as $key => $value ) {
$post = $post . " ".$key."=".$value;
}
$post =  str_replace( "Array&", "", $post ); // fruit=banana&color=yellow
$post =  str_replace( "\n", " ", $post );
$post =  str_replace( "\r", " ", $post );
*/

//$post=str_replace( "Array", "", $post );

/*
$sitepost=$_POST['site'];
if (trim($sitepost) == ""){$sitepost=$_POST['siteid'];}
if (trim($sitepost) == ""){$sitepost=$_GET['site'];}
*/

        $sql2= $sql2 . " QUERY_STRING = '" . $_SERVER['QUERY_STRING']. $post ."',";
	if (! isset($_SESSION['user'])){
		$sql2= $sql2 . " USERNAME = 'Guest-".$_SERVER['REMOTE_ADDR']."',";
	}else{
	        $sql2= $sql2 . " USERNAME = '" . addslashes($_SESSION['user'])."',";
	}
        if (! isset($_SESSION['accesslevel'])){
                $sql2= $sql2 . " ACCESSLEVEL = 0";
        }else{
                $sql2= $sql2 . " ACCESSLEVEL = " . $_SESSION['accesslevel'];
        }
        $query2 = @mysqli_query($conn,$sql2);
?>
