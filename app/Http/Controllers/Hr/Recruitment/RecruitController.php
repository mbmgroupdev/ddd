<?php

namespace App\Http\Controllers\Hr\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\EmpType;
use App\Models\Hr\Unit;
use App\Models\Hr\WorkerRecruitment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth, Validator;

class RecruitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $input = $request->all($request->except('_token'));

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

            WorkerRecruitment::insert([
                'worker_name'           => $input['worker_name'],
                'worker_doj'            => $input['worker_doj'],
                'worker_emp_type_id'    => $input['worker_emp_type_id'],
                'worker_designation_id' => $input['worker_designation_id'],
                'worker_unit_id'        => $input['worker_unit_id'],
                'worker_area_id'        => $input['worker_area_id'],
                'worker_department_id'  => $input['worker_department_id'],
                'worker_section_id'     => $input['worker_section_id'],
                'worker_subsection_id'  => $input['worker_subsection_id'],
                'worker_dob'            => $input['worker_dob'],
                'worker_ot'             => isset($input['worker_ot'])?1:0,
                'worker_gender'         => $input['worker_gender'],
                'worker_contact'        => $input['worker_contact'],
                'worker_nid'            => $input['worker_nid'],
                'as_rfid'               => $input['as_rfid'],
                'as_oracle_code'        => $input['as_oracle_code'],
                'worker_created_at'     => Carbon::now(),
                'worker_created_by'     => Auth::user()->id
            ]);

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
        return $input;
        // check existing worker
        $worker = WorkerRecruitment::checkRecruitmentWorker($input);
        if($worker != null){
            $data['message'] = $input['worker_name'].' info already exists';
            return response()->json($data);
        }
        try {
            WorkerRecruitment::insert([
                'worker_name'                => $input['worker_name'],
                'worker_doj'                 => $input['worker_doj'],
                'worker_emp_type_id'         => $input['worker_emp_type_id'],
                'worker_designation_id'      => $input['worker_designation_id'],
                'worker_unit_id'             => $input['worker_unit_id'],
                'worker_area_id'             => $input['worker_area_id'],
                'worker_department_id'       => $input['worker_department_id'],
                'worker_section_id'          => $input['worker_section_id'],
                'worker_subsection_id'       => $input['worker_subsection_id'],
                'worker_dob'                 => $input['worker_dob'],
                'worker_ot'                  => isset($input['worker_ot'])?1:0,
                'worker_gender'              => $input['worker_gender'],
                'worker_contact'             => $input['worker_contact'],
                'worker_nid'                 => $input['worker_nid'],
                'as_rfid'                    => $input['as_rfid'],
                'as_oracle_code'             => $input['as_oracle_code'],
                'worker_height'              => $input['worker_height'],
                'worker_weight'              => $input['worker_weight'],
                'worker_tooth_structure'     => $input['worker_tooth_structure'],
                'worker_blood_group'         => $input['worker_blood_group'],
                'worker_identification_mark' => $input['worker_identification_mark'],
                'worker_doctor_age_confirm'  => $input['worker_doctor_age_confirm'],
                'worker_doctor_comments'     => $input['worker_doctor_comments'],
                'worker_doctor_acceptance'   => isset($input['worker_doctor_acceptance'])?1:2,
                'worker_created_at'          => Carbon::now(),
                'worker_created_by'          => Auth::user()->id
            ]);
            
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
