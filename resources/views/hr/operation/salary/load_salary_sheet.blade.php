
<button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('salary-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
<div id="salary-print">
    <style>
        @media print {
            
            .pagebreak {
                page-break-before: always !important;
            }
            .disburse-button{
                display: none;
            }
        }
    </style>
    @php
        $loc_count = 0;
        $total_emp = 0;
        $total_sal = 0;
        $locations = location_by_id();
        $salmonth = date_to_bn_month($pageHead->for_date);
        $totalPayable = 0;
        $attendanceBonus = 0;
    @endphp
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
                $getLocation = $locations[$location]['hr_location_name']??'';
                $loc_count++;
                $totalSalary_s = 0;
                $emp = 0;
                $ot_payable = 0; 
                foreach($lists as $tSalary) {
                    if($tSalary->as_location == $location && $tSalary != null){

                        // $salaryAdd_s = (($tSalary->salary_add_deduct_id == null) ? '0.00' : $tSalary->salary_add_deduct_id);
                        $ot_s = round((float)($tSalary->ot_rate) * $tSalary->ot_hour);
                        //$leaveAdjust_s = Custom::salaryLeaveAdjustAsIdMonthYearWise($tSalary->as_id, $tSalary->month, $tSalary->year);
                        $totalSalary_s += ($tSalary->total_payable);
                        $emp++;
                        $ot_payable += round((float)($tSalary->ot_rate) * $tSalary->ot_hour); 
                    }
                }
            @endphp
            @if(in_array($location, $asLocationList) && $emp > 0)
                <div class="panel panel-info">
                    
                    <div class="panel-body">

                        <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                            <tr>
                                <td style="width:14%">
                                    <p style="margin:0;padding: 0"><strong>লোকেশনঃ </strong>
                                        {{ $getLocation }}
                                    </p>
                                    <p style="margin:0;padding: 0">
                                        @if(!empty($info['sub_sec']))
                                            <strong>সাব-সেকশন:</strong> {{$info['sub_sec']}}
                                        @elseif(!empty($info['section']))
                                            <strong>সেকশন: </strong> {{$info['section']}}
                                        @elseif(!empty($info['department']))
                                            <strong>ডিপার্টমেন্ট: </strong> {{$info['department']}}
                                        @elseif(!empty($info['area']))
                                            <strong>এরিয়া: </strong> {{$info['area']}}
                                        @endif
                                    </p>
                                    {{-- <p style="margin:0;padding: 0"><strong>তারিখঃ </strong>
                                        {{Custom::engToBnConvert($pageHead->current_date)}}
                                    </p>
                                    <p style="margin:0;padding: 0"><strong>&nbsp;সময়ঃ </strong>
                                        {{ Custom::engToBnConvert($pageHead->current_time) }}
                                    </p> --}}
                                    @if(isset($input['perpage'] ))
                                    @if($input['perpage'] > 1)
                                    <p style="margin:0;padding: 0"><strong>&nbsp;পৃষ্ঠা নংঃ </strong>
                                        {{ Custom::engToBnConvert($pageKey) }}
                                    </p>
                                    @endif
                                    @endif
                                </td>
                                <td style="width:15%;font-size:10px">
                                    @if(isset($pageHead->pay_date) && $pageHead->pay_date != null)
                                    <p style="margin:0;padding: 0"><strong>&nbsp;প্রদান তারিখঃ </strong>
                                        {{ Custom::engToBnConvert($pageHead->pay_date) }} ইং
                                    </p>
                                    @endif
                                </td>
                                <td>
                                    <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:14px;">
                                        {{ $pageHead->unit_name }}
                                    </h3>
                                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:11px;">বেতন/মজুরী এবং অতিরিক্ত সময়ের মজুরী
                                    
                                    <br/>
                                    মাসঃ {{ $salmonth }}</h5>
                                </td>
                                <td width="0%"> &nbsp;</td>
                                <td style="width:30%" style="text-align: right;">
                                    @if($pageHead->floor_name != null)
                                    <p style="margin:0;padding: 0;">
                                        <strong>ফ্লোর নংঃ
                                            {{ Custom::engToBnConvert($pageHead->floor_name) }}
                                        </strong>
                                    </p>
                                    @endif
                                    @if(isset($input['perpage'] ))
                                    @if($input['perpage'] > 1)
                                    <p style="margin:0;padding: 0;text-align: right;">
                                        সর্বমোট টাকার পরিমানঃ <span style="color:hotpink" >{{Custom::engToBnConvert(bn_money($totalSalary_s))}}</span>
                                    </p>
                                    @php
                                        $list_total_ot = array_column(array_column($lists, 'salary'),'ot_rate');
                                    @endphp
                                    <p style="margin:0;padding: 0;text-align: right;">
                                        মোট কর্মী/কর্মচারীঃ <span style="color:hotpink" >{{Custom::engToBnConvert($emp)}}</span>
                                    </p>
                                    <p style="margin:0;padding: 0;text-align: right;">
                                        {{-- স্ট্যাম্প বাবদঃ <span style="color:hotpink" >{{Custom::engToBnConvert($emp*10)}}</span> |  --}}
                                        অতিরিক্ত কাজের মজুরীঃ <span style="color:hotpink" id="">{{Custom::engToBnConvert(bn_money($ot_payable))}}</span>
                                    </p>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <table class="table table-head" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen" cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead>
                                <tr style="color:hotpink">
                                    <th style="color:lightseagreen">ক্রমিক নং</th>
                                    <th width="180" style="width: 225px;">কর্মী/কর্মচারীদের নাম
                                        <br/> ও যোগদানের তারিখ</th>
                                    <th>আই ডি নং</th>
                                    <th>মাসিক বেতন/মজুরী</th>
                                    <th width="140">হাজিরা দিবস</th>
                                    <th width="180">বেতন হইতে কর্তন </th>
                                    <th width="250">মোট দেয় টাকার পরিমান</th>
                                    <th>সর্বমোট টাকার পরিমান</th>
                                    <th width="80">দস্তখত</th>
                                    <th class="disburse-button" width="80">বিতরণ</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach($locationDataSet as $pageKey=>$lists) --}}
                                <?php $j=1;?>
                                @foreach($lists as $k=>$list)
                                    @if($list->as_location == $location && $list != null)
                                        <tr>
                                            <td style="text-align: center;">{{ Custom::engToBnConvert($j) }}</td>
                                            <td>
                                                <p style="margin:0;padding:0;">{{ $list->hr_bn_associate_name }}</p>
                                                <p style="margin:0;padding:0;">{{ Custom::engToBnConvert($list->as_doj) }}</p>
                                                <p style="margin:0;padding:0;">{{ $designation[$list->as_designation_id]['hr_designation_name_bn']}} </p>
                                                <p style="margin:0;padding:0;color:hotpink">মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                                                <p style="margin:0;padding:0;">
                                                    {{ Custom::engToBnConvert($list->basic.'+'.$list->house.'+'.$list->medical.'+'.$list->transport.'+'.$list->food) }}
                                                </p>
                                                <p style="margin:0;padding:0;color:hotpink">
                                                   {{ $salmonth}}
                                                </p>
                                            </td>
                                            <td>
                                                <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                                                    {{ $list->as_id }}
                                                </p>
                                                পূর্বের আইডিঃ 
                                                <p style="font-size:11px;margin:0;padding:0;color:blueviolet">
                                                    {{ $list->as_oracle_code }}
                                                </p>
                                                <p style="margin:0;padding:0;color:hotpink">
                                                    বিলম্ব উপস্থিতিঃ {{ Custom::engToBnConvert($list->late_count) }}
                                                </p>
                                                @if(isset($designation[$list->as_designation_id]))
                                                    @if($designation[$list->as_designation_id]['hr_designation_grade'] > 0 || $designation[$list->as_designation_id]['hr_designation_grade'] != null)
                                                    <p style="margin:0;padding:0">গ্রেডঃ {{ eng_to_bn($designation[$list->as_designation_id]['hr_designation_grade'])}}</p>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0;text-align: center;">
                                                    {{ Custom::engToBnConvert($list->gross) }}
                                                </p>
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত দিবস
                                                    </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink;" > {{ Custom::engToBnConvert($list->present) }}</font>
                                                    </span>

                                                </p>
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">সরকারি ছুটি </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink"> {{ Custom::engToBnConvert($list->holiday) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0k">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিত দিবস </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                          <font style="color:hotpink"> {{ Custom::engToBnConvert($list->absent + $list->leave) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি মঞ্জুর </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($list->leave) }}</font>
                                                    </span>

                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($list->present + $list->holiday + $list->leave)}}</font>
                                                    </span>
                                                </p>
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিতির জন্য</span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{  Custom::engToBnConvert(round($list->absent_deduct)) }}</font>
                                                    </span>
                                                </p>
                                                @if($list->half_day_deduct > 0)
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অর্ধ দিবসের জন্য কর্তন </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert(round($list->half_day_deduct)) }}</font>
                                                    </span>
                                                </p>
                                                @endif
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অগ্রিম গ্রহণ বাবদ </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($list->salary_add_deduct_id == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert($salaryAddDeduct[$list->as_id]['advp_deduct']) }}</font>
                                                    </span>

                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">স্ট্যাম্প বাবদ </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert($list->stamp??0) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ভোগ্যপণ্য ক্রয় </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                                        @if($list->salary_add_deduct_id == null)
                                                            {{ Custom::engToBnConvert('0.00') }}
                                                        @else
                                                            {{ Custom::engToBnConvert($salaryAddDeduct[$list->as_id]['cg_product']) }}
                                                        @endif
                                                    </font>
                                                    </span>
                                                </p>
                                                @if($list->salary_add_deduct_id)
                                                    @if($salaryAddDeduct[$list->as_id]['food_deduct'] > 0)
                                                    <p style="margin:0;padding:0">
                                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">খাবার বাবদ কর্তন </span>
                                                        <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                        </span>
                                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                                          {{   Custom::engToBnConvert($salaryAddDeduct[$list->as_id]['food_deduct']) }} </font>
                                                        </span>
                                                    </p>
                                                    @endif
                                                @endif
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অন্যান্য </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($list->salary_add_deduct_id == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert($salaryAddDeduct[$list->as_id]['others_deduct']) }} </font>
                                                    </span>

                                                </p>
                                            </td>

                                            @php
                                                
                                                $otHour = numberToTimeClockFormat($list->ot_hour);
                                                $ot = ((float)($list->ot_rate) * $list->ot_hour);
                                                $ot = number_format((float)$ot, 2, '.', '');
                                                $salaryAdd = ($list->salary_add_deduct_id == null) ? '0.00' : ($salaryAddDeduct[$list->as_id]['salary_add']);
                                                // $total = ($list->salary_payable + $ot + $list->attendance_bonus + $salaryAdd);
                                                $totalPayable = $totalPayable + $list->salary_payable;
                                                $attendanceBonus = $attendanceBonus + $list->attendance_bonus;
                                            @endphp
                                            <td>
                                                <p style="margin:0;padding:0">

                                                      <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মজুরী </span>
                                                      <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                      <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink"> {{ Custom::engToBnConvert($list->salary_payable) }}</font>
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
                                                            <font style="color:hotpink">{{ Custom::engToBnConvert($list->ot_rate) }} </font>
                                                        </span>
                                                        @if($list->as_ot>0)
                                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink"> ({{ $list->as_ot==1?Custom::engToBnConvert($otHour):Custom::engToBnConvert('00') }}  ঘন্টা)</font>
                                                        </span>
                                                        @endif
                                                </p>
                                                @if($list->as_ot>0)
                                                <p style="margin:0;padding:0">

                                                        <span style="text-align: right;width: 65%; float: right;  white-space: wrap;">&nbsp;
                                                        </span>
                                                </p>
                                                @endif
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত বোনাস </span>
                                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink">{{ Custom::engToBnConvert($list->attendance_bonus) }}</font>
                                                        </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">প্রোডাকশন বোনাস </span>
                                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink">{{ Custom::engToBnConvert($list->production_bonus) }}</font>
                                                        </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মজুরী অগ্রিম/সমন্বয়</span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert($salaryAdd) }}</font>
                                                    </span>

                                                </p>

                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি সমন্বয়</span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert($list->leave_adjust) }}</font>
                                                    </span>

                                                </p>
                                            </td>
                                            <td style="text-align: center;">
                                                @php
                                                    $loc_emp++;
                                                    $totalSalary = ($list->total_payable);
                                                    $loc_sal = $loc_sal+$totalSalary;

                                                @endphp
                                                {{ Custom::engToBnConvert(bn_money($totalSalary)) }}
                                            </td>
                                            <td></td>
                                            <td class="disburse-button" id="{{ $j }}-{{ $list->as_id }}">
                                                @if($list->disburse_date == null)
                                                    <a data-id="{{ $j }}-{{ $list->as_id }}" class="btn btn-primary btn-sm disbursed_salary text-white" data-eaid="{{ $list->as_id }}" data-date="{{ $pageHead->for_date }}" data-month="{{ $pageHead->month }}" data-year="{{ $pageHead->year }}" data-name="{{ $list->hr_bn_associate_name }}" data-post="{{ $designation[$list->as_designation_id]['hr_designation_name_bn']}}"  rel='tooltip' data-tooltip-location='top' data-tooltip='বেতন প্রদান করুন' > হয় নি </a>
                                                @else
                                                    হ্যাঁ 
                                                    <br>
                                                    <b>{{ Custom::engToBnConvert($list->disburse_date) }}</b>
                                                @endif
                                            </td>
                                        </tr>
                                        <?php $j++; ?>
                                    @endif
                                @endforeach
                                {{-- @endforeach --}}

                            </tbody>
                            <tfoot>
                                <tr>
                                    
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" class="hidden_loc" data-target="{{$loc_count}}" value="{{ Custom::engToBnConvert(bn_money($loc_sal)) }}" data-emp="{{ Custom::engToBnConvert($loc_emp)}}">
                        @php
                            $total_sal = $total_sal+$loc_sal;
                            $total_emp += $loc_emp;
                        @endphp

                    </div>
                </div>
                @if(count($locationDataSet) != $pageKey || count($locationDataSet) != 1)
                <div class="pagebreak"> </div>
                @endif
            @endif
        @endforeach
    @endforeach
    @if(isset($pageHead->totalStamp) && $input['perpage'] > 1)
        <div id="unit-info">
            <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                <tr>
                    <td style="width:25%">
                        <p style="margin:0;padding: 0"><strong>মোট কর্মী/কর্মচারীঃ </strong>
                            {{ Custom::engToBnConvert($pageHead->totalEmployees) }}
                        </p>
                        <p style="margin:0;padding: 0"><strong>স্ট্যাম্প বাবদঃ </strong>
                            {{ Custom::engToBnConvert(bn_money($pageHead->totalStamp)) }}
                        </p>
                        <p style="margin:0;padding: 0"><strong>অতিরিক্ত কাজের সময়: </strong>
                            {{ Custom::engToBnConvert(numberToTimeClockFormat($pageHead->totalOtHour)) }}
                        </p>
                    </td>
                    <td style="width:25%;">
                        <p style="margin:0;padding: 0"><strong>সর্বমোট বেতন/মজুরী: </strong>
                            {{ Custom::engToBnConvert(bn_money($totalPayable)) }}
                        </p>
                        <p style="margin:0;padding: 0"><strong>অতিরিক্ত কাজের মজুরী: </strong>
                            {{ Custom::engToBnConvert(bn_money($pageHead->totalOTAmount)) }}
                        </p>
                        <p style="margin:0;padding: 0"><strong> উপস্থিত বোনাস: </strong>
                            {{ Custom::engToBnConvert($attendanceBonus) }}
                        </p>
                    </td>
                    <td style="width:25%; text-align:right;">
                        <p style="margin:0;padding: 0"><strong>সর্বমোট টাকার পরিমানঃ </strong>
                            {{ Custom::engToBnConvert(bn_money($pageHead->totalSalary)) }}
                        </p>
                    </td>
                    
                </tr>
            </table>
        </div>
    @endif
</div>
{{-- modal --}}
<div class="item_details_section">
    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
      <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
        <div class="fade-box-details fade-box">
          <div class="inner_gray clearfix">
            <div class="inner_gray_text text-center" id="heading">
             <h3 class="no_margin text-white">বেতন বিতরণ</h3>   
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
               <h3 class="text-center" ><strong class="f22" id="disbursed_name"></strong></h3>
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
<input type="hidden" id="hidden_data"  value="{{ Custom::engToBnConvert(bn_money($total_sal)) }}" data-emp="{{ Custom::engToBnConvert($total_emp)}}">
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
    detailsheight = $(".item_details_dialog").css("min-height", "115px");
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
