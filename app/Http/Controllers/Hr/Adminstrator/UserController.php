<?php

namespace App\Http\Controllers\Hr\Adminstrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use DB,DataTables;

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
        $units_count= count($units);
        $buyers= Buyer::get();
        $templates = DB::table('hr_buyer_template')->get();
        return view('hr.adminstrator.add-user', compact('roles', 'units','buyers','templates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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


    public function getUserList()
    {

        $unit_perm = auth()->user()->unit_permissions();

        $data = User::whereIn('unit_permissions',$unit_perm)
                ->where(function($q) {
                    if (auth()->user()->associate_id != 9999999999)
                    {
                        $q->whereNotIn("users.associate_id", [9999999999]);
                    }
                })
                ->get();

        return DataTables::of($data)
            ->addColumn('units', function ($data) {
                $result = "";
                $units = explode(",", $data->unit_permissions);
                foreach ($units as $unit):
                    $name = DB::table("hr_unit")->where("hr_unit_id", $unit)->value("hr_unit_name");
                    if (!empty($name))
                    $result .= "<span class=\"label label-primary\">$name</span> ";
                endforeach;
                return $result;
            })
            ->addColumn('roles', function ($data) {
                $roles = "";
                foreach ($data->roles()->pluck('name') as $role):
                    $roles .= "<span class=\"label label-info\">$role</span> ";
                endforeach;
                return $roles;
            })


            ->addColumn('buyer', function ($data) {
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
            })
            ->addColumn('management', function ($data) {
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
            })
            ->addColumn('action', function ($data) {
                if ($data->associate_id == 9999999999)
                {
                    return "<a href=".url('users_management/user/edit/'.$data->id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>";
                }
                else
                {
                    return "<a href=".url('users_management/user/edit/'.$data->id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>
                    <a href=".url('users_management/user/delete/'.$data->id)." onclick=\"return confirm('Are you sure?');\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete\" style=\"padding-right: 6px;\">
                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                    </a>";
                }

            })
            ->rawColumns(['serial_no', 'units', 'buyer', 'roles','management','action'])
            ->make(true);
    }
}
