<?php

namespace App\Http\Controllers\Hr\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Floor;
use App\Models\Hr\Benefits;
use App\Models\Hr\Location;
use DB, PDF, auth;

class GroupAttendanceController extends Controller
{ 
    public function showForm(Request $request)
    {
        

        $unitList  = Unit::where('hr_unit_status', '1')->whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id'); 
        $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id'); 
        
        $g_date       = $request->g_date; 
        $g_prev_date  = date('Y-m-d', strtotime('-1 day', strtotime($g_date))); 
        $salaryMin    = Benefits::getSalaryRangeMin();
        $salaryMax    = Benefits::getSalaryRangeMax();
        $locationlist = Location::getLocationDistinct();
        $countLocation= count($locationlist);
        $tableName="";

        $all_empAttendance = array();
        #--------------------------------------------------
        if($request->g_date){         
            $employees= DB::table('hr_benefits as ben')
                            ->select(                     
                                                      
                                "b.as_id",                               
                                "b.associate_id AS associate",
                                "b.as_name",
                                "b.as_area_id",
                                "b.as_unit_id",                              
                                "ben.ben_current_salary AS salary",                             
                                "ar.hr_area_name",
                                "d.hr_designation_name"
                            )                                                  
                            ->whereBetween('ben.ben_current_salary',array($request->salary_from,$request->salary_to))                          
                            ->leftjoin("hr_as_basic_info as b", "b.associate_id", "=", "ben.ben_as_id")
                            ->leftjoin("hr_area as ar", "ar.hr_area_id", "=", "b.as_area_id")                          
                            ->leftjoin("hr_designation as d", "d.hr_designation_id", "=", "b.as_designation_id")        
                            ->orderBy("b.as_area_id")
                            ->get(); 
                          //dd($employees);
            $empAttendance = [];
            $empAttendance_out = [];
            $all_empAttendance = [];
            foreach($employees AS $employe){
                // Attendance table define------
                 $unit = $employe->as_unit_id;  

                //CEIL
                if($unit == 2){
                   $tableName= "hr_attendance_ceil AS a";
                }
                //AQl
                else if($unit == 3){
                    $tableName= "hr_attendance_aql AS a";
                }
                else if($unit == 1 || $unit == 4 || $unit == 5 || $unit == 9){
                    $tableName= "hr_attendance_mbm AS a";
                }
                //HO
                else if($unit == 6){
                    $tableName= "hr_attendance_ho AS a";
                }
                else if($unit == 8){
                    $tableName= "hr_attendance_cew AS a";
                }
                else{
                    $tableName= "hr_attendance_mbm AS a";
                } 

                // Query for attendance in time
                $attendance = DB::table($tableName)
                         ->select(                           
                                "a.in_time",
                                // "a.out_time",
                                "a.in_unit",
                                "a.remarks",
                                "a.as_id",
                                "loc.hr_location_id"
                            )
                         
                            ->whereDate("a.in_time", $g_date)                      
                            ->where( "a.as_id", $employe->as_id)
                            ->leftjoin("hr_location as loc", "loc.hr_location_unit_id", "=","a.in_unit")                               
                            ->orderBy('a.in_time','asc')->first();

                // $attendance->orderBy('a.in_time','desc');

                // Query for attendance  out time              
                $attendance_out= DB::table($tableName)
                     ->select(                       
                            "a.as_id AS out_as_id",
                            "a.out_time AS outtime",
                            "a.in_unit AS out_unit",
                            "loc.hr_location_id"
                        )                             
                       
                        ->whereDate("a.out_time", $g_prev_date)
                        ->where( "a.as_id", $employe->as_id)
                        ->leftjoin("hr_location as loc", "loc.hr_location_unit_id", "=","a.in_unit")                               
                        ->first();

                
                // For  In time----------------------------------------------------------------                
                if(!empty($attendance)) {
                    
                    // $empAttendance[] = $attendance;

                    if($attendance->in_time!=""){   
                       $empAttendance[$employe->associate]['in_time'] = $attendance->in_time;
                     
                    }                

                    $empAttendance[$employe->associate]['area'] = $employe->hr_area_name;
                    $empAttendance[$employe->associate]['name'] = $employe->as_name;
                    $empAttendance[$employe->associate]['associate'] = $employe->associate;
                    $empAttendance[$employe->associate]['designation'] = $employe->hr_designation_name;      

                    $empAttendance[$employe->associate]['in_unit'] = $attendance->in_unit;
                    $empAttendance[$employe->associate]['remarks'] = $attendance->remarks;
                    $empAttendance[$employe->associate]['location_id'] = $attendance->hr_location_id;
                    $empAttendance[$employe->associate]['requested_place'] =''; 
                    $empAttendance[$employe->associate]['leave'] =''; 
                  

                        
                }
                else{
                //If not find in time then check in hr_outside table
                    $outside= DB::table("hr_outside AS o")
                        ->select(                       
                            "o.as_id AS out_as_id",
                            "o.requested_place"                                   
                        )
                        // ->whereRaw('('$g_date.' between start_date and end_date)')
                        ->where('start_date', '<=', $g_date)
                        ->where('end_date', '>=', $g_date)                  
                        ->where( "o.as_id", $employe->associate)                                                                  
                        ->first(); 



                    if(!empty($outside)) {  
                        $empAttendance[$employe->associate]['in_time'] = '';                       
                        $empAttendance[$employe->associate]['in_unit'] = '';
                        $empAttendance[$employe->associate]['requested_place'] =$outside->requested_place; 

                        $empAttendance[$employe->associate]['area'] = $employe->hr_area_name;
                        $empAttendance[$employe->associate]['name'] = $employe->as_name;
                        $empAttendance[$employe->associate]['associate'] = $employe->associate;
                        $empAttendance[$employe->associate]['designation'] = $employe->hr_designation_name;
                        $empAttendance[$employe->associate]['location_id'] = '';   
                        $empAttendance[$employe->associate]['remarks']='';
                        $empAttendance[$employe->associate]['leave'] =''; 


                    }
                    else{ 
                        // Check in leave table If not find in time  in hr_outside table
                         $leave= DB::table("hr_leave AS l")
                        ->select(                       
                            "l.leave_type"                                 
                        )                     
                        ->where('leave_from', '<=', $g_date)
                        ->where('leave_to', '>=', $g_date)                  
                        ->where( "l.leave_ass_id", $employe->associate)                                                                  
                        ->first();
                        if(!empty($leave)) { 

                            $empAttendance[$employe->associate]['leave'] =$leave->leave_type; 
                            $empAttendance[$employe->associate]['in_time'] = '';
                            $empAttendance[$employe->associate]['in_unit'] = '';
                            $empAttendance[$employe->associate]['requested_place'] =''; 

                            $empAttendance[$employe->associate]['area'] = $employe->hr_area_name;
                            $empAttendance[$employe->associate]['name'] = $employe->as_name;
                            $empAttendance[$employe->associate]['associate'] = $employe->associate;
                            $empAttendance[$employe->associate]['designation'] = $employe->hr_designation_name;
                            $empAttendance[$employe->associate]['location_id'] = '';   
                            $empAttendance[$employe->associate]['remarks']='';

                        }
                         
                    }
                }// End Intime Calculation

                
                // For  Out time----------------------------------------------------------------                
                if(!empty($attendance_out)) {
                    
                    // $empAttendance[] = $attendance;
                      $empAttendance_out[$employe->associate]['outtime'] = $attendance_out->outtime;
                      $empAttendance_out[$employe->associate]['location_id_out'] = $attendance_out->hr_location_id;
                      $empAttendance_out[$employe->associate]['requested_place_out'] =''; 
                      $empAttendance_out[$employe->associate]['leave_out'] =''; 
                }
                else{
                //If not find in time then check in hr_outside table
                    $outside_out= DB::table("hr_outside AS o")
                        ->select(                       
                            "o.as_id AS out_as_id",
                            "o.requested_place"                                   
                        )
                       
                        ->where('start_date', '<=', $g_prev_date)
                        ->where('end_date', '>=', $g_prev_date)                  
                        ->where( "o.as_id", $employe->associate)                                                                  
                        ->first();


                    if(!empty($outside_out)) {   // Check in outside table
                    
                        $empAttendance_out[$employe->associate]['outtime'] = '';   
                        $empAttendance_out[$employe->associate]['location_id_out'] = '';                     
                        $empAttendance_out[$employe->associate]['requested_place_out'] =$outside_out->requested_place;
                        $empAttendance_out[$employe->associate]['leave_out'] =''; 
                        $empAttendance_out[$employe->associate]['in_unit_out'] = '';



                    }
                    else{ 
                        // Check in leave table
                         $leave_out= DB::table("hr_leave AS l")
                        ->select(                       
                            "l.leave_type"                                 
                        )                     
                        ->where('leave_from', '<=', $g_prev_date)
                        ->where('leave_to', '>=', $g_prev_date)                  
                        ->where( "l.leave_ass_id", $employe->associate)                                                                  
                        ->first();

                        if(!empty($leave_out)) {

                            $empAttendance_out[$employe->associate]['leave_out'] =$leave_out->leave_type;                   
                            $empAttendance_out[$employe->associate]['outtime'] = '';
                            $empAttendance_out[$employe->associate]['location_id_out'] = '';
                            $empAttendance_out[$employe->associate]['in_unit_out'] = '';
                            $empAttendance_out[$employe->associate]['requested_place_out'] =''; 

                        }
                        // else{
                        //     $empAttendance_out[$employe->associate]['leave_out'] ='';
                        //     $empAttendance_out[$employe->associate]['location_id_out'] = '';                   
                        //     $empAttendance_out[$employe->associate]['outtime'] = '';
                        //     $empAttendance_out[$employe->associate]['in_unit_out'] = '';
                        //     $empAttendance_out[$employe->associate]['requested_place_out'] ='';
                        // }
                         
                    }
                }// End out time Calculation  
            }

            // Merge two array
            $all_empAttendance=array_merge_recursive($empAttendance,$empAttendance_out);

        }                    

  // return ($all_empAttendance);
          
  
  // return dd($empAttendance);
    return view("hr/reports/group_attendance", compact(
            "g_date", 
            "unitList", 
            "areaList",
            "salaryMin",
            "salaryMax",
            "locationlist",
            "countLocation",
            "all_empAttendance"
        ));
  }


}