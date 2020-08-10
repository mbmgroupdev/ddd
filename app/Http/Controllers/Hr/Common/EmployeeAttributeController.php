<?php

namespace App\Http\Controllers\Hr\Common;

use App\Http\Controllers\Controller;
use App\Models\Hr\Department;
use App\Models\Hr\Designation;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use Illuminate\Http\Request;

class EmployeeAttributeController extends Controller
{
    public function getDesignation($id)
    {
    	$data['status'] = 'error';
    	try {
    		$designation = Designation::getDesignationEmpTypeIdWise($id);
    		$data['status'] = 'success';
    		$data['value'] = $designation;
    		return response()->json($data);
    	} catch (\Exception $e) {
    		$data['message'] = $e->getMessage();
    		return $data;
    	}
    	
    }

    public function getDepartment($id)
    {
        $data['status'] = 'error';
        try {
            $department = Department::getDepartmentAreaIdWise($id);
            $data['status'] = 'success';
            $data['value'] = $department;
            return response()->json($data);
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
        
    }

    public function getSection($id)
    {
        $data['status'] = 'error';
        try {
            $section = Section::getSectionDepartmentIdWise($id);
            $data['status'] = 'success';
            $data['value'] = $section;
            return response()->json($data);
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
        
    }

    public function getSubSection($id)
    {
        $data['status'] = 'error';
        try {
            $subSection = Subsection::getSubSectionSectionIdWise($id);
            $data['status'] = 'success';
            $data['value'] = $subSection;
            return response()->json($data);
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
        
    }
}
