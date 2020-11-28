<?php
namespace App\Http\Controllers\Hr\Recruitment;

use App\Helpers\Custom;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Hr\Recruitment\CostMappingController AS CostMappingController;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Designation;
use App\Models\Hr\DesignationUpdateLog;
use App\Models\Hr\EducationLevel;
use App\Models\Hr\EmpType;
use App\Models\Employee;
use App\Models\Hr\EmployeeBengali;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Increment;
use App\Models\Hr\Line;
use App\Models\Hr\LoanApplication;
use App\Models\Hr\Location;
use App\Models\Hr\MapCostArea;
use App\Models\Hr\MapCostUnit;
use App\Models\Hr\Nominee;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftRoaster;
use App\Models\Hr\Station;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use App\Models\Hr\promotion;
use Auth, DB, Validator, Image, Session, ACL, PDF, Response, Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class EmployeeController extends Controller
{
    public function showEmployeeForm()
    {
        //ACL::check(["permission" => "hr_recruitment_employer_add"]);
        #-----------------------------------------------------------#
        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->pluck('hr_unit_short_name', 'hr_unit_id');
        $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        return view('hr/recruitment/add_employee', compact('employeeTypes', 'unitList', 'areaList'));
    }

    public function saveEmployee(Request $request)
    {
        //ACL::check(["permission" => "hr_recruitment_employer_add"]);
        #-----------------------------------------------------------#

        $validator = Validator::make($request->all(), [
            'as_emp_type_id'    => 'required',
            // 'as_unit_id'        => '',
            // 'as_floor_id'       => '',
            // 'as_line_id'        => '',
            // 'as_shift_id'       => '',

            'as_area_id'        => 'required',
            'as_department_id'  => 'required',
            'as_section_id'     => 'required',
            'as_subsection_id'  => 'required',

            'as_designation_id' => 'required',

            'as_doj'            => 'required|date',
            'associate_id'      => 'required|unique:hr_as_basic_info|max:10|min:10',
            'temp_id'           => 'required|max:6|min:6',
            'as_name'           => 'required|max:128',
            'as_gender'         => 'required|max:10',
            'as_dob'            => 'required|date',
            'as_contact'        => 'required',
            'as_ot'             => 'required|max:1',
            'as_pic'            => 'image|mimes:jpeg,png,jpg|max:200',
        ]);

        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        else
        {
            //-----------IMAGE UPLOAD---------------------
            $as_pic = null;
            if($request->hasFile('as_pic'))
            {
                $file = $request->file('as_pic');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $as_pic = '/assets/images/employee/' . $filename;
                Image::make($file)->resize(180, 200)->save(public_path( $as_pic ) );
            }

            //-----------Store Data---------------------
            $user = new Employee;
            $user->as_emp_type_id = $request->as_emp_type_id;
            $user->as_unit_id     = $request->as_unit_id;

            // if employee is worker then store the unit, floor, line & shift id
            $user->as_floor_id    = $request->as_floor_id;
            $user->as_line_id     = $request->as_line_id;
            $user->as_shift_id    = $request->as_shift_id;

            $user->as_area_id     = $request->as_area_id;
            $user->as_department_id = $request->as_department_id;
            $user->as_section_id  = $request->as_section_id;
            $user->as_subsection_id  = $request->as_subsection_id;
            $user->as_designation_id = $request->as_designation_id;
            $user->as_doj         = (!empty($request->as_doj)?date('Y-m-d',strtotime($request->as_doj)):null);
            $user->temp_id        = $request->temp_id;
            $user->associate_id   = $request->associate_id;
            $user->as_name        = strtoupper($request->as_name);
            $user->as_gender      = $request->as_gender;
            $user->as_dob         = (!empty($request->as_dob)?date('Y-m-d',strtotime($request->as_dob)):null);
            $user->as_contact     = $request->as_contact;
            $user->as_ot          = $request->as_ot;
            $user->as_pic         = $as_pic;
            $user->created_at     = date("Y-m-d H:i:s");
            $user->created_by     = Auth::user()->id;
            $user->as_status      = 1;

            if ($user->save())
            {
                $this->logFileWrite("Employee Entry Data Saved", $user->as_id);
                Cache::forget('employee_count');
                return back()
                    ->with('associate_id', $request->associate_id)
                    ->with('associate_name', $request->as_name)
                    ->with('success', 'Save Successful.');
            }
            else
            {
                return back()
                    ->withInput()->with('error', 'Please try again.');
            }
        }
    }

    public function showList()
    {
        
        $reportCount = employee_count();

        $employeeTypes = EmpType::where('hr_emp_type_status', '1')->distinct()->orderBy('hr_emp_type_name', 'ASC')->pluck('hr_emp_type_name');
        $empTypes = EmpType::where('hr_emp_type_status', '1')
                            ->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->distinct()
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_short_name', 'ASC')
            ->pluck('hr_unit_short_name');
        $allUnit= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
                        ->pluck('hr_unit_name', 'hr_unit_id');
        $floorList = Floor::where('hr_floor_status', '1')->distinct()->orderBy('hr_floor_name', 'ASC')->pluck('hr_floor_name');
        $lineList  = Line::where('hr_line_status', '1')->distinct()->orderBy('hr_line_name', 'ASC')->pluck('hr_line_name');
        $shiftList  = Shift::where('hr_shift_status', '1')->distinct()->orderBy('hr_shift_name', 'ASC')->pluck('hr_shift_name');
        $areaList  = Area::where('hr_area_status', '1')->distinct()->orderBy('hr_area_name', 'ASC')->pluck('hr_area_name');
        $departmentList  = Department::where('hr_department_status', '1')->distinct()->orderBy('hr_department_name', 'ASC')->pluck('hr_department_name');
        $designationList  = Designation::where('hr_designation_status', '1')->distinct()->orderBy('hr_designation_name', 'ASC')->pluck('hr_designation_name');
        $sectionList  = Section::where('hr_section_status', '1')->distinct()->orderBy('hr_section_name', 'ASC')->pluck('hr_section_name');
        $subSectionList  = Subsection::where('hr_subsec_status', '1')->distinct()->orderBy('hr_subsec_name', 'ASC')->pluck('hr_subsec_name');
        $educationList  = EducationLevel::pluck('education_level_title');

        return view('hr.recruitment.employee_list', compact(
            'reportCount',
            'employeeTypes',
            'unitList',
            'floorList',
            'lineList',
            'shiftList',
            'areaList',
            'departmentList',
            'designationList',
            'sectionList',
            'subSectionList',
            'educationList',
            "allUnit",
            "empTypes"
        ));
    }
    
    public function today()
    {
        
        $reportCount = employee_count();

        $employeeTypes = EmpType::where('hr_emp_type_status', '1')->distinct()->orderBy('hr_emp_type_name', 'ASC')->pluck('hr_emp_type_name');
        $empTypes = EmpType::where('hr_emp_type_status', '1')
                            ->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->distinct()
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_short_name', 'ASC')
            ->pluck('hr_unit_short_name');
        $allUnit= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
                        ->pluck('hr_unit_name', 'hr_unit_id');
        $floorList = Floor::where('hr_floor_status', '1')->distinct()->orderBy('hr_floor_name', 'ASC')->pluck('hr_floor_name');
        $lineList  = Line::where('hr_line_status', '1')->distinct()->orderBy('hr_line_name', 'ASC')->pluck('hr_line_name');
        $shiftList  = Shift::where('hr_shift_status', '1')->distinct()->orderBy('hr_shift_name', 'ASC')->pluck('hr_shift_name');
        $areaList  = Area::where('hr_area_status', '1')->distinct()->orderBy('hr_area_name', 'ASC')->pluck('hr_area_name');
        $departmentList  = Department::where('hr_department_status', '1')->distinct()->orderBy('hr_department_name', 'ASC')->pluck('hr_department_name');
        $designationList  = Designation::where('hr_designation_status', '1')->distinct()->orderBy('hr_designation_name', 'ASC')->pluck('hr_designation_name');
        $sectionList  = Section::where('hr_section_status', '1')->distinct()->orderBy('hr_section_name', 'ASC')->pluck('hr_section_name');
        $educationList  = EducationLevel::pluck('education_level_title');

        return view('hr.recruitment.employee_today', compact(
            'reportCount',
            'employeeTypes',
            'unitList',
            'floorList',
            'lineList',
            'shiftList',
            'areaList',
            'departmentList',
            'designationList',
            'sectionList',
            'educationList',
            "allUnit",
            "empTypes"
        ));
    }

    public function getTodayData(Request $request)
    {
        
        $data = DB::table('hr_as_basic_info AS b')
            ->select([
                DB::raw('b.as_id AS serial_no'),
                'b.associate_id',
                'b.as_name',
                'e.hr_emp_type_name AS hr_emp_type_name',
                'u.hr_unit_short_name',
                'f.hr_floor_name',
                'l.hr_line_name',
                'dp.hr_department_name',
                'dg.hr_designation_name',
                'dg.hr_designation_position',
                'b.as_gender',
                'b.as_ot',
                'b.as_status',
                'b.as_oracle_code',
                'b.as_rfid_code'
            ])
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'b.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->whereIn('b.as_status',[1])
            ->where(function ($query) use ($request) {
                if($request->otnonot != null){
                    $query->where('b.as_ot', '=', $request->otnonot);
                }
                if($request->emp_type != ""){
                    $query->where('b.as_emp_type_id', '=', $request->emp_type);
                }
                if($request->unit != ""){
                    $query->where('b.as_unit_id', '=', $request->unit);
                }
            })
            ->whereNotIn('as_id', auth()->user()->management_permissions())
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->whereMonth('b.as_doj',date('m'))
            ->orderBy('dg.hr_designation_position','ASC')
            ->get();

        
        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('as_ot', function($user){
                if($user->as_ot==1){
                    $ot_id2="OT";
                }
                else{
                    $ot_id2="Non OT";
                }
                return ($ot_id2);
            })
            ->editColumn('action', function ($user) {

                $return = "<a href=".url('hr/recruitment/employee/show/'.$user->associate_id)." class=\"btn btn-sm btn-success\" data-toggle='tooltip' data-placement='top' title='' data-original-title='View Employee Profile'>
                        <i class=\"ace-icon fa fa-eye bigger-120\"></i>
                    </a>
                    <a href=".url('hr/recruitment/employee/edit/'.$user->associate_id)." class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"Edit\" style=\"margin-top:1px;\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>";
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                
                'as_status',
                'action',
                'as_ot'
            ])
            ->make(true);
    }
    
    public function incompleteEmployee()
    {
        $reportCount = employee_count();

        $employeeTypes = EmpType::where('hr_emp_type_status', '1')->distinct()->orderBy('hr_emp_type_name', 'ASC')->pluck('hr_emp_type_name');
        $empTypes = EmpType::where('hr_emp_type_status', '1')
                            ->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->distinct()
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_short_name', 'ASC')
            ->pluck('hr_unit_short_name');
        $allUnit= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
                        ->pluck('hr_unit_name', 'hr_unit_id');
        $floorList = Floor::where('hr_floor_status', '1')->distinct()->orderBy('hr_floor_name', 'ASC')->pluck('hr_floor_name');
        $lineList  = Line::where('hr_line_status', '1')->distinct()->orderBy('hr_line_name', 'ASC')->pluck('hr_line_name');
        $shiftList  = Shift::where('hr_shift_status', '1')->distinct()->orderBy('hr_shift_name', 'ASC')->pluck('hr_shift_name');
        $areaList  = Area::where('hr_area_status', '1')->distinct()->orderBy('hr_area_name', 'ASC')->pluck('hr_area_name');
        $departmentList  = Department::where('hr_department_status', '1')->distinct()->orderBy('hr_department_name', 'ASC')->pluck('hr_department_name');
        $designationList  = Designation::where('hr_designation_status', '1')->distinct()->orderBy('hr_designation_name', 'ASC')->pluck('hr_designation_name');
        $sectionList  = Section::where('hr_section_status', '1')->distinct()->orderBy('hr_section_name', 'ASC')->pluck('hr_section_name');
        $educationList  = EducationLevel::pluck('education_level_title');

        return view('hr.recruitment.employee_incomplete', compact(
            'reportCount',
            'employeeTypes',
            'unitList',
            'floorList',
            'lineList',
            'shiftList',
            'areaList',
            'departmentList',
            'designationList',
            'sectionList',
            'educationList',
            "allUnit",
            "empTypes"
        ));
    }

    public function getIncompleteData(Request $request)
    {
        
        $data = DB::table('hr_as_basic_info AS b')
            ->select([
                DB::raw('b.as_id AS serial_no'),
                'b.associate_id',
                'b.as_name',
                'e.hr_emp_type_name AS hr_emp_type_name',
                'u.hr_unit_short_name',
                'f.hr_floor_name',
                'l.hr_line_name',
                'dp.hr_department_name',
                'dg.hr_designation_name',
                'dg.hr_designation_position',
                'b.as_gender',
                'b.as_ot',
                'b.as_status',
                'b.as_oracle_code',
                'b.as_rfid_code',
                'bn.hr_bn_associate_name',
                'bn.hr_bn_father_name',
                'ben.ben_current_salary'
            ])
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'b.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_benefits AS ben', 'ben.ben_as_id', '=', 'b.associate_id')
            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'b.associate_id')
            ->whereIn('b.as_status',[1])
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->where(function ($query) use ($request) {
                if($request->otnonot != null){
                    $query->where('b.as_ot', '=', $request->otnonot);
                }
                if($request->emp_type != ""){
                    $query->where('b.as_emp_type_id', '=', $request->emp_type);
                }
            })
            ->where(function ($query) {
                $query->whereNull('b.as_shift_id');
                $query->orWhereNull('b.as_oracle_code');
                $query->orWhereNull('b.as_rfid_code');
               $query->orWhereNull('ben.ben_joining_salary');
                $query->orWhereNull('ben.ben_current_salary');
                $query->orWhereNull('b.as_designation_id');
                $query->orWhereNull('bn.hr_bn_associate_name');
                $query->orWhereNull('bn.hr_bn_father_name');
            })
            ->whereNotIn('b.as_id', auth()->user()->management_permissions())
            ->orderBy('dg.hr_designation_position','ASC')
            ->get();

        
        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('as_ot', function($user){
                if($user->as_ot==1){
                    $ot_id2="OT";
                }
                else{
                    $ot_id2="Non OT";
                }
                return ($ot_id2);
            })
            ->editColumn('action', function ($user) {

                $return = "
                    <a href=".url('hr/recruitment/employee/edit/'.$user->associate_id)." class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"Edit\" style=\"margin-top:1px;\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>";
                    $return .= " <a href=".url('hr/recruitment/operation/advance_info_edit/'.$user->associate_id)." class=\"btn btn-sm btn-success\" data-toggle=\"tooltip\" title=\"Advance info edit\" style=\"margin-top:1px;\">
                        A
                    </a>
                    ";
                if($user->hr_bn_associate_name == null || $user->hr_bn_father_name == null){
                    $return .= "<a href=".url('hr/recruitment/operation/advance_info_edit/'.$user->associate_id.'#bangla')." class=\"btn btn-sm btn-danger\" data-toggle=\"tooltip\" title=\"Edit Bangla Info\" style=\"margin-top:1px;\">
                        অ
                    </a>
                    ";
                }

                if($user->ben_current_salary == null){
                    $return .= "<a href=".url('hr/payroll/employee-benefit?associate_id='.$user->associate_id)." class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"Add Benefit\" style=\"margin-top:1px;\">
                        <i class=\"las la-file-invoice-dollar bigger-120\"></i>
                    </a>
                    ";
                }

                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                
                'as_status',
                'action',
                'as_ot'
            ])
            ->make(true);
    }
    public function showListDetails()
    {
        //ACL::check(["permission" => "hr_recruitment_employer_list"]);
        #-----------------------------------------------------------#

        $reportCount = (new DashboardController)->reportCount();

        $empTypes = EmpType::where('hr_emp_type_status', '1')
                            ->pluck('hr_emp_type_name', 'emp_type_id');
        $allUnit= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
                        ->pluck('hr_unit_name', 'hr_unit_id');
        $employeeTypes = EmpType::where('hr_emp_type_status', '1')->distinct()->orderBy('hr_emp_type_name', 'ASC')->pluck('hr_emp_type_name');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->distinct()
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_short_name', 'ASC')
            ->pluck('hr_unit_short_name');
        $floorList = Floor::where('hr_floor_status', '1')->distinct()->whereIn('hr_floor_unit_id', auth()->user()->unit_permissions())->orderBy('hr_floor_name', 'ASC')->pluck('hr_floor_name');
        $lineList  = Line::where('hr_line_status', '1')->distinct()->whereIn('hr_line_unit_id', auth()->user()->unit_permissions())->orderBy('hr_line_name', 'ASC')->pluck('hr_line_name');
        $shiftList  = Shift::where('hr_shift_status', '1')->distinct()->orderBy('hr_shift_name', 'ASC')->pluck('hr_shift_name');
        $areaList  = Area::where('hr_area_status', '1')->distinct()->orderBy('hr_area_name', 'ASC')->pluck('hr_area_name');
        $departmentList  = Department::where('hr_department_status', '1')->distinct()->orderBy('hr_department_name', 'ASC')->pluck('hr_department_name');
        $designationList  = Designation::where('hr_designation_status', '1')->distinct()->orderBy('hr_designation_name', 'ASC')->pluck('hr_designation_name');
        $sectionList  = Section::where('hr_section_status', '1')->distinct()->orderBy('hr_section_name', 'ASC')->pluck('hr_section_name');
        $educationList  = EducationLevel::pluck('education_level_title');

        return view('hr.recruitment.employee_details_list', compact(
            'reportCount',
            'employeeTypes',
            'unitList',
            'floorList',
            'lineList',
            'shiftList',
            'areaList',
            'departmentList',
            'designationList',
            'sectionList',
            'educationList',
            "allUnit",
            "empTypes"
        ));
    }


    public function getData(Request $request)
    {
        
        $data = DB::table('hr_as_basic_info AS b')
            ->select([
                DB::raw('b.as_id AS serial_no'),
                'b.associate_id',
                'b.as_name',
                'e.hr_emp_type_name AS hr_emp_type_name',
                'u.hr_unit_short_name',
                'f.hr_floor_name',
                'l.hr_line_name',
                'lc.hr_location_name',
                'dp.hr_department_name',
                'dg.hr_designation_name',
                'dg.hr_designation_position',
                'dg.hr_designation_grade',
                'b.as_gender',
                'b.as_ot',
                'b.as_doj',
                'b.as_dob',
                'b.as_status',
                'b.as_oracle_code',
                'b.as_rfid_code',
                'sec.hr_section_name',
                'subsec.hr_subsec_name',
                'b.as_shift_id',
                'ben.ben_current_salary'
            ])
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'b.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_location AS lc', 'lc.hr_location_id', '=', 'b.as_location')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_section AS sec', 'sec.hr_section_id', '=', 'b.as_section_id')
            ->leftJoin('hr_subsection AS subsec', 'subsec.hr_subsec_id', '=', 'b.as_subsection_id')
            ->leftJoin('hr_benefits AS ben', 'b.associate_id', '=', 'ben.ben_as_id')
            ->where('b.as_status',1)
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->where(function ($query) use ($request) {
                if($request->otnonot != null){
                    $query->where('b.as_ot', '=', $request->otnonot);
                }
                if($request->emp_type != ""){
                    $query->where('b.as_emp_type_id', '=', $request->emp_type);
                }
                if($request->doj_from != ""){
                    $query->where('b.as_doj', '>=', $request->doj_from);
                }
                if($request->doj_to != ""){
                    $query->where('b.as_doj', '<=', $request->doj_to);
                }
                if($request->unit != ""){
                    $query->where('b.as_unit_id', '=', $request->unit);
                }
            })
            ->whereNotIn('as_id', auth()->user()->management_permissions())
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->orderBy('dg.hr_designation_position','ASC')
            ->get();

        $perm = false;
        if(auth()->user()->can('Manage Employee') || auth()->user()->hasRole('Super Admin')){
            $perm = true; 
        }

        $sal = check_permission('Salary Sheet');
        // education
        $educationDegree = DB::table('hr_education_degree_title')->pluck('education_degree_title', 'id');
        $educations = DB::table('hr_education AS e')
        ->select(DB::raw('t.*'))
        ->from(DB::raw('(SELECT * FROM hr_education ORDER BY id DESC) t'))
        ->groupBy('t.education_as_id')
        ->pluck('education_degree_id_1', 'education_as_id');

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('as_ot', function($user){
                if($user->as_ot==1){
                    $ot_id2="OT";
                }
                else{
                    $ot_id2="Non OT";
                }
                return ($ot_id2);
            })
            ->editColumn('hr_designation_grade', function($user){
                if($user->hr_designation_grade > 0 ){
                    return $user->hr_designation_grade;
                }
                else{
                    return '';
                }
                
            })
            ->addColumn('age', function($user){
                // if($user->as_dob){
                //     return Carbon::parse($user->as_dob)->age;
                // }
                // else{
                //     return '';
                // }
                return date('Y-m-d', strtotime($user->as_dob));
                
            })
            ->addColumn('education', function($user) use ($educations, $educationDegree){
                $edu = $educations[$user->associate_id]??'';
                if($edu != ''){
                    $educationDeg = $educationDegree[$edu]??'';
                }else{
                    $educationDeg = '';
                }
                return $educationDeg;
                
            })
            ->addColumn('salary', function($user) use ($sal){
                if($sal){
                    $salary = $user->ben_current_salary;
                }else{
                    $salary = '';
                }
                return $salary;
                
            })
            ->editColumn('action', function ($user) use($perm) {

                $return = "<a href=".url('hr/recruitment/employee/show/'.$user->associate_id)." class=\"btn btn-sm btn-success\" data-toggle='tooltip' data-placement='top' title='' data-original-title='View Employee Profile'>
                        <i class=\"ace-icon fa fa-eye bigger-120\"></i>
                    </a> ";
                    if($perm){

                        $return .= "<a href=".url('hr/recruitment/employee/edit/'.$user->associate_id)." class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"Edit\" style=\"margin-top:1px;\">
                            <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                        </a>";
                    }
                return $return;
            })
            ->rawColumns([
                
                'as_status',
                'age',
                'action',
                'as_ot',
                'education',
                'salary'
            ])
            ->make(true);
    }

    //get select data by unit
    public function getDropdownData(Request $request)
    {
        // dd($request->all());
        $employeeTypes = EmpType::where('hr_emp_type_status', '1')->distinct()->orderBy('hr_emp_type_name', 'ASC')->pluck('hr_emp_type_name');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->distinct()
            ->where('hr_unit_id',$request->unit)
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_short_name', 'ASC')
            ->pluck('hr_unit_short_name');

        $floorList = Floor::where('hr_floor_status', '1')->distinct()->where('hr_floor_unit_id',$request->unit)->orderBy('hr_floor_name', 'ASC')->pluck('hr_floor_name');
        $lineList  = Line::where('hr_line_status', '1')->distinct()->where('hr_line_unit_id',$request->unit)->orderBy('hr_line_name', 'ASC')->pluck('hr_line_name');
        $departmentList  = Department::where('hr_department_status', '1')->distinct()->orderBy('hr_department_name', 'ASC')->pluck('hr_department_name');
        
        return Response::json(array(
            'unitList' => $unitList,
            'floorList' => $floorList,
            'lineList' => $lineList
            
        ));

    }

    //get employee details data
    public function getDetailsData(Request $request)
    {

        //ACL::check(["permission" => "hr_recruitment_employer_list"]);
        #-----------------------------------------------------------#

        $data = DB::table('hr_as_basic_info AS b')
            ->select([
                DB::raw('b.as_id AS serial_no'),
                'b.associate_id',
                'b.as_name',
                'e.hr_emp_type_name AS hr_emp_type_name',
                'u.hr_unit_short_name',
                'f.hr_floor_name',
                'l.hr_line_name',
                'a.hr_area_name',
                'dp.hr_department_name',
                'dg.hr_designation_name',
                's.hr_section_name',
                'b.as_gender',
                'b.as_ot',
                'adv.emp_adv_info_religion',
                'b.as_contact',
                'edu.education_as_id',
                'el.education_level_title',
                'b.as_status',
                'b.as_oracle_code',
                'b.as_rfid_code'
            ])
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'b.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_section AS s', 's.hr_section_id', '=', 'b.as_section_id')
            ->leftJoin("hr_as_adv_info AS adv", "adv.emp_adv_info_as_id", "=", "b.associate_id")
            ->leftJoin(DB::raw("(SELECT edu1.*
            FROM hr_education edu1 LEFT JOIN hr_education edu2
             ON (edu1.education_as_id = edu2.education_as_id AND edu1.education_passing_year < edu2.education_passing_year)
            WHERE edu2.id IS NULL) AS edu"), function($query){
                $query->on( 'edu.education_as_id', '=', 'b.associate_id');
            })
            ->leftJoin('hr_education_level AS el', 'el.id', '=', 'edu.education_level_id')
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->where('b.as_unit_id', $request->unit)
            ->where(function ($query) use ($request) {
                if($request->emp_type != ""){
                    $query->where('b.as_emp_type_id', '=', $request->emp_type);
                }
            })
            ->orderBy('b.as_id','desc')
            ->get();


        return Datatables::of($data)
            ->editColumn('as_ot', function($user){

               $ot_id= "<span style='display:none;>". $user->as_ot ."-</span>";
              if($user->as_ot==1){
                 $ot_id2="OT";
              }
              else{
                $ot_id2="Non OT";
              }
              $ot_id.= $ot_id.$ot_id2 ;

                //return (($user->as_ot==1)?"OT":"Non OT");
                  return ($ot_id);
            })
            ->editColumn('as_status', function($user){
                if ($user->as_status == 1)
                {
                    return "Active";
                }
                elseif ($user->as_status == 2)
                {
                    return "Resign";
                }
                elseif ($user->as_status == 3)
                {
                    return "Terminate";
                }
                elseif ($user->as_status == 4)
                {
                    return "Suspend";
                }
                elseif ($user->as_status == 5)
                {
                    return "Left";
                }
            })
            ->editColumn('action', function ($user) {

                // $return = "<div class=\"btn-group\" style=\"width:104px\">" . ($user->as_status?"<span class='btn btn-xs disabled btn-info'>Active</span>":"<span class='btn btn-xs disabled btn-warning'>Inactive</span>");
                $return = "<a href=".url('hr/recruitment/employee/show/'.$user->associate_id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                        <i class=\"ace-icon fa fa-eye bigger-120 fa-fw\"></i>
                    </a>
                    <a href=".url('hr/recruitment/employee/edit/'.$user->associate_id)." class=\"btn btn-xs btn-info\" data-toggle=\"tooltip\" title=\"Edit\" style=\"margin-top:1px;\">
                        <i class=\"ace-icon fa fa-pencil bigger-120 fa-fw\"></i>
                    </a>";
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'serial_no',
                'as_status',
                'action',
                'as_ot'
            ])
            ->make(true);
    }

    protected function getDataByID($associate_id = null)
    {
        return Employee::select(
                'hr_as_basic_info.*',
                'u.hr_unit_id',
                'u.hr_unit_name',
                'u.hr_unit_short_name',
                'u.hr_unit_name_bn',
                'f.hr_floor_name',
                'f.hr_floor_name_bn',
                'l.hr_line_name',
                'l.hr_line_name_bn',
                'dp.hr_department_name',
                'dp.hr_department_name_bn',
                'dg.hr_designation_name',
                'dg.hr_designation_name_bn',
                'a.*',
                'be.*',
                'm.*',
                'e.hr_emp_type_name',
                'ar.hr_area_name',
                'se.hr_section_name',
                'se.hr_section_name_bn',
                'sb.hr_subsec_name',
                'sb.hr_subsec_name_bn',
                'bn.*',
                # unit/floor/line/shif
                DB::raw("
                    CONCAT_WS('. ',
                        CONCAT('Unit: ', u.hr_unit_short_name),
                        CONCAT('Floor: ', f.hr_floor_name),
                        CONCAT('Line: ', l.hr_line_name)
                    ) AS unit_floor_line
                "),
                # permanent district & upazilla
                "per_dist.dis_name AS permanent_district",
                "per_dist.dis_name_bn AS permanent_district_bn",
                "per_upz.upa_name AS permanent_upazilla",
                "per_upz.upa_name_bn AS permanent_upazilla_bn",
                # present district & upazilla
                "pres_dist.dis_name AS present_district",
                "pres_dist.dis_name_bn AS present_district_bn",
                "pres_upz.upa_name AS present_upazilla",
                "pres_upz.upa_name_bn AS present_upazilla_bn"
            )
            ->leftJoin('hr_area AS ar', 'ar.hr_area_id', '=', 'hr_as_basic_info.as_area_id')
            ->leftJoin('hr_section AS se', 'se.hr_section_id', '=', 'hr_as_basic_info.as_section_id')
            ->leftJoin('hr_subsection AS sb', 'sb.hr_subsec_id', '=', 'hr_as_basic_info.as_subsection_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'hr_as_basic_info.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'hr_as_basic_info.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'hr_as_basic_info.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'hr_as_basic_info.as_line_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'hr_as_basic_info.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'hr_as_basic_info.as_designation_id')
            ->leftJoin("hr_as_adv_info AS a", "a.emp_adv_info_as_id", "=", "hr_as_basic_info.associate_id")
            ->leftJoin('hr_benefits AS be',function ($leftJoin) {
                $leftJoin->on('be.ben_as_id', '=' , 'hr_as_basic_info.associate_id') ;
                $leftJoin->where('be.ben_status', '=', '1') ;
            })
            ->leftJoin('hr_med_info AS m', 'm.med_as_id', '=', 'hr_as_basic_info.associate_id')

            #permanent district & upazilla
            ->leftJoin('hr_dist AS per_dist', 'per_dist.dis_id', '=', 'a.emp_adv_info_per_dist')
            ->leftJoin('hr_upazilla AS per_upz', 'per_upz.upa_id', '=', 'a.emp_adv_info_per_upz')
            #present district & upazilla
            ->leftJoin('hr_dist AS pres_dist', 'pres_dist.dis_id', '=', 'a.emp_adv_info_pres_dist')
            ->leftJoin('hr_upazilla AS pres_upz', 'pres_upz.upa_id', '=', 'a.emp_adv_info_pres_upz')
            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'hr_as_basic_info.associate_id')
            ->where("hr_as_basic_info.associate_id", $associate_id)
            ->whereIn('hr_as_basic_info.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('hr_as_basic_info.as_location', auth()->user()->location_permissions())
            ->first();
    }

    protected function getCompleteInfo($associate_id = null)
    {
        $info = DB::table('hr_as_basic_info AS b')
            ->select(
                'b.*',
                'a.*',
                'be.*',
                'm.*',
                'bn.*'
            )
            ->leftJoin("hr_as_adv_info AS a", "a.emp_adv_info_as_id", "=", "b.associate_id")
            ->leftJoin('hr_benefits AS be',function ($leftJoin) {
                $leftJoin->on('be.ben_as_id', '=' , 'b.associate_id') ;
                $leftJoin->where('be.ben_status', '=', '1') ;
            })
            ->leftJoin('hr_med_info AS m', 'm.med_as_id', '=', 'b.associate_id')
            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'b.associate_id')
            ->where("b.associate_id", $associate_id)
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->first();

            $infocount=0; $totalinfo=0;
            foreach ($info as $key =>$infovalue)
            {
                if($infovalue!=null){ $infocount++;}
                $totalinfo++;
            }
            $per_complete=round((($infocount/$totalinfo)*100), 2);
        return $per_complete;
    }

    # Show User by Associate ID
    /*get employee attendance table*/
    public function getTableName($unit)
{
    $tableName = "";
    //CEIL
    if($unit == 2){
        $tableName= "hr_attendance_ceil AS a";
    }
    //AQl
    else if($unit == 3){
        $tableName= "hr_attendance_aql AS a";
    }
    // MBM
    else if($unit == 1 || $unit == 4 || $unit == 5 || $unit == 9){
        $tableName= "hr_attendance_mbm AS a";
    }
    //HO
    else if($unit == 6){
        $tableName= "hr_attendance_ho AS a";
    }
    // CEW
    else if($unit == 8){
        $tableName= "hr_attendance_cew AS a";
    }
    else{
        $tableName= "hr_attendance_mbm AS a";
    }
    return $tableName;
}

    public function show(Request $request)
    {
        //ACL::check(["permission" => "hr_recruitment_employer_list"]);
        #-----------------------------------------------------------#

        if (!empty($request->associate_id))
        {
            $info = get_employee_by_id($request->associate_id);

            if(empty($info)){
                toastr()->error($request->associate_id.' Not Found!');
                return back();
            }
            // $info = Employee::where('associate_id', $request->associate_id)->first();
            $per_complete = $this->getCompleteInfo($request->associate_id);


            $loans = DB::table("hr_loan_application")
                ->select(
                "*",
                DB::raw("
                    CASE
                        WHEN hr_la_status = '0' THEN 'Applied'
                        WHEN hr_la_status = '1' THEN 'Approved'
                        WHEN hr_la_status = '2' THEN 'Declined'
                    END AS hr_la_status
                ")
            )
            ->where("hr_la_as_id", $request->associate_id)
            ->get();

            $month  = date('m');
            $year   = date('Y');
            $day    = date('d');
            $day    = (int)$day;

            $shiftRoaster = ShiftRoaster::where([
                'shift_roaster_associate_id' => $request->associate_id,
                'shift_roaster_year' => (int)$year,
                'shift_roaster_month' => (int)$month
            ])->first();

            $roasterShift = null;
            if($shiftRoaster) {
                $roasterShift = 'day_'.$day;
                $roasterShift = $shiftRoaster->$roasterShift;
            }

            //get todays status

            $tableName    = $this->getTableName($info->hr_unit_id);
            $daystart=date('Y-m-d')." 00:00:00";
            $dayend=date('Y-m-d')." 23:59:59";
            $status=[];
            $attend = DB::table($tableName)->where('as_id',$info->as_id)
                          ->whereBetween('in_time',[$daystart,$dayend])
                          ->leftJoin("hr_shift AS s", "a.hr_shift_code", "=", "s.hr_shift_code")
                          ->first();


            if($attend != null){
                $status=[
                    'status'=> 1,
                    'in_time' => $attend->in_time,
                ];
            }else{
                $leave = DB::table('hr_leave')
                        ->where('leave_ass_id', $info->associate_id)
                        ->where('leave_from','<=', date('Y-m-d'))
                        ->where('leave_to','>=', date('Y-m-d'))
                        ->where('leave_status','1')
                        ->first();

                    //return $leave;
                    if($leave !=null){
                        $status=[
                            'status'=> 2,
                            'type' => $leave->leave_type
                        ];
                    }
                    else{
                        $status=[
                            'status'=> 0
                        ];
                    }

            }

            //return $status;


            $leaves = DB::table('hr_leave')
                ->select(
                    DB::raw("
                        YEAR(leave_from) AS year,
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual,
                        SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned,
                        SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick,
                        SUM(CASE WHEN leave_type = 'Maternity' THEN DATEDIFF(leave_to, leave_from)+1 END) AS maternity,
                        SUM(CASE WHEN leave_type = 'Special' THEN DATEDIFF(leave_to, leave_from)+1 END) AS special,
                        SUM(DATEDIFF(leave_to, leave_from)+1) AS total
                    ")
                )
                ->where('leave_status', '1')
                ->where("leave_ass_id", $request->associate_id)
                ->groupBy('year')
                ->orderBy('year', 'DESC')
                ->get();



            //Earned Leave Calculation

            $earnedLeaves = get_earned_leave($leaves,$info->as_id,$info->associate_id,$info->as_unit_id);

            //dd($leavesForEarned);


            //dd($earnedLeaves);

            $information = DB::table("hr_as_basic_info AS b")
            ->select(
              "b.as_id AS id",
              "b.associate_id AS associate",
              "b.as_name AS name",
              "b.as_doj AS doj",
              "u.hr_unit_id AS unit_id",
              "u.hr_unit_name AS unit",
              "s.hr_section_name AS section",
              "d.hr_designation_name AS designation"
            )
            ->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id")
            ->leftJoin("hr_section AS s", "s.hr_section_id", "=", "b.as_section_id")
            ->leftJoin("hr_designation AS d", "d.hr_designation_id", "=", "b.as_designation_id")
            ->where("b.associate_id", "=", $request->associate_id)
            ->first();
            //earned leave


            $records = DB::table('hr_dis_rec AS r')
                ->select(
                    'r.*',
                    DB::raw("CONCAT_WS(' to ', r.dis_re_doe_from, r.dis_re_doe_to) AS date_of_execution"),
                    'i.hr_griv_issue_name',
                    's.hr_griv_steps_name'
                )
                ->leftJoin('hr_grievance_issue AS i', 'i.hr_griv_issue_id', '=', 'r.dis_re_issue_id')
                ->leftJoin('hr_grievance_steps AS s', 's.hr_griv_steps_id', '=', 'r.dis_re_issue_id')
                ->where('r.dis_re_offender_id', $request->associate_id)
                ->get();


            $promotions = DB::table("hr_promotion AS p")
                ->select(
                    "d1.hr_designation_name AS previous_designation",
                    "d2.hr_designation_name AS current_designation",
                    "p.eligible_date",
                    "p.effective_date"
                )
                ->leftJoin("hr_designation AS d1", "d1.hr_designation_id", "=", "p.previous_designation_id")
                ->leftJoin("hr_designation AS d2", "d2.hr_designation_id", "=", "p.current_designation_id")
                ->where('p.associate_id', $request->associate_id)
                ->orderBy('p.effective_date', "DESC")
                ->get();

            $increments = Increment::where('associate_id', $request->associate_id)
                ->orderBy('effective_date', 'DESC')->get();

            $educations = DB::table('hr_education AS e')
                ->select(
                    'l.education_level_title',
                    'dt.education_degree_title',
                    'e.education_level_id',
                    'e.education_degree_id_2',
                    'e.education_major_group_concentation',
                    'e.education_institute_name',
                    'r.education_result_title',
                    'e.education_result_id',
                    'e.education_result_marks',
                    'e.education_result_cgpa',
                    'e.education_result_scale',
                    'e.education_passing_year'
                )
                ->leftJoin('hr_education_level AS l', 'l.id', '=', 'e.education_level_id')
                ->leftJoin('hr_education_degree_title AS dt', 'dt.id', '=', 'e.education_degree_id_1')
                ->leftJoin('hr_education_result AS r', 'r.id', '=', 'e.education_result_id')
                ->where("e.education_as_id", $request->associate_id)
                ->get();


            //check current station
            $station= DB::table('hr_station AS s')
                        ->where('s.associate_id', $request->associate_id)
                        ->whereDate('s.start_date', "<=", date('Y-m-d'))
                        ->orWhereDate('s.end_date', ">=", date("Y-m-d"))
                        ->select([
                            "s.associate_id",
                            "s.changed_floor",
                            "s.changed_line",
                            "s.start_date",
                            "s.updated_by",
                            "s.end_date",
                            "f.hr_floor_name",
                            "l.hr_line_name",
                            "u.name"
                        ])
                        ->leftJoin('hr_floor AS f', 'f.hr_floor_id', 's.changed_floor')
                        ->leftJoin('hr_line AS l', 'l.hr_line_id', 's.changed_line')
                        ->leftJoin('users AS u', 'u.id', 's.created_by')
                        ->first();
            

        $getSalaryList      = HrMonthlySalary::where('as_id', $request->associate_id)
                            ->where('year',date('Y'))
                            ->get();
        $getEmployee        = Employee::getEmployeeAssociateIdWise($request->associate_id);
        $title              = 'Unit : '.($getEmployee->unit != null?$getEmployee->unit['hr_unit_name_bn']:'').' - Location : '.($getEmployee->location != null?$getEmployee->location['hr_unit_name_bn']:'');
        $pageHead['current_date']   = date('d-m-Y');
        $pageHead['current_time']   = date('H:i');
        $pageHead['pay_date']       = '';
        $pageHead['unit_name']      = $getEmployee->unit['hr_unit_name_bn']??'';
        $pageHead['for_date']       = 'Jan, '.date('Y').' - '.date('M, Y');
        $pageHead['floor_name']     = ($getEmployee->floor != null?$getEmployee->floor['hr_floor_name_bn']:'');

        $pageHead = (object) $pageHead;
            return view('hr.recruitment.employee', compact(
                'info',
                'loans',
                'leaves',
                'earnedLeaves',
                'records',
                'promotions',
                'increments',
                'educations',
                "station",
                'getSalaryList',
                'title',
                'pageHead',
                'status',
                'per_complete',
                'getEmployee',
                'roasterShift'
            ));
        }
        else
        {
            abort(404);
        }
    }

    #Generate pdf for each employee
    # Show User by Associate ID
    public function pdfEmployee(Request $request)
    {
        //ACL::check(["permission" => "hr_recruitment_employer_list"]);
        #-----------------------------------------------------------#

        if (!empty($request->associate_id))
        {
            $info = get_employee_by_id($request->associate_id);
            if(empty($info)) abort(404, "$request->associate_id not found!");

            $loans = DB::table("hr_loan_application")
                ->select(
                "*",
                DB::raw("
                    CASE
                        WHEN hr_la_status = '0' THEN 'Applied'
                        WHEN hr_la_status = '1' THEN 'Approved'
                        WHEN hr_la_status = '2' THEN 'Declined'
                    END AS hr_la_status
                ")
            )
            ->where("hr_la_as_id", $request->associate_id)
            ->get();

            $leaves = DB::table('hr_leave')
                ->select(
                    DB::raw("
                        YEAR(leave_from) AS year,
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual,
                        SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned,
                        SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick,
                        SUM(CASE WHEN leave_type = 'Maternity' THEN DATEDIFF(leave_to, leave_from)+1 END) AS maternity,
                        SUM(DATEDIFF(leave_to, leave_from)+1) AS total
                    ")
                )
                ->where('leave_status', '1')
                ->where("leave_ass_id", $request->associate_id)
                ->groupBy('year')
                ->orderBy('year', 'DESC')
                ->get();
            $earnedLeaves = get_earned_leave($leaves,$info->as_id,$info->associate_id,$info->as_unit_id);

            $information = DB::table("hr_as_basic_info AS b")
            ->select(
              "b.as_id AS id",
              "b.associate_id AS associate",
              "b.as_name AS name",
              "b.as_doj AS doj",
              "u.hr_unit_id AS unit_id",
              "u.hr_unit_name AS unit",
              "s.hr_section_name AS section",
              "d.hr_designation_name AS designation"
            )
            ->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id")
            ->leftJoin("hr_section AS s", "s.hr_section_id", "=", "b.as_section_id")
            ->leftJoin("hr_designation AS d", "d.hr_designation_id", "=", "b.as_designation_id")
            ->where("b.associate_id", "=", $request->associate_id)
            ->first();

            $records = DB::table('hr_dis_rec AS r')
                ->select(
                    'r.*',
                    DB::raw("CONCAT_WS(' to ', r.dis_re_doe_from, r.dis_re_doe_to) AS date_of_execution"),
                    'i.hr_griv_issue_name',
                    's.hr_griv_steps_name'
                )
                ->leftJoin('hr_grievance_issue AS i', 'i.hr_griv_issue_id', '=', 'r.dis_re_issue_id')
                ->leftJoin('hr_grievance_steps AS s', 's.hr_griv_steps_id', '=', 'r.dis_re_issue_id')
                ->where('r.dis_re_offender_id', $request->associate_id)
                ->get();


            $promotions = DB::table("hr_promotion AS p")
                ->select(
                    "d1.hr_designation_name AS previous_designation",
                    "d2.hr_designation_name AS current_designation",
                    "p.eligible_date",
                    "p.effective_date"
                )
                ->leftJoin("hr_designation AS d1", "d1.hr_designation_id", "=", "p.previous_designation_id")
                ->leftJoin("hr_designation AS d2", "d2.hr_designation_id", "=", "p.current_designation_id")
                ->where('p.associate_id', $request->associate_id)
                ->orderBy('p.effective_date', "DESC")
                ->get();

            $increments = Increment::where('associate_id', $request->associate_id)
                ->orderBy('effective_date', 'DESC')->get();

            $educations = DB::table('hr_education AS e')
                ->select(
                    'l.education_level_title',
                    'dt.education_degree_title',
                    'e.education_level_id',
                    'e.education_degree_id_2',
                    'e.education_major_group_concentation',
                    'e.education_institute_name',
                    'r.education_result_title',
                    'e.education_result_id',
                    'e.education_result_marks',
                    'e.education_result_cgpa',
                    'e.education_result_scale',
                    'e.education_passing_year'
                )
                ->leftJoin('hr_education_level AS l', 'l.id', '=', 'e.education_level_id')
                ->leftJoin('hr_education_degree_title AS dt', 'dt.id', '=', 'e.education_degree_id_1')
                ->leftJoin('hr_education_result AS r', 'r.id', '=', 'e.education_result_id')
                ->where("e.education_as_id", $request->associate_id)
                ->get();


            //check current station
            $station= DB::table('hr_station AS s')
                        ->where('s.associate_id', $request->associate_id)
                        ->whereDate('s.start_date', "<=", date('Y-m-d'))
                        ->whereDate('s.end_date', ">=", date("Y-m-d"))
                        ->select([
                            "s.associate_id",
                            "s.changed_floor",
                            "s.changed_line",
                            "s.start_date",
                            "s.updated_by",
                            "s.end_date",
                            "f.hr_floor_name",
                            "l.hr_line_name",
                            "b.as_name"
                        ])
                        ->leftJoin('hr_floor AS f', 'f.hr_floor_id', 's.changed_floor')
                        ->leftJoin('hr_line AS l', 'l.hr_line_id', 's.changed_line')
                        ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 's.updated_by')
                        ->first();

        $getSalaryList      = HrMonthlySalary::where('as_id', $request->associate_id)
                            ->where('year',2019)
                            ->get();
        $getEmployee        = Employee::getEmployeeAssociateIdWise($request->associate_id);
        $title              = 'Unit : '.$getEmployee->unit['hr_unit_name_bn']??''.' - Location : '.$getEmployee->location['hr_unit_name_bn'];
        $pageHead['current_date']   = date('d-m-Y');
        $pageHead['current_time']   = date('H:i');
        $pageHead['pay_date']       = '';
        $pageHead['unit_name']      = $getEmployee->unit['hr_unit_name_bn']??'';
        $pageHead['for_date']       = 'Jan, '.date('Y').' - '.date('M, Y');
        $pageHead['floor_name']     = $getEmployee->floor['hr_floor_name_bn'];

        $pageHead = (object) $pageHead;

            /*return view('hr.recruitment.employee', compact(
                'info',
                'loans',
                'leaves',
                'records',
                'promotions',
                'increments',
                'educations',
                "station",
                'getSalaryList',
                'title',
                'pageHead'
            ));*/
            $pdf = PDF::loadView('hr.recruitment.pdf_employee', [
                'info'           =>$info,
                'loans'          =>$loans,
                'leaves'         =>$leaves,
                'records'        =>$records,
                'promotions'     =>$promotions,
                'increments'     =>$increments,
                'educations'     =>$educations,
                'getSalaryList'  =>$getSalaryList,
                'title'          =>$title,
                'pageHead'       =>$pageHead,
                'earnedLeaves'   =>$earnedLeaves
              ]);
              return $pdf->download('Employee_Report_'.$info->associate_id.'_'.date('d_F_Y').'.pdf');
        }
        else
        {
            abort(404);
        }
    }
   
    # Edit User by Associate ID
    public function edit(Request $request)
    {
        $id=$request->associate_id;


        $get_as_id = Employee::where('associate_id', $id)->first(['as_id']);
        $m_restriction=  auth()->user()->management_permissions(); //dd($m_restriction);
        $as_id=$get_as_id->as_id;

        // check if  id is restricted
        if (in_array($as_id, $m_restriction)) {
            return redirect()->to('hr/recruitment/employee_list')->with('error', 'You do not have permission!');
        }

        else{

            $employee  = get_employee_by_id($id);
            // $all_employee_list  = Employee::select('as_id', 'associate_id', 'as_name')->get();
            $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
            $unitList  = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->pluck('hr_unit_short_name', 'hr_unit_id');
            $locationList  = Location::pluck('hr_location_short_name', 'hr_location_id');

            $floorList  = Floor::where('hr_floor_status', '1')
                                ->where('hr_floor_unit_id', $employee->as_unit_id)
                                ->pluck('hr_floor_name', 'hr_floor_id');

            $lineList  = Line::where('hr_line_status', '1')
                                ->where('hr_line_unit_id', $employee->as_unit_id)
                                ->where('hr_line_floor_id', $employee->as_floor_id)
                                ->pluck('hr_line_name', 'hr_line_id');

            $shiftList  = Shift::where('hr_shift_status', '1')
                                ->where('hr_shift_unit_id', $employee->as_unit_id)
                                ->pluck('hr_shift_name', 'hr_shift_name');
            
            $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

            $departmentList  = Department::where("hr_department_area_id", $employee->as_area_id)
                    ->where("hr_department_status", 1)
                    ->pluck('hr_department_name', "hr_department_id");
            $sectionList = Section::where("hr_section_department_id", $employee->as_department_id)
                ->where("hr_section_status", 1)
                ->pluck("hr_section_name", "hr_section_id");
            $subsectionList = Subsection::where("hr_subsec_section_id", $employee->as_section_id)
                ->where("hr_subsec_status", 1)
                ->pluck("hr_subsec_name", "hr_subsec_id");

            $designationList  = Designation::where('hr_designation_status', '1')->distinct()->orderBy('hr_designation_name', 'ASC')->select('hr_designation_name','hr_designation_id')->get();

            //Cost mapping status(Unit)
            $cost_mapping_unit_status= MapCostUnit::where('associate_id', $request->associate_id)->exists();
            //Cost Mapping status(Area)
            $cost_mapping_area_status= MapCostArea::where('associate_id', $request->associate_id)->exists();

            return view("hr.recruitment.employee_basic_info_edit", compact(
                'employee',
                // 'all_employee_list',
                'employeeTypes',
                'unitList',
                'floorList',
                'lineList',
                'shiftList',
                'areaList',
                'departmentList',
                'sectionList',
                'subsectionList',
                'designationList',
                'cost_mapping_unit_status',
                'cost_mapping_area_status',
                'locationList'
            ));
      }
    }


    public function updateEmployee(Request $request)
    {
        $map = new CostMappingController;
        if($request->has('unit_map_checkbox')){
            $map->defaultCostMapUnit($request->associate_id, $request->as_emp_type_id);
        }
        if($request->has('area_map_checkbox')){
            $map->defaultCostMapArea($request->associate_id, $request->as_emp_type_id);
        }

        //ACL::check(["permission" => "hr_recruitment_employer_list"]);
        #-----------------------------------------------------------#
        $validator = Validator::make($request->all(), [
            'as_emp_type_id'    => 'required',
            'as_area_id'        => 'required',
            'as_department_id'  => 'required',
            'as_section_id'     => 'required',
            'as_subsection_id'  => 'required',
            'as_shift_id'       => 'required',
            'as_doj'            => 'required|date',
            'temp_id'           => 'required|max:6|min:6',
            'as_name'           => 'required|max:128',
            'as_gender'         => 'required|max:10',
            'as_dob'            => 'required|date',
            'as_contact'        => 'required',
            'as_ot'             => 'required|max:1',
            'as_pic'            => 'image|mimes:jpeg,png,jpg|max:200',
            'as_oracle_code'    => 'max:20',
            'as_rfid_code'      => 'max:20',
        ]);

        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        $input = $request->all();
        $employeeOldStatus = Employee::select('as_status')->where('as_id', $input['as_id'])->pluck('as_status')->first();
       
        DB::beginTransaction();
        
        try {
            // return $request->all();
            //-----------IMAGE UPLOAD---------------------
            $as_pic = $request->old_pic;
            if($request->hasFile('as_pic'))
            {
                $file = $request->file('as_pic');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $as_pic = '/assets/images/employee/' . $filename;
                Image::make($file)->resize(800, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                    })
                                   ->save(public_path( $as_pic ) );
            }
            //getting previous designation
            $prev_dsg= Employee::where('as_id', $request->as_id)->pluck('as_designation_id')->first();
            //updated by current user
            $user= Auth()->user()->associate_id;
            if($prev_dsg != $request->as_designation_id){

                Employee::where('as_id', $request->as_id)->update([
                'as_designation_id' => $request->as_designation_id
                ]);

                DesignationUpdateLog::insert([
                    'associate_id' =>$request->associate_id,
                    'previous_designation' =>$prev_dsg,
                    'updated_designation' =>$request->as_designation_id,
                    'updated_by' =>$user
                ]);
            }

            //-----------Store Data---------------------
            $update = Employee::where('as_id', $request->as_id)->update([
                'as_emp_type_id'   => $request->as_emp_type_id,
                'as_unit_id'       => $request->as_unit_id,
                'as_floor_id'      => $request->as_floor_id,
                'as_line_id'       => $request->as_line_id,
                'as_shift_id'      => $request->as_shift_id,
                'as_area_id'       => $request->as_area_id,
                'as_department_id' => $request->as_department_id,
                'as_section_id'    => $request->as_section_id,
                'as_subsection_id' => $request->as_subsection_id,
                // 'as_designation_id' => $request->as_designation_id,
                'as_doj'           => (!empty($request->as_doj)?date('Y-m-d',strtotime($request->as_doj)):null),
                // 'temp_id'        => $request->temp_id,
                // 'associate_id'   => $request->associate_id,
                'as_name'          => strtoupper($request->as_name),
                'as_gender'        => $request->as_gender,
                'as_dob'           => (!empty($request->as_dob)?date('Y-m-d',strtotime($request->as_dob)):null),
                'as_contact'       => $request->as_contact,
                'as_ot'            => $request->as_ot,
                'as_pic'           => $as_pic,
                'as_remarks'       => $request->as_remarks,
                'as_oracle_code'   => $request->as_oracle_code,
                'as_oracle_sl'     => ($request->as_oracle_code != ''?substr($request->as_oracle_code,3, -1):''),
                'as_rfid_code'     => $request->as_rfid_code,
                'as_location'      => $request->as_location_id
            ]);

            
            $this->logFileWrite("Employee Data Updated", $request->as_id);
            Cache::forget('employee_count');
            DB::commit();
            return back()->with('success', 'Update Successful.');
            
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return back()->with('error', $bug);
        }
    }

    # Search Associate ID returns NAME & ID
    public function associtaeSearch(Request $request)
    {

        $data = [];
        if($request->has('keyword'))
        {
            $search = $request->keyword;
            $data = Employee::select("associate_id","as_pic","as_oracle_code",DB::raw('CONCAT_WS(" - ", associate_id, as_name) AS associate_name'))
                ->where(function($q) use($search) {
                    $q->where("associate_id", "LIKE" , "%{$search}%");
                    $q->orWhere("as_name", "LIKE" , "%{$search}%");
                    $q->orWhere("as_oracle_code", "LIKE" , "%{$search}%");
                })
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions())
                ->whereNotIn('as_id', auth()->user()->management_permissions())
                ->take(20)
                ->get();
        }

        return response()->json($data);
    }

    public function femaleSearch(Request $request)
    {
        $data = [];
        if($request->has('keyword'))
        {
            $search = $request->keyword;
            $data = Employee::select("associate_id","as_pic","as_oracle_code",DB::raw('CONCAT_WS(" - ", associate_id, as_name) AS associate_name'))
                ->where(function($q) use($search) {
                    $q->where("associate_id", "LIKE" , "%{$search}%");
                    $q->orWhere("as_name", "LIKE" , "%{$search}%");
                    $q->orWhere("as_oracle_code", "LIKE" , "%{$search}%");
                })
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions())
                ->whereNotIn('as_id', auth()->user()->management_permissions())
                ->where('as_gender','Female')
                ->take(20)
                ->get();
        }

        return response()->json($data);
    }

    # Search Associate Info. Returns All Information
    public function associtaeInfo(Request $request)
    {
        $data = [];
        if($request->has('associate_id'))
        {
            $data = get_employee_by_id($request->associate_id);
        }
        return response()->json($data);
    }

    # Associate Tags
    public function associateTags(Request $request)
    {
        if($request->has('keyword'))
        {
            return Employee::select(DB::raw('associate_id AS associate_info'))
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions())
                ->where("associate_id", "LIKE" , "%{$request->keyword}%" )
                ->orWhere('as_name', "LIKE" , "%{$request->keyword}%" )
                ->pluck('associate_info');
        }
        else
        {
            return 'No Employee Found!';
        }
    }

    /*
    *--------------------------------------------------------
    * ID CARD GENERATE
    *--------------------------------------------------------
    */
    # Associate ID CARD
    public function idCard()
    {
        //ACL::check(["permission" => "hr_time_id_card"]);
        #-----------------------------------------------------------#

        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name', 'hr_unit_id');

        return view('hr/recruitment/idcard', compact(
            'employeeTypes',
            'unitList'
        ));
    }

    # Associate Floor List by Unit
    public function idCardFloorListByUnit(Request $request)
    {
        $data['floorList'] = "<option value=\"\">Select Floor</value>";
        $floors = Floor::where('hr_floor_unit_id', $request->unit)->pluck('hr_floor_name', 'hr_floor_id');
        foreach($floors as $key => $floor)
        {
            $data['floorList'] .= "<option value=\"$key\">$floor</value>";
        }
        return $data;
    }


    # Associate Unit, Floor by Line List
    public function idCardLineListByUnitFloor(Request $request)
    {
        // create line list
        $data['lineList'] = "<option value=\"\">Select Line</value>";
        $lines = line::where('hr_line_unit_id', $request->unit)
            ->where('hr_line_floor_id', $request->floor)
            ->pluck('hr_line_name', 'hr_line_id');
        foreach($lines as $key => $line)
        {
            $data['lineList'] .= "<option value=\"$key\">$line</value>";
        }
        return $data;
    }

    #  filter Associate
    public function filterAssociate(Request $request)
    {
        // employee type wise data
        $employees = [];
        $type   = $request->emp_type;
        $unit   = $request->unit;
        $floor  = $request->floor;
        $line   = $request->line;
        $doj_from = $request->doj_from;
        $doj_to = $request->doj_to;
        #-----------------------------------------------------------
        $employees = Employee::where(function($q) use($type, $unit, $floor, $line, $doj_from, $doj_to) {
                if (!empty($type))
                {
                    $q->where('as_emp_type_id', $type);
                }
                if (!empty($unit))
                {
                    $q->where('as_unit_id', $unit);
                }
                if (!empty($floor))
                {
                    $q->where('as_floor_id', $floor);
                }
                if (!empty($line))
                {
                    $q->where('as_line_id', $line);
                }
                if (!empty($doj_from) )
                {
                    $q->where('as_doj', '>=', $doj_from);
                }
                if (!empty($doj_to))
                {
                    $q->where('as_doj', '<=', $doj_to);
                }
            })
            ->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions())
            ->where('as_status', 1)
            ->get();


        // show user id
        $data['result'] = null;
        $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
        foreach($employees as $employee)
        {
            $data['result'] .= "<tr>
                <td><input name=\"associate_id[]\" type=\"checkbox\" class=\"associate-select\" value=\"$employee->associate_id\"></td>
                <td>$employee->associate_id</td>
                <td>$employee->as_oracle_code</td>
                <td>$employee->as_name</td>
            </tr>";
        }
        return $data;
    }


    # Associate ID CARD Search
    public function idCardSearch(Request $request)
    {
        //ACL::check(["permission" => "hr_time_id_card"]);
        #-----------------------------------------------------------#

        if (!is_array($request->associate_id) || sizeof($request->associate_id) == 0)
            return response()->json(['idcard'=>'<div class="alert alert-danger">Please Select Associate ID</div>', 'printbutton'=>'']);


        $employees = [];
        $employees = DB::table('hr_as_basic_info AS b')
            ->select(
                'b.as_id',
                'b.associate_id',
                'b.as_oracle_code',
                'b.as_emp_type_id',
                'b.temp_id',
                'b.as_pic',
                'b.as_gender',
                'u.hr_unit_name',
                'u.hr_unit_name_bn',
                'u.hr_unit_logo',
                'u.hr_unit_authorized_signature',
                'u.hr_unit_address_bn',
                'u.hr_unit_telephone',
                'b.as_name',
                'b.as_contact',
                'bn.*',
                'b.as_doj',
                'd.hr_department_name',
                'd.hr_department_name_bn',
                'dg.hr_designation_name',
                'dg.hr_designation_name_bn',
                's.hr_section_name_bn',
                's.hr_section_name',
                'm.med_blood_group',
                'ad.emp_adv_info_per_upz',
                'ad.emp_adv_info_per_dist',
                'ad.emp_adv_info_nid',
                'ad.emp_adv_info_emg_con_num'
            )
            ->leftJoin('hr_employee_bengali AS bn','bn.hr_bn_associate_id', 'b.associate_id')
            ->leftJoin('hr_as_adv_info AS ad','ad.emp_adv_info_as_id', 'b.associate_id')
            ->leftJoin('hr_unit AS u','u.hr_unit_id', 'b.as_unit_id')
            ->leftJoin('hr_department AS d','d.hr_department_id', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg','dg.hr_designation_id', 'b.as_designation_id')
            ->leftJoin('hr_section AS s','s.hr_section_id', 'b.as_section_id')
            ->leftJoin('hr_med_info AS m','m.med_as_id', 'b.associate_id')
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->whereIn('b.associate_id', $request->associate_id)
            ->get();

        $type = $request->type;
        $districts = DB::table('hr_dist')->pluck('dis_name_bn','dis_id');
        $upzillas = DB::table('hr_upazilla')->pluck('upa_name_bn','upa_id');

        $data['idcard'] = view('hr.common.idcard_view', compact('employees','type','districts','upzillas'))->render();
       

        $data['printbutton'] = "";
        if (strlen($data['idcard'])>1)
        {
            $data['printbutton'] .= "<button onclick=\"printContent('idCardPrint')\" type=\"button\" class=\"btn btn-success btn-xs\"><i class=\"fa fa-print\"></i> Print</button>";
        }

        return response()->json($data);
    }

    /*
    *--------------------------------------------------------
    * EMPLOYEE HIERARCHY
    *--------------------------------------------------------
    */
    public function hierarchy()
    {
        //ACL::check(["permission" => "hr_recruitment_employer_list"]);
        #-----------------------------------------------------------#
        $employeeTypes = EmpType::where('hr_emp_type_status', '1')
            ->distinct()
            ->orderBy('hr_emp_type_name', 'ASC')
            ->pluck('hr_emp_type_name');

        return view('hr.recruitment.employee_hierarchy', compact(
            'employeeTypes'
        ));
    }

    public function getHierarchy()
    {

        $data = DB::table('hr_as_basic_info AS b')
            ->select(
                'e.hr_emp_type_name',
                'b.as_name',
                'b.associate_id',
                'u.hr_unit_name',
                'd.hr_department_name',
                'dg.hr_designation_name'
            )
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'b.as_emp_type_id')
            ->leftJoin('hr_unit AS u','u.hr_unit_id', 'b.as_unit_id')
            ->leftJoin('hr_department AS d','d.hr_department_id', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg','dg.hr_designation_id', 'b.as_designation_id')
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->orderBy("dg.hr_designation_position", "DESC")
            ->orderBy("b.as_id", "DESC")
            ->orderBy("e.emp_type_id", "ASC")
            ->get();


        return Datatables::of($data)
            ->editColumn('name', function($data){
                return "$data->as_name <br/> ($data->associate_id)";
            })
            ->rawColumns(["name"])
            ->make(true);
    }

    public function statusUpdate(Request $request)
    {
        $input = $request->all();
        // return $input;
        try {
            $employee = Employee::getEmployeeAssociateIdWise($input['associate_id']);
            if($employee != null){
                Employee::where('as_id', $employee->as_id)
                ->update([
                    'as_status'      => $input['as_status'],
                    'as_status_date' => $input['as_status_date'],
                    'as_remarks'     => $input['as_remarks']
                ]);
            }
            toastr()->success('Successful Status Updated');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return $bug;
        }
    }

    public function getAssociateBy(Request $request)
    {   
        // dd($request->all());exit;
        $employees = Employee::where(function($query) use ($request){
            if ($request->unit != null)
            {
                $query->where('as_unit_id', $request->unit);
            }
            if ($request->otnonot != null)
            {
                $query->where('as_ot', $request->otnonot);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->section != null)
            {
                $query->where('as_section_id', $request->section);
            }
            if ($request->subsection != null)
            {
                $query->where('as_subsection_id', $request->subsection);
            }
            if ($request->line_id != null)
            {
                $query->where('as_line_id', $request->line_id);
            }
            if ($request->shift_id != null)
            {
                $query->where('as_shift_id', $request->shift_id);
            }
            $query->where("as_status", 1);
        })
        ->whereIn('as_unit_id', auth()->user()->unit_permissions())
        ->get();

        // show user id
        $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
        $data['result'] = "";
        $data['total'] = count($employees);
        foreach($employees as $employee)
        {
            $image = ($employee->as_pic == null?'/assets/images/avatars/profile-pic.jpg': $employee->as_pic);
            $data['result'].= "<tr class='add'>
                        <td><input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/></td><td><span class=\"lbl\"> <img src='".emp_profile_picture($employee)."' class='small-image min-img-file'> </span></td><td><span class=\"lbl\"> $employee->associate_id</span></td>
                        <td>$employee->as_oracle_code </td>
                        <td>$employee->as_name </td></tr>";
        }

        return $data;
    }
}
