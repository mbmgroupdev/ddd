<?php

namespace App\Http\Controllers\Hr\Search;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Unit;
use App\Models\Hr\Line;
use App\Models\Hr\Station;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Validator, Auth, ACL, DB, DataTables, stdClass;

class LineSearchController extends Controller
{
    public function hrLineSearch(Request $request)
    {
        try{
        	
            return $this->hrLineSearchGlobal($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function pageTitle($request)
    {
        $showTitle = 'Line Change - '.ucwords($request['type']) ;
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
        if($request['type'] == 'month') {
            $date = [
            'month' => date('m', strtotime($request['month'])),
            'year' => date('Y', strtotime($request['month']))
           ];        
        }else if($request['type'] == 'date') {          
            $date=['date'=> $request['date']];
        }else if($request['type'] == 'year') {
            $date = ['year'=> $request['year'] ];
        }else{
            $date=[ 'year' => date('Y-m-d') ];
        } 
        return $date;
    }




    public function getLineInfo($date,$where)
    {
    	$lineInfo = Station::where(function($condition) use ($date){
		                if(isset($date['date'])){
		                    $condition->where('start_date', '<=', $date['date']);
		                	$condition->where('end_date', '>=', $date['date']);
		                }
		                if(isset($date['month'])){
		                	$year = $date['year'];
		                	$month = $date['month'];
		                	$condition->where('start_date','like',"$year-$month-%");
	                		$condition->orWhere('end_date','like',"$year-$month-%");
	                	}
	                	if(isset($date['year'])&& empty($date['month'])){
	                		$year = $date['year'];
		                	$condition->where('start_date','like',"$year-%-%");
	                		$condition->orWhere('end_date','like',"$year-%-%");
	                	}
		            })
	    			->where(function($q) use ($where){
		                if (!empty($where['unit'])) {
	                        $q->where('unit_id', $where['unit']);
	                    }
	                    if (!empty($where['floor'])) {
	                        $q->where('changed_floor', $where['floor']);
	                    }
	                    if (!empty($where['line'])) {
	                        $q->where('changed_line', $where['line']);
	                    }
		            })
		            ->get();
	    return $lineInfo;    
    }


    public function hrLineSearchGlobal($request)
    {
        try {
            //return $request;
            $date = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            $info = $this->getLineInfo($date,[]);
            unset($request['line'],$request['floor'],$request['unit'],$request['view']);

            $lineInfo = new stdClass();
            $lineInfo->unit = Unit::count();
            $lineInfo->line = $info->unique('changed_line')->count();
            $lineInfo->emp = $info->unique('associate_id')->count();
            $lineInfo->line_change = $info->count();
            $result = [];
            $result['page'] = view('hr.search.line.info',
                compact('lineInfo','showTitle', 'request'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }


    public function hrLineSearchUnit(Request $request)
    {
        try {
            //return $request;
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view'] = 'allunit';
            unset($request1['line'],$request1['floor'],$request1['unit']);

            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);
            $unit_list = Unit::get();
            $unit_data = [];
            foreach ($unit_list as $key => $unit) {
            	$unit_data[$unit->hr_unit_id] = new stdClass();
            	$where = ['unit' => $unit->hr_unit_id];
            	$info = $this->getLineInfo($date, $where);
            	$unit_data[$unit->hr_unit_id]->hr_unit_id = $unit->hr_unit_id;
            	$unit_data[$unit->hr_unit_id]->hr_unit_name = $unit->hr_unit_name;
            	$unit_data[$unit->hr_unit_id]->floor = Floor::where('hr_floor_unit_id', $unit->hr_unit_id)->count();
            	$unit_data[$unit->hr_unit_id]->emp = $info->unique('associate_id')->count();
            	$unit_data[$unit->hr_unit_id]->line_change = $info->count();
            }


            $result = [];
            $result['page'] = view('hr.search.line.allunit',
                compact('unit_data','showTitle', 'request'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrLineSearchFloor(Request $request)
    {
        try {
            //return $request;
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view'] = 'floor';
            unset($request1['line'],$request1['floor']);

            if(isset($request->unit)){
                $request1['unit'] = $request->unit;
            }
            $unit = Unit::where('hr_unit_id', $request1['unit'])->first();

            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);
            $floor_list = Floor::where('hr_floor_unit_id', $request1['unit'])->get();
            $floor_data = [];
            foreach ($floor_list as $key => $floor) {
            	$floor_data[$floor->hr_floor_id] = new stdClass();
            	$where = ['floor' => $floor->hr_floor_id, 'unit' => $request1['unit']];
            	$info = $this->getLineInfo($date, $where);
            	$floor_data[$floor->hr_floor_id]->hr_floor_id = $floor->hr_floor_id;
            	$floor_data[$floor->hr_floor_id]->hr_floor_name = $floor->hr_floor_name;
            	$floor_data[$floor->hr_floor_id]->line = Line::where(['hr_line_floor_id'=> $floor->hr_floor_id, 'hr_line_unit_id' => $request1['unit']])->count();
            	$floor_data[$floor->hr_floor_id]->emp = $info->unique('associate_id')->count();
            	$floor_data[$floor->hr_floor_id]->line_change = $info->count();
            }


            $result = [];
            $result['page'] = view('hr.search.line.allfloor',
                compact('floor_data','showTitle', 'request1','unit'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function hrLineSearchLine(Request $request)
    {
        try {
            //return $request;
            //dd($request->all());
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view'] = 'line';
            $data = [];
            if(isset($request->unit)){
                $request1['unit'] = $request->unit; 	
            }
            if(isset($request->floor)){
                $request1['floor'] = $request->floor;
            }
            unset($request1['line']);
            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            if(isset($request1['floor']) && $request1['unit']) {
            	$data['unit'] = Unit::where('hr_unit_id', $request1['unit'])->first();
            	$data['floor'] = Floor::where('hr_floor_id', $request1['floor'])->first();
            	$where = ['unit' => $request1['unit'], 'floor'=> $request1['floor']];
            	$line_list = Line::where([
            					'hr_line_floor_id'=> $request1['floor'], 
            					'hr_line_unit_id' => $request1['unit']
            				])
            				->leftJoin('hr_unit AS u','u.hr_unit_id','hr_line.hr_line_unit_id')
            				->leftJoin('hr_floor AS f','f.hr_floor_id','hr_line.hr_line_floor_id')
            				->get();

            }else{
            	$line_list = DB::table('hr_line AS l')
            				  ->Join('hr_station AS s','l.hr_line_id', 's.changed_line')
            				  ->leftJoin('hr_unit AS u','u.hr_unit_id','l.hr_line_unit_id')
            				  ->leftJoin('hr_floor AS f','f.hr_floor_id','l.hr_line_floor_id')
            				  ->groupBy('s.changed_line')
            				  ->get();
            	$where = [];

            }
            //dd($line_list);
            $line_data= [];
        	foreach ($line_list as $key => $line) {
            	$line_data[$line->hr_line_id] = new stdClass();
            	$where['line'] = $line->hr_line_id;
            	$info = $this->getLineInfo($date, $where);
            	$line_data[$line->hr_line_id]->hr_line_id = $line->hr_line_id;
            	$line_data[$line->hr_line_id]->hr_line_name = $line->hr_line_name;
            	$line_data[$line->hr_line_id]->unit = $line->hr_unit_name;
            	$line_data[$line->hr_line_id]->floor = $line->hr_floor_name;
            	$line_data[$line->hr_line_id]->emp = $info->unique('associate_id')->count();
            	$line_data[$line->hr_line_id]->line_change = $info->count();
            }

            //dd( $line_data);

            $result = [];
            $result['page'] = view('hr.search.line.allline',
                compact('line_data','showTitle', 'request1','data'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrLineSearchChange(Request $request)
    {
        try {
            
            $request2 = [];
            $data=[];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view']='change';
            //return $request1;
            if(isset($request->unit)){
                $request1['unit']= $request->unit;
            }
           
            if(isset($request->floor)){
                $request1['floor']= $request->floor;
            }
            if(isset($request->line)){
                $request1['line']= $request->line;
            }
            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            if(isset($request1['floor'])) {
                $data['floor'] = Floor::where('hr_floor_id',$request1['floor'])->first();
            }

            if(isset($request1['line'])) {
                $data['line'] = Line::where('hr_line_id',$request1['line'])->first();
            }

            if(isset($request1['unit'])) {
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            $result['page'] = view('hr.search.line.allchange',
                compact('data','request1','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function  hrLineSearchListChange(Request $request){
        try {

            $request2 = [];
            $parts = parse_url(url()->previous());
            $infocon=[];
            parse_str($parts['query'], $request1);
            //return $request1;
            $where = [];
            if(isset($request->unit)){
                $where['unit']= $request->unit;
            }
           
            if(isset($request->floor)){
                $where['floor']= $request->floor;
            }
            if(isset($request->line)){
                $where['line']= $request->line;
            }

            $date = $this->getSearchType($request1);

            $lineInfo = DB::table('hr_station AS s')
            			->select(
            				'e.as_name',
            				'e.associate_id',
            				's.*',
            				'e.as_floor_id',
            				'u.hr_unit_name',
            				'e.as_line_id'
            			)
            			->where(function($condition) use ($date){
			                if(isset($date['date'])){
			                    $condition->where('s.start_date', '<=', $date['date']);
			                	$condition->where('s.end_date', '>=', $date['date']);
			                }
			                if(isset($date['month'])){
			                	$year = $date['year'];
			                	$month = $date['month'];
			                	$condition->where('s.start_date','like',"$year-$month-%");
		                		$condition->orWhere('s.end_date','like',"$year-$month-%");
		                	}
		                	if(isset($date['year'])&& empty($date['month'])){
		                		$year = $date['year'];
			                	$condition->where('s.start_date','like',"$year-%-%");
		                		$condition->orWhere('s.end_date','like',"$year-%-%");
		                	}
			            })
		    			->where(function($q) use ($where){
			                if (!empty($where['unit'])) {
		                        $q->where('s.unit_id', $where['unit']);
		                    }
		                    if (!empty($where['floor'])) {
		                        $q->where('s.changed_floor', $where['floor']);
		                    }
		                    if (!empty($where['line'])) {
		                        $q->where('s.changed_line', $where['line']);
		                    }
			            })
            			->leftJoin('hr_as_basic_info AS e','e.associate_id','s.associate_id')
			            ->leftJoin('hr_unit AS u','u.hr_unit_id','s.unit_id')
			            ->get();

            //dd($lineInfo);
            return DataTables::of($lineInfo)->addIndexColumn()
                
                ->addColumn('changed_line', function ($lineInfo) {
                    return Line::where('hr_line_id',$lineInfo->changed_line)->first()->hr_line_name??'';
                })
                ->addColumn('changed_floor', function ($lineInfo) {
                    return Floor::where('hr_floor_id',$lineInfo->changed_floor)->first()->hr_floor_name??'';
                })
                ->editColumn('as_line_id', function ($lineInfo) {
                    return Line::where('hr_line_id',$lineInfo->as_line_id)->first()->hr_line_name??'';
                })
                ->editColumn('as_floor_id', function ($lineInfo) {
                    return Floor::where('hr_floor_id',$lineInfo->as_floor_id)->first()->hr_floor_name??'';
                })
                ->editColumn('associate_id', function ($lineInfo){
                            return HtmlFacade::link("#",$lineInfo->associate_id,['class' => 'employee_info','data-emp'=>$lineInfo->associate_id]);
                        })
                ->rawColumns(['changed_line','changed_floor','associate_id'])
                ->make(true);


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }
    public function hrLineSearchEmployee(Request $request)
    {
        try {
            
            $request2 = [];
            $data=[];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view']='employee';
            //return $request1;
            if(isset($request->unit)){
                $request1['unit']= $request->unit;
            }
           
            if(isset($request->floor)){
                $request1['floor']= $request->floor;
            }
            if(isset($request->line)){
                $request1['line']= $request->line;
            }
            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            if(isset($request1['floor'])) {
                $data['floor'] = Floor::where('hr_floor_id',$request1['as_floor_id'])->first();
            }

            if(isset($request1['line'])) {
                $data['area'] = Line::where('hr_line_id',$request1['line'])->first();
            }


            if(isset($request1['unit'])) {
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            $result['page'] = view('hr.search.line.allemployee',
                compact('data','request1','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function  employeeWiseChange(Request $request){
    	$parts = parse_url(url()->previous());
        parse_str($parts['query'], $request1);
        $date = $this->getSearchType($request1);
        $associate_id = $request->as_id;
    	$changes= DB::table('hr_station AS s')
    			->select(
    				'f.hr_floor_name',
    				'l.hr_line_name',
    				's.*'
    			)
    			->where(function($condition) use ($date){
	                if(isset($date['date'])){
	                    $condition->where('s.start_date', '<=', $date['date']);
	                	$condition->where('s.end_date', '>=', $date['date']);
	                }
	                if(isset($date['month'])){
	                	$year = $date['year'];
	                	$month = $date['month'];
	                	$condition->where('s.start_date','like',"$year-$month-%");
                		$condition->orWhere('s.end_date','like',"$year-$month-%");
                	}
                	if(isset($date['year'])&& empty($date['month'])){
                		$year = $date['year'];
	                	$condition->where('s.start_date','like',"$year-%-%");
                		$condition->orWhere('s.end_date','like',"$year-%-%");
                	}
	            })
    			->leftJoin('hr_as_basic_info AS e','e.associate_id','s.associate_id')
	            ->leftJoin('hr_floor AS f','f.hr_floor_id','s.changed_floor')
	            ->leftJoin('hr_line AS l','l.hr_line_id','s.changed_line')
	            ->where('s.associate_id',$associate_id)
	            ->get();
	    return view('hr.search.line.ajax_employee_line_change',compact('changes','associate_id'))->render();

    }

    public function  hrLineSearchListEmployee(Request $request){
        try {

            $request2 = [];
            $parts = parse_url(url()->previous());
            $infocon=[];
            parse_str($parts['query'], $request1);
            //return $request1;
            $where = [];
            if(isset($request->unit)){
                $where['unit']= $request->unit;
            }
           
            if(isset($request->floor)){
                $where['floor']= $request->floor;
            }
            if(isset($request->line)){
                $where['line']= $request->line;
            }

            $date = $this->getSearchType($request1);
            $query = DB::table('hr_station AS s')
            			->where(function($condition) use ($date){
			                if(isset($date['date'])){
			                    $condition->where('s.start_date', '<=', $date['date']);
			                	$condition->where('s.end_date', '>=', $date['date']);
			                }
			                if(isset($date['month'])){
			                	$year = $date['year'];
			                	$month = $date['month'];
			                	$condition->where('s.start_date','like',"$year-$month-%");
		                		$condition->orWhere('s.end_date','like',"$year-$month-%");
		                	}
		                	if(isset($date['year'])&& empty($date['month'])){
		                		$year = $date['year'];
			                	$condition->where('s.start_date','like',"$year-%-%");
		                		$condition->orWhere('s.end_date','like',"$year-%-%");
		                	}
			            })
		    			->where(function($q) use ($where){
			                if (!empty($where['unit'])) {
		                        $q->where('s.unit_id', $where['unit']);
		                    }
		                    if (!empty($where['floor'])) {
		                        $q->where('s.changed_floor', $where['floor']);
		                    }
		                    if (!empty($where['line'])) {
		                        $q->where('s.changed_line', $where['line']);
		                    }
			            });
            if(isset($date['date'])){
                $query->select(
                            'e.as_name',
                            'e.associate_id',
                            's.*',
                            'e.as_floor_id',
                            'u.hr_unit_name',
                            'e.as_line_id'
                        );               
            }else{
                $query->select(
                            'e.as_name',
                            'e.associate_id',
                            DB::raw('count(s.station_id) as count_change'),
                            'e.as_floor_id',
                            'u.hr_unit_name',
                            'e.as_line_id',
                            's.start_date',
                            's.end_date'
                        );
                $query->groupBy('s.associate_id');

            }
        	$lineInfo =$query->leftJoin('hr_as_basic_info AS e','e.associate_id','s.associate_id')
        		            ->leftJoin('hr_unit AS u','u.hr_unit_id','s.unit_id')
        		            ->get();

                return DataTables::of($lineInfo)->addIndexColumn()
                        ->editColumn('as_line_id', function ($lineInfo) {
                            return Line::where('hr_line_id',$lineInfo->as_line_id)->first()->hr_line_name??'';
                        })
                        ->editColumn('as_floor_id', function ($lineInfo) {
                            return Floor::where('hr_floor_id',$lineInfo->as_floor_id)->first()->hr_floor_name??'';
                        })
                        ->addColumn('changed_line', function ($lineInfo) use($date) {
                            $line = '';
                            if(isset($date['date'])){
                                $line =  Line::where('hr_line_id',$lineInfo->changed_line)->first()->hr_line_name??'';
                            }
                            return $line;
                        })
                        ->addColumn('changed_floor', function ($lineInfo) use($date) {
                            $floor = '';
                            if(isset($date['date'])){
                                $floor =  Floor::where('hr_floor_id',$lineInfo->changed_floor)->first()->hr_floor_name??'';
                            }
                            return $floor;
                        })
                        ->addColumn('count_change', function ($lineInfo) use($date) {
                            $count = 0;
                            if(!isset($date['date'])){
                                $count = $lineInfo->count_change;
                            }
                            return $count;
                        })
                        ->editColumn('associate_id', function ($lineInfo) use($date) {
                            if(!isset($date['date'])){
                                $emp = HtmlFacade::link("#",$lineInfo->associate_id,['class' => 'lineEmpDetail','data-emp'=>$lineInfo->associate_id]);
                            }else{
                                $emp = $lineInfo->associate_id;
                            }
                            return $emp;
                        })
                        ->rawColumns(['associate_id','changed_floor','changed_line'])
                        ->make(true);

            


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }


    public function hrLineSearchPrintPage(Request $request)
    {
        //dd($request->all());
        $info = [];
        if(isset($request->floor)) {
            $info['floor'] = Floor::where('hr_floor_id',$request->floor)->first()->hr_floor_name??'';
        }

        if(isset($request->line)) {
            $info['line'] = Line::where('hr_line_id',$request->line)->first()->hr_line_name??'';
        }

        if(isset($request->unit)) {
            $info['unit'] = Unit::where('hr_unit_id',$request->unit)->first()->hr_unit_name??'';
        }
        $type = $request->type;
        $data = $request->data;
        $title = $request->title;
        return view('hr.search.line.printpages',compact('data','title','info','type'))->render();
    }

}
