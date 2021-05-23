
<button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('bill-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
@php
    $urldata = http_build_query($input) . "\n";
@endphp
<a href='{{ url("hr/operation/filter-wise-bill-announcement-sheet?$urldata&output=excel")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 20px; left: 80px;"><i class="fa fa-file-excel-o"></i></a>
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
        .modal-footer {
            position: fixed;
            width: 100%;
            bottom: 0;
            overflow: hidden;
            background: #fff;
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
        <input type="hidden" value="{{ $input['date_type']}}" name="date_type">
        <input type="hidden" value="{{ $input['month_year']}}" name="month_year">
        <input type="hidden" value="{{ $input['pay_status']}}" name="pay_status">
        @csrf
        @php
            $pageKey = 0;
        @endphp
        @foreach($uniqueUnit as $key=>$unit)
            
            @foreach($getBillDataSet as $key=>$lists)
                @if(count($lists) > 0)
                    @php
                        ++$pageKey;
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
                                            টিফিন বিল
                                            @elseif($input['bill_type'] == 2)
                                            @if($unit == 3)
                                            নাইট ভাতা
                                            @else
                                            ডিনার বিল
                                            @endif
                                            @elseif($input['bill_type'] == 3)
                                            লাঞ্চ বিল
                                            @elseif($input['bill_type'] == 4)
                                            ইফতার বিল
                                            @endif 
                                            
                                            <br/>
                                            @if($input['date_type'] == 'range')
                                            তারিখ: {{ Custom::engToBnConvert($fromDate) }} থেকে {{ Custom::engToBnConvert($toDate) }}
                                            @elseif($input['date_type'] == 'month')
                                            মাসঃ {{ date_to_bn_month($input['month_year']) }}
                                            @endif
                                            
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
        @if(auth()->user()->canany(['Bill Operation']))
        <div class="row">
            <div class="col form-group text-right disburse-button" id="review-btn">
                <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit"><i class="fa fa-eye"></i> Review</button>
            </div>
        </div>
        @endif
    </form>
    
</div>

<div class="modal right fade" id="right_modal_lg-group" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg-group">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
            <i class="las la-chevron-left"></i>
        </a>
        <h5 class="modal-title right-modal-title text-center" id="modal-title-right-group"> বিল বিতরণ </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-content-result content-result" id="body_result_section">
            
        </div>
      </div>
      <div class="modal-footer">
        <div class="inner_buttons">
          <a class=" prev_btn btn btn-outline-danger btn-sm" data-toggle="tooltip" data-dismiss="modal"><i class="las la-times"></i> Cancel </a>
          <button class=" btn btn-sm btn-outline-primary hidden-print" onclick="printDiv('bill-review-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> Print</button>

          <button class=" btn btn-sm btn-outline-success confirm-disbursed" id="confirm-disbursed" type="submit" tabindex="0">
           <i class="las la-check"></i> Confirm & Pay
          </button>
         
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    
    $('#billReport').on('submit', function(e) {
        e.preventDefault();
        $("#body_result_section").html('<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>');
        var form = $("#billReport");
        // console.log(form.serialize())
        $('#right_modal_lg-group').modal('show');
        // ajax call
        $.ajax({
            url: '{{ url("/hr/operation/review-bill-announcement")}}',
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
        
    });

    $('#confirm-disbursed').on('click', function(e) {
        $("#confirm-disbursed").hide();
        var form = $("#billReport");
        // ajax call
        $.ajax({
            url: '{{ url("/hr/operation/pay-bill-announcement")}}',
            type: "POST",
            data: form.serialize(),
            success: function(response){
                // console.log(response)
                if(response.type === 'error'){
                    $("#confirm-disbursed").show();
                }else{
                    $("#review-btn").html('Already Payment Selected Employee');
                }

                $.notify(response.msg, response.type);
            }
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
