
<div class="panel panel-info">

    <div class="panel-body pb-0">
    	@php
    		$designation = designation_by_id();
    	@endphp
        <table class="table employee-salary-table" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen" cellpadding="2" cellspacing="0" border="1" align="center">
            <thead>
                <tr style="color:hotpink">
                    <th width="180">কর্মী/কর্মচারীদের নাম
                        <br/> ও যোগদানের তারিখ</th>
                    <th>আই ডি নং</th>
                    <th>মাসিক বেতন/মজুরী</th>
                    <th width="140">হাজিরা দিবস</th>
                    <th width="220">বেতন হইতে কর্তন </th>
                    <th width="250">মোট দেয় টাকার পরিমান</th>
                    <th>সর্বমোট টাকার পরিমান</th>
                </tr>
            </thead>
            <tbody>
            	@if($salary != null)
                <tr>
                    
                    <td>
                        <p style="margin:0;padding:0;">{{ $salary->employee_bengali['hr_bn_associate_name']??'' }}</p>
                        <p style="margin:0;padding:0;">{{ Custom::engToBnConvert(date('Y-m-d', strtotime($salary->employee['as_doj'])))??'' }}</p>
                        @php
                        	$designationId = $salary->employee['as_designation_id'];
                        @endphp
                        <p style="margin:0;padding:0;">{{ isset($designation[$designationId]['hr_designation_name_bn'])? Custom::engToBnConvert($designation[$designationId]['hr_designation_name_bn']):'' }} </p>
                        <p style="margin:0;padding:0;color:hotpink">মূল+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                        <p style="margin:0;padding:0;">
                            {{ Custom::engToBnConvert(($salary['basic'].'+'.$salary['house'].'+'.$salary['medical'].'+'.$salary['transport'].'+'.$salary['food'])) }}
                        </p>
                    </td>
                    <td>
                        <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                            {{ $salary['as_id'] }}
                        </p>
                        <p style="margin:0;padding:0;color:hotpink">
                            বিলম্ব উপস্থিতিঃ {{ Custom::engToBnConvert($salary['late_count']) }}
                        </p>
                        <p style="margin:0;padding:0">গ্রেডঃ {{ isset($salary->designation['hr_designation_grade'])? Custom::engToBnConvert($salary->designation['hr_designation_grade']):'' }}</p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">
                            {{ Custom::engToBnConvert(bn_money($salary['gross'])) }}
                            @if(isset($salaryIncrement[$salary->as_id]) && $salaryIncrement[$salary->as_id] != null)
                               <br>

                               <p style="font-size:11px;margin:0;padding:0;color:blueviolet">বর্ধিত বেতন:</p>
                                <p style="font-size:11px;margin:0;padding:0;color:blueviolet">
                                    {{ Custom::engToBnConvert($salaryIncrement[$salary->as_id]->increment_amount??'0.00') }}
                                </p>
                            @endif
                        </p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত দিবস
                            </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink;" > {{ Custom::engToBnConvert($salary['present']) }}</font>
                            </span>

                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">সরকারি ছুটি </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink"> {{ Custom::engToBnConvert($salary['holiday']) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0k">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিত দিবস </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                  <font style="color:hotpink"> {{ Custom::engToBnConvert($salary['absent']) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি মঞ্জুর </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($salary['leave']) }}</font>
                            </span>

                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($salary['present'] + $salary['holiday'] + $salary['leave'])}}</font>
                            </span>
                        </p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিতির জন্য</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{  Custom::engToBnConvert(round($salary['absent_deduct'])) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অর্ধ দিবসের জন্য কর্তন </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert(round($salary['half_day_deduct'])) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অগ্রিম গ্রহণ বাবদ </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($salary['salary_add_deduct'] == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert($salary->add_deduct['advp_deduct']??'0.00') }} </font>
                            </span>

                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">স্ট্যাম্প বাবদ </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert($salary->stamp??0) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ভোগ্যপণ্য ক্রয় </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                @if($salary['salary_add_deduct'] == null)
                                    {{ Custom::engToBnConvert('0.00') }}
                                @else
                                    {{ isset($salary->add_deduct['cg_deduct']) ? Custom::engToBnConvert($salary->add_deduct['cg_deduct']):Custom::engToBnConvert('0.00') }}
                                @endif
                            </font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">খাবার বাবদ কর্তন </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                
                              {{ ($salary['salary_add_deduct'] == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert(($salary->add_deduct['food_deduct']??'0.00')) }} </font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অন্যান্য </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($salary['salary_add_deduct'] == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert(($salary->add_deduct['others_deduct']??'0.00')) }} </font>
                            </span>

                        </p>
                    </td>

                    @php
                        $otHour = numberToTimeClockFormat($salary['ot_hour']);

                        // $otHour = $salary['ot_hour'];
                        $ot = round((float)($salary['ot_rate']) * $salary['ot_hour']);
                        $salaryAdd = ($salary['salary_add_deduct'] == null) ? '0.00' : ($salary->add_deduct['salary_add']??'0.00');
                        // $total = ($list->salary_payable + $ot + $list->attendance_bonus + $salaryAdd);
                    @endphp
                    <td>
                        <p style="margin:0;padding:0">

                              <span style="text-align: left; width: 57%; float: left;  white-space: wrap;">বেতন/মজুরী </span>
                              <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                              <span style="text-align: right;width: 28%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink"> {{ Custom::engToBnConvert(bn_money($salary['salary_payable'])) }}</font>
                             </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 57%; float: left;  white-space: wrap;">অতিরিক্ত সময়ের কাজের মজুরী </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 28%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert($ot) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 57%; float: left;  white-space: wrap;">অতিরিক্ত কাজের মজুরী হার </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 28%; float: right;  white-space: wrap;">
                            	<font style="color:hotpink"> ({{ $salary->employee['as_ot']==1?Custom::engToBnConvert($otHour):Custom::engToBnConvert('00') }}  ঘন্টা)</font>
                                <font style="color:hotpink">{{ Custom::engToBnConvert($salary['ot_rate']) }} </font>
                            </span>
                            {{-- <span style="text-align: right;width: 28%; float: right;  white-space: wrap;">
                                <font style="color:hotpink"> ({{ $salary['as_ot']==1?Custom::engToBnConvert($otHour):Custom::engToBnConvert('00') }}  ঘন্টা)</font>
                            </span> --}}

                            

                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: right;width: 57%; float: right;  white-space: wrap;">&nbsp;
                                </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 57%; float: left;  white-space: wrap;">উপস্থিত বোনাস </span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                <span style="text-align: right;width: 28%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ Custom::engToBnConvert($salary['attendance_bonus']) }}</font>
                                </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 57%; float: left;  white-space: wrap;">প্রোডাকশন বোনস </span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ Custom::engToBnConvert($salary->production_bouns) }}</font>
                                </span>
                        </p>
                        
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">
                                বেতন/মজুরী অগ্রিম/সমন্বয় 
                                @if(isset($salaryAdjust[$salary->as_id][2]))
                                    ( {!! Custom::engToBnConvert($salaryAdjust[$salary->as_id][2]->days??'') !!})
                                @endif
                            </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">

                                    @php 
                                    $salaryExtraAdd = 0;
                                    if(array_key_exists($salary->as_id, $salaryAddDeduct)){
                                    
                                        $salaryExtraAdd = $salaryAddDeduct[$salary->as_id]->salary_add??0;
                                    }
                                    if(isset($salaryAdjust[$salary->as_id][2])){
                                        $salaryExtraAdd += $salaryAdjust[$salary->as_id][2]->sum??0;
                                    }
                                    @endphp
                                        
                                    
                                   

                                    {{ Custom::engToBnConvert(number_format($salaryExtraAdd,2)??'0.0') }}
                                </font>
                            </span>

                        </p>
                        @if(isset($salaryAdjust[$salary->as_id][3]))
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বর্ধিত বেতন সমন্বয়</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">
                                    {{ Custom::engToBnConvert(number_format($salaryAdjust[$salary->as_id][3]->sum, 2) ??'0.00') }}
                                </font>
                            </span>

                        </p>
                        @endif
                        

                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 57%; float: left;  white-space: wrap;">ছুটি সমন্বয়</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 28%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert($salary['leave_adjust']) }}</font>
                            </span>

                        </p>
                    </td>
                    <td>
                        @php
                            $totalSalary = ($salary['total_payable']);
                        @endphp
                        {{ Custom::engToBnConvert(bn_money($totalSalary)) }}
                    </td>
                    
                </tr>
                @else
                <tr>
                	<td colspan="7">
                		<h4 class="text-center text-red">No Salary Sheet Found!</h4>
                	</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
