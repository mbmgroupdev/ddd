
<button class="btn btn-sm btn-primary hidden-print" onclick="printPayslip('salary-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
<div id="salary-print">
    <style>
        @media print {
            
            .pagebreak {
                page-break-before: always !important;
            }
            .disburse-button{
                display: none;
            }
            table td p{
                font-size: 11px;
                margin: 0;
                padding: 0;
            }
            
        }
        table td {
                vertical-align: top !important;
            }
    </style>
    @php
        $pageKey = 0;
        $loc_count = 0;
        $total_emp = 0;
        $total_sal = 0;
        $locations = location_by_id();
        $units = unit_by_id();
        $salmonth = date_to_bn_month($pageHead->for_date);
        $totalPayable = 0;
        $attendanceBonus = 0;
        $j=1;
    @endphp
    
    @foreach($locationDataSet as $key => $lists)
        @php
            $pageKey += 1;
            $loc_emp = 0;
            $loc_sal = 0;
            $loc_count++;
            $totalSalary_s = 0;
            $emp = 0;
            $ot_payable = 0; 
            foreach($lists as $tSalary) {

                    // $salaryAdd_s = (($tSalary->salary_add_deduct_id == null) ? '0.00' : $tSalary->salary_add_deduct_id);
                    $ot_s = round((float)($tSalary->ot_rate) * $tSalary->ot_hour);
                    //$leaveAdjust_s = Custom::salaryLeaveAdjustAsIdMonthYearWise($tSalary->as_id, $tSalary->month, $tSalary->year);
                    $totalSalary_s += ($tSalary->total_payable);
                    $emp++;
                    $ot_payable += round((float)($tSalary->ot_rate) * $tSalary->ot_hour); 

            }
        @endphp
        @if($emp > 0)
            <div class="panel panel-info">
                
                <div class="panel-body">
                    {{-- @foreach($locationDataSet as $pageKey=>$lists) --}}
                    
                    @foreach($lists as $k=>$list)
                        @if( $list != null)
                            @php
                                                
                                $otHour = numberToTimeClockFormat($list->ot_hour);
                                $ot = ((float)($list->ot_rate) * $list->ot_hour);
                                $ot = number_format((float)$ot, 2, '.', '');
                                $salaryAdd = ($list->salary_add_deduct_id == null) ? '0.00' : ($salaryAddDeduct[$list->as_id]['salary_add']??'0.00');
                                // $total = ($list->salary_payable + $ot + $list->attendance_bonus + $salaryAdd);
                                $totalPayable = $totalPayable + $list->salary_payable;
                                $attendanceBonus = $attendanceBonus + $list->attendance_bonus;
                            @endphp
                            <table style="width:100%;margin-bottom: 40px; ">
                                <tr>
                                <td colspan="2" style="">
                                    <br>
                                    <p style="">নামঃ <strong style="color:hotpink;">{{ $list->hr_bn_associate_name }}</strong></p>
                                    <p style="">পদবীঃ <span style="color:hotpink;">{{ $designation[$list->as_designation_id]['hr_designation_name_bn']}}</span></p>
                                    
                                    <p >যোগদানের তারিখঃ <span style="color:hotpink;">{{ Custom::engToBnConvert($list->as_doj) }}</span></p>
                                </td>
                                <td colspan="3" style="padding:5px;color:hotpink;text-align:center">
                                    <h2 style="margin:0;text-align:center;font-weight:600;font-size:16px;">{{ $units[$list->as_unit_id]['hr_unit_name_bn']}}</h2>
                                    <h3 style="font-size: 14px;margin: 0">পে-স্লিপঃ <strong>{{ $salmonth }}</strong> </h3>
                                   <p style="color:black;">
                                        <font style="font-size: 12px;">আইডি  <strong>{{ $list->as_id }}</strong>  </font>
                                        </font> (পূর্বের আইডি: {{ $list->as_oracle_code }} )
                                   </p>

                                    
                                </td>
                                <td colspan="2" style="padding:5px;">

                                    <p style="text-align: right;">
                                        
                                        <span style="border-radius:50%;width: 40px;height: 40px;border:1px solid #999;color:#999;line-height: 40px;text-align:center;display: inline-block;">{{eng_to_bn($j)}}</span>
                                    </p>
                                        @if(isset($designation[$list->as_designation_id]))
                                            @if($designation[$list->as_designation_id]['hr_designation_grade'] > 0 || $designation[$list->as_designation_id]['hr_designation_grade'] != null)
                                            <p>গ্রেডঃ <span style="color:hotpink;">{{ eng_to_bn($designation[$list->as_designation_id]['hr_designation_grade'])}}</span></p>
                                            @endif
                                        @endif
                                    
                                    
                                </td>
                            </tr>
                            <tr>
                                <!-- first -->
                                <td  style="width: 13%;">
                                    <p><br></p>
                                    <p>উপস্থিত দিবস</p>
                                    <p>ছুটি দিবস</p>
                                    <p style="margin:0;padding:0k">অনুপস্থিত দিবস</p>
                                    <p>ছুটি মঞ্জুর</p>
                                    <p style="border-top:1px solid #999">মোট দেয়</p>
                                    @if($list->as_ot>0)
                                    <p>মোট অতিরিক্ত দেয় </p>
                                    @endif
                                </td>
                                <td style="width: 10%; text-align: right;color:hotpink;">
                                    <p><br></p>
                                    <p>=&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($list->present) }}</p>
                                    <p>=&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($list->holiday) }}</p>
                                    <p style="margin:0;padding:0k">=&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($list->absent + $list->leave) }}</p>
                                    <p>=&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($list->leave) }} </p>
                                    <p style="border-top:1px solid #999">=&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($list->present + $list->holiday + $list->leave)}} দিন</p>
                                    @if($list->as_ot>0)
                                    <p>{{ $list->as_ot==1?Custom::engToBnConvert($otHour):Custom::engToBnConvert('00') }} ঘন্টা </p>
                                    @endif

                                </td>
                                <!-- second -->
                                <td style="width: 20%;padding-left: 15px; ">
                                    <p></p>
                                    <p>মূল বেতন</p>
                                    <p>বাড়ী বাড়া (৫০%)</p>
                                    <p>চিকিৎসা ভাতা</p>
                                    <p>যাতায়াত</p>
                                    <p>খাদ্য</p>
                                    <p style="border-top:1px solid #999">মোট মজুরি</p>
                                    @if($list->as_ot>0)
                                    <p>অতিরিক্ত কাজের মজুরী হার </p>
                                    @endif
                                </td>
                                <td style="width: 9%;text-align: right;color:hotpink;">
                                    <p></p>
                                    <p>
                                       {{ Custom::engToBnConvert($list->basic) }}
                                   </p>
                                    <p>
                                        {{ Custom::engToBnConvert($list->house) }}
                                    </p>
                                    <p>
                                    {{ Custom::engToBnConvert($list->medical) }}
                                    </p>
                                    <p> {{ Custom::engToBnConvert($list->transport) }}
                                    </p>
                                    <p> {{ Custom::engToBnConvert($list->food) }}
                                    </p>
                                    <p style="border-top:1px solid #999"> {{ Custom::engToBnConvert($list->basic+$list->house+$list->medical+$list->transport+$list->food) }}
                                    </p>
                                    @if($list->as_ot>0)
                                    <p > {{ Custom::engToBnConvert($list->ot_rate) }} </p>
                                    @endif
                                </td>
                                <td style="width: 32%;padding-left: 15px; ">
                                    <p>অগ্রিম গ্রহণ/ভোগ্যপণ্য ক্রয়/অন্যান্য কর্তন</p>
                                    <p>প্রদেয় মজুরি&nbsp;&nbsp;&nbsp;</p>
                                    <p>অতিরিক্ত কাজের মজুরি &nbsp;&nbsp;&nbsp;</p>
                                    <p>হাজিরা বোনাস&nbsp;&nbsp;&nbsp;</p>
                                    <p>প্রোডাকশন বোনাস&nbsp;&nbsp;&nbsp;</p>
                                    <p>মজুরি সমন্বয়&nbsp;&nbsp;&nbsp;</p>
                                    <p>ছুটি সমন্বয়&nbsp;&nbsp;&nbsp;</p>
                                    <p style="border-top:1px solid #999">মোট প্রদেয়&nbsp;&nbsp;&nbsp;</p>
                                </td>
                                <!-- third -->
                                
                                <td  style="width: 12%;text-align: right; color:hotpink;" >
                                    


                                    @php
                                        $listdeduct = 0;
                                        if(($list->salary_add_deduct_id != null)){
                                            $adv = $salaryAddDeduct[$list->as_id]['advp_deduct']??0;
                                            $cg = $salaryAddDeduct[$list->as_id]['cg_product']??0;
                                            $fd = $salaryAddDeduct[$list->as_id]['food_deduct']??0;
                                            $other = $salaryAddDeduct[$list->as_id]['others_deduct']??0;

                                            $listdeduct = $adv+$cg+$fd+$other;
                                        }
                                        $deduct = $list->absent_deduct+$list->half_day_deduct+$listdeduct;
                                    @endphp
                                    <p>&nbsp;&nbsp;&nbsp; {{Custom::engToBnConvert($deduct)}} =/টঃ</p>
                                    <p>&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($list->salary_payable) }} =/টঃ</p>
                                    <p>&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($ot) }} =/টঃ</p>
                                    <p>&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($list->attendance_bonus) }} =/টঃ</p>
                                    <p>&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($list->production_bonus) }} =/টঃ</p>
                                    <p>&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($salaryAdd) }} =/টঃ</p>
                                    <p>&nbsp;&nbsp;&nbsp;{{ Custom::engToBnConvert($list->leave_adjust) }} =/টঃ</p>
                                    <p style="border-top:1px solid #999">&nbsp;&nbsp;&nbsp; {{ Custom::engToBnConvert(bn_money($list->total_payable)) }}=/টঃ</p>
                                </td>
                            </tr>
                            </table>
                            <?php $j++; ?>
                        @endif
                    @endforeach
                    {{-- @endforeach --}}

                       
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
