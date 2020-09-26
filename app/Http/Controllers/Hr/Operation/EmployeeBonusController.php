<?php

namespace App\Http\Controllers\Hr\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Unit;
use App\Models\Hr\Floor;
use App\Models\Hr\Line;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use DB,Response, Exception, Validator;

class EmployeeBonusController extends Controller
{
    public function index(){

    	$employees = Employee::getSelectIdNameEmployee();
    	// dd($employees);
    	$units = Unit::unitListAsObject();
    	// dd($units);
		$areas = Area::getAreatListAsObject();

    	return view('hr.employeebonus.employee_bonus', compact('employees', 'units', 'areas'));
    }

    public function getFloorData(Request $req){
    	$floors = Floor::getSelectedFloorIdName($req->unit_id ); 
    	// dd($floors);
    	return Response::json($floors);
    }

    public function getLineData(Request $req){
    	$lines = Line::getSelectedLineIdName($req->floor_id );
    	return Response::json($lines); 
    }

    public function getDepartmentData(Request $req){
    	$deparments = Department::getSelctedDepartmentIdName($req->area_id);
    	return Response::json($deparments);
    }

    public function getSectionData(Request $req){
    	$sections = Section::getSelectedSectionIdName($req->deparment_id);
    	return Response::json($sections);
    }

    public function getSubSectionData(Request $req){
    	$subsections = Subsection::getSelectedSubSectionIdName($req->section_id);
    	return Response::json($subsections);	
    }

    //Saving Data
    public function entrySave(Request $request){
    	// dd($request->all());

    	$validator = Validator::make($request->all(), [
            'bonus_type_id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return back()
            ->withErrors($validator)
            ->withInput();
        }
        try{
        	if($request->choice == 'employee'){
        		dd("Employee Section", $request->all() );
        	}
        	else{
        		if($request->all_unit_or_not == 0){
        			dd("Single Unit:", $request->all() );

        		}
        		else{
					dd("All Units:", $request->all() );        			
        		}	
        	}


        }catch(\Exception $e){
        	$msg = $e->getMessage();
        	return back()->with('error', $msg);
        }
    }
}
