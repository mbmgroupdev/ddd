<?php

namespace App\Models;
use App\Models\Employee;
use App\Models\Hr\Leave;
use App\Models\Hr\Shift;
use Awobaz\Compoships\Compoships;
use DB;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
	use Compoships;

    //public $with = ['employee_bengali','designation', 'unit', 'floor', 'department'];
	//public $with = ['designation'];
    protected $table = "hr_as_basic_info";
    protected $guarded = [];

    protected $dates = [
        'as_doj', 'created_at', 'updated_at'
    ];

    public static function getEmployeeAssociateIdWise($as_id)
    {
    	return Employee::where('associate_id', $as_id)->first();
    }

    public static function getEmployeeAssIdWiseSelectedField($associate_id, $selectedField)
    {
        $query = DB::table('hr_as_basic_info')
        ->where('associate_id', $associate_id);
        if($selectedField != 'all'){
            $query->select($selectedField);
        }
        return $query->first();
    }

    public static function getSelectIdNameEmployee()
    {
        return Employee::select('as_id', 'as_name', 'associate_id')->get();
    }
    
    public function designation()
    {
    	return $this->belongsTo('App\Models\Hr\Designation', 'as_designation_id', 'hr_designation_id');
    }
    
    public function benefits()
    {
        return $this->hasOne(Benefits::class, 'ben_as_id', 'associate_id');
    }

    public static function getEmployeeFilterWise($data)
    {
        $query = Employee::select('as_id', 'associate_id', 'as_unit_id', 'as_location');
        if($data['unit']){
            if($data['unit'] == 1){
                $query->whereIn('as_unit_id', [1,4,5]);
            }else{
                $query->where('as_unit_id', $data['unit']);
            }
            // $query->orWhere('as_location', $data['unit']);
        }
        if(isset($data['floor'])){
            $query->where('as_floor_id', $data['floor']);
        }
        if(isset($data['section'])){
            $query->where('as_section_id', $data['section']);
        }
        if(isset($data['sub_section'])){
            $query->where('as_subsection_id', $data['sub_section']);
        }
        if(isset($data['area'])){
            $query->where('as_area_id', $data['area']);
        }
        if(isset($data['department'])){
            $query->where('as_department_id', $data['department']);
        }
        if(isset($data['employee_status'])){
            $query->where('as_status',(int)$data['employee_status']);
        }
        return $query->get();
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Hr\Unit', 'as_unit_id', 'hr_unit_id');
    }

    public function floor()
    {
        return $this->belongsTo('App\Models\Hr\Floor', 'as_floor_id', 'hr_floor_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Hr\Department', 'as_department_id', 'hr_department_id');
    }

    public function section()
    {
        return $this->belongsTo('App\Models\Hr\Section', 'as_section_id', 'hr_section_id');
    }

    public function line()
    {
        return $this->belongsTo('App\Models\Hr\Line', 'as_line_id', 'hr_line_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Hr\Unit', 'as_location', 'hr_unit_id');
    }

    // public function shift()
    // {
    //     return $this->hasOne('App\Models\Hr\Shift', 'hr_shift_id', 'as_shift_id');
    // }
    public function shift()
    {
        return $this->belongsTo(Shift::class, ['as_unit_id', 'as_shift_id'], ['hr_shift_unit_id', 'hr_shift_name'])->latest();
    }

    public function get_shift_name()
    {
        $shifts = Shift::select('hr_shift_id','hr_shift_name','hr_shift_unit_id')->latest()->get()->groupBy('hr_shift_name','hr_shift_unit_id')->toArray();

        dd($shifts);
    }

    public function salary()
    {
        return $this->belongsTo('App\Models\Hr\HrMonthlySalary', 'associate_id', 'as_id');
    }

    public function employee_bengali()
    {
        return $this->belongsTo('App\Models\Hr\EmployeeBengali', 'associate_id', 'hr_bn_associate_id');
    }

    public static function getSingleEmployeeWiseSalarySheet($data)
    {
        $query = Employee::
        where('hr_as_basic_info.associate_id', $data['as_id'])
        // ->whereHas('salary', function($query) use ($data)
        // {
        //     $query->where('year', '>=', $data['formYear']);
        //     $query->where('year', '<=', $data['toYear']);
        //     $query->where('month', '>=', $data['formMonth']);
        //     $query->where('month', '<=', $data['toMonth']);
        // })
        ->with(array('salary'=>function($query) use ($data){
            $query->where('year', '>=', $data['formYear']);
            $query->where('year', '<=', $data['toYear']);
            $query->where('month', '>=', $data['formMonth']);
            $query->where('month', '<=', $data['toMonth']);
         }));

        return $query;
    }

    public static function getEmployeeWiseSalarySheet($data)
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
    
        $yearMonth = $data['year'].' '.$data['month'];
      
        $userIdNotAccessible = DB::table('roles')
                ->whereIn('name',$cantacces)
                ->leftJoin('model_has_roles','roles.id','model_has_roles.role_id')
                ->pluck('model_has_roles.model_id');

        $asIds = DB::table('users')
                   ->whereIn('id',$userIdNotAccessible)
                   ->pluck('associate_id');

        $query = Employee::where('hr_as_basic_info.as_unit_id', $data['unit'])
        ->whereNotIn('hr_as_basic_info.associate_id',$asIds)
        ->where('hr_as_basic_info.as_status', $data['employee_status'])
        ->where(DB::raw("(DATE_FORMAT(as_doj,'%Y-%m'))"), '<=',$yearMonth)
        ->with(array('salary'=>function($query) use ($data)
        {
            $query->where('month', $data['month']);
            $query->where('year', $data['year']);
            $query->where('gross', '>=', $data['min_sal']);
            $query->where('gross', '<=', $data['max_sal']);
            if(isset($data['disbursed']) && $data['disbursed'] != null){
                if($data['disbursed'] == 1){
                    $query->where('disburse_date', '!=', null);
                }else{
                    $query->where('disburse_date', null);
                }
            }
        }));
        if($data['floor']){
            $query->where('hr_as_basic_info.as_floor_id', $data['floor']);
        }

        if($data['area']){
            $query->where('hr_as_basic_info.as_area_id', $data['area']);
        }

        if($data['department']){
            $query->where('hr_as_basic_info.as_department_id', $data['department']);
        }

        if($data['section']){
            $query->where('hr_as_basic_info.as_section_id', $data['section']);
        }

        if($data['sub_section']){
            $query->where('hr_as_basic_info.as_subsection_id', $data['sub_section']);
        }

        if(isset($data['as_ot'])){
            $query->where('hr_as_basic_info.as_ot', $data['as_ot']);
        }
        return $query;

    }

    public static function getSearchKeyWise($value)
    {
        return Employee::
        where('associate_id', 'LIKE', '%'. $value .'%')
        ->orWhere('as_name', 'LIKE', '%'. $value . '%')
        ->orWhere('as_oracle_code', 'LIKE', '%'. $value . '%')
        ->paginate(10);
    }

    public static function getSearchGlobalKeyWise($value)
    {
        return DB::table('hr_as_basic_info')
        ->select('as_name', 'associate_id')
        ->where('associate_id', 'LIKE', '%'. $value .'%')
        ->orWhere('as_name', 'LIKE', '%'. $value . '%')
        ->orWhere('as_oracle_code', 'LIKE', '%'. $value . '%')
        ->limit(10)
        ->get();
    }

    public static function getEmployeeShiftIdWise($shiftId, $unitId)
    {
        return Employee::select('as_id')
        ->where('as_shift_id', $shiftId)
        ->where('as_unit_id', $unitId)
        ->get();
    }
    
    public  function today_status()
    {
        $today = date('Y-m-d');
        $table = get_att_table($this->as_unit_id);
        
        $att = DB::table($table)->where([
                'as_id' => $this->as_id,
                'in_date' => $today
            ])->first();

        $leave = Leave::where('leave_from', '=<', $today)
                    ->where('leave_to', '>=', $today)
                    ->where('leave_ass_id', $this->associate_id)
                    ->first();

        // if leave and att both exists
        $data = array();

        if($att != null && $leave != null){
            $data['status'] = 'Leave';
            $data['info'] = $leave;
        }else if($att != null && $leave == null){
            $data['status'] = 'Leave';
            $data['info'] = $att;
        }else{
            $data['status'] = 'Absent';
        }
        return $data;
    }

    public  function job_duration($date)
    {
        $joind = \Carbon\Carbon::createFromFormat('Y-m-d', $this->as_doj);
        $thisday = \Carbon\Carbon::createFromFormat('Y-m-d', $date);

        $diff = round($joind->floatDiffInMonths($thisday));
        

        return (int) $diff;
    }
}
