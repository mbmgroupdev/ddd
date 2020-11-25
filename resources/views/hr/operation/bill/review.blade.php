


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
                                        টিফিন / ডিনার বিল 
                                        
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
                                    <th>তারিখ</th>
                                    <th width="80">দিন</th>
                                    <th width="120">মোট দেয় টাকার পরিমান</th>
                                    <th class="" width="80" >দস্তখত</th>
                                    
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
                                                <div class="flex-content" style="display: flex; height: 100%; border: 0;">
                                                    @if(isset($getBillLists[$list->as_id]))
                                                    @foreach($getBillLists[$list->as_id]->chunk(2) as $billLists)
                                                        <div class="flex-chunk">
                                                        @foreach($billLists as $dateList)
                                                        <p style="margin:0;padding:0" >
                                                            <span style="text-align: left; width: 40%; float: left;  white-space: wrap; {{ $dateList->pay_status ==0?'color:hotpink':'' }}" >
                                                                @php
                                                                    $singDate = date('d', strtotime($dateList->bill_date));
                                                                @endphp
                                                                {{ Custom::engToBnConvert($singDate) }}
                                                            </span>
                                                            <span style ="text-align: right;width: 10%; float: left;white-space: wrap; {{ $dateList->pay_status ==0?'color:hotpink':'' }}" >=
                                                            </span>
                                                            <span style="text-align: right;width: 50%; float: right;  white-space: wrap; {{ $dateList->pay_status ==0?'color:hotpink':'' }}" >
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
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দিন
                                                    </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
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
                                    <td colspan="6" style="text-align: right">মোট দেয় টাকার</td>
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
                        <td style="width:25%;">
                            <p style="margin:0;padding: 0"><strong>সর্বমোট টাকার পরিমান: </strong>
                                {{ Custom::engToBnConvert(bn_money($pageHead['totalBill'])) }}
                            </p>
                            
                        </td>
                        
                        <td style="width:25%; text-align:right;">
                            
                        </td>
                        
                    </tr>
                </table>
            </div>
        @endif
        
    </form>
    
</div>

