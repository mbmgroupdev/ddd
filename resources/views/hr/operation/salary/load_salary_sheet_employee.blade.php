
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
        $locations = location_by_id();
        $getUnit = unit_by_id();
        $salmonth = date_to_bn_month($pageHead->for_date);
    @endphp
    @if(count($getSalaryList) > 0)
    @foreach($getSalaryList as $salary)
        <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
            <tr>
                <td style="width:14%">
                    <p style="margin:0;padding: 0"><strong>লোকেশনঃ </strong>
                        {{ $locations[$salary->as_location]['hr_location_name'] }}
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
                        {{ $getUnit[$salary->as_unit_id]['hr_unit_name_bn'] }}
                    </h3>
                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:11px;">
                        বেতন/মজুরী এবং অতিরিক্ত সময়ের মজুরী
                        
                        <br/>
                        মাসঃ {{ $salmonth }}
                        
                    </h5>
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
                    <th width="120">সর্বমোট টাকার পরিমান</th>
                    <th width="70">দস্তখত</th>
                    {{-- <th class="disburse-button" width="80">বিতরণ</th> --}}
                </tr>
            </thead>
            <tbody>
                <?php $j=1;?>
                
                <tr>
                    <td style="text-align: center;">{{ Custom::engToBnConvert($j) }}</td>
                    <td>
                        <p style="margin:0;padding:0;">{{ $salary->hr_bn_associate_name }}</p>
                        <p style="margin:0;padding:0;">{{ Custom::engToBnConvert(date('Y-m-d', strtotime($salary->as_doj))) }}</p>
                        <p style="margin:0;padding:0;">
                            {{ $designation[$salary->as_designation_id]['hr_designation_name_bn']}}
                            @if($salary->as_ot == 0)
                            - {{ $getSection[$salary->as_section_id]['hr_section_name_bn']}}
                            @endif
                        </p>
                        <p style="margin:0;padding:0;color:hotpink">মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                        <p style="margin:0;padding:0;">
                            {{ Custom::engToBnConvert($salary->basic.'+'.$salary->house.'+'.$salary->medical.'+'.$salary->transport.'+'.$salary->food) }}
                        </p>
                        <p style="margin:0;padding:0;color:hotpink">
                           {{ $salmonth}}

                        </p>
                    </td>
                    <td>
                        <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                            {{ $salary->as_id }}
                        </p>
                        পূর্বের আইডিঃ 
                        <p style="font-size:11px;margin:0;padding:0;color:blueviolet">
                            {{ $salary->as_oracle_code }}
                        </p>
                        <p style="margin:0;padding:0;color:hotpink">
                            বিলম্ব উপস্থিতিঃ {{ Custom::engToBnConvert($salary->late_count) }}
                        </p>
                        @if(isset($designation[$salary->as_designation_id]))
                            @if($designation[$salary->as_designation_id]['hr_designation_grade'] > 0 || $designation[$salary->as_designation_id]['hr_designation_grade'] != null)
                            <p style="margin:0;padding:0">গ্রেডঃ {{ eng_to_bn($designation[$salary->as_designation_id]['hr_designation_grade'])}}</p>
                            @endif
                        @endif
                    </td>
                    <td>
                        <p style="margin:0;padding:0;text-align: center;">
                            {{ Custom::engToBnConvert($salary->gross) }}

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
                                <font style="color:hotpink;" > {{ Custom::engToBnConvert($salary->present) }}</font>
                            </span>

                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">সরকারি ছুটি </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink"> {{ Custom::engToBnConvert($salary->holiday) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0k">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিত দিবস </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                  <font style="color:hotpink"> {{ Custom::engToBnConvert($salary->absent + $salary->leave) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি মঞ্জুর </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($salary->leave) }}</font>
                            </span>

                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($salary->present + $salary->holiday + $salary->leave)}}</font>
                            </span>
                        </p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিতির জন্য</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{  Custom::engToBnConvert(number_format($salary->absent_deduct,2)) }}</font>
                            </span>
                        </p>
                        @if($salary->half_day_deduct > 0)
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অর্ধ দিবসের জন্য কর্তন </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($salary->half_day_deduct,2)) }}</font>
                            </span>
                        </p>
                        @endif
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অগ্রিম গ্রহণ বাবদ </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                @if(array_key_exists($salary->as_id, $salaryAddDeduct))
                                    @php
                                        $advDeduct = $salaryAddDeduct[$salary->as_id]->advp_deduct??'0.00';
                                    @endphp
                                    {{ Custom::engToBnConvert(number_format($advDeduct, 2)) }}
                                @else
                                    {{ Custom::engToBnConvert('0.00') }}
                                @endif
                                
                            </font>
                            </span>

                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">স্ট্যাম্প বাবদ </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($salary->stamp,2)??'0.0') }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ভোগ্যপণ্য ক্রয় </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                @if(array_key_exists($salary->as_id, $salaryAddDeduct))
                                    {{ Custom::engToBnConvert($salaryAddDeduct[$salary->as_id]->cg_deduct??'0.0') }}
                                @else
                                    {{ Custom::engToBnConvert('0.00') }}
                                @endif
                            </font>
                            </span>
                        </p>

                        @if($salary->as_location == 7)
                            
                            <p style="margin:0;padding:0">
                                <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">খাবার বাবদ কর্তন </span>
                                <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                </span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                    @if(array_key_exists($salary->as_id, $salaryAddDeduct))
                                    {{ Custom::engToBnConvert(number_format($salaryAddDeduct[$salary->as_id]->food_deduct,2)??'0.0') }}
                                    @else
                                        {{ Custom::engToBnConvert('0.00') }}
                                    @endif
                                  
                                </font>
                                </span>
                            </p>
                           
                        @endif
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অন্যান্য </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                @if(array_key_exists($salary->as_id, $salaryAddDeduct))
                                    {{ Custom::engToBnConvert(number_format($salaryAddDeduct[$salary->as_id]->others_deduct,2)??'0.0') }}
                                @else
                                    {{ Custom::engToBnConvert('0.00') }}
                                @endif
                            </font>
                            </span>

                        </p>
                    </td>

                    @php
                        
                        $otHour = numberToTimeClockFormat($salary->ot_hour);
                        $ot = ((float)($salary->ot_rate) * $salary->ot_hour);
                        $ot = number_format((float)$ot, 2, '.', '');
                        
                    @endphp
                    <td>
                        <p style="margin:0;padding:0">

                              <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মজুরী </span>
                              <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                              <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink"> {{ Custom::engToBnConvert(number_format($salary->salary_payable,2)) }}</font>
                             </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত সময়ের কাজের মজুরী </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($ot,2)) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                                 <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত কাজের মজুরী হার </span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($salary->ot_rate,2)) }} </font>
                                </span>
                                @if($salary->as_ot>0)
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink"> ({{ $salary->as_ot==1?Custom::engToBnConvert($otHour):Custom::engToBnConvert('00') }}  ঘন্টা)</font>
                                </span>
                                @endif
                        </p>
                        @if($salary->as_ot>0)
                        <p style="margin:0;padding:0">

                                <span style="text-align: right;width: 65%; float: right;  white-space: wrap;">&nbsp;
                                </span>
                        </p>
                        @endif
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত বোনাস </span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($salary->attendance_bonus,2)) }}</font>
                                </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">প্রোডাকশন বোনাস </span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($salary->production_bonus,2)) }}</font>
                                </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মজুরী অগ্রিম/সমন্বয়</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">
                                @if(array_key_exists($salary->as_id, $salaryAddDeduct))
                                    {{ Custom::engToBnConvert(number_format($salaryAddDeduct[$salary->as_id]->salary_add,2)??'0.0') }}
                                @else
                                    {{ Custom::engToBnConvert('0.00') }}
                                @endif
                            </font>
                            </span>

                        </p>

                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি সমন্বয়</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($salary->leave_adjust,2)) }}</font>
                            </span>

                        </p>
                    </td>
                    <td style="text-align: center;">
                        @php

                            $totalSalary = ($salary->total_payable);

                        @endphp
                        {{ Custom::engToBnConvert(bn_money($totalSalary)) }}
                        @if(isset($input['pay_status']) && ($input['pay_status'] == 'dbbl' || $input['pay_status'] == 'cash'))
                            @if(isset($salary->pay_status) && $salary->pay_status == 3)
                            <p style="margin:0;padding:0">

                                <span style="text-align: left; width: 45%; float: left;  white-space: wrap;">ব্যাংক পে</span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                </span>
                                <span style="text-align: right;width: 50%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ Custom::engToBnConvert(bn_money($salary->bank_payable)) }}</font>
                                </span>

                            </p>
                            <p style="margin:0;padding:0">

                                <span style="text-align: left; width: 45%; float: left;  white-space: wrap;">ক্যাশ পে</span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                </span>
                                <span style="text-align: right;width: 50%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ Custom::engToBnConvert(bn_money($salary->cash_payable)) }}</font>
                                </span>

                            </p>
                            @endif
                        @endif
                    </td>
                    <td></td>
                    {{-- <td class="disburse-button" id="{{ $j }}-{{ $salary->as_id }}">
                        @if($salary->disburse_date == null)
                            <a data-id="{{ $j }}-{{ $salary->as_id }}" class="btn btn-primary btn-sm disbursed_salary text-white" data-eaid="{{ $salary->as_id }}" data-date="{{ $pageHead->for_date }}" data-month="{{ $pageHead->month }}" data-year="{{ $pageHead->year }}" data-name="{{ $salary->hr_bn_associate_name }}" data-post="{{ $designation[$salary->as_designation_id]['hr_designation_name_bn']}}"  rel='tooltip' data-tooltip-location='top' data-tooltip='বেতন প্রদান করুন' > হয় নি </a>
                        @else
                            হ্যাঁ 
                            <br>
                            <b>{{ Custom::engToBnConvert($salary->disburse_date) }}</b>
                        @endif
                    </td> --}}
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    
                </tr>
            </tfoot>
        </table>
    @endforeach
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
<script type="text/javascript">

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
