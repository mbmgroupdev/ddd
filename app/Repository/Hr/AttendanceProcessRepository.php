<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\AttendanceProcessInterface;
use DB;
use Illuminate\Support\Collection;

class AttendanceProcessRepository implements AttendanceProcessInterface
{
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function attDataChecking($input)
    {
        $getData = $this->unitWiseDataProcess($input);
        $data = collect($input['getData'])->map(function($q) {
            $p = (object)[];

        });
    }
	
    protected function unitWiseDataProcess($input)
    {
        $unit = $input['unit'];
        return collect($input['getData'])->map(function($q) use ($unit){
            $lineData = $q;
            if(!empty($lineData) && (strlen($lineData)>1)){
                return;
            }

            if(($unit==1 || $unit==4 || $unit==5)){
                $sl = substr($lineData, 0, 2);
                $date   = substr($lineData, 3, 8);
                $time   = substr($lineData, 12, 6);
                $rfid = substr($lineData, 19, 10);
                $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
            }else if($unit==2){
                $sl = substr($lineData, 0, 2);
                $date   = substr($lineData, 2, 8);
                $rfid = substr($lineData, 16, 10);
                $time   = substr($lineData, 10, 6);
                $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
            }else if($unit==8  &&  !empty($lineData) && (strlen($lineData)>1)){
                $lineData = explode(" ", $lineData);
                if(isset($lineData[2])){
                    $rfid = $lineData[0];
                    $date = $lineData[1];
                    $time = $lineData[2];
                    $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                }else{
                    $msg[] = " Punch Problem!";
                    return;
                }
                
            }
            else if($unit==3  &&  !empty($lineData) && (strlen($lineData)>1)){
                if($input['device'] == 1){
                    $sl = substr($lineData, 0, 2);
                    $date   = substr($lineData, 2, 8);
                    $rfid = substr($lineData, 16, 10);
                    $time   = substr($lineData, 10, 6);
                    $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                }elseif($input['device'] == 2){
                    $rfid = '0'; // only unit 3 device automation
                    $lineData = preg_split("/[\t]/", $lineData);
                    $asId = $lineData[0];
                    $checktime = explode(" ", $lineData[1]);
                    $date = $checktime[0];
                    $time = $checktime[1];
                    $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                }else{
                    $msg[] = $value." - AQL device mismatch.";
                    return false;
                }
            }
            else if($unit==1001){
                $lineData = preg_replace('/\s+/', ' ', $lineData);
                $valueExloade = explode(',', $lineData);
                $dateExp = explode('/', $valueExloade[1]);
                if(count($dateExp) > 1 && count($valueExloade) > 2){
                    $dateTimeFormat = $dateExp[2].'-'.$dateExp[1].'-'.$dateExp[0].' '.$valueExloade[2];
                    $date  =  date("Y-m-d H:i:s", strtotime(str_replace("/", "-", $dateTimeFormat)));
                    $rfidNameExloade = explode('-', $valueExloade[4]);
                    $rfid = $rfidNameExloade[0];
                    $checktime = (!empty($date)?date("Y-m-d H:i:s", strtotime($date)):null);
                }
                
            }else{
                if($value != null){
                    $msg[] = $value." - Unit do not match, issue data ";
                    return false;
                }
            }
        });
    }
}