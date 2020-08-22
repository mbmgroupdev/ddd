<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Employee;
use App\Models\Hr\AdvaneInfo;
use App\Models\Hr\Ot;
use App\Models\Hr\Unit;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendanceMBM;
use App\Models\Hr\Leave;
use App\Models\Hr\TrainingAssign;
use App\Models\Hr\ShiftRoaster;
use App\Models\Hr\Event;
use App\User;
use App\Helpers\Attendance2;
use DB, ACL,stdClass, PDF, Auth, Calendar;
use DateTime;
use DatePeriod;
use DateInterval;

class AttArchiveController extends Controller
{
    public function index()
    {
    	$data = AttendanceMBM::whereMonth('in_date', '=', '11')->groupBy('as_id')->get();
    	dd($data[0]);
    }
}