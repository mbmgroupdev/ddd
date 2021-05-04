<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Exports\Hr\BonusExport;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Models\Hr\BonusRule;
use App\Repository\Hr\BonusRepository;
use App\Repository\Hr\EmployeeRepository;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BonusSheetController extends Controller
{
    protected $bonus;
    protected $employee;
    public function __construct(BonusRepository $bonus, EmployeeRepository $employee)
    {
        ini_set('zlib.output_compression', 1);
        $this->bonus = $bonus;
        $this->employee = $employee;
    }

    public function index()
    {
        // $data['bonusSheet'] = collect(BonusRule::getApprovalGroupBonusList())->pluck('text', 'id');
        $data['bonusSheet'] = DB::table('hr_bonus_rule AS r')
                    ->select('r.id', DB::raw('CONCAT_WS(" - ",hr_unit_short_name, bonus_type_name, bonus_year) AS text'))
                    ->join('hr_bonus_type AS b', 'r.bonus_type_id', 'b.id')
                    ->join('hr_unit AS u', 'r.unit_id', 'u.hr_unit_id')
                    ->whereIn('r.unit_id', auth()->user()->unit_permissions())
                    ->where('r.status', 1)
                    ->pluck('text', 'id');
        $data['unitList'] = collect(unit_by_id())->pluck('hr_unit_name', 'hr_unit_id');
        $data['locationList'] = collect(location_by_id())->pluck('hr_location_name', 'hr_location_id');
        $data['areaList'] = collect(area_by_id())->pluck('hr_area_name', 'hr_area_id');
        $data['salaryMin'] = 0;
        $data['salaryMax'] = Benefits::getSalaryRangeMax();
        return view('hr.reports.bonus.index', $data);
    }

    public function report(Request $request)
    {
        try {
            $getBonus = $this->bonus->getBonusBySheet($request);
            if(count($getBonus) > 0){
                $asIds = collect($getBonus)->pluck('associate_id');
                $getEmployee = collect($this->employee->getEmployeeByAssociateId($asIds, ['associate_id', 'as_name', 'as_line_id', 'as_floor_id', 'as_designation_id', 'as_doj', 'as_oracle_code']))->keyBy('associate_id');
                $dataRow = $this->bonus->getBonusMergeByEmployee($getBonus, $getEmployee);
                $getBonus = $this->employee->getEmployeeByFilter($request, $dataRow);
            }
            $result = $this->bonus->getBonusReport($request, $getBonus);
            if(isset($request->export)){
                $filename = 'Bonus Report - ';
                $filename .= '.xlsx';
                return Excel::download(new BonusExport($result, 'report'), $filename);
            }
            return view('hr.reports.bonus.report', $result)->render();
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }

    public function audit(Request $request)
    {
        $input = $request->all();
        // return $input;
        $data['type'] = 'error';
        try {
            if($input['status'] == 1){
                $bonusRule = BonusRule::findOrFail($input['id']);
                $bonusRule->update([
                    'approved_at' => date('Y-m-d H:i:s'),
                    'approved_by' => auth()->user()->id
                ]);
            }

            $data['type'] = 'success';
            $data['message'] = 'Process Successfully Done';
            $data['url'] = url('hr/payroll/bonus-sheet-process');
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            $data['message'] = $e->getMessage();
            return $data;
        }
    }
}
