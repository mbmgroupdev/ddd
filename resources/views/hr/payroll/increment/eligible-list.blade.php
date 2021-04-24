<style type="text/css">
    input[type=date]{
        position: relative;
    }
    input[type="date"]::-webkit-inner-spin-button,
    input[type="date"]::-webkit-calendar-picker-indicator {
        position: absolute;
        right:0;
        -webkit-appearance: none;
    }
    .table td {
        padding: 3px;
    }
    table.table-head th{
        top: -1px;
        z-index: 10;
        vertical-align: middle !important;
    }
    .table th {
        padding: .5rem;
        vertical-align: middle !important;
    }
</style>

    
    {{-- <form class="" role="form" id="billReport" method="get" action="#"> --}}
        
        <div class="panel panel-info" >
            <div class="panel-body" >
                <form class="eligible-data" id="increment-action" action="{{url('hr/payroll/increment-action')}}" method="post" > 
                    @csrf 
                    <div class="row justify-content-between print-hidden">
                        <div class="col-sm-12 text-center ">
                            <button type="button" class="btn btn-sm btn-primary hidden-print print-hidden" onclick="printDiv('bill-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report" style="position: absolute;top: 10px;left: 15px;"><i class="las la-print"></i> </button>
                            @php
                                $urldata = http_build_query($input) . "\n";
                            @endphp
                            <a href='{{ url("hr/payroll/increment-eligible?$urldata&export=excel")}}' target="_blank" class="btn btn-sm btn-primary hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 10px; left: 50px;"><i class="fa fa-file-excel-o"></i></a>
                            <h1 style="font-size: 20pt!important;font-weight: bold;">{{$un}}</h1>
                            <h3 style="font-size: 10pt!important;font-weight:bold;">Yearly Increment (For the month of {{date('M, Y', strtotime($date))}}) </h3>
                            <p style="font-weight: bold;" >No of Employee: <span class="total-employee">{{count($data)}}</span> &nbsp;&nbsp; Total Amount : <span class="total-amount">0</span></p>
                            <br>
                        </div>
                        <div class="col-sm-2 print-hidden">
                            <div class="form-group has-float-label selet-search-group" >
                                <select id="report_type" name="type" class="form-control" >
                                    <option>Select Type</option>
                                    <option value="All" @if($request->type == 'all')selected @endif>All</option>
                                    <option value="pending" @if($request->type == 'pending')selected @endif>Pending</option>
                                    <option value="running" @if($request->type == 'running')selected @endif>Running</option>
                                </select>
                                <label for="report_type">Filter</label>
                            </div>
                        </div>
                        <div class="col-sm-3 print-hidden">
                            <div class="form-group has-float-label" >
                                <input type="text" class="inc_percent datepicker form-control" id="AssociateSearch" name="AssociateSearch" placeholder="Enter associate id/oracle id"  autocomplete="off"  />
                                <label for="AssociateSearch">Search Employee</label>
                            </div>
                        </div>
                        <div class="col-sm-3 print-hidden">
                            <div class="form-group has-float-label" >
                                <input type="date" class="  form-control" id="effective_date" name="effective_date" value="{{$effective_date->format('Y-m-d')}}"  />
                                <label for="effective_date">Effective Date</label>
                            </div>
                            
                        </div>
                        <div class="col-sm-2 print-hidden">
                            <div class="form-group has-float-label selet-search-group" >
                                <select id="increment_type" name="increment_type" class="form-control" >
                                    <option value="2" selected>Yearly</option>
                                    <option value="3" >Special</option>
                                </select>
                                <label for="increment_type">Increment Type</label>
                            </div>
                        </div>
                        <div class="col-sm-2 print-hidden">
                            <div class="form-group has-float-label" data-toggle="tooltip" data-placement="top" title="" data-original-title="Changing any value will recalculate increment amount of each employee!">
                                <input type="text" class="inc_percent text-center form-control" id="inc_percent" name="inc_percent" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value="" autocomplete="off"  />
                                <label for="inc_percent">Increment %</label>
                            </div>
                        </div>
                    </div> 
                    <div id="bill-print" style="height: 350px;overflow-y: auto;">
                    
                    
                        <style>
                            .designation-boss .select2-container .select2-selection--single {
                                height: 25px!important;
                            }
                            .designation-boss .select2-container--default .select2-selection--single .select2-selection__arrow b {
                                top: 10px;

                            }
                            .designation-boss{
                                height: 25px !important;
                            }
                            .designation-boss .select2-container--default .select2-selection--single .select2-selection__rendered{
                                height: 25px !important;
                            }
                            .designation-boss .select2-container--default .select2-selection--single .select2-selection__arrow {
                                    height: 25px;
                                }
                            .designation-boss .select2-container--default .select2-selection--single .select2-selection__rendered {
                                color: #414141;
                                line-height: 25px;
                            }
                            *{
                                font-size: 11px;
                            }
                            .signature{
                                display: none;
                            }
                            .table td, table.table-head th {
                                padding: 3px !important;
                                font-size: 11px;
                            }
                            table.table-head th, .table thead th {
                                top: -1px;
                                z-index: 10;
                                vertical-align: middle !important;
                            }
                            .table th {
                                padding: 3px !important;
                                vertical-align: middle !important;
                                border-bottom: 1px solid #dee2e6;
                                border-top: 1px solid #dee2e6;
                                border-bottom: 1px solid #dee2e6;
                                text-align: center;
                            }
                            table.table-head th{
                                box-shadow: none;
                                background: #ececec;
                            }
                            @media print {
                                
                                .pagebreak {
                                    page-break-before: always !important;
                                }
                                @page {
                                    size: landscape;
                                }
                                .designation-boss{display: none;}
                                .print-hidden{display: none;}
                                .badge{display: none;}
                                input{
                                    border: 0 !important;
                                    text-align: right;
                                    max-width: 80px;
                                }
                                tfoot{
                                    display: none;
                                }
                                * {
                                    font-size: 11px !important;
                                    font-weight: normal;
                                }
                                .disburse-button{
                                    display: none;
                                }
                                .signature{
                                    display: block;
                                }
                                input[type="date"]::-webkit-inner-spin-button,
                                input[type="date"]::-webkit-calendar-picker-indicator {
                                    display: none;
                                }
                                @import url(https://fonts.googleapis.com/css?family=Poppins:200,200i,300,400,500,600,700,800,900&amp;display=swap);
                                body {
                                    font-family: Poppins,sans-serif;
                                }
                                tfoot td{
                                    position: relative !important;
                                }
                                
                            }
                            .flex-chunk{
                                min-width: 40px;margin-right: 2px;border-right: 1px solid;padding-right: 2px;
                            }
                            .flex-chunk:last-child{
                                margin-right: 0px;border-right: 0px solid;padding-right: 0px;
                            }
                            
                        </style> 
                        <div class="hide" style="text-align: center;">
                            <h1 style="font-size: 25pt!important;font-weight: bold;">{{$un}}</h1>
                            <h3 style="font-size: 13pt!important;font-weight:bold;">Yearly Increment (For the month of {{date('M, Y', strtotime($date))}}) </h3>
                            {{-- <p style="font-weight: bold;" >Employee: <span class="total-employee">{{count($data)}}</span> &nbsp;&nbsp; Amount : <span class="total-amount">0</span></p> --}}
                            <br>
                            <br>
                        </div>
                                 
                        <table id="increment-table" class="table table-head table-hover" style="width:100%;border:1px solid #ccc;font-size:9px;position: relative;" cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead>
                                
                                <tr style="text-align: center;" >
                                    <th rowspan="2" width="10" >Sl.</th>
                                    <th rowspan="2" width="" >Employee <br> ID</th>
                                    <th rowspan="2">Name</th>
                                    <th rowspan="2">Designation</th>
                                    <th rowspan="2">Department</th>
                                    <th rowspan="2">Section</th>
                                    <th rowspan="2">DOJ</th>
                                    <th rowspan="2">Present <br> Salary</th>
                                    <th colspan="2" style="white-space: nowrap;">Last Increment</th>
                                    <th colspan="4" style="white-space: nowrap;">Proposed Increment</th>
                                    <th rowspan="2" class="disburse-button" width="40" >
                                        <input type="checkbox" class="checkBoxGroup" onclick="checkAllGroup(this)" id="check-all" checked />
                                    </th>
                                </tr>
                                <tr>
                                    <th style="top:25px;">Amount</th>
                                    <th style="top:25px;white-space: nowrap;">Date</th>
                                    <th style="top:25px;width:60px;">Amount</th>
                                    <th style="top:25px;width:40px;">%</th>
                                    <th style="top:25px;width:60px;">Gross</th>
                                    <th style="top:25px;width:120px;">Designation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $p = 0; @endphp
                                @foreach($data as $k => $list)
                                    <tr class="row_{{ $list->associate_id }} row_{{ $list->as_oracle_code }}">
                                        <td style="text-align: center;">
                                            {{ (++$p) }}
                                            
                                        </td>
                                        <td>
                                            {{-- <b style="font-size:11px;margin:0;padding:0;"> --}}
                                                {{ $list->associate_id }}
                                            {{-- </b> --}}
                                        </td>
                                        
                                        <td>
                                            {{-- <p class="yearly-activity text-primary" style="margin:0;padding:0;cursor: pointer;font-size: 11px;" data-id="{{ $list->as_id}}" data-eaid="{{ $list->associate_id }}" data-ename="{{ $list->as_name }}" data-edesign="{{ $designation[$list->as_designation_id]['hr_designation_name']}}" data-image="{{emp_profile_picture($list)}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Preview yearly activity report"> --}}{{ $list->as_name }}{{-- </p> --}}
                                            
                                        </td>
                                        <td>
                                            {{ $designation[$list->as_designation_id]['hr_designation_name']}}
                                           
                                        </td>
                                        
                                        <td>
                                           {{ $department[$list->as_department_id]['hr_department_name']??''}}
                                        </td>
                                        <td>
                                            {{ $section[$list->as_section_id]['hr_section_name']??''}}<br>
                                            
                                        </td>
                                        <td>
                                            <p style="margin:0;padding:0;white-space: nowrap;">
                                                @php
                                                    $doj = date('d-M-y', strtotime($list->as_doj));
                                                @endphp
                                                {{ $doj }}
                                            </p>
                                        </td>

                                        {{-- <td style="text-align: center;">
                                            @if(in_array($list->associate_id, $gazette))
                                                {{date('M, y', strtotime($date))}} 
                                            @elseif(date('n', strtotime($date)) == $list->doj_month)
                                                {{date('M, y', strtotime($date))}}
                                            @elseif(date('n', strtotime($date)) < $list->doj_month )
                                                {{date('M', strtotime($list->as_doj))}}, {{date('y', strtotime($date. '-1 year'))}}
                                            @else
                                                {{date('M', strtotime($list->as_doj))}}, {{date('y', strtotime($date))}}
                                            @endif
                                        </td> --}}
                                        <td style="text-align: right;padding-right:10px;">
                                            {{$list->ben_current_salary}}
                                        </td>
                                        <td style="text-align: right;">
                                            @php
                                            $incr = '';
                                            if(isset($last_increment[$list->associate_id])){
                                                $incr = $last_increment[$list->associate_id];
                                            }
                                            @endphp
                                            @if($incr != '')
                                                {{$incr->increment_amount}}
                                            @endif
                                        </td>
                                        <td style="text-align: center;white-space: nowrap;">
                                            @if($incr != '')
                                                @if($incr->effective_date)
                                                    {{date('d-M-y',strtotime($incr->effective_date))}}
                                                @endif
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="hidden" name="increment['{{ $list->associate_id }}']['salary']" value="{{ $list->ben_current_salary }}">
                                            <input id="inc_{{$list->associate_id}}" type="text" name="increment['{{ $list->associate_id }}']['amount']" class="form-control text-center increment-amount " style="width:60px;margin:auto;height:25px;font-size: 11px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"  data-salary="{{ $list->ben_current_salary }}" value="" data-checked="1">
                                        </td>

                                        <td style="text-align: center;">
                                            <span class="proposed-percent"></span>
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="proposed-gross"></span>
                                        </td>
                                        <td style="width:100px;vertical-align: middle;">
                                            <input type="hidden" name="increment[{{$list->associate_id}}][prev_desgn]" value="{{$list->as_designation_id}}">
                                            <div class="form-group has-float-label  select-search-group designation-boss" style="    margin-bottom: 0;">
                                            @if($list->as_emp_type_id == 1)
                                                  {{ Form::select('increment['. $list->associate_id.'][desgn]', $management, null, ['placeholder'=>'Select', 'id'=>'gender'.$list->associate_id, 'class'=> 'form-control designation', 'style' => 'height:25px !important;']) }} 
                                            @else
                                                {{ Form::select('increment['. $list->associate_id.'][desgn]', $worker, null, ['placeholder'=>'Select', 'id'=>'gender'.$list->associate_id, 'class'=> 'form-control designation', 'style' => 'height:25px !important;']) }} 
                                            @endif
                                           </div>
                                        </td>
                                           
                                        <td class="disburse-button" id="" style="text-align: center;">
                                            <input type='checkbox' class="checkbox-inc" style="margin: 0 auto;" onclick="checkSingle('{{$list->associate_id}}')"  name="increment[{{ $list->associate_id }}][status]" id="check_{{$list->associate_id}}" checked/>
                                            
                                        </td>
                                    </tr>
                                        
                                @endforeach
                                
                            </tbody>
                            @if(count($data) > 0)
                                <tfoot>
                                    
                                    <tr>
                                        <td colspan="10" style="text-align: right;position: sticky;background: #ececec;bottom: 37px;z-index: 100;"><b>Total</b></td>
                                        <td style="text-align: center;position: sticky;background: #ececec;bottom: 37px;z-index: 100;">
                                            <b class="total-amount">0</b>
                                        </td>
                                        <td class="text-center" colspan="4" style="position: sticky;background: #ececec;bottom: 37px;z-index: 100;">
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="15" class="text-right" style="
                                        position: sticky;background: #fff;bottom: 0;z-index: 100;">
                                            <button id="submission-button" type="button" class="btn btn-primary submission-button">Proceed</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr><td colspan="15" class="text-center">No eligible employee found!</td></tr>
                            @endif
                        </table>
                    </div> 
                </form>

            </div>
        </div>
        
         
  {{--   </form> --}}
    
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.designation').select2();
    });
</script>


