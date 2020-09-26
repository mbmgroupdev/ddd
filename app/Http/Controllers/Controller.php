<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\UserLog;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    # Write Every Events in Log File
    public function logFileWrite($message, $event_id){
        $log_message = date("Y-m-d H:i:s")." \"".Auth()->user()->associate_id."\" ".$message." ".$event_id.PHP_EOL;
        $log_message .= file_get_contents("assets/log.txt");
        file_put_contents("assets/log.txt", $log_message);

        $associate_id=Auth()->user()->associate_id;
        $logs=UserLog::where('log_as_id',$associate_id)->orderBy('updated_at','ASC')->get();
        if(count($logs)<3){
            $user_log= new UserLog;
        }else{
            $user_log =$logs->first();
            $user_log->id = $logs->first()->id;
        }
            $user_log->log_as_id = $associate_id;
            $user_log->log_message = $message;
            $user_log->log_table = '';
            $user_log->log_row_no = $event_id;
            $user_log->save();

    }

    // write every events in log file process queue procedu
    public function logFileWriteJobs($message, $event_id)
    {
        $filePath = url('/assets\log.txt');
    	$job = (new ProcessLogFile(auth()->user()->associate_id, $message, $event_id, $filePath))
        ->delay(Carbon::now()->addSeconds(10));
        dispatch($job);
    }

    public function quoteReplaceHtmlEntry($data)
    {
        if(strpos($data, "'") !== FALSE){
          return str_replace("'", "&#39;", $data);
        }elseif(strpos($data, '"') !== FALSE){
          return str_replace('"', "&#34;", $data);
        }else{
          return $data;
        }


    }

    public function getTableNameUnit($unit){
      if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
          $tableName="hr_attendance_mbm AS a";
      }else if($unit ==2){
          $tableName="hr_attendance_ceil AS a";
      }else if($unit ==3){
          $tableName="hr_attendance_aql AS a";
      }else if($unit ==6){
          $tableName="hr_attendance_ho AS a";
      }else if($unit ==8){
          $tableName="hr_attendance_cew AS a";
      }else{
          $tableName="hr_attendance_mbm AS a";
      }

      return $tableName;

  }

  public function otbuffer($minutes){

    if($minutes >= 15 && $minutes < 45) $minutes = 30;
    else if($minutes >= 45) $minutes = 60;
    else $minutes = 0;
    return $minutes;
  }
}
