<?php
namespace App\Http\Controllers\Hr\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\User;
use App\Models\Hr\Voucher;
use App\Models\Hr\EmpType;
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\Line;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use DB, ACL,stdClass, PDF, Auth;


class VoucherController extends Controller
{
    public function index()
    {
    	return view('hr.operation.voucher.index');
    }

    public function voucher(Request $request)
    {
		$employee = get_employee_by_id($request->associate);

		$voucher = new Voucher();
		$voucher->associate_id = $request->associate;
		$voucher->type = $request->type;
		$voucher->amount = $request->amount;
		$voucher->description = $request->description;
		$voucher->manager_id = $request->manager;
		$voucher->status = 0;
		$voucher->created_by = auth()->id();
		$voucher->save();

		$view =  view('hr.operation.voucher.voucher', compact('voucher','employee'))->render();

		return response(['view' => $view]);
    }


    public function productionBonus()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name', 'hr_unit_id');
        $areaList = Area::where('hr_area_status',1)->pluck('hr_area_name','hr_area_id');

    	return view('hr.operation.production_bonus',compact('unitList','areaList'));
    }


    public function storeProductionBonus(Request $request)
    {
        if(empty($request->assigned)){
            return back()->with('error', 'Select at least One Employee');
        }
        else{
            $list_emp = '';
            $monthYear = explode("-", $request->month);
            foreach ($request->assigned as $as_id => $associate_id) {
                $pb = SalaryAddDeduct::firstOrNew(
                    ['associate_id' => $associate_id, 'year' => $monthYear[0], 'month' => $monthYear[1]],
                    ['bonus_add' => $request->amount ]
                );
                $pb->save();
                $list_emp .= $associate_id.', ';
            }
            log_file_write('Production bonus added for ', $list_emp);
            return back()->with('success', 'Production bonus saved');
          
        }
    }

    public function productionList()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name', 'hr_unit_id');
        return view('hr.operation.production_bonus_list', compact('unitList'));
    }


    public function productionListData()
    {
        $data = DB::table('hr_salary_add_deduct AS ad')
            ->select([
                'ad.*',
                'b.as_name',
                'u.hr_unit_short_name',
                'f.hr_floor_name',
                'l.hr_line_name',
                'a.hr_area_name',
                'dp.hr_department_name',
                'dg.hr_designation_name',
                's.hr_section_name',
                'b.as_gender',
                'b.as_ot',
                'b.as_contact',
                'b.as_status',
                'b.as_oracle_code',
                'b.as_rfid_code'
            ])
            ->leftJoin('hr_as_basic_info As b','ad.associate_id','=','b.associate_id')
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_section AS s', 's.hr_section_id', '=', 'b.as_section_id')
            ->where('ad.bonus_add', '>', 0)
            ->get();


        return Datatables::of($data)
            ->editColumn('month', function($data){
                return Carbon::create($data->year,$data->month,1,0)->format('F, Y');
            })
            ->editColumn('action', function ($data) {
                $return = "<a href=".url('hr/recruitment/employee/show/'.$data->associate_id)." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Trash\">
                        <i class=\"ace-icon fa fa-trash bigger-120 fa-fw\"></i>
                    </a>";
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'month',
                'action'
            ])
            ->make(true);
    }
}