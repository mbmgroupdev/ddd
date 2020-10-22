<?php

namespace App\Http\Controllers\Hr\Recruitment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hr\EmpType;
use App\Models\Hr\WorkerRecruitment;
use App\Models\Employee;
use App\Models\Hr\AdvanceInfo;
use App\Models\Hr\MedicalInfo;
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Designation;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Location;
use Auth, Validator, DB, Cache;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Hr\IDGenerator as IDGenerator;

class RecruitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hr.recruitment.recruit.list');
    }

    public function bulk()
    {
        return view('hr.recruitment.recruit.bulk');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        DB::statement(DB::raw('set @rownum=0'));
        $data = WorkerRecruitment::with(['employee_type:emp_type_id,hr_emp_type_name', 'designation:hr_designation_id,hr_designation_name','unit:hr_unit_id,hr_unit_short_name', 'area:hr_area_id,hr_area_name'])
        ->where('worker_is_migrated','!=' ,1)
        ->whereIn('worker_unit_id', auth()->user()->unit_permissions())
        ->orderBy('worker_id','DESC')->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('hr_emp_type_name', function ($data) {
            return $data->employee_type['hr_emp_type_name']??'';
        })
        ->addColumn('hr_designation_name', function ($data) {
            return $data->designation['hr_designation_name']??'';
        })
        ->addColumn('hr_unit_short_name', function ($data) {
            return $data->unit['hr_unit_short_name']??'';
        })
        ->addColumn('hr_area_name', function ($data) {
            return $data->area['hr_area_name']??'';
        })
        ->addColumn('worker_doj', function ($data) {
            return date('Y-m-d', strtotime($data->worker_doj));
        })
        ->addColumn('medical_info', function ($data) {
            if($data->worker_doctor_acceptance == 1){
                return '<i class="las f-18 la-check-circle text-success"></i>';
            }else{
                return '<i class="las f-18 la-times-circle text-danger"></i>';
            }
        })
        ->addColumn('ie_info', function ($data) {
            if($data->worker_pigboard_test == 1 || $data->worker_finger_test == 1 || $data->worker_color_join == 1 || $data->worker_color_band_join == 1 || $data->worker_clip_join == 1 || $data->worker_box_pleat_join == 1 || $data->worker_color_top_stice){
                return '<i class="las f-18 la-check-circle text-success"></i>';
            }else{
                return '<i class="las f-18 la-times-circle text-danger"></i>';
            }
        })
        ->addColumn('action', function ($data) {
            if($data->worker_doctor_acceptance == 1){
                $migrate = '<a class="btn btn-success btn-sm" href="'.url('hr/recruitment/worker/migrate/'.$data->worker_id).'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Migrate to Employee"><i class="las la-external-link-alt f-18"></i></a>';
            }else{
                $migrate = '';
            }
            return '<div style="width:80px;"><a class="btn btn-primary btn-sm" href="'.url('hr/recruitment/recruit/'.$data->worker_id.'/edit/').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Recruitment Information"><i class="las la-pencil-alt f-18"></i></a> '.$migrate.'</div>';
        })
        ->rawColumns(['DT_RowIndex', 'hr_emp_type_name', 'hr_designation_name', 'hr_unit_short_name','hr_area_name','worker_name','worker_contact','worker_doj','medical_info','ie_info','action'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['getEmpType'] = EmpType::getActiveEmpType();
        $data['getUnit'] = Unit::getActiveUnit();
        $data['getArea'] = Area::getActiveArea();
        return view('hr.recruitment.recruit.create', $data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'worker_name'           => 'required|max:128',
            'worker_doj'            => 'required|date',
            'worker_emp_type_id'    => 'required',
            'worker_designation_id' => 'required',
            'worker_unit_id'        => 'required',
            'worker_area_id'        => 'required',
            'worker_department_id'  => 'required',
            'worker_section_id'     => 'required',
            'worker_subsection_id'  => 'required',
            'worker_gender'         => 'required',
            'worker_dob'            => 'required',
            'worker_contact'        => 'required',
            'worker_nid'            => 'required|unique:hr_worker_recruitment',
            'as_oracle_code'        => 'nullable|unique:hr_worker_recruitment',
            'as_rfid'               => 'nullable|unique:hr_worker_recruitment'
        ]);
        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->except('_token');

        // check existing Employee
        $worker = AdvanceInfo::checkExistID($input['worker_nid']);
        if($worker != null){
            toastr()->error($input['worker_name'].' Employee NID/Birth already exists');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input['worker_ot'] = isset($input['worker_ot'])?1:0;
        $input['worker_doctor_acceptance'] = isset($input['worker_doctor_acceptance'])?1:2;
        $input['worker_pigboard_test'] = isset($input['worker_pigboard_test'])?1:0;
        $input['worker_finger_test'] = isset($input['worker_finger_test'])?1:0;
        $input['worker_color_join'] = isset($input['worker_color_join'])?1:0;
        $input['worker_color_band_join'] = isset($input['worker_color_band_join'])?1:0;
        $input['worker_box_pleat_join'] = isset($input['worker_box_pleat_join'])?1:0;
        $input['worker_color_top_stice'] = isset($input['worker_color_top_stice'])?1:0;
        $input['worker_urmol_join'] = isset($input['worker_urmol_join'])?1:0;
        $input['worker_clip_join'] = isset($input['worker_clip_join'])?1:0;
        try {
            WorkerRecruitment::create($input);
            toastr()->success('Successful Recruitment Completed');
            return redirect('/hr/recruitment/recruit');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            // return $bug;
            toastr()->error($bug);
            return back();
        }
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['getEmpType'] = EmpType::getActiveEmpType();
        $data['getUnit'] = unit_list();
        $data['getArea'] = Area::getActiveArea();

        $worker = WorkerRecruitment::where('worker_id',$id)->firstOrFail();
        $getDepartment = Department::getDepartmentAreaIdWise($worker->worker_area_id);
        $getSection = Section::getSectionDepartmentIdWise($worker->worker_department_id);
        $getSubSection = Subsection::getSubSectionSectionIdWise($worker->worker_section_id);
        $getDesignation = Designation::getDesignationEmpTypeIdWise($worker->worker_emp_type_id);

        return view('hr.recruitment.recruit.edit', compact('data','worker', 'getDepartment', 'getSection', 'getSubSection', 'getDesignation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'worker_name'           => 'required|max:128',
            'worker_doj'            => 'required|date',
            'worker_emp_type_id'    => 'required',
            'worker_designation_id' => 'required',
            'worker_unit_id'        => 'required',
            'worker_area_id'        => 'required',
            'worker_department_id'  => 'required',
            'worker_section_id'     => 'required',
            'worker_subsection_id'  => 'required',
            'worker_gender'         => 'required',
            'worker_dob'            => 'required',
            'worker_contact'        => 'required'
        ]);
        if($validator->fails()){
            toastr()->error('Some field validation fails');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->except('_token');

        $worker = WorkerRecruitment::checkRecruitmentWorkerUpdate($input);
        if($worker != null){
            toastr()->error($input['worker_name'].' info already exists');
            return back();
        }
        // check existing Employee
        $worker = AdvanceInfo::checkExistID($input['worker_nid']);
        if($worker != null){
            toastr()->error($input['worker_name'].' Employee NID/Birth already exists');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input['worker_ot'] = isset($input['worker_ot'])?1:0;
        $input['worker_doctor_acceptance'] = isset($input['worker_doctor_acceptance'])?1:2;
        $input['worker_pigboard_test'] = isset($input['worker_pigboard_test'])?1:0;
        $input['worker_finger_test'] = isset($input['worker_finger_test'])?1:0;
        $input['worker_color_join'] = isset($input['worker_color_join'])?1:0;
        $input['worker_color_band_join'] = isset($input['worker_color_band_join'])?1:0;
        $input['worker_box_pleat_join'] = isset($input['worker_box_pleat_join'])?1:0;
        $input['worker_color_top_stice'] = isset($input['worker_color_top_stice'])?1:0;
        $input['worker_urmol_join'] = isset($input['worker_urmol_join'])?1:0;
        $input['worker_clip_join'] = isset($input['worker_clip_join'])?1:0;
        try {

            WorkerRecruitment::where('worker_id', $request->worker_id)->update($input);
            toastr()->success('Recruitment information updated');
            return redirect('/hr/recruitment/recruit');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            toastr()->error($bug);
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Basic info recurment store.
     *
     * @param  Request
     * @return \Illuminate\Http\Response
     */
    public function basicRecruitStore(Request $request)
    {
        $request->validate([
            'worker_name'           => 'required|max:128',
            'worker_doj'            => 'required|date',
            'worker_emp_type_id'    => 'required',
            'worker_designation_id' => 'required',
            'worker_unit_id'        => 'required',
            'worker_area_id'        => 'required',
            'worker_department_id'  => 'required',
            'worker_section_id'     => 'required',
            'worker_subsection_id'  => 'required',
            'worker_gender'         => 'required',
            'worker_dob'            => 'required',
            'worker_contact'        => 'required',
            'worker_nid'            => 'required|unique:hr_worker_recruitment',
            'as_oracle_code'        => 'nullable|unique:hr_worker_recruitment',
            'as_rfid'               => 'nullable|unique:hr_worker_recruitment'
        ]);
        $data = array();
        $data['type'] = 'error';
        $input = $request->all();

        // check existing Employee
        $worker = AdvanceInfo::checkExistID($input['worker_nid']);
        if($worker != null){
            $data['message'] = $input['worker_name'].' Employee NID/Birth already exists';
            return response()->json($data);
        }

        try {
            $input['worker_ot'] = isset($input['worker_ot'])?1:0;
            $input['worker_created_by'] = Auth::user()->id;
            WorkerRecruitment::create($input);

            $data['type'] = 'success';
            $data['url'] = url()->previous();
            $data['message'] = "Recruitment successfully done.";
            return response()->json($data);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            $data['message'] = $bug;
            return response()->json($data);
        }
        
    }

    /**
     * Basic info recurment update.
     *
     * @param  Request
     * @return \Illuminate\Http\Response
     */
    public function recruitUpdate(Request $request, $type)
    {
        $request->validate([
            'worker_name'           => 'required|max:128',
            'worker_doj'            => 'required|date',
            'worker_emp_type_id'    => 'required',
            'worker_designation_id' => 'required',
            'worker_unit_id'        => 'required',
            'worker_area_id'        => 'required',
            'worker_department_id'  => 'required',
            'worker_section_id'     => 'required',
            'worker_subsection_id'  => 'required',
            'worker_gender'         => 'required',
            'worker_dob'            => 'required',
            'worker_contact'        => 'required',
            'worker_nid'            => 'required',
            'as_oracle_code'        => 'nullable',
            'as_rfid'               => 'nullable'
        ]);
        $data = array();
        $data['type'] = 'error';
        $input = $request->all();
        // return $input;
        $worker = WorkerRecruitment::checkRecruitmentWorkerUpdate($input);
        if($worker != null){
            toastr()->error($input['worker_name'].' Recruitment info already exists');
            return back();
        }
        // check existing Employee
        $worker = AdvanceInfo::checkExistID($input['worker_nid']);
        if($worker != null){
            $data['message'] = $input['worker_name'].' Employee NID/Birth already exists';
            return response()->json($data);
        }

        try {
            if($type == 'first'){
                $input['worker_ot'] = isset($input['worker_ot'])?1:0;
            }elseif($type == 'second'){
                $input['worker_doctor_acceptance'] = isset($input['worker_doctor_acceptance'])?1:2;
            }
            $input['updated_by'] = Auth::user()->id;
            WorkerRecruitment::where('worker_id', $input['worker_id'])->update($input);

            $data['type'] = 'success';
            $data['url'] = url()->previous();
            $data['message'] = "Recruitment successfully updated done.";
            return response()->json($data);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            $data['message'] = $bug;
            return response()->json($data);
        }
        
    }

    /**
     * Basic info recurment store.
     *
     * @param  Request
     * @return \Illuminate\Http\Response
    */
    public function medicalRecruitStore(Request $request)
    {
        $request->validate([
            'worker_name'                => 'required|max:128',
            'worker_doj'                 => 'required|date',
            'worker_emp_type_id'         => 'required',
            'worker_designation_id'      => 'required',
            'worker_unit_id'             => 'required',
            'worker_area_id'             => 'required',
            'worker_department_id'       => 'required',
            'worker_section_id'          => 'required',
            'worker_subsection_id'       => 'required',
            'worker_gender'              => 'required',
            'worker_dob'                 => 'required',
            'worker_contact'             => 'required',
            'worker_nid'                 => 'required|unique:hr_worker_recruitment',
            'as_oracle_code'             => 'nullable|unique:hr_worker_recruitment',
            'as_rfid'                    => 'nullable|unique:hr_worker_recruitment',
            'worker_height'              => 'required',
            'worker_weight'              => 'required',
            'worker_tooth_structure'     => 'required',
            'worker_blood_group'         => 'required',
            'worker_identification_mark' => 'required',
            'worker_doctor_age_confirm'  => 'required',
            'worker_doctor_comments'     => 'required'
        ]);
        $data = array();
        $data['type'] = 'error';
        $input = $request->all();

        // check existing Employee
        $worker = AdvanceInfo::checkExistID($input['worker_nid']);
        if($worker != null){
            $data['message'] = $input['worker_name'].' Employee NID/Birth already exists';
            return response()->json($data);
        }
        try {

            $input['worker_ot'] = isset($input['worker_ot'])?1:0;
            $input['worker_doctor_acceptance'] = isset($input['worker_doctor_acceptance'])?1:2;
            $input['worker_created_by'] = Auth::user()->id;
            // return $input;
            WorkerRecruitment::create($input);
            
            $data['type'] = 'success';
            $data['url'] = url()->previous();
            $data['message'] = "Recruitment successfully done.";
            return response()->json($data);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            $data['message'] = $bug;
            return response()->json($data);
        }
        
    }


    public function migrate(Request $request)
    {
        if (empty($request->worker_id)){
            return back()->with("error", "Unable to start the migration: Invalid user!");
        }

        DB::beginTransaction();
        try {
           
            $data = WorkerRecruitment::where("worker_id", $request->worker_id)
            ->where("worker_doctor_acceptance", "1")
            ->where("worker_is_migrated", "0");
            $location= Location::first(['hr_location_id']); 
            $worker = $data->first();
            if ($data->exists() && ($worker->worker_unit_id != null || $worker->worker_unit_id != ''))
            {
                $shift_exist= DB::table('hr_shift')
                        ->where('hr_shift_unit_id', $worker->worker_unit_id)
                        ->where('hr_shift_default', 1)
                        ->pluck('hr_shift_name')
                        ->first();
                /*
                * @function - IDGenerator
                * @parameter - department and join_date
                * @return   - ID and Temporary_ID
                */
                $IDGenerator = (new IDGenerator)->generator2(array(
                    'department' => $worker->worker_department_id,
                    'date' => $worker->worker_doj
                ));

                if (!empty($IDGenerator['error']))
                {
                    toastr()->error($IDGenerator['error']);
                    return back()->with("error", $IDGenerator['error']);
                }
                else if(strlen($IDGenerator['id']) != 10)
                {
                    toastr()->error("Unable to start the migration: Alphanumeric Associate's ID required with exactly 10 characters ");
                    return back()->with("error", "Unable to start the migration: Alphanumeric Associate's ID required with exactly 10 characters ");
                }
                else if($shift_exist == null)
                {
                    toastr()->error("Unable to start the migration: Default Shift Doesn't Exist ");
                    return back()->with("error", "Unable to start the migration: Default Shift Doesn't Exist ");
                }
                else
                {
                    //Default Shift Code
                    $default_shift= DB::table('hr_shift')
                    ->where('hr_shift_unit_id', $worker->worker_unit_id)
                    ->where('hr_shift_default', 1)
                    ->pluck('hr_shift_name')
                    ->first();
                    /*---INSERT INTO BASIC INFO TABLE---*/
                    $check = Employee::insert(array(
                        'as_emp_type_id'  => $worker->worker_emp_type_id,
                        'as_unit_id'      => $worker->worker_unit_id,
                        'as_shift_id'     => $default_shift,
                        'as_area_id'      => $worker->worker_area_id,
                        'as_department_id' => $worker->worker_department_id,
                        'as_section_id'  => $worker->worker_section_id,
                        'as_subsection_id'  => $worker->worker_subsection_id,
                        'as_designation_id' => $worker->worker_designation_id,
                        'as_doj'         => (!empty($worker->worker_doj)?date('Y-m-d',strtotime($worker->worker_doj)):null),
                        'temp_id'        => $IDGenerator['temp'],
                        'associate_id'   => $IDGenerator['id'],
                        'as_name'        => $worker->worker_name,
                        'as_gender'      => $worker->worker_gender,
                        'as_dob'         => (!empty($worker->worker_dob)?date('Y-m-d',strtotime($worker->worker_dob)):null),
                        'as_contact'     => $worker->worker_contact,
                        'as_ot'          => $worker->worker_ot,
                        'as_oracle_code' => $worker->as_oracle_code,
                        'as_rfid_code'   => $worker->as_rfid,
                        'as_pic'         => null,
                        'created_at'     => date("Y-m-d H:i:s"),
                        'created_by'     => Auth::user()->id,
                        'as_status'      => 1 ,
                        'as_location'    => $location->hr_location_id
                    ));

                    MedicalInfo::insert(array(
                        'med_as_id'       => $IDGenerator['id'],
                        'med_height'      => $worker->worker_height,
                        'med_weight'      => $worker->worker_weight,
                        'med_tooth_str'   => $worker->worker_tooth_structure,
                        'med_blood_group' => $worker->worker_blood_group,
                        'med_ident_mark'  => (!empty($worker->worker_identification_mark)?$worker->worker_identification_mark:"N/A"),
                        'med_doct_comment'   => $worker->worker_doctor_comments,
                        'med_doct_conf_age'  => $worker->worker_doctor_age_confirm,
                        'med_doct_signature' => $worker->worker_doctor_signature
                    ));

                    AdvanceInfo::insert(array(
                        'emp_adv_info_as_id' => $IDGenerator['id'],
                        'emp_adv_info_nid'   => $worker->worker_nid
                    ));


                    WorkerRecruitment::where('worker_id', $request->worker_id)
                        ->delete();

                    Cache::forget('employee_count');
                    DB::commit();
                    toastr()->success('Migration successful!');
                    $this->logFileWrite("Employee migration updated", $request->worker_id);
                    return back()->with("success", "Migration successful!");
                }
            }
            else
            {
                toastr()->error("Unable to start the migration: Please check the user's medical status or user already migrated!");
                return back()->with("error", "Unable to start the migration: Please check the user's medical status or user already migrated!");
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            if(isset($e->errorInfo)){
                $duplicate = $e->errorInfo[1];
                if($duplicate == 1062){
                    $message = $e->errorInfo[2];
                    toastr()->error($message);
                    return back()->with('error', $message);
                }
            }
            toastr()->error($bug);
            // return $bug;
            return back()->with('error', $bug);
        }
    }

}
