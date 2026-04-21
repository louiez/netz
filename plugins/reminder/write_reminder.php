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

include('../../logon.php');
include_once("../../site-monitor.conf.php");
include('../../write_access_log.php');
include_once("../../lmz-functions.php");
// open connection to Database server
$conns = mysqli_connect(NETZ_DB_SERVER, NETZ_DB_USERNAME, NETZ_DB_PASSWORD,NETZ_DATABASE);
if (!$conns) {
   die('Could not connect: ' . mysqli_error());
}
$action=$_GET['action'];
if ($action == ""){
	$remindername=addslashes(remove_code($_POST['reminder_name']));
	$reminderdate=$_POST['reminder_date'];
	$remindertime=$_POST['reminder_time'];
	$t=$reminderdate." ".remove_code($remindertime);
	$reminder_date_time=strftime("%Y-%m-%d %H:%M:%S",strtotime(trim(remove_code($t))));
	$reminder_advance_time=remove_code($_POST['reminder_advance_time']);
	$description=addslashes(remove_code($_POST['reminder_description']));
	$id=$_POST['reminder_id'];

	if ($remindername == "" || $reminderdate=="" || $remindertime=="" || $id==""){
		echo "Missing data ! \n NO Reminder set";
	}else{
		$SQL = "INSERT INTO REMINDERS SET  USERNAME = '".$_SESSION['user']."',";
		$SQL .= "REMINDER_NAME = '".$remindername."',";
		$SQL .= "REMINDER_DATE_TIME = '".$reminder_date_time."',";
		$SQL .= "REMINDER_ADVANCE_TIME = '".$reminder_advance_time."',";
		$SQL .= "DESCRIPTION = '".$description."',";
		$SQL .= "REMINDER_ID = '".$id."'";
		// Query database
		mysqli_query($conn,$SQL);
	//	echo $SQL."\n";
	}
}elseif ($action == "edit"){
        $remindername=addslashes(remove_code($_POST['reminder_name']));
        $reminderdate=$_POST['reminder_date'];
        $remindertime=$_POST['reminder_time'];
        $t=$reminderdate." ".remove_code($remindertime);
        $reminder_date_time=strftime("%Y-%m-%d %H:%M:%S",strtotime(trim(remove_code($t))));
        $reminder_advance_time=remove_code($_POST['reminder_advance_time']);
        //$description=addslashes(remove_code($_POST['reminder_description']));
	$description=remove_code($_POST['reminder_description']);
        $id=$_POST['reminder_id'];

        if ($remindername == "" || $reminderdate=="" || $remindertime=="" || $id==""){
                echo "Missing data ! \n reminder NOT Changed";
        }else{
                $SQL = "UPDATE REMINDERS SET  USERNAME = '".$_SESSION['user']."',";
                $SQL .= "REMINDER_NAME = '".$remindername."',";
                $SQL .= "REMINDER_DATE_TIME = '".$reminder_date_time."',";
                $SQL .= "REMINDER_ADVANCE_TIME = '".$reminder_advance_time."',";
                $SQL .= "DESCRIPTION = '".$description."' ";
                $SQL .= "WHERE REMINDER_ID = '".$id."'";
                // Query database
                mysqli_query($conn,$SQL);
              //echo $SQL."\n";
        }	
}elseif ($action == "dismiss"){
	$id=$_GET['id'];
	$SQL = "DELETE FROM REMINDERS WHERE REMINDER_ID = '".$id."'";
	mysqli_query($conn,$SQL);
}elseif ($action == "snooze"){
	$local_time_zone_offset = (date("Z")/ 3600);
	// get user time offset
	$SQL_user = "SELECT * FROM USERS WHERE USERNAME = '" .$_SESSION['user']."'";
	$result_user=mysqli_query($conn,$SQL_user);
	$row_user = mysqli_fetch_assoc($result_user);
	$user_time_zone_offset = $row_user['TIME_ZONE_OFFSET'];
	// Calculate offset differance from user to server
	$total_offset = abs($local_time_zone_offset - $user_time_zone_offset) * 60;
        $id=$_GET['id'];
	$snooze_time=10 - $total_offset;
	$snooze_time= mktime(date("H"),date("i")+$snooze_time,date("s"), date("m"), date("d") , date("Y"));
	$snooze_time=date("Y-m-d G:i:s",$snooze_time);
        $SQL = "UPDATE REMINDERS SET REMINDER_DATE_TIME = '".$snooze_time."'";
	$SQL .= "WHERE REMINDER_ID = '".$id."'";
        mysqli_query($conn,$SQL);
        $SQL = "UPDATE REMINDERS SET REMINDER_ADVANCE_TIME = '0' ";
        $SQL .= "WHERE REMINDER_ID = '".$id."'";
        mysqli_query($conn,$SQL);
}

?>

