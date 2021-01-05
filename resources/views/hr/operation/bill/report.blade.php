
<button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('bill-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
<button class="btn btn-sm btn-outline-success pull-right hidden-print">
   Check All <input type="checkbox" id="checkAll"/>
</button>
<div id="bill-print">
    <style>
        .signature{
            display: none;
        }
        @media print {
            
            .pagebreak {
                page-break-before: always !important;
            }
            .disburse-button{
                display: none;
            }
            .signature{
                display: block;
            }
        }
        .flex-chunk{
            min-width: 40px;margin-right: 2px;border-right: 1px solid;padding-right: 2px;
        }
        .flex-chunk:last-child{
            margin-right: 0px;border-right: 0px solid;padding-right: 0px;
        }
        
    </style>
    @php
        $locations = location_by_id();
        $locationId = $input['location'];
        $getLocation = $locations[$locationId]['hr_location_name']??'';
        $getUnit = unit_by_id();
        $fromDate = date('d-m-Y', strtotime($input['from_date']));
        $toDate = date('d-m-Y', strtotime($input['to_date']));
    @endphp
    <form class="" role="form" id="billReport" method="get" action="#">
        <input type="hidden" value="{{ $input['from_date'] }}" name="from_date">
        <input type="hidden" value="{{ $input['to_date'] }}" name="to_date">
        <input type="hidden" value="{{ $input['bill_type']}}" name="bill_type">
        <input type="hidden" value="{{ $input['subSection'] }}" name="subSection">
        <input type="hidden" value="{{ $input['section'] }}" name="section">
        <input type="hidden" value="{{ $input['department'] }}" name="department">
        <input type="hidden" value="{{ $input['area']}}" name="area">
        @csrf
        @foreach($uniqueUnit as $key=>$unit)
            @php
                $pageKey = 0;
            @endphp
            @foreach($getBillDataSet as $key=>$lists)
                @php
                    $pageKey += 1;
                    $getUnitHead = $getUnit[$unit]['hr_unit_name_bn']??'';
                @endphp
                
                <div class="panel panel-info">
                    
                    <div class="panel-body">

                        <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                            <tr>
                                <td style="width:30%">
                                    
                                    <p style="margin:0;padding: 0"><strong>&nbsp;পৃষ্ঠা নংঃ </strong>
                                        {{ Custom::engToBnConvert($pageKey) }}
                                    </p>
                                    
                                </td>
                                
                                <td>
                                    <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:14px;">
                                        {{ $getUnitHead }}
                                    </h3>
                                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:12px;">
                                        @if($input['bill_type'] == 1)
                                        টিফিন
                                        @elseif($input['bill_type'] == 2)
                                        ডিনার
                                        @else
                                        টিফিন / ডিনার 
                                        @endif 
                                        বিল
                                        <br/>
                                        তারিখ: {{ Custom::engToBnConvert($fromDate) }} থেকে {{ Custom::engToBnConvert($toDate) }}
                                        
                                    </h5>
                                </td>
                                <td width="0%"> &nbsp;</td>
                                <td style="width:30%" style="text-align: right;">
                                    
                                    
                                </td>
                            </tr>
                        </table>

                        <table class="table table-head" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen" cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead>
                                <tr style="color:hotpink">
                                    <th style="color:lightseagreen" width="10">ক্রমিক নং</th>
                                    <th width="100" style="width: 225px;">নাম ও 
                                        <br/> যোগদানের তারিখ</th>
                                    <th width="200">পদবি  ও গ্রেড</th>
                                    <th width="120">ইআরপি ও ওরাকল আইডি</th>
                                    <th width="120">তারিখ ও টাকা </th>
                                    <th width="180">ইনটাইম - আউটটাইম</th>
                                    <th width="120">দিন</th>
                                    <th width="120">বিল</th>
                                    <th width="120">মোট দেয় টাকার পরিমান</th>
                                    <th class="signature" width="80" >দস্তখত</th>
                                    <th class="disburse-button" width="80">
                                        <input type="checkbox" class="checkBoxGroup"onclick="checkAllGroup(this)" id="{{ $pageKey }}"/>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $j=1; $total = 0;?>
                                @foreach($lists as $k=>$list)
                                    @if($list->as_unit_id == $unit)
                                        <tr>
                                            <td style="text-align: center;">{{ Custom::engToBnConvert($j) }}</td>
                                            <td>
                                                <p style="margin:0;padding:0;">{{ $list->hr_bn_associate_name }}</p>
                                                <p style="margin:0;padding:0;">
                                                    @php
                                                        $doj = date('d-m-Y', strtotime($list->as_doj));
                                                    @endphp
                                                    {{ Custom::engToBnConvert($doj) }}
                                                </p>
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0;">
                                                    {{ $designation[$list->as_designation_id]['hr_designation_name_bn']}}
                                                    @if($list->as_ot == 0)
                                                    - {{ $section[$list->as_section_id]['hr_section_name_bn']??''}}
                                                    @endif 
                                                </p>
                                                @if(isset($designation[$list->as_designation_id]))
                                                    @if($designation[$list->as_designation_id]['hr_designation_grade'] > 0 || $designation[$list->as_designation_id]['hr_designation_grade'] != null)
                                                    <p style="margin:0;padding:0">গ্রেডঃ {{ eng_to_bn($designation[$list->as_designation_id]['hr_designation_grade'])}}</p>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                                                    {{ $list->associate_id }}
                                                </p>
                                                <p style="font-size:11px;margin:0;padding:0;color:blueviolet">
                                                    {{ $list->as_oracle_code }}
                                                </p>
                                            </td>
                                            <td>
                                                <div class="flex-content" style="display: block; height: 100%; border: 0;">
                                                    @if(isset($getBillLists[$list->as_id]))
                                                    @foreach($getBillLists[$list->as_id]->chunk(2) as $billLists)
                                                        <div class="flex-chunk1">
                                                        @foreach($billLists as $dateList)
                                                        <p style="margin:0;padding:0" >
                                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap; {{ $dateList->pay_status ==0?'color:hotpink':'' }}" >
                                                                @php
                                                                    $singDate = date('d-m-Y', strtotime($dateList->bill_date));
                                                                @endphp
                                                                {{ Custom::engToBnConvert($singDate) }}
                                                            </span>
                                                            <span style ="text-align: right;width: 10%; float: left;white-space: wrap; {{ $dateList->pay_status ==0?'color:hotpink':'' }}" >=
                                                            </span>
                                                            <span style="text-align: right;width: 25%; float: right;  white-space: wrap; {{ $dateList->pay_status ==0?'color:hotpink':'' }}" >
                                                                <font > {{ Custom::engToBnConvert($dateList->amount) }}</font>
                                                            </span>

                                                        </p>
                                                        @endforeach
                                                        </div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex-content" style="display: block; height: 100%; border: 0;">
                                                    @if(isset($getBillLists[$list->as_id]))
                                                    @foreach($getBillLists[$list->as_id] as $dateList)
                                                        @if(isset($attendance[$list->as_id][$dateList->bill_date]))
                                                        <p style="margin:0;padding:0" >
                                                            
                                                            <span style="width: 40%; text-align: left; white-space: wrap; float: left;" >
                                                                <font > {{ $attendance[$list->as_id][$dateList->bill_date]->in_time == null?'null':Custom::engToBnConvert(date('H:i',strtotime($attendance[$list->as_id][$dateList->bill_date]->in_time))) }}</font>
                                                            </span>
                                                            <span style ="width: 20%; text-align: left; white-space: wrap; float: left;" > -
                                                            </span>
                                                            <span style="width: 40%; text-align: left; float: left; white-space: wrap;" >
                                                                <font > {{ Custom::engToBnConvert(date('H:i',strtotime($attendance[$list->as_id][$dateList->bill_date]->out_time))) }}</font>
                                                            </span>
                                                        </p>
                                                        @endif
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দিন
                                                    </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink;" > {{ Custom::engToBnConvert($list->totalDay) }}</font>
                                                    </span>

                                                </p>
                                                @if($input['pay_status'] != 0)
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">প্রদিত দিন  </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink"> {{ Custom::engToBnConvert($list->totalDay - $list->dueDay) }}</font>
                                                    </span>
                                                </p>
                                                @endif
                                                {{-- <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($list->dueDay)}}</font>
                                                    </span>
                                                </p> --}}
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0">

                                                      <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট বিল </span>
                                                      <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                      <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink"> {{ Custom::engToBnConvert($list->totalAmount) }}</font>
                                                     </span>
                                                </p>
                                                @if($input['pay_status'] != 0)
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">প্রদিত বিল </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert($list->totalAmount - $list->dueAmount) }}</font>
                                                    </span>
                                                </p>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                {{ Custom::engToBnConvert(bn_money($list->dueAmount)) }}
                                                
                                            </td>
                                            <td class="signature"></td>
                                            <td class="disburse-button" id="{{ $j }}-{{ $list->as_id }}">
                                                <input type='checkbox' class="pay-{{$pageKey}}" value="{{ $list->as_id }}" name='pay_id[]'/>
                                                @if($list->dueAmount > 0)
                                                    {{-- <a data-id="{{ $j }}-{{ $list->as_id }}" class="btn btn-primary btn-sm disbursed_bill text-white" rel='tooltip' data-eaid="{{ $list->associate_id }}" data-asid="{{ $list->as_id }}" data-name="{{ $list->hr_bn_associate_name }}" data-post="{{ $designation[$list->as_designation_id]['hr_designation_name_bn']}}" data-from="{{ $input['from_date'] }}" data-to="{{ $input['to_date'] }}" data-amount="{{ $list->dueAmount }}" data-tooltip-location='top' data-tooltip='বিতরণ করুন' > বিতরণ </a> --}}
                                                
                                                @endif
                                            </td>
                                        </tr>
                                        <?php $j++; $total = $total+$list->dueAmount; ?>
                                    @endif
                                @endforeach
                                @if(count($lists) > 0)
                                <tr>
                                    <td colspan="8" style="text-align: right">মোট দেয় টাকার</td>
                                    <td style="text-align: center;">{{ Custom::engToBnConvert(bn_money($total)) }}</td>
                                    <td></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        

                    </div>
                </div>
                @if(count($getBillDataSet) != $pageKey)
                    @if(count($getBillDataSet) != 1)
                    <div class="pagebreak"> </div>
                    @endif
                @endif
            @endforeach
        @endforeach
        @if(isset($pageHead))
            <div id="unit-info">
                <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                    <tr>
                        <td style="width:25%; text-align:right;">
                            
                        </td>
                        <td style="width:25%">
                            <p style="margin:0;padding: 0"><strong>মোট কর্মী/কর্মচারীঃ </strong>
                                {{ Custom::engToBnConvert($pageHead['totalEmployees']) }}
                            </p>
                            
                        </td>
                        <td style="width:30%;">
                            <p style="margin:0;padding: 0"><strong>সর্বমোট টাকার পরিমান: </strong>
                                {{ Custom::engToBnConvert(bn_money($pageHead['totalBill'])) }}
                            </p>
                            
                        </td>
                        
                        <td style="width:20%; text-align:right;">
                            
                        </td>
                        
                    </tr>
                </table>
            </div>
        @endif
        <br>
        <div class="row">
            <div class="col form-group text-right disburse-button" id="review-btn">
                <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit"><i class="fa fa-eye"></i> Review</button>
            </div>
        </div>
    </form>
    
</div>
{{-- modal --}}
<div class="item_details_section">
    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
      <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
        <div class="fade-box-details fade-box">
          <div class="inner_gray clearfix">
            <div class="inner_gray_text text-center" id="heading">
             <h3 class="no_margin text-white">টিফিন / ডিনার বিল বিতরণ</h3>   
            </div>
            <div class="inner_gray_close_button">
              <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
            </div>
          </div>

          <div class="inner_body" id="modal-details-content" style="display: none">
            <div class="inner_body_content" id="body_result_section">
               
            </div>
            <div class="inner_buttons">
              <a class="cancel_modal_button cancel_details btn btn-sm btn-danger" role="button"> Cancel </a>
              <button class="okay_modal_button btn btn-sm btn-primary hidden-print" onclick="printDiv('bill-review-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
              <button class="okay_modal_button confirm-disbursed" id="confirm-disbursed" type="submit" tabindex="0">
                Confirm & Pay
              </button>
             
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<script type="text/javascript">
    function printDiv(divName)
    { 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }

    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    detailsheight = $(".item_details_dialog").css("min-height", "115px");

    $('#billReport').on('submit', function(e) {
        e.preventDefault();
        $("#body_result_section").html('<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>');
        var form = $("#billReport");
        
        /*Show the dialog overlay-modal*/
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        // ajax call
        $.ajax({
            url: '{{ url("/hr/operation/review-tiffin-dinner-bill")}}',
            type: "POST",
            data: form.serialize(),
            success: function(response){
                // console.log(response)
                if(response === 'error'){
                    $.notify('Something wrong, please close the modal and try again', 'error');
                }else if(response === 'warning'){
                    $.notify('Something wrong, please select employee and try again', 'error');
                }else{
                    setTimeout(function(){
                        $("#body_result_section").html(response);
                    }, 1000);
                }
            }
        });
        /*Animate Dialog*/
        $(".show_item_details_modal").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "80%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });

    $('#confirm-disbursed').on('click', function(e) {
        $("#confirm-disbursed").hide();
        var form = $("#billReport");
        // ajax call
        $.ajax({
            url: '{{ url("/hr/operation/pay-tiffin-dinner-bill")}}',
            type: "POST",
            data: form.serialize(),
            success: function(response){
                console.log(response)
                if(response.type === 'error'){
                    $("#confirm-disbursed").show();
                }else{
                    $("#review-btn").html('Already Payment Selected Employee');
                }

                $.notify(response.msg, response.type);
            }
        });
        
    });

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
    $('#checkAll').click(function(){
        var checked =$(this).prop('checked');
        var selectemp = 0;
        if(!checked) {
            selectemp = $('input:checkbox:checked').length;
            
        } else {
            selectemp = $('input:checkbox:not(:checked)').length;
        }
        $('input:checkbox').prop('checked', checked);
    });
    $('body').on('click', 'input:checkbox', function() {
        if(!this.checked) {
            $('#checkAll').prop('checked', false);
        }
        else {
            var numChecked = $('input:checkbox:checked:not(#checkAll)').length;
            var numTotal = $('input:checkbox:not(#checkAll)').length;
            if(numTotal == numChecked) {
                $('#checkAll').prop('checked', true);
            }
        }
        
    });
    function checkAllGroup(val){
        var id = $(val).attr('id')
      if($(val).is(':checked')){
        $('.pay-'+id).each(function() {
            $(this).prop("checked", true);
        });
      }else{
        $('.pay-'+id).each(function() {
            $(this).prop("checked", false);
        });
      }
    }
</script>
