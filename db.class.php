<?php
//include_once 'auth.php';
include_once('lmz-functions.php');
include_once('site-monitor.conf.php');
class DB_Class
{

        function get_field_list($field_name)
        {
                $this->query="select DISTINCT ".$field_name." from SITEDATA ORDER BY ".$field_name.";";
		$rows=run_netz_query($this->query);
		$i =0;
		foreach($rows as $row){
			$this->field_list[$i] = $row[$field_name];
			$i++;
	
		}

                return $this->field_list;
        }
	        function get_support_centers(){
                $this->query="select DISTINCT LOCATION from ALERTEMAILS where TYPE = 'support' ORDER BY LOCATION;";
		$rows=run_netz_query($this->query);
                $i =0;
		foreach($rows as $row){
                        $this->support_list[$i] = $row['LOCATION'];
                        $i++;

                }

                return $this->support_list;
        }
}
?>
