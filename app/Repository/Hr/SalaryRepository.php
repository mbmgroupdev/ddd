<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\SalaryInterface;
use App\Repository\Hr\EmployeeRepository;
use DB;
use Illuminate\Support\Collection;

class SalaryRepository implements SalaryInterface
{
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }
	public function getSalaryReport($input, $data)
    {
        $result['summary']      = $this->makeSummarySalary($data);
        $list = collect($data)
            ->groupBy($input['report_group'],true);
        if(!empty($input['selected'])){
            $input['report_format'] = 0;
        }
        if($input['report_format'] == 1){
            $list = $list->map(function($q){
                $q = collect($q);
                $sum  = (object)[];
                $sum->ot            = $q->where('ot_status', 1)->count();
                $sum->otAmount      = $q->where('ot_status', 1)->sum('total_payable');
                $sum->nonot         = $q->where('ot_status', 0)->count();
                $sum->nonotAmount   = $q->where('ot_status', 0)->sum('total_payable');
                $sum->otHour        = $q->where('ot_status', 1)->sum('ot_hour');
                $sum->otHourAmount  = $q->sum(function ($s) {
                                        return ($s->ot_hour * $s->ot_rate);
                                    });
                $sum->cashPayable   = $q->sum('cash_payable');
                $sum->bankPayable   = $q->sum('bank_payable');
                $sum->stamp         = $q->sum('stamp');
                $sum->tds           = $q->sum('tds');
                $sum->salaryPayable = $q->sum('salary_payable');
                $sum->totalPayable  = $q->sum('total_payable');
                $sum->foodAmount    = $q->sum('food_deduct');
                return $sum;
            })->all();
        }

        $result['uniqueGroup'] = $list;
        $result['input']       = $input->all();
        $result['format']      = $input['report_group'];
        $result['unit']        = unit_by_id();
        $result['location']    = location_by_id();
        $result['line']        = line_by_id();
        $result['floor']       = floor_by_id();
        $result['department']  = department_by_id();
        $result['designation'] = designation_by_id();
        $result['section']     = section_by_id();
        $result['subSection']  = subSection_by_id();
        $result['area']        = area_by_id();
        return $result;
    }

    public function getSalaryByFilter($input, $dataRow, $employee){
        
        $subSection = subSection_by_id();
        $getFoodDeduct = $this->getFoodDeductList($input['year_month']);
        return collect($dataRow)->map(function($q) use ($subSection, $employee, $getFoodDeduct) {
            $q->as_section_id = $subSection[$q->sub_section_id]['hr_subsec_section_id']??'';
            $q->as_department_id = $subSection[$q->sub_section_id]['hr_subsec_department_id']??'';
            $q->as_area_id = $subSection[$q->sub_section_id]['hr_subsec_area_id']??'';
            $q->as_name = $employee[$q->as_id]->as_name??'';
            $q->as_oracle_code = $employee[$q->as_id]->as_oracle_code??'';
            $q->as_line_id = $employee[$q->as_id]->as_line_id??'';
            $q->as_floor_id = $employee[$q->as_id]->as_floor_id??'';
            $q->as_unit_id = $q->unit_id;
            $q->as_location = $q->location_id;
            $q->as_subsection_id = $q->sub_section_id;
            $q->as_designation_id = $q->designation_id;
            $q->food_deduct = $getFoodDeduct[$q->as_id]??0;
            unset($q->unit_id, $q->location_id, $q->sub_section_id, $q->designation_id);
            return $q;
        });
        
        // $collection = collect($getSalary)->whereNotIn('as_id', config('base.ignore_salary'))->sortByDesc('gross');

        // if(isset($input['employee']) && $input['employee'] != null && $input['report_format'] == 0){
        //     $collection = collect($collection)->where('as_id', 'LIKE', '%'.$input['employee'] .'%');
        // }

        // if(isset($input['min_sal']) && $input['min_sal'] != null){
        //     $collection = collect($collection)->whereBetween('gross', [$input['min_sal'], $input['max_sal']]);
        // }

        // if(isset($input['unit']) && $input['unit'] != null){
        //     $collection = collect($collection)->whereIn('as_unit_id', $input['unit']);
        // }

        // if(isset($input['location']) && $input['location'] != null){
        //     $collection = collect($collection)->whereIn('as_location', $input['location']);
        // }

        // if(isset($input['pay_status']) && $input['pay_status'] != null){
        //     if($input['pay_status'] == 'cash'){
        //         $collection = collect($collection)->where('cash_payable', '>', 0);
        //     }elseif($input['pay_status'] != 'all'){
        //         $collection = collect($collection)->where('pay_type', $input['pay_status']);
        //     }
        // }

        // if(isset($input['area']) && $input['area'] != null){
        //     $collection = collect($collection)->whereIn('as_area_id', $input['area']);
        // }

        // if(isset($input['department']) && $input['department'] != null){
        //     $collection = collect($collection)->where('as_department_id', $input['department']);
        // }

        // if(isset($input['section']) && $input['section'] != null){
        //     $collection = collect($collection)->where('as_section_id', $input['section']);
        // }

        // if(isset($input['subSection']) && $input['subSection'] != null){
        //     $collection = collect($collection)->where('as_subsection_id', $input['subSection']);
        // }

        // if(isset($input['otnonot']) && $input['otnonot'] != null){
        //     $collection = collect($collection)->where('ot_status', $input['otnonot']);
        // }

        // if(isset($input['floor_id']) && $input['floor_id'] != null){
        //     $collection = collect($collection)->where('as_floor_id', $input['floor_id']);
        // }

        // if(isset($input['line_id']) && $input['line_id'] != null){
        //     $collection = collect($collection)->where('as_line_id', $input['line_id']);
        // }

        // if(isset($input['selected'])){
        //     if($input['selected'] == 'null'){
        //         $collection = collect($collection)->whereNull($input['report_group']);
        //     }else{
        //         $collection = collect($collection)->where($input['report_group'], $input['selected']);
        //     }
        // }

        // return $collection;
    }

    protected function getFoodDeductList($yearMonth)
    {
        $yearMonthExp = explode('-', $yearMonth);
        return DB::table('hr_salary_add_deduct')
        ->where('year', $yearMonthExp[0])
        ->where('month', $yearMonthExp[1])
        ->pluck('food_deduct', 'associate_id');
    }

    public function getSalaryByMonth($input){
        $input['emp_status'] = $input['emp_status']??1;
        $yearMonthExp = explode('-', $input['year_month']);
        $data = DB::table('hr_monthly_salary')
        ->where('emp_status', $input['emp_status'])
        ->where('year', $yearMonthExp[0])
        ->where('month', $yearMonthExp[1])
        ->whereIn('unit_id', auth()->user()->unit_permissions())
        ->whereIn('location_id', auth()->user()->location_permissions())
        ->get();
        return $data;
    }

    protected function makeSummarySalary($data){
        $data = collect($data);
        $sum  = (object)[];
        $sum->totalOt          = $data->where('ot_status', 1)->count();
        $sum->totalOtAmount    = $data->where('ot_status', 1)->sum('total_payable');
        $sum->totalNonot       = $data->where('ot_status', 0)->count();
        $sum->totalNonotAmount = $data->where('ot_status', 0)->sum('total_payable');
        $sum->totalOtHour      = $data->where('ot_status', 1)->sum('ot_hour');
        $sum->totalSalary      = $data->sum('total_payable');
        $sum->totalCashSalary  = $data->sum('cash_payable');
        $sum->totalBankSalary  = $data->sum('bank_payable');
        $sum->totalStamp       = $data->sum('stamp');
        $sum->totalTax         = $data->sum('tds');
        $sum->totalEmployees   = $data->count();
        $sum->totalFood        = $data->sum('food_deduct');
        $sum->totalOTHourAmount   = $data->sum(function ($s) {
                                    return ($s->ot_hour * $s->ot_rate);
                                });
        return $sum;
    }
}