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

	    $data['salaryMin']      = Benefits::getSalaryRangeMin();
	    $data['salaryMax']      = Benefits::getSalaryRangeMax();


	    return view('hr.payroll.increment.index', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList', 'data'));

    }

    public function incrementList()
    {
        return view('hr/payroll/increment_list');
    }

    public function incrementListData(Request $request)
    {
        $year = $request->year??date('Y');
        $data= DB::table('hr_increment AS inc')
                    ->where('status', 0)
                    ->select([
                        'inc.id',
                        'inc.associate_id',
                        'b.as_name',
                        'inc.increment_type',
                        'inc.increment_amount',
                        'inc.amount_type',
                        'inc.eligible_date',
                        'inc.effective_date',
                        'c.increment_type AS inc_type_name',
                    ])
                    ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'inc.associate_id')
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->leftJoin('hr_increment_type AS c', 'c.id', 'inc.increment_type' )
                    ->whereYear('effective_date', $year)
                    ->orderBy('inc.effective_date','desc')
                    ->get();

        $perm = check_permission('Manage Increment');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) use ($perm) {
                if($perm){

                    return "<div class=\"btn-group\">
                        <a type=\"button\" href=".url('hr/payroll/increment_edit/'.$data->id)." class=\"btn btn-xs btn-primary\"><i class=\"fa fa-pencil\"></i></a>
                    </div>";
                }else{
                    return '';
                }
            })
            ->editColumn('effective_date', function($data){
                return date('Y-m-d', strtotime($data->effective_date));
            })
            ->rawColumns(['action'])
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

        // dd($request->applied_date, $request->effective_date,"Difference: ".$month_diff.' Months ');



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
                    //-----------master data insert
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


                    //-----------details insert
                        $detail = new SalaryAdjustDetails();
                        $detail->salary_adjust_master_id = $master->id;
                        $detail->date                    = date('Y-m-d');
                        $detail->amount                  = $_amount;
                        $detail->type                    = 3;
                        $detail->save();
                    
             }   
            //-----------------------------------------------------------------------------
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
    public function incrementJobs(){

        $today= date('Y-m-d');
        $todays_increment= DB::table('hr_increment')
                ->where('effective_date', '<=', $today)
                ->where('status', 0)
                ->limit(10)
                ->get();

        $salary_structure= DB::table('hr_salary_structure AS s')
                                ->where('status', 1)
                                ->select('s.*')
                                ->orderBy('id', 'DESC')
                                ->first();

        if(!empty($todays_increment) && !empty($salary_structure)){

            foreach ($todays_increment as $key => $increment) {

                if($increment->amount_type ==1)
                {
                    $ben_current_salary= $increment->current_salary+ $increment->increment_amount;
                }
                else{
                    $ben_current_salary= $increment->current_salary+ (($increment->current_salary/100)*$increment->increment_amount);
                }

                $ben_medical= $salary_structure->medical;
                $ben_transport= $salary_structure->transport;
                $ben_food= $salary_structure->food;

                $ben_basic= (($ben_current_salary-($salary_structure->medical+$salary_structure->transport+$salary_structure->food))/$salary_structure->basic);
                //$ben_basic= (($ben_current_salary/100)*$salary_structure->basic);
                $ben_house_rent= $ben_current_salary - ($ben_basic+$salary_structure->medical+$salary_structure->transport+$salary_structure->food);
                

                $bank= DB::table('hr_benefits')->where('ben_as_id', $increment->associate_id)
                            ->where('ben_status', 1)
                            ->first();

                //paid in bank
                if(!empty($bank)){
                    if($bank->ben_bank_amount>= $ben_current_salary ){
                        $bank_paid= $ben_current_salary;
                        $cash_paid= 0;
                    }
                    else{
                        $bank_paid= $bank->ben_bank_amount;
                        $cash_paid= $ben_current_salary-$bank->ben_bank_amount;
                    }
                }
                else{
                    $bank_paid= $ben_current_salary;
                        $cash_paid= 0;
                }


                Benefits::where('ben_as_id', $increment->associate_id)
                    ->update([
                        'ben_cash_amount' => $cash_paid,
                        'ben_bank_amount' => $bank_paid,
                        'ben_current_salary' => $ben_current_salary,
                        'ben_basic' => $ben_basic,
                        'ben_house_rent' => $ben_house_rent,
                        'ben_medical' => $ben_medical,
                        'ben_transport' => $ben_transport,
                        'ben_food' => $ben_food
                        ]);

                $id = Benefits::where('ben_as_id', $increment->associate_id)->value('ben_id');
                log_file_write("Jobs Benefits Updated", $id );

                Increment::where('associate_id', $increment->associate_id)
                            ->where('status', 0)
                            ->update([
                                'status' => 1
                            ]);
            }
        }
    }


    public function getGazzeteEmployee()
    {

    }

    public function getEligibleList(Request $request)
    {
    	$inc_month = $request->month;
    	$effective_date = Carbon::parse($request->month.'-01');
    	$range_start = $effective_date->copy()->subYear()->toDateString();
    	$range_end = $effective_date->copy()->endOfMonth()->toDateString();


    	$gazette_date = '2018-12-01';
    	$eligible_date = Carbon::parse($range_end)->endOfMonth()->toDateString();

    	$increment = DB::table('hr_increment')
    				 ->where('effective_date','>=',$range_start)
    				 ->where('effective_date','<=',$range_end)
    				 ->pluck('associate_id')->toArray();

    	
    	$gazette = DB::table('hr_as_basic_info')
    				->where('as_doj', '<=', $gazette_date)
    				->where('as_emp_type_id', 3)
    				->whereIn('as_unit_id', auth()->user()->unit_permissions())
    				->whereIn('as_location', auth()->user()->location_permissions())
    				->pluck('associate_id')->toArray();

    	$no_associate = array_merge($increment,$gazette);

    	$eligible = DB::table('hr_as_basic_info')
    				->leftJoin('hr_benefits')
    				->where('as_doj','<=',$eligible_date)
    				->whereIn('as_unit_id', auth()->user()->unit_permissions())
    				->whereIn('as_location', auth()->user()->location_permissions())
    				->whereNotIn('associate_id',$no_associate)
    				->get();

    }
}