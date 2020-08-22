<style>
    @media print {
      #unit-info{
        display:none;
      }
        .pagebreak {
            page-break-before: always !important;
        }
    }
</style>
<div id="unit-info">
<h2 style="margin:4px 10px;text-align:center;">
    {{$pageHead->unit_name}}
</h2>
@if(isset($info) && count($info)>1)
    <h5 style="margin:4px 10px;text-align:center;">
        @if(isset($info['floor']))
            <span style="color:lightseagreen;">ফ্লোর:</span> {{$info['floor']}}
        @endif
        @if(isset($info['area']))
            <span style="color:lightseagreen;" class="f17">এরিয়া:</span> {{$info['area']}}
        @endif
        @if(isset($info['department']))
            <span style="color:lightseagreen;" class="f17">ডিপার্টমেন্ট:</span> {{$info['department']}}
        @endif
        @if(isset($info['section']))
            <span style="color:lightseagreen;">সেকশন:</span> {{$info['section']}}
        @endif
        @if(isset($info['sub_sec']))
            <span style="color:lightseagreen;">সাব-সেকশন:</span> {{$info['sub_sec']}}
        @endif
    </h5>
@endif

<h3 style="margin:4px 10px;text-align:center;">
    বেতন/মজুরি এবং অতিরিক্ত সময়ের মজুরী<br/>
    তারিখঃ {{ Custom::engToBnConvert($pageHead->for_date) }}
</h3>
@php
    $loc_count = 0;
    $total_emp = 0;
    $total_sal = 0;
@endphp

<h6 style="margin:4px 10px;text-align:center;font-weight:600;font-size:13px;">
    সর্বমোট টাকার পরিমানঃ
    <span style="color:hotpink;font-size:15px;" id="total-salary"></span><br/>
    মোট কর্মী/কর্মচারীঃ
    <span style="color:hotpink;font-size:15px;" id="emp-count"></span>
</h6>

@if(count($getSalaryList) == 0)
    <b><h5 class="text-center"> No data found !</h5></b>
@endif
</div>
    @foreach($uniqueLocation as $locKey=>$location)
        @php
            $pageKey = 0;
            $totalSalary_s = 0;
        @endphp
        @foreach($locationDataSet as $key=>$lists)
            @php
                $pageKey += 1;
                $asLocationList = array_column($lists,'as_location');
                $loc_emp = 0;
                $loc_sal = 0;
                $getLocation = Custom::locationNameBangla($location);
                $loc_count++;
                $totalSalary_s = 0;
                $emp = 0;
                $ot_payable = 0; 
                foreach($lists as $tSalary) {
                    if($tSalary['as_location'] == $location && $tSalary['salary'] != null){

                        $salaryAdd_s = (($tSalary['salary']['add_deduct'] == null) ? '0.00' : $tSalary['salary']['add_deduct']['salary_add']);
                        $ot_s = round((float)($tSalary['salary']['ot_rate']) * $tSalary['salary']['ot_hour']);
                        $leaveAdjust_s = Custom::salaryLeaveAdjustAsIdMonthYearWise($tSalary['associate_id'], $tSalary['salary']['month'], $tSalary['salary']['year']);
                        $totalSalary_s += ($tSalary['salary']['total_payable']);
                        $emp++;
                        $ot_payable += round((float)($tSalary['salary']['ot_rate']) * $tSalary['salary']['ot_hour']);; 
                    }
                }
            @endphp
            @if(in_array($location, $asLocationList) && $emp > 0)
                <div class="panel panel-info">
                    <div class="panel-heading">ইউনিট :<b> {{ $pageHead->unit_name }}</b> - লোকেশন :<b> {{ $getLocation }}</b></div>
                    <div class="panel-body">

                        <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                            <tr>
                                <td style="width:14%">
                                    <p style="margin:0;padding:4px 0"><strong>তারিখঃ </strong>
                                        {{ Custom::engToBnConvert($pageHead->current_date) }}
                                    </p>
                                    <p style="margin:0;padding:4px 0"><strong>&nbsp;সময়ঃ </strong>
                                        {{ Custom::engToBnConvert($pageHead->current_time) }}
                                    </p>
                                    <p style="margin:0;padding:4px 0"><strong>&nbsp;পৃষ্ঠা নংঃ </strong>
                                        {{ Custom::engToBnConvert($pageKey) }}
                                    </p>
                                </td>
                                <td style="width:15%;font-size:10px">
                                    @if($pageHead->pay_date != null)
                                    <p style="margin:0;padding:4px 0"><strong>&nbsp;প্রদান তারিখঃ </strong>
                                        {{ Custom::engToBnConvert($pageHead->pay_date) }} ইং
                                    </p>
                                    @endif
                                </td>
                                <td>
                                    <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:18px;">
                                        {{ $pageHead->unit_name }}
                                    </h3>
                                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:14px;">বেতন/মজুরি এবং অতিরিক্ত সময়ের মজুরী
                            <br/>
                            তারিখঃ {{ Custom::engToBnConvert($pageHead->for_date) }}</h5>
                                </td>
                                <td width="0%"> &nbsp;</td>
                                <td style="width:30%" style="text-align: right;">
                                    @if($pageHead->floor_name != null)
                                    <p style="margin:0;padding:4px 0;">
                                        <strong>ফ্লোর নংঃ
                                            {{ Custom::engToBnConvert($pageHead->floor_name) }}
                                        </strong>
                                    </p>
                                    @endif
                                    <p style="margin:0;padding:4px 0;text-align: right;">
                                        সর্বমোট টাকার পরিমানঃ <span style="color:hotpink" >{{Custom::engToBnConvert($totalSalary_s)}}</span>
                                    </p>
                                    @php
                                        $list_total_ot = array_column(array_column($lists, 'salary'),'ot_rate');
                                    @endphp
                                    <p style="margin:0;padding:4px 0;text-align: right;">
                                        মোট কর্মী/কর্মচারীঃ <span style="color:hotpink" >{{Custom::engToBnConvert($emp)}}</span>
                                    </p>
                                    <p style="margin:0;padding:4px 0;text-align: right;">
                                        স্ট্যাম্প বাবদঃ <span style="color:hotpink" >{{Custom::engToBnConvert($emp*10)}}</span> | অতিরিক্ত কাজের মজুরীঃ <span style="color:hotpink" id="">{{Custom::engToBnConvert($ot_payable)}}</span>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <table class="table" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen" cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead>
                                <tr style="color:hotpink">
                                    <th style="color:lightseagreen">ক্রমিক নং</th>
                                    <th width="180">কর্মী/কর্মচারীদের নাম
                                        <br/> ও যোগদানের তারিখ</th>
                                    <th>আই ডি নং</th>
                                    <th>মাসিক বেতন/মজুরী</th>
                                    <th width="140">হাজিরা দিবস</th>
                                    <th width="220">বেতন হইতে কর্তন </th>
                                    <th width="250">মোট দেয় টাকার পরিমান</th>
                                    <th>সর্বমোট টাকার পরিমান</th>
                                    <th width="80">দস্তখত</th>
                                    <th width="80">বিতরণ</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach($locationDataSet as $pageKey=>$lists) --}}
                                <?php $j=1;?>
                                @foreach($lists as $k=>$list)
                                    @if($list['as_location'] == $location && $list['salary'] != null)
                                        <tr>
                                            <td>{{ $j }}</td>
                                            <td>
                                                <p style="margin:0;padding:0;">{{ $list['employee_bengali']['hr_bn_associate_name'] }}</p>
                                                <p style="margin:0;padding:0;">{{ Custom::engToBnConvert($list['as_doj']) }}</p>
                                                <p style="margin:0;padding:0;">{{ isset($list['designation']['hr_designation_name_bn'])? Custom::engToBnConvert($list['designation']['hr_designation_name_bn']):'' }} </p>
                                                <p style="margin:0;padding:0;color:hotpink">মূল+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                                                <p style="margin:0;padding:0;">
                                                    {{ Custom::engToBnConvert($list['salary']['basic'].'+'.$list['salary']['house'].'+'.$list['salary']['medical'].'+'.$list['salary']['transport'].'+'.$list['salary']['food']) }}
                                                </p>
                                            </td>
                                            <td>
                                                <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                                                    {{ $list['associate_id'] }}
                                                </p>
                                                <p style="margin:0;padding:0;color:hotpink">
                                                    বিলম্ব উপস্থিতিঃ {{ Custom::engToBnConvert($list['salary']['late_count']) }}
                                                </p>
                                                <p style="margin:0;padding:0">গ্রেডঃ {{ isset($list['designation']['hr_designation_grade'])? Custom::engToBnConvert($list['designation']['hr_designation_grade']):'' }}</p>
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0">
                                                    {{ Custom::engToBnConvert($list['salary']['gross']) }}
                                                </p>
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত দিবস
                                                    </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink;" > {{ Custom::engToBnConvert($list['salary']['present']) }}</font>
                                                    </span>

                                                </p>
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">সরকারি ছুটি </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink"> {{ Custom::engToBnConvert($list['salary']['holiday']) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0k">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিত দিবস </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                          <font style="color:hotpink"> {{ Custom::engToBnConvert($list['salary']['absent']) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি মঞ্জুর </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($list['salary']['leave']) }}</font>
                                                    </span>

                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($list['salary']['present'] + $list['salary']['holiday'] + $list['salary']['leave'])}}</font>
                                                    </span>
                                                </p>
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিতির জন্য</span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{  Custom::engToBnConvert(round($list['salary']['absent_deduct'])) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অর্ধ দিবসের জন্য কর্তন </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert(round($list['salary']['half_day_deduct'])) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অগ্রিম গ্রহণ বাবদ </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($list['salary']['add_deduct'] == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert($list['salary']['add_deduct']['advp_deduct']) }} </font>
                                                    </span>



                                                  {{--   অগ্রিম গ্রহণ বাবদ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->advp_deduct }} </font> --}}

                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">স্ট্যাম্প বাবদ </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert('10.00') }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ভোগ্যপণ্য ক্রয় </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                                        @if($list['salary']['add_deduct'] == null)
                                                            {{ Custom::engToBnConvert('0.00') }}
                                                        @else
                                                            {{ isset($list['salary']['add_deduct']['cg_product']) ? Custom::engToBnConvert($list['salary']['add_deduct']['cg_product']):Custom::engToBnConvert('0.00') }}
                                                        @endif
                                                    </font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">খাবার বাবদ কর্তন </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                                      {{ ($list['salary']['add_deduct'] == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert($list['salary']['add_deduct']['food_deduct']) }} </font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অন্যান্য </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($list['salary']['add_deduct'] == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert($list['salary']['add_deduct']['others_deduct']) }} </font>
                                                    </span>

                                             {{--        ভোগ্যপণ্য ক্রয় &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : isset($list->add_deduct['cg_product'])?$list->add_deduct['cg_product']:'' }}</font>
                                                </p>
                                                <p style="margin:0;padding:0">
                                                    খাবার বাবদ কর্তন &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->food_deduct }} </font>
                                                </p>
                                                <p style="margin:0;padding:0">
                                                    অন্যান্য &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->others_deduct }} </font> --}}

                                                </p>
                                            </td>

                                            @php
                                                $otHourEx = explode('.', $list['salary']['ot_hour']);
                                                $minute = '00';
                                                if(isset($otHourEx[1])){
                                                    $minute = $otHourEx[1];
                                                    if($minute == 5){
                                                        $minute = 30;
                                                    }
                                                }
                                                $otHour = $otHourEx[0].'.'.$minute;
                                                // $otHour = $list['salary']['ot_hour'];
                                                $ot = round((float)($list['salary']['ot_rate']) * $list['salary']['ot_hour']);
                                                $salaryAdd = ($list['salary']['add_deduct'] == null) ? '0.00' : $list['salary']['add_deduct']['salary_add'];
                                                // $total = ($list->salary_payable + $ot + $list->attendance_bonus + $salaryAdd);
                                            @endphp
                                            <td>
                                                <p style="margin:0;padding:0">

                                                      <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মজুরী </span>
                                                      <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                      <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink"> {{ Custom::engToBnConvert($list['salary']['salary_payable']) }}</font>
                                                     </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত সময়ের কাজের মজুরী </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert($ot) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                         <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত কাজের মজুরী হার </span>
                                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink">{{ Custom::engToBnConvert($list['salary']['ot_rate']) }} </font>
                                                        </span>
                                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink"> ({{ $list['as_ot']==1?Custom::engToBnConvert($otHour):Custom::engToBnConvert('00') }}  ঘন্টা)</font>
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

                                                        <span style="text-align: right;width: 65%; float: right;  white-space: wrap;">&nbsp;
                                                        </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত বোনাস </span>
                                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink">{{ Custom::engToBnConvert($list['salary']['attendance_bonus']) }}</font>
                                                        </span>


                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মজুরী অগ্রিম/সমন্বয়</span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert($salaryAdd) }}</font>
                                                    </span>

                                                   {{--  বেতন/মজুরী অগ্রিম/সমন্বয় &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->salary_add }}</font> --}}

                                                </p>

                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি সমন্বয়</span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert($list['salary']['leave_adjust']) }}</font>
                                                    </span>

                                                </p>
                                            </td>
                                            <td>
                                                @php
                                                    $loc_emp++;
                                                    $totalSalary = ($list['salary']['total_payable']);
                                                    $loc_sal = $loc_sal+$totalSalary;
                                                @endphp
                                                {{ Custom::engToBnConvert(number_format($totalSalary,2)) }}
                                            </td>
                                            <td></td>
                                            <td id="{{ $j }}-{{ $list['associate_id'] }}">
                                                @if($list['salary']['disburse_date'] == null)
                                                    <a data-id="{{ $j }}-{{ $list['associate_id'] }}" class="btn btn-primary btn-sm disbursed_salary" data-eaid="{{ $list['associate_id'] }}" data-date="{{ $pageHead->for_date }}" data-month="{{ $pageHead->month }}" data-year="{{ $pageHead->year }}" data-name="{{ $list['employee_bengali']['hr_bn_associate_name'] }}" data-post="{{ $list['designation']['hr_designation_name_bn'] }}"  rel='tooltip' data-tooltip-location='top' data-tooltip='বেতন প্রদান করুন' ><i class="fa fa-money" aria-hidden="true"></i> হয় নি </a>
                                                @else
                                                    হ্যাঁ 
                                                    <br>
                                                    <b>{{ Custom::engToBnConvert($list['salary']['disburse_date']) }}</b>
                                                @endif
                                            </td>
                                        </tr>
                                        <?php $j++; ?>
                                    @endif
                                @endforeach
                                {{-- @endforeach --}}

                            </tbody>
                        </table>
                        <input type="hidden" class="hidden_loc" data-target="{{$loc_count}}" value="{{ Custom::engToBnConvert(number_format($loc_sal,2)) }}" data-emp="{{ Custom::engToBnConvert($loc_emp)}}">
                        @php
                            $total_sal = $total_sal+$loc_sal;
                            $total_emp += $loc_emp;
                        @endphp

                    </div>
                </div>
                <div class="pagebreak"> </div>
            @endif
        @endforeach
    @endforeach
{{-- modal --}}
<div class="item_details_section">
    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
      <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
        <div class="fade-box-details fade-box">
          <div class="inner_gray clearfix">
            <div class="inner_gray_text text-center" id="heading">
             <h3 class="no_margin">বেতন বিতরণ</h3>   
            </div>
            <div class="inner_gray_close_button">
              <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
            </div>
          </div>

          <div class="inner_body" id="modal-details-content" style="display: none">
            <div class="inner_body_content">
               <input type="hidden" name="id" value="" id="modal-id">
               <input type="hidden" name="associateId" value="" id="modal-associateId">
               <input type="hidden" name="month" value="" id="modal-month">
               <input type="hidden" name="year" value="" id="modal-year">
               <h1 class="text-center" ><strong class="f22" id="disbursed_name"></strong></h1>
               <h4 class="text-center" id="disbursed_post"></h4>
               <h4 class="text-center" id="disbursed_id"></h4>
               <h4 class="text-center" id="disbursed_body"></h4>
            </div>
            <div class="inner_buttons">
              <button class="okay_modal_button confirm-disbursed" id="confirm-disbursed" type="submit" tabindex="0">
                Confirm
              </button>
              <a class="cancel_modal_button cancel_details" role="button"> Cancel </a>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<input type="hidden" id="hidden_data"  value="{{ Custom::engToBnConvert(number_format($total_sal,2)) }}" data-emp="{{ Custom::engToBnConvert($total_emp)}}">
<script type="text/javascript">
    $(document).ready(function(){
        $('.hidden_loc').each(function(){
            var target = $(this).data('target');
            var emp = $(this).data('emp');
            var sal = $(this).val();

            $('#emp-'+target).text(emp);
            $('#salary-'+target).text(sal);
            var temp = $('#hidden_data').data('emp');
            var tsal = $('#hidden_data').val();

            $('#emp-count').text(temp);
            $('#total-salary').text(tsal);
        });
    });
    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    let detailsheight = $(".item_details_dialog").css("min-height", "115px");
    $(document).on('click','.disbursed_salary',function(){
        let id = $(this).data('id');
        let associateId = $(this).data('eaid');
        let date = $(this).data('date');
        let name = $(this).data('name');
        let post = $(this).data('post');
        $("#modal-id").val(id);
        $("#modal-associateId").val(associateId);
        $("#modal-month").val($(this).data('month'));
        $("#modal-year").val($(this).data('year'));
        $('#disbursed_name').html(name);
        $('#disbursed_post').html(post);
        $('#disbursed_id').html('আইডি: '+associateId);
        $('#disbursed_body').html(date+' মাসের বেতন প্রদান করা হচ্ছে । ');
        /*Show the dialog overlay-modal*/
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        /*Animate Dialog*/
        $(".show_item_details_modal").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "40%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });
    $("#confirm-disbursed").click(function() {
        let associate_id = $("#modal-associateId").val();
        let month = $("#modal-month").val();
        let year = $("#modal-year").val();
        let select_id = $("#modal-id").val();
        //alert(id);
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '/hr/reports/employee-salary-disbursed',
            type: "post",
            data: { _token : _token,
                as_id: associate_id,
                year: year,
                month: month
            },
            success: function(response){
                console.log(response);
                if(response.status === 'success'){
                    $("#"+select_id).html( $.trim(response.value) ).effect('highlight',{},2500);
                }

            }
        });

        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
</script>
