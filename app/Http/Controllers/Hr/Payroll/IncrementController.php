<?php

namespace App\Http\Controllers\Hr\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Models\Hr\Designation;
use App\Models\Hr\SalaryStructure;
use App\Models\Hr\Unit;
use App\Models\Hr\EmpType;
use App\Models\Employee; 
use App\Models\Hr\Increment;
use App\Models\Hr\Promotion;
use App\Models\Hr\FixedSalary;
use App\Models\Hr\IncrementType;
use App\Models\Hr\OtherBenefits;
use App\Models\Hr\OtherBenefitAssign;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\SalaryAdjustDetails;
use App\Jobs\ProcessUnitWiseSalary;
use Carbon\Carbon;
use Validator,DB, DataTables, ACL,Auth;

class IncrementController extends Controller
{
    public function index(Request $request)
    {

        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        $floorList= [];
        $lineList= [];
 
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

        $deptList= [];

        $sectionList= [];

        $subSectionList= [];

        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');

        $data['salaryMin']      = Benefits::getSalaryRangeMin();
        $data['salaryMax']      = Benefits::getSalaryRangeMax();


        return view('hr.payroll.increment.index', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList', 'data','employeeTypes'));

    }

    public function incrementList()
    {
        return view('hr/payroll/increment_list');
    }

    public function incrementListData(Request $request)
    {

        $year = $request->year??date('Y');
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $unit = unit_by_id();

        $data= DB::table('hr_increment AS inc')
                ->select([
                    'inc.*',
                    'b.as_name',
                    'b.as_oracle_code',
                    'b.as_emp_type_id',
                    'b.as_gender',
                    'b.as_doj',
                    'b.as_section_id',
                    'b.as_department_id',
                    'b.as_designation_id',
                    'b.as_unit_id',
                    'bn.hr_bn_associate_name',
                ])
                ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'inc.associate_id')
                ->where('applied_date', '>=', $year.'-01-01')
                ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', 'b.associate_id')
                ->where('applied_date', '<=', $year.'-12-31')
                ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('b.as_location', auth()->user()->location_permissions())
                ->orderBy('inc.effective_date','desc')
                ->orderBy('inc.created_at','desc')
                ->get();

        $perm = check_permission('Manage Increment');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) use ($perm, $designation,$section,$department) {
                $button = '<div class=\"btn-group\">';
                if($perm){

                    $button .= "<a type=\"button\" href=".url('hr/payroll/increment_edit/'.$data->id)." class=\"btn btn-sm btn-primary\"><i class=\"fa fa-pencil\"></i></a>";
                    if($data->status == 1){
                        if($data->as_emp_type_id == 3){
                            $letter = array(
                                'name' => $data->hr_bn_associate_name,
                                /*'salary' => eng_to_bn(bn_money($data->current_salary)),
                                'inc' => eng_to_bn(bn_money($data->increment_amount)),
                                'new_salary' => eng_to_bn(bn_money(($data->current_salary + $data->increment_amount))),*/
                                'designation' => $designation[$data->as_designation_id]['hr_designation_name_bn']??'',
                                'section' => $section[$data->as_section_id]['hr_section_name_bn']??'',
                                'effective_date' => eng_to_bn($data->effective_date),
                                'associate_id' => $data->associate_id
                            );

                            $button .=" <button type=\"button\" onclick='printLetter(".json_encode($letter).")' class=\"btn btn-sm btn-danger\"><i class=\"fa fa-print\"></i></button"; 
                        }else{
                            $letter = array(
                                'name' => $data->as_name,
                                'designation' => $designation[$data->as_designation_id]['hr_designation_name']??'',
                                /*'salary' => bn_money($data->current_salary),
                                'inc' => bn_money($data->increment_amount),
                                'new_salary' => bn_money(($data->current_salary + $data->increment_amount)),*/
                                'department' => $department[$data->as_department_id]['hr_department_name']??'',
                                'effective_date' => $data->effective_date,
                                'associate_id' => $data->associate_id,
                                'doj' => $data->as_doj
                            );

                            $button .=" <button type=\"button\" onclick='printEnLetter(".json_encode($letter).")' class=\"btn btn-sm btn-danger\"><i class=\"fa fa-print\"></i></button";
                        }

                    }
                }

                $button .= '</div>';

                return $button;
            })
            ->addColumn('designation', function ($data) use ($designation) {
                return $designation[$data->as_designation_id]['hr_designation_name']??'';
            })
            ->editColumn('increment_type', function($data){
                return $data->increment_type == 2?'Yearly':'Special';
            })
            ->rawColumns(['action','designation','increment_type'])
            ->make(true);  
                             
    }

    public function storeIncrement(Request $request)
    {
        $created_by= Auth::user()->associate_id;

        if(empty($request->associate_id) || !is_array($request->associate_id))
        {
            return back()
                ->withInput()
                ->with('error', 'Please select at least one associate.');
        }
        
        // Declare and define two dates 
        $date1 = strtotime($request->applied_date);  
        $date2 = strtotime($request->effective_date);  
        //extract years
        $year1 = date('Y', $date1);
        // $year2 = date('Y', $date2);
        $year2 = date('Y');
        //extract month
        $month1 = date('m', $date1);
        // $month2 = date('m', $date2);
        $month2 = date('m');
        //month difference
        $month_diff = (($year2 - $year1) * 12) + ($month2 - $month1);

        for($i=0; $i<sizeof($request->associate_id); $i++)
        {
            $salary= DB::table('hr_benefits')
                            ->where('ben_as_id', $request->associate_id[$i])
                            ->pluck('ben_current_salary')
                            ->first();

            $doj= DB::table('hr_as_basic_info')
                    ->where('associate_id',$request->associate_id[$i] )
                    ->pluck('as_doj')
                    ->first();
            $eligible_at = date("Y-m-d", strtotime("$doj + 1 year"));
            // $eligible_at = $request->elligible_date;        

            $increment= new Increment();
            $increment->associate_id = $request->associate_id[$i] ;
            $increment->current_salary = $salary;
            $increment->increment_type = $request->increment_type;
            $increment->increment_amount = $request->increment_amount ;
            $increment->amount_type = $request->amount_type ;
            $increment->applied_date = $request->applied_date ;
            $increment->eligible_date = $eligible_at ;
            $increment->effective_date = $request->effective_date ;
            $increment->status = 0 ;
            $increment->created_by = $created_by;
            $increment->created_at = date('Y-m-d H:i:s') ;
            $increment->save();

            log_file_write("Increment Entry Saved", $increment->id);


            //Keeping the not given increment amount---- SalaryAdjustMaster, SalaryAdjustDetails
             $basic = DB::table('hr_benefits')
                            ->where('ben_as_id', $request->associate_id[$i])
                            ->pluck('ben_basic')
                            ->first();
             if($request->amount_type == 1){
                    $_amount = $request->increment_amount;
                }
             else{
                    $_amount = ($basic/100)*$increment->increment_amount;
                }

             $y = (int)$year1;
             $m = (int)$month1;

             for($j=0; $j<$month_diff; $j++ ){
                        $master = new SalaryAdjustMaster();
                        $master->associate_id = $request->associate_id[$i];
                        if($m > 12){
                            $m=1;
                            $y+=1;
                            $master->month    = $m;
                            $master->year     = $y;
                            $m+=1;
                        }
                        else{
                            $master->month    = $m;
                            $master->year     = $y;
                            $m+=1;
                        }
                        
                        $master->save();

                        $detail = new SalaryAdjustDetails();
                        $detail->salary_adjust_master_id = $master->id;
                        $detail->date                    = date('Y-m-d');
                        $detail->amount                  = $_amount;
                        $detail->type                    = 3;
                        $detail->save();
                    
             }   
            
        }


        return back()
            ->with('success', "Increment Saved Successfully!");
    }

    //Edit Increment
    public function editIncrement($id){

        $unitList = Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $typeList= IncrementType::pluck('increment_type', 'id');
        $increment= DB::table('hr_increment AS inc')
                        ->where('id', $id)
                        ->select([
                            'inc.*',
                            'b.as_emp_type_id',
                            'b.as_unit_id'
                        ])
                        ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'inc.associate_id')
                        ->first();
        return view('hr/payroll/increment_edit', compact('unitList', 'employeeTypes', 'typeList', 'increment'));
    }

    //Update Increment
    public function updateIncrement(Request $request){

        Increment::where('id', $request->increment_id)
                    ->update([
                          "increment_type"      => $request->increment_type,
                          "applied_date"        => $request->applied_date,
                          "effective_date"      => $request->effective_date,
                          "increment_amount"    => $request->increment_amount,
                          "amount_type"         => $request->amount_type
                    ]);

        log_file_write("Increment Updated", $request->increment_id);
        return back()
            ->with('success', "Increment updated Successfully!");
    }

    //Increment corn job
    public function incrementJobs()
    {
        $data = DB::table('hr_increment as ic')
                ->select('ic.*','a.as_id','a.as_unit_id','a.as_status','b.*')
                ->leftJoin('hr_as_basic_info as a','a.associate_id','ic.associate_id')
                ->leftJoin('hr_benefits as b','b.ben_as_id','ic.associate_id')
                ->where('ic.effective_date','<=',date('Y-m-d'))
                ->where('ic.status', 0)
                ->get();
                
        

        foreach ($data as $key => $d) {
            $gross = $d->current_salary + $d->increment_amount;
            $up['ben_current_salary'] = $gross;
            $up['ben_basic'] = ceil(($gross-1850)/1.5);
            $up['ben_house_rent'] = $gross -1850 - $up['ben_basic'];

            if($d->ben_bank_amount > 0 && $d->ben_cash_amount > 0){
                $up['ben_cash_amount'] = $gross - $d->ben_bank_amount;
            }else if ($d->ben_bank_amount > 0 && $d->ben_cash_amount == 0){
                $up['ben_bank_amount'] = $gross;
                $up['ben_cash_amount'] = 0;
            }else{
                $up['ben_bank_amount'] = 0;
                $up['ben_cash_amount'] = $gross;
            }

            DB::table('hr_benefits')->where('ben_id', $d->ben_id)->update($up);
            DB::table('hr_increment')->where('id', $d->id)->where('associate_id', $d->associate_id)->update(['status' => 1]);

            $tableName = get_att_table($d->as_unit_id);

            if($d->as_status == 1){

                $queue = (new ProcessUnitWiseSalary($tableName, date('m'), date('Y'), $d->as_id, date('d')))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            }

        }
        
    }


    public function getGazzeteEmployee()
    {

    }

    public function getEmployeeSpecialList(Request $request)
    {

        $data = DB::table('hr_as_basic_info as b')
                ->select(
                    'b.as_id',
                    'b.as_name',
                    'b.associate_id',
                    'b.as_oracle_code',
                    'b.as_emp_type_id',
                    'b.as_unit_id',
                    'b.as_location',
                    'b.as_department_id',
                    'b.as_area_id',
                    'b.as_floor_id',
                    'b.as_line_id',
                    'b.as_section_id',
                    'b.as_subsection_id',
                    'b.as_designation_id', 
                    'b.as_gender',
                    'b.as_doj',
                    'b.as_pic',
                    'ben.ben_current_salary',
                    DB::raw('CEIL((ben.ben_current_salary - 1850)*0.05) as inc'),
                    DB::raw('MONTH(as_doj) AS doj_month')
                )
                ->leftJoin('hr_benefits as ben','ben.ben_as_id','b.associate_id')
                ->whereIn('b.associate_id', $request->associate_id)
                ->orderBy('as_oracle_sl','ASC')
                ->get();

        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();

        $date = date('Y-m-01');
        $effective_date = Carbon::parse(date('Y-m-01'));

        return view('hr.payroll.increment.employee_wise', compact('data','unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area','effective_date','date','request'))->render();

    }

    public function getEligibleList(Request $request)
    {
        $input = $request->all();

        $request_associates = DB::table('hr_as_basic_info as b')
            ->leftJoin('hr_benefits as ben','ben.ben_as_id','b.associate_id')
            ->where('b.as_status',1)
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('b.as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('b.as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('b.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('b.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('b.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('b.as_floor_id',$input['floor_id']);
            })
            ->when($input['as_ot']!=null, function ($query) use($input){
               return $query->where('b.as_ot',$input['as_ot']);
            })
            ->when($input['emp_type']!=null, function ($query) use($input){
               return $query->where('b.as_emp_type_id',$input['emp_type']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('b.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('b.as_subsection_id', $input['subSection']);
            })
            ->where('ben.ben_current_salary','>=' ,$input['min_salary'])
            ->where('ben.ben_current_salary','<=' ,$input['max_salary'])
            ->pluck('b.associate_id')->toArray();

        $inc_month = $request->month;
        $date = $request->month.'-01';
        $inc_year = date('Y', strtotime($date));
        $effective_date = Carbon::parse($date);
        $range_start = $effective_date->copy()->subMonths(11)->toDateString();
        $range_end = $effective_date->copy()->endOfMonth()->toDateString();


        $gazette_date = '2018-12-01';
        $gazette_month = date('m', strtotime($gazette_date)) ;
        $isGazette = $gazette_month == date('m', strtotime($inc_month))?1:0;
        $eligible_date = Carbon::parse($range_end)->subYear()->endOfMonth()->toDateString();
        
        $increment = DB::table('hr_increment')
                     ->where('increment_type', 2)
                     ->where('applied_date','>=',$range_start)
                     ->where('applied_date','<=',$range_end)
                     ->pluck('associate_id')->toArray();

        // if gazette month gazzette employee will be added
        $gazette = DB::table('hr_as_basic_info')
                    ->where('as_doj', '<=', $gazette_date)
                    ->where('as_emp_type_id', 3)
                    ->whereIn('associate_id', $request_associates)
                    ->whereNotIn('associate_id', $increment)
                    ->pluck('associate_id')->toArray();

        $no_associate = $increment;

        if($isGazette == 0){
           $no_associate = array_merge($increment,$gazette);
        }

        $eligible = DB::table('hr_as_basic_info')
                    ->where('as_doj','<=',$eligible_date)
                    ->whereIn('associate_id', $request_associates)
                    ->whereNotIn('associate_id',$no_associate)
                    ->pluck('associate_id')->toArray();

        if($isGazette == 1){
            $eligible = array_merge($eligible,$gazette);
        }



        $data = DB::table('hr_as_basic_info as b')
                ->select(
                    'b.as_id',
                    'b.as_name',
                    'b.associate_id',
                    'b.as_oracle_code',
                    'b.as_emp_type_id',
                    'b.as_unit_id',
                    'b.as_location',
                    'b.as_department_id',
                    'b.as_area_id',
                    'b.as_floor_id',
                    'b.as_line_id',
                    'b.as_section_id',
                    'b.as_subsection_id',
                    'b.as_designation_id', 
                    'b.as_gender',
                    'b.as_pic',
                    'b.as_doj',
                    'ben.ben_current_salary',
                    DB::raw('CEIL((ben.ben_current_salary - 1850)*0.05) as inc'),
                    DB::raw('MONTH(as_doj) AS doj_month')
                )
                ->leftJoin('hr_benefits as ben','ben.ben_as_id','b.associate_id')
                ->whereIn('b.associate_id', $eligible)
                ->orderBy('as_oracle_sl','ASC')
                ->get();

        if($request->type == 'running' ){

            $data = collect($data)->filter(function ($item) use ($inc_month, $isGazette, $gazette) {
                            if($item->doj_month == date('m', strtotime($inc_month)) || ($isGazette == 1 && in_array($item->associate_id, $gazette))){
                                return $item;
                            }
                        })->values()->toArray();
        }else if($request->type == 'pending'){
            $data = collect($data)->filter(function ($item) use ($inc_month, $isGazette, $gazette) {
                            if($item->doj_month != date('m', strtotime($inc_month)) && (!in_array($item->associate_id, $gazette))){
                                return $item;
                            }
                        })->values()->toArray();

        }

        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();

        return view('hr.payroll.increment.eligible-list', compact('data','unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area','effective_date','gazette','inc_year','date','request'))->render();

    }

    public function incrementAction(Request $request)
    {
        $created_by= Auth::user()->associate_id;

        //return (count($request->increment));
        if(empty($request->increment) || !is_array($request->increment))
        {
            return response([
                'msg' => 'Please select at least one associate.',
                'status' => 'failed'
            ]);
        }
        
        $increment = $request->increment;
        $count = 0;
        $benefits = DB::table('hr_benefits as b')
                    ->select('a.as_id','a.as_unit_id','a.as_status','b.*')
                    ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                    ->get()
                    ->keyBy('ben_as_id');

        foreach ($increment as $key => $v) {

            if(isset($v['status'])){
                $count++;
                $inc = new Increment();
                $inc->associate_id = $key;
                $inc->current_salary = $v['salary'];
                $inc->increment_type = $request->increment_type;
                $inc->increment_amount = $v['amount'] ;
                $inc->amount_type = 1 ;
                $inc->eligible_date =  $request->effective_date;
                $inc->effective_date = $request->effective_date;
                $inc->applied_date = $v['date'] ;
                $inc->status = 0 ;
                $inc->created_by = auth()->id();
                $inc->save();

            }
            $amount = $v['salary'] + $v['amount'];
            if($inc->effective_date <= date('Y-m-d')){
                $this->reflectBenefit($benefits[$key], $amount, $key, $request->effective_date);
                $inc->status = 1 ;
                $inc->save();
            }
        }

        return response([
                'msg' => 'Increment information saved successfully!',
                'status' => 'success'
        ]);


    }

    public function reflectBenefit($b, $amount, $associate, $date)
    {
        $ss =  \Cache::remember('salary_structure', 10000000, function () {
            return DB::table('hr_salary_structure AS s')
                    ->where('status', 1)
                    ->select('s.*')
                    ->orderBy('id', 'DESC')
                    ->first();
        }); 
        

       
        $allowance = $ss->medical + $ss->transport+$ss->food;
        $ben_basic= (($amount - $allowance / $ss->basic));
        //$ben_basic= (($amount/100)*$ss->basic);
        $ben_house_rent = $amount - $allowance;

        if($b->ben_bank_amount > 0 && $b->ben_cash_amount > 0){
            $ben_bank_amount = $b->ben_bank_amount;
            $ben_cash_amount = $amount - $ben_bank_amount;
        }else if($b->ben_bank_amount > 0 && $b->ben_cash_amount == 0){
            $ben_bank_amount = $amount;
            $ben_cash_amount = 0;
        }else{
            $ben_bank_amount = 0;
            $ben_cash_amount = $amount;
        }

        DB::table('hr_benefits')
            ->where('ben_as_id', $associate)
            ->update([
                'ben_current_salary' => $amount,
                'ben_basic' => $ben_basic,
                'ben_house_rent' => $ben_house_rent,
                'ben_cash_amount' => $ben_cash_amount,
                'ben_bank_amount' => $ben_bank_amount
            ]);

        $tableName = get_att_table($b->as_unit_id);

        if($b->as_status == 1){
            
            
                // check monthly salary lock
              $month = date('m', strtotime($date));
              $year = date('Y', strtotime($date));
              $t = date('t', strtotime($date));
              
              $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $b->as_id, $t))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
              
            
        }
    }
}