<?php

namespace App\Http\Controllers\Hr\Search;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Hr\Department;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Leave;
use App\Models\Hr\Outsides;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Validator, Auth, ACL, DB, DataTables;

class EmployeeStatusSearchController extends Controller
{
    public function hrEmpstatusSearch(Request $request)
    {
        try{
            return $this->searchGetEmpstatusReport_global($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function pageTitle($request)
    {
        // $showTitle = ucwords($request['category']).' - '.ucwords($request['type']) ;
        $showTitle = 'Employee Status - '.ucwords($request['type']) ;
        if(isset($request['month'])){
            $showTitle =$showTitle.': '.$request['month'];
        }
        if(isset($request['year'])){
            $showTitle =$showTitle.'-'.$request['year'];
        }
        if($request['type']=='date'){
            $showTitle =$showTitle.': '.$request['date'];
        }
        return $showTitle;
    }

    public function getSearchType($request)
    {
        if($request['type'] == 'date') {
            $date = $request['date'];
            $date = Carbon::create($date)->format('Y-m-d%');
        }
        else if($request['type'] == 'yesterday') {
            $date = Carbon::create(date('d')-1)->format('Y-m-d%');
        } else {
            $date = Carbon::create(date('d'))->format('Y-m-d%');
        }
        return $date;
    }

    public function getEmpstatusFromDate($date,$type,$statusType=null,$all=null,$allEmp=null,$unit=null)
    {
        $empstatus_type_list = '';
        if($type == 'date') {
            $empstatus_type_list = Employee::where(function ($q) use($date) {
                $q->where('as_status_date', '=', $date);
            })
            ->when($unit!=null, function($q) use ($unit) {
                $q->where('as_unit_id',$unit);
            })
            ->when($statusType!=null, function($q) use($statusType){
                if($statusType == 'resign') {
                    $q->where('as_status', 2);
                } else if($statusType == 'terminate') {
                    $q->where('as_status', 3);
                } else if($statusType == 'suspend') {
                    $q->where('as_status', 4);
                } else if($statusType == 'left') {
                    $q->where('as_status', 5);
                } else if($statusType == 'maternity') {
                    $q->where('as_status', 6);
                }
            })
            ->when($all!=null, function($q) use($all){
                $q->whereNotIn('as_status',[0,1]);
            })
            ->get();
            if($allEmp == null && $all != null) {
                $empstatus_type_list = $empstatus_type_list->groupBy('as_status');
            }
            $empstatus_type_list = $empstatus_type_list->toArray();
        } else if($type == 'month') {
            list($month,$year) = explode('-',$date['month']);
            $month = date('m', strtotime($month));
            $empstatus_type_list = Employee::where(function ($q) use($month,$year) {
                $q->whereMonth('as_status_date',$month);
                $q->whereYear('as_status_date',$year);
            })
            ->when($unit!=null, function($q) use ($unit) {
                $q->where('as_unit_id',$unit);
            })
            ->when($statusType!=null, function($q) use($statusType){
                if($statusType == 'resign') {
                    $q->where('as_status', 2);
                } else if($statusType == 'terminate') {
                    $q->where('as_status', 3);
                } else if($statusType == 'suspend') {
                    $q->where('as_status', 4);
                } else if($statusType == 'left') {
                    $q->where('as_status', 5);
                } else if($statusType == 'maternity') {
                    $q->where('as_status', 6);
                }
            })
            ->when($all!=null, function($q) use($all){
                $q->whereNotIn('as_status',[0,1]);
            })
            ->get();
            if($allEmp == null && $all != null) {
                $empstatus_type_list = $empstatus_type_list->groupBy('as_status');
            }
            $empstatus_type_list = $empstatus_type_list->toArray();
        } else if($type == 'range') {          
        } else if($type == 'year') {
            $year = $date['year'];
            $empstatus_type_list = Employee::where(function ($q) use($year) {
                $q->whereYear('as_status_date',$year);
            })
            ->when($unit!=null, function($q) use ($unit) {
                $q->where('as_unit_id',$unit);
            })
            ->when($statusType!=null, function($q) use($statusType){
                if($statusType == 'resign') {
                    $q->where('as_status', 2);
                } else if($statusType == 'terminate') {
                    $q->where('as_status', 3);
                } else if($statusType == 'suspend') {
                    $q->where('as_status', 4);
                } else if($statusType == 'left') {
                    $q->where('as_status', 5);
                } else if($statusType == 'maternity') {
                    $q->where('as_status', 6);
                }
            })
            ->when($all!=null, function($q) use($all){
                $q->whereNotIn('as_status',[0,1]);
            })
            ->get();
            if($allEmp == null && $all != null) {
                $empstatus_type_list = $empstatus_type_list->groupBy('as_status');
            }
            $empstatus_type_list = $empstatus_type_list->toArray();
        }
        //dd($empstatus_type_list);
        return $empstatus_type_list;
    }

    public function getActiveEmpFromDate($date,$type,$unit=null)
    {
        $empstatus_type_list = [];
        if($type == 'date') {
            $empstatus_type_list = Employee::where(function ($q) use($date) {
                $q->where('as_doj', '=', $date);
            })
            ->when($unit!=null, function($q) use ($unit) {
                $q->where('as_unit_id',$unit);
            })
            ->get();
            $empstatus_type_list = $empstatus_type_list->toArray();
        } else if($type == 'month') {
            list($month,$year) = explode('-',$date['month']);
            $month = date('m', strtotime($month));
            $empstatus_type_list = Employee::where(function ($q) use($month,$year) {
                $q->whereMonth('as_doj',$month);
                $q->whereYear('as_doj',$year);
            })
            ->when($unit!=null, function($q) use ($unit) {
                $q->where('as_unit_id',$unit);
            })
            ->get();
            $empstatus_type_list = $empstatus_type_list->toArray();
        } else if($type == 'range') {
            
        } else if($type == 'year') {
            $year = $date['year'];
            $empstatus_type_list = Employee::where(function ($q) use($year) {
                $q->whereYear('as_doj',$year);
            })
            ->when($unit!=null, function($q) use ($unit) {
                $q->where('as_unit_id',$unit);
            })
            ->get();
            $empstatus_type_list = $empstatus_type_list->toArray();
        }
        return $empstatus_type_list;
    }

    public function searchGetEmpstatusReport_global($request)
    {
        try {
            $showTitle = $this->pageTitle($request);
            // get previous url params
            $parts = parse_url(url()->previous());
            $request2 = [];
            if(isset($parts['query'])){
                parse_str($parts['query'], $request1);
                // $request1['date'] = null;
                if($request1['category'] != $request['category']) {
                    $request['category'] = $request['category'];
                }
                if($request1['type'] != $request['type']) {
                    $request['type'] = $request['type'];
                }
                if(isset($request['date'])){
                    $request2['date'] = $request['date'];
                }
                if(isset($request['month'])){
                    $request2['month'] = $request['month'];
                }
                if(isset($request['year'])){
                    $request2['year'] = $request['year'];
                }
                $request = ['category'=> $request['category'], 'type' => $request['type']];
                $request = array_merge($request2, $request);
            } else {
                if(isset($request['date'])){
                    $request2['date'] = $request['date'];
                }
                if(isset($request['month'])){
                    $request2['month'] = $request['month'];
                }
                if(isset($request['year'])){
                    $request2['year'] = $request['year'];
                }
                $request = ['category'=> $request['category'], 'type' => $request['type']];
                $request = array_merge($request2, $request);
            }
            $empList = $this->getEmpstatusFromDate($request2,$request['type'],null,'all',null);
            $empActiveList = $this->getActiveEmpFromDate($request2,$request['type']);
            // return $empList;
            $result['page'] = view('hr.search.empstatus.allempstatus',compact('showTitle', 'request', 'empList', 'empActiveList'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrEmpstatusSearchEmployee(Request $request)
    {
        try {
            // get previous url params
            $status = $request->status;
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $showTitle = $this->pageTitle($request1);
            $result['page'] = view('hr.search.empstatus.allemployee',compact('showTitle','request1','status'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }


    public function hrEmpstatusSearchEmpData(Request $request)
    {
        $dataList = [];
        if($request->data['type'] == 'year') {
            if($request->status == 'join') {
                $dataList = $this->getActiveEmpFromDate($request->data,$request->data['type']);
            } else {
                $dataList = $this->getEmpstatusFromDate($request->data,$request->data['type'],Custom::getEmpStatusName($request->status));
            }
        }
        if($request->data['type'] == 'month') {
            if($request->status == 'join') {
                $dataList = $this->getActiveEmpFromDate($request->data,$request->data['type']);
            } else {
                $dataList = $this->getEmpstatusFromDate($request->data,$request->data['type'],Custom::getEmpStatusName($request->status));
            }
        }
        if($request->data['type'] == 'date') {
            if($request->status == 'join') {
                $dataList = $this->getActiveEmpFromDate($request->data['date'],$request->data['type']);
            } else {
                $dataList = $this->getEmpstatusFromDate($request->data['date'],$request->data['type'],Custom::getEmpStatusName($request->status));
            }
        }
        // return $dataList;
        return DataTables::of($dataList)->addIndexColumn()
            ->addColumn('as_id', function ($dataList) use($request) {
                return $dataList['associate_id'];
            })
            ->addColumn('as_name', function ($dataList) {
                return $dataList['as_name'];
            })
            ->addColumn('date', function ($dataList) use($request) {
                if($request->status == 'join') {
                    return $dataList['as_doj'];
                } else {
                    return $dataList['as_status_date'];
                }
            })
            ->rawColumns(['as_id','as_name','date'])
            ->make(true);
            // ->toJson();
    }

    public function hrOutsideSearchEmpDateDataList(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $showTitle = $this->pageTitle($request1);
            $dataList = [];
            if($request1['type'] == 'year') {
                $dataList = $this->getEmpstatusFromDate($request1,$request1['type'],null,null,1);
            }
            if($request1['type'] == 'month') {
                $dataList = $this->getEmpstatusFromDate($request1,$request1['type'],null,null,1);
            }
            if($request1['type'] == 'date') {
                $dataList = $this->getEmpstatusFromDate($request1['date'],$request1['type'],null,null,1);
            }
            $result['page'] = view('hr.search.outside.allemployeeDateList',compact('showTitle','request1','dataList'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrAllUnit(Request $request)
    {
        try {
            $showTitle = $this->pageTitle($request);
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $unit_list = Unit::where('hr_unit_status',1)->get();
            foreach($unit_list as $k=>$unit) {
                $empActiveList[$unit->hr_unit_id] = $this->getActiveEmpFromDate($request1,$request1['type'],$unit->hr_unit_id);
                $empList[$unit->hr_unit_id] = $this->getEmpstatusFromDate($request1,$request1['type'],null,'all',null,$unit->hr_unit_id);
            }
            // $total_status[]=array_merge($empActiveList,$empList);
            // return [$empActiveList,$empList];
            // dd($total_status); exit;
            
            $result = [];
            $result['page'] = view('hr.search.empstatus.all_unit',
                compact('unit_emp', 'request', 'area_count', 'attCountUnitWise', 'showTitle','unit_list','empActiveList','empList'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }


    public function hrUnitEmployeeList(Request $request)
    {
        try {
            // get previous url params
            $status = $request->status;
            $unit = $request->unit;
            // dd($unit);
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $showTitle = $this->pageTitle($request1);
            $result['page'] = view('hr.search.empstatus.unitwise_employee',compact('showTitle','request1','status','unit'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrUnitEmpData(Request $request)
    {
        $dataList = [];
        if($request->data['type'] == 'year') {
            if($request->status == 'join') {
                $dataList = $this->getActiveEmpFromDate($request->data,$request->data['type'],$request->unit);
            } else {
                $dataList = $this->getEmpstatusFromDate($request->data,$request->data['type'],Custom::getEmpStatusName($request->status),null,null,$request->unit);
            }
        }
        if($request->data['type'] == 'month') {
            if($request->status == 'join') {
                $dataList = $this->getActiveEmpFromDate($request->data,$request->data['type'],$request->unit);
            } else {
                $dataList = $this->getEmpstatusFromDate($request->data,$request->data['type'],Custom::getEmpStatusName($request->status),null,null,$request->unit);
            }
        }
        if($request->data['type'] == 'date') {
            if($request->status == 'join') {
                $dataList = $this->getActiveEmpFromDate($request->data['date'],$request->data['type'],$request->unit);
            } else {
                $dataList = $this->getEmpstatusFromDate($request->data['date'],$request->data['type'],Custom::getEmpStatusName($request->status),null,null,$request->unit);
            }
        }
        // return $dataList;
        return DataTables::of($dataList)->addIndexColumn()
            ->addColumn('as_id', function ($dataList) use($request) {
                return $dataList['associate_id'];
            })
            ->addColumn('as_name', function ($dataList) {
                return $dataList['as_name'];
            })
            ->addColumn('date', function ($dataList) use($request) {
                if($request->status == 'join') {
                    return $dataList['as_doj'];
                } else {
                    return $dataList['as_status_date'];
                }
            })
            ->rawColumns(['as_id','as_name','date'])
            ->make(true);
            // ->toJson();
    }

    
    // print section =======
    public function hrEmpstatusSearchPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        return view('hr.search.empstatus.print.allempstatusPrint',compact('data','title'))->render();
    }

}
