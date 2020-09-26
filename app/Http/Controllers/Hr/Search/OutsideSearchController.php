<?php

namespace App\Http\Controllers\Hr\Search;

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

class OutsideSearchController extends Controller
{
    public function hrOutsideSearch(Request $request)
    {
        try{
            return $this->searchGetOutsideReport_global($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function pageTitle($request)
    {
        $showTitle = ucwords($request['category']).' - '.ucwords($request['type']) ;
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

    public function getOutsideFromDate($date,$type,$location=null,$as_id=null,$all=null)
    {
        $outside_type_list = '';
        if($type == 'date') {
            $outside_type_list = Outsides::where(function ($q) use($date) {
                $q->where('start_date', '<=', $date);
                $q->where('end_date', '>=', $date);
            })
            ->where('status',1)
            ->when($location!=null&&$as_id!=null&&$all==null, function($q) use($location,$as_id) {
                $q->where('requested_location',$location);
                $q->where('as_id',$as_id);
            })
            ->when($location!=null&&$as_id==null&&$all==null, function($q) use($location) {
                $q->where('requested_location',$location);
                $q->groupBy('as_id');
            })
            ->get()
            ->when($location==null&&$all==null, function($q) {
                return $q->groupBy('requested_location');
            })
            ->toArray();
        } else if($type == 'month') {
            list($month,$year) = explode('-',$date['month']);
            $month = date('m', strtotime($month));
            $outside_type_list = Outsides::where(function ($q) use($month,$year) {
                $q->where('start_date','like',"$year-$month-%");
                $q->orWhere('end_date','like',"$year-$month-%");
            })
            ->where('status',1)
            ->when($location!=null&&$as_id!=null, function($q) use($location,$as_id) {
                $q->where('requested_location',$location);
                $q->where('as_id',$as_id);
            })
            ->when($location!=null&&$as_id==null, function($q) use($location) {
                $q->where('requested_location',$location);
                $q->groupBy('as_id');
            })
            ->get()
            ->when($location==null, function($q) {
                return $q->groupBy('requested_location');
            })
            ->toArray();
        } else if($type == 'range') {
            
        } else if($type == 'year') {
            $year = $date['year'];
            $outside_type_list = Outsides::where(function ($q) use($year) {
                $q->where('start_date','like',"$year-%-%");
                $q->orWhere('end_date','like',"$year-%-%");
            })
            ->where('status',1)
            ->when($location!=null&&$as_id!=null, function($q) use($location,$as_id) {
                $q->where('requested_location',$location);
                $q->where('as_id',$as_id);
            })
            ->when($location!=null&&$as_id==null, function($q) use($location) {
                $q->where('requested_location',$location);
                $q->groupBy('as_id');
            })
            ->get()
            ->when($location==null, function($q) {
                return $q->groupBy('requested_location');
            })
            ->toArray();
        }
        return $outside_type_list;
    }

    public function searchGetOutsideReport_global($request)
    {
        try {
            $showTitle = $this->pageTitle($request);
            // get previous url params
            $parts = parse_url(url()->previous());
            if(isset($parts['query'])){
                parse_str($parts['query'], $request1);
                // $request1['date'] = null;
                // $request2 = [];
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
            // return $request2;
            $locationList = $this->getOutsideFromDate($request2,$request['type']);
            $result['page'] = view('hr.search.outside.alloutside',compact('showTitle', 'request', 'locationList'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOutsideSearchEmployee(Request $request)
    {
        try {
            // get previous url params
            $location = $request->unit;
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $showTitle = $this->pageTitle($request1);
            $result['page'] = view('hr.search.outside.allemployee',compact('showTitle','request1','location'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOutsideGetEmpDetails(Request $request)
    {
        try {
            $dataList = [];
            if($request->data['type'] == 'year') {
                $dataList = $this->getOutsideFromDate($request->data,$request->data['type'],$request->location,$request->as_id);
            }
            if($request->data['type'] == 'month') {
                $dataList = $this->getOutsideFromDate($request->data,$request->data['type'],$request->location,$request->as_id);
            }
            if($request->data['type'] == 'date') {
                $dataList = $this->getOutsideFromDate($request->data['date'],$request->data['type'],$request->location,$request->as_id);
            }
            return view('hr.search.outside.ajax_empdetails',compact('dataList'))->render();
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOutsideSearchEmpData(Request $request)
    {
        if($request->data['type'] == 'year') {
            $dataList = $this->getOutsideFromDate($request->data,$request->data['type'],$request->location);
        }
        if($request->data['type'] == 'month') {
            $dataList = $this->getOutsideFromDate($request->data,$request->data['type'],$request->location);
        }
        if($request->data['type'] == 'date') {
            $dataList = $this->getOutsideFromDate($request->data['date'],$request->data['type'],$request->location);
        }
        // return $dataList;
        return DataTables::of($dataList)->addIndexColumn()
            ->addColumn('as_id', function ($dataList) use($request) {
                return '<a href="#" class="outsideEmpDetail" data-as_id="'.$dataList['as_id'].'" data-request="'.htmlspecialchars(json_encode($request->data), ENT_QUOTES, 'UTF-8').'" data-location="'.$request->location.'">'.$dataList['as_id'].'</a>';
            })
            ->addColumn('as_name', function ($dataList) {
                return $dataList['basic']['as_name'];
            })
            ->rawColumns(['as_id','as_name'])
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
                $dataList = $this->getOutsideFromDate($request1,$request1['type'],null,null,1);
            }
            if($request1['type'] == 'month') {
                $dataList = $this->getOutsideFromDate($request1,$request1['type'],null,null,1);
            }
            if($request1['type'] == 'date') {
                $dataList = $this->getOutsideFromDate($request1['date'],$request1['type'],null,null,1);
            }
            $result['page'] = view('hr.search.outside.allemployeeDateList',compact('showTitle','request1','dataList'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    // print section =======
    public function hrOutsideSearchPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        return view('hr.search.outside.print.alloutsidePrint',compact('data','title'))->render();
    }

}
