<?php

namespace App\Http\Controllers\Hr\Adminstrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hr\Unit;
use App\Models\Employee;
use App\Models\Merch\Buyer;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use DB,DataTables,Hash,Validator;

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
        /*$buyers= Buyer::get();
        $templates = DB::table('hr_buyer_template')->get();*/
        return view('hr.adminstrator.add-user', compact('roles', 'units'));
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
            'associate_id' => 'sometimes|string|unique:users,associate_id',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'role'    => 'required'
        ]);

        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }else{

            $unit_permissions = implode(",", $request->input("unit_permissions"));

            $user = new User();
            $user->name = $request->name;
            $user->associate_id = $request->associate_id??'';
            $user->email = $request->email;
            $user->password = Hash::make('123456');
            $user->unit_permissions = $unit_permissions;

            $user->save();

            $roles = $request->input('role') ? [$request->input('role')] : [];
            $user->assignRole($roles);

            // create log file
            log_file_write("User Created", $user->id);

            return redirect('hr/adminstrator/user/edit/'.$user->id)->with('success', 'Save Successful.');
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
        $user = User::findOrFail($id);
        $roles = Role::get()->pluck('name', 'name');
        $units = Unit::get();
        $role = $user->roles()->first()->name??'';

        return view('hr.adminstrator.edit-user', compact('user','roles', 'units','role'));
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
        $validator = Validator::make($request->all(),[
            'name'     => 'required|string|max:255',
            'associate_id' => 'sometimes|unique:users,associate_id,{$id}',
            'role'    => 'required'
        ]);

        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }else{

            $user = User::findOrFail($id);
            $user->name = $request->name;
            if($request->associate_id){
                $user->associate_id = $request->associate_id;
            }
            $user->unit_permissions = implode(",", $request->input("unit_permissions"));

            $user->save();

            $roles = $request->input('role') ? [$request->input('role')] : [];
            $user->syncRoles($roles);

            // create log file
            log_file_write("User information updated", $user->id);

            return redirect()->back()->with('success', 'User information updated succesfully.');
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
        $permissions = Permission::orderBy('name','ASC')->get();
        $permissions = $permissions->groupBy(['module','groups']);

        return view('hr.adminstrator.assign-permission', compact('permissions'));
    }


    public function getPermission(Request $request)
    {
        $user = User::where('associate_id', $request->id)->first();
        //$test = $user->hasPermissionTo('Add User');
        //dd($test);
        $permissions = Permission::orderBy('name','ASC')->get();
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
