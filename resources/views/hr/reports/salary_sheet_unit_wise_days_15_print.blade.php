@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
    <style>
        .progress[data-percent]:after {
            color: #000 !important;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Operations</a>
                </li>
                <li class="active"> Salary Sheet day wise</li>
            </ul>
            <!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <!-- <div id="load"></div>
                <!-- <div class="page-header">
                    <h1>Reports<small><i class="ace-icon fa fa-angle-double-right"></i>  Salary Sheet Day Wise</small></h1>
                </div> -->
                <!-- <div class="row"> -->
                    <!-- Display Erro/Success Message -->
                    <!-- @include('inc/message')
                    <div id="selectOne"></div>
                    <div class="col-sm-12" id="search_area">
                        <form role="form" method="get" action="{{ url('hr/reports/save_salary_sheet') }}" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="col-sm-12">
                              <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="unit"> Unit <span class="text-red" style="vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-6">
                                        {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'This field is required']) }}
                                    </div>
                                </div>
                              </div> -->
                            <!-- <div class="col-sm-4">
                              <div class="form-group">
                                  <label class="col-sm-4 control-label no-padding-right" for="day_from"> Salary From <span class="text-red" style="vertical-align: top;">&#42;</span></label>
                                  <div class="col-sm-6">
                                    <?php
                                        $days = [];
                                         $cdate = date('Y-m-d');
                                         $cdatearray = explode('-',$cdate);
                                         for ($i=1; $i <=$cdatearray[2] ; $i++) {
                                           $days[date('Y-m-').$i]=date('Y-m-').$i;
                                         }
                                       ?>
                                      {{ Form::select('day_from', $days, null, ['placeholder'=>'Select Date', 'id'=>'day_from', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'This field is required','class' => 'day_from']) }}
                                  </div>
                              </div> -->
                            <!-- </div>
                              <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-5 control-label no-padding-right" for="day_to"> Salary To <span class="text-red" style="vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-6">
                                      <?php
                                          $days = [];
                                           $cdate = date('Y-m-d');
                                           $cdatearray = explode('-',$cdate);
                                           for ($i=1; $i <=$cdatearray[2] ; $i++) {
                                             $days[date('Y-m-').$i]=date('Y-m-').$i;
                                           }
                                         ?>
                                        {{ Form::select('day_to', $days, null, ['placeholder'=>'Select Date', 'id'=>'day_to', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'This field is required', 'class' => 'day_to']) }}
                                    </div>
                                </div>
                              </div> -->

                            <!-- </div>
                            <div class="col-sm-12">
                              <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="day_from"> OT From </label>
                                    <div class="col-sm-6">
                                      <?php
                                          $days = [];
                                           $cdate = date('Y-m-d');
                                           $cdatearray = explode('-',$cdate);
                                           for ($i=1; $i <=$cdatearray[2] ; $i++) {
                                             $days[date('Y-m-').$i]=date('Y-m-').$i;
                                           }
                                         ?>
                                        {{ Form::select('ot_from', $days, null, ['placeholder'=>'Select Date', 'id'=>'day_from', 'style'=>'width:100%;']) }}
                                    </div>
                                </div> -->
                              <!-- </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                      <label class="col-sm-4 control-label no-padding-right" for="day_to"> OT To </label>
                                      <div class="col-sm-6">
                                        <?php
                                            $days = [];
                                             $cdate = date('Y-m-d');
                                             $cdatearray = explode('-',$cdate);
                                             for ($i=1; $i <=$cdatearray[2] ; $i++) {
                                               $days[date('Y-m-').$i]=date('Y-m-').$i;
                                             }
                                           ?>
                                          {{ Form::select('ot_to', $days, null, ['placeholder'=>'Select Date', 'id'=>'day_to', 'style'=>'width:100%;','class' => 'day_to']) }}
                                      </div>
                                  </div>
                                </div> -->

                              <!-- <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-5 control-label no-padding-right" for="disbursed_date"> Disbursed Date <span class="text-red" style="vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-6">
                                      <?php
                                          $days = [];
                                           $cdate = date('Y-m-d');
                                           $cdatearray = explode('-',$cdate);
                                           for ($i=1; $i <=$cdatearray[2] ; $i++) {
                                             $days[date('Y-m-').$i]=date('Y-m-').$i;
                                           }
                                         ?>
                                        {{ Form::select('disbursed_date', $days, null, ['placeholder'=>'Select Disbursed Date', 'id'=>'disbursed_date', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'This field is required']) }} -->

                                        <!-- <input type="text" name="disbursed_date" id="disbursed_date" class="form-control datepicker" data-validation="required" data-validation-format="yyyy-mm-dd" autocomplete="off" placeholder="Y-m-d" value="{{ Request::get('disbursed_date') }}" /> -->
                                    <!-- </div>
                                </div>
                              </div>
                            </div> -->

                               <!-- <div class="form-group text-center">
                                   <div class="row text-center">
                                     <div class="col-sm-2">
                                       <br><br>
                                         <button type="submit" class="btn btn-primary btn-sm">
                                             <i class="fa fa-search"></i> Generate
                                         </button>
                                     </div>
                                     <div class="col-sm-2">
                                       <br><br>
                                         <button type="submit" class="btn btn-primary btn-sm" name="save" value="save">
                                             <i class="fa fa-search"></i> Save
                                         </button>
                                     </div>
                                   </div>

                               </div>

                        </form>
                    </div>
                </div> -->
                <!-- <div class="progress progress-striped pos-rel"  data-percent="0%" style="display: none">
                    <div class="progress-bar" id="progress_bar" style="width:0%;"></div>
                </div> -->
                <!-- <div class="row text-center" id="loader"></div> -->
                <!-- ////////////////////////////////////////////////////////////////////// -->

                @php
                    // function ifIsset($value){
                    //     $value = FALSE;
                    //     if(isset($value)) {
                    //         return $value;
                    //     }
                    //     return $value;
                    // }
                    // get total hour with minutes calculation
                    // function getTotalHour($totalHour){
                    //     if (strpos($totalHour, ':') !== false) {
                    //         list($hour,$minutes) = array_pad(explode(':',$totalHour),2,NULL);
                    //         $minuteHour = 0;
                    //         if($minutes!==NULL) {
                    //             $minuteHour = number_format((float)($minutes/60), 3, '.', '');;
                    //             $totalHour = $hour + $minuteHour;
                    //         }
                    //     }
                    //     return $totalHour;
                    // }
                @endphp
                @if(isset($resultList['local']))

                <div class="panel panel-info">
                    <div class="panel-heading"></div>
                    <div class="panel-heading" id="salary-sheet-result-inner">Salary Sheet Day Wise Result  &nbsp;<button rel='tooltip' data-tooltip-location='left' data-tooltip='Salary Sheet Day Wise Result Print' type="button" onClick="printMe1('result-show')" class="btn btn-primary btn-xs text-right"><i class="fa fa-print"></i> Print</button></div>

                    <div class="panel-body" id="result-shows">

                        <table class="table"  style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                            <tr>
                                <td style="width:14%">
                                    <p style="margin:0;padding:4px 0"><strong>তারিখঃ {{$resultList['global']['dateDate']}}</strong>
                                    </p>
                                    <p style="margin:0;padding:4px 0"><strong>&nbsp;সময়ঃ {{$resultList['global']['dateTime']}}</strong>
                                    </p>
                                </td>
                                <td style="width:15%;font-size:10px">
                                    <p style="margin:0;padding:4px 0"><strong>&nbsp;প্রদান তারিখঃ {{ $resultList['global']['disbursed_date']}}</strong>
                                    </p>
                                </td>
                                <td>
                                    <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:18px;">
                                      {{ $resultList['global']['unit']}}
                                    </h3>
                                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:14px;">বেতন/মজুরি এবং অতিরিক্ত সময়ের মজুরী
                            <br/>
                            <br>
                          তারিখঃ{{ $resultList['global']['start_date']}} থেকে {{ $resultList['global']['end_date']}}</h5>
                                </td>
                                <td style="width:22%">
                                    <p style="margin:0;padding:4px 0;">
                                        <strong>ফ্লোর নংঃ
                                        </strong>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <table class="table" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen;" cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead>
                                <tr style="color:hotpink">
                                    <th style="color:lightseagreen">ক্রমিক নং</th>
                                    <th width="180">কর্মী/কর্মচারীদের নাম
                                        <br/> ও যোগদানের তারিখ</th>
                                    <th>আই ডি নং</th>
                                    <th>মাসিক বেতন/মজুরি</th>
                                    <th width="140">হাজিরা দিবস</th>
                                    <th width="220">বেতন হইতে কর্তন </th>
                                    <th width="250">মোট দেয় টাকার পরিমান</th>
                                    <th>সর্বমোট টাকার পরিমান</th>
                                    <th width="80">দস্তখত</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if(count($resultList['local']) == 0)
                                    <tr>
                                        <td colspan='9'> <b><h5 class="text-center"> No data found !</h5></b></td>
                                    </tr>
                                @endif
                                @php
                                    $i = 0;
                                    $k =1;
                                @endphp
                                @foreach($resultList['local'] as $list)

                                @php
                                //dd($list);exit;
                                // get total hour with minutes calculation
                                if (strpos($list['ot_hour'], ':') !== false) {
                                    list($hour,$minutes) = array_pad(explode(':',$list['ot_hour']),2,NULL);
                                    $minuteHour = 0;
                                    if($minutes!==NULL) {
                                        $minuteHour = number_format((float)($minutes/60), 3, '.', '');;
                                        $list['ot_hour'] = $hour + $minuteHour;
                                    }
                                }
                                // get designation
                                if(isset($list['designation'])){

                                    $designation =$list['designation'];
                                } else {
                                    $designation = new stdClass();
                                }
                                @endphp
                                <?php //dump($list->ot_overtime_minutes);?>
                                <tr>
                                    <td>{{ isset($list['no'])?$list['no']:$k }}</td>
                                    <td>
                                        <p style="margin:0;padding:0;color:blueviolet">{{ $list['name'] }}</p>
                                        <p style="margin:0;padding:0;">{{ $list['doj'] }}</p>
                                        <p style="margin:0;padding:0;">{{ isset($list['designation_grade'])?$list['designation_grade']:'' }}</p>
                                        <p style="margin:0;padding:0;color:hotpink">মূল+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                                        <p style="margin:0;padding:0;">
                                            {{ $list['basic'].'+'.$list['house'].'+'.$list['medical'].'+'.$list['transport'].'+'.$list['food'] }}
                                        </p>
                                    </td>
                                    <td>
                                        <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                                            {{ $list['as_id'] }}
                                        </p>
                                        <p style="margin:0;padding:0;color:hotpink">
                                            বিলম্ব উপস্থিতিঃ {{ $list['late_count'] }}
                                        </p>
                                        <p style="margin:0;padding:0">গ্রেডঃ {{ isset($list['designation_grade'])?$list['designation_grade']:'' }}</p>
                                    </td>
                                    <td>
                                        <p style="margin:0;padding:0">
                                            {{ $list['gross'] }}
                                        </p>
                                    </td>
                                    <td>
                                        <p style="margin:0;padding:0">
                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত দিবস
                                            </span>
                                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                            </span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                <font style="color:hotpink;" > {{ $list['present']}}</font>
                                            </span>

                                        </p>
                                        <p style="margin:0;padding:0">
                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">সরকারি ছুটি </span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                <font style="color:hotpink"> {{$list['holiday']}}</font>
                                            </span>
                                        </p>
                                        <p style="margin:0;padding:0k">
                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিত দিবস </span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                  <font style="color:hotpink"> {{ $list['absent']}}</font>
                                            </span>
                                        </p>
                                        <p style="margin:0;padding:0">
                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি মঞ্জুর </span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ $list['leave'] }}</font>
                                            </span>

                                        </p>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ ($list['present'] + $list['holiday'] + $list['leave'])}}</font>
                                            </span>
                                        </p>
                                    </td>
                                    <td>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিতির জন্য</span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{  $list['absent_deduct'] }}</font>
                                            </span>
                                        </p>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অর্ধ দিবসের জন্য কর্তন </span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                <font style="color:hotpink">{{$list['half_day_deduct'] }}</font>
                                            </span>
                                        </p>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অগ্রিম গ্রহণ বাবদ </span>
                                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                            </span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                              <font style="color:hotpink">
                                                {{$list['salary_advance_adjust'] }}
                                              </font>
                                            </span>




                                        </p>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">স্ট্যাম্প বাবদ </span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                <font style="color:hotpink"> 10.00</font>
                                            </span>
                                        </p>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ভোগ্যপণ্য ক্রয় </span>
                                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                            </span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                              <font style="color:hotpink">
                                                {{$list['salary_product'] }}
                                              </font>
                                            </span>
                                        </p>
                                        <p style="margin:0;padding:0">
                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">খাবার বাবদ কর্তন </span>
                                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                            </span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                              <font style="color:hotpink">
                                                {{$list['food'] }}
                                              </font>
                                            </span>
                                        </p>
                                        <p style="margin:0;padding:0">
                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অন্যান্য </span>
                                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                            </span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                              <font style="color:hotpink">
                                                {{$list['salary_others'] }}
                                            </font>
                                            </span>

                                        </p>
                                        <!-- <p style="margin:0;padding:0">
                                            খাবার বাবদ কর্তন &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">=
                                            </font>
                                        </p>
                                        <p style="margin:0;padding:0">
                                            অন্যান্য &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">=
                                            </font>

                                        </p> -->
                                    </td>
                                    <td>
                                        <p style="margin:0;padding:0">

                                              <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মঞ্জুরি </span>
                                              <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                              <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                    <font style="color:hotpink"> {{ $list['salary_payable']}}</font>
                                             </span>
                                        </p>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত সময়ের কাজের মঞ্জুরি </span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                <font style="color:hotpink">{{ ($list['ot_rate'] * $list['ot_hour'])}}</font>
                                            </span>
                                        </p>
                                        <p style="margin:0;padding:0">


                                                 <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত কাজের মঞ্জুরি হার </span>
                                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                    <font style="color:hotpink">{{ $list['ot_rate'] }} </font>
                                                </span>
                                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                    <font style="color:hotpink"> ({{ $list['ot_hour'] }}  ঘন্টা)</font>
                                                </span>

                                            <?php
                                                // show emploee holiday ot hours
                                                //if(isset($list->holiday_ot_minutes)) {
                                                 //   if($list->holiday_ot_minutes != 0) {
                                                  //      $holiday_ot_hours = 0;
                                                  //      $holiday_ot_hours = number_format((float)($list->holiday_ot_minutes/60), 2, '.', ''); // minute to float hours
                                                 //       $holiday_ot_hours = sprintf('%02d:%02d', (int) $holiday_ot_hours, fmod($holiday_ot_hours, 1) * 60); // convert float hours to hour:minute
                                                //        echo '('.$holiday_ot_hours.')';
                                               //     }
                                               // }
                                            ?>

                                        </p>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত বোনাস </span>
                                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                    <font style="color:hotpink">{{$list['attendance_bonus'] }}</font>
                                                </span>


                                        </p>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মঞ্জুরি অগ্রিম/সমন্বয়</span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                            </span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                <font style="color:hotpink">
                                                  {{$list['salary_advance_adjust'] }}
                                                </font>
                                            </span>


                                        </p>
                                    </td>
                                    <td>
                                        @php
                                            $ot = ($list['ot_rate'] * $list['ot_hour']);
                                            $salaryAdd = '0.00';
                                            $total = ($list['salary_payable'] + $ot +$list['attendance_bonus']+$list['salary_advance_adjust']+$salaryAdd);
                                        @endphp
                                        {{ $total }}



                                    </td>
                                    <td></td>
                                </tr>
                                <?php $k++; ?>
                              @endforeach

                            </tbody>

                        </table>
                        <!-- <div class="text-center">

                         {!! isset($resultList['global']['links'])?$resultList['global']['links']:'' !!}
                        </div> -->
                    </div>
                </div>

@endif


                <!-- ///////////////////////////////////////////////////////////////////////////// -->
        </div>
        <!-- /.page-content -->
    </div>
</div>

<script type="text/javascript">
    // click done button to hide process div and show search area
    $(document).on('click', '#done_process', function(){
        $('#search_area').show();
        $('.prepend').remove('');
    });

    $(document).ready(function() {

        //date range validation----------------------------------
        $('#day_from').on('change', function(){

            var from    = new Date($(this).val());
            var to      = new Date($('#day_to').val());
            // console.log(from, to);
            if(to<from){
                $(this).find("option:selected").removeAttr("selected");
                alert('From Date is greater than To Date');
            }
        });

        $('#day_to').on('change',function(){
            var from    = new Date($('#day_from').val());
            var to      = new Date($(this).val());
            var d_date  = new Date($('#disbursed_date').val());
            // console.log(from, to);
            if($('#day_from').val() == null || $('#day_from').val() == ''){
                $(this).find("option:selected").removeAttr("selected");
                alert('Please Select From Date First');
            }
            else{
                if(to<from){
                    $(this).find("option:selected").removeAttr("selected");
                    alert('From Date is greater than To Date');
                }
                if(to>d_date){
                    $(this).find("option:selected").removeAttr("selected");
                    alert('To Date is greater than Disburse Date');
                }
            }
        });

        $('#disbursed_date').on('change', function(){
            var d_date  = new Date($(this).val());
            var to_date = new Date($('#day_to').val());

            if($('#day_to').val() == null || $('#day_to').val() == ''){
                $(this).find("option:selected").removeAttr("selected");
                alert('Please select To Date First');
            }
            else{
                if(d_date<to_date){
                    $(this).find("option:selected").removeAttr("selected");
                    alert('To Date is greater than Disburse Date');
                }
            }
        });

        //----------------------------------------------------------

        function ajax_loader_fn(divId) {
            var loaderPath = "{{asset('assets/rubel/img/loader.gif')}}";
            $("#"+divId).html('<div class="loader-cycle text-center"><img src="'+loaderPath+'" /></div>');
            $('html, body').animate({
                scrollTop: $("#"+divId).offset().top
            }, 2000);
        }

        $('#salary_generate').on('click', function(){
            // hide form section
            $('#search_area').hide();
            var unit        = $('#unit').val();
            var start_date  = $('#start_date').val();
            var end_date    = $('#end_date').val();
            var disbursed_date = $('#disbursed_date').val();
            if(unit != '' && start_date != '' && end_date != '' && disbursed_date != '') {
                // remove error message
                $('#selectOne').html('');
                ajax_loader_fn('loader');
                $('#progress_bar_main').before('<h3 class="text-center prepend" id="data_fach_update">Data Fetching</h3>');
                // fetch employee/user list data
                setTimeout(() => {
                    $.ajax({
                        url: '{{ url('hr/reports/save_salary_sheet_unit_wise') }}',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            unit: unit,
                            start_date: start_date,
                            end_date: end_date,
                            disbursed_date: disbursed_date
                        },
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function(res) {
                            if(res != '') {
                                // if list data amount > 100 then it will send data packet wise
                                if(res.employee_count > 100) {
                                    //$('#progress_bar_main').before('<h3 class="text-center prepend">Total Data Found: '+res.employee_count+'</h3>');
                                    var array_count = res.array_count;
                                    var emp_data    = res.employee_list;
                                    // console.log(emp_data);
                                    var percentage  = 0;
                                    for(var i = 0; i < array_count; i++) {
                                        // console.log(i, emp_data[i]);
                                        (function(i){
                                            setTimeout(function(){
                                                $.ajax({
                                                    url: '{{ url('hr/reports/save_salary_sheet_unit_wise_data') }}',
                                                    type: 'POST',
                                                    datatype: 'json',
                                                    data: {
                                                        employeelist: emp_data[i],
                                                        unit: unit,
                                                        start_date: start_date,
                                                        end_date: end_date,
                                                        disbursed_date: disbursed_date
                                                    },
                                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                                    success: function(res) {                                                        percentage = parseInt((i-0)*100/(array_count-1));
                                                        //console.log(i, percentage, emp_data[i]);
                                                        if(i == 0) {
                                                            $('#loader').hide();
                                                            $('#progress_bar_main').show();
                                                            $('#data_fach_update').text('Data Updating');
                                                        }
                                                        if(i == (array_count-1)) {
                                                            percentage = 100;
                                                            $('#progress_bar_main').before('<div class="row text-center prepend"><button class="btn btn-success btn-sm" id="done_process">Click to Done</button></div>');
                                                            $('#progress_bar_main').hide();
                                                        }
                                                        $('#progress_bar').css({width: percentage+'%'});
                                                        $('#progress_bar_main').attr('data-percent', percentage+'%');
                                                    }, error: function($ex) {
                                                        console.log($ex);
                                                    }
                                                });
                                            }, 1000*i);
                                        })(i);
                                    }
                                } else {
                                    // data found 100 or less
                                    $('#progress_bar_main').before('<h3 class="text-center">Total Data Found: '+res.array_count+'</h3>');
                                    console.log('100 or less entity found');
                                }
                            } else {
                                // no data found
                                $('#selectOne').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>No Data Found</div>');
                            }
                        },
                        error: function() {
                            // error occurred
                            $('#selectOne').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Error Occurred</div>');
                        }
                    });
                }, 1000);
            } else {
                // input field validation
                $('#selectOne').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Please select all option</div>');
            }
        });

        // day According to month
        $('#start_date').datetimepicker({
            showClose: true,
            showTodayButton: true,
            dayViewHeaderFormat: "YYYY MMMM",
            format: "YYYY-MM-DD"
        }).on("dp.update", function() {
            $('#end_date').each(function() {
                if ($(this).data('DateTimePicker')) {
                    $(this).data("DateTimePicker").destroy();
                    $(this).val("");
                }
            });
        });

        //end_date
        $("body").on("focusin", '#end_date', function() {
            var startDate = $("#start_date").val();
            if (startDate == "") {
                $("#start_date").val(moment().format("YYYY-MM-DD"));
                var startDate = $("#start_date").val();
            }
            var day = startDate.substring(8, 10);
            var daysInMonth = moment(startDate).daysInMonth();
            var enableDays = daysInMonth - day;
            var lastDay = moment(startDate).add(enableDays, 'days').format("YYYY-MM-DD");
            var firstDay = moment(startDate).format("YYYY-MM-DD");

            $(this).datetimepicker({
                dayViewHeaderFormat: 'MMMM',
                format: "YYYY-MM-DD",
                minDate: firstDay,
                maxDate: lastDay
            });
        });
    });
    // Radio Button Location
    function attLocation(loc) {
        window.location = loc;
    }

    function printMe1(divName)
{
    var url      = window.location.href+'&paginate=false';
    console.log(url);
    //var myWindow=window.open(url);
    // myWindow.document.write('<style>.pagination{display:none;}</style>');
    // myWindow.document.write(document.getElementById(divName).innerHTML);
    // myWindow.document.close();
    // myWindow.focus();
    //myWindow.print();
    //myWindow.close();

    var printWindow = window.open(
    url,
    'Print',
    'left=200',
    'top=200',
    'width=950',
    'height=500',
    'toolbar=0',
    'resizable=0'
);
printWindow.addEventListener('load', function() {
    printWindow.print();
    printWindow.close();
}, true);
}
</script>
@endsection
