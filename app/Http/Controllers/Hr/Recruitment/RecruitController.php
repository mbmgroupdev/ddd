<?php

namespace App\Http\Controllers\Hr\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\EmpType;
use App\Models\Hr\Unit;
use App\Models\Hr\WorkerRecruitment;
use Auth, Validator, DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        DB::statement(DB::raw('set @rownum=0'));
        $data = WorkerRecruitment::with(['employee_type:emp_type_id,hr_emp_type_name', 'designation:hr_designation_id,hr_designation_name','unit:hr_unit_id,hr_unit_short_name', 'area:hr_area_id,hr_area_name'])
        ->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('hr_emp_type_name', function ($data) {
            return $data->employee_type['hr_emp_type_name'];
        })
        ->addColumn('hr_designation_name', function ($data) {
            return $data->designation['hr_designation_name'];
        })
        ->addColumn('hr_unit_short_name', function ($data) {
            return $data->unit['hr_unit_short_name'];
        })
        ->addColumn('hr_area_name', function ($data) {
            return $data->area['hr_area_name'];
        })
        ->addColumn('worker_doj', function ($data) {
            return date('Y-m-d', strtotime($data->worker_doj));
        })
        ->addColumn('medical_info', function ($data) {
            if($data->worker_doctor_acceptance == 1){
                return '<div data-icon="S" class="icon"></div>';
            }else{
                return '<div class="icon dripicons-cross"></div>';
            }
        })
        ->addColumn('ie_info', function ($data) {
            if($data->worker_is_migrated == 1){
                return '<div data-icon="S" class="icon"></div>';
            }else{
                return '<div class="icon dripicons-cross"></div>';
            }
        })
        ->addColumn('action', function ($data) {
            
            /*return "<a class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"Edit\">
                <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
            </a>
            <a onclick=\"return confirm('Are you sure?');\" class=\"btn btn-sm btn-danger\" data-toggle=\"tooltip\" title=\"Delete\" style=\"padding-right: 6px;\">
                <i class=\"ace-icon fa fa-trash bigger-120\"></i>
            </a>";*/
            return '<button class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Migrate To Employee"><i class="ri-heart-fill pr-0"></i></button>';

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
            'as_oracle_code'        => 'nullable|unique:hr_worker_recruitment',
            'as_rfid'               => 'nullable|unique:hr_worker_recruitment'
        ]);
        if($validator->fails()){
            toastr()->error('Some field validation fails');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->except('_token');

        $worker = WorkerRecruitment::checkRecruitmentWorker($input);
        if($worker != null){
            toastr()->error($input['worker_name'].' info already exists');
            return back();
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
            return $bug;
            toastr()->error($bug);
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
            'as_oracle_code'        => 'nullable|unique:hr_worker_recruitment',
            'as_rfid'               => 'nullable|unique:hr_worker_recruitment'
        ]);
        $data = array();
        $data['type'] = 'error';
        $input = $request->all();
        
        // check existing worker
        $worker = WorkerRecruitment::checkRecruitmentWorker($input);
        if($worker != null){
            $data['message'] = $input['worker_name'].' info already exists';
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

        // check existing worker
        $worker = WorkerRecruitment::checkRecruitmentWorker($input);
        if($worker != null){
            $data['message'] = $input['worker_name'].' info already exists';
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


}
