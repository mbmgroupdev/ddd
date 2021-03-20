<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessUnitWiseSalary;
use Carbon\Carbon;
use DB;

class TestXYZController extends Controller
{
    public function rfidUpdate()
    {
    	return $this->incrementHistory();
        return "";
    	$data = array();
    	$getBasic = DB::table('hr_as_basic_info')
    	->select('as_id', 'as_rfid_code', 'as_oracle_code', 'as_unit_id')
    	->whereIn('as_unit_id', [3,8])
    // 	->where('as_rfid_code', 'LIKE', '#%')
    	->whereRaw('LENGTH(as_rfid_code) < 10')
    	->get();
    // 	->pluck('as_oracle_code');
    	return ($getBasic);
    	foreach ($getBasic as $emp) {
    	    $rfid = ltrim($emp->as_rfid_code,'#');
    // 		$rfid = str_pad($emp->as_rfid_code, 10, "0", STR_PAD_LEFT); 
	   //     if($rfid == '0000000000'){
	   //     	$rfid = null;
	   //     }
	        $check = DB::table('hr_as_basic_info')->where('as_rfid_code', $rfid)->first();
	        if($check == null){
	            $data[$emp->as_id] = DB::table('hr_as_basic_info')
    	        ->where('as_id', $emp->as_id)
    	        ->update([
    	        	'as_rfid_code' => $rfid
    	        ]);
	        }
    	}
    	
    	return $data;
    }

    public function shiftUpdate()
    {
    	$data[] = DB::table('hr_as_basic_info')
    	->where('as_unit_id', 8)
    	->whereIn('as_oracle_code', [])
    	->update([
    		'as_shift_id' => 'Day'
    	]);

    	return $data;
    }
    public function monthlyCheck(){

        $leave_array = [];
        $absent_array = [];
        for($i=1; $i<=28; $i++) {
	        $date = date('Y-m-d', strtotime('2021-02-'.$i));
	        $leave = DB::table('hr_attendance_ceil AS a')
	                ->where('a.in_time', 'like', $date.'%')
	                ->leftJoin('hr_as_basic_info AS b', function($q){
	                    $q->on('b.as_id', 'a.as_id');
	                })
	                ->pluck('b.associate_id');
	        $leave_array[] = $leave;
	        $getholiday = DB::table('holiday_roaster AS a')
	        		->select('a.id','b.as_id', 'a.date', 'a.month', 'a.year')
	        		->leftJoin('hr_as_basic_info AS b', function($q){
	                    $q->on('b.associate_id', 'a.as_id');
	                })
		            ->whereIn('a.as_id', $leave)
		            ->whereDate('a.date', $date)
	                ->get();
	        if(count($getholiday) > 0){
	        	$absent_array[] = $getholiday->toArray();
	        	foreach ($getholiday as $value) {
	        		DB::table('holiday_roaster')->where('id', $value->id)->delete();
	        		$queue = (new ProcessUnitWiseSalary('hr_attendance_ceil', '02', 2021, $value->as_id, 28))
                        ->onQueue('salarygenerate')
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
	        	}
	        }

        }

        return $absent_array;
        
        $leave_array = [];
        $absent_array = [];
        for($i=1; $i<=31; $i++) {
            $date = date('Y-m-d', strtotime('2021-02-'.$i));
            $leave = DB::table('hr_leave AS l')
                    ->where('l.leave_from', '<=', $date)
                    ->where('l.leave_to',   '>=', $date)
                    ->where('l.leave_status', '=', 1)
                    ->leftJoin('hr_as_basic_info AS b', function($q){
                        $q->on('b.associate_id', 'l.leave_ass_id');
                    })
                    ->pluck('b.associate_id','b.as_id');
            $leave_array[] = $leave;
            $absent_array[] = DB::table('hr_absent')
                    ->whereDate('date', $date)
                    ->whereIn('associate_id', $leave)
                    ->get()->toArray();
        }
        // return $absent_array;
        return ($absent_array);
        exit;
    }
    public function otHourCheck()
    {
    	$getBasic = DB::table('hr_as_basic_info')
    	->where('as_ot', 1)
    	->whereIn('as_unit_id', [2])
    	->where('as_status', 1)
    	->pluck('as_id');
    	$getat = [];
    	for($i=1; $i<=28; $i++) {
	    	$getData = DB::table('hr_attendance_ceil AS m')
	    	->select('m.*', 'b.hr_shift_end_time', 'b.hr_shift_break_time')
	    	->where('m.in_date', '2021-02-'.$i)
	    	->whereIn('m.as_id', $getBasic)
	    	->leftJoin('hr_shift AS b', function($q){
	            $q->on('b.hr_shift_code', 'm.hr_shift_code');
	        })
	        ->whereNotNull('m.out_time')
	        ->whereNotNull('m.in_time')
	        ->get();
	        // dd($getData);
	        
	        foreach ($getData as $data) {
	        	$punchOut = $data->out_time;
	        	$shiftOuttime = date('Y-m-d', strtotime($punchOut)).' '.$data->hr_shift_end_time;
	        	$otDiff = ((strtotime($punchOut) - (strtotime($shiftOuttime) + (($data->hr_shift_break_time + 10) * 60))))/3600;
	        	if($otDiff > 0 && $data->ot_hour <= 0){
	        		$getat[$data->as_id] = $data;
	        	}
	        }
	    }
        return ($getat);
        
    }
    public function earlyarPunchCheck()
    {
        $getBasic = DB::table('hr_as_basic_info')
        ->where('as_ot', 1)
        ->whereIn('as_unit_id', [2])
        ->where('as_status', 1)
        ->pluck('as_id');
        $getat = [];
        for($i=1; $i<=28; $i++) {
            $getData = DB::table('hr_attendance_ceil AS m')
            ->select('m.*', 'b.hr_shift_start_time', 'b.hr_shift_break_time')
            ->where('m.in_date', '2021-02-'.$i)
            ->whereIn('m.as_id', $getBasic)
            ->leftJoin('hr_shift AS b', function($q){
                $q->on('b.hr_shift_code', 'm.hr_shift_code');
            })
            // ->whereNotNull('m.out_time')
            // ->whereNotNull('m.in_time')
            ->get();
            // dd($getData);
            
            foreach ($getData as $data) {
                $punchIn = $data->in_time;
                $shiftIntime = date('Y-m-d', strtotime($punchIn)).' '.$data->hr_shift_start_time;
                $earlyTime = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($shiftIntime)));
                
                if(strtotime($punchIn) < strtotime($earlyTime)){
                    $getat[$data->as_id.' - '.$data->in_date] = $data;
                }
            }
        }
        return ($getat);
        
    }
    public function monthlyLeftCheck()
    {
    	$data = DB::table('hr_monthly_salary')
    	->where('month', '01')
    	->where('year', '2021')
    	->where('emp_status', 2)
    	->get();

    	$current = DB::table('hr_monthly_salary')
    	->select('as_id')
    	->where('month', '02')
    	->where('year', '2021')
    	->where('emp_status', 1)
    	->get()
    	->keyBy('as_id')
    	->toArray();

    	$ge = array();
    	foreach ($data as $value) {
    		if(isset($current[$value->as_id])){
    			$ge[] = $value->as_id;
    		}
    	}
    	return ($ge);
    }
    
    public function tiffinBillCheck()
    {
        $date = '2021-02-';
        $data = [];
        for ($i=1; $i <= 31; $i++) { 
            $getBill = DB::table('hr_bill')
            ->where('bill_date', date('Y-m-d', strtotime($date.$i)))
            ->get()
            ->toArray();
            // $getatt = DB::table('hr_attendance_mbm')
            // ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"))
            // ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            // ->get()
            // ->keyBy('asdate')
            // ->toArray();
            $getatt = DB::table('hr_attendance_mbm')
            ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"), 'in_date', 'in_time', 'out_time')
            ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            ->where('hr_shift_code', 'HH3')
            ->get()
            ->keyBy('asdate')
            ->toArray();
            
            foreach ($getBill as $value) {
                if(isset($getatt[$value->bill_date.$value->as_id])){
                    // $data[] = $value;
                    $data[] = DB::table('hr_bill')->where('id', $value->id)->delete();
                }
            }
        }
        return ($data);
    }
    public function billRemove()
    {
        $getBill = DB::table("hr_bill AS t")
        ->select('t.*', 'b.as_designation_id', 'b.as_location', 'b.as_subsection_id', 'b.as_department_id')
        ->leftJoin('hr_as_basic_info AS b', function($q){
            $q->on('b.as_id', 't.as_id');
        })
        // ->whereIn('b.as_location', [12,13])
        // ->whereIn('b.as_subsection_id', [185,108])
        // ->whereIn('b.as_designation_id', [408,397,218,229,204,211,356,230,470,407,221,293,375,449,196,454,402,463])
        ->whereIn('b.as_department_id', [53,56])
        ->get();
        return $getBill;

    }
    public function employeeCheck()
    {
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->select('b.as_oracle_code', 'b.associate_id', 'ben.hr_bn_associate_name', 'b.as_status', 'b.as_name')
        ->leftJoin('hr_employee_bengali AS ben', 'b.associate_id', 'ben.hr_bn_associate_id')
        ->where('b.as_unit_id', 2)
        ->whereNull('ben.hr_bn_associate_name')
        ->whereIn('b.as_status', [1,6,2,5])
        ->get();
        dd($getEmployee);
    }
    
    public function incrementHistory()
    {
        $getData = array(
            0 => array('PID' => '15K1096E', 'NAME' => 'AFROJA AKTER', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10850'),
            1 => array('PID' => '98K4518J', 'NAME' => 'MAHMUDUL HAQUE', 'DESIG' => 'OFFICER-CUTTING', 'L_INCR_DT' => '2019-10-01', 'L_INCR_AMT' => '950', 'CURRENT_SALARY' => '19950'),
            2 => array('PID' => '17G4142H', 'NAME' => 'ARUN KUMAR SINGHA', 'DESIG' => 'JUNIOR OFFICER Q.C.', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '4153', 'CURRENT_SALARY' => '14000'),
            3 => array('PID' => '17H0426C', 'NAME' => 'TAOHEDUL ISLAM', 'DESIG' => 'OFFICER MARKER MAN', 'L_INCR_DT' => '2020-01-01', 'L_INCR_AMT' => '2500', 'CURRENT_SALARY' => '22500'),
            4 => array('PID' => '15K6287G', 'NAME' => 'SAIFUL', 'DESIG' => 'SUPERVISOR', 'L_INCR_DT' => '2019-10-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '18500'),
            5 => array('PID' => '18D4371H', 'NAME' => 'MEHEDI', 'DESIG' => 'GPQ', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '7000', 'CURRENT_SALARY' => '17000'),
            6 => array('PID' => '17J0728D', 'NAME' => 'MOHAMMAD HANIF', 'DESIG' => 'MANAGER MERCHANDISING', 'L_INCR_DT' => '2019-11-01', 'L_INCR_AMT' => '11000', 'CURRENT_SALARY' => '90000'),
            7 => array('PID' => '14K2187E', 'NAME' => 'HUNOFA', 'DESIG' => 'LINE MANAGER', 'L_INCR_DT' => '2019-03-01', 'L_INCR_AMT' => '4000', 'CURRENT_SALARY' => '25000'),
            8 => array('PID' => '18A6457G', 'NAME' => 'MST. BEAUTI BEGUM', 'DESIG' => 'FOLDING MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            9 => array('PID' => '18C0695W', 'NAME' => 'MANIK HASAN', 'DESIG' => 'SR. SAMPLEMAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '12350'),
            10 => array('PID' => '16F0386C', 'NAME' => 'MD.BILLAL HOSSA', 'DESIG' => 'ASSISTANT MANAGER TECHNIC', 'L_INCR_DT' => '2019-11-01', 'L_INCR_AMT' => '5000', 'CURRENT_SALARY' => '41000'),
            11 => array('PID' => '15M6534Z', 'NAME' => 'MD.SHAMIM SHEIKH', 'DESIG' => 'COOKER', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '7200', 'CURRENT_SALARY' => '17500'),
            12 => array('PID' => '16E5492Q', 'NAME' => 'MD.SHAFIQUL ISLAM', 'DESIG' => 'SENIOR OFFICER STORE', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '10000', 'CURRENT_SALARY' => '25000'),
            13 => array('PID' => '18C3655F', 'NAME' => 'FORHAD', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '8450', 'CURRENT_SALARY' => '18000'),
            14 => array('PID' => '18C1081E', 'NAME' => 'MONIR', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10500'),
            15 => array('PID' => '18C3963F', 'NAME' => 'HAWA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9400'),
            16 => array('PID' => '18D3477F', 'NAME' => 'SUFIA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '345', 'CURRENT_SALARY' => '9095'),
            17 => array('PID' => '12J3926F', 'NAME' => 'MD. JAKIR HOSSE', 'DESIG' => 'PATERN ASSISTANT', 'L_INCR_DT' => '2019-09-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '18300'),
            18 => array('PID' => '93K0235C', 'NAME' => 'KAMRUL ISLAM', 'DESIG' => 'SR. MANAGER-Q.M.P.', 'L_INCR_DT' => '2017-10-01', 'L_INCR_AMT' => '13000', 'CURRENT_SALARY' => '112000'),
            19 => array('PID' => '18D3704F', 'NAME' => 'SHORNALI KHANOM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9350'),
            20 => array('PID' => '17L0220C', 'NAME' => 'MD. JUWEL RANA', 'DESIG' => 'ASST MANAGER QUALITY CONTROL', 'L_INCR_DT' => '2019-11-01', 'L_INCR_AMT' => '3000', 'CURRENT_SALARY' => '35000'),
            21 => array('PID' => '18C1494E', 'NAME' => 'SHAHNAZ', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10200'),
            22 => array('PID' => '18D2062E', 'NAME' => 'KAKOLI', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10200'),
            23 => array('PID' => '05L1223E', 'NAME' => 'RIPON', 'DESIG' => 'SR. SAMPLEMAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '570', 'CURRENT_SALARY' => '13820'),
            24 => array('PID' => '15C3027R', 'NAME' => 'MD. ALLAMA IKBAL', 'DESIG' => 'MERCHANDISER', 'L_INCR_DT' => '2019-03-30', 'L_INCR_AMT' => '5000', 'CURRENT_SALARY' => '33000'),
            25 => array('PID' => '18C3656F', 'NAME' => 'AYSHA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9100'),
            26 => array('PID' => '15M1831E', 'NAME' => 'RINA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '10660'),
            27 => array('PID' => '15L0382C', 'NAME' => 'JAHANGIR ALAM', 'DESIG' => 'SR. OFFICER QMS', 'L_INCR_DT' => '2019-12-01', 'L_INCR_AMT' => '4000', 'CURRENT_SALARY' => '24000'),
            28 => array('PID' => '17A4489H', 'NAME' => 'SUMAN SARKER', 'DESIG' => 'GPQ', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '3500', 'CURRENT_SALARY' => '24000'),
            29 => array('PID' => '18D0105B', 'NAME' => 'MD. RASHED KHAN', 'DESIG' => 'SR. OFFICER HR & ADMIN', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '7000', 'CURRENT_SALARY' => '27000'),
            30 => array('PID' => '12M0107B', 'NAME' => 'MD. NAZMUL HOSSAIN', 'DESIG' => 'SENIOR OFFICER COMPLIANCE', 'L_INCR_DT' => '2019-12-01', 'L_INCR_AMT' => '5000', 'CURRENT_SALARY' => '29000'),
            31 => array('PID' => '18C1508E', 'NAME' => 'TASLIMA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9700'),
            32 => array('PID' => '15E4700J', 'NAME' => 'BELLAL HOSSAIN', 'DESIG' => 'JUNIUR OFFICER CUTTING', 'L_INCR_DT' => '2019-05-01', 'L_INCR_AMT' => '3000', 'CURRENT_SALARY' => '20008'),
            33 => array('PID' => '18C1241E', 'NAME' => 'BONNA', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10600'),
            34 => array('PID' => '10F4762J', 'NAME' => 'HUMAYUN KABIR', 'DESIG' => 'ASST MANAGER CUTTING', 'L_INCR_DT' => '2019-10-01', 'L_INCR_AMT' => '4000', 'CURRENT_SALARY' => '31000'),
            35 => array('PID' => '14E1701E', 'NAME' => 'RINA', 'DESIG' => 'LINE LEADER', 'L_INCR_DT' => '2019-03-01', 'L_INCR_AMT' => '3500', 'CURRENT_SALARY' => '21500'),
            36 => array('PID' => '14H2136E', 'NAME' => 'NARGIS', 'DESIG' => 'LINE MANAGER', 'L_INCR_DT' => '2019-03-01', 'L_INCR_AMT' => '4000', 'CURRENT_SALARY' => '25000'),
            37 => array('PID' => '16K5295M', 'NAME' => 'MD.MASUD', 'DESIG' => 'OFFICER MECHANIC', 'L_INCR_DT' => '2019-10-01', 'L_INCR_AMT' => '2500', 'CURRENT_SALARY' => '26500'),
            38 => array('PID' => '18D4578J', 'NAME' => 'MOMIN MIAH', 'DESIG' => 'ORDINARY LAY-MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9420'),
            39 => array('PID' => '11D7185U', 'NAME' => 'MD. AMINUL ISLAM', 'DESIG' => 'ASST GENERAL MANAGER', 'L_INCR_DT' => '2019-04-30', 'L_INCR_AMT' => '15000', 'CURRENT_SALARY' => '135000'),
            40 => array('PID' => '16K1063E', 'NAME' => 'MST.NARGIS', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2019-10-01', 'L_INCR_AMT' => '2500', 'CURRENT_SALARY' => '17500'),
            41 => array('PID' => '18C1304E', 'NAME' => 'JAHANGIR ALOM', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10750'),
            42 => array('PID' => '13C4776Z', 'NAME' => 'YAKUB ALI', 'DESIG' => 'JUNIUR OFFICER CUTTING', 'L_INCR_DT' => '2019-03-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '16500'),
            43 => array('PID' => '18C1306E', 'NAME' => 'MOZIRON', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10130'),
            44 => array('PID' => '16M3899F', 'NAME' => 'SADIYA AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '9600'),
            45 => array('PID' => '18C1307E', 'NAME' => 'AKLIMA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '375', 'CURRENT_SALARY' => '9655'),
            46 => array('PID' => '17A4528J', 'NAME' => 'MD. RUBEL', 'DESIG' => 'JUNIUR OFFICER CUTTING', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '2500', 'CURRENT_SALARY' => '19000'),
            47 => array('PID' => '18C6185G', 'NAME' => 'MUKTA RANI', 'DESIG' => 'FOLDING MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            48 => array('PID' => '18C2560F', 'NAME' => 'LIPI', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9100'),
            49 => array('PID' => '18C3143R', 'NAME' => 'RAHUL CHANDRA DAS', 'DESIG' => 'ASSISTANT MERCHANDISER', 'L_INCR_DT' => '2019-03-02', 'L_INCR_AMT' => '5000', 'CURRENT_SALARY' => '20000'),
            50 => array('PID' => '15L6364Z', 'NAME' => 'MOKTAR', 'DESIG' => 'INPUTMAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10490'),
            51 => array('PID' => '18D3766F', 'NAME' => 'ABU RAIHAN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9800'),
            52 => array('PID' => '18D0313C', 'NAME' => 'MD. EMAN ALI', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2019-03-01', 'L_INCR_AMT' => '3000', 'CURRENT_SALARY' => '18000'),
            53 => array('PID' => '18A1775E', 'NAME' => 'DOLI BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '9500'),
            54 => array('PID' => '18A2732F', 'NAME' => 'LUTIFA BANU', 'DESIG' => 'SEWING IRON MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9200'),
            55 => array('PID' => '18A2465E', 'NAME' => 'FATEMA KHATUN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10000'),
            56 => array('PID' => '12H6069G', 'NAME' => 'MST. FUL MALA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10000'),
            57 => array('PID' => '17M5203M', 'NAME' => 'SHAJIBUR RAHMAN', 'DESIG' => 'OFFICER ELECTRICIAN', 'L_INCR_DT' => '2019-12-01', 'L_INCR_AMT' => '2500', 'CURRENT_SALARY' => '30000'),
            58 => array('PID' => '16J1574E', 'NAME' => 'AMARI AKTER', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2019-03-01', 'L_INCR_AMT' => '3000', 'CURRENT_SALARY' => '18000'),
            59 => array('PID' => '18C1375E', 'NAME' => 'KOHINUR', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9750'),
            60 => array('PID' => '18D4311H', 'NAME' => 'HALIMA', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            61 => array('PID' => '18A2157E', 'NAME' => 'LAILY KHATUN', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10850'),
            62 => array('PID' => '18A2265E', 'NAME' => 'FIROZA KHATUN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '370', 'CURRENT_SALARY' => '9600'),
            63 => array('PID' => '18A2268E', 'NAME' => 'MD. SHOFIZOL HA', 'DESIG' => 'SR. SAMPLEMAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '12350'),
            64 => array('PID' => '18A2337E', 'NAME' => 'MD. KANCHON MIA', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10600'),
            65 => array('PID' => '17D0419C', 'NAME' => 'MD.HABIBUR RAHMAN', 'DESIG' => 'MANAGER IE & PLANING', 'L_INCR_DT' => '2020-01-01', 'L_INCR_AMT' => '7000', 'CURRENT_SALARY' => '48000'),
            66 => array('PID' => '18A0252C', 'NAME' => 'MAJIBAR RAHMAN', 'DESIG' => 'ASST MANAGER IE', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '7000', 'CURRENT_SALARY' => '37000'),
            67 => array('PID' => '18A0732D', 'NAME' => 'MIRZA SADIKUR RAHAMAN', 'DESIG' => 'OFFICER ACCOUNTS', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '7500', 'CURRENT_SALARY' => '23500'),
            68 => array('PID' => '18A4723J', 'NAME' => 'ARIF HOSSAIN', 'DESIG' => 'INPUTMAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            69 => array('PID' => '18A2458E', 'NAME' => 'NAZMA BEGUM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10400'),
            70 => array('PID' => '18A5621K', 'NAME' => 'MAYA RANI CHOKROBORTI', 'DESIG' => 'CLEANER', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9400'),
            71 => array('PID' => '18B4725J', 'NAME' => 'MD. RIPON HOSSEN', 'DESIG' => 'JUNIUR OFFICER CUTTING', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '5580', 'CURRENT_SALARY' => '14500'),
            72 => array('PID' => '18B1291E', 'NAME' => 'MALEKA KHATUN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10500'),
            73 => array('PID' => '18B1300E', 'NAME' => 'MD. SOHEL RANA MALTIA', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '7600', 'CURRENT_SALARY' => '18000'),
            74 => array('PID' => '18B3533F', 'NAME' => 'NARGIS FATEMA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9100'),
            75 => array('PID' => '18B3543F', 'NAME' => 'RIMA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '345', 'CURRENT_SALARY' => '9095'),
            76 => array('PID' => '18B4166H', 'NAME' => 'BABLI AKTER', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            77 => array('PID' => '18B4172H', 'NAME' => 'HAFIZUL  ISLAM', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            78 => array('PID' => '18B4173H', 'NAME' => 'NASRUL ISLAM', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '410', 'CURRENT_SALARY' => '10410'),
            79 => array('PID' => '18B1438E', 'NAME' => 'AKHI AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10350'),
            80 => array('PID' => '18B1468E', 'NAME' => 'SHANTY', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10400'),
            81 => array('PID' => '18B3547F', 'NAME' => 'BOKUL', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9100'),
            82 => array('PID' => '18B4064H', 'NAME' => 'ABDUS SATTAR', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            83 => array('PID' => '18B6549G', 'NAME' => 'HELENA AKTER', 'DESIG' => 'SPOT MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            84 => array('PID' => '18B6668G', 'NAME' => 'PARVEZ', 'DESIG' => 'SUPERVISOR', 'L_INCR_DT' => '2020-02-01', 'L_INCR_AMT' => '3000', 'CURRENT_SALARY' => '20000'),
            85 => array('PID' => '18B0261C', 'NAME' => 'MD.BABUL HOSSAIN', 'DESIG' => 'SENIOR MARKER MAN', 'L_INCR_DT' => '2020-01-01', 'L_INCR_AMT' => '3000', 'CURRENT_SALARY' => '37000'),
            86 => array('PID' => '18A5322L', 'NAME' => 'YEAKUB ALI MOLLA', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '11875'),
            87 => array('PID' => '18B1270E', 'NAME' => 'MST. ROZINA KHATUN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10600'),
            88 => array('PID' => '18B1673E', 'NAME' => 'MAKSUDA', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2019-03-01', 'L_INCR_AMT' => '3000', 'CURRENT_SALARY' => '18000'),
            89 => array('PID' => '18A5460Z', 'NAME' => 'SHAJAHAN', 'DESIG' => 'STORE LABOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9275'),
            90 => array('PID' => '18A5466Z', 'NAME' => 'MD.ABDUL KUDDUS', 'DESIG' => 'STORE LABOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9375'),
            91 => array('PID' => '18A5205M', 'NAME' => 'MD.BABU', 'DESIG' => 'JR. OFFICER ELECTRICIAN', 'L_INCR_DT' => '2020-02-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '19000'),
            92 => array('PID' => '18A5207M', 'NAME' => 'MD. ABU SALIM', 'DESIG' => 'JR. OFFICER MECHANIC', 'L_INCR_DT' => '2020-02-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '25000'),
            93 => array('PID' => '18A1656E', 'NAME' => 'MD. RUBEL MIA', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '2500', 'CURRENT_SALARY' => '21500'),
            94 => array('PID' => '18A1348E', 'NAME' => 'MUNNI', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '375', 'CURRENT_SALARY' => '9675'),
            95 => array('PID' => '18A1440E', 'NAME' => 'MST.RAHIMA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10300'),
            96 => array('PID' => '17A0854U', 'NAME' => 'MD. ASLAM HUSSAIN', 'DESIG' => 'MERCHANDISER', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '10000', 'CURRENT_SALARY' => '40000'),
            97 => array('PID' => '18E1029E', 'NAME' => 'MONIR', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10800'),
            98 => array('PID' => '18E1032E', 'NAME' => 'BIPLOB', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9200'),
            99 => array('PID' => '18E5217M', 'NAME' => 'TUTUL CHANDRA BISHWAS', 'DESIG' => 'OFFICER ELECTRICIAN', 'L_INCR_DT' => '2019-05-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '23000'),
            100 => array('PID' => '18B4121H', 'NAME' => 'MIRZA FORHAD', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            101 => array('PID' => '18B3917F', 'NAME' => 'MAKSUDA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9150'),
            102 => array('PID' => '18B4293H', 'NAME' => 'ROFIQUL ISLAM', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            103 => array('PID' => '18B6037G', 'NAME' => 'MD. SHAKIL MIA', 'DESIG' => 'JUNIOR PACKER', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9875'),
            104 => array('PID' => '18B4865J', 'NAME' => 'MD. IBRAHIM HOSSAIN', 'DESIG' => 'NIDDLE MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            105 => array('PID' => '18B1615E', 'NAME' => 'KOLPONA BEGUM', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10750'),
            106 => array('PID' => '18B6846G', 'NAME' => 'MST. SALMA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9250'),
            107 => array('PID' => '18C4212H', 'NAME' => 'SHIRIN', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            108 => array('PID' => '18C4562J', 'NAME' => 'MD. AZIZUL ISLAM', 'DESIG' => 'SUPERVISOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '8080', 'CURRENT_SALARY' => '17000'),
            109 => array('PID' => '18C4049H', 'NAME' => 'MONIR HOSSAIN', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            110 => array('PID' => '18C3621F', 'NAME' => 'SHEFALI BEGUM', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9100'),
            111 => array('PID' => '18C2582F', 'NAME' => 'KHADIZA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9100'),
            112 => array('PID' => '18C3137R', 'NAME' => 'GOWTAM MITRA', 'DESIG' => 'ASST. FEBRIC TECHNICIAN', 'L_INCR_DT' => '2019-03-02', 'L_INCR_AMT' => '4000', 'CURRENT_SALARY' => '19000'),
            113 => array('PID' => '18D1423E', 'NAME' => 'SHATHI', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9700'),
            114 => array('PID' => '18D5468Q', 'NAME' => 'MD. SHARIFUL ISLAM', 'DESIG' => 'MESSENGER', 'L_INCR_DT' => '2019-04-01', 'L_INCR_AMT' => '3000', 'CURRENT_SALARY' => '13000'),
            115 => array('PID' => '18C6134G', 'NAME' => 'MD. MAMUN HOSSAIN', 'DESIG' => 'PACKER', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            116 => array('PID' => '18C6135G', 'NAME' => 'MD. ABDUR RAZZAK', 'DESIG' => 'PACKER', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            117 => array('PID' => '18D6213G', 'NAME' => 'MST. SHIRINA', 'DESIG' => 'POLY MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            118 => array('PID' => '18C1579E', 'NAME' => 'SAZEDA', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10850'),
            119 => array('PID' => '18C1632E', 'NAME' => 'JORINA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '360', 'CURRENT_SALARY' => '9412'),
            120 => array('PID' => '18C1692E', 'NAME' => 'JOSNA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '380', 'CURRENT_SALARY' => '9770'),
            121 => array('PID' => '18C3649F', 'NAME' => 'MOMOTA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '9200'),
            122 => array('PID' => '18C1889E', 'NAME' => 'SHELPY AKTER', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10700'),
            123 => array('PID' => '18C3654F', 'NAME' => 'SHAIFUL', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10100'),
            124 => array('PID' => '18J1076E', 'NAME' => 'MD. IMRAN HOSSAIN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '360', 'CURRENT_SALARY' => '9260'),
            125 => array('PID' => '18J1079E', 'NAME' => 'MINARA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '365', 'CURRENT_SALARY' => '9423'),
            126 => array('PID' => '18J3488F', 'NAME' => 'MST. FUARA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '330', 'CURRENT_SALARY' => '8730'),
            127 => array('PID' => '18J1117E', 'NAME' => 'DOLA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10000'),
            128 => array('PID' => '18J1120E', 'NAME' => 'SHARMIN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10400'),
            129 => array('PID' => '18J1124E', 'NAME' => 'SHOHEL', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '410', 'CURRENT_SALARY' => '10410'),
            130 => array('PID' => '18J5666K', 'NAME' => 'AFROZA', 'DESIG' => 'CLEANER', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9300'),
            131 => array('PID' => '18J1127E', 'NAME' => 'ROKEYA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10130'),
            132 => array('PID' => '18J1129E', 'NAME' => 'SHAMIMA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '680', 'CURRENT_SALARY' => '9600'),
            133 => array('PID' => '18J1161E', 'NAME' => 'SHAMIA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '370', 'CURRENT_SALARY' => '9540'),
            134 => array('PID' => '18J1163E', 'NAME' => 'SHAHNAZ', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10500'),
            135 => array('PID' => '18J0249W', 'NAME' => 'MD. SAZID MAHAM', 'DESIG' => 'SR. SAMPLEMAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '680', 'CURRENT_SALARY' => '16030'),
            136 => array('PID' => '18K1198E', 'NAME' => 'NAZMA', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '10620'),
            137 => array('PID' => '18K0283C', 'NAME' => 'MD. ANOWAR PARVAG', 'DESIG' => 'SR. OFFICER PRODUCTION', 'L_INCR_DT' => '2019-10-01', 'L_INCR_AMT' => '4000', 'CURRENT_SALARY' => '21000'),
            138 => array('PID' => '18L1090E', 'NAME' => 'SUMAIYA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10230'),
            139 => array('PID' => '18L0293C', 'NAME' => 'RIPON', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2019-12-01', 'L_INCR_AMT' => '1000', 'CURRENT_SALARY' => '19000'),
            140 => array('PID' => '18G0348C', 'NAME' => 'MD. MAZEDUL TALUKDER', 'DESIG' => 'SUPERVISOR', 'L_INCR_DT' => '2019-07-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '22000'),
            141 => array('PID' => '18G5319V', 'NAME' => 'SONJOY SING', 'DESIG' => 'DRIVER', 'L_INCR_DT' => '2019-07-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '14000'),
            142 => array('PID' => '18J4047H', 'NAME' => 'FOJLE RABBI', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            143 => array('PID' => '18K1194E', 'NAME' => 'POLY', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            144 => array('PID' => '18K2606F', 'NAME' => 'SHOPNA BEGUM', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '345', 'CURRENT_SALARY' => '9150'),
            145 => array('PID' => '18M1013E', 'NAME' => 'RUBI AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9250'),
            146 => array('PID' => '18M1294E', 'NAME' => 'FARUK SORKER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10100'),
            147 => array('PID' => '18M1313E', 'NAME' => 'RUNA', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '550', 'CURRENT_SALARY' => '10850'),
            148 => array('PID' => '18G1388E', 'NAME' => 'MAHFUZ', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10850'),
            149 => array('PID' => '18G0275C', 'NAME' => 'SREE SHAYMOL BABU', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2019-03-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '19000'),
            150 => array('PID' => '18G0203W', 'NAME' => 'RIPON BARUA', 'DESIG' => 'SR. SAMPLEMAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '700', 'CURRENT_SALARY' => '16400'),
            151 => array('PID' => '18G4032H', 'NAME' => 'MD. MARUF AHAMMED', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            152 => array('PID' => '18J1095E', 'NAME' => 'HASINA KHATUN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10100'),
            153 => array('PID' => '18J1097E', 'NAME' => 'INSAN ALI', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '360', 'CURRENT_SALARY' => '9260'),
            154 => array('PID' => '18J1100E', 'NAME' => 'NUR NAHAR', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9300'),
            155 => array('PID' => '18L1183E', 'NAME' => 'MUSLIMA KHATUN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2019-12-31', 'L_INCR_AMT' => '371', 'CURRENT_SALARY' => '9400'),
            156 => array('PID' => '18L1253E', 'NAME' => 'ASHA AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9250'),
            157 => array('PID' => '19A6196G', 'NAME' => 'MOMINUR ROHMAN', 'DESIG' => 'JUNIOR IRONMAN FINISHING', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9700'),
            158 => array('PID' => '19A4017H', 'NAME' => 'NAZMUL', 'DESIG' => 'JUNIOR QUALITY INSPECTOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9900'),
            159 => array('PID' => '19A0298C', 'NAME' => 'SHAMIM HOSSAIN', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '22000'),
            160 => array('PID' => '19A1145E', 'NAME' => 'MOUSUMI', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10850'),
            161 => array('PID' => '19A1228E', 'NAME' => 'RAZIA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10000'),
            162 => array('PID' => '19A1311E', 'NAME' => 'RUMA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9800'),
            163 => array('PID' => '18G1014E', 'NAME' => 'SHATHI', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '410', 'CURRENT_SALARY' => '10410'),
            164 => array('PID' => '18G4056H', 'NAME' => 'SHOBUZ', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            165 => array('PID' => '18G2514F', 'NAME' => 'KOHINOOR', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9100'),
            166 => array('PID' => '18G2520F', 'NAME' => 'AKLIMA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '9500'),
            167 => array('PID' => '18J3462F', 'NAME' => 'MST. RAZIYA AKT', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10200'),
            168 => array('PID' => '18L1257E', 'NAME' => 'BEDENA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '405', 'CURRENT_SALARY' => '10355'),
            169 => array('PID' => '18M1282E', 'NAME' => 'ROHIMA AKTER CH', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9450'),
            170 => array('PID' => '19A2701F', 'NAME' => 'SHEMA PARVIN', 'DESIG' => 'SEWING ASSISTANT', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '330', 'CURRENT_SALARY' => '8640'),
            171 => array('PID' => '19A3632F', 'NAME' => 'SHOFIR', 'DESIG' => 'SEWING IRON MAN', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9200'),
            172 => array('PID' => '19A1349E', 'NAME' => 'JAKIR', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10850'),
            173 => array('PID' => '18B5312L', 'NAME' => 'M.A. HAMZA', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '11875'),
            174 => array('PID' => '18F5324L', 'NAME' => 'SHOFIQUL ISLAM', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '11875'),
            175 => array('PID' => '18J1080E', 'NAME' => 'MST. HALIMA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '360', 'CURRENT_SALARY' => '9260'),
            176 => array('PID' => '18J1082E', 'NAME' => 'MST. SUJOLA KAHTUN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9925'),
            177 => array('PID' => '18L1256E', 'NAME' => 'SHIRINA BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '375', 'CURRENT_SALARY' => '9655'),
            178 => array('PID' => '19A1000E', 'NAME' => 'SHORIF MIAH', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10600'),
            179 => array('PID' => '19A1104E', 'NAME' => 'MURAD', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10500'),
            180 => array('PID' => '19A3254F', 'NAME' => 'SAJEDA KHATUN', 'DESIG' => 'SEWING ASSISTANT', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '330', 'CURRENT_SALARY' => '8640'),
            181 => array('PID' => '19A4561J', 'NAME' => 'MONIRUL', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9000'),
            182 => array('PID' => '19A3521F', 'NAME' => 'ASMA BEGUM', 'DESIG' => 'SEWING ASSISTANT', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '330', 'CURRENT_SALARY' => '8640'),
            183 => array('PID' => '19A3683F', 'NAME' => 'ROCHONA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9300'),
            184 => array('PID' => '19A4068H', 'NAME' => 'WAHIDUL ISLAM', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            185 => array('PID' => '18D5333L', 'NAME' => 'MD. MONIRUL HAQUE MOLLIK', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '11875'),
            186 => array('PID' => '18G5602L', 'NAME' => 'MAKHAN CHANDRA DEBNATH', 'DESIG' => 'SUPERVISOR', 'L_INCR_DT' => '2019-04-30', 'L_INCR_AMT' => '9225', 'CURRENT_SALARY' => '20000'),
            187 => array('PID' => '18G5605L', 'NAME' => 'MD. MOSHARAF HOSSAIN', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '11875'),
            188 => array('PID' => '18G5614L', 'NAME' => 'JASIM UDDIN MOLLA', 'DESIG' => 'SUPERVISOR', 'L_INCR_DT' => '2019-04-01', 'L_INCR_AMT' => '9225', 'CURRENT_SALARY' => '20000'),
            189 => array('PID' => '18D5618L', 'NAME' => 'MD. SAMIUL ISLAM', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '11875'),
            190 => array('PID' => '18G1618E', 'NAME' => 'HELENA BEGUM', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10700'),
            191 => array('PID' => '18H4626J', 'NAME' => 'TAPOSH KUMAR ROY', 'DESIG' => 'JR. BUNDILINGMAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '8900'),
            192 => array('PID' => '18J3380F', 'NAME' => 'MD. MONIRUZZAMAN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9860'),
            193 => array('PID' => '18J1036E', 'NAME' => 'TAZALLI', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10600'),
            194 => array('PID' => '18J4040H', 'NAME' => 'NUR MOHAMMOD', 'DESIG' => 'OFFICER Q.C', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '7153', 'CURRENT_SALARY' => '17000'),
            195 => array('PID' => '18J1039E', 'NAME' => 'TASLIMA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '410', 'CURRENT_SALARY' => '10460'),
            196 => array('PID' => '18J1148E', 'NAME' => 'HASNA BEGUM SHIMA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9950'),
            197 => array('PID' => '18J1149E', 'NAME' => 'MST. MALEKA BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '360', 'CURRENT_SALARY' => '9310'),
            198 => array('PID' => '18J1151E', 'NAME' => 'JESMIN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '410', 'CURRENT_SALARY' => '10310'),
            199 => array('PID' => '18J1160E', 'NAME' => 'LAILY BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '370', 'CURRENT_SALARY' => '9540'),
            200 => array('PID' => '18K1182E', 'NAME' => 'SHANTI', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '370', 'CURRENT_SALARY' => '9540'),
            201 => array('PID' => '18K1204E', 'NAME' => 'POLY AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10600'),
            202 => array('PID' => '18K1206E', 'NAME' => 'KHADIZA AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9900'),
            203 => array('PID' => '18G2588F', 'NAME' => 'JESMIN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '345', 'CURRENT_SALARY' => '9095'),
            204 => array('PID' => '18G1075E', 'NAME' => 'SHUKHI', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9800'),
            205 => array('PID' => '18G3062Q', 'NAME' => 'MD. ABDUL HAMID', 'DESIG' => 'JR OFFICER STORE', 'L_INCR_DT' => '2019-11-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '12500'),
            206 => array('PID' => '18G4140H', 'NAME' => 'MD. MEHEDI HASAN', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            207 => array('PID' => '18G2816F', 'NAME' => 'RABIA BEGUM', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9100'),
            208 => array('PID' => '18J4048H', 'NAME' => 'MILON REZA', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            209 => array('PID' => '18J1131E', 'NAME' => 'KHALEDA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10100'),
            210 => array('PID' => '18K1173E', 'NAME' => 'TASLIMA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '390', 'CURRENT_SALARY' => '10005'),
            211 => array('PID' => '18K1208E', 'NAME' => 'MILA', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '550', 'CURRENT_SALARY' => '10850'),
            212 => array('PID' => '18K1211E', 'NAME' => 'MINA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '390', 'CURRENT_SALARY' => '10005'),
            213 => array('PID' => '18K4066H', 'NAME' => 'MD. RIPON SIKDER', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            214 => array('PID' => '18G6009G', 'NAME' => 'RINA PARVIN', 'DESIG' => 'FOLDING MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            215 => array('PID' => '18G1393E', 'NAME' => 'MAHFUZA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10500'),
            216 => array('PID' => '18G4071H', 'NAME' => 'DELOWAR', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            217 => array('PID' => '18G6010G', 'NAME' => 'JHORNA', 'DESIG' => 'POLY MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            218 => array('PID' => '18G1460E', 'NAME' => 'JOHORA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '410', 'CURRENT_SALARY' => '10410'),
            219 => array('PID' => '18G2609F', 'NAME' => 'RUNA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9100'),
            220 => array('PID' => '18G2682F', 'NAME' => 'SALMA KHATUN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '9520'),
            221 => array('PID' => '18G4013H', 'NAME' => 'MD. SANI SORDER', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            222 => array('PID' => '18G1710E', 'NAME' => 'MST. SUMI AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10300'),
            223 => array('PID' => '18J1042E', 'NAME' => 'ADURI', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10150'),
            224 => array('PID' => '18J1043E', 'NAME' => 'MASUM', 'DESIG' => 'SR. SAMPLEMAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '540', 'CURRENT_SALARY' => '13190'),
            225 => array('PID' => '18J1056E', 'NAME' => 'RIMA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9550'),
            226 => array('PID' => '18J1064E', 'NAME' => 'JESMIN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10200'),
            227 => array('PID' => '19A3730F', 'NAME' => 'SUMI AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '490', 'CURRENT_SALARY' => '8800'),
            228 => array('PID' => '19A3748F', 'NAME' => 'PARVIN BEGUM', 'DESIG' => 'SEWING ASSISTANT', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '330', 'CURRENT_SALARY' => '8640'),
            229 => array('PID' => '19B0265C', 'NAME' => 'ALOM HOSSAIN', 'DESIG' => 'TRAINER', 'L_INCR_DT' => '2020-02-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '20500'),
            230 => array('PID' => '19C1403E', 'NAME' => 'NAHAR AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-03-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10200'),
            231 => array('PID' => '19D1435E', 'NAME' => 'ROKSANA KHATUN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-04-01', 'L_INCR_AMT' => '358', 'CURRENT_SALARY' => '9358'),
            232 => array('PID' => '19D1006E', 'NAME' => 'MONOWARA BEGUM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-04-01', 'L_INCR_AMT' => '383', 'CURRENT_SALARY' => '9883'),
            233 => array('PID' => '19F3479F', 'NAME' => 'MST. SHAHNAZ BEGUM', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '342', 'CURRENT_SALARY' => '8650'),
            234 => array('PID' => '19H1034E', 'NAME' => 'ASHA SARKER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-08-01', 'L_INCR_AMT' => '383', 'CURRENT_SALARY' => '9883'),
            235 => array('PID' => '19H1037E', 'NAME' => 'NIROB', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-08-01', 'L_INCR_AMT' => '358', 'CURRENT_SALARY' => '9358'),
            236 => array('PID' => '19J6264G', 'NAME' => 'MD. SUKKUR ALI', 'DESIG' => 'JUNIOR IRONMAN FINISHING', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '365', 'CURRENT_SALARY' => '9465'),
            237 => array('PID' => '19J4107H', 'NAME' => 'MD. RASEL MIA', 'DESIG' => 'JUNIOR QUALITY INSPECTOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9300'),
            238 => array('PID' => '19J4108H', 'NAME' => 'MST. KULSUM', 'DESIG' => 'JUNIOR QUALITY INSPECTOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9800'),
            239 => array('PID' => '19J1022E', 'NAME' => 'MST. GULSANA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '9300'),
            240 => array('PID' => '19J1084E', 'NAME' => 'MST. REHENA PARVIN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            241 => array('PID' => '19J1112E', 'NAME' => 'MST. SABINA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '550', 'CURRENT_SALARY' => '10050'),
            242 => array('PID' => '19A3427F', 'NAME' => 'ASMA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '9350'),
            243 => array('PID' => '19B3837F', 'NAME' => 'MINU', 'DESIG' => 'SEWING ASSISTANT', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '325', 'CURRENT_SALARY' => '8635'),
            244 => array('PID' => '19B1191E', 'NAME' => 'BANESA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '370', 'CURRENT_SALARY' => '9570'),
            245 => array('PID' => '19B1153E', 'NAME' => 'MONI', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '10620'),
            246 => array('PID' => '19B3859F', 'NAME' => 'MD. RAZU MONDOL', 'DESIG' => 'SEWING IRON MAN', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '360', 'CURRENT_SALARY' => '9260'),
            247 => array('PID' => '19B1376E', 'NAME' => 'ESMETARA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10000'),
            248 => array('PID' => '19B0650W', 'NAME' => 'MAHFUZA', 'DESIG' => 'SAMPLE MAN', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '585', 'CURRENT_SALARY' => '14070'),
            249 => array('PID' => '19B3886F', 'NAME' => 'TURJOY SHORKER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9800'),
            250 => array('PID' => '19B5351V', 'NAME' => 'MD. MINHAZUR RAHMAN', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-02-01', 'L_INCR_AMT' => '800', 'CURRENT_SALARY' => '8800'),
            251 => array('PID' => '19C1220E', 'NAME' => 'SALMA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10180'),
            252 => array('PID' => '19C1227E', 'NAME' => 'RUBINA BEGUM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-03-01', 'L_INCR_AMT' => '390', 'CURRENT_SALARY' => '9890'),
            253 => array('PID' => '19E1115E', 'NAME' => 'SHAKATUN BEGUM', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-05-01', 'L_INCR_AMT' => '343', 'CURRENT_SALARY' => '9043'),
            254 => array('PID' => '19F1245E', 'NAME' => 'MD. ASADUL ISLAM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '378', 'CURRENT_SALARY' => '9778'),
            255 => array('PID' => '19F2537F', 'NAME' => 'MST. JESMIN AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '492', 'CURRENT_SALARY' => '8800'),
            256 => array('PID' => '19F2591F', 'NAME' => 'MST. MODINA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '480', 'CURRENT_SALARY' => '8800'),
            257 => array('PID' => '19F1252E', 'NAME' => 'MST. HAFIZA AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '373', 'CURRENT_SALARY' => '9673'),
            258 => array('PID' => '19F1261E', 'NAME' => 'MD. MEHEDI HASAN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '338', 'CURRENT_SALARY' => '8938'),
            259 => array('PID' => '19F1262E', 'NAME' => 'MST. ASMA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '348', 'CURRENT_SALARY' => '9148'),
            260 => array('PID' => '19F1266E', 'NAME' => 'SHUMI AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '338', 'CURRENT_SALARY' => '8938'),
            261 => array('PID' => '19B3573F', 'NAME' => 'MD. ALAL', 'DESIG' => 'SUPERVISOR', 'L_INCR_DT' => '2020-02-01', 'L_INCR_AMT' => '9500', 'CURRENT_SALARY' => '18000'),
            262 => array('PID' => '19B1085E', 'NAME' => 'KULSUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10000'),
            263 => array('PID' => '19B1121E', 'NAME' => 'MAHFUZA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            264 => array('PID' => '19B3710F', 'NAME' => 'AMBIYA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9250'),
            265 => array('PID' => '19B3731F', 'NAME' => 'ONJONA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8810'),
            266 => array('PID' => '19B5209M', 'NAME' => 'FOIZUL ISLAM', 'DESIG' => 'OFFICER', 'L_INCR_DT' => '2020-02-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '27000'),
            267 => array('PID' => '19B1400E', 'NAME' => 'JOSNA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9800'),
            268 => array('PID' => '19B5706K', 'NAME' => 'SHAHNAZ', 'DESIG' => 'CLEANER', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9475'),
            269 => array('PID' => '19E1114E', 'NAME' => 'MST. BORHANA AKTER', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '300', 'CURRENT_SALARY' => '10550'),
            270 => array('PID' => '19F1234E', 'NAME' => 'MD. KAMRUZZAMAN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9183'),
            271 => array('PID' => '19F1267E', 'NAME' => 'AYNA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '348', 'CURRENT_SALARY' => '9148'),
            272 => array('PID' => '19F1268E', 'NAME' => 'KHUKUMONI', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10245'),
            273 => array('PID' => '19F1031E', 'NAME' => 'MST. SHATHI KHATUN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '373', 'CURRENT_SALARY' => '9673'),
            274 => array('PID' => '19F4079H', 'NAME' => 'AL-AMIN', 'DESIG' => 'JUNIOR QUALITY INSPECTOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '368', 'CURRENT_SALARY' => '9568'),
            275 => array('PID' => '19F4080H', 'NAME' => 'RAZIB MIA', 'DESIG' => 'JUNIOR QUALITY INSPECTOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '368', 'CURRENT_SALARY' => '9568'),
            276 => array('PID' => '19F4083H', 'NAME' => 'AMENA KHATUN', 'DESIG' => 'JUNIOR QUALITY INSPECTOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '373', 'CURRENT_SALARY' => '9673'),
            277 => array('PID' => '19F4090H', 'NAME' => 'ALAUDDIN', 'DESIG' => 'JUNIOR QUALITY INSPECTOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '368', 'CURRENT_SALARY' => '9568'),
            278 => array('PID' => '19F4091H', 'NAME' => 'MD. SHEHAB UDDIN', 'DESIG' => 'ORDINARY QUALITY INSPECTOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '333', 'CURRENT_SALARY' => '8833'),
            279 => array('PID' => '19F1089E', 'NAME' => 'MST. SHOPNA PARVIN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '393', 'CURRENT_SALARY' => '10093'),
            280 => array('PID' => '19F1125E', 'NAME' => 'MST. NASIMA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '393', 'CURRENT_SALARY' => '10093'),
            281 => array('PID' => '19F3122F', 'NAME' => 'MD. MASUK MIA', 'DESIG' => 'SEWING IRON MAN', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '333', 'CURRENT_SALARY' => '8833'),
            282 => array('PID' => '19F6085G', 'NAME' => 'NARGIS AKTER', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '308', 'CURRENT_SALARY' => '8308'),
            283 => array('PID' => '19F6169G', 'NAME' => 'MST. JAMELA BEGUM', 'DESIG' => 'ORDINARY OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '329', 'CURRENT_SALARY' => '8749'),
            284 => array('PID' => '19F4103H', 'NAME' => 'MST. SHAJIYA SULTANA', 'DESIG' => 'ORDINARY QUALITY INSPECTOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '348', 'CURRENT_SALARY' => '9148'),
            285 => array('PID' => '19F3450F', 'NAME' => 'TANZILA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '8708'),
            286 => array('PID' => '19F1285E', 'NAME' => 'RIBANA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '333', 'CURRENT_SALARY' => '8833'),
            287 => array('PID' => '19G1207E', 'NAME' => 'MST. SHOPNA BEGUM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-07-01', 'L_INCR_AMT' => '383', 'CURRENT_SALARY' => '9883'),
            288 => array('PID' => '19G6270G', 'NAME' => 'PARVIN BEGUM', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '8708'),
            289 => array('PID' => '19G2995F', 'NAME' => 'MST. YASMIN AKTER', 'DESIG' => 'SEWING IRON MAN', 'L_INCR_DT' => '2020-07-01', 'L_INCR_AMT' => '333', 'CURRENT_SALARY' => '8833'),
            290 => array('PID' => '19A3806F', 'NAME' => 'HAZERA', 'DESIG' => 'SEWING IRON MAN', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9105'),
            291 => array('PID' => '19C1154E', 'NAME' => 'MITU', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-03-01', 'L_INCR_AMT' => '360', 'CURRENT_SALARY' => '9360'),
            292 => array('PID' => '19D6114G', 'NAME' => 'MD. ABDUR RASHID', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-04-01', 'L_INCR_AMT' => '308', 'CURRENT_SALARY' => '8308'),
            293 => array('PID' => '19E1118E', 'NAME' => 'MST. NUR NAHAR', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-05-01', 'L_INCR_AMT' => '363', 'CURRENT_SALARY' => '9463'),
            294 => array('PID' => '19G1290E', 'NAME' => 'SUMONA AKTER SURAIA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '9708'),
            295 => array('PID' => '19G1299E', 'NAME' => 'MST. KULSUM AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-07-01', 'L_INCR_AMT' => '393', 'CURRENT_SALARY' => '10093'),
            296 => array('PID' => '19G1326E', 'NAME' => 'MST. SONIYA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-07-01', 'L_INCR_AMT' => '393', 'CURRENT_SALARY' => '10093'),
            297 => array('PID' => '19G1328E', 'NAME' => 'MST. HOSNA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-07-01', 'L_INCR_AMT' => '363', 'CURRENT_SALARY' => '9463'),
            298 => array('PID' => '19G1331E', 'NAME' => 'NAZMA KHATUN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '332', 'CURRENT_SALARY' => '9900'),
            299 => array('PID' => '19H4058H', 'NAME' => 'RUNA AKTER', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-08-01', 'L_INCR_AMT' => '375', 'CURRENT_SALARY' => '9722'),
            300 => array('PID' => '19B1418E', 'NAME' => 'MORZINA AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '390', 'CURRENT_SALARY' => '9990'),
            301 => array('PID' => '19C1108E', 'NAME' => 'JANNAT', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10180'),
            302 => array('PID' => '19C1137E', 'NAME' => 'MUKTA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-03-01', 'L_INCR_AMT' => '380', 'CURRENT_SALARY' => '9680'),
            303 => array('PID' => '19D1065E', 'NAME' => 'KOMOLA BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-04-01', 'L_INCR_AMT' => '373', 'CURRENT_SALARY' => '9673'),
            304 => array('PID' => '19D4530J', 'NAME' => 'MD. SAIDUR RAHMAN', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-04-01', 'L_INCR_AMT' => '308', 'CURRENT_SALARY' => '8308'),
            305 => array('PID' => '19D1092E', 'NAME' => 'JUMUR BEGUM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-04-01', 'L_INCR_AMT' => '388', 'CURRENT_SALARY' => '9988'),
            306 => array('PID' => '19F1222E', 'NAME' => 'YASIN RANA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '373', 'CURRENT_SALARY' => '9673'),
            307 => array('PID' => '19C5711K', 'NAME' => 'JAMILA', 'DESIG' => 'CLEANER', 'L_INCR_DT' => '2020-03-01', 'L_INCR_AMT' => '330', 'CURRENT_SALARY' => '8705'),
            308 => array('PID' => '19D1007E', 'NAME' => 'SALMA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-04-01', 'L_INCR_AMT' => '393', 'CURRENT_SALARY' => '10093'),
            309 => array('PID' => '19F1159E', 'NAME' => 'MAZIDUL RAHMAN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '333', 'CURRENT_SALARY' => '8833'),
            310 => array('PID' => '19F1165E', 'NAME' => 'MST. SHAHIDA BEGUM', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '343', 'CURRENT_SALARY' => '9043'),
            311 => array('PID' => '19F1168E', 'NAME' => 'SREE SHONJIT', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '378', 'CURRENT_SALARY' => '9778'),
            312 => array('PID' => '19F1177E', 'NAME' => 'EMDADUL', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '398', 'CURRENT_SALARY' => '10198'),
            313 => array('PID' => '19F1189E', 'NAME' => 'AKHIUZZAMAN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '343', 'CURRENT_SALARY' => '9043'),
            314 => array('PID' => '19F5825U', 'NAME' => 'SREE SOYON CHANDRA BISHAWSARMA', 'DESIG' => 'ASSISTENT PRODUCTION REPOTER', 'L_INCR_DT' => '2020-06-01', 'L_INCR_AMT' => '373', 'CURRENT_SALARY' => '9673'),
            315 => array('PID' => '19G1350E', 'NAME' => 'SHOJIB', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-07-01', 'L_INCR_AMT' => '383', 'CURRENT_SALARY' => '9883'),
            316 => array('PID' => '19J1327E', 'NAME' => 'MD. RASHIDUL ISLAM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '220', 'CURRENT_SALARY' => '10000'),
            317 => array('PID' => '19J3565F', 'NAME' => 'MD. ASHIK MIA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '335', 'CURRENT_SALARY' => '9200'),
            318 => array('PID' => '19J3574F', 'NAME' => 'MD. MOTALEB HOSSAIN SHEKH', 'DESIG' => 'SEWING IRON MAN', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '335', 'CURRENT_SALARY' => '8835'),
            319 => array('PID' => '19J1357E', 'NAME' => 'ROKSANA PARVEEN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '355', 'CURRENT_SALARY' => '9230'),
            320 => array('PID' => '19K3433F', 'NAME' => 'MST. SHARMIN AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '327', 'CURRENT_SALARY' => '8635'),
            321 => array('PID' => '19L1414E', 'NAME' => 'TAWHIDA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9300'),
            322 => array('PID' => '19L4822J', 'NAME' => 'SHAMIM MIA', 'DESIG' => 'ORD. FUSING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '335', 'CURRENT_SALARY' => '8835'),
            323 => array('PID' => '19L4009H', 'NAME' => 'HAFIZUR RAHMAN', 'DESIG' => 'ORDINARY QUALITY INSPECTOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '9100'),
            324 => array('PID' => '19M1110E', 'NAME' => 'SHEWLE', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10500'),
            325 => array('PID' => '19M6511G', 'NAME' => 'MST. NASIMA', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '8350'),
            326 => array('PID' => '19M2544F', 'NAME' => 'MST. RABIYA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '8660'),
            327 => array('PID' => '19M6258G', 'NAME' => 'MST. LUTFUNNAHAR', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '8800'),
            328 => array('PID' => '19M6725G', 'NAME' => 'MST. SOKINA BEGUM', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '8350'),
            329 => array('PID' => '19M3423F', 'NAME' => 'MST. SHILA KHATUN', 'DESIG' => 'ORDINARY POLY MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9200'),
            330 => array('PID' => '19J4691Z', 'NAME' => 'MD. IBRAHIM HOSSAIN', 'DESIG' => 'STORE LABOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '330', 'CURRENT_SALARY' => '8705'),
            331 => array('PID' => '19J5302L', 'NAME' => 'MD. KHADIMUL ISLAM', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '11225'),
            332 => array('PID' => '19J5303L', 'NAME' => 'AHAD MOLLAH', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '11225'),
            333 => array('PID' => '19K1212E', 'NAME' => 'MST. BITHI KHATUN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10000'),
            334 => array('PID' => '19L0228C', 'NAME' => 'ABU SAYED', 'DESIG' => 'OFFICER Q.C', 'L_INCR_DT' => '2020-01-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '25000'),
            335 => array('PID' => '19M2563F', 'NAME' => 'MD. MONIRUZZAMAN', 'DESIG' => 'SEWING IRON MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '335', 'CURRENT_SALARY' => '8835'),
            336 => array('PID' => '19M1021E', 'NAME' => 'MST. JOSNI AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9000'),
            337 => array('PID' => '19M1178E', 'NAME' => 'MD. HARUN OR RASHID', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            338 => array('PID' => '19M1188E', 'NAME' => 'MD. RAJIB MIA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9400'),
            339 => array('PID' => '19J5712K', 'NAME' => 'MST. HAZERA BEGUM', 'DESIG' => 'CLEANER', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '8725'),
            340 => array('PID' => '19K1246E', 'NAME' => 'SUMI AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '398', 'CURRENT_SALARY' => '10198'),
            341 => array('PID' => '19L1323E', 'NAME' => 'MST. RINA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            342 => array('PID' => '19L1382E', 'NAME' => 'MST. AKHI AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '370', 'CURRENT_SALARY' => '9570'),
            343 => array('PID' => '19L1383E', 'NAME' => 'MD. OMOR FARUK', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9000'),
            344 => array('PID' => '19L1385E', 'NAME' => 'MD. HASAN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10200'),
            345 => array('PID' => '19L1391E', 'NAME' => 'MST. CHAMPA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '9100'),
            346 => array('PID' => '19L4001H', 'NAME' => 'MOJAMMEL HOQUE', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '375', 'CURRENT_SALARY' => '9722'),
            347 => array('PID' => '19L1396E', 'NAME' => 'SADDAM HOSSAIN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9800'),
            348 => array('PID' => '19L4716J', 'NAME' => 'MD. SELIM HOSSAIN', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            349 => array('PID' => '19L1450E', 'NAME' => 'MST. SULTANA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9100'),
            350 => array('PID' => '19L1488E', 'NAME' => 'MST. TANJINA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '8820'),
            351 => array('PID' => '19M3709G', 'NAME' => 'TAHMINA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '8420'),
            352 => array('PID' => '19M5705K', 'NAME' => 'MD. HIRA MIA', 'DESIG' => 'CLEANER', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '335', 'CURRENT_SALARY' => '8835'),
            353 => array('PID' => '19M1217E', 'NAME' => 'TANZILA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            354 => array('PID' => '20B5710K', 'NAME' => 'JAKIYA SHIKDER', 'DESIG' => 'BABY SITTER', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '11225'),
            355 => array('PID' => '19J4696J', 'NAME' => 'MD. SABBIR HOSSAIN', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '310', 'CURRENT_SALARY' => '8310'),
            356 => array('PID' => '19J5300L', 'NAME' => 'SREE SHAMOL SUTRADHAR', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '11225'),
            357 => array('PID' => '19L1409E', 'NAME' => 'MST. TAHMINA KHATUN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '550', 'CURRENT_SALARY' => '10050'),
            358 => array('PID' => '19L1456E', 'NAME' => 'MD. JAMAN MIA', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '10550'),
            359 => array('PID' => '19M4513Z', 'NAME' => 'TORONI KANTO RAI', 'DESIG' => 'STORE LABOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '8775'),
            360 => array('PID' => '19M1147E', 'NAME' => 'MST. MONOWARA BEGUM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '380', 'CURRENT_SALARY' => '9780'),
            361 => array('PID' => '19M2506F', 'NAME' => 'MD. ESMAEL HOSSAN', 'DESIG' => 'SEWING ASSISTANT', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '325', 'CURRENT_SALARY' => '8625'),
            362 => array('PID' => '19K1001E', 'NAME' => 'MST. REJENA BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9500'),
            363 => array('PID' => '19K1024E', 'NAME' => 'SHEFALY', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '375', 'CURRENT_SALARY' => '9675'),
            364 => array('PID' => '19K1051E', 'NAME' => 'SABINA AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '360', 'CURRENT_SALARY' => '9360'),
            365 => array('PID' => '19K1093E', 'NAME' => 'MST. MORSHEDA KHATUN', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10245'),
            366 => array('PID' => '19K1105E', 'NAME' => 'NAZMA BEGUM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '383', 'CURRENT_SALARY' => '9883'),
            367 => array('PID' => '19K6289G', 'NAME' => 'MAZEDA', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '308', 'CURRENT_SALARY' => '8308'),
            368 => array('PID' => '19K6294G', 'NAME' => 'PRIYA DATTO', 'DESIG' => 'ORDINARY TAGMAN', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '342', 'CURRENT_SALARY' => '8650'),
            369 => array('PID' => '19L1425E', 'NAME' => 'HABIZA BEGUM', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10000'),
            370 => array('PID' => '19L1426E', 'NAME' => 'MST. MORZINA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '8900'),
            371 => array('PID' => '19L1431E', 'NAME' => 'MD. RAJIB HOSSAIN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9900'),
            372 => array('PID' => '19L1436E', 'NAME' => 'MST. MONIRA KHATUN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            373 => array('PID' => '19L1439E', 'NAME' => 'MST. ASMA BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '365', 'CURRENT_SALARY' => '9465'),
            374 => array('PID' => '19L0655W', 'NAME' => 'MD. FIROZ SHEKH', 'DESIG' => 'SR. SAMPLEMAN', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '620', 'CURRENT_SALARY' => '14620'),
            375 => array('PID' => '19L1476E', 'NAME' => 'MD. SHAHAB UDDIN', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9800'),
            376 => array('PID' => '19L1477E', 'NAME' => 'LIPI AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9900'),
            377 => array('PID' => '19L1483E', 'NAME' => 'NAHAR', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-11-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10500'),
            378 => array('PID' => '19M1025E', 'NAME' => 'LIPI', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '410', 'CURRENT_SALARY' => '10410'),
            379 => array('PID' => '19M1126E', 'NAME' => 'SHIMA RANI DEBNATH', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '340', 'CURRENT_SALARY' => '8840'),
            380 => array('PID' => '19K1377E', 'NAME' => 'AKLIMA KHATUN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '352', 'CURRENT_SALARY' => '9227'),
            381 => array('PID' => '19K5304L', 'NAME' => 'MD. SHAHIN ALAM', 'DESIG' => 'SECURITY GUARD', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '450', 'CURRENT_SALARY' => '11225'),
            382 => array('PID' => '19M4036H', 'NAME' => 'MD. JOSIM UDDIN', 'DESIG' => 'QUALITY INSPECTOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '700', 'CURRENT_SALARY' => '10200'),
            383 => array('PID' => '19M1010E', 'NAME' => 'MST. SUMI AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '370', 'CURRENT_SALARY' => '9570'),
            384 => array('PID' => '19M1018E', 'NAME' => 'CHAMPA BEGUM', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10400'),
            385 => array('PID' => '19M1068E', 'NAME' => 'MD. DELOWER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            386 => array('PID' => '19M1101E', 'NAME' => 'MD. MOJAMMEL HOQUE', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9900'),
            387 => array('PID' => '19M1132E', 'NAME' => 'MST. PARVIN AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10400'),
            388 => array('PID' => '19M1166E', 'NAME' => 'ANOUR HOSSEN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '650', 'CURRENT_SALARY' => '9850'),
            389 => array('PID' => '19M1205E', 'NAME' => 'MST. RINA BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9600'),
            390 => array('PID' => '19M3637F', 'NAME' => 'MD. SHOHEL', 'DESIG' => 'SEWING IRON MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '8850'),
            391 => array('PID' => '18D2094E', 'NAME' => 'SHAHNAZ', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10230'),
            392 => array('PID' => '19J1201E', 'NAME' => 'MURSHIDA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10000'),
            393 => array('PID' => '19J1242E', 'NAME' => 'SABINA', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '360', 'CURRENT_SALARY' => '9360'),
            394 => array('PID' => '19J1249E', 'NAME' => 'JHORNA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100'),
            395 => array('PID' => '19J6544G', 'NAME' => 'RAKIB MUNSHI', 'DESIG' => 'JUNIOR IRONMAN FINISHING', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '365', 'CURRENT_SALARY' => '9465'),
            396 => array('PID' => '19J6558G', 'NAME' => 'MD. ABU BOKOR SIDDIK', 'DESIG' => 'PACKER', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '380', 'CURRENT_SALARY' => '9780'),
            397 => array('PID' => '17L6283G', 'NAME' => 'DELOWER RAHMAN', 'DESIG' => 'SPOT MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '430', 'CURRENT_SALARY' => '10777'),
            398 => array('PID' => '19J1315E', 'NAME' => 'MD. AMANOT', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-09-01', 'L_INCR_AMT' => '370', 'CURRENT_SALARY' => '9570'),
            399 => array('PID' => '19K6312G', 'NAME' => 'LUCKY AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '350', 'CURRENT_SALARY' => '8658'),
            400 => array('PID' => '19K6439G', 'NAME' => 'SAINA', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '308', 'CURRENT_SALARY' => '8308'),
            401 => array('PID' => '19K3247F', 'NAME' => 'NASRIN AKHTER', 'DESIG' => 'ASSISTANT', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '308', 'CURRENT_SALARY' => '8308'),
            402 => array('PID' => '19K1296E', 'NAME' => 'NIJAM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '368', 'CURRENT_SALARY' => '9568'),
            403 => array('PID' => '19K1308E', 'NAME' => 'MD. SHAGOR', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '388', 'CURRENT_SALARY' => '9988'),
            404 => array('PID' => '19K4712J', 'NAME' => 'ABDUR RAHMAN', 'DESIG' => 'ORD. FUSING MACHINE OPERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '380', 'CURRENT_SALARY' => '8800'),
            405 => array('PID' => '18B1955E', 'NAME' => 'SABIA YASMIN SOBI', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9980'),
            406 => array('PID' => '18B4856J', 'NAME' => 'MD.NAZRUL ISLAM', 'DESIG' => 'JUNIUR OFFICER CUTTING', 'L_INCR_DT' => '2020-02-01', 'L_INCR_AMT' => '2000', 'CURRENT_SALARY' => '21000'),
            407 => array('PID' => '18C1525E', 'NAME' => 'RUBEL', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10600'),
            408 => array('PID' => '19A3668F', 'NAME' => 'SHILPI', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '340', 'CURRENT_SALARY' => '8650'),
            409 => array('PID' => '19A1364E', 'NAME' => 'JESMIN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9300'),
            410 => array('PID' => '18K0288C', 'NAME' => 'MD. MOHSIN ALAM', 'DESIG' => 'ASST MANAGER PRODUCTION', 'L_INCR_DT' => '2019-10-01', 'L_INCR_AMT' => '5000', 'CURRENT_SALARY' => '40000'),
            411 => array('PID' => '20G1046E', 'NAME' => 'MD. AL AMIN MONDOL', 'DESIG' => 'JUNIOR QUALITY INSPECTOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '700', 'CURRENT_SALARY' => '9200'),
            412 => array('PID' => '20G2546F', 'NAME' => 'ALINUR', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            413 => array('PID' => '20G2710F', 'NAME' => 'MST. MUNNI KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            414 => array('PID' => '20H1244E', 'NAME' => 'RUBEL HOSSAIN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2020-10-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9700'),
            415 => array('PID' => '20H1255E', 'NAME' => 'ISMATARA BEGUM', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9000'),
            416 => array('PID' => '20G3586F', 'NAME' => 'MD.MAMUN HAWLADER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            417 => array('PID' => '20G3642F', 'NAME' => 'MISS. TANIA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            418 => array('PID' => '20H1340E', 'NAME' => 'ASMA AKTER', 'DESIG' => 'SR SEWING MACHINE OPRATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10100'),
            419 => array('PID' => '20G3598F', 'NAME' => 'MST. SHAGORIKA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            420 => array('PID' => '20J4012J', 'NAME' => 'MD. SHOHAG HOSSAIN', 'DESIG' => 'JR. INPUTMAN', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9000'),
            421 => array('PID' => '20G2687F', 'NAME' => 'BITHI AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            422 => array('PID' => '20G2855F', 'NAME' => 'MST. RUPALI KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            423 => array('PID' => '20H1329E', 'NAME' => 'KHURSHIDA BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '300', 'CURRENT_SALARY' => '9200'),
            424 => array('PID' => '16L6159G', 'NAME' => 'MD. BACHCHU MIA', 'DESIG' => 'IRON MAN', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10247'),
            425 => array('PID' => '20M1232E', 'NAME' => 'RUMA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10000'),
            426 => array('PID' => '20M1570E', 'NAME' => 'MD. REZA HOSSAIN', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9000'),
            427 => array('PID' => '20K1462E', 'NAME' => 'MD. ZAHID', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9700'),
            428 => array('PID' => '20L3750F', 'NAME' => 'MST. POLY KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            429 => array('PID' => '20L3716G', 'NAME' => 'MST. MASUDA KHATUN', 'DESIG' => 'ORDINARY TAGMAN', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            430 => array('PID' => '20L3832F', 'NAME' => 'SHOMA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            431 => array('PID' => '20M2633F', 'NAME' => 'ROSHEDA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            432 => array('PID' => '20M1597E', 'NAME' => 'MST. MIRA BEGUM', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9000'),
            433 => array('PID' => '20M1609E', 'NAME' => 'SREEMOTI MALOTI', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '9500'),
            434 => array('PID' => '20M1610E', 'NAME' => 'MST. JINIYA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '300', 'CURRENT_SALARY' => '9600'),
            435 => array('PID' => '20M2711F', 'NAME' => 'SREE MALA RANI', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            436 => array('PID' => '20M2720F', 'NAME' => 'KULSUMA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            437 => array('PID' => '20M1603E', 'NAME' => 'MST. SHUBORNA', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '580', 'CURRENT_SALARY' => '9000'),
            438 => array('PID' => '20M2846F', 'NAME' => 'MST. TANIA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            439 => array('PID' => '20M2866F', 'NAME' => 'MST. SHARMIN KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-01-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            440 => array('PID' => '20M1554E', 'NAME' => 'RIPA AKTER', 'DESIG' => 'JR SEWING MACHINE OPRERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '9700'),
            441 => array('PID' => '20L1019E', 'NAME' => 'SREEMOTI ANJALI', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '300', 'CURRENT_SALARY' => '10000'),
            442 => array('PID' => '20L3821F', 'NAME' => 'MISS SRABONI AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            443 => array('PID' => '20M2925F', 'NAME' => 'MST. SUMAIYA BEGUM', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            444 => array('PID' => '21A3178F', 'NAME' => 'MST. ROJINA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            445 => array('PID' => '18B2235E', 'NAME' => 'MAHFUJA', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '10500'),
            446 => array('PID' => '18A2449E', 'NAME' => 'MST. DINA AKTER', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2020-12-01', 'L_INCR_AMT' => '600', 'CURRENT_SALARY' => '10400'),
            447 => array('PID' => '21A3001F', 'NAME' => 'SOMELA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            448 => array('PID' => '21A3094F', 'NAME' => 'RUNA KHATUN', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            449 => array('PID' => '21A3061F', 'NAME' => 'JUMA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            450 => array('PID' => '21A3113F', 'NAME' => 'MOSAMMOT SURAIYA AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '420', 'CURRENT_SALARY' => '8420'),
            451 => array('PID' => '21A3051F', 'NAME' => 'JESMIN AKTER', 'DESIG' => 'ORD SEWING MACH OPERATOR', 'L_INCR_DT' => '2021-02-01', 'L_INCR_AMT' => '500', 'CURRENT_SALARY' => '8500'),
            452 => array('PID' => '21A1699E', 'NAME' => 'MD. OMAR FARUK', 'DESIG' => 'SEWING MACHINE OPERATOR', 'L_INCR_DT' => '2021-03-01', 'L_INCR_AMT' => '400', 'CURRENT_SALARY' => '10100')
        );
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('as_oracle_code', 'associate_id', 'as_status', 'as_doj', 'as_name')
        ->whereIn('b.as_unit_id', [3])
        ->whereIn('b.as_location', [9])
        ->get();

        // $getIncrement = DB::table('hr_increment')
        // ->get()
        // ->keyBy('associate_id')
        // ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getData as $key1 => $value) {
                if($info->as_oracle_code == $value['PID']){
                    $getIncrement = DB::table('hr_increment')->where('associate_id', $info->associate_id)->where('effective_date', date('Y-m-d', strtotime($value['L_INCR_DT'])))->first();
                    // ++$count;
                    if($getIncrement != null){
                            // $macth[$info->associate_id] = $value;
                            ++$count;
                        
                        $macth[] = DB::table('hr_increment')
                        ->where('id', $getIncrement->id)
                        ->update([
                            'associate_id' => $info->associate_id,
                            'current_salary' => ($value['CURRENT_SALARY'] - $value['L_INCR_AMT']),
                            'increment_type' => 2,
                            'increment_amount' => $value['L_INCR_AMT'],
                            'amount_type' => 1,
                            'applied_date' => date('Y-m-d', strtotime($value['L_INCR_DT'])),
                            'eligible_date' => date('Y-m-d', strtotime($value['L_INCR_DT'])),
                            'effective_date' => date('Y-m-d', strtotime($value['L_INCR_DT'])),
                            'status' => 1,
                        ]);

                    }
                }
            }
        }

        // return $count;
        return count($macth);
    }
    public function benefitUpdate()
    {
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('b.as_oracle_code', 'b.associate_id', 'b.as_status', 'b.as_doj', 'b.as_name', 'b.as_unit_id', 'a.ben_current_salary')
        // ->whereIn('b.as_unit_id', [8])
        ->leftJoin('hr_benefits AS a', function($q){
            $q->on('a.ben_as_id', 'b.associate_id');
        })
        ->where('as_status', '!=', 0)
        ->get();
        // return $getBasic;
        $getIncrement = DB::table('hr_increment')
        ->get()
        ->keyBy('associate_id')
        ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getIncrement as $key => $value) {
                if($info->associate_id == $value->associate_id && (($value->current_salary+$value->increment_amount) > $info->ben_current_salary)){

                    $value->ben_current_salary = $info->ben_current_salary;
                    $value->as_unit_id = $info->as_unit_id;
                    $macth[] = $value;

                }
            }
        }

        $tomacth = [];
        return $macth;
        foreach ($macth as $key1 => $val) {
            $ben = DB::table('hr_benefits as b')
                            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                            ->where('a.associate_id', $val->associate_id)
                            ->first();
            if($ben != null){
                $up['ben_current_salary'] = ($val->current_salary + $val->increment_amount);
                $up['ben_basic'] = ceil(($up['ben_current_salary']-1850)/1.5);
                $up['ben_house_rent'] = $up['ben_current_salary'] -1850 - $up['ben_basic'];

                if($ben->ben_bank_amount > 0){
                    $up['ben_bank_amount'] = $up['ben_current_salary'];
                    $up['ben_cash_amount'] = 0;
                }else{
                    $up['ben_cash_amount'] = $up['ben_current_salary'];
                    $up['ben_bank_amount'] = 0;
                }
                $tomacth[] = $up;
                //$exist[$key1] = DB::table('hr_benefits')->where('ben_id', $ben->ben_id)->update($up);
            }
        }
        return ($exist);
    }
    
    public function incrementMarge()
    {
        $getIncrement = DB::table('hr_increment')
        ->select('associate_id', 'increment_type', 'applied_date', 'eligible_date', DB::raw('COUNT(*) AS count'))
        ->groupBy(['associate_id', 'increment_type', 'applied_date', 'eligible_date'])
        ->having('count', '>', 1)
        ->get();
        $increment = [];
        foreach ($getIncrement as $key => $value) {
            $increment[] = DB::table('hr_increment')
            ->select('associate_id', 'applied_date', DB::raw('sum(increment_amount) as amount'), DB::raw('MAX(id) AS maxid'), DB::raw('MIN(id) AS minid'))
            ->where('associate_id', $value->associate_id)
            ->where('applied_date', $value->applied_date)
            ->groupBy('associate_id')
            ->first();
        }

        foreach ($increment as $key1 => $va) {
            DB::table("hr_increment")
            ->where('associate_id', $va->associate_id)
            ->where('id', $va->maxid)
            ->update([
                'increment_amount' => $va->amount
            ]);

            DB::table('hr_increment')
            ->where('id', $va->minid)
            ->delete();
        }
        return 'success';
    }


    public function incrementHistory()
    {
        $getData = array(
            0 => array(
                'PID' => '15K1096E',
                'NAME' => 'AFROJA AKTER',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '10/17/2015',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '479',
                'CURRENT_SALARY' => '10850'
            ),
            1 => array(
                'PID' => '98K4518J',
                'NAME' => 'MAHMUDUL HAQUE',
                'DESIGNATION' => 'OFFICER-CUTTING',
                'DOJ' => '10/11/1998',
                'LAST_INCRIMENT_DATE' => '10/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '3531',
                'CURRENT_SALARY' => '19950'
            ),
            2 => array(
                'PID' => '17G4142H',
                'NAME' => 'ARUN KUMAR SINGHA',
                'DESIGNATION' => 'JUNIOR OFFICER Q.C.',
                'DOJ' => '7/11/2017',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '4153',
                'CURRENT_SALARY' => '14000'
            ),
            3 => array(
                'PID' => '17H0426C',
                'NAME' => 'TAOHEDUL ISLAM',
                'DESIGNATION' => 'OFFICER MARKER MAN',
                'DOJ' => '8/7/2017',
                'LAST_INCRIMENT_DATE' => '1/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '4000',
                'CURRENT_SALARY' => '22500'
            ),
            4 => array(
                'PID' => '15K6287G',
                'NAME' => 'SAIFUL',
                'DESIGNATION' => 'SUPERVISOR',
                'DOJ' => '10/18/2015',
                'LAST_INCRIMENT_DATE' => '10/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '18500'
            ),
            5 => array(
                'PID' => '18D4371H',
                'NAME' => 'MEHEDI',
                'DESIGNATION' => 'GPQ',
                'DOJ' => '4/10/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '7000',
                'CURRENT_SALARY' => '17000'
            ),
            6 => array(
                'PID' => '17J0728D',
                'NAME' => 'MOHAMMAD HANIF',
                'DESIGNATION' => 'MANAGER MERCHANDISING',
                'DOJ' => '9/16/2017',
                'LAST_INCRIMENT_DATE' => '11/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '11000',
                'CURRENT_SALARY' => '90000'
            ),
            7 => array(
                'PID' => '14K2187E',
                'NAME' => 'HUNOFA',
                'DESIGNATION' => 'LINE MANAGER',
                'DOJ' => '10/14/2014',
                'LAST_INCRIMENT_DATE' => '3/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '7000',
                'CURRENT_SALARY' => '25000'
            ),
            8 => array(
                'PID' => '18A6457G',
                'NAME' => 'MST. BEAUTI BEGUM',
                'DESIGNATION' => 'FOLDING MAN',
                'DOJ' => '1/13/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            9 => array(
                'PID' => '18C0695W',
                'NAME' => 'MANIK HASAN',
                'DESIGNATION' => 'SR. SAMPLEMAN',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '650',
                'CURRENT_SALARY' => '12350'
            ),
            10 => array(
                'PID' => '16F0386C',
                'NAME' => 'MD.BILLAL HOSSA',
                'DESIGNATION' => 'ASSISTANT MANAGER TECHNIC',
                'DOJ' => '6/1/2016',
                'LAST_INCRIMENT_DATE' => '11/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '6000',
                'CURRENT_SALARY' => '41000'
            ),
            11 => array(
                'PID' => '15M6534Z',
                'NAME' => 'MD.SHAMIM SHEIKH',
                'DESIGNATION' => 'COOKER',
                'DOJ' => '12/10/2015',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '7200',
                'CURRENT_SALARY' => '17500'
            ),
            12 => array(
                'PID' => '16E5492Q',
                'NAME' => 'MD.SHAFIQUL ISLAM',
                'DESIGNATION' => 'SENIOR OFFICER STORE',
                'DOJ' => '5/26/2016',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '10000',
                'CURRENT_SALARY' => '25000'
            ),
            13 => array(
                'PID' => '18C3655F',
                'NAME' => 'FORHAD',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '8450',
                'CURRENT_SALARY' => '18000'
            ),
            14 => array(
                'PID' => '18C1081E',
                'NAME' => 'MONIR',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10500'
            ),
            15 => array(
                'PID' => '18C3963F',
                'NAME' => 'HAWA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '1000',
                'CURRENT_SALARY' => '9400'
            ),
            16 => array(
                'PID' => '18D3477F',
                'NAME' => 'SUFIA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '4/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9095'
            ),
            17 => array(
                'PID' => '12J3926F',
                'NAME' => 'MD. JAKIR HOSSE',
                'DESIGNATION' => 'PATERN ASSISTANT',
                'DOJ' => '9/16/2012',
                'LAST_INCRIMENT_DATE' => '9/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '7825',
                'CURRENT_SALARY' => '18300'
            ),
            18 => array(
                'PID' => '93K0235C',
                'NAME' => 'KAMRUL ISLAM',
                'DESIGNATION' => 'SR. MANAGER-Q.M.P.',
                'DOJ' => '10/9/1993',
                'LAST_INCRIMENT_DATE' => '10/1/2017',
                'LAST_INCRIMENT_AMOUNT' => '15000',
                'CURRENT_SALARY' => '112000'
            ),
            19 => array(
                'PID' => '18D3704F',
                'NAME' => 'SHORNALI KHANOM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '4/2/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9350'
            ),
            20 => array(
                'PID' => '17L0220C',
                'NAME' => 'MD. JUWEL RANA',
                'DESIGNATION' => 'ASST MANAGER QUALITY CONTROL',
                'DOJ' => '11/12/2017',
                'LAST_INCRIMENT_DATE' => '11/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '4000',
                'CURRENT_SALARY' => '35000'
            ),
            21 => array(
                'PID' => '18C1494E',
                'NAME' => 'SHAHNAZ',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/12/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10200'
            ),
            22 => array(
                'PID' => '18D2062E',
                'NAME' => 'KAKOLI',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '4/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10200'
            ),
            23 => array(
                'PID' => '05L1223E',
                'NAME' => 'RIPON',
                'DESIGNATION' => 'SR. SAMPLEMAN',
                'DOJ' => '11/21/2005',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '1605',
                'CURRENT_SALARY' => '13820'
            ),
            24 => array(
                'PID' => '15C3027R',
                'NAME' => 'MD. ALLAMA IKBAL',
                'DESIGNATION' => 'MERCHANDISER',
                'DOJ' => '3/12/2015',
                'LAST_INCRIMENT_DATE' => '3/30/2019',
                'LAST_INCRIMENT_AMOUNT' => '6000',
                'CURRENT_SALARY' => '33000'
            ),
            25 => array(
                'PID' => '18C3656F',
                'NAME' => 'AYSHA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '3/3/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9100'
            ),
            26 => array(
                'PID' => '15M1831E',
                'NAME' => 'RINA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/1/2015',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '464',
                'CURRENT_SALARY' => '10660'
            ),
            27 => array(
                'PID' => '15L0382C',
                'NAME' => 'JAHANGIR ALAM',
                'DESIGNATION' => 'SR. OFFICER QMS',
                'DOJ' => '11/3/2015',
                'LAST_INCRIMENT_DATE' => '12/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '4000',
                'CURRENT_SALARY' => '24000'
            ),
            28 => array(
                'PID' => '17A4489H',
                'NAME' => 'SUMAN SARKER',
                'DESIGNATION' => 'GPQ',
                'DOJ' => '1/2/2017',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '3500',
                'CURRENT_SALARY' => '24000'
            ),
            29 => array(
                'PID' => '18D0105B',
                'NAME' => 'MD. RASHED KHAN',
                'DESIGNATION' => 'SR. OFFICER HR & ADMIN',
                'DOJ' => '4/1/2018',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '7000',
                'CURRENT_SALARY' => '27000'
            ),
            30 => array(
                'PID' => '12M0107B',
                'NAME' => 'MD. NAZMUL HOSSAIN',
                'DESIGNATION' => 'SENIOR OFFICER COMPLIANCE',
                'DOJ' => '12/13/2012',
                'LAST_INCRIMENT_DATE' => '12/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '5000',
                'CURRENT_SALARY' => '29000'
            ),
            31 => array(
                'PID' => '18C1508E',
                'NAME' => 'TASLIMA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '3/14/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9700'
            ),
            32 => array(
                'PID' => '15E4700J',
                'NAME' => 'BELLAL HOSSAIN',
                'DESIGNATION' => 'JUNIUR OFFICER CUTTING',
                'DOJ' => '5/3/2015',
                'LAST_INCRIMENT_DATE' => '5/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '3000',
                'CURRENT_SALARY' => '20008'
            ),
            33 => array(
                'PID' => '18C1241E',
                'NAME' => 'BONNA',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '3/6/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10600'
            ),
            34 => array(
                'PID' => '10F4762J',
                'NAME' => 'HUMAYUN KABIR',
                'DESIGNATION' => 'ASST MANAGER CUTTING',
                'DOJ' => '6/10/2010',
                'LAST_INCRIMENT_DATE' => '10/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '4000',
                'CURRENT_SALARY' => '31000'
            ),
            35 => array(
                'PID' => '14E1701E',
                'NAME' => 'RINA',
                'DESIGNATION' => 'LINE LEADER',
                'DOJ' => '5/13/2014',
                'LAST_INCRIMENT_DATE' => '3/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '6430',
                'CURRENT_SALARY' => '21500'
            ),
            36 => array(
                'PID' => '14H2136E',
                'NAME' => 'NARGIS',
                'DESIGNATION' => 'LINE MANAGER',
                'DOJ' => '8/7/2014',
                'LAST_INCRIMENT_DATE' => '3/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '9000',
                'CURRENT_SALARY' => '25000'
            ),
            37 => array(
                'PID' => '16K5295M',
                'NAME' => 'MD.MASUD',
                'DESIGNATION' => 'OFFICER MECHANIC',
                'DOJ' => '10/1/2016',
                'LAST_INCRIMENT_DATE' => '10/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '2500',
                'CURRENT_SALARY' => '26500'
            ),
            38 => array(
                'PID' => '18D4578J',
                'NAME' => 'MOMIN MIAH',
                'DESIGNATION' => 'ORDINARY LAY-MAN',
                'DOJ' => '4/21/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9420'
            ),
            39 => array(
                'PID' => '11D7185U',
                'NAME' => 'MD. AMINUL ISLAM',
                'DESIGNATION' => 'ASST GENERAL MANAGER',
                'DOJ' => '4/2/2011',
                'LAST_INCRIMENT_DATE' => '4/30/2019',
                'LAST_INCRIMENT_AMOUNT' => '25000',
                'CURRENT_SALARY' => '135000'
            ),
            40 => array(
                'PID' => '16K1063E',
                'NAME' => 'MST.NARGIS',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '10/17/2016',
                'LAST_INCRIMENT_DATE' => '10/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '6990',
                'CURRENT_SALARY' => '17500'
            ),
            41 => array(
                'PID' => '18C1304E',
                'NAME' => 'JAHANGIR ALOM',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '3/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10750'
            ),
            42 => array(
                'PID' => '13C4776Z',
                'NAME' => 'YAKUB ALI',
                'DESIGNATION' => 'JUNIUR OFFICER CUTTING',
                'DOJ' => '3/9/2013',
                'LAST_INCRIMENT_DATE' => '3/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '6730',
                'CURRENT_SALARY' => '16500'
            ),
            43 => array(
                'PID' => '18C1306E',
                'NAME' => 'MOZIRON',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10130'
            ),
            44 => array(
                'PID' => '16M3899F',
                'NAME' => 'SADIYA AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/10/2016',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '1000',
                'CURRENT_SALARY' => '9600'
            ),
            45 => array(
                'PID' => '18C1307E',
                'NAME' => 'AKLIMA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '3/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '375',
                'CURRENT_SALARY' => '9655'
            ),
            46 => array(
                'PID' => '17A4528J',
                'NAME' => 'MD. RUBEL',
                'DESIGNATION' => 'JUNIUR OFFICER CUTTING',
                'DOJ' => '1/10/2017',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '6500',
                'CURRENT_SALARY' => '19000'
            ),
            47 => array(
                'PID' => '18C6185G',
                'NAME' => 'MUKTA RANI',
                'DESIGNATION' => 'FOLDING MAN',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            48 => array(
                'PID' => '18C2560F',
                'NAME' => 'LIPI',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9100'
            ),
            49 => array(
                'PID' => '18C3143R',
                'NAME' => 'RAHUL CHANDRA DAS',
                'DESIGNATION' => 'ASSISTANT MERCHANDISER',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '3/2/2019',
                'LAST_INCRIMENT_AMOUNT' => '5000',
                'CURRENT_SALARY' => '20000'
            ),
            50 => array(
                'PID' => '15L6364Z',
                'NAME' => 'MOKTAR',
                'DESIGNATION' => 'INPUTMAN',
                'DOJ' => '11/5/2015',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '506',
                'CURRENT_SALARY' => '10490'
            ),
            51 => array(
                'PID' => '18D3766F',
                'NAME' => 'ABU RAIHAN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '4/23/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '9800'
            ),
            52 => array(
                'PID' => '18D0313C',
                'NAME' => 'MD. EMAN ALI',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '4/23/2018',
                'LAST_INCRIMENT_DATE' => '3/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '3000',
                'CURRENT_SALARY' => '18000'
            ),
            53 => array(
                'PID' => '18A1775E',
                'NAME' => 'DOLI BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '1/10/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '9500'
            ),
            54 => array(
                'PID' => '18A2732F',
                'NAME' => 'LUTIFA BANU',
                'DESIGNATION' => 'SEWING IRON MAN',
                'DOJ' => '1/10/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9200'
            ),
            55 => array(
                'PID' => '18A2465E',
                'NAME' => 'FATEMA KHATUN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '1/10/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10000'
            ),
            56 => array(
                'PID' => '12H6069G',
                'NAME' => 'MST. FUL MALA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '8/26/2012',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '10000'
            ),
            57 => array(
                'PID' => '17M5203M',
                'NAME' => 'SHAJIBUR RAHMAN',
                'DESIGNATION' => 'OFFICER ELECTRICIAN',
                'DOJ' => '12/2/2017',
                'LAST_INCRIMENT_DATE' => '12/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '2500',
                'CURRENT_SALARY' => '30000'
            ),
            58 => array(
                'PID' => '16J1574E',
                'NAME' => 'AMARI AKTER',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '9/20/2016',
                'LAST_INCRIMENT_DATE' => '3/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '3000',
                'CURRENT_SALARY' => '18000'
            ),
            59 => array(
                'PID' => '18C1375E',
                'NAME' => 'KOHINUR',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '3/10/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '475',
                'CURRENT_SALARY' => '9750'
            ),
            60 => array(
                'PID' => '18D4311H',
                'NAME' => 'HALIMA',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '4/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            61 => array(
                'PID' => '18A2157E',
                'NAME' => 'LAILY KHATUN',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '1/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '555',
                'CURRENT_SALARY' => '10850'
            ),
            62 => array(
                'PID' => '18A2265E',
                'NAME' => 'FIROZA KHATUN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '1/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '370',
                'CURRENT_SALARY' => '9600'
            ),
            63 => array(
                'PID' => '18A2268E',
                'NAME' => 'MD. SHOFIZOL HA',
                'DESIGNATION' => 'SR. SAMPLEMAN',
                'DOJ' => '1/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '650',
                'CURRENT_SALARY' => '12350'
            ),
            64 => array(
                'PID' => '18A2337E',
                'NAME' => 'MD. KANCHON MIA',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '1/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '650',
                'CURRENT_SALARY' => '10600'
            ),
            65 => array(
                'PID' => '17D0419C',
                'NAME' => 'MD.HABIBUR RAHMAN',
                'DESIGNATION' => 'MANAGER IE & PLANING',
                'DOJ' => '4/19/2017',
                'LAST_INCRIMENT_DATE' => '1/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '7000',
                'CURRENT_SALARY' => '48000'
            ),
            66 => array(
                'PID' => '18A0252C',
                'NAME' => 'MAJIBAR RAHMAN',
                'DESIGNATION' => 'ASST MANAGER IE',
                'DOJ' => '1/11/2018',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '7000',
                'CURRENT_SALARY' => '37000'
            ),
            67 => array(
                'PID' => '18A0732D',
                'NAME' => 'MIRZA SADIKUR RAHAMAN',
                'DESIGNATION' => 'OFFICER ACCOUNTS',
                'DOJ' => '1/23/2018',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '7500',
                'CURRENT_SALARY' => '23500'
            ),
            68 => array(
                'PID' => '18A4723J',
                'NAME' => 'ARIF HOSSAIN',
                'DESIGNATION' => 'INPUTMAN',
                'DOJ' => '10/12/2017',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '700',
                'CURRENT_SALARY' => '10247'
            ),
            69 => array(
                'PID' => '18A2458E',
                'NAME' => 'NAZMA BEGUM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '1/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '553',
                'CURRENT_SALARY' => '10400'
            ),
            70 => array(
                'PID' => '18A5621K',
                'NAME' => 'MAYA RANI CHOKROBORTI',
                'DESIGNATION' => 'CLEANER',
                'DOJ' => '1/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '525',
                'CURRENT_SALARY' => '9400'
            ),
            71 => array(
                'PID' => '18B4725J',
                'NAME' => 'MD. RIPON HOSSEN',
                'DESIGNATION' => 'JUNIUR OFFICER CUTTING',
                'DOJ' => '2/5/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '5580',
                'CURRENT_SALARY' => '14500'
            ),
            72 => array(
                'PID' => '18B1291E',
                'NAME' => 'MALEKA KHATUN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '2/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '657',
                'CURRENT_SALARY' => '10500'
            ),
            73 => array(
                'PID' => '18B1300E',
                'NAME' => 'MD. SOHEL RANA MALTIA',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '2/3/2018',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '7600',
                'CURRENT_SALARY' => '18000'
            ),
            74 => array(
                'PID' => '18B3533F',
                'NAME' => 'NARGIS FATEMA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '2/12/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9100'
            ),
            75 => array(
                'PID' => '18B3543F',
                'NAME' => 'RIMA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '2/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9095'
            ),
            76 => array(
                'PID' => '18B4166H',
                'NAME' => 'BABLI AKTER',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '2/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            77 => array(
                'PID' => '18B4172H',
                'NAME' => 'HAFIZUL  ISLAM',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '2/6/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            78 => array(
                'PID' => '18B4173H',
                'NAME' => 'NASRUL ISLAM',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '2/5/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10410'
            ),
            79 => array(
                'PID' => '18B1438E',
                'NAME' => 'AKHI AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '2/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '553',
                'CURRENT_SALARY' => '10350'
            ),
            80 => array(
                'PID' => '18B1468E',
                'NAME' => 'SHANTY',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '2/7/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '553',
                'CURRENT_SALARY' => '10400'
            ),
            81 => array(
                'PID' => '18B3547F',
                'NAME' => 'BOKUL',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '2/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9100'
            ),
            82 => array(
                'PID' => '18B4064H',
                'NAME' => 'ABDUS SATTAR',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '2/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            83 => array(
                'PID' => '18B6549G',
                'NAME' => 'HELENA AKTER',
                'DESIGNATION' => 'SPOT MAN',
                'DOJ' => '2/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            84 => array(
                'PID' => '18B6668G',
                'NAME' => 'PARVEZ',
                'DESIGNATION' => 'SUPERVISOR',
                'DOJ' => '2/5/2018',
                'LAST_INCRIMENT_DATE' => '2/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '7500',
                'CURRENT_SALARY' => '20000'
            ),
            85 => array(
                'PID' => '18B0261C',
                'NAME' => 'MD.BABUL HOSSAIN',
                'DESIGNATION' => 'SENIOR MARKER MAN',
                'DOJ' => '2/12/2018',
                'LAST_INCRIMENT_DATE' => '1/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '5000',
                'CURRENT_SALARY' => '37000'
            ),
            86 => array(
                'PID' => '18A5322L',
                'NAME' => 'YEAKUB ALI MOLLA',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '1/2/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '11875'
            ),
            87 => array(
                'PID' => '18B1270E',
                'NAME' => 'MST. ROZINA KHATUN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '2/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '653',
                'CURRENT_SALARY' => '10600'
            ),
            88 => array(
                'PID' => '18B1673E',
                'NAME' => 'MAKSUDA',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '2/1/2018',
                'LAST_INCRIMENT_DATE' => '3/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '3000',
                'CURRENT_SALARY' => '18000'
            ),
            89 => array(
                'PID' => '18A5460Z',
                'NAME' => 'SHAJAHAN',
                'DESIGNATION' => 'STORE LABOR',
                'DOJ' => '1/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9275'
            ),
            90 => array(
                'PID' => '18A5466Z',
                'NAME' => 'MD.ABDUL KUDDUS',
                'DESIGNATION' => 'STORE LABOR',
                'DOJ' => '1/2/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9375'
            ),
            91 => array(
                'PID' => '18A5205M',
                'NAME' => 'MD.BABU',
                'DESIGNATION' => 'JR. OFFICER ELECTRICIAN',
                'DOJ' => '1/1/2018',
                'LAST_INCRIMENT_DATE' => '2/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '19000'
            ),
            92 => array(
                'PID' => '18A5207M',
                'NAME' => 'MD. ABU SALIM',
                'DESIGNATION' => 'JR. OFFICER MECHANIC',
                'DOJ' => '1/1/2018',
                'LAST_INCRIMENT_DATE' => '2/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '4000',
                'CURRENT_SALARY' => '25000'
            ),
            93 => array(
                'PID' => '18A1656E',
                'NAME' => 'MD. RUBEL MIA',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '1/1/2018',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '7300',
                'CURRENT_SALARY' => '21500'
            ),
            94 => array(
                'PID' => '18A1348E',
                'NAME' => 'MUNNI',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '1/10/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '378',
                'CURRENT_SALARY' => '9675'
            ),
            95 => array(
                'PID' => '18A1440E',
                'NAME' => 'MST.RAHIMA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '1/10/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10300'
            ),
            96 => array(
                'PID' => '17A0854U',
                'NAME' => 'MD. ASLAM HUSSAIN',
                'DESIGNATION' => 'MERCHANDISER',
                'DOJ' => '1/2/2017',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '10000',
                'CURRENT_SALARY' => '40000'
            ),
            97 => array(
                'PID' => '18E1029E',
                'NAME' => 'MONIR',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '5/3/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10800'
            ),
            98 => array(
                'PID' => '18E1032E',
                'NAME' => 'BIPLOB',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '5/3/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9200'
            ),
            99 => array(
                'PID' => '18E5217M',
                'NAME' => 'TUTUL CHANDRA BISHWAS',
                'DESIGNATION' => 'OFFICER ELECTRICIAN',
                'DOJ' => '5/17/2018',
                'LAST_INCRIMENT_DATE' => '5/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '23000'
            ),
            100 => array(
                'PID' => '18B4121H',
                'NAME' => 'MIRZA FORHAD',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '2/5/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            101 => array(
                'PID' => '18B3917F',
                'NAME' => 'MAKSUDA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '2/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9150'
            ),
            102 => array(
                'PID' => '18B4293H',
                'NAME' => 'ROFIQUL ISLAM',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '2/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            103 => array(
                'PID' => '18B6037G',
                'NAME' => 'MD. SHAKIL MIA',
                'DESIGNATION' => 'JUNIOR PACKER',
                'DOJ' => '2/6/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9875'
            ),
            104 => array(
                'PID' => '18B4865J',
                'NAME' => 'MD. IBRAHIM HOSSAIN',
                'DESIGNATION' => 'NIDDLE MAN',
                'DOJ' => '2/12/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            105 => array(
                'PID' => '18B1615E',
                'NAME' => 'KOLPONA BEGUM',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '2/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '455',
                'CURRENT_SALARY' => '10750'
            ),
            106 => array(
                'PID' => '18B6846G',
                'NAME' => 'MST. SALMA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '2/4/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '430',
                'CURRENT_SALARY' => '9250'
            ),
            107 => array(
                'PID' => '18C4212H',
                'NAME' => 'SHIRIN',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '3/24/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            108 => array(
                'PID' => '18C4562J',
                'NAME' => 'MD. AZIZUL ISLAM',
                'DESIGNATION' => 'SUPERVISOR',
                'DOJ' => '3/25/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '8080',
                'CURRENT_SALARY' => '17000'
            ),
            109 => array(
                'PID' => '18C4049H',
                'NAME' => 'MONIR HOSSAIN',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            110 => array(
                'PID' => '18C3621F',
                'NAME' => 'SHEFALI BEGUM',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9100'
            ),
            111 => array(
                'PID' => '18C2582F',
                'NAME' => 'KHADIZA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9100'
            ),
            112 => array(
                'PID' => '18C3137R',
                'NAME' => 'GOWTAM MITRA',
                'DESIGNATION' => 'ASST. FEBRIC TECHNICIAN',
                'DOJ' => '3/10/2018',
                'LAST_INCRIMENT_DATE' => '3/2/2019',
                'LAST_INCRIMENT_AMOUNT' => '4000',
                'CURRENT_SALARY' => '19000'
            ),
            113 => array(
                'PID' => '18D1423E',
                'NAME' => 'SHATHI',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '4/16/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9700'
            ),
            114 => array(
                'PID' => '18D5468Q',
                'NAME' => 'MD. SHARIFUL ISLAM',
                'DESIGNATION' => 'MESSENGER',
                'DOJ' => '4/16/2018',
                'LAST_INCRIMENT_DATE' => '4/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '3000',
                'CURRENT_SALARY' => '13000'
            ),
            115 => array(
                'PID' => '18C6134G',
                'NAME' => 'MD. MAMUN HOSSAIN',
                'DESIGNATION' => 'PACKER',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            116 => array(
                'PID' => '18C6135G',
                'NAME' => 'MD. ABDUR RAZZAK',
                'DESIGNATION' => 'PACKER',
                'DOJ' => '3/3/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            117 => array(
                'PID' => '18D6213G',
                'NAME' => 'MST. SHIRINA',
                'DESIGNATION' => 'POLY MAN',
                'DOJ' => '4/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            118 => array(
                'PID' => '18C1579E',
                'NAME' => 'SAZEDA',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '3/20/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '555',
                'CURRENT_SALARY' => '10850'
            ),
            119 => array(
                'PID' => '18C1632E',
                'NAME' => 'JORINA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '3/21/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '360',
                'CURRENT_SALARY' => '9412'
            ),
            120 => array(
                'PID' => '18C1692E',
                'NAME' => 'JOSNA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '3/22/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '380',
                'CURRENT_SALARY' => '9770'
            ),
            121 => array(
                'PID' => '18C3649F',
                'NAME' => 'MOMOTA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '3/4/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '1000',
                'CURRENT_SALARY' => '9200'
            ),
            122 => array(
                'PID' => '18C1889E',
                'NAME' => 'SHELPY AKTER',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '3/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '635',
                'CURRENT_SALARY' => '10700'
            ),
            123 => array(
                'PID' => '18C3654F',
                'NAME' => 'SHAIFUL',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/4/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10100'
            ),
            124 => array(
                'PID' => '18J1076E',
                'NAME' => 'MD. IMRAN HOSSAIN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '9/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '480',
                'CURRENT_SALARY' => '9260'
            ),
            125 => array(
                'PID' => '18J1079E',
                'NAME' => 'MINARA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/3/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '365',
                'CURRENT_SALARY' => '9423'
            ),
            126 => array(
                'PID' => '18J3488F',
                'NAME' => 'MST. FUARA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '9/3/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '8730'
            ),
            127 => array(
                'PID' => '18J1117E',
                'NAME' => 'DOLA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/10/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10000'
            ),
            128 => array(
                'PID' => '18J1120E',
                'NAME' => 'SHARMIN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/12/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10400'
            ),
            129 => array(
                'PID' => '18J1124E',
                'NAME' => 'SHOHEL',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/9/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '700',
                'CURRENT_SALARY' => '10410'
            ),
            130 => array(
                'PID' => '18J5666K',
                'NAME' => 'AFROZA',
                'DESIGNATION' => 'CLEANER',
                'DOJ' => '9/9/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '525',
                'CURRENT_SALARY' => '9300'
            ),
            131 => array(
                'PID' => '18J1127E',
                'NAME' => 'ROKEYA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/13/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10130'
            ),
            132 => array(
                'PID' => '18J1129E',
                'NAME' => 'SHAMIMA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '680',
                'CURRENT_SALARY' => '9600'
            ),
            133 => array(
                'PID' => '18J1161E',
                'NAME' => 'SHAMIA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/16/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '370',
                'CURRENT_SALARY' => '9540'
            ),
            134 => array(
                'PID' => '18J1163E',
                'NAME' => 'SHAHNAZ',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/20/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10500'
            ),
            135 => array(
                'PID' => '18J0249W',
                'NAME' => 'MD. SAZID MAHAM',
                'DESIGNATION' => 'SR. SAMPLEMAN',
                'DOJ' => '9/24/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '680',
                'CURRENT_SALARY' => '16030'
            ),
            136 => array(
                'PID' => '18K1198E',
                'NAME' => 'NAZMA',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '10/2/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '528',
                'CURRENT_SALARY' => '10620'
            ),
            137 => array(
                'PID' => '18K0283C',
                'NAME' => 'MD. ANOWAR PARVAG',
                'DESIGNATION' => 'SR. OFFICER PRODUCTION',
                'DOJ' => '10/1/2018',
                'LAST_INCRIMENT_DATE' => '10/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '4000',
                'CURRENT_SALARY' => '21000'
            ),
            138 => array(
                'PID' => '18L1090E',
                'NAME' => 'SUMAIYA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '480',
                'CURRENT_SALARY' => '10230'
            ),
            139 => array(
                'PID' => '18L0293C',
                'NAME' => 'RIPON',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '11/3/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '1000',
                'CURRENT_SALARY' => '19000'
            ),
            140 => array(
                'PID' => '18G0348C',
                'NAME' => 'MD. MAZEDUL TALUKDER',
                'DESIGNATION' => 'SUPERVISOR',
                'DOJ' => '7/14/2018',
                'LAST_INCRIMENT_DATE' => '7/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '22000'
            ),
            141 => array(
                'PID' => '18G5319V',
                'NAME' => 'SONJOY SING',
                'DESIGNATION' => 'DRIVER',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '7/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '14000'
            ),
            142 => array(
                'PID' => '18J4047H',
                'NAME' => 'FOJLE RABBI',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '9/2/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            143 => array(
                'PID' => '18K1194E',
                'NAME' => 'POLY',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/4/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '457',
                'CURRENT_SALARY' => '10100'
            ),
            144 => array(
                'PID' => '18K2606F',
                'NAME' => 'SHOPNA BEGUM',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '10/23/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9150'
            ),
            145 => array(
                'PID' => '18M1013E',
                'NAME' => 'RUBI AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '430',
                'CURRENT_SALARY' => '9250'
            ),
            146 => array(
                'PID' => '18M1294E',
                'NAME' => 'FARUK SORKER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/3/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10100'
            ),
            147 => array(
                'PID' => '18M1313E',
                'NAME' => 'RUNA',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '12/5/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10850'
            ),
            148 => array(
                'PID' => '18G1388E',
                'NAME' => 'MAHFUZ',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '7/2/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '555',
                'CURRENT_SALARY' => '10850'
            ),
            149 => array(
                'PID' => '18G0275C',
                'NAME' => 'SREE SHAYMOL BABU',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '7/11/2018',
                'LAST_INCRIMENT_DATE' => '3/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '19000'
            ),
            150 => array(
                'PID' => '18G0203W',
                'NAME' => 'RIPON BARUA',
                'DESIGNATION' => 'SR. SAMPLEMAN',
                'DOJ' => '7/5/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '700',
                'CURRENT_SALARY' => '16400'
            ),
            151 => array(
                'PID' => '18G4032H',
                'NAME' => 'MD. MARUF AHAMMED',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '7/14/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            152 => array(
                'PID' => '18J1095E',
                'NAME' => 'HASINA KHATUN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10100'
            ),
            153 => array(
                'PID' => '18J1097E',
                'NAME' => 'INSAN ALI',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '9/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '480',
                'CURRENT_SALARY' => '9260'
            ),
            154 => array(
                'PID' => '18J1100E',
                'NAME' => 'NUR NAHAR',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/9/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '480',
                'CURRENT_SALARY' => '9300'
            ),
            155 => array(
                'PID' => '18L1183E',
                'NAME' => 'MUSLIMA KHATUN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '11/1/2018',
                'LAST_INCRIMENT_DATE' => '12/31/2019',
                'LAST_INCRIMENT_AMOUNT' => '371',
                'CURRENT_SALARY' => '9400'
            ),
            156 => array(
                'PID' => '18L1253E',
                'NAME' => 'ASHA AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '11/15/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '430',
                'CURRENT_SALARY' => '9250'
            ),
            157 => array(
                'PID' => '19A6196G',
                'NAME' => 'MOMINUR ROHMAN',
                'DESIGNATION' => 'JUNIOR IRONMAN FINISHING',
                'DOJ' => '1/5/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9700'
            ),
            158 => array(
                'PID' => '19A4017H',
                'NAME' => 'NAZMUL',
                'DESIGNATION' => 'JUNIOR QUALITY INSPECTOR',
                'DOJ' => '1/6/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9900'
            ),
            159 => array(
                'PID' => '19A0298C',
                'NAME' => 'SHAMIM HOSSAIN',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '1/3/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '22000'
            ),
            160 => array(
                'PID' => '19A1145E',
                'NAME' => 'MOUSUMI',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '1/7/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '700',
                'CURRENT_SALARY' => '10850'
            ),
            161 => array(
                'PID' => '19A1228E',
                'NAME' => 'RAZIA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '1/6/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10000'
            ),
            162 => array(
                'PID' => '19A1311E',
                'NAME' => 'RUMA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '1/7/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9800'
            ),
            163 => array(
                'PID' => '18G1014E',
                'NAME' => 'SHATHI',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '542',
                'CURRENT_SALARY' => '10410'
            ),
            164 => array(
                'PID' => '18G4056H',
                'NAME' => 'SHOBUZ',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            165 => array(
                'PID' => '18G2514F',
                'NAME' => 'KOHINOOR',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9100'
            ),
            166 => array(
                'PID' => '18G2520F',
                'NAME' => 'AKLIMA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '1000',
                'CURRENT_SALARY' => '9500'
            ),
            167 => array(
                'PID' => '18J3462F',
                'NAME' => 'MST. RAZIYA AKT',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/3/2018',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '1000',
                'CURRENT_SALARY' => '10200'
            ),
            168 => array(
                'PID' => '18L1257E',
                'NAME' => 'BEDENA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/20/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '405',
                'CURRENT_SALARY' => '10355'
            ),
            169 => array(
                'PID' => '18M1282E',
                'NAME' => 'ROHIMA AKTER CH',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/2/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9450'
            ),
            170 => array(
                'PID' => '19A2701F',
                'NAME' => 'SHEMA PARVIN',
                'DESIGNATION' => 'SEWING ASSISTANT',
                'DOJ' => '1/10/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '330',
                'CURRENT_SALARY' => '8640'
            ),
            171 => array(
                'PID' => '19A3632F',
                'NAME' => 'SHOFIR',
                'DESIGNATION' => 'SEWING IRON MAN',
                'DOJ' => '1/6/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '9200'
            ),
            172 => array(
                'PID' => '19A1349E',
                'NAME' => 'JAKIR',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '1/8/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '550',
                'CURRENT_SALARY' => '10850'
            ),
            173 => array(
                'PID' => '18B5312L',
                'NAME' => 'M.A. HAMZA',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '2/12/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '11875'
            ),
            174 => array(
                'PID' => '18F5324L',
                'NAME' => 'SHOFIQUL ISLAM',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '1/14/2017',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '11875'
            ),
            175 => array(
                'PID' => '18J1080E',
                'NAME' => 'MST. HALIMA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '9/5/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '407',
                'CURRENT_SALARY' => '9260'
            ),
            176 => array(
                'PID' => '18J1082E',
                'NAME' => 'MST. SUJOLA KAHTUN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/5/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '496',
                'CURRENT_SALARY' => '9925'
            ),
            177 => array(
                'PID' => '18L1256E',
                'NAME' => 'SHIRINA BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '11/21/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '375',
                'CURRENT_SALARY' => '9655'
            ),
            178 => array(
                'PID' => '19A1000E',
                'NAME' => 'SHORIF MIAH',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '1/3/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10600'
            ),
            179 => array(
                'PID' => '19A1104E',
                'NAME' => 'MURAD',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '1/5/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10500'
            ),
            180 => array(
                'PID' => '19A3254F',
                'NAME' => 'SAJEDA KHATUN',
                'DESIGNATION' => 'SEWING ASSISTANT',
                'DOJ' => '1/10/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '330',
                'CURRENT_SALARY' => '8640'
            ),
            181 => array(
                'PID' => '19A4561J',
                'NAME' => 'MONIRUL',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '1/8/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9000'
            ),
            182 => array(
                'PID' => '19A3521F',
                'NAME' => 'ASMA BEGUM',
                'DESIGNATION' => 'SEWING ASSISTANT',
                'DOJ' => '1/10/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '330',
                'CURRENT_SALARY' => '8640'
            ),
            183 => array(
                'PID' => '19A3683F',
                'NAME' => 'ROCHONA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/15/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9300'
            ),
            184 => array(
                'PID' => '19A4068H',
                'NAME' => 'WAHIDUL ISLAM',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '1/17/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10100'
            ),
            185 => array(
                'PID' => '18D5333L',
                'NAME' => 'MD. MONIRUL HAQUE MOLLIK',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '4/15/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '11875'
            ),
            186 => array(
                'PID' => '18G5602L',
                'NAME' => 'MAKHAN CHANDRA DEBNATH',
                'DESIGNATION' => 'SUPERVISOR',
                'DOJ' => '6/19/2011',
                'LAST_INCRIMENT_DATE' => '4/30/2019',
                'LAST_INCRIMENT_AMOUNT' => '9225',
                'CURRENT_SALARY' => '20000'
            ),
            187 => array(
                'PID' => '18G5605L',
                'NAME' => 'MD. MOSHARAF HOSSAIN',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '4/1/2013',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '11875'
            ),
            188 => array(
                'PID' => '18G5614L',
                'NAME' => 'JASIM UDDIN MOLLA',
                'DESIGNATION' => 'SUPERVISOR',
                'DOJ' => '8/1/2007',
                'LAST_INCRIMENT_DATE' => '4/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '9225',
                'CURRENT_SALARY' => '20000'
            ),
            189 => array(
                'PID' => '18D5618L',
                'NAME' => 'MD. SAMIUL ISLAM',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '4/9/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '11875'
            ),
            190 => array(
                'PID' => '18G1618E',
                'NAME' => 'HELENA BEGUM',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '7/9/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10700'
            ),
            191 => array(
                'PID' => '18H4626J',
                'NAME' => 'TAPOSH KUMAR ROY',
                'DESIGNATION' => 'JR. BUNDILINGMAN',
                'DOJ' => '8/4/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8900'
            ),
            192 => array(
                'PID' => '18J3380F',
                'NAME' => 'MD. MONIRUZZAMAN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9860'
            ),
            193 => array(
                'PID' => '18J1036E',
                'NAME' => 'TAZALLI',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '9/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '653',
                'CURRENT_SALARY' => '10600'
            ),
            194 => array(
                'PID' => '18J4040H',
                'NAME' => 'NUR MOHAMMOD',
                'DESIGNATION' => 'OFFICER Q.C',
                'DOJ' => '9/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '7153',
                'CURRENT_SALARY' => '17000'
            ),
            195 => array(
                'PID' => '18J1039E',
                'NAME' => 'TASLIMA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '485',
                'CURRENT_SALARY' => '10460'
            ),
            196 => array(
                'PID' => '18J1148E',
                'NAME' => 'HASNA BEGUM SHIMA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/15/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '521',
                'CURRENT_SALARY' => '9950'
            ),
            197 => array(
                'PID' => '18J1149E',
                'NAME' => 'MST. MALEKA BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/16/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '360',
                'CURRENT_SALARY' => '9310'
            ),
            198 => array(
                'PID' => '18J1151E',
                'NAME' => 'JESMIN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/15/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '442',
                'CURRENT_SALARY' => '10310'
            ),
            199 => array(
                'PID' => '18J1160E',
                'NAME' => 'LAILY BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/17/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '370',
                'CURRENT_SALARY' => '9540'
            ),
            200 => array(
                'PID' => '18K1182E',
                'NAME' => 'SHANTI',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '370',
                'CURRENT_SALARY' => '9540'
            ),
            201 => array(
                'PID' => '18K1204E',
                'NAME' => 'POLY AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '10/16/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '535',
                'CURRENT_SALARY' => '10600'
            ),
            202 => array(
                'PID' => '18K1206E',
                'NAME' => 'KHADIZA AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/16/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9900'
            ),
            203 => array(
                'PID' => '18G2588F',
                'NAME' => 'JESMIN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9095'
            ),
            204 => array(
                'PID' => '18G1075E',
                'NAME' => 'SHUKHI',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9800'
            ),
            205 => array(
                'PID' => '18G3062Q',
                'NAME' => 'MD. ABDUL HAMID',
                'DESIGNATION' => 'JR OFFICER STORE',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '11/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '12500'
            ),
            206 => array(
                'PID' => '18G4140H',
                'NAME' => 'MD. MEHEDI HASAN',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '7/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            207 => array(
                'PID' => '18G2816F',
                'NAME' => 'RABIA BEGUM',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9100'
            ),
            208 => array(
                'PID' => '18J4048H',
                'NAME' => 'MILON REZA',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '9/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            209 => array(
                'PID' => '18J1131E',
                'NAME' => 'KHALEDA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10100'
            ),
            210 => array(
                'PID' => '18K1173E',
                'NAME' => 'TASLIMA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '390',
                'CURRENT_SALARY' => '10005'
            ),
            211 => array(
                'PID' => '18K1208E',
                'NAME' => 'MILA',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '10/16/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '550',
                'CURRENT_SALARY' => '10850'
            ),
            212 => array(
                'PID' => '18K1211E',
                'NAME' => 'MINA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/16/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '390',
                'CURRENT_SALARY' => '10005'
            ),
            213 => array(
                'PID' => '18K4066H',
                'NAME' => 'MD. RIPON SIKDER',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '10/15/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            214 => array(
                'PID' => '18G6009G',
                'NAME' => 'RINA PARVIN',
                'DESIGNATION' => 'FOLDING MAN',
                'DOJ' => '7/2/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            215 => array(
                'PID' => '18G1393E',
                'NAME' => 'MAHFUZA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10500'
            ),
            216 => array(
                'PID' => '18G4071H',
                'NAME' => 'DELOWAR',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            217 => array(
                'PID' => '18G6010G',
                'NAME' => 'JHORNA',
                'DESIGNATION' => 'POLY MAN',
                'DOJ' => '7/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            218 => array(
                'PID' => '18G1460E',
                'NAME' => 'JOHORA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '7/4/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '542',
                'CURRENT_SALARY' => '10410'
            ),
            219 => array(
                'PID' => '18G2609F',
                'NAME' => 'RUNA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/8/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '9100'
            ),
            220 => array(
                'PID' => '18G2682F',
                'NAME' => 'SALMA KHATUN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '7/9/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '9520'
            ),
            221 => array(
                'PID' => '18G4013H',
                'NAME' => 'MD. SANI SORDER',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '7/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            222 => array(
                'PID' => '18G1710E',
                'NAME' => 'MST. SUMI AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '7/16/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10300'
            ),
            223 => array(
                'PID' => '18J1042E',
                'NAME' => 'ADURI',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10150'
            ),
            224 => array(
                'PID' => '18J1043E',
                'NAME' => 'MASUM',
                'DESIGNATION' => 'SR. SAMPLEMAN',
                'DOJ' => '9/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '650',
                'CURRENT_SALARY' => '13190'
            ),
            225 => array(
                'PID' => '18J1056E',
                'NAME' => 'RIMA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/3/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9550'
            ),
            226 => array(
                'PID' => '18J1064E',
                'NAME' => 'JESMIN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/2/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '10200'
            ),
            227 => array(
                'PID' => '19A3730F',
                'NAME' => 'SUMI AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/23/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '490',
                'CURRENT_SALARY' => '8800'
            ),
            228 => array(
                'PID' => '19A3748F',
                'NAME' => 'PARVIN BEGUM',
                'DESIGNATION' => 'SEWING ASSISTANT',
                'DOJ' => '1/23/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '330',
                'CURRENT_SALARY' => '8640'
            ),
            229 => array(
                'PID' => '19B0265C',
                'NAME' => 'ALOM HOSSAIN',
                'DESIGNATION' => 'TRAINER',
                'DOJ' => '2/3/2019',
                'LAST_INCRIMENT_DATE' => '2/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '20500'
            ),
            230 => array(
                'PID' => '19C1403E',
                'NAME' => 'NAHAR AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/23/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10200'
            ),
            231 => array(
                'PID' => '19D1435E',
                'NAME' => 'ROKSANA KHATUN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '4/7/2019',
                'LAST_INCRIMENT_DATE' => '4/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '358',
                'CURRENT_SALARY' => '9358'
            ),
            232 => array(
                'PID' => '19D1006E',
                'NAME' => 'MONOWARA BEGUM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '4/15/2019',
                'LAST_INCRIMENT_DATE' => '4/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '383',
                'CURRENT_SALARY' => '9883'
            ),
            233 => array(
                'PID' => '19F3479F',
                'NAME' => 'MST. SHAHNAZ BEGUM',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/24/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '342',
                'CURRENT_SALARY' => '8650'
            ),
            234 => array(
                'PID' => '19H1034E',
                'NAME' => 'ASHA SARKER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '8/24/2019',
                'LAST_INCRIMENT_DATE' => '8/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '383',
                'CURRENT_SALARY' => '9883'
            ),
            235 => array(
                'PID' => '19H1037E',
                'NAME' => 'NIROB',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '8/22/2019',
                'LAST_INCRIMENT_DATE' => '8/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '358',
                'CURRENT_SALARY' => '9358'
            ),
            236 => array(
                'PID' => '19J6264G',
                'NAME' => 'MD. SUKKUR ALI',
                'DESIGNATION' => 'JUNIOR IRONMAN FINISHING',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '365',
                'CURRENT_SALARY' => '9465'
            ),
            237 => array(
                'PID' => '19J4107H',
                'NAME' => 'MD. RASEL MIA',
                'DESIGNATION' => 'JUNIOR QUALITY INSPECTOR',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9300'
            ),
            238 => array(
                'PID' => '19J4108H',
                'NAME' => 'MST. KULSUM',
                'DESIGNATION' => 'JUNIOR QUALITY INSPECTOR',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9800'
            ),
            239 => array(
                'PID' => '19J1022E',
                'NAME' => 'MST. GULSANA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '9300'
            ),
            240 => array(
                'PID' => '19J1084E',
                'NAME' => 'MST. REHENA PARVIN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10100'
            ),
            241 => array(
                'PID' => '19J1112E',
                'NAME' => 'MST. SABINA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/2/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '550',
                'CURRENT_SALARY' => '10050'
            ),
            242 => array(
                'PID' => '19A3427F',
                'NAME' => 'ASMA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/22/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '9350'
            ),
            243 => array(
                'PID' => '19B3837F',
                'NAME' => 'MINU',
                'DESIGNATION' => 'SEWING ASSISTANT',
                'DOJ' => '2/3/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '325',
                'CURRENT_SALARY' => '8635'
            ),
            244 => array(
                'PID' => '19B1191E',
                'NAME' => 'BANESA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '2/4/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9570'
            ),
            245 => array(
                'PID' => '19B1153E',
                'NAME' => 'MONI',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '2/9/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '700',
                'CURRENT_SALARY' => '10620'
            ),
            246 => array(
                'PID' => '19B3859F',
                'NAME' => 'MD. RAZU MONDOL',
                'DESIGNATION' => 'SEWING IRON MAN',
                'DOJ' => '2/9/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9260'
            ),
            247 => array(
                'PID' => '19B1376E',
                'NAME' => 'ESMETARA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '2/9/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10000'
            ),
            248 => array(
                'PID' => '19B0650W',
                'NAME' => 'MAHFUZA',
                'DESIGNATION' => 'SAMPLE MAN',
                'DOJ' => '2/5/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '585',
                'CURRENT_SALARY' => '14070'
            ),
            249 => array(
                'PID' => '19B3886F',
                'NAME' => 'TURJOY SHORKER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '2/13/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '700',
                'CURRENT_SALARY' => '9800'
            ),
            250 => array(
                'PID' => '19B5351V',
                'NAME' => 'MD. MINHAZUR RAHMAN',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '2/12/2019',
                'LAST_INCRIMENT_DATE' => '2/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '800',
                'CURRENT_SALARY' => '8800'
            ),
            251 => array(
                'PID' => '19C1220E',
                'NAME' => 'SALMA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/16/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10180'
            ),
            252 => array(
                'PID' => '19C1227E',
                'NAME' => 'RUBINA BEGUM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/20/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '390',
                'CURRENT_SALARY' => '9890'
            ),
            253 => array(
                'PID' => '19E1115E',
                'NAME' => 'SHAKATUN BEGUM',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '5/9/2019',
                'LAST_INCRIMENT_DATE' => '5/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '343',
                'CURRENT_SALARY' => '9043'
            ),
            254 => array(
                'PID' => '19F1245E',
                'NAME' => 'MD. ASADUL ISLAM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '6/20/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '378',
                'CURRENT_SALARY' => '9778'
            ),
            255 => array(
                'PID' => '19F2537F',
                'NAME' => 'MST. JESMIN AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/24/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '492',
                'CURRENT_SALARY' => '8800'
            ),
            256 => array(
                'PID' => '19F2591F',
                'NAME' => 'MST. MODINA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/24/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '480',
                'CURRENT_SALARY' => '8800'
            ),
            257 => array(
                'PID' => '19F1252E',
                'NAME' => 'MST. HAFIZA AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '6/16/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '373',
                'CURRENT_SALARY' => '9673'
            ),
            258 => array(
                'PID' => '19F1261E',
                'NAME' => 'MD. MEHEDI HASAN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/25/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '338',
                'CURRENT_SALARY' => '8938'
            ),
            259 => array(
                'PID' => '19F1262E',
                'NAME' => 'MST. ASMA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/25/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '348',
                'CURRENT_SALARY' => '9148'
            ),
            260 => array(
                'PID' => '19F1266E',
                'NAME' => 'SHUMI AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/25/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '338',
                'CURRENT_SALARY' => '8938'
            ),
            261 => array(
                'PID' => '19B3573F',
                'NAME' => 'MD. ALAL',
                'DESIGNATION' => 'SUPERVISOR',
                'DOJ' => '2/2/2019',
                'LAST_INCRIMENT_DATE' => '2/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '9500',
                'CURRENT_SALARY' => '18000'
            ),
            262 => array(
                'PID' => '19B1085E',
                'NAME' => 'KULSUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '2/2/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10000'
            ),
            263 => array(
                'PID' => '19B1121E',
                'NAME' => 'MAHFUZA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '2/2/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10100'
            ),
            264 => array(
                'PID' => '19B3710F',
                'NAME' => 'AMBIYA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '2/2/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9250'
            ),
            265 => array(
                'PID' => '19B3731F',
                'NAME' => 'ONJONA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '2/2/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8810'
            ),
            266 => array(
                'PID' => '19B5209M',
                'NAME' => 'FOIZUL ISLAM',
                'DESIGNATION' => 'OFFICER',
                'DOJ' => '2/2/2019',
                'LAST_INCRIMENT_DATE' => '2/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '27000'
            ),
            267 => array(
                'PID' => '19B1400E',
                'NAME' => 'JOSNA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '2/10/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9800'
            ),
            268 => array(
                'PID' => '19B5706K',
                'NAME' => 'SHAHNAZ',
                'DESIGNATION' => 'CLEANER',
                'DOJ' => '2/9/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '9475'
            ),
            269 => array(
                'PID' => '19E1114E',
                'NAME' => 'MST. BORHANA AKTER',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '5/5/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10550'
            ),
            270 => array(
                'PID' => '19F1234E',
                'NAME' => 'MD. KAMRUZZAMAN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '6/20/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '9183'
            ),
            271 => array(
                'PID' => '19F1267E',
                'NAME' => 'AYNA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/24/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '348',
                'CURRENT_SALARY' => '9148'
            ),
            272 => array(
                'PID' => '19F1268E',
                'NAME' => 'KHUKUMONI',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '6/22/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10245'
            ),
            273 => array(
                'PID' => '19F1031E',
                'NAME' => 'MST. SHATHI KHATUN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '6/13/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '373',
                'CURRENT_SALARY' => '9673'
            ),
            274 => array(
                'PID' => '19F4079H',
                'NAME' => 'AL-AMIN',
                'DESIGNATION' => 'JUNIOR QUALITY INSPECTOR',
                'DOJ' => '6/15/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '368',
                'CURRENT_SALARY' => '9568'
            ),
            275 => array(
                'PID' => '19F4080H',
                'NAME' => 'RAZIB MIA',
                'DESIGNATION' => 'JUNIOR QUALITY INSPECTOR',
                'DOJ' => '6/15/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '368',
                'CURRENT_SALARY' => '9568'
            ),
            276 => array(
                'PID' => '19F4083H',
                'NAME' => 'AMENA KHATUN',
                'DESIGNATION' => 'JUNIOR QUALITY INSPECTOR',
                'DOJ' => '6/15/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '373',
                'CURRENT_SALARY' => '9673'
            ),
            277 => array(
                'PID' => '19F4090H',
                'NAME' => 'ALAUDDIN',
                'DESIGNATION' => 'JUNIOR QUALITY INSPECTOR',
                'DOJ' => '6/13/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '368',
                'CURRENT_SALARY' => '9568'
            ),
            278 => array(
                'PID' => '19F4091H',
                'NAME' => 'MD. SHEHAB UDDIN',
                'DESIGNATION' => 'ORDINARY QUALITY INSPECTOR',
                'DOJ' => '6/13/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '333',
                'CURRENT_SALARY' => '8833'
            ),
            279 => array(
                'PID' => '19F1089E',
                'NAME' => 'MST. SHOPNA PARVIN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '6/13/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '393',
                'CURRENT_SALARY' => '10093'
            ),
            280 => array(
                'PID' => '19F1125E',
                'NAME' => 'MST. NASIMA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '6/13/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '393',
                'CURRENT_SALARY' => '10093'
            ),
            281 => array(
                'PID' => '19F3122F',
                'NAME' => 'MD. MASUK MIA',
                'DESIGNATION' => 'SEWING IRON MAN',
                'DOJ' => '6/24/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '333',
                'CURRENT_SALARY' => '8833'
            ),
            282 => array(
                'PID' => '19F6085G',
                'NAME' => 'NARGIS AKTER',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '6/26/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '308',
                'CURRENT_SALARY' => '8308'
            ),
            283 => array(
                'PID' => '19F6169G',
                'NAME' => 'MST. JAMELA BEGUM',
                'DESIGNATION' => 'ORDINARY OPERATOR',
                'DOJ' => '6/26/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8749'
            ),
            284 => array(
                'PID' => '19F4103H',
                'NAME' => 'MST. SHAJIYA SULTANA',
                'DESIGNATION' => 'ORDINARY QUALITY INSPECTOR',
                'DOJ' => '6/24/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '348',
                'CURRENT_SALARY' => '9148'
            ),
            285 => array(
                'PID' => '19F3450F',
                'NAME' => 'TANZILA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/24/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '8708'
            ),
            286 => array(
                'PID' => '19F1285E',
                'NAME' => 'RIBANA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/24/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '333',
                'CURRENT_SALARY' => '8833'
            ),
            287 => array(
                'PID' => '19G1207E',
                'NAME' => 'MST. SHOPNA BEGUM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '7/2/2019',
                'LAST_INCRIMENT_DATE' => '7/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '383',
                'CURRENT_SALARY' => '9883'
            ),
            288 => array(
                'PID' => '19G6270G',
                'NAME' => 'PARVIN BEGUM',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/2/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '8708'
            ),
            289 => array(
                'PID' => '19G2995F',
                'NAME' => 'MST. YASMIN AKTER',
                'DESIGNATION' => 'SEWING IRON MAN',
                'DOJ' => '7/1/2019',
                'LAST_INCRIMENT_DATE' => '7/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '333',
                'CURRENT_SALARY' => '8833'
            ),
            290 => array(
                'PID' => '19A3806F',
                'NAME' => 'HAZERA',
                'DESIGNATION' => 'SEWING IRON MAN',
                'DOJ' => '1/13/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '9105'
            ),
            291 => array(
                'PID' => '19C1154E',
                'NAME' => 'MITU',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '3/13/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9360'
            ),
            292 => array(
                'PID' => '19D6114G',
                'NAME' => 'MD. ABDUR RASHID',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '4/2/2019',
                'LAST_INCRIMENT_DATE' => '4/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '308',
                'CURRENT_SALARY' => '8308'
            ),
            293 => array(
                'PID' => '19E1118E',
                'NAME' => 'MST. NUR NAHAR',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '5/11/2019',
                'LAST_INCRIMENT_DATE' => '5/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '363',
                'CURRENT_SALARY' => '9463'
            ),
            294 => array(
                'PID' => '19G1290E',
                'NAME' => 'SUMONA AKTER SURAIA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '7/1/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '358',
                'CURRENT_SALARY' => '9708'
            ),
            295 => array(
                'PID' => '19G1299E',
                'NAME' => 'MST. KULSUM AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '7/1/2019',
                'LAST_INCRIMENT_DATE' => '7/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '393',
                'CURRENT_SALARY' => '10093'
            ),
            296 => array(
                'PID' => '19G1326E',
                'NAME' => 'MST. SONIYA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '7/6/2019',
                'LAST_INCRIMENT_DATE' => '7/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '393',
                'CURRENT_SALARY' => '10093'
            ),
            297 => array(
                'PID' => '19G1328E',
                'NAME' => 'MST. HOSNA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '7/2/2019',
                'LAST_INCRIMENT_DATE' => '7/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '363',
                'CURRENT_SALARY' => '9463'
            ),
            298 => array(
                'PID' => '19G1331E',
                'NAME' => 'NAZMA KHATUN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '7/1/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '368',
                'CURRENT_SALARY' => '9900'
            ),
            299 => array(
                'PID' => '19H4058H',
                'NAME' => 'RUNA AKTER',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '8/22/2019',
                'LAST_INCRIMENT_DATE' => '8/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '375',
                'CURRENT_SALARY' => '9722'
            ),
            300 => array(
                'PID' => '19B1418E',
                'NAME' => 'MORZINA AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '2/17/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9990'
            ),
            301 => array(
                'PID' => '19C1108E',
                'NAME' => 'JANNAT',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/2/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10180'
            ),
            302 => array(
                'PID' => '19C1137E',
                'NAME' => 'MUKTA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/2/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '380',
                'CURRENT_SALARY' => '9680'
            ),
            303 => array(
                'PID' => '19D1065E',
                'NAME' => 'KOMOLA BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '4/1/2019',
                'LAST_INCRIMENT_DATE' => '4/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '373',
                'CURRENT_SALARY' => '9673'
            ),
            304 => array(
                'PID' => '19D4530J',
                'NAME' => 'MD. SAIDUR RAHMAN',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '4/1/2019',
                'LAST_INCRIMENT_DATE' => '4/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '308',
                'CURRENT_SALARY' => '8308'
            ),
            305 => array(
                'PID' => '19D1092E',
                'NAME' => 'JUMUR BEGUM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '4/20/2019',
                'LAST_INCRIMENT_DATE' => '4/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '388',
                'CURRENT_SALARY' => '9988'
            ),
            306 => array(
                'PID' => '19F1222E',
                'NAME' => 'YASIN RANA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '6/18/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '373',
                'CURRENT_SALARY' => '9673'
            ),
            307 => array(
                'PID' => '19C5711K',
                'NAME' => 'JAMILA',
                'DESIGNATION' => 'CLEANER',
                'DOJ' => '3/2/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '330',
                'CURRENT_SALARY' => '8705'
            ),
            308 => array(
                'PID' => '19D1007E',
                'NAME' => 'SALMA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '4/15/2019',
                'LAST_INCRIMENT_DATE' => '4/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '393',
                'CURRENT_SALARY' => '10093'
            ),
            309 => array(
                'PID' => '19F1159E',
                'NAME' => 'MAZIDUL RAHMAN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/17/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '333',
                'CURRENT_SALARY' => '8833'
            ),
            310 => array(
                'PID' => '19F1165E',
                'NAME' => 'MST. SHAHIDA BEGUM',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/16/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '343',
                'CURRENT_SALARY' => '9043'
            ),
            311 => array(
                'PID' => '19F1168E',
                'NAME' => 'SREE SHONJIT',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '6/17/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '378',
                'CURRENT_SALARY' => '9778'
            ),
            312 => array(
                'PID' => '19F1177E',
                'NAME' => 'EMDADUL',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '6/15/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '398',
                'CURRENT_SALARY' => '10198'
            ),
            313 => array(
                'PID' => '19F1189E',
                'NAME' => 'AKHIUZZAMAN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '6/17/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '343',
                'CURRENT_SALARY' => '9043'
            ),
            314 => array(
                'PID' => '19F5825U',
                'NAME' => 'SREE SOYON CHANDRA BISHAWSARMA',
                'DESIGNATION' => 'ASSISTENT PRODUCTION REPOTER',
                'DOJ' => '6/15/2019',
                'LAST_INCRIMENT_DATE' => '6/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9673'
            ),
            315 => array(
                'PID' => '19G1350E',
                'NAME' => 'SHOJIB',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '7/13/2019',
                'LAST_INCRIMENT_DATE' => '7/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '383',
                'CURRENT_SALARY' => '9883'
            ),
            316 => array(
                'PID' => '19J1327E',
                'NAME' => 'MD. RASHIDUL ISLAM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/7/2019',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '380',
                'CURRENT_SALARY' => '10000'
            ),
            317 => array(
                'PID' => '19J3565F',
                'NAME' => 'MD. ASHIK MIA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/8/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '335',
                'CURRENT_SALARY' => '9200'
            ),
            318 => array(
                'PID' => '19J3574F',
                'NAME' => 'MD. MOTALEB HOSSAIN SHEKH',
                'DESIGNATION' => 'SEWING IRON MAN',
                'DOJ' => '9/9/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '335',
                'CURRENT_SALARY' => '8835'
            ),
            319 => array(
                'PID' => '19J1357E',
                'NAME' => 'ROKSANA PARVEEN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/8/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '355',
                'CURRENT_SALARY' => '9230'
            ),
            320 => array(
                'PID' => '19K3433F',
                'NAME' => 'MST. SHARMIN AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '10/9/2019',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '327',
                'CURRENT_SALARY' => '8635'
            ),
            321 => array(
                'PID' => '19L1414E',
                'NAME' => 'TAWHIDA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '11/11/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9300'
            ),
            322 => array(
                'PID' => '19L4822J',
                'NAME' => 'SHAMIM MIA',
                'DESIGNATION' => 'ORD. FUSING MACHINE OPERATOR',
                'DOJ' => '11/11/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '335',
                'CURRENT_SALARY' => '8835'
            ),
            323 => array(
                'PID' => '19L4009H',
                'NAME' => 'HAFIZUR RAHMAN',
                'DESIGNATION' => 'ORDINARY QUALITY INSPECTOR',
                'DOJ' => '11/18/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '9100'
            ),
            324 => array(
                'PID' => '19M1110E',
                'NAME' => 'SHEWLE',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '12/8/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10500'
            ),
            325 => array(
                'PID' => '19M6511G',
                'NAME' => 'MST. NASIMA',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '12/7/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '8350'
            ),
            326 => array(
                'PID' => '19M2544F',
                'NAME' => 'MST. RABIYA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/15/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '8660'
            ),
            327 => array(
                'PID' => '19M6258G',
                'NAME' => 'MST. LUTFUNNAHAR',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/19/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '8800'
            ),
            328 => array(
                'PID' => '19M6725G',
                'NAME' => 'MST. SOKINA BEGUM',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '12/19/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '8350'
            ),
            329 => array(
                'PID' => '19M3423F',
                'NAME' => 'MST. SHILA KHATUN',
                'DESIGNATION' => 'ORDINARY POLY MAN',
                'DOJ' => '12/19/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9200'
            ),
            330 => array(
                'PID' => '19J4691Z',
                'NAME' => 'MD. IBRAHIM HOSSAIN',
                'DESIGNATION' => 'STORE LABOR',
                'DOJ' => '9/2/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '330',
                'CURRENT_SALARY' => '8705'
            ),
            331 => array(
                'PID' => '19J5302L',
                'NAME' => 'MD. KHADIMUL ISLAM',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '9/14/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '11225'
            ),
            332 => array(
                'PID' => '19J5303L',
                'NAME' => 'AHAD MOLLAH',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '9/22/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '11225'
            ),
            333 => array(
                'PID' => '19K1212E',
                'NAME' => 'MST. BITHI KHATUN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '10/24/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10000'
            ),
            334 => array(
                'PID' => '19L0228C',
                'NAME' => 'ABU SAYED',
                'DESIGNATION' => 'OFFICER Q.C',
                'DOJ' => '11/26/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '2000',
                'CURRENT_SALARY' => '25000'
            ),
            335 => array(
                'PID' => '19M2563F',
                'NAME' => 'MD. MONIRUZZAMAN',
                'DESIGNATION' => 'SEWING IRON MAN',
                'DOJ' => '12/18/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '335',
                'CURRENT_SALARY' => '8835'
            ),
            336 => array(
                'PID' => '19M1021E',
                'NAME' => 'MST. JOSNI AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/17/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9000'
            ),
            337 => array(
                'PID' => '19M1178E',
                'NAME' => 'MD. HARUN OR RASHID',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/17/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10100'
            ),
            338 => array(
                'PID' => '19M1188E',
                'NAME' => 'MD. RAJIB MIA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/14/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9400'
            ),
            339 => array(
                'PID' => '19J5712K',
                'NAME' => 'MST. HAZERA BEGUM',
                'DESIGNATION' => 'CLEANER',
                'DOJ' => '9/17/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '8725'
            ),
            340 => array(
                'PID' => '19K1246E',
                'NAME' => 'SUMI AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '10/14/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '398',
                'CURRENT_SALARY' => '10198'
            ),
            341 => array(
                'PID' => '19L1323E',
                'NAME' => 'MST. RINA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/2/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10100'
            ),
            342 => array(
                'PID' => '19L1382E',
                'NAME' => 'MST. AKHI AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '11/2/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '370',
                'CURRENT_SALARY' => '9570'
            ),
            343 => array(
                'PID' => '19L1383E',
                'NAME' => 'MD. OMOR FARUK',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '11/2/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9000'
            ),
            344 => array(
                'PID' => '19L1385E',
                'NAME' => 'MD. HASAN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/2/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10200'
            ),
            345 => array(
                'PID' => '19L1391E',
                'NAME' => 'MST. CHAMPA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '11/2/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '9100'
            ),
            346 => array(
                'PID' => '19L4001H',
                'NAME' => 'MOJAMMEL HOQUE',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '11/2/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '375',
                'CURRENT_SALARY' => '9722'
            ),
            347 => array(
                'PID' => '19L1396E',
                'NAME' => 'SADDAM HOSSAIN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/4/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9800'
            ),
            348 => array(
                'PID' => '19L4716J',
                'NAME' => 'MD. SELIM HOSSAIN',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '11/3/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            349 => array(
                'PID' => '19L1450E',
                'NAME' => 'MST. SULTANA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '11/14/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9100'
            ),
            350 => array(
                'PID' => '19L1488E',
                'NAME' => 'MST. TANJINA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '11/14/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '8820'
            ),
            351 => array(
                'PID' => '19M3709G',
                'NAME' => 'TAHMINA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/15/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '8420'
            ),
            352 => array(
                'PID' => '19M5705K',
                'NAME' => 'MD. HIRA MIA',
                'DESIGNATION' => 'CLEANER',
                'DOJ' => '12/19/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '335',
                'CURRENT_SALARY' => '8835'
            ),
            353 => array(
                'PID' => '19M1217E',
                'NAME' => 'TANZILA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/18/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10100'
            ),
            354 => array(
                'PID' => '20B5710K',
                'NAME' => 'JAKIYA SHIKDER',
                'DESIGNATION' => 'BABY SITTER',
                'DOJ' => '2/16/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '11225'
            ),
            355 => array(
                'PID' => '19J4696J',
                'NAME' => 'MD. SABBIR HOSSAIN',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '310',
                'CURRENT_SALARY' => '8310'
            ),
            356 => array(
                'PID' => '19J5300L',
                'NAME' => 'SREE SHAMOL SUTRADHAR',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '9/10/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '11225'
            ),
            357 => array(
                'PID' => '19L1409E',
                'NAME' => 'MST. TAHMINA KHATUN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/9/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '550',
                'CURRENT_SALARY' => '10050'
            ),
            358 => array(
                'PID' => '19L1456E',
                'NAME' => 'MD. JAMAN MIA',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '11/17/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '10550'
            ),
            359 => array(
                'PID' => '19M4513Z',
                'NAME' => 'TORONI KANTO RAI',
                'DESIGNATION' => 'STORE LABOR',
                'DOJ' => '12/10/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '8775'
            ),
            360 => array(
                'PID' => '19M1147E',
                'NAME' => 'MST. MONOWARA BEGUM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/8/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '380',
                'CURRENT_SALARY' => '9780'
            ),
            361 => array(
                'PID' => '19M2506F',
                'NAME' => 'MD. ESMAEL HOSSAN',
                'DESIGNATION' => 'SEWING ASSISTANT',
                'DOJ' => '12/11/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '325',
                'CURRENT_SALARY' => '8625'
            ),
            362 => array(
                'PID' => '19K1001E',
                'NAME' => 'MST. REJENA BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/5/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9500'
            ),
            363 => array(
                'PID' => '19K1024E',
                'NAME' => 'SHEFALY',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/3/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '375',
                'CURRENT_SALARY' => '9675'
            ),
            364 => array(
                'PID' => '19K1051E',
                'NAME' => 'SABINA AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/2/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '360',
                'CURRENT_SALARY' => '9360'
            ),
            365 => array(
                'PID' => '19K1093E',
                'NAME' => 'MST. MORSHEDA KHATUN',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '10/1/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10245'
            ),
            366 => array(
                'PID' => '19K1105E',
                'NAME' => 'NAZMA BEGUM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '10/1/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '383',
                'CURRENT_SALARY' => '9883'
            ),
            367 => array(
                'PID' => '19K6289G',
                'NAME' => 'MAZEDA',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '10/7/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '308',
                'CURRENT_SALARY' => '8308'
            ),
            368 => array(
                'PID' => '19K6294G',
                'NAME' => 'PRIYA DATTO',
                'DESIGNATION' => 'ORDINARY TAGMAN',
                'DOJ' => '10/7/2019',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '342',
                'CURRENT_SALARY' => '8650'
            ),
            369 => array(
                'PID' => '19L1425E',
                'NAME' => 'HABIZA BEGUM',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/10/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10000'
            ),
            370 => array(
                'PID' => '19L1426E',
                'NAME' => 'MST. MORZINA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '11/2/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '8900'
            ),
            371 => array(
                'PID' => '19L1431E',
                'NAME' => 'MD. RAJIB HOSSAIN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/9/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9900'
            ),
            372 => array(
                'PID' => '19L1436E',
                'NAME' => 'MST. MONIRA KHATUN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/9/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10100'
            ),
            373 => array(
                'PID' => '19L1439E',
                'NAME' => 'MST. ASMA BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '11/13/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '365',
                'CURRENT_SALARY' => '9465'
            ),
            374 => array(
                'PID' => '19L0655W',
                'NAME' => 'MD. FIROZ SHEKH',
                'DESIGNATION' => 'SR. SAMPLEMAN',
                'DOJ' => '11/6/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '620',
                'CURRENT_SALARY' => '14620'
            ),
            375 => array(
                'PID' => '19L1476E',
                'NAME' => 'MD. SHAHAB UDDIN',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/17/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9800'
            ),
            376 => array(
                'PID' => '19L1477E',
                'NAME' => 'LIPI AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/19/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9900'
            ),
            377 => array(
                'PID' => '19L1483E',
                'NAME' => 'NAHAR',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '11/14/2019',
                'LAST_INCRIMENT_DATE' => '11/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10500'
            ),
            378 => array(
                'PID' => '19M1025E',
                'NAME' => 'LIPI',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '12/3/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '410',
                'CURRENT_SALARY' => '10410'
            ),
            379 => array(
                'PID' => '19M1126E',
                'NAME' => 'SHIMA RANI DEBNATH',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/1/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '340',
                'CURRENT_SALARY' => '8840'
            ),
            380 => array(
                'PID' => '19K1377E',
                'NAME' => 'AKLIMA KHATUN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/12/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '352',
                'CURRENT_SALARY' => '9227'
            ),
            381 => array(
                'PID' => '19K5304L',
                'NAME' => 'MD. SHAHIN ALAM',
                'DESIGNATION' => 'SECURITY GUARD',
                'DOJ' => '10/17/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '450',
                'CURRENT_SALARY' => '11225'
            ),
            382 => array(
                'PID' => '19M4036H',
                'NAME' => 'MD. JOSIM UDDIN',
                'DESIGNATION' => 'QUALITY INSPECTOR',
                'DOJ' => '12/3/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '700',
                'CURRENT_SALARY' => '10200'
            ),
            383 => array(
                'PID' => '19M1010E',
                'NAME' => 'MST. SUMI AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/7/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '370',
                'CURRENT_SALARY' => '9570'
            ),
            384 => array(
                'PID' => '19M1018E',
                'NAME' => 'CHAMPA BEGUM',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '12/7/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10400'
            ),
            385 => array(
                'PID' => '19M1068E',
                'NAME' => 'MD. DELOWER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/11/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10100'
            ),
            386 => array(
                'PID' => '19M1101E',
                'NAME' => 'MD. MOJAMMEL HOQUE',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/14/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9900'
            ),
            387 => array(
                'PID' => '19M1132E',
                'NAME' => 'MST. PARVIN AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/11/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10400'
            ),
            388 => array(
                'PID' => '19M1166E',
                'NAME' => 'ANOUR HOSSEN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/14/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '650',
                'CURRENT_SALARY' => '9850'
            ),
            389 => array(
                'PID' => '19M1205E',
                'NAME' => 'MST. RINA BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/12/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9600'
            ),
            390 => array(
                'PID' => '19M3637F',
                'NAME' => 'MD. SHOHEL',
                'DESIGNATION' => 'SEWING IRON MAN',
                'DOJ' => '12/18/2019',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '8850'
            ),
            391 => array(
                'PID' => '18D2094E',
                'NAME' => 'SHAHNAZ',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '4/12/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10230'
            ),
            392 => array(
                'PID' => '19J1201E',
                'NAME' => 'MURSHIDA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10000'
            ),
            393 => array(
                'PID' => '19J1242E',
                'NAME' => 'SABINA',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '360',
                'CURRENT_SALARY' => '9360'
            ),
            394 => array(
                'PID' => '19J1249E',
                'NAME' => 'JHORNA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10100'
            ),
            395 => array(
                'PID' => '19J6544G',
                'NAME' => 'RAKIB MUNSHI',
                'DESIGNATION' => 'JUNIOR IRONMAN FINISHING',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '365',
                'CURRENT_SALARY' => '9465'
            ),
            396 => array(
                'PID' => '19J6558G',
                'NAME' => 'MD. ABU BOKOR SIDDIK',
                'DESIGNATION' => 'PACKER',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '380',
                'CURRENT_SALARY' => '9780'
            ),
            397 => array(
                'PID' => '17L6283G',
                'NAME' => 'DELOWER RAHMAN',
                'DESIGNATION' => 'SPOT MAN',
                'DOJ' => '11/21/2017',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10777'
            ),
            398 => array(
                'PID' => '19J1315E',
                'NAME' => 'MD. AMANOT',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '9/1/2019',
                'LAST_INCRIMENT_DATE' => '9/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '370',
                'CURRENT_SALARY' => '9570'
            ),
            399 => array(
                'PID' => '19K6312G',
                'NAME' => 'LUCKY AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '10/7/2019',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '350',
                'CURRENT_SALARY' => '8658'
            ),
            400 => array(
                'PID' => '19K6439G',
                'NAME' => 'SAINA',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '10/8/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '308',
                'CURRENT_SALARY' => '8308'
            ),
            401 => array(
                'PID' => '19K3247F',
                'NAME' => 'NASRIN AKHTER',
                'DESIGNATION' => 'ASSISTANT',
                'DOJ' => '10/8/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '308',
                'CURRENT_SALARY' => '8308'
            ),
            402 => array(
                'PID' => '19K1296E',
                'NAME' => 'NIJAM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/8/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '368',
                'CURRENT_SALARY' => '9568'
            ),
            403 => array(
                'PID' => '19K1308E',
                'NAME' => 'MD. SHAGOR',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '10/8/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '388',
                'CURRENT_SALARY' => '9988'
            ),
            404 => array(
                'PID' => '19K4712J',
                'NAME' => 'ABDUR RAHMAN',
                'DESIGNATION' => 'ORD. FUSING MACHINE OPERATOR',
                'DOJ' => '10/19/2019',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '380',
                'CURRENT_SALARY' => '8800'
            ),
            405 => array(
                'PID' => '18B1955E',
                'NAME' => 'SABIA YASMIN SOBI',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '2/1/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9980'
            ),
            406 => array(
                'PID' => '18B4856J',
                'NAME' => 'MD.NAZRUL ISLAM',
                'DESIGNATION' => 'JUNIUR OFFICER CUTTING',
                'DOJ' => '2/5/2018',
                'LAST_INCRIMENT_DATE' => '2/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '3000',
                'CURRENT_SALARY' => '21000'
            ),
            407 => array(
                'PID' => '18C1525E',
                'NAME' => 'RUBEL',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '3/18/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '650',
                'CURRENT_SALARY' => '10600'
            ),
            408 => array(
                'PID' => '19A3668F',
                'NAME' => 'SHILPI',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/12/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '340',
                'CURRENT_SALARY' => '8650'
            ),
            409 => array(
                'PID' => '19A1364E',
                'NAME' => 'JESMIN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/15/2019',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '480',
                'CURRENT_SALARY' => '9300'
            ),
            410 => array(
                'PID' => '18K0288C',
                'NAME' => 'MD. MOHSIN ALAM',
                'DESIGNATION' => 'ASST MANAGER PRODUCTION',
                'DOJ' => '10/20/2018',
                'LAST_INCRIMENT_DATE' => '10/1/2019',
                'LAST_INCRIMENT_AMOUNT' => '5000',
                'CURRENT_SALARY' => '40000'
            ),
            411 => array(
                'PID' => '20G1046E',
                'NAME' => 'MD. AL AMIN MONDOL',
                'DESIGNATION' => 'JUNIOR QUALITY INSPECTOR',
                'DOJ' => '7/7/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '700',
                'CURRENT_SALARY' => '9200'
            ),
            412 => array(
                'PID' => '20G2546F',
                'NAME' => 'ALINUR',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/9/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            413 => array(
                'PID' => '20G2710F',
                'NAME' => 'MST. MUNNI KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/9/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            414 => array(
                'PID' => '20H1244E',
                'NAME' => 'RUBEL HOSSAIN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '8/12/2020',
                'LAST_INCRIMENT_DATE' => '10/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9700'
            ),
            415 => array(
                'PID' => '20H1255E',
                'NAME' => 'ISMATARA BEGUM',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '8/9/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9000'
            ),
            416 => array(
                'PID' => '20G3586F',
                'NAME' => 'MD.MAMUN HAWLADER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/15/2020',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            417 => array(
                'PID' => '20G3642F',
                'NAME' => 'MISS. TANIA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/18/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            418 => array(
                'PID' => '20H1340E',
                'NAME' => 'ASMA AKTER',
                'DESIGNATION' => 'SR SEWING MACHINE OPRATOR',
                'DOJ' => '8/22/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10100'
            ),
            419 => array(
                'PID' => '20G3598F',
                'NAME' => 'MST. SHAGORIKA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/15/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            420 => array(
                'PID' => '20J4012J',
                'NAME' => 'MD. SHOHAG HOSSAIN',
                'DESIGNATION' => 'JR. INPUTMAN',
                'DOJ' => '9/2/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9000'
            ),
            421 => array(
                'PID' => '20G2687F',
                'NAME' => 'BITHI AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/9/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            422 => array(
                'PID' => '20G2855F',
                'NAME' => 'MST. RUPALI KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '7/11/2020',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            423 => array(
                'PID' => '20H1329E',
                'NAME' => 'KHURSHIDA BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '8/17/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '300',
                'CURRENT_SALARY' => '9200'
            ),
            424 => array(
                'PID' => '16L6159G',
                'NAME' => 'MD. BACHCHU MIA',
                'DESIGNATION' => 'IRON MAN',
                'DOJ' => '11/1/2016',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '10247'
            ),
            425 => array(
                'PID' => '20M1232E',
                'NAME' => 'RUMA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/7/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10000'
            ),
            426 => array(
                'PID' => '20M1570E',
                'NAME' => 'MD. REZA HOSSAIN',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/5/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9000'
            ),
            427 => array(
                'PID' => '20K1462E',
                'NAME' => 'MD. ZAHID',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '10/12/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9700'
            ),
            428 => array(
                'PID' => '20L3750F',
                'NAME' => 'MST. POLY KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '11/12/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            429 => array(
                'PID' => '20L3716G',
                'NAME' => 'MST. MASUDA KHATUN',
                'DESIGNATION' => 'ORDINARY TAGMAN',
                'DOJ' => '11/2/2020',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            430 => array(
                'PID' => '20L3832F',
                'NAME' => 'SHOMA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '11/21/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            431 => array(
                'PID' => '20M2633F',
                'NAME' => 'ROSHEDA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/9/2020',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            432 => array(
                'PID' => '20M1597E',
                'NAME' => 'MST. MIRA BEGUM',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/5/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9000'
            ),
            433 => array(
                'PID' => '20M1609E',
                'NAME' => 'SREEMOTI MALOTI',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/5/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '9500'
            ),
            434 => array(
                'PID' => '20M1610E',
                'NAME' => 'MST. JINIYA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '12/7/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '300',
                'CURRENT_SALARY' => '9600'
            ),
            435 => array(
                'PID' => '20M2711F',
                'NAME' => 'SREE MALA RANI',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/5/2020',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            436 => array(
                'PID' => '20M2720F',
                'NAME' => 'KULSUMA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/3/2020',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            437 => array(
                'PID' => '20M1603E',
                'NAME' => 'MST. SHUBORNA',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/10/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '580',
                'CURRENT_SALARY' => '9000'
            ),
            438 => array(
                'PID' => '20M2846F',
                'NAME' => 'MST. TANIA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/6/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            439 => array(
                'PID' => '20M2866F',
                'NAME' => 'MST. SHARMIN KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/6/2020',
                'LAST_INCRIMENT_DATE' => '1/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            440 => array(
                'PID' => '20M1554E',
                'NAME' => 'RIPA AKTER',
                'DESIGNATION' => 'JR SEWING MACHINE OPRERATOR',
                'DOJ' => '12/1/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '9700'
            ),
            441 => array(
                'PID' => '20L1019E',
                'NAME' => 'SREEMOTI ANJALI',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '11/17/2020',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '300',
                'CURRENT_SALARY' => '10000'
            ),
            442 => array(
                'PID' => '20L3821F',
                'NAME' => 'MISS SRABONI AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '11/18/2020',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            443 => array(
                'PID' => '20M2925F',
                'NAME' => 'MST. SUMAIYA BEGUM',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '12/26/2020',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            444 => array(
                'PID' => '21A3178F',
                'NAME' => 'MST. ROJINA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/9/2021',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            445 => array(
                'PID' => '18B2235E',
                'NAME' => 'MAHFUJA',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '2/11/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '650',
                'CURRENT_SALARY' => '10500'
            ),
            446 => array(
                'PID' => '18A2449E',
                'NAME' => 'MST. DINA AKTER',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '1/10/2018',
                'LAST_INCRIMENT_DATE' => '12/1/2020',
                'LAST_INCRIMENT_AMOUNT' => '600',
                'CURRENT_SALARY' => '10400'
            ),
            447 => array(
                'PID' => '21A3001F',
                'NAME' => 'SOMELA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/3/2021',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            448 => array(
                'PID' => '21A3094F',
                'NAME' => 'RUNA KHATUN',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/2/2021',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            449 => array(
                'PID' => '21A3061F',
                'NAME' => 'JUMA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/10/2021',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            450 => array(
                'PID' => '21A3113F',
                'NAME' => 'MOSAMMOT SURAIYA AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/5/2021',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '420',
                'CURRENT_SALARY' => '8420'
            ),
            451 => array(
                'PID' => '21A3051F',
                'NAME' => 'JESMIN AKTER',
                'DESIGNATION' => 'ORD SEWING MACH OPERATOR',
                'DOJ' => '1/2/2021',
                'LAST_INCRIMENT_DATE' => '2/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '500',
                'CURRENT_SALARY' => '8500'
            ),
            452 => array(
                'PID' => '21A1699E',
                'NAME' => 'MD. OMAR FARUK',
                'DESIGNATION' => 'SEWING MACHINE OPERATOR',
                'DOJ' => '1/17/2021',
                'LAST_INCRIMENT_DATE' => '3/1/2021',
                'LAST_INCRIMENT_AMOUNT' => '400',
                'CURRENT_SALARY' => '10100'
            )
        );
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('as_oracle_code', 'associate_id', 'as_status', 'as_doj', 'as_name')
        ->whereIn('b.as_unit_id', [3])
        ->whereIn('b.as_location', [9])
        ->get();

        // $getIncrement = DB::table('hr_increment')
        // ->get()
        // ->keyBy('associate_id')
        // ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getData as $key1 => $value) {
                if($info->as_oracle_code == $value['PID']){
                    $getIncrement = DB::table('hr_increment')->where('associate_id', $info->associate_id)->where('effective_date', date('Y-m-d', strtotime($value['LAST_INCRIMENT_DATE'])))->where('increment_amount', $value['LAST_INCRIMENT_AMOUNT'])->first();

                    if($getIncrement == null){
                            // $macth[$info->associate_id] = $value;
                            ++$count;
                        
                        $macth[] = DB::table('hr_increment')
                        ->insertGetId([
                            'associate_id' => $info->associate_id,
                            'current_salary' => ($value['CURRENT_SALARY'] - $value['LAST_INCRIMENT_AMOUNT']),
                            'increment_type' => 2,
                            'increment_amount' => $value['LAST_INCRIMENT_AMOUNT'],
                            'amount_type' => 1,
                            'applied_date' => date('Y-m-d', strtotime($value['LAST_INCRIMENT_DATE'])),
                            'eligible_date' => date('Y-m-d', strtotime($value['LAST_INCRIMENT_DATE'])),
                            'effective_date' => date('Y-m-d', strtotime($value['LAST_INCRIMENT_DATE'])),
                            'status' => 1,
                        ]);

                    }
                }
            }
        }

        // return $count;
        return count($macth);
    }

    public function benefitUpdate()
    {
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('b.as_oracle_code', 'b.associate_id', 'b.as_status', 'b.as_doj', 'b.as_name', 'b.as_unit_id', 'a.ben_current_salary')
        // ->whereIn('b.as_unit_id', [8])
        ->leftJoin('hr_benefits AS a', function($q){
            $q->on('a.ben_as_id', 'b.associate_id');
        })
        ->where('as_status', '!=', 0)
        ->get();
        // return $getBasic;
        $getIncrement = DB::table('hr_increment')
        ->get()
        ->keyBy('associate_id')
        ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getIncrement as $key => $value) {
                if($info->associate_id == $value->associate_id && (($value->current_salary+$value->increment_amount) > $info->ben_current_salary) && in_array($info->as_unit_id, [3,8])){

                    $value->ben_current_salary = $info->ben_current_salary;
                    $value->as_unit_id = $info->as_unit_id;
                    $macth[] = $value;

                }
            }
        }

        $tomacth = [];
        return $macth;
        foreach ($macth as $key1 => $val) {
            $ben = DB::table('hr_benefits as b')
                            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                            ->where('a.associate_id', $val->associate_id)
                            ->first();
            if($ben != null){
                $up['ben_current_salary'] = ($val->current_salary + $val->increment_amount);
                $up['ben_basic'] = ceil(($up['ben_current_salary']-1850)/1.5);
                $up['ben_house_rent'] = $up['ben_current_salary'] -1850 - $up['ben_basic'];

                if($ben->ben_bank_amount > 0){
                    $up['ben_bank_amount'] = $up['ben_current_salary'];
                    $up['ben_cash_amount'] = 0;
                }else{
                    $up['ben_cash_amount'] = $up['ben_current_salary'];
                    $up['ben_bank_amount'] = 0;
                }
                $tomacth[] = $up;
                $exist[$key1] = DB::table('hr_benefits')->where('ben_id', $ben->ben_id)->update($up);
            }
        }
        return ($exist);
    }

    public function incrementMarge()
    {
        $getIncrement = DB::table('hr_increment')
        ->select('associate_id', 'increment_type', 'applied_date', 'eligible_date', DB::raw('COUNT(*) AS count'))
        ->groupBy(['associate_id', 'increment_type', 'applied_date', 'eligible_date'])
        ->having('count', '>', 1)
        ->get();
        $increment = [];
        foreach ($getIncrement as $key => $value) {
            $increment[] = DB::table('hr_increment')
            ->select('associate_id', 'applied_date', DB::raw('sum(increment_amount) as amount'), DB::raw('MAX(id) AS maxid'), DB::raw('MIN(id) AS minid'))
            ->where('associate_id', $value->associate_id)
            ->where('applied_date', $value->applied_date)
            ->groupBy('associate_id')
            ->first();
        }

        foreach ($increment as $key1 => $va) {
            DB::table("hr_increment")
            ->where('associate_id', $va->associate_id)
            ->where('id', $va->maxid)
            ->update([
                'increment_amount' => $va->amount
            ]);

            DB::table('hr_increment')
            ->where('id', $va->minid)
            ->delete();
        }
        return 'success';
    }

}
