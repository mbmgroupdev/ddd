<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hr\Leave;
use Illuminate\Support\Facades\Artisan;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        $att = $this->userAtt();
        $associate_id = auth()->user()->associate_id;
        $leaves = array();
        if($associate_id){
            $leaves= Leave::where('leave_ass_id', $associate_id)
                        ->orderBy('id', 'DESC')
                        ->take(5)
                        ->get();

        }
        
        return view('user.index', compact('att','leaves'));
    }


    public function userAtt()
    {
        $user = auth()->user();
        if($user->employee){
            
            $table = get_att_table($user->employee['as_unit_id']);
            $as_id = $user->employee['as_id'];


            $present  = DB::table($table)
                        ->whereMonth('in_date', date('m'))
                        ->whereYear('in_date',date('Y'))
                        ->where('as_id', $as_id)
                        ->count();

            $late  = DB::table($table)
                        ->whereMonth('in_date', date('m'))
                        ->whereYear('in_date',date('Y'))
                        ->where('as_id', $as_id)
                        ->where('late_status', 1)
                        ->count();
          
            /*----------------Leave------------------*/
            $leave = DB::table('hr_leave')
                     ->where('leave_status', '=', 1)
                     ->count();

            $absent = DB::table('hr_absent')
                       ->whereMonth('date', date('m'))
                       ->where('associate_id', $user->associate_id)
                       ->count();
        }

        $chartdata=[
            'present' => $present??0,
            'late' => $late??0,
            'leave' => $leave??0,
            'absent' => $absent??0
        ];

        return $chartdata;
    }
    
    public function login()
    {
        return view('login');
    }


    public function clear()
    {
        $exitCode = Artisan::call('config:clear');
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('route:clear');
        $exitCode = Artisan::call('config:cache');
        $exitCode = Artisan::call('route:cache');
        $exitCode = Artisan::call('clear-compiled');
        $exitCode = Artisan::call('optimize');
        return 'DONE'; //Return anything
    }
}
