<?php

if(!function_exists('get_unit_att')){
	
	function get_unit_att($unit = null)
	{
		$tableName = "";

        if($unit== 1 || $unit == 4 || $unit ==5 || $unit ==9){
            $tableName= "hr_attendance_mbm";
        }
        else if($unit==2){
            $tableName= "hr_attendance_ceil";
        }
        else if($unit==3){
            $tableName= "hr_attendance_aql";
        }
        else if($unit==8){
            $tableName= "hr_attendance_cew";
        }

        return $tableName;
	}
}
