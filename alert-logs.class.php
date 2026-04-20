<?php
include_once 'auth.php';
include_once('lmz-functions.php');
include_once('site-monitor.conf.php');
class SiteLog
{
        private string $query;
        private $result;
        private int $count;


        function get_count($site,$daysback)
        {
                $conn = mysqli_connect(NETZ_DB_SERVER,NETZ_DB_USERNAME,NETZ_DB_PASSWORD,NETZ_DATABASE);
                //@mysql_select_db(NETZ_DATABASE) or die( "Unable to select database");
                //             hour
                //               |  minute
                //               |    |
                //               |    |  second
                //               |    |    | month---|     day--|days back-|    year-|
                $back  = mktime("0", "0", "0", date("m"), date("d")-$daysback, date("Y"));
                $back  = date("Y-m-d G:i:s",$back);
//echo $back;
                $this->query="SELECT * FROM ALERTLOGS WHERE ";
                $this->query .= "SITE_ID = '".$site."' AND CHECK_DATE_TIME >= '".$back."' ORDER BY CHECK_DATE_TIME DESC";
                $this->result=mysqli_query($conn,$this->query);
                $this->count = mysqli_num_rows($this->result);
                mysqli_close($conn);
                return $this->count;
        }
}
?>
