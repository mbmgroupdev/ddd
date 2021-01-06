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
        padding: 3px 5px;
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
                <form id="increment-action" action="{{url('hr/payroll/increment-action')}}" method="post" > 
                    @csrf 
                    <div class="row justify-content-between print-hidden">
                        <div class="col-sm-12 text-center ">
                            <button type="button" class="btn btn-sm btn-primary hidden-print print-hidden" onclick="printDiv('increment-action')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report" style="position: absolute;top: 10px;left: 15px;"><i class="las la-print"></i> </button>
                            <strong style="font-size: 18px;">Increment Projection</strong>
                            <p style="font-weight: bold;" >Employee: <span class="total-employee">{{count($data)}}</span> &nbsp;&nbsp; Amount : <span class="total-amount">{{collect($data)->sum('inc')}}</span></p>
                            <br>
                        </div>
                        <div class="col-sm-2 print-hidden">
                            <div class="form-group has-float-label selet-search-group" >
                                <select id="report_type" name="type" class="form-control" >
                                    <option value="All" @if($request->type == 'all')selected @endif>Select Type</option>
                                    <option value="pending" @if($request->type == 'pending')selected @endif>Pending</option>
                                    <option value="running" @if($request->type == 'running')selected @endif>Running</option>
                                </select>
                                <label for="report_type">Report Type</label>
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
                                <input type="date" class="  form-control" id="effective_date" name="effective_date" value="{{$effective_date->toDateString()}}"  />
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
                                <input type="text" class="inc_percent text-center form-control" id="inc_percent" name="inc_percent" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value="5" autocomplete="off"  />
                                <label for="inc_percent">Increment %</label>
                            </div>
                        </div>
                    </div> 
                    <div id="bill-print" style="height: 350px;overflow-y: auto;">
                    
                    
                        <style>
                            .signature{
                                display: none;
                            }
                            @media print {
                                
                                .pagebreak {
                                    page-break-before: always !important;
                                }
                                @page {
                                    size: landscape;
                                }
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
                                 
                        <table id="increment-table" class="table table-head table-hover" style="width:100%;border:1px solid #ccc;font-size:9px;position: relative;" cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead>
                                
                                <tr style="text-align: center;vertical-align: middle !important;" >
                                    <th width="10" >Sl.</th>
                                    <th width="150" style="width: 150px;" >Name</th>
                                    <th width="200" style="width: 250px;" >Designation & Grade</th>
                                    <th width="" >Associate ID</th>
                                    <th width="180" style="width: 150px;">Section & Department</th>
                                    <th width="" >Eligible</th>
                                    <th width="" >Salary</th>
                                    <th width="" >Increment Amount</th>
                                    <th width="" >Applied Date</th>
                                    <th class="disburse-button" width="40" >
                                        <input type="checkbox" class="checkBoxGroup" onclick="checkAllGroup(this)" id="check-all" checked />
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $k => $list)
                                    <tr class="row_{{ $list->associate_id }} row_{{ $list->as_oracle_code }}">
                                        <td style="text-align: center;">
                                            {{ ($k+1) }}
                                            @if(in_array($list->associate_id, $gazette))
                                                <span class="badge badge-primary" data-toggle="tooltip" data-placement="top" title="" data-original-title="According to gazette!">G</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="yearly-activity text-primary" style="margin:0;padding:0;width: 150px;cursor: pointer;font-weight: bold;" data-id="{{ $list->as_id}}" data-eaid="{{ $list->associate_id }}" data-ename="{{ $list->as_name }}" data-edesign="{{ $designation[$list->as_designation_id]['hr_designation_name']}}" data-image="{{emp_profile_picture($list)}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Preview yearly activity report">{{ $list->as_name }}</p>
                                            <p style="margin:0;padding:0;">
                                                @php
                                                    $doj = date('d-m-Y', strtotime($list->as_doj));
                                                @endphp
                                                {{ $doj }}
                                            </p>
                                        </td>
                                        <td>
                                            <p style="margin:0;padding:0;">
                                                {{ $designation[$list->as_designation_id]['hr_designation_name']}}
                                                 
                                            </p>
                                           
                                                @if($designation[$list->as_designation_id]['hr_designation_grade'] > 0 )
                                                <b style="margin:0;padding:0">Grade: {{ $designation[$list->as_designation_id]['hr_designation_grade']}}</b>
                                                @endif
                                        </td>
                                        <td>
                                            <b style="font-size:14px;margin:0;padding:0;">
                                                {{ $list->associate_id }}
                                            </b>
                                            <p style="font-size:11px;margin:0;padding:0;color:blueviolet">
                                                {{ $list->as_oracle_code }}
                                            </p>
                                        </td>
                                        <td>
                                            @if($list->as_emp_type_id == 3)
                                            {{ $section[$list->as_section_id]['hr_section_name']??''}}<br>
                                            @endif
                                            <b>{{ $department[$list->as_department_id]['hr_department_name']??''}}</b>
                                        </td>
                                        <td style="text-align: center;">
                                            @if(in_array($list->associate_id, $gazette))
                                                {{date('M, y', strtotime($date))}} 
                                            @elseif(date('n', strtotime($date)) == $list->doj_month)
                                                {{date('M, y', strtotime($date))}}
                                            @elseif(date('n', strtotime($date)) < $list->doj_month )
                                                {{date('M', strtotime($list->as_doj))}}, {{date('y', strtotime($date. '-1 year'))}}
                                            @else
                                                {{date('M', strtotime($list->as_doj))}}, {{date('y', strtotime($date))}}
                                            @endif
                                        </td>
                                        <td style="text-align: right;padding-right:10px;">
                                            {{$list->ben_current_salary}}
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="hidden" name="increment['{{ $list->associate_id }}']['salary']" value="{{ $list->ben_current_salary }}">
                                            <input id="inc_{{$list->associate_id}}" type="text" name="increment['{{ $list->associate_id }}']['amount']" class="form-control text-center increment-amount " style="width:80px;margin: 0 auto;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"  data-salary="{{ $list->ben_current_salary }}" value="{{ceil($list->inc)}}" data-checked="1">
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="date" name="increment[{{ $list->associate_id }}][date]" class="form-control" style="width:110px;" value="{{$effective_date->toDateString()}}">
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
                                        <td colspan="7" style="text-align: right;position: sticky;background: #fff;bottom: 0;"><b>Total</b></td>
                                        <td style="text-align: center;position: sticky;background: #fff;bottom: 0;">
                                            <b class="total-amount">{{collect($data)->sum('inc')}}</b>
                                        </td>
                                        <td class="text-center" colspan="2" style="position: sticky;background: #fff;bottom: 0;">
                                            <input type="submit" name="" value="Save Increment" class="btn btn-primary">
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr><td colspan="10" class="text-center">No eligible employee found!</td></tr>
                            @endif
                        </table>
                    </div> 
                </form>

            </div>
        </div>
        
         
  {{--   </form> --}}
    
</div>


