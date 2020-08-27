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
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Validator, Auth, ACL, DB, DataTables;

class SalaryIncreaseDecreaseSearchContorller extends Controller
{

	 public function hrSalIncDecSearch(Request $request)
    {
        try{
        	// dd($request->all());exit;
            return $this->searchSalIncDecReport_global($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getSearchType($request)
    {
        if($request['type'] == 'month') {
            $date = [
            'month' => date('m', strtotime($request['month'])),
            'year' => date('Y', strtotime($request['month']))
           ];        
        }else{
            $date = ['year'=> $request['year'] ];
        }
        return $date;
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

    public function getEmployeeCountStatusWise($status, $month='', $year=''){
    	//function that count status of employess.................
    		if($status==1){
    			$date_str = $year."-".$month."-01";
    			// $ck_doj = date('Y-m-d', strtotime($date_str));

    			$count = DB::table('hr_as_basic_info')
							->where('as_status', $status)
							->where('as_doj', '<', $date_str)
							->count('as_id');
    		}
    		else{
	    		$count = DB::table('hr_as_basic_info')
							->where('as_status', $status)
							->where(function($qw) use ($month, $year){
								if($month != '' && $year != ''){
									$qw->whereMonth('as_status_date', $month);
									$qw->whereYear('as_status_date', $year);
								}
							})
							->count('as_id');
    		}

			return $count;
    }
    public function getNewEmployeeCount($status, $month='', $year=''){
    	//function that count new joined employees..............
    		$count = DB::table('hr_as_basic_info')
						->where('as_status', $status)
						->where(function($qw) use ($month, $year){
							if($month != '' && $year != ''){
								$qw->whereMonth('as_doj', $month);
								$qw->whereYear('as_doj', $year);
							}
						})
						->count('as_id');
			return $count;
    }

	public function searchSalIncDecReport_global($request){
		
		try{
			$showTitle = $this->pageTitle($request);
			// get previous url params
            $parts = parse_url(url()->previous());
            // parse_str($parts['query'], $request1);

            $date = $this->getSearchType($request);

            if(isset($parts['query'])){
                
                if(isset($request1['category'])) {
                    $request1['category'] = $request['category'];
                }
                if(isset($request1['type'])) {
                    $request1['type'] = $request['type'];
                }
                if(isset($request['month'])){
                    $request1['month'] = $request['month'];
                }
                if(isset($request['year'])){
                    $request1['year'] = $request['year'];
                }

                // dd($request1);

                $request = ['category'=> $request['category'], 'type' => $request['type'], 'month' => $request['month'],'year' => $request['year']];
                // dd(url('hr/search?').http_build_query($request));
            }

            // dd($request);exit;

            //default declare 
            	$salInfo_input_month = new Employee;
            	$salInfo_input_month->total_payable = round(0.0, 2);
            	$salInfo_input_month->ot_payable 	= round(0.0, 2);
            	$salInfo_input_month->emp 			= 0;

            	$salInfo_previous = new Employee;
            	$salInfo_previous->total_payable = round(0.0, 2);
            	$salInfo_previous->ot_payable = round(0.0, 2);
            	$salInfo_previous->previous_month = '';
            	$salInfo_previous->previous_Y = '';
            	
            	$yearly_compare = 'no';

            	$input_year_salary = new Employee;
        		$input_year_salary->total_payable = round(0.0, 2);
        		$input_year_salary->ot_payable = round(0.0, 2);
				$pre_year_salary = new Employee;
        		$pre_year_salary->total_payable = round(0.0, 2);
        		$pre_year_salary->ot_payable = round(0.0, 2);

        		$prevoius_out_emp['tmp'] = '';
        		$current_out_emp['tmp'] = '';
				$new_employee['tmp'] = '';        		
            //default declare end

            //Salary calculation or fetching
            if(isset($request['month'])){
	            $str = explode('-', $request['month']);

	            $current_month  = date('m', strtotime($request['month'])); 
	            $current_Y  	= date('Y', strtotime($request['month']));

	            // $previous_month	= date('m', strtotime(date($request1['month'])." -1 month"));
	            // $previous_Y		= date('Y', strtotime(date($request1['month'])." -1 month"));


	            if($current_month == '01'){
		            $previous_month = '12';
		            $previous_Y 	= (string) ((int) $current_Y - 1);
	            }
	            else{
	            	$previous_month = (string) ((int) $current_month -1);
	            	if(strlen($previous_month)<2){
	            		$previous_month = '0'.$previous_month;
	            	}
		            $previous_Y 	= $current_Y;	
	            }

	            // dd($previous_month, $previous_Y, $current_month, $current_Y);exit;

	            //Employee IN OUT Calculation.....
	            //OUT calculation-> 0-delete, 1-active, 2-resign, 3-terminate, 4-suspend
	            	$delete 	= $this->getEmployeeCountStatusWise(0,$previous_month, $previous_Y);
	            	$active 	= $this->getEmployeeCountStatusWise(1,$previous_month, $previous_Y);
	            	$resign 	= $this->getEmployeeCountStatusWise(2,$previous_month, $previous_Y);
	            	$terminate  = $this->getEmployeeCountStatusWise(3,$previous_month, $previous_Y);
	            	$suspand 	= $this->getEmployeeCountStatusWise(4,$previous_month, $previous_Y);
	            	$maternity 	= $this->getEmployeeCountStatusWise(6,$previous_month, $previous_Y);

	            	$prevoius_out_emp['deleted'] 	= $delete;
	            	$prevoius_out_emp['active'] 	= $active;
	            	$prevoius_out_emp['resigned'] 	= $resign;
	            	$prevoius_out_emp['terminated'] = $terminate;
	            	$prevoius_out_emp['suspand'] 	= $suspand;
	            	$prevoius_out_emp['maternity'] 	= $maternity;

	            	$delete 	= $this->getEmployeeCountStatusWise(0,$current_month, $current_Y);
	            	$active 	= $this->getEmployeeCountStatusWise(1,$current_month, $current_Y);
	            	$resign 	= $this->getEmployeeCountStatusWise(2,$current_month, $current_Y);
	            	$terminate  = $this->getEmployeeCountStatusWise(3,$current_month, $current_Y);
	            	$suspand 	= $this->getEmployeeCountStatusWise(4,$current_month, $current_Y);
	            	$maternity 	= $this->getEmployeeCountStatusWise(6,$current_month, $current_Y);

	            	$current_out_emp['deleted'] 	= $delete;
	            	$current_out_emp['active'] 		= $active;
	            	$current_out_emp['resigned'] 	= $resign;
	            	$current_out_emp['terminated']  = $terminate;
	            	$current_out_emp['suspand'] 	= $suspand;
	            	$current_out_emp['maternity'] 	= $maternity;



	            //New joined employee
	            	$new_previous_month_emp  = $this->getNewEmployeeCount(1,$previous_month, $previous_Y);
	            	$new_current_month_emp   = $this->getNewEmployeeCount(1,$current_month, $current_Y);

	            	$new_employee['previous'] 	= $new_previous_month_emp;
	            	$new_employee['current']	= $new_current_month_emp;

	            	// dd($prevoius_out_emp, $current_out_emp, $new_employee );exit;
	            //SALARY CALCULATION.....for month
	            if($current_month == date('m', strtotime( date('Y-m-d') )) && $current_Y == date('Y', strtotime( date('Y-m-d') )) ){
	            	// dd('Today');
	            	$total_employee = DB::table('hr_as_basic_info')->count('associate_id');
	            	// dd($total_employee);

	            	$payble_without_ot = DB::table('hr_benefits')
	            						->select([
	            							DB::raw('sum(ben_current_salary) as ben_current_salary_total')
	            						])
	            						->get();
	            						// ->sum('ben_current_salary', 'ben_house_rent', 'ben_medical', 'ben_transport', 'ben_food');
	            	$total_payable_without_ot =  $payble_without_ot[0]->ben_current_salary_total;

	            	// dd($total_payable_without_ot);

	            	$total_months_in_monthly_salary = DB::table('hr_monthly_salary')->select('month','year')->groupBy('month')->orderBy('year', 'DESC')->get()->toArray();
	            	// dd($total_months_in_monthly_salary[1]->month);
		            	if(sizeof($total_months_in_monthly_salary) == 0){
		            		$projected_ot = 0;
		            	}
		            	else if(sizeof($total_months_in_monthly_salary) < 3){
		            		//1 month..ot
		            		$projected_ot = DB::table('hr_monthly_salary')
		            									->select([
		            										DB::raw('sum(ot_hour*ot_rate) AS ot_payable')
		            									])
		            									->where([
		            												'month'=>$total_months_in_monthly_salary[0]->month, 
		            												'year'=>$total_months_in_monthly_salary[0]->year 
		            											])
		            									->get()->toArray();

		            		$projected_ot = $projected_ot[0]->ot_payable;
		            		// dd('Single',$projected_ot);
		            	}
		            	else{
		            		//last 3 months ot ..
		            		$limit=sizeof($total_months_in_monthly_salary);
		            		// dd($limit);
		            		$projected_ot = 0.0;
		            		for ($i=$limit-1; $i >= $limit-3 ; $i--) { 
		            			$single_month_ot = DB::table('hr_monthly_salary')
		            									->select([
		            										DB::raw('sum(ot_hour*ot_rate) AS ot_payable')
		            									])
		            									->where([
		            												'month'=>$total_months_in_monthly_salary[$i]->month, 
		            												'year'=>$total_months_in_monthly_salary[$i]->year 
		            											])
		            									->get()->toArray();
		            									// dd($single_month_ot[0]->ot_payable);
		            			$projected_ot += $single_month_ot[0]->ot_payable;
		            		}

		            		$projected_ot = $projected_ot/3.0; //Average the value
		            		// dd("Multiple",$projected_ot);
		            	}


	            	// $salInfo_input_month = $total_payable_without_ot + $projected_ot;

	            	$find_pre_month_in_salary_table = DB::table('hr_monthly_salary')
	            														->where([
	            																	'month'=>$previous_month,
	            																	'year' =>$previous_Y
	            																])
	            														->count('month');

	          		// dd($find_pre_month_in_salary_table);

		            	if($find_pre_month_in_salary_table == 0 ){
		            		$has_pre_month_data = 'no';
		            	}else{
		            		$has_pre_month_data = 'yes';
		            	}

		            $salInfo_previous = $this->fetchData($previous_month, $previous_Y);
	            	

	            	$salInfo_input_month = new Employee;
	            	$salInfo_input_month->total_payable = round($total_payable_without_ot, 2);
	            	$salInfo_input_month->ot_payable 	= round($projected_ot, 2);
	            	$salInfo_input_month->emp 			= $total_employee;

	            	$salInfo_previous->total_payable = round($salInfo_previous->total_payable, 2);
	            	$salInfo_previous->ot_payable = round($salInfo_previous->ot_payable, 2);
	            	$salInfo_previous->previous_month = $previous_month;
	            	$salInfo_previous->previous_Y = $previous_Y;


	            	// dd($salInfo_input_month ,$salInfo_previous);


	            	

	            	}//when input month is current month-- end
	            	else{
	            		
	            		//when the input month is not current month
	            		$salInfo_input_month = $this->fetchData($current_month, $current_Y);
	            		$salInfo_input_month->total_payable = round($salInfo_input_month->total_payable, 2);
	            		$salInfo_input_month->ot_payable = round($salInfo_input_month->ot_payable, 2);

	            		$salInfo_previous 	 = $this->fetchData($previous_month, $previous_Y);
	            		$salInfo_previous->total_payable = round($salInfo_previous->total_payable, 2);
	            		$salInfo_previous->ot_payable = round($salInfo_previous->ot_payable, 2);
	            		$salInfo_previous->previous_month = $previous_month;
	            		$salInfo_previous->previous_Y = $previous_Y;


	            		// dd($current_month."-".$current_Y,$salInfo_input_month,$previous_month."-".$previous_Y, $salInfo_previous);

	            	} 

	            



	            // $salInfo_previous = $this->fetchData($previous_month, $previous_Y);
	            // $salInfo = $this->fetchData(null, $previous_Y);

		        // dd($salInfo_previous);

            }
            else{
            	// dd('Year');
            	$current_Y  = $request['year'];
            	$previous_Y = (string) ( (int)$request['year']-1);
            	

            	$check_input_Y = DB::table('hr_monthly_salary')
            						->select('year')
            						->where('year', $current_Y)
            						->first();
            	$check_pre_Y = DB::table('hr_monthly_salary')
            						->select('year')
            						->where('year', $previous_Y)
            						->first();
            	// dd( $check_input_Y,  $check_pre_Y);
            	if($check_input_Y == null && $check_pre_Y == null){
            		// dd('Null Check');
            		$pre_year_salary = new Employee;
            		$pre_year_salary->total_payable = round(0.0, 2);
            		$pre_year_salary->ot_payable = round(0.0, 2);

            		$input_year_salary = new Employee;
            		$input_year_salary->total_payable = round(0.0, 2);
            		$input_year_salary->ot_payable = round(0.0, 2);
            	}
            	else if($check_input_Y == null && $check_pre_Y != null){

            		//pre_year
            		$pre_year_salary = $this->fetchData(null,$previous_Y);
            		$pre_year_salary->total_payable = round($pre_year_salary->total_payable, 2);
            		$pre_year_salary->ot_payable = round($pre_year_salary->ot_payable, 2);


            		$input_year_salary = new Employee;
            		$input_year_salary->total_payable = round(0.0, 2);
            		$input_year_salary->ot_payable = round(0.0, 2);

            		// dd($pre_year_salary, $input_year_salary);

            	}
            	else if($check_input_Y != null && $check_pre_Y == null){
					//pre_year
            		$pre_year_salary = new Employee;
            		$pre_year_salary->total_payable = round(0.0, 2);
            		$pre_year_salary->ot_payable = round(0.0, 2);

            		$input_year_salary = $this->fetchData(null,$current_Y);
            		$input_year_salary->total_payable = round($input_year_salary->total_payable, 2);
            		$input_year_salary->ot_payable = round($input_year_salary->ot_payable, 2);

            		// dd($pre_year_salary, $input_year_salary);            		
            	}
            	else{
            		$yearly_compare = 'yes';
            		$pre_year_salary   = $this->fetchData(null, $previous_Y);
            		$pre_year_salary->total_payable = round($pre_year_salary->total_payable, 2);
            		$pre_year_salary->ot_payable = round($pre_year_salary->ot_payable, 2);
            		
            		$input_year_salary = $this->fetchData(null, $current_Y);
            		$input_year_salary->total_payable = round($input_year_salary->total_payable, 2);
            		$input_year_salary->ot_payable = round($input_year_salary->ot_payable, 2);

            		// dd($pre_year_salary, $input_year_salary);
            	}
            }




            // dd($salInfo_input_month, $salInfo_previous);exit;

            $result['page'] = view('hr.search.salincdec.salary_increase_decrease', compact('showTitle', 'request', 'salInfo_input_month', 'salInfo_previous', 'pre_year_salary','input_year_salary', 'yearly_compare', 'prevoius_out_emp', 'current_out_emp', 'new_employee' ))->render();
            $result['url'] = url('hr/search?').http_build_query($request);

            // dd($result);exit;
            return $result;

		}catch(\Exception $e) {
            return $e->getMessage();
        }

	}

	public function fetchData($m=null, $y){
			//Fetching the data..

	        $query= DB::table('hr_monthly_salary')
	                ->select(
	                    DB::raw('sum(total_payable) AS total_payable'),
	                    DB::raw('sum(ot_hour*ot_rate) AS ot_payable'),
	                    'as_id'
	                );
	                if(isset($m)){
	                	$query->where(['month' => (int)$m, 'year' => $y ]);
	                }
	                else{
	                	$query->where(['year' => $y ]);	
	                }
	                // ->where(DB::raw("CONCAT(month, '-', year)"),[$date]);
	                // ->get();
	        $salaryData = $query->groupBy('as_id');

	        // dd($salaryData);
	         
	         $salaryData_sql = $salaryData->toSql();


	         $query1 = DB::table('hr_as_basic_info AS e')
	                        ->select(
	                            DB::raw('sum(a.total_payable) AS total_payable'),
	                            DB::raw('sum(a.ot_payable) AS ot_payable'),
	                            DB::raw('count(a.as_id) AS emp')
	                        );
	            $query1->Join(DB::raw('(' . $salaryData_sql. ') AS a'), function($join) use ($salaryData) {
	                        $join->on('a.as_id', '=', 'e.associate_id')->addBinding($salaryData->getBindings()); ;
	                    });
	            
	            $salInfo = $query1->first();

	        return $salInfo;
	}
}