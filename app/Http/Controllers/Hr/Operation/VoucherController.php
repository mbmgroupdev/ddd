<?php
namespace App\Http\Controllers\Hr\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\User;
use App\Models\Hr\Voucher;
use Carbon\Carbon;
use DB, ACL,stdClass, PDF, Auth, Calendar;


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
}