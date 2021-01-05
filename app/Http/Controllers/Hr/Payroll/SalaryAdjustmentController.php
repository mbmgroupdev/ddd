<?php

namespace App\Http\Controllers\Hr\Payroll;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\Location;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use DB, stdClass;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class SalaryAdjustmentController extends Controller
{
    public function index(Request $request)
    {
    	$input = $request->all();
    	if(!isset($input['month_year'])){
    		$input['month_year'] = date('Y-m');
    	}
    	$unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->orderBy('hr_unit_short_name', 'desc')
        ->pluck('hr_unit_short_name', 'hr_unit_id');

        $locationList  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');
    	return view('hr.payroll.salary_adjustment_list', compact('input', 'unitList', 'locationList'));
    }
    public function data(Request $request)
    {
    	$input = $request->all();
    	// employee basic sql binding
        $employeeData = DB::table('hr_as_basic_info');
        $employeeDataSql = $employeeData->toSql();
        $yearMonth = explode('-', $input['month_year']);
        $year = $yearMonth[0];
        $month = $yearMonth[1];
    	$query = DB::table('hr_salary_add_deduct AS s')
        ->where('s.year', $year)
        ->where('s.month', $month)
        ->whereIn('b.as_location', auth()->user()->location_permissions())
        ->whereIn('b.as_unit_id', auth()->user()->unit_permissions());
        $query->leftjoin(DB::raw('(' . $employeeDataSql. ') AS b'), function($join) use ($employeeData) {
            $join->on('b.associate_id','s.associate_id')->addBinding($employeeData->getBindings());
        });
        $data = $query->get();
    	$getDepartment = department_by_id();
    	$getDesignation = designation_by_id();
    	$getUnit = unit_by_id();
    	$getLocation = location_by_id();
    	return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('pic', function($data){
            	return '<img src="'.emp_profile_picture($data).'" class="small-image min-img-file">';
            })
            ->addColumn('associate_id', function($data) use ($input){
            	$month = $input['month_year'];
            	$jobCard = url("hr/operation/job_card?associate=$data->associate_id&month_year=$month");
            	return '<a href="'.$jobCard.'" target="_blank">'.$data->associate_id.'</a>';
            })
            ->addColumn('hr_unit_name', function($data) use ($getUnit){
            	return $getUnit[$data->as_unit_id]['hr_unit_short_name']??'';
            })
            ->addColumn('hr_location_name', function($data) use ($getLocation){
            	return $getLocation[$data->as_location]['hr_location_name']??'';
            })
            ->addColumn('as_name', function($data){
            	return $data->as_name. ' '.$data->as_contact;
            })
            ->addColumn('hr_department_name', function($data) use ($getDepartment){
            	return $getDepartment[$data->as_department_id]['hr_department_name']??'';
            })
            ->addColumn('hr_designation_name', function($data) use ($getDesignation){
            	return $getDesignation[$data->as_designation_id]['hr_designation_name']??'';
            })
            ->addColumn('action', function($data) use ($input){
            	return '';
            })
            ->rawColumns([
                'pic', 'associate_id', 'hr_unit_name', 'as_name', 'advp_deduct', 'hr_department_name', 'hr_designation_name', 'action'
            ])
            ->make(true);
    }
    public function include(Request $request)
    {
    	$input = $request->all();
    	if(!isset($input['month_year'])){
    		$input['month_year'] = date('Y-m');
    	}
    	return view('hr.payroll.salary_adjustment', compact('input'));
    }
    public function adjustEmployee(Request $request)
    {
        $input = $request->all();
        $data = array();
        // return $input;
        if(!empty($input['keyvalue'])){
            $yearMonth = explode('-', $input['month_year']);
            $year = $yearMonth[0];
            $month = $yearMonth[1];
            // add advp_deduct sql binding

            $queryData = DB::table('hr_as_basic_info AS b')
            ->select('b.associate_id','b.as_name','b.as_designation_id','b.as_department_id')
            ->where('b.as_status', '!=', 0)
            ->when(!empty($input['type']), function ($query) use($input){
                if($input['type'] == 'associateid'){
                    return $query->where('b.associate_id','LIKE','%'.$input['keyvalue'].'%')->orWhere('b.as_oracle_code','LIKE','%'.$input['keyvalue'].'%');
                }else{
                    return $query->where('b.as_name','LIKE','%'.$input['keyvalue'].'%');
                }
            })
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions());
            
            $getEmployee = $queryData->limit(10)->get();
            // return $getEmployee;
            $getDepartment = department_by_id();
            $getDesignation = designation_by_id();
            foreach ($getEmployee as $emp) {
                $empAdjust = new stdClass();
                $adjust = DB::table('hr_salary_add_deduct AS s')
                ->where('s.associate_id', $emp->associate_id)
                ->where('s.year', $year)
                ->where('s.month', $month)
                ->first();
                $empAdjust->associate = $emp->associate_id;
                $empAdjust->name = $emp->as_name;
                $empAdjust->designation = $getDesignation[$emp->as_designation_id]['hr_designation_name']??'';
                $empAdjust->department = $getDepartment[$emp->as_department_id]['hr_department_name']??'';
                if($adjust != null){
                    $empAdjust->advdeduct = $adjust->advp_deduct;
                    $empAdjust->cgdeduct = $adjust->cg_deduct;
                    $empAdjust->fooddeduct = $adjust->food_deduct;
                    $empAdjust->otherdeduct = $adjust->others_deduct;
                    $empAdjust->salaryadd = $adjust->salary_add;
                }else{
                    $empAdjust->advdeduct = 0;
                    $empAdjust->cgdeduct = 0;
                    $empAdjust->fooddeduct = 0;
                    $empAdjust->otherdeduct = 0;
                    $empAdjust->salaryadd = 0;
                }
                $data[] = $empAdjust;
            }
        }
        
        return $data;
    }

    public function adjustStore(Request $request)
    {
        $input = $request->all();
        try {
            $yearMonth = explode('-', $input['month_year']);
            $year = $yearMonth[0];
            $month = $yearMonth[1];
            $countEmp = count($input['associate']);
            for ($i=0; $i < $countEmp; $i++) { 
                if($input['associate'][$i] != '' || $input['name'][$i] != ''){
                    
                    $associate = $input['associate'][$i];
                    $info = Employee::select('as_name','as_id', 'as_unit_id')->where('associate_id',$associate)->first();
                    $lock['unit_id'] = $info->as_unit_id;
                    $lock['month'] = $month;
                    $lock['year'] = $year;
                    $checkLock = monthly_activity_close($lock);
                    // return $info;
                    if($checkLock == 1){
                        toastr()->success('This month already locked!');
                        continue;
                    }
                    $adjust = DB::table('hr_salary_add_deduct AS s')
                    ->where('s.associate_id', $associate)
                    ->where('s.year', $year)
                    ->where('s.month', $month)
                    ->first();
                    if($adjust != null){
                        DB::table('hr_salary_add_deduct')
                        ->where('id', $adjust->id)
                        ->update([
                            'advp_deduct' => $input['advdeduct'][$i],
                            'cg_deduct' => $input['cgdeduct'][$i],
                            'food_deduct' => $input['fooddeduct'][$i],
                            'others_deduct' => $input['otherdeduct'][$i],
                            'salary_add' => $input['salaryadd'][$i]
                        ]);
                    }else{
                        DB::table('hr_salary_add_deduct')
                        ->insert([
                            'associate_id' => $associate,
                            'month' => $month,
                            'year' => $year,
                            'advp_deduct' => $input['advdeduct'][$i],
                            'cg_deduct' => $input['cgdeduct'][$i],
                            'food_deduct' => $input['fooddeduct'][$i],
                            'others_deduct' => $input['otherdeduct'][$i],
                            'salary_add' => $input['salaryadd'][$i]
                        ]);
                    }

                    $yearMonth = $year.'-'.$month;
                    if($month == date('m')){
                        $totalDay = date('d');
                    }else{
                        $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                    }
                    toastr()->success($info->as_name.' Salary Adjustment Successfully Done');
                    $tableName= get_att_table($info->as_unit_id);
                    $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $info->as_id, $totalDay))
                    ->onQueue('salarygenerate')
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
                }
            }
            
            return redirect("hr/payroll/monthly-salary-adjustment-list?month_year=$yearMonth");
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            // return $bug;
            toastr()->error($bug);
            return back();
        }
    }
}
