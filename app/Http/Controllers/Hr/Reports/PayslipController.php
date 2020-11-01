<?php

namespace App\Http\Controllers\Hr\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use App\Models\Hr\Location;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Department;
use App\Models\Employee;

use App\Models\Hr\Floor;
use DB, PDF;

class PayslipController extends Controller
{
	public function showForm(Request $request)
    {

		$data = $request->all();
		$unitList      = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_name', 'desc')
            ->pluck('hr_unit_name', 'hr_unit_id');
        $locationList  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');

         $areaList = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

        
        $salaryMin      = Benefits::getSalaryRangeMin();
        $salaryMax     = Benefits::getSalaryRangeMax();
        $getYear      = HrMonthlySalary::select('year')->distinct('year')->orderBy('year', 'desc')->pluck('year');

        $floorList= DB::table('hr_floor')
                        ->where('hr_floor_unit_id', $request->unit)
                        ->pluck('hr_floor_name', 'hr_floor_id');

        $deptList= DB::table('hr_department')
                        ->where('hr_department_area_id', $request->area)
                        ->pluck('hr_department_name', 'hr_department_id');
        $sectionList= DB::table('hr_section')
                        ->where('hr_section_department_id', $request->department)
                        ->pluck('hr_section_name', 'hr_section_id');


        $subSectionList= DB::table('hr_subsection')
                        ->where('hr_subsec_section_id', $request->section)
                        ->pluck('hr_subsec_name', 'hr_subsec_id');

        return view("hr/reports/payslip", compact(
            "data",
        	"unitList",
        	"areaList",
            "floorList",
            "deptList",
            "sectionList",
            "subSectionList",
						"getYear",
                        "locationList",
                        "salaryMin",
                        "salaryMax"
        ));
    }

    public function unitWise(Request $request)
    {
        $input = $request->all();
        $input['department'] = $input['department']??'';
        $input['section'] = $input['section']??'';
        $input['subSection'] = $input['subSection']??'';
        $input['month'] = date('m', strtotime($input['month_year']));
        $input['year'] = date('Y', strtotime($input['month_year']));
        try {

            $getUnit = Unit::getUnitNameBangla($input['unit']);
            $info = [];
            if(isset($input['area'])){
                $info['area'] = Area::where('hr_area_id',$input['area'])->first()->hr_area_name_bn??'';
            }
            if(isset($input['floor'])){
                $info['floor'] = Floor::where('hr_floor_id',$input['floor'])->first()->hr_floor_name_bn??'';
            }
            if(isset($input['department'])){
                $info['department'] = Department::where('hr_department_id',$input['department'])->first()->hr_department_name_bn??'';
            }
            if(isset($input['section'])){
                $info['section'] = Section::where('hr_section_id',$input['section'])->first()->hr_section_name_bn??'';
            }
            if(isset($input['subSection'])){
                $info['sub_sec'] = Subsection::where('hr_subsec_id',$input['subSection'])->first()->hr_subsec_name_bn??'';
            }
            // employee info
            $employeeData = DB::table('hr_as_basic_info');
            $employeeDataSql = $employeeData->toSql();
            // employee bangla info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

            $queryData = DB::table('hr_monthly_salary as s')
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->where('s.year', $input['year'])
            ->where('s.month', $input['month'])
            ->whereBetween('s.gross', [$input['min_sal'], $input['max_sal']])
            ->whereNotIn('s.as_id', config('base.ignore_salary'))
            ->when(!empty($input['unit']), function ($query) use($input){
               return $query->where('emp.as_unit_id',$input['unit']);
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('emp.as_location',$input['location']);
            })
            ->where('emp.as_status', $input['employee_status'])
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['line']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line']);
            })
            ->when(!empty($input['floor']), function ($query) use($input){
               return $query->where('emp.as_floor_id',$input['floor']);
            })
            // ->when(!empty($input['otnonot']), function ($query) use($input){
            //    return $query->where('emp.as_ot',$input['otnonot']);
            // })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subSection']);
            });
            if(isset($input['otnonot']) && $input['otnonot'] != null){
                $queryData->where('emp.as_ot',$input['otnonot']);
            }
            if(isset($input['disbursed']) && $input['disbursed'] != null){
                if($input['disbursed'] == 1){
                    $queryData->where('s.disburse_date', '!=', null);
                }else{
                    $queryData->where('s.disburse_date', null);
                }
            }
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.associate_id','s.as_id')->addBinding($employeeData->getBindings());
            });

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
                
            $queryData->select('s.*', 'emp.as_doj', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_location', 'bemp.hr_bn_associate_name', 'emp.as_oracle_code', 'emp.as_unit_id', 's.ot_hour', 's.ot_rate', 's.total_payable', 's.bank_payable', 's.cash_payable', 's.tds', 's.stamp', 's.pay_status');
            $totalSalary = round($queryData->sum("s.total_payable"));
            $totalCashSalary = round($queryData->sum("s.cash_payable"));
            $totalBankSalary = round($queryData->sum("s.bank_payable"));
            $totalStamp = round($queryData->sum("s.stamp"));
            $totalTax = round($queryData->sum("s.tds"));
            $totalOtHour = ($queryData->sum("s.ot_hour"));
            $totalOTAmount = round($queryData->sum(DB::raw('s.ot_hour * s.ot_rate')));
            $getSalaryList = $queryData->orderBy('emp.as_oracle_sl', 'asc')->get();
            $totalEmployees = count($getSalaryList);
            // return $getSalaryList;
            $employeeAssociates = $queryData->select('emp.associate_id')->pluck('emp.associate_id')->toArray();
            // salary adjust
            $salaryAddDeduct = DB::table('hr_salary_add_deduct')
                ->where('year', $input['year'])
                ->where('month', date('n', strtotime($input['month'])))
                ->whereIn('associate_id', $employeeAssociates)
                ->get()->keyBy('associate_id')->toArray();
            // employee designation
            $designation = designation_by_id();
            // return $designation;

            $locationDataSet = $getSalaryList->toArray();
            // return $locationDataSet;
            if($input['unit'] != null){
                $locationList = array_column($locationDataSet, 'as_location');
                $uniqueLocation = array_unique($locationList);
            }elseif($input['unit'] == null){
                $locationList = array_column($locationDataSet, 'as_unit_id');
                $uniqueLocation = array_unique($locationList);
            }
            
            $perPage = $input['perpage']??4;
            $locationDataSet = array_chunk($locationDataSet, $perPage, true);
            // dd($uniqueLocatiosn);
            // $title = $getUnit->hr_unit_name_bn;
            $pageHead['current_date']   = date('Y-m-d');
            $pageHead['current_time']   = date('H:i');
            $pageHead['unit_name']      = $getUnit->hr_unit_name_bn??'';
            $pageHead['for_date']       = $input['month_year'];
            $pageHead['floor_name']     = $input['floor']??'';
            $pageHead['month']     = $input['month'];
            $pageHead['year']     = $input['year'];
            $pageHead['totalSalary'] = $totalSalary;
            $pageHead['totalOtHour'] = $totalOtHour;
            $pageHead['totalOTAmount'] = $totalOTAmount;
            $pageHead['totalStamp'] = $totalStamp;
            $pageHead['totalEmployees'] = $totalEmployees;
            $pageHead = (object)$pageHead;
                
            return view('hr.operation.salary.generate_pay_slip', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'input'));
          
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }

    // get total working days
    public function workDays($startDate = null, $endDate = null, $unit = null)
    {
        $startDate = date("Y-m-d", strtotime($startDate));
        $endDate   = date("Y-m-d", strtotime($endDate));
        $totalDays = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
        $work_days = 0;
        #----------------------------------------------
        # Check Holiday with unit & month_year
        $total_holidays = DB::table("hr_yearly_holiday_planner")
            ->where("hr_yhp_unit", $unit)
            ->whereBetween("hr_yhp_dates_of_holidays", [$startDate, $endDate])
            ->count();

        $work_days = (($totalDays+1)-$total_holidays);
        return $work_days;
    }


    //get employee info
    public function employeeInfo($unit = null, $floor=null, $department=null, $section=null, $subSection=null, $salaryMonth=null)
    {
			if(auth()->user()->hasRole('power user 3')){
				$cantacces = ['power user 2','advance user 2'];
			}elseif (auth()->user()->hasRole('power user 2')) {
				$cantacces = ['power user 3','advance user 2'];
			}elseif (auth()->user()->hasRole('advance user 2')) {
				$cantacces = ['power user 3','power user 2'];
			}else{
				$cantacces = [];
			}
			$userIdNotAccessible = DB::table('roles')
								->whereIn('name',$cantacces)
								->leftJoin('model_has_roles','roles.id','model_has_roles.role_id')
								->pluck('model_has_roles.model_id');

					$asIds = DB::table('users')
									 ->whereIn('id',$userIdNotAccessible)
									 ->pluck('associate_id');

        $salaryMonth = date("Y-m", strtotime($salaryMonth));

        DB::statement(DB::raw('set @serial=0'));
    	return DB::table("hr_as_basic_info AS b")
    		->select(
                DB::raw('@serial := @serial + 1 AS serial'),
    			"bd.hr_bn_associate_name AS name",
    			"b.as_doj AS doj",
                'dg.hr_designation_name_bn AS designation',
                'dg.hr_designation_grade AS grade',
    			"b.as_id",
                "b.as_ot",
                "b.as_emp_type_id AS type",
    			"b.temp_id",
    			"b.associate_id AS associate",
    			"b.as_name",
    			"b.as_unit_id AS unit",
    			"ben.ben_current_salary AS salary",
    			"ben.ben_basic AS basic",
    			"ben.ben_house_rent AS house",
    			"ben.ben_medical AS medical",
    			"ben.ben_transport AS transport",
    			"ben.ben_food AS food"
    		)
    		->leftJoin("hr_employee_bengali AS bd", "bd.hr_bn_associate_id", "=", "b.associate_id")
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_benefits AS ben', function($join){
            	$join->on('ben.ben_as_id', '=', 'b.associate_id');
            	$join->where('ben.ben_status', '=', 1);
            })
            ->where(function($c) use($unit, $floor, $department, $section, $subSection){
									$c->where("b.as_unit_id", $unit);
									if (!empty($department))
									{
									$c->where("b.as_department_id", $department);
								}
								if (!empty($floor))
								{
									$c->where("b.as_floor_id", $floor);
								}
                if (!empty($section))
                {
                    $c->where("b.as_section_id", $section);
                }
                if (!empty($subSection))
                {
                    $c->where("b.as_subsection_id", $subSection);
                }
            })
            ->where(DB::raw("DATE_FORMAT(b.as_doj, '%Y-%m')"), "<=", $salaryMonth)
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
						->whereNotIn('b.associate_id',$asIds)
            ->where('b.as_status',1) // checking status
    		->paginate(24);
    }

}
