<?php
namespace App\Http\Controllers\Hr\TimeAttendance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessMonthlySalary;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;

use Carbon\Carbon;
use DB ,DataTables;

class FridayShiftController extends Controller
{
	protected $shift;

	public function __construct()
	{
		$this->shift = collect(shift_by_code())
			->where('hr_shift_name','Friday OT')
			->first();
	}
	public function otUpdate(Request $request)
	{
		return $request->all();
	}
}