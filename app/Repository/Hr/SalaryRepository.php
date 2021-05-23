<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\SalaryInterface;
use App\Models\Hr\AttendanceBonusConfig;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
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

    public function makeEmployeeBenefitValue($value='')
    {
        $addDeduct = $this->getEmployeeSalaryAddDeduct($value);
        $stamp = $this->getEmployeeStampAmount($value);
        $attBonus = $this->getEmployeeAttendanceBonus($value);
        $salaryAdjust = $this->getEmployeeSalaryAdjust($value);
        $value = array_merge($value, $addDeduct, $stamp, $attBonus, $salaryAdjust);
        $salaryPayable = $this->getEmployeeSalaryPayable($value);

        $value = array_merge($value, $salaryPayable);
        $cashBank = $this->getEmployeeSalaryCashBank($value);
        return array_merge($value, $cashBank);
    }

    public function getEmployeeSalaryAddDeduct($value='')
    {
        $getAddDeduct = SalaryAddDeduct::
            where('associate_id', $value['associate_id'])
            ->where('month', '=', $value['month'])
            ->where('year', '=', $value['year'])
            ->first();
        if($getAddDeduct != null){
            $row['deductCost'] = ($getAddDeduct->advp_deduct + $getAddDeduct->cg_deduct + $getAddDeduct->food_deduct + $getAddDeduct->others_deduct);
            $row['deductSalaryAdd'] = $getAddDeduct->salary_add;
            $row['productionBonus'] = $getAddDeduct->bonus_add;
            $row['deductId'] = $getAddDeduct->id;
        }else{
            $row['deductCost'] = 0;
            $row['deductSalaryAdd'] = 0;
            $row['deductId'] = null;
            $row['productionBonus'] = 0;
        }
        return $row;
    }

    public function getEmployeeStampAmount($value='')
    {
        $stamp = 10;
        if($value['ben_cash_amount'] == 0 && $value['as_emp_type_id'] == 3){
            $stamp = 0;
        }
        return ['stamp'=>$stamp];
    }

    public function getEmployeeAttendanceBonus($value='')
    {         
        /*
         *get unit wise bonus rules 
         *if employee joined this month, employee will get bonus 
          only he/she joined at 1
        */ 
        $attBonus = 0;
        if(($value['empdojMonth'] == $value['yearMonth'] && date('d', strtotime($value['as_doj'])) > 1) || $value['partial'] == 1 ){
            $attBonus = 0;
        }else{
            $getBonusRule = AttendanceBonusConfig::
            where('unit_id', $value['as_unit_id'])
            ->first();
            if($getBonusRule != null){
                $lateAllow = $getBonusRule->late_count;
                $leaveAllow = $getBonusRule->leave_count;
                $absentAllow = $getBonusRule->absent_count;
            }else{
                $lateAllow = 3;
                $leaveAllow = 1;
                $absentAllow = 1;
            }
            
            if ($value['lateCount'] <= $lateAllow && $value['leaveCount'] <= $leaveAllow && $value['absentCount'] <= $absentAllow && $value['as_emp_type_id'] == 3) {
                $lastMonth = date('m', strtotime('-1 months', strtotime($value['year'].'-'.$value['month'].'-01')));
                if($lastMonth == '12'){
                    $value['year'] = $value['year'] - 1;
                }
                $getLastMonthSalary = HrMonthlySalary::
                    where('as_id', $value['associate_id'])
                    ->where('month', $lastMonth)
                    ->where('year', $value['year'])
                    ->first();
                if (($getLastMonthSalary != null) && ($getLastMonthSalary->attendance_bonus > 0)) {
                    if(isset($getBonusRule->second_month)) {
                        $attBonus = $getBonusRule->second_month;
                    }
                } else {
                    if(isset($getBonusRule->first_month)) {
                        $attBonus = $getBonusRule->first_month;
                    }
                }
            }
        }
        return ['attBonus'=>$attBonus];
    }

    public function getEmployeeSalaryAdjust($value='')
    {
        $salaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($value['associate_id'], $value['month'], $value['year']);

        $leaveAdjust = 0;
        $incrementAdjust = 0;
        $salaryAdd = 0;
        if($salaryAdjust != null){
            $adj = DB::table('hr_salary_adjust_details')
                ->where('salary_adjust_master_id', $salaryAdjust->id)
                ->get();

            $leaveAdjust = collect($adj)->where('type',1)->sum('amount');
            $incrementAdjust = collect($adj)->where('type',3)->sum('amount');
            $salaryAdd = collect($adj)->where('type',2)->sum('amount');
            
        }

        return [
            'leaveAdjust' => ceil((float) $leaveAdjust),
            'incrementAdjust' => ceil((float) $incrementAdjust),
            'salaryAdd' => ceil((float) $salaryAdd)
        ];
        
    }

    public function getEmployeeSalaryPayable($value='')
    {
        $perDayBasic = $value['ben_basic'] / 30;
        $getAbsentDeduct = (int)($value['absentCount'] * $perDayBasic);
        $getHalfDeduct = (int)($value['halfCount'] * ($perDayBasic / 2));
        $overtime_rate = number_format((($value['ben_basic']/208)*2), 2, ".", "");
        $overtime_rate = ($value['as_ot']==1)?($overtime_rate):0;

        if(($value['empdojMonth'] == $value['yearMonth'] && date('d', strtotime($value['as_doj'])) > 1) || $value['monthDayCount'] > $value['totalDay'] || $value['partial'] == 1){
            $perDayGross   = $value['ben_current_salary']/$value['monthDayCount'];
            $totalGrossPay = ($perDayGross * $value['totalDay']);
            
        }else{
            $totalGrossPay = $value['ben_current_salary'];
        }

        $salaryPayable = $totalGrossPay - ($getAbsentDeduct + $getHalfDeduct + $value['deductCost'] + $value['stamp']);

        $otAmount = ((float)($overtime_rate) * ($value['otCount']));
        
        $totalPayable = ceil((float)($salaryPayable + $otAmount + $value['deductSalaryAdd'] + $value['attBonus'] + $value['productionBonus'] + $value['leaveAdjust'] + $value['salaryAdd'] + $value['incrementAdjust']));
        return [
            'salaryPayable' => $salaryPayable,
            'overtime_rate' => $overtime_rate,
            'totalPayable'  => $totalPayable,
            'absentDeduct'  => $getAbsentDeduct,
            'halfDeduct'    => $getHalfDeduct
        ];
    }

    public function getEmployeeSalaryCashBank($value='')
    {

        $payStatus = 1; // cash pay
        if($value['ben_bank_amount'] > 0 && $value['ben_cash_amount'] > 0){
            $payStatus = 3; // partial pay
        }elseif($value['ben_bank_amount'] > 0){
            $payStatus = 2; // bank pay
        }

        $tds = $value['ben_tds_amount']??0;
        if($payStatus == 1){
            $tds = 0;
            $cashPayable = $value['totalPayable'];
            $bankPayable = 0; 
        }elseif($payStatus == 2){
            $cashPayable = 0;
            $bankPayable = $value['totalPayable'];
        }else{
            if($value['ben_bank_amount'] <= $value['totalPayable']){
                $cashPayable = $value['totalPayable'] - $value['ben_bank_amount'];
                $bankPayable = $value['ben_bank_amount'];
            }else{
                $cashPayable = 0;
                $bankPayable = $value['totalPayable'];
            }
        }

        if($bankPayable > 0 && $tds > 0 && $bankPayable > $tds){
            $bankPayable = $bankPayable - $tds;
        }else{
            $tds = 0;
        }

        return [
            'payStatus' => $payStatus,
            'cashPayable' => $cashPayable,
            'bankPayable' => $bankPayable,
            'tds' => $tds
        ];
    }

    public function slaryStore($value='')
    {
        try {
            HrMonthlySalary::updateOrCreate(
            [
                'as_id' => $value['associate_id'],
                'month' => $value['month'],
                'year' => $value['year']
            ],
            [
                'ot_status' => $value['as_ot'],
                'unit_id' => $value['as_unit_id'],
                'designation_id' => $value['as_designation_id'],
                'sub_section_id' => $value['as_subsection_id'],
                'location_id' => $value['as_location'],
                'pay_type' => ($value['payStatus'] != 1?$value['bank_name']:''),
                'gross' => $value['ben_current_salary'],
                'basic' => $value['ben_basic'],
                'house' => $value['ben_house_rent'],
                'medical' => $value['ben_medical'],
                'transport' => $value['ben_transport'],
                'food' => $value['ben_food'],
                'late_count' => $value['lateCount'],
                'present' => $value['presentCount'],
                'holiday' => $value['holidayCount'],
                'absent' => $value['absentCount'],
                'leave' => $value['leaveCount'],
                'absent_deduct' => $value['absentDeduct'],
                'half_day_deduct' => $value['halfDeduct'],
                'salary_add_deduct_id' => $value['deductId'],
                'salary_payable' => $value['salaryPayable'],
                'ot_rate' => $value['overtime_rate'],
                'ot_hour' => $value['otCount'],
                'attendance_bonus' => $value['attBonus'],
                'production_bonus' => $value['productionBonus'],
                'leave_adjust' => $value['leaveAdjust'],
                'stamp' => $value['stamp'],
                'pay_status' => $value['payStatus'],
                'emp_status' => $value['as_status'],
                'total_payable' => $value['totalPayable'],
                'cash_payable' => $value['cashPayable'],
                'bank_payable' => $value['bankPayable'],
                'tds' => $value['tds'],
                'roaster_status' => $value['shift_roaster_status']
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}