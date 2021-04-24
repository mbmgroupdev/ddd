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
                $sum->foodAmount    = 0;
                return $sum;
            })->all();
        }

        $result['uniqueGroup'] = $list;
        $result['input']       = $input;
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
        $getSalary = collect($dataRow)->map(function($q) use ($subSection, $employee) {
            $q->section_id = $subSection[$q->sub_section_id]['hr_subsec_section_id']??'';
            $q->department_id = $subSection[$q->sub_section_id]['hr_subsec_department_id']??'';
            $q->area_id = $subSection[$q->sub_section_id]['hr_subsec_area_id']??'';
            $q->as_name = $employee[$q->as_id]->as_name??'';
            $q->line_id = $employee[$q->as_id]->as_line_id??'';
            $q->floor_id = $employee[$q->as_id]->as_floor_id??'';
            return $q;
        });
        
        $queryData = collect($getSalary)->whereNotIn('as_id', config('base.ignore_salary'));
        if(isset($input['employee']) && !empty($input['employee']) && $input['report_format'] == 0){
            $queryData->where('as_id', 'LIKE', '%'.$input['employee'] .'%');
        }
        if(isset($input['min_sal'])){
            $queryData->whereBetween('gross', [$input['min_sal'], $input['max_sal']]);
        }
        $queryData->when(isset($input['unit']) && !empty($input['unit']), function ($query) use($input){
           return $query->whereIn('unit_id',$input['unit']);
        })
        ->when(isset($input['location']) && !empty($input['location']), function ($query) use($input){
           return $query->whereIn('location_id',$input['location']);
        })
        ->when(isset($input['pay_status']) && !empty($input['pay_status']), function ($query) use($input){
            if($input['pay_status'] == 'cash'){
                return $query->where('cash_payable', '>', 0);
            }elseif($input['pay_status'] != 'all'){
                return $query->where('pay_type', $input['pay_status']);
            }
        })
        ->when(isset($input['area']) && !empty($input['area']), function ($query) use($input){
           return $query->where('area_id',$input['area']);
        })
        ->when(isset($input['department']) && !empty($input['department']), function ($query) use($input){
           return $query->where('department_id',$input['department']);
        })
        ->when(isset($input['line_id']) && !empty($input['line_id']), function ($query) use($input){
           return $query->where('line_id', $input['line_id']);
        })
        ->when(isset($input['floor_id']) && !empty($input['floor_id']), function ($query) use($input){
           return $query->where('floor_id',$input['floor_id']);
        })
        ->when(isset($input['otnonot']) && $input['otnonot']!=null, function ($query) use($input){
           return $query->where('ot_status',$input['otnonot']);
        })
        ->when(isset($input['section']) && !empty($input['section']), function ($query) use($input){
           return $query->where('section_id', $input['section']);
        })
        ->when(isset($input['subSection']) && !empty($input['subSection']), function ($query) use($input){
           return $query->where('sub_section_id', $input['subSection']);
        });

        return $queryData->sortBy('department_id');
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
        $sum->totalFood        = 0;
        $sum->totalOTHourAmount   = $data->sum(function ($s) {
                                    return ($s->ot_hour * $s->ot_rate);
                                });
        return $sum;
    }
}