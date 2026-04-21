<?php
/*###############################################################
        NETz Network Management system				#
        http://www.proedgenetworks.com/netz			#
								#
								#
        Copyright (C) 2005-2026 Louie Zarrella			#
	louiez@proedgenetworks.com				#
								#
        Released under the GNU General Public License		#
	Copy of License available at :				#
	http://www.gnu.org/copyleft/gpl.html			#
###############################################################*/
ini_set('display_errors', 1);  // Display errors on the page
error_reporting(E_ALL);

ob_start(); 
include_once("auth.php");
include_once("site-monitor.conf.php");
include_once("lmz-functions.php");
include('write_access_log.php');

if ($_SESSION['accesslevel'] < 7){
        echo '<html><head><script type="text/javascript">window.location.href="access_denied.html"</script>';       //
        echo '<meta http-equiv="refresh" content="0;url=access_denied.html" /></head><body></body><html>';                        //
        }                                                                                               //
// =====================================================================================================//
$logfile = $netzlogs."db-change.log";
$DELETERECORD=trim($_POST["txtdelete"]);
$APTR=chr(39);
//$CALLINGPAGE=$_GET["callingpage"]; // not sure what i used this for. but i do not see it anywhere else
//IP = request.form("txtIPADDRESS")

$STRSTORENUM=trim($_POST['txtstorenumber']);

$conn=mysqli_connect("localhost",NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
//mysqli_select_db(NETZ_DATABASE);
//@mysqli_select_db(NETZ_DATABASE) or die( "Unable to select database");
function change_made($feild,$posttxt){
	// declare the global variables above as global here too
	global $conn, $logfile, $STRSTORENUM; 
	$sql="SELECT ".$feild." FROM SITEDATA JOIN MONITORINFO USING(SITE_ID) WHERE SITE_ID = '".$STRSTORENUM."'";
	$r=mysqli_query($conn,$sql);
	$rw = mysqli_fetch_assoc($r);
	if ($posttxt != $rw[$feild]){
		$err_msg=" ".$STRSTORENUM." - ".$feild ."(".Display_name($feild);
		$err_msg .= ") Changed from (".$rw[$feild].") to (".$posttxt. ") By ".$_SESSION['user'];
		error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
		return 1;
	}else{
		return 0;
	}
}
if ($DELETERECORD=="")	//~~~ Delete record was not requested
{
 	$SQLSTR="SELECT * FROM SITEDATA JOIN MONITORINFO USING(SITE_ID)";
  	$SQLSTR .=" WHERE SITE_ID = '". $STRSTORENUM ."'";
  	$rs=mysqli_query($conn,$SQLSTR);//~~~ check if the store number exists and add it if not  
	//**DEBUG//echo $SQLSTR."<br>";
    	if ((mysqli_num_rows($rs) < 1) && trim($_POST["txtstorenumberbac"])=="")
  	{
    		$SQLSTR="INSERT INTO SITEDATA (SITE_ID) VALUES (".$APTR.$STRSTORENUM.$APTR.")";
    		mysqli_query($conn,$SQLSTR);   
		//**DEBUG//echo $SQLSTR."<br>";
		$err_msg=" ".$STRSTORENUM." - New Site Added By ".$_SESSION['user'];
		error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
	}    
      	elseif ((mysqli_num_rows($rs) > 0) && trim($_POST["txtnewrecord"])=="new")
    	{   
		echo  "Site already exists <a href='' onclick='javascript:document.history.back()'> Back </a>";
        	exit();
    	}
	// Normal change of existing record
    	elseif ((trim($_POST["txtstorenumber"]) != trim($_POST["txtstorenumberbac"])) && (mysqli_num_rows($rs) < 1))
  	{
			
		//using the hidden textbox "txtstorenumberbac"
        	//~~~~ if the store number was changed make the change to the database here
		//~~~ Update Store Number
    		$SQLSTR="UPDATE SITEDATA SET SITE_ID = ";
    		$SQLSTR .= $APTR.trim(remove_code($_POST["txtstorenumber"])).$APTR;
		$SQLSTR .=  " WHERE SITE_ID = ".$APTR.trim($_POST["txtstorenumberbac"]).$APTR;
    		mysqli_query($conn,$SQLSTR); 
		$SQLSTR="UPDATE MONITORINFO SET SITE_ID = ";
                $SQLSTR .= $APTR.trim(remove_code($_POST["txtstorenumber"])).$APTR;
                $SQLSTR .=  " WHERE SITE_ID = ".$APTR.trim($_POST["txtstorenumberbac"]).$APTR;
                mysqli_query($conn,$SQLSTR);
		//**DEBUG//echo $SQLSTR."<br>";		
		// Update Monitor and down site info
                $SQLSTR="UPDATE MONLOGS SET SITE_ID = ";
                $SQLSTR .= $APTR.trim(remove_code($_POST["txtstorenumber"])).$APTR." WHERE SITE_ID = ".$APTR.trim($_POST["txtstorenumberbac"]).$APTR;
                mysqli_query($conn,$SQLSTR);
                // Update HTTP Monitor and down site info
                $SQLSTR="UPDATE HTTPMONLOGS SET SITE_ID = ";
                $SQLSTR .= $APTR.trim(remove_code($_POST["txtstorenumber"])).$APTR." WHERE SITE_ID = ".$APTR.trim($_POST["txtstorenumberbac"]).$APTR;
                mysqli_query($conn,$SQLSTR);
                // Update ATTACHMENTS info
                $SQLSTR="UPDATE ATTACHMENTS SET SITE_ID = ";
                $SQLSTR .= $APTR.trim(remove_code($_POST["txtstorenumber"])).$APTR." WHERE SITE_ID = ".$APTR.trim($_POST["txtstorenumberbac"]).$APTR;
                mysqli_query($conn,$SQLSTR);
                // Update ALERTEMAILS info
                $SQLSTR="UPDATE ALERTEMAILS SET LOCATION = ";
                $SQLSTR .= $APTR.trim(remove_code($_POST["txtstorenumber"])).$APTR." WHERE LOCATION = ".$APTR.trim($_POST["txtstorenumberbac"]).$APTR;
                mysqli_query($conn,$SQLSTR);
		//**DEBUG//echo $SQLSTR."<br>";

		//******************************//		
		// rename the RRDtool database	//
        	//**************************************************************//
        	// cleanup valid site names to valid filenames                  //
        	// NETz allows names that may not be legal as file names        //
        	//**************************************************************//
        	$allowed = '/[^a-z0-9\\.\\-\\_\\\\]/i';                         //
		$site=trim(remove_code($_POST["txtstorenumber"]));		//
		$old_site=trim($_POST["txtstorenumberbac"]);			//
        	$rrdfilename=preg_replace($allowed,"",$site);            	//
		$old_site_rrd=preg_replace($allowed,"",$old_site);		//
		$old_site_rrd= $basedir.'rrd/'.$old_site_rrd.'.rrd';		//
        	$rrdfilename= $basedir.'rrd/'.$rrdfilename.'.rrd';              //
        	exec("mv ".$old_site_rrd." ".$rrdfilename);			//
        	//**************************************************************//

		//******************************//
		//	Log the Site ID Change	//
		//******************************//
		$err_msg=" ".trim($_POST["txtstorenumberbac"])." - Site ID  Changed from (".trim($_POST["txtstorenumberbac"]);
		$err_msg=$err_msg.") to (".trim(remove_code($_POST["txtstorenumber"])). ") By ".$_SESSION['user'];
		error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);

  	}elseif ((trim($_POST["txtstorenumber"]) != trim($_POST["txtstorenumberbac"])) && (mysqli_num_rows($rs) > 0)) { 
		// site rename but new name already usde
	  	echo "Site already exists <a href='ops.php?site=".trim($_POST["txtstorenumberbac"])."' > Back </a>";
		exit();
	}

//**************  Write Ops ALL *********************	

  	if ($CALLINGPAGE!="new")
  	{
//Update SITE_TYPE
	change_made("SITE_TYPE",trim(remove_code($_POST["txtstoretype"])));
	$SQLSTR="Update SITEDATA Set SITE_TYPE = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtstoretype"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);   
	//**DEBUG//echo $SQLSTR."<br>";
//Update Service Request Date

    		if (trim($_POST["txtrequestdate"])!="")
    		{
			$t=strftime("%Y-%m-%d",strtotime(trim(remove_code($_POST["txtrequestdate"]))));
                	change_made("SERVICE_REQUEST_DATE",$t);
      			$SQLSTR="Update SITEDATA Set SERVICE_REQUEST_DATE = ";
			//$SQLSTR .= $APTR.date('Y-m-d h:i:s A',strtotime(trim(remove_code($_POST["txtrequestdate"])))).$APTR;
			$SQLSTR .= $APTR.$t.$APTR;
			$SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
      			mysqli_query($conn,$SQLSTR);
			//**DEBUG//echo $SQLSTR."<br>";
    		}
      		else
    		{
			change_made("SERVICE_REQUEST_DATE","");
      			$SQLSTR="Update SITEDATA Set SERVICE_REQUEST_DATE = ";
      			$SQLSTR .= "Null"." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
      			mysqli_query($conn,$SQLSTR);  
			//**DEBUG//echo $SQLSTR."<br>";
		}
		//###############################
		//	Update Active Date	#
		//###############################
    		if (trim($_POST["txtvpnactivedate"])!="")
    		{
			$t=strftime("%Y-%m-%d",strtotime(trim(remove_code($_POST["txtvpnactivedate"]))));
			change_made("ACTIVE_DATE",$t);
      			$SQLSTR="Update SITEDATA Set ACTIVE_DATE = ";
      			//$SQLSTR .= $APTR.date('Y-m-d h:i:s A',strtotime(trim(remove_code($_POST["txtvpnactivedate"])))).$APTR;
			$SQLSTR .= $APTR.$t.$APTR;
			$SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
      			mysqli_query($conn,$SQLSTR); 
			//**DEBUG//echo $SQLSTR."<br>";
    		}
      		else
    		{
			change_made("ACTIVE_DATE","");
      			$SQLSTR="Update SITEDATA Set ACTIVE_DATE = ";
      			$SQLSTR .= "Null"." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
      			mysqli_query($conn,$SQLSTR);
			//**DEBUG//echo $SQLSTR."<br>";
    		} 
		//###############################
		//	Update Site Close Date	#
		//###############################

    		if (trim($_POST["txtstoreclosedate"])!="")
    		{
			$t=strftime("%Y-%m-%d",strtotime(trim(remove_code($_POST["txtstoreclosedate"]))));
			change_made("CLOSE_DATE",$t);
      			$SQLSTR="Update SITEDATA Set CLOSE_DATE = ";
      			//$SQLSTR .= $APTR.date('Y-m-d h:i:s A',strtotime(trim(remove_code($_POST["txtstoreclosedate"])))).$APTR;
			$SQLSTR .= $APTR.$t.$APTR;
			$SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
      			mysqli_query($conn,$SQLSTR);
			//**DEBUG//echo $SQLSTR."<br>";
    		}
      		else
    		{
			change_made("CLOSE_DATE","");
      			$SQLSTR="Update SITEDATA Set CLOSE_DATE = ";
      			$SQLSTR .= "Null"." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
      			mysqli_query($conn,$SQLSTR); 
			//**DEBUG//echo $SQLSTR."<br>";
    		} 


//Update SITE_IMAGE_MAP
	//change_made("SITE_IMAGE_MAP",trim(remove_code($_POST["txtnetworkmapimage"])));
	change_made("SITE_IMAGE_MAP",trim($_POST["txtnetworkmapimage"]));
	$SQLSTR="Update SITEDATA Set SITE_IMAGE_MAP = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtnetworkmapimage"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);   
	//**DEBUG//echo $SQLSTR."<br>";
//Update GROUP_IMAGE_MAP
	change_made("GROUP_IMAGE_MAP",trim(remove_code($_POST["txtgroupimage"])));
	$SQLSTR="Update SITEDATA Set GROUP_IMAGE_MAP = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtgroupimage"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);    
	//**DEBUG//echo $SQLSTR."<br>";
//Update Router Model
	change_made("ROUTER_MODEL",trim(remove_code($_POST["txtroutermodel"])));
	$SQLSTR="Update SITEDATA Set ROUTER_MODEL = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtroutermodel"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);    
	//**DEBUG//echo $SQLSTR."<br>";
//Update Router Firmware rev
	change_made("ROUTER_FIRMWARE_REV",trim(remove_code($_POST["txtrouterfirmwarerev"])));
	$SQLSTR="Update SITEDATA Set ROUTER_FIRMWARE_REV = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtrouterfirmwarerev"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR); 
	//**DEBUG//echo $SQLSTR."<br>";
//Update Router Serial Number
	change_made("ROUTER_SERIAL_NUM",trim(remove_code($_POST["txtrouterserialnumber"])));
	$SQLSTR="Update SITEDATA Set ROUTER_SERIAL_NUM = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtrouterserialnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
	//**DEBUG//echo $SQLSTR."<br>";
//Update Router Asset number
	change_made("ROUTER_ASSET_NUM",trim(remove_code($_POST["txtrouterassetnumber"])));
	$SQLSTR="Update SITEDATA Set ROUTER_ASSET_NUM  = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtrouterassetnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
	//**DEBUG//echo $SQLSTR."<br>";
//Update Router Access Username
	change_made("ROUTER_ACCESS_USERNAME",trim(remove_code($_POST["txtrouteraccessusername"])));
	$SQLSTR="Update SITEDATA Set ROUTER_ACCESS_USERNAME  = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtrouteraccessusername"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
//Update Router Access Password
        //**********************************************************************//
        //      Did not want the Password in the logs... so log it seperate     //
        //**********************************************************************************************//
        $sql="SELECT ROUTER_ACCESS_PASSWORD FROM SITEDATA WHERE SITE_ID = '".$STRSTORENUM."'";          //
        $r=mysqli_query($conn,$sql);                                                                   	//
        $rw = mysqli_fetch_assoc($r);                                                            	//
        if (trim(remove_code($_POST["txtrouteraccesspassword"])) != $rw['ROUTER_ACCESS_PASSWORD']){     //
                $err_msg=" ".$STRSTORENUM." - Router Access Password Changed By ".$_SESSION['user'];	//
                error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);                      	//
        }                                                                                       	//
        //**********************************************************************************************//
	$SQLSTR="Update SITEDATA Set ROUTER_ACCESS_PASSWORD = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtrouteraccesspassword"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
                //###############################
                //      Update Router In Service Date      #
                //###############################
                if (trim($_POST["txtrouterinservicedate"])!="")
                {
                        $t=strftime("%Y-%m-%d",strtotime(trim(remove_code($_POST["txtrouterinservicedate"]))));
                        change_made("ROUTER_INSERVICE_DATE",$t);
                        $SQLSTR="Update SITEDATA Set ROUTER_INSERVICE_DATE = ";
                        //$SQLSTR .= $APTR.date('Y-m-d h:i:s A',strtotime(trim(remove_code($_POST["txtrouterinservicedate"])))).$APTR;
                        $SQLSTR .= $APTR.$t.$APTR;
                        $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
                        mysqli_query($conn,$SQLSTR);
                        //**DEBUG//echo $SQLSTR."<br>";
                }
                else
                {
                        change_made("ROUTER_INSERVICE_DATE","");
                        $SQLSTR="Update SITEDATA Set ROUTER_INSERVICE_DATE = ";
                        $SQLSTR .= "Null"." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
                        mysqli_query($conn,$SQLSTR);
                        //**DEBUG//echo $SQLSTR."<br>";
                }

//Update DLCI ID
	change_made("DLCI_ID",trim(remove_code($_POST["txtdlciid"])));
        $SQLSTR="Update SITEDATA Set DLCI_ID = ";
        $SQLSTR .= $APTR.trim(remove_code($_POST["txtdlciid"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
        mysqli_query($conn,$SQLSTR);
//Update CPE Modem Model
	change_made("CPE_MODEM_MODEL",trim(remove_code($_POST["txtdslmodemmodel"])));
	$SQLSTR="Update SITEDATA Set CPE_MODEM_MODEL = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtdslmodemmodel"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
//Update CPE Modem Firmware Rev
	change_made("CPE_MODEM_FIRMWARE_REV",trim(remove_code($_POST["txtcpemodemfirmwarerev"])));
	$SQLSTR="Update SITEDATA Set CPE_MODEM_FIRMWARE_REV = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtcpemodemfirmwarerev"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
//Update CPE Modem Serial Number
	change_made("CPE_MODEM_SERIAL_NUM",trim(remove_code($_POST["txtcpemodemserialnumber"])));
	$SQLSTR="Update SITEDATA Set CPE_MODEM_SERIAL_NUM = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtcpemodemserialnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
//Update CPE Modem Asset Number
	change_made("CPE_ASSET_NUM",trim(remove_code($_POST["txtcpemodemassetnumber"])));
	$SQLSTR="Update SITEDATA Set CPE_ASSET_NUM = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtcpemodemassetnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
//Update CPE Modem Access Username
	change_made("CPE_ACCESS_USERNAME",trim(remove_code($_POST["txtcpemodemaccessusername"])));
	$SQLSTR="Update SITEDATA Set CPE_ACCESS_USERNAME = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtcpemodemaccessusername"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
//Update CPE Modem Access Password
        //**********************************************************************//
        //      Did not want the Password in the logs... so log it seperate     //
        //**********************************************************************************************//
        $sql="SELECT CPE_ACCESS_PASSWORD FROM SITEDATA WHERE SITE_ID = '".$STRSTORENUM."'";     	//
        $r=mysqli_query($conn,$sql);                                                                   	//
        $rw = mysqli_fetch_assoc($r);                                                            	//
        if (trim(remove_code($_POST["txtcpemodemaccesspassword"])) != $rw['CPE_ACCESS_PASSWORD']){	//
                $err_msg=" ".$STRSTORENUM." - CPE Password Changed By ".$_SESSION['user'];      	//
                error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);                      	//
        }                                                                                       	//
        //**********************************************************************************************//
	$SQLSTR="Update SITEDATA Set CPE_ACCESS_PASSWORD = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtcpemodemaccesspassword"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);
                //###############################
                //      Update CPE In Service Date      #
                //###############################
                if (trim($_POST["txtcpeinservicedate"])!="")
                {
                        $t=strftime("%Y-%m-%d",strtotime(trim(remove_code($_POST["txtcpeinservicedate"]))));
                        change_made("CPE_INSERVICE_DATE",$t);
                        $SQLSTR="Update SITEDATA Set CPE_INSERVICE_DATE = ";
                        //$SQLSTR .= $APTR.date('Y-m-d h:i:s A',strtotime(trim(remove_code($_POST["txtcpeinservicedate"])))).$APTR;
                        $SQLSTR .= $APTR.$t.$APTR;
                        $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
                        mysqli_query($conn,$SQLSTR);
                        //**DEBUG//echo $SQLSTR."<br>";
                }
                else
                {
                        change_made("CPE_INSERVICE_DATE","");
                        $SQLSTR="Update SITEDATA Set CPE_INSERVICE_DATE = ";
                        $SQLSTR .= "Null"." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
                        mysqli_query($conn,$SQLSTR);
                        //**DEBUG//echo $SQLSTR."<br>";
                }
//Update STatic IP range
	change_made("WAN_IP_RANGE",trim(remove_code($_POST["txtstaticiprange"])));
	$SQLSTR="Update SITEDATA Set WAN_IP_RANGE = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtstaticiprange"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);    
//************** Write Both ******************

  	} // END if ($CALLINGPAGE!="new") 

//Update LAN_IP
	change_made("LAN_IP",trim(remove_code($_POST["txtip"])));
  	$SQLSTR="Update SITEDATA Set LAN_IP = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtip"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
//Update Router IP
	change_made("LAN_GATEWAY",trim(remove_code($_POST["txtrouterip"])));
  	$SQLSTR="Update SITEDATA Set LAN_GATEWAY = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtrouterip"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
//Update LAN Netmask
	change_made("LAN_NETMASK",trim(remove_code($_POST["txtlannetmask"])));
        $SQLSTR="Update SITEDATA Set LAN_NETMASK = ";
        $SQLSTR .= $APTR.trim(remove_code($_POST["txtlannetmask"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
        mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
//Update SUPPORT_CENTER
	change_made("SUPPORT_CENTER",trim(remove_code($_POST["txtadp"])));
  	$SQLSTR="Update SITEDATA Set SUPPORT_CENTER = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtadp"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
//Update FIELD_REP
	change_made("FIELD_REP",trim(remove_code($_POST["txtbsfsr"])));
  	$SQLSTR="Update SITEDATA Set FIELD_REP = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtbsfsr"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;

//Update Group
	change_made("GROUP_NAME",trim(remove_code($_POST["txtgroup"])));
  	$SQLSTR="Update SITEDATA Set GROUP_NAME = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtgroup"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;

//Update Site Name
	change_made("SITE_NAME",$_POST["txtsitename"]);
  	$SQLSTR="Update SITEDATA Set SITE_NAME = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtsitename"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";
// Update Latitude
        change_made("LATITUDE",$_POST["txtlatitude"]);
        $SQLSTR="Update SITEDATA Set LATITUDE = ";
        $SQLSTR .= $APTR.trim(remove_code($_POST["txtlatitude"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
        mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
        //**DEBUG//echo $SQLSTR."<br>";
// Update Longitude
        change_made("LONGITUDE",$_POST["txtlongitude"]);
        $SQLSTR="Update SITEDATA Set LONGITUDE = ";
        $SQLSTR .= $APTR.trim(remove_code($_POST["txtlongitude"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
        mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
        //**DEBUG//echo $SQLSTR."<br>";
//Update ADDRESS
	$changed = change_made("ADDRESS",trim(remove_code($_POST["txtaddress"])));
	$SQLSTR="Update SITEDATA Set ADDRESS = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtaddress"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

        //**********************************************//
        //***** Clear Latitude and Longitude values ****//
        //**************************************************************//
	if ($changed == 1){						//
        	$SQLSTR="Update SITEDATA Set LATITUDE = ''";    	//
		$SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;//
        	mysqli_query($conn,$SQLSTR);                  	//
        	$SQLSTR="Update SITEDATA Set LONGITUDE = ''";		//
                $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;//
        	mysqli_query($conn,$SQLSTR);                  	//
	}								//
        //**************************************************************//
	
//Update CITY
	$changed = change_made("CITY",trim(remove_code($_POST["txtcity"])));
  	$SQLSTR="Update SITEDATA Set CITY = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtcity"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

        //**********************************************//
        //***** Clear Latitude and Longitude values ****//
        //**************************************************************//
        if ($changed == 1){                                     	//
                $SQLSTR="Update SITEDATA Set LATITUDE = ''";    	//
                $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;//
                mysqli_query($conn,$SQLSTR);                  	//
                $SQLSTR="Update SITEDATA Set LONGITUDE = ''";   	//
                $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;//
                mysqli_query($conn,$SQLSTR);                  	//
        }                                                       	//
        //**************************************************************//
//Update ST
	$changed = change_made("ST",trim(remove_code($_POST["txtst"])));
	$SQLSTR="Update SITEDATA Set ST = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtst"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

        //**********************************************//
        //***** Clear Latitude and Longitude values ****//
        //**************************************************************//
        if ($changed == 1){                                     	//
                $SQLSTR="Update SITEDATA Set LATITUDE = ''";    	//
                $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;//
                mysqli_query($conn,$SQLSTR);                  	//
                $SQLSTR="Update SITEDATA Set LONGITUDE = ''";   	//
                $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;//
                mysqli_query($conn,$SQLSTR);                  	//
        }                                                       	//
        //**************************************************************//
//Update ZIP
	$changed = change_made("ZIP",trim(remove_code($_POST["txtzip"])));
  	$SQLSTR="Update SITEDATA Set ZIP = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtzip"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

        //**********************************************//
        //***** Clear Latitude and Longitude values ****//
        //**************************************************************//
        if ($changed == 1){                                     	//
                $SQLSTR="Update SITEDATA Set LATITUDE = ''";    	//
                $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;//
                mysqli_query($conn,$SQLSTR);                  	//
                $SQLSTR="Update SITEDATA Set LONGITUDE = ''";   	//
                $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;//
                mysqli_query($conn,$SQLSTR);                  	//
        }                                                       	//
        //**************************************************************//
//Update NOTES_1
	$sql="SELECT NOTES_1 FROM SITEDATA WHERE SITE_ID = '".$STRSTORENUM."'";    
	$r=mysqli_query($conn,$sql);   
	$rw = mysqli_fetch_assoc($r);    
	if (trim(remove_code($_POST["txtnotes"])) != $rw['NOTES_1']){
		$err_msg=" ".$STRSTORENUM." - Public Note changed By ".$_SESSION['user'];      
		error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);      
	}     
//	$txtnotes=remove_code($_POST["txtnotes"]);
	//$txtnotes=trim(remove_code($_POST["txtnotes"]));
	$txtnotes=trim(htmlentities($_POST["txtnotes"],ENT_QUOTES));
  	$SQLSTR="Update SITEDATA Set NOTES_1 = ";
	$SQLSTR .= $APTR.$txtnotes.$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update NOTES_2
        $sql="SELECT NOTES_2 FROM SITEDATA WHERE SITE_ID = '".$STRSTORENUM."'";
        $r=mysqli_query($conn,$sql);
        $rw = mysqli_fetch_assoc($r);
        if (trim(remove_code($_POST["txtnotes2"])) != $rw['NOTES_2']){
                $err_msg=" ".$STRSTORENUM." - Admin Note changed By ".$_SESSION['user'];
                error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
        }

  	$SQLSTR="Update SITEDATA Set NOTES_2 = ";
//  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtnotes2"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtnotes2"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Store Phone Number
	change_made("SITE_PHONE_NUMBER",trim(remove_code($_POST["txtstorephonenumber"])));
 	$SQLSTR="Update SITEDATA Set SITE_PHONE_NUMBER = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtstorephonenumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Store Fax Number
	change_made("SITE_FAX_NUMBER",trim(remove_code($_POST["txtstorefaxnumber"])));
  	$SQLSTR="Update SITEDATA Set SITE_FAX_NUMBER = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtstorefaxnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Store Manager Contact
	change_made("SITE_CONTACT",trim(remove_code($_POST["txtstoremanager"])));
  	$SQLSTR="Update SITEDATA Set SITE_CONTACT = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtstoremanager"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Store Manager Number
	change_made("SITE_CONTACT_PHONE",trim(remove_code($_POST["txtstoremanagernumber"])));
  	$SQLSTR="Update SITEDATA Set SITE_CONTACT_PHONE = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtstoremanagernumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update STore Hours
	change_made("SITE_HOURS",trim(remove_code($_POST["txtstorehours"])));
  	$SQLSTR="Update SITEDATA Set SITE_HOURS = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtstorehours"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Time Zone
	change_made("TIME_ZONE",trim(remove_code($_POST["txttimezone"])));
  	$SQLSTR="Update SITEDATA Set TIME_ZONE = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txttimezone"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Order Date
  	if (trim($_POST["txtvpnorderdate"])!="")
  	{
		$t=strftime("%Y-%m-%d",strtotime(trim(remove_code($_POST["txtvpnorderdate"]))));
		change_made("ORDER_DATE",$t);
    		$SQLSTR="Update SITEDATA Set ORDER_DATE = ";
    		//$SQLSTR .= $APTR.strftime("%Y-%m-%d %H:%M:%S",strtotime(trim(remove_code($_POST["txtvpnorderdate"])))).$APTR;
		$SQLSTR .= $APTR.$t.$APTR;
		$SQLSTR .=  " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
    		mysqli_query($conn,$SQLSTR);    //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
		//**DEBUG//echo $SQLSTR."<br>";

  	}
    	else
  	{
		change_made("ORDER_DATE","");
    		$SQLSTR="Update SITEDATA Set ORDER_DATE = ";
    		$SQLSTR .= "Null"." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
    		mysqli_query($conn,$SQLSTR);    //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
		//**DEBUG//echo $SQLSTR."<br>";

  	} 

//Update Service Type
	change_made("SERVICE_TYPE",trim(remove_code($_POST["txtservicetype"])));
	$SQLSTR="Update SITEDATA Set SERVICE_TYPE = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtservicetype"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Order Flag
	change_made("ORDER_FLAG",trim(remove_code($_POST["txtorderflag"])));
	$SQLSTR="Update SITEDATA Set ORDER_FLAG = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtorderflag"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update ORDER_BY
	change_made("ORDER_BY",trim(remove_code($_POST["txtorderby"])));
	$SQLSTR="Update SITEDATA Set ORDER_BY = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtorderby"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Group Contact Name
	change_made("GROUP_CONTACT",trim(remove_code($_POST["txtgroupcontact"])));
	$SQLSTR="Update SITEDATA Set GROUP_CONTACT = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtgroupcontact"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Group Contact Number
	change_made("GROUP_CONTACT_PHONE",trim(remove_code($_POST["txtgroupnumber"])));
	$SQLSTR="Update SITEDATA Set GROUP_CONTACT_PHONE = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtgroupnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Group Contact E-Mail
	change_made("GROUP_CONTACT_EMAIL",trim(remove_code($_POST["txtgroupemail"])));
	$SQLSTR="Update SITEDATA Set GROUP_CONTACT_EMAIL = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtgroupemail"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Service Code
	change_made("SERVICE_CODE",trim(remove_code($_POST["txtservicecode"])));
	$SQLSTR="Update SITEDATA Set SERVICE_CODE = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtservicecode"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update DialUp Number
	change_made("DIAL_UP_NUMBER",trim(remove_code($_POST["txtdialupnumber"])));
	$SQLSTR="Update SITEDATA Set DIAL_UP_NUMBER = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtdialupnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Asset Number
	change_made("ROUTER_ASSET_NUM",trim(remove_code($_POST["txtrouterassetnumber"])));
	$SQLSTR="Update SITEDATA Set ROUTER_ASSET_NUM = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtrouterassetnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Router Serial Number
	change_made("ROUTER_SERIAL_NUM",trim(remove_code($_POST["txtrouterserialnumber"])));
	$SQLSTR="Update SITEDATA Set ROUTER_SERIAL_NUM = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtrouterserialnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Telco Service Provider
	change_made("TELCO_PROVIDER",trim(remove_code($_POST["txttelcoserviceprovider"])));
	$SQLSTR="Update SITEDATA Set TELCO_PROVIDER = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txttelcoserviceprovider"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Telco Support Number
	change_made("TELCO_SUPPORT",trim(remove_code($_POST["txttelcosupportnumber"])));
	$SQLSTR="Update SITEDATA Set TELCO_SUPPORT = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txttelcosupportnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update T-1 Circuitt Number
	change_made("T1_CIRCUIT",trim(remove_code($_POST["txtt1circuit"])));
	$SQLSTR="Update SITEDATA Set T1_CIRCUIT = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtt1circuit"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update LEC Circuit number
	change_made("LEC_CIRCUIT",trim(remove_code($_POST["txtleccircuit"])));
	$SQLSTR="Update SITEDATA Set LEC_CIRCUIT = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtleccircuit"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update DSL Phone Number
	change_made("DSL_LINE_NUMBER",trim(remove_code($_POST["txtdslnumber"])));
	$SQLSTR="Update SITEDATA Set DSL_LINE_NUMBER = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtdslnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update DSL Circuit Number
	change_made("DSL_CIRCUIT_NUMBER",trim(remove_code($_POST["txtdslcircuit"])));
	$SQLSTR="Update SITEDATA Set DSL_CIRCUIT_NUMBER = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtdslcircuit"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Broadband/ISP Provider
	change_made("INET_PROVIDER",trim(remove_code($_POST["txtbroadbandprovider"])));
	$SQLSTR="Update SITEDATA Set INET_PROVIDER = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtbroadbandprovider"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Broadband/ISP Support Number
	change_made("INET_PROVIDER_SUPPORT_NUMBER",trim(remove_code($_POST["txtbroadbandnumber"])));
	$SQLSTR="Update SITEDATA Set INET_PROVIDER_SUPPORT_NUMBER = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtbroadbandnumber"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Broadband URL
	change_made("INET_PROVIDER_WEB",trim(remove_code($_POST["txtbroadbandurl"])));
	$SQLSTR="Update SITEDATA Set INET_PROVIDER_WEB = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtbroadbandurl"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update DSL Authentication Type
	change_made("WAN_AUTHENTICATION_TYPE",trim(remove_code($_POST["txtwantype"])));
	$SQLSTR="Update SITEDATA Set WAN_AUTHENTICATION_TYPE = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtwantype"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Public IP address
	change_made("WAN_IP",trim(remove_code($_POST["txtpublicipaddress"])));
	$SQLSTR="Update SITEDATA Set WAN_IP = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtpublicipaddress"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Default Gateway
	change_made("WAN_GATEWAY",trim(remove_code($_POST["txtdefaultgateway"])));
	$SQLSTR="Update SITEDATA Set WAN_GATEWAY = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtdefaultgateway"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update WAN_NETMASK
	change_made("WAN_NETMASK",trim(remove_code($_POST["txtnetmask"])));
	$SQLSTR="Update SITEDATA Set WAN_NETMASK = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtnetmask"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update DSL UserName
	change_made("DSL_USERNAME",trim(remove_code($_POST["txtdslusername"])));
	$SQLSTR="Update SITEDATA Set DSL_USERNAME = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtdslusername"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update DSL Password
	//**********************************************************************//
	//      Did not want the Password in the logs... so log it seperate	//
	//**************************************************************************************//
	$sql="SELECT DSL_PASSWORD FROM SITEDATA WHERE SITE_ID = '".$STRSTORENUM."'";		//
	$r=mysqli_query($conn,$sql);									//
	$rw = mysqli_fetch_assoc($r);								//
	if (trim(remove_code($_POST["txtdslpassword"])) != $rw['DSL_PASSWORD']){		//
		$err_msg=" ".$STRSTORENUM." - DSL Password Changed By ".$_SESSION['user'];	//
		error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);			//
	}											//
	//**************************************************************************************//

	$SQLSTR="Update SITEDATA Set DSL_PASSWORD = ";
	$SQLSTR .= $APTR.trim(remove_code($_POST["txtdslpassword"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Last Change By
	$SQLSTR="Update SITEDATA Set LAST_CHANGE_BY = ";
	$SQLSTR .= $APTR.trim($_SESSION["user"]).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Date Last Change
	$SQLSTR="Update SITEDATA Set LAST_CHANGE_DATE = ";
	$SQLSTR .= $APTR.strftime("%Y-%m-%d %H:%M:%S",time()).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update Edit flag
        $SQLSTR ="Update SITEDATA Set EDIT_FLAG  = '' ";
        $SQLSTR .= " WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
        mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
        //**DEBUG//echo $SQLSTR."<br>";

//Update REGION
	change_made("REGION",trim(remove_code($_POST["txtdc"])));
  	$SQLSTR="Update SITEDATA Set REGION = ";
  	$SQLSTR .= $APTR.trim(remove_code($_POST["txtdc"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
  	mysqli_query($conn,$SQLSTR);  //$AREGIONMDTEXT+$ADEXECUTENORECORDS;
	//**DEBUG//echo $SQLSTR."<br>";

//Update total Alerts
        //$SQLSTR="Update SITEDATA Set TOTAL_ALERTS_SENT = ";
        //$SQLSTR .= $APTR.trim(remove_code($_POST["txtalerts"])).$APTR." WHERE SITE_ID = ".$APTR.$STRSTORENUM.$APTR;
        //mysqli_query($conn,$SQLSTR);
	//**DEBUG//echo $SQLSTR."<br>";

	//###############################
	//      Log who made changes	#
	//###############################
//	$err_msg=" ".$STRSTORENUM." Changed by ".$_SESSION['user'];
//	error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);

//###############################
//	Delete the record	#
//###############################

}
else
{
	// delete all site data from table
  	$SQLSTR="Delete From SITEDATA WHERE SITE_ID = \"". $STRSTORENUM . "\"";
  	mysqli_query($conn,$SQLSTR); 
	//**DEBUG//echo $SQLSTR."<br>";
	// Delete all VIP Emails
        // delete all Monitor info from table
        $SQLSTR="Delete From MONITORINFO WHERE SITE_ID = \"". $STRSTORENUM . "\"";
        mysqli_query($conn,$SQLSTR);
        //**DEBUG//echo $SQLSTR."<br>";
        // Delete Monitor Info 
        $SQLSTR="Delete From ALERTEMAILS WHERE LOCATION = \"" . $STRSTORENUM . "\"";
        mysqli_query($conn,$SQLSTR);
	//**DEBUG//echo $SQLSTR."<br>";

	// delete any logs in monitor logs table
        $SQLSTR="Delete From MONLOGS WHERE SITE_ID = \"" . $STRSTORENUM . "\"";
        mysqli_query($conn,$SQLSTR);
	//**DEBUG//echo $SQLSTR."<br>";

	// Delete the RRD file
	//**************************************************************//
	// cleanup valid site names to valid filenames                  //
	// NETz allows names that may not be legal as file names        //
	//**************************************************************//
	$allowed = '/[^a-z0-9\\.\\-\\_\\\\]/i';                         //
	$rrdfilename=preg_replace($allowed,"",$STRSTORENUM);		//
	$rrdfilename= $basedir.'rrd/'.$rrdfilename.'.rrd';              //
	exec("rm -f ".$rrdfilename);
	//**************************************************************//

	//##########################################
	//      Log that record was deleteed       #
	//##########################################
	$err_msg=" ".$STRSTORENUM." - Deleted By ".$_SESSION['user'];
	error_log(date('Y-m-d G:i:s').$err_msg."\n", 3, $logfile);
} 

mysqli_close($conn);
//#######################################
// IS THIS STILL USED ???????????????	#
//#######################################
if ($CALLINGPAGE=="new")
{
	header("Location:".$_SESSION['secure'] . $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']). "/" . "ops_all_new.php?SITE_ID=".$STRSTORENUM);
	ob_end_flush();
}
  else
{
//	header("Location:".$_SESSION['secure']. $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']). "/" . "ops.php?site=".$STRSTORENUM);
	header("Location: ops.php?site=".$STRSTORENUM);
	ob_end_flush();
}
?>
