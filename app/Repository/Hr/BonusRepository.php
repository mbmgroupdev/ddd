<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\BonusInterface;
use App\Models\Hr\BonusRule;
use App\Repository\Hr\EmployeeRepository;
use DB;
use Illuminate\Support\Collection;

class BonusRepository implements BonusInterface
{
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }
	public function getBonusReport($input, $data)
    {
        $result['bonusSheet'] = BonusRule::findOrFail($input['sheet_id']);
        $result['summary'] = $this->makeSummaryBonus($data);
        $list = collect($data)
            ->groupBy($input['report_group'],true);
        if(!empty($input['selected'])){
            $input['report_format'] = 0;
        }
        if($input['report_format'] == 1){
            $list = $list->map(function($q){
                $q = collect($q);
                $sum  = (object)[];
                $sum->ot           = $q->where('ot_status', 1)->count();
                $sum->ot_amount    = $q->where('ot_status', 1)->sum('bonus_amount');
                $sum->nonot        = $q->where('ot_status', 0)->count();
                $sum->nonot_amount = $q->where('ot_status', 0)->sum('bonus_amount');
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
        $result['bonusType']   = bonus_type_by_id();
        return $result;
    }

    public function getBonusMergeByEmployee($dataRow, $employee){
        
        $subSection = subSection_by_id();
        $data = collect($dataRow)->map(function($q) use ($subSection, $employee) {
            $q->as_section_id = $subSection[$q->subsection_id]['hr_subsec_section_id']??'';
            $q->as_department_id = $subSection[$q->subsection_id]['hr_subsec_department_id']??'';
            $q->as_area_id = $subSection[$q->subsection_id]['hr_subsec_area_id']??'';
            $q->as_name = $employee[$q->associate_id]->as_name??'';
            $q->as_line_id = $employee[$q->associate_id]->as_line_id??'';
            $q->as_floor_id = $employee[$q->associate_id]->as_floor_id??'';
            $q->as_designation_id = $employee[$q->associate_id]->as_designation_id??'';
            $q->as_doj = $employee[$q->associate_id]->as_doj??'';
            $q->as_subsection_id = $q->subsection_id;
            $q->gross = $q->gross_salary;
            $q->as_unit_id = $q->unit_id;
            $q->as_location = $q->location_id;
            $q->as_oracle_code = $employee[$q->associate_id]->as_oracle_code??'';
            unset($q->subsection_id, $q->unit_id, $q->location_id);
            return $q;
        });
        return $data;
    }

    public function getBonusBySheet($input){
        $input['emp_status'] = $input['emp_status']??1;
        if(isset($input['group_unit'])){
            $groupUnit[] = $input['group_unit'];
            $unit = unit_by_id();
            $location = location_by_id();
            $groupLocation = collect($location)->where('hr_location_unit_id', $input['group_unit'])->pluck('hr_location_id');
            if($input['group_unit'] == 1){
                $groupUnit = collect($unit)->pluck('hr_unit_id');
                $groupLocation = [6,8,10,12,13,14];
            }
        }else{
            $groupUnit = auth()->user()->unit_permissions();
            $groupLocation = auth()->user()->location_permissions();
        }

        $data = DB::table('hr_bonus_sheet')
        ->where('emp_status', $input['emp_status'])
        ->where('bonus_rule_id', $input['sheet_id'])
        ->whereIn('unit_id', $groupUnit)
        ->whereIn('location_id', $groupLocation)
        ->when(isset($input['emp_type']) && !empty($input['emp_type'] && $input['emp_type'] != 'all'), function ($query) use($input){
            if($input['emp_type'] == 'lessyear'){
                return $query->where('duration', '<', 12);
            }elseif($input['emp_type'] == 'partial'){
                return $query->where('type', 'partial');
            }elseif($input['emp_type'] == 'special'){
                return $query->where('type', 'special');
            }
            $status = 6;
            if($input['emp_type'] == 'active'){
                $status = 1;
            }
            return $query->where('emp_status', $status);
        })
        ->when(isset($input['pay_status']) && !empty($input['pay_status']), function ($query) use($input){
           
            if($input['pay_status'] == 'cash'){
                return $query->where('cash_payable', '>', 0);
            }elseif($input['pay_status'] != 'all'){
                return $query->where('bank_name', $input['pay_status']);
            }
        })
        ->get();

        return $data;
    }

    protected function makeSummaryBonus($data){
        $data = collect($data);
        $sum  = (object)[];
        $sum->maternity         = $data->where('emp_status', 6)->count();
        $sum->maternity_amount  = $data->where('emp_status', 6)->sum('bonus_amount');
        $sum->active            = $data->where('emp_status', 1)->count();
        $sum->active_amount     = $data->where('emp_status', 1)->sum('bonus_amount');
        $sum->ot                = $data->where('ot_status', 1)->count();
        $sum->ot_amount         = $data->where('ot_status', 1)->sum('bonus_amount');
        $sum->nonot             = $data->where('ot_status', 0)->count();
        $sum->nonot_amount      = $data->where('ot_status', 0)->sum('bonus_amount');
        $sum->partial           = $data->where('duration','<' ,12)->count();
        $sum->partial_amount    = $data->where('duration','<' ,12)->sum('bonus_amount');
        $sum->stamp             = $data->sum('stamp');
        $cash = $data->where('cash_payable', '>', 0);
        $sum->cash_emp          = $cash->count();
        $sum->cash_amount       = $cash->sum('cash_payable');

        $group = collect($data)
                    ->whereIn('pay_status', [2,3])
                    ->groupBy('bank_name', true)
                    ->map(function($q){
                        $p = (object)[];
                        $p->emp = collect($q)->count();
                        $p->amount = collect($q)->sum('bank_payable');
                        return $p;
                    })->all();
        $sum->payment_group = $group;
       

        return $sum;
    }
}