<?php
namespace App\Http\Controllers\Hr\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\User;
use App\Models\Hr\Voucher;
use App\Models\Hr\EmpType;
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use App\Models\Hr\Line;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\AttendanceBonus;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use DB, ACL,stdClass, PDF, Auth;


class VoucherController extends Controller
{
    public function index()
    {

    	return view('hr.operation.voucher.index');
    }

   

    public function voucher(Request $request)
    {
		$employee = get_employee_by_id($request->associate);

		$voucher = new Voucher();
		$voucher->associate_id = $request->associate;
		$voucher->type = $request->type;
		$voucher->amount = $request->amount;
		$voucher->description = $request->description;
		$voucher->manager_id = $request->manager;
		$voucher->status = 0;
		$voucher->created_by = auth()->id();
		$voucher->save();

		$view =  view('hr.operation.voucher.maternity_voucher', compact('voucher','employee'))->render();

		return response(['view' => $view]);
    }

    
    public function partial()
    {
        return view('hr.operation.voucher.partial_index');
    }

    public function partialGenerate(Request $request)
    {
        $month = explode('-', $request->slary_date);
        $employee = get_employee_by_id($request->associate);
       

        $salary = $this->processPartialSalary($employee, $request->slary_date);

        

        $view =  view('hr.operation.voucher.partial_salary', compact('salary','employee' ))->render();
        return response(['view' => $view]);

    }

    public function disburse(Request $request)
    {

        $getSalary = HrMonthlySalary::
                    where('as_id', $request->as_id)
                    ->where('month', $request->month)
                    ->where('year', $request->year)
                    ->first();

        $salary = $request->except('_token');
        if($getSalary == null){
            DB::table('hr_monthly_salary')->insert($salary);
        }else{
            DB::table('hr_monthly_salary')->where('id', $getSalary->id)->update($salary);  
        }

        return back()->with('success', 'Partial salary has been processed');

    }


    public function productionBonus()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name', 'hr_unit_id');
        $areaList = Area::where('hr_area_status',1)->pluck('hr_area_name','hr_area_id');

    	return view('hr.operation.production_bonus',compact('unitList','areaList'));
    }


    public function storeProductionBonus(Request $request)
    {
        if(empty($request->assigned)){
            return back()->with('error', 'Select at least One Employee');
        }
        else{
            $list_emp = '';
            $monthYear = explode("-", $request->month);
            foreach ($request->assigned as $as_id => $associate_id) {
                $pb = SalaryAddDeduct::firstOrNew(
                    ['associate_id' => $associate_id, 'year' => $monthYear[0], 'month' => $monthYear[1]],
                    ['bonus_add' => $request->amount ]
                );
                $pb->save();
                $list_emp .= $associate_id.', ';
            }
            log_file_write('Production bonus added for ', $list_emp);
            return back()->with('success', 'Production bonus saved');
          
        }
    }

    public function productionList()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name', 'hr_unit_id');
        return view('hr.operation.production_bonus_list', compact('unitList'));
    }


    public function productionListData()
    {
        $data = DB::table('hr_salary_add_deduct AS ad')
            ->select([
                'ad.*',
                'b.as_name',
                'u.hr_unit_short_name',
                'f.hr_floor_name',
                'l.hr_line_name',
                'a.hr_area_name',
                'dp.hr_department_name',
                'dg.hr_designation_name',
                's.hr_section_name',
                'b.as_gender',
                'b.as_ot',
                'b.as_contact',
                'b.as_status',
                'b.as_oracle_code',
                'b.as_rfid_code'
            ])
            ->leftJoin('hr_as_basic_info As b','ad.associate_id','=','b.associate_id')
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_section AS s', 's.hr_section_id', '=', 'b.as_section_id')
            ->where('ad.bonus_add', '>', 0)
            ->get();


        return Datatables::of($data)
            ->editColumn('month', function($data){
                return Carbon::create($data->year,$data->month,1,0)->format('F, Y');
            })
            ->editColumn('action', function ($data) {
                $return = "<a href=".url('hr/recruitment/employee/show/'.$data->associate_id)." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Trash\">
                        <i class=\"ace-icon fa fa-trash bigger-120 fa-fw\"></i>
                    </a>";
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'month',
                'action'
            ])
            ->make(true);
    }

    public function processPartialSalary($employee, $salary_date)
    {
        $month = explode('-', $salary_date);
        $first_day = Carbon::create($salary_date)->firstOfMonth()->format('Y-m-d');

        $table = get_att_table($employee->as_unit_id);
        $att = DB::table($table)
                ->select(
                    DB::raw('COUNT(*) as present'),
                    DB::raw('SUM(ot_hour) as ot_hour'),
                    DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late'),
                    DB::raw('COUNT(CASE WHEN remarks ="HD" THEN 1 END) AS halfday')
                )
                ->where('as_id',$employee->as_id)
                ->where('in_date','>=',$first_day)
                ->where('in_date','<=', $salary_date)
                ->first();

        $late = $att->late??0;
        $overtimes = $att->ot_hour??0; 
        $present = $att->present??0;
        $halfCount = $att->halfday??0;
        $getSalary = DB::table('hr_monthly_salary')
                    ->where([
                        'as_id' => $employee->associate_id,
                        'month' => $month[1],
                        'year' => $month[0]
                    ])
                    ->first();

        // get holiday employee wise
        
        $yearMonth = $month[0].'-'.$month[1];
        $empdoj = $employee->as_doj;
        $empdojMonth = date('Y-m', strtotime($employee->as_doj));
        $empdojDay = date('d', strtotime($employee->as_doj));

        if($employee->shift_roaster_status == 1){
            // check holiday roaster employee
            $holidayCount = HolidayRoaster::where('year', $month[0])
            ->where('month', $month[1])
            ->where('date','<=', $salary_date)
            ->where('as_id', $employee->associate_id)
            ->where('remarks', 'Holiday')
            ->count();
        }else{
            // check holiday roaster employee
            $RosterHolidayCount = HolidayRoaster::where('year', $month[0])
            ->where('month', $month[1])
            ->where('date','<=', $salary_date)
            ->where('as_id', $employee->associate_id)
            ->where('remarks', 'Holiday')
            ->count();
            // check General roaster employee
            $RosterGeneralCount = HolidayRoaster::where('year', $month[0])
            ->where('month', $month[1])
            ->where('date','<=', $salary_date)
            ->where('as_id', $employee->associate_id)
            ->where('remarks', 'General')
            ->count();
             // check holiday shift employee
            
            if($empdojMonth == $yearMonth){
                $shiftHolidayCount = YearlyHolyDay::
                    where('hr_yhp_unit', $employee->as_unit_id)
                    ->where('hr_yhp_dates_of_holidays','>=', $first_day)
                    ->where('hr_yhp_dates_of_holidays','<=', $salary_date)
                    ->where('hr_yhp_dates_of_holidays','>=', $empdoj)
                    ->where('hr_yhp_open_status', 0)
                    ->count();
            }else{
                $shiftHolidayCount = YearlyHolyDay::
                    where('hr_yhp_unit', $employee->as_unit_id)
                    ->where('hr_yhp_dates_of_holidays','>=', $first_day)
                    ->where('hr_yhp_dates_of_holidays','<=', $salary_date)
                    ->where('hr_yhp_open_status', 0)
                    ->count();
            }
            
            if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
            }else{
                $getHoliday = $shiftHolidayCount;
            }
        }


        // get absent employee wise
        $getAbsent = DB::table('hr_absent')
            ->where('associate_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $salary_date)
            ->count();

        // get leave employee wise

        $leaveCount = DB::table('hr_leave')
        ->select(
            DB::raw("SUM(DATEDIFF(leave_to, leave_from)+1) AS total")
        )
        ->where('leave_ass_id', $employee->associate_id)
        ->where('leave_status', 1)
        ->where('leave_from', '>=', $first_day)
        ->where('leave_to', '<=', $salary_date)
        ->first()->total??0;

        // get salary add deduct id form salary add deduct table
        $getAddDeduct = SalaryAddDeduct::
        where('associate_id', $employee->associate_id)
        ->where('month',  $month[1])
        ->where('year',  $month[0])
        ->first();

        if($getAddDeduct != null){
            $deductCost = ($getAddDeduct->advp_deduct + $getAddDeduct->cg_deduct + $getAddDeduct->food_deduct + $getAddDeduct->others_deduct);
            $deductSalaryAdd = $getAddDeduct->salary_add;
            $deductId = $getAddDeduct->id;
        }else{
            $deductCost = 0;
            $deductSalaryAdd = 0;
            $deductId = null;
        }

        //get add absent deduct calculation
        $perDayBasic = round(($employee->ben_basic / 30),2);
        $perDayGross = round(($employee->ben_current_salary / 30),2);
        $getAbsentDeduct = $getAbsent * $perDayBasic;
        $getHalfDeduct = $halfCount * ($perDayBasic / 2);
        //stamp = 10 by default all employee;
        
        if($employee->as_ot == 1){
            $overtime_rate = number_format((($employee->ben_basic/208)*2), 2, ".", "");
        } else {
            $overtime_rate = 0;
        }
        $overtime_salary = 0;
        

        $attBonus = 0;
        $totalLate = $late;
        $salary_date = $present + $getHoliday + $leaveCount;
        
        $salary = [
            'as_id' => $employee->associate_id,
            'month' => $month[1],
            'year'  => $month[0],
            'gross' => $employee->ben_current_salary,
            'basic' => $employee->ben_basic,
            'house' => $employee->ben_house_rent,
            'medical' => $employee->ben_medical,
            'transport' => $employee->ben_transport,
            'food' => $employee->ben_food,
            'late_count' => $late,
            'present' => $present,
            'holiday' => $getHoliday,
            'absent' => $getAbsent,
            'leave' => $leaveCount,
            'absent_deduct' => $getAbsentDeduct,
            'half_day_deduct' => $getHalfDeduct,
            'salary_add_deduct_id' => $deductId,
            'ot_rate' => $overtime_rate,
            'ot_hour' => $overtimes,
            'attendance_bonus' => $attBonus,
        ];
        $salary['per_day_basic'] = $perDayBasic;
        $salary['per_day_gross'] = $perDayGross;
        $salary['salary_date'] = $salary_date;
        
        $stamp = 10;

        if($employee->as_emp_type_id == 3){
            $stamp = 0;
        }
        
        // get salary payable calculation
        $salaryPayable = round((($perDayGross*$salary_date) - ($getAbsentDeduct + $getHalfDeduct + ($deductCost))),2);
        $totalPayable = round(($salaryPayable + ($overtime_rate*$overtimes)),2);
        $salary['deduct'] = $deductCost;
        if($totalPayable > 1000){
            $salaryPayable = $salaryPayable - $stamp;
            $totalPayable = $totalPayable - $stamp;
            $salary['deduct'] = (($deductCost) + $stamp);
        }
        $salary['total_payable'] = $totalPayable;
        $salary['salary_payable'] = $salaryPayable;

        return $salary;
    }

     public function test()
    {
        $employee = DB::table('hr_as_basic_info')
                    ->where('as_doj', '>','2020-08-04')
                    ->where('as_ot', 1)
                    ->whereNotIn('as_location', [12,13])
                    ->whereIn('as_unit_id', [1,4,5])
                    ->pluck('associate_id');
        
        foreach ($employee as $key => $emp) {
            HolidayRoaster::firstOrCreate(
                ['as_id' => $emp, 'date' => '2020-09-18'],
                [
                    'year' => '2020',
                    'month' => '09',
                    'as_id' => $emp,
                    'date' => '2020-09-18',
                    'remarks' => 'OT',
                    'comment' => 'Instead of 2020-08-04'
                ]
            );
        }

        
    }
}