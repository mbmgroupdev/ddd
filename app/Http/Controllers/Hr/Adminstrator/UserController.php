<?php

namespace App\Http\Controllers\Hr\Adminstrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hr\Unit;
use App\Models\Hr\Location;
use App\Models\Employee;
use App\Models\Merch\Buyer;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use DB,DataTables,Hash,Validator,Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hr.adminstrator.users');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::get()->pluck('name', 'name');
        $units = Unit::get();
        $locations = Location::where('hr_location_status', 1)->get();
        $buyers = DB::table('mr_buyer')->get();
        return view('hr.adminstrator.add-user', compact('roles', 'units', 'locations','buyers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'     => 'required|string|max:255',
            'associate_id' => 'sometimes|unique:users,associate_id',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'role'    => 'required'
        ]);

        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }else{

            $unit_permissions = implode(",", $request->input("unit_permissions"));
            $location_permission = implode(",", $request->input("location_permission"));
            $buyer_permission = null;
            if($request->input("buyer_permissions")){
                $buyer_permission = implode(",", $request->input("buyer_permissions"));

            }

            $user = new User();
            $user->name = $request->name;
            $user->associate_id = $request->associate_id??'';
            $user->email = $request->email;
            $user->password = Hash::make('123456');
            $user->unit_permissions = $unit_permissions;
            $user->location_permission = $location_permission;
            $user->buyer_permissions = $buyer_permission;
            $user->created_by = auth()->user()->id??'';

            $user->save();

            $roles = $request->input('role') ? [$request->input('role')] : [];
            $user->assignRole($roles);

            // create log file
            log_file_write("User Created", $user->id);
            toastr()->success('User created successfully');
            return redirect('hr/adminstrator/user/edit/'.$user->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $associate_id = $request->associate_id;
        if ($associate_id)
        {
            $info = get_employee_by_id($associate_id);
            // $info = Employee::where('associate_id', $associate_id)->first();
            $per_complete = get_complete_user_info($associate_id);

            if(empty($info)) abort(404, "$associate_id not found!");

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
            ->where("hr_la_as_id", $associate_id)
            ->get();

            $month  = date('m');
            $year   = date('Y');
            $day    = date('d');
            $day    = (int)$day;

            $shiftRoaster = ShiftRoaster::where([
                'shift_roaster_associate_id' => $associate_id,
                'shift_roaster_year' => (int)$year,
                'shift_roaster_month' => (int)$month
            ])->first();

            $roasterShift = null;
            if($shiftRoaster) {
                $roasterShift = 'day_'.$day;
                $roasterShift = $shiftRoaster->$roasterShift;
            }

            //get todays status

            $tableName = get_att_table($info->hr_unit_id).' AS a';
            $daystart= date('Y-m-d')." 00:00:00";
            $dayend= date('Y-m-d')." 23:59:59";
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
                ->where("leave_ass_id", $associate_id)
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
            ->where("b.associate_id", "=", $associate_id)
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
                ->where('r.dis_re_offender_id', $associate_id)
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
                ->where('p.associate_id', $associate_id)
                ->orderBy('p.effective_date', "DESC")
                ->get();

            $increments = Increment::where('associate_id', $associate_id)
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
                ->where("e.education_as_id", $associate_id)
                ->get();


            //check current station
            $station= DB::table('hr_station AS s')
                        ->where('s.associate_id', $associate_id)
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

            $getSalaryList      = HrMonthlySalary::where('as_id', $associate_id)
                                ->where('year',2019)
                                ->get();
            $getEmployee        = Employee::getEmployeeAssociateIdWise($associate_id);
            $title              = 'Unit : '.($getEmployee->unit != null?$getEmployee->unit['hr_unit_name_bn']:'').' - Location : '.($getEmployee->location != null?$getEmployee->location['hr_unit_name_bn']:'');
            $pageHead['current_date']   = date('d-m-Y');
            $pageHead['current_time']   = date('H:i');
            $pageHead['pay_date']       = '';
            $pageHead['unit_name']      = $getEmployee->unit['hr_unit_name_bn'];
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with('employee')->findOrFail($id);
        $roles = Role::get()->pluck('name', 'name');
        $units = Unit::get();
        $buyers = DB::table('mr_buyer')->get();
        $locations = Location::where('hr_location_status', 1)->get();
        $role = $user->roles()->first()->name??'';

        return view('hr.adminstrator.edit-user', compact('user','roles', 'units','role','locations','buyers'));
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
        try{

            $user = User::findOrFail($id);
            if(isset($request->name)){
                $user->name = $request->name;
            }
            if(isset($request->unit_permissions)){
                $user->unit_permissions = $request->unit_permissions?(implode(",", $request->unit_permissions)):null;
            }
            if(isset($request->location_permission)){
                $user->location_permission = $request->location_permission?(implode(",", $request->location_permission)):null;
            }
            if(isset($request->buyer_permissions)){
                $user->buyer_permissions = $request->buyer_permissions?(implode(",", $request->buyer_permissions)):null;
            }

            // change password
            if(isset($request->password)){
                $user->password = Hash::make($request->password);
                log_file_write("User password changed", $user->id);
            }
            $user->save();
            if(isset($request->role)){
                $roles = $request->input('role') ? [$request->input('role')] : [];
                $user->syncRoles($roles);
            }

            // create log file
            log_file_write("User information updated", $user->id);
            toastr()->success('User information updated succesfully.');
            return redirect()->back();
        }catch( \Exception $e){
            toastr()->error($e->getMessage());
            return redirect()->back();
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
        $user = User::findOrFail($id);

        if ($user->delete()){

            log_file_write("User Deleted", $id );
            return redirect()->back()
                ->with("success", "Delete Successful!");
        }else{
            return redirect()->back()
                    ->with("error", "Please try again.");

        }
    }


    public function getUserList()
    {
        $data = User::where(function($q) {
                    if (auth()->user()->associate_id != 9999999999)
                    {
                        $q->whereNotIn("users.associate_id", [9999999999]);
                    }
                })
                ->get();

        return DataTables::of($data)
            /*->addColumn('units', function ($data) {
                $result = "";
                $units = explode(",", $data->unit_permissions);
                foreach ($units as $unit):
                    $name = DB::table("hr_unit")->where("hr_unit_id", $unit)->value("hr_unit_name");
                    if (!empty($name))
                    $result .= "<span class=\"label label-primary\">$name</span> ";
                endforeach;
                return $result;
            })*/
            ->addColumn('roles', function ($data) {
                $roles = "";
                foreach ($data->roles()->pluck('name') as $role):
                    $roles .= "<span class=\"label label-info\">$role</span> ";
                endforeach;
                return $roles;
            })


            /*->addColumn('buyer', function ($data) {
                $i=1;
                $result = "";

                $buyerList = explode(",", $data->buyer_permissions);
                foreach ($buyerList as $buyer):
                    $name = DB::table("mr_buyer")->where("b_id", $buyer)->value("b_name");
                    if (!empty($name)){
                    $result .=$i.".".$name."<br/>";
                    $i++;
                    }
                endforeach;
                return $result;
            })*/
            /*->addColumn('management', function ($data) {
                $i=1;
                $result = "";

                $managementList = explode(",", $data->management_restriction);
                foreach ($managementList as $management):
                    $name = DB::table("hr_as_basic_info")->where("as_id", $management)->value("as_name");
                    if (!empty($name)){
                    $result .=$i.".".$name."<br/>";
                    $i++;
                    }
                endforeach;
                return $result;
            })*/
            ->addColumn('action', function ($data) {
                
                return "<a href=".url('hr/adminstrator/user/edit/'.$data->id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit\">
                    <i class=\"fa fa-pencil\"></i>
                </a>
                <a href=".url('hr/adminstrator/user/delete/'.$data->id)." onclick=\"return confirm('Are you sure?');\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Trash\" style=\"padding-right: 6px;\">
                    <i class=\"fa fa-trash\"></i>
                </a>";

            })
            ->rawColumns(['serial_no', 'roles','action'])
            ->make(true);
    }

    public function permissionAssign(Request $request)
    {
        $permissions = Permission::orderBy('groups','ASC')->get();
        $permissions = $permissions->groupBy(['module','groups']);

        return view('hr.adminstrator.assign-permission', compact('permissions'));
    }


    public function password(Request $request)
    {
        return view('user.change-password');
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails())
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else
        {
            $user = User::find(auth()->id());
            $user->password = Hash::make($request->password);
            $user->save();

            Auth::guard()->login($user);

            return redirect()->back()
                ->with('success', 'Your password have been changed')
                ->withInput();
        }
    }

    public function userPassword($id, Request $request)
    {
        $validator = Validator::make($request->all(),[
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails())
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else
        {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
            $user->save();

            //Auth::guard()->login($user);
            log_file_write($user->associate_id."'s password changed by ".auth()->user()->associate_id, $user->id );

            return redirect()->back()
                ->with('success', 'users password have been changed')
                ->withInput();
        }
    }


    public function getPermission(Request $request)
    {
        $user = User::where('associate_id', $request->id)->first();
        //$test = $user->hasPermissionTo('Add User');
        //dd($test);
        $permissions = Permission::orderBy('groups','ASC')->get();
        $permissions = $permissions->groupBy(['module','groups']);

        return view('hr.adminstrator.get-permission', compact('user','permissions'))->render();
    }

    public function syncPermission(Request $request)
    {
        $user = User::where('associate_id', $request->id)->first();

        if($request->type == 'revoke'){
            $user->revokePermissionTo($request->permission);
            log_file_write("Permission ".$request->permission." revoked from ".$request->id, '');

            return '"'.$request->permission.'" revoked from';

        }else if($request->type == 'assign'){
            $user->givePermissionTo($request->permission); 
            log_file_write("Permission ".$request->permission." assigned to ".$request->id, '');

            return '"'.$request->permission.'" assigned to';            
        }

    }


    public function employeeSearch(Request $request)
    {
        $data = []; 
        if($request->has('keyword')){
            $search = $request->keyword;
            $data = Employee::select("associate_id", DB::raw('CONCAT_WS(" - ", associate_id, as_name) AS user_name'))
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions())
                ->whereIn('as_status', [1,6])
                ->where(function($q) use($search) {
                    $q->where("associate_id", "LIKE" , "%{$search}%");
                    $q->orWhere("as_name", "LIKE" , "%{$search}%");
                    $q->orWhere("as_oracle_code", "LIKE" , "%{$search}%");
                })
                ->take(10)
                ->get();
        }

        return response()->json($data);
    }

    public function allEmployeeSearch(Request $request)
    {
        $data = []; 
        if($request->has('keyword')){
            $search = $request->keyword;
            $data = Employee::select("associate_id", DB::raw('CONCAT_WS(" - ", associate_id, as_name) AS user_name'))
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions())
                // ->whereIn('as_status', [1,6])
                ->where(function($q) use($search) {
                    $q->where("associate_id", "LIKE" , "%{$search}%");
                    $q->orWhere("as_name", "LIKE" , "%{$search}%");
                    $q->orWhere("as_oracle_code", "LIKE" , "%{$search}%");
                })
                ->take(10)
                ->get();
        }

        return response()->json($data);
    }

    public function femaleSearch(Request $request)
    {
        $data = []; 
        if($request->has('keyword')){
            $search = $request->keyword;
            $data = Employee::select("associate_id", DB::raw('CONCAT_WS(" - ", associate_id, as_name) AS user_name'))
                ->where('as_gender', 'Female')
                ->whereIn('as_status', [1,6])
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions())
                ->where(function($q) use($search) {
                    $q->where("associate_id", "LIKE" , "%{$search}%");
                    $q->orWhere("as_name", "LIKE" , "%{$search}%");
                    $q->orWhere("as_oracle_code", "LIKE" , "%{$search}%");
                })
                ->take(10)
                ->get();
        }

        return response()->json($data);
    }


    public function userSearch(Request $request)
    {
        $data = []; 
        if($request->has('keyword')){
            $search = $request->keyword;
            $data = User::select("associate_id", DB::raw('CONCAT_WS(" - ", associate_id, name) AS user_name'))
                ->where(function($q) use($search) {
                    $q->where("associate_id", "LIKE" , "%{$search}%");
                    $q->orWhere("name", "LIKE" , "%{$search}%");
                })
                ->take(10)
                ->get();
        }

        return response()->json($data);
    }
}
