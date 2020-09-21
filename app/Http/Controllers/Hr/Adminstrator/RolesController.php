<?php

namespace App\Http\Controllers\Hr\Adminstrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth, Validator, DB; 

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('hierarchy','ASC')->get();

        return view('hr.adminstrator.roles', compact('roles'));;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::orderBy('groups','ASC')->get();
        $permissions = $permissions->groupBy(['module','groups']);
        return view('hr.adminstrator.add-roles', compact('permissions'));
    }

   /**
    * Store a newly created Role in storage.
    *
    * @param  \App\Http\Requests\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {     
            
        $validator = Validator::make($request->all(),[ 
            'name' => 'required|string|max:255|unique:roles,name,'. $request->id,
        ]);  

        if ($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput();
        }
        dd($request->all());

        $role = new Role();
        $role->name = $request->name;
        $role->hierarchy = $request->hierarchy;
        $role->save(); 

        if($request->permissions != null){
            $role->givePermissionTo($request->permissions);
            log_file_write($role->name ." - role created: ", $role->id);

        }
        return redirect('hr/adminstrator/edit/role/'.$role->id)
                ->with("success", "Save Successful!");
             
    }
    

    public function edit($id, Request $request)
    {  
        $role = Role::findOrFail($id); 

        $permissions = Permission::get();
        $permissions = $permissions->groupBy(['module','groups']);


        return view('hr.adminstrator.edit-roles', compact('role','permissions'));


    }

    public function syncPermission(Request $request)
    {
        $role = Role::find($request->id);

        if($request->type == 'revoke'){
            $role->revokePermissionTo($request->permission);
            log_file_write("Permission ".$request->permission." revoked from ".$request->id, '');

            return '"'.$request->permission.'" revoked from';

        }else if($request->type == 'assign'){
            $role->givePermissionTo($request->permission); 
            log_file_write("Permission ".$request->permission." assigned to ".$request->id, '');

            return '"'.$request->permission.'" assigned to';            
        }

    }

    public function update(Request $request)
    {     
            
        $validator = Validator::make($request->all(),[ 
            'name' => 'required|string|max:255',
        ]);  

        if ($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput();
        }
       
            
        $role = Role::findOrFail($request->id);
        $update = $role->update([
            'name' => $request->name,
            'hierarchy' => $request->hierarchy
        ]);
        
        $permissions = $request->permissions ? $request->permissions : [];
        $role->syncPermissions($permissions);

        log_file_write("Roles Updated", $request->id);

        return redirect()->back()->with("success", "Update Successful!");
             
    }


    /**
     * Remove Role from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {     
        

        $role = Role::findOrFail($request->id);
        if ($role->delete())
        {
            log_file_write($role->name."- role deleted", $request->id);
            
            return back()
                ->with("success", "Delete Successful!");
        }
        else
        {
            return back()
                    ->with("error", "Please try again.");

        }
    }
}
