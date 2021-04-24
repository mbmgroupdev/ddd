<div id="bill-print" >
    <div class="hide" style="text-align: center;">
        <h1 style="font-size: 25pt!important;font-weight: bold;">{{$un}}</h1>
        <h3 style="font-size: 13pt!important;font-weight:bold;">Increment on Approval </h3>
    </div>
    <table id="increment-table" class="table table-head table-hover" style="width:100%;border:1px solid #ccc;font-size:9px;position: relative;" cellpadding="2" cellspacing="0" border="1" align="center">
        <thead>
            
            <tr style="text-align: center;" >
                <th rowspan="2" >Sl.</th>
                <th rowspan="2" >Employee <br> ID</th>
                <th rowspan="2">Name</th>
                <th rowspan="2">Designation</th>
                <th rowspan="2">Department</th>
                <th rowspan="2">Section</th>
                <th rowspan="2">DOJ</th>
                <th rowspan="2">Present <br> Salary</th>
                <th colspan="2" style="white-space: nowrap;">Last Increment</th>
                <th rowspan="2">Type</th>
                <th colspan="4" style="white-space: nowrap;">{{$set['extype']}}</th>
                <th rowspan="2">Effective <br> Date</th>
                <th rowspan="2" class="disburse-button" width="40" >
                    <input type="checkbox" class="checkBoxGroup" onclick="checkAllGroup(this)" id="check-all"  checked />
                </th>
            </tr>
            <tr>
                <th >Amount</th>
                <th style="white-space: nowrap;">Date</th>
                <th >Amount</th>
                <th >%</th>
                <th >Gross</th>
                <th >Designation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($increment as $k => $list)
                <tr>
                    <td style="text-align: center;">
                        {{ ($k+1) }}        
                    </td>
                    <td>{{ $list->associate_id }}</td>
                    <td>{{ $list->as_name }}</td>
                    <td>{{ $designation[$list->as_designation_id]['hr_designation_name']}}</td>
                    
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

                    <td style="text-align: right;padding-right:10px;">
                        {{$list->current_salary}}
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
                    <td>
                        @if($list->increment_type == 2)
                            Yearly
                        @elseif($list->increment_type == 3)
                            Special
                        @endif
                    </td>
                    
                    <td style="text-align: right;">
                        @php 
                            $proposed = $list->{$set['exfield']}??'';
                            $percent = '';
                            if($proposed){

                                $percent  = round(($proposed/($list->current_salary - 1850))*100,2);
                            }
                        @endphp
                        {{ $proposed }}
                    </td>
                    <td style="text-align: center;">
                        {{$percent}}
                    </td>
                    
                    <td style="text-align: right;">
                        <span class="proposed-gross">
                            @if($proposed)  
                                {{($proposed + $list->current_salary) }}
                            @endif                                 
                        </span>
                    </td>
                    <td style="vertical-align: middle;">
                        @isset($designation[$list->designation_id])
                            <input type="hidden" name="increment[{{$list->associate_id}}][prev_desgn]" value="{{$list->as_designation_id}}">
                            {{ $designation[$list->designation_id]['hr_designation_name']}}
                        @endisset
                    </td>
                    <td style="white-space: nowrap;">
                        @if($list->effective_date)
                        {{date('d-M-y',strtotime($list->effective_date))}}
                        @endif
                    </td>
                </tr>
                    
            @endforeach
            
        </tbody>
    </table>