


<div id="bill-review-print">
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
            .flex {
                display: flex;
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
        $getUnit = unit_by_id();
        $fromDate = date('d-m-Y', strtotime($input['from_date']));
        $toDate = date('d-m-Y', strtotime($input['to_date']));
    @endphp
    <form class="" role="form" id="billReport" method="get" action="#">
        <input type="hidden" value="{{ $input['from_date'] }}" name="from_date">
        <input type="hidden" value="{{ $input['to_date'] }}" name="to_date">
        <input type="hidden" value="{{ $input['bill_type']}}" name="bill_type">
        <input type="hidden" value="{{ $input['date_type']}}" name="date_type">
        <input type="hidden" value="{{ $input['month_year']}}" name="month_year">
        <input type="hidden" value="{{ $input['pay_status']}}" name="pay_status">
        @php
            $pageKey = 0;
        @endphp
        @foreach($uniqueUnit as $key=>$unit)
            
            @foreach($getBillDataSet as $key=>$lists)
                @php
                    ++$pageKey;
                    $getUnitHead = $getUnit[$unit]['hr_unit_name_bn']??'';
                @endphp
                
                <div class="panel panel-info">
                    
                    <div class="panel-body">

                        <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                            <tr>
                                <td style="width:30%">
                                    
                                    <p style="margin:0;padding: 0"><strong>&nbsp;?????????????????? ????????? </strong>
                                        {{ Custom::engToBnConvert($pageKey) }}
                                    </p>
                                    
                                </td>
                                
                                <td>
                                    <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:14px;">
                                        {{ $getUnitHead }}
                                    </h3>
                                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:12px;">
                                        
                                        @if($input['bill_type'] == 1)
                                        ??????????????? ?????????
                                        @elseif($input['bill_type'] == 2)
                                        @if($unit == 3)
                                        ???????????? ????????????
                                        @else
                                        ??????????????? ?????????
                                        @endif
                                        @elseif($input['bill_type'] == 3)
                                        ??????????????? ?????????
                                        @elseif($input['bill_type'] == 4)
                                        ??????????????? ?????????
                                        @endif 
                                        
                                        <br/>

                                        @if($input['date_type'] == 'range')
                                        ???????????????: {{ Custom::engToBnConvert($fromDate) }} ???????????? {{ Custom::engToBnConvert($toDate) }}
                                        @elseif($input['date_type'] == 'month')
                                        ???????????? {{ date_to_bn_month($input['month_year']) }}
                                        @endif
                                        
                                    </h5>
                                </td>
                                <td width="0%"> &nbsp;</td>
                                <td style="width:30%" style="text-align: right;">
                                    
                                    @if(!empty($input['subSection']))
                                        <strong>?????????-???????????????:</strong> {{ $subSection[$input['subSection']]['hr_subsec_name_bn']??'' }}
                                    @elseif(!empty($input['section']))
                                        <strong>???????????????: </strong> {{ $section[$input['section']]['hr_section_name_bn']??'' }}
                                    @elseif(!empty($input['department']))
                                        <strong>????????????????????????????????????: </strong> {{ $department[$input['department']]['hr_department_name_bn']??'' }}
                                    @elseif(!empty($input['area']))
                                        <strong>???????????????: </strong> {{ $area[$input['area']]['hr_area_name_bn']??'' }}
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <table class="table table-head" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen" cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead>
                                <tr style="color:hotpink">
                                    <th style="color:lightseagreen" width="10">?????????????????? ??????</th>
                                    <th width="100" style="width: 205px;">????????? ??? 
                                        <br/> ???????????????????????? ???????????????</th>
                                    <th width="240">????????????  ??? ???????????????</th>
                                    <th width="100">??????????????? ??? ???????????????</th>
                                    <th>???????????????</th>
                                    <th>????????????????????? ???????????????</th>
                                    <th width="70">?????????</th>
                                    <th width="120">????????? ??????????????? ??????????????????</th>
                                    <th class="" width="120" >??????????????????</th>
                                    
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
                                                    <p style="margin:0;padding:0">?????????????????? {{ eng_to_bn($designation[$list->as_designation_id]['hr_designation_grade'])}}</p>
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
                                                <div class="flex-content" style="height: 100%; border: 0; cursor: pointer;">
                                                    @if(isset($getBillLists[$list->as_id]))
                                                    @foreach($getBillLists[$list->as_id]->chunk(5) as $billLists)
                                                        <div class="flex-chunk1 flex">
                                                        @foreach($billLists as $dateList)
                                                        <p style="margin:0;border: 1px solid #ccc; padding: 0px 5px;" >
                                                            @php
                                                                $singDate = date('d', strtotime($dateList->bill_date));
                                                            @endphp
                                                            {{ Custom::engToBnConvert($singDate) }}
                                                            

                                                        </p>
                                                        @endforeach
                                                        </div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                {{ $list->bank_no }}
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0">
                                                    
                                                    <span style="text-align: center;  white-space: wrap;">
                                                        <font style="color:hotpink;" > {{ Custom::engToBnConvert($list->totalDay) }}</font>
                                                    </span>
                                                </p>
                                                
                                            </td>
                                            
                                            <td style="text-align: center;">
                                                {{ Custom::engToBnConvert(bn_money($list->dueAmount)) }}
                                                
                                            </td>
                                            <td class=""></td>
                                            
                                        </tr>
                                        <?php $j++; $total = $total+$list->dueAmount; ?>
                                    @endif
                                @endforeach
                                @if(count($lists) > 0)
                                <tr>
                                    <td colspan="7" style="text-align: right">????????? ????????? ???????????????</td>
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
                            <p style="margin:0;padding: 0"><strong>????????? ???????????????/??????????????????????????? </strong>
                                {{ Custom::engToBnConvert($pageHead['totalEmployees']) }}
                            </p>
                            
                        </td>
                        <td style="width:30%;">
                            <p style="margin:0;padding: 0"><strong>????????????????????? ??????????????? ??????????????????: </strong>
                                {{ Custom::engToBnConvert(bn_money($pageHead['totalBill'])) }}
                            </p>
                            
                        </td>
                        
                        <td style="width:20%; text-align:right;">
                            
                        </td>
                        
                    </tr>
                </table>
            </div>
        @endif
        
    </form>
    
</div>

