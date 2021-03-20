   
<div id="bill-print" >
    <div class="hide" style="text-align: center;">
        <h1 style="font-size: 25pt!important;font-weight: bold;">{{$un}}</h1>
        <h3 style="font-size: 13pt!important;font-weight:bold;">Yearly Increment (For the month of {{date('M, Y', strtotime($date))}}) </h3>
    </div>
             
    <table id="increment-table" class="table table-head table-hover" style="border:1px solid #ccc;">
        <thead>
            
            <tr style="text-align: center;font-weight:bold;" >
                <th rowspan="2" > <b>Sl.</b></th>
                <th rowspan="2"><b>Associate ID</b></th>
                <th rowspan="2"><b>Oracle ID</b></th>
                <th rowspan="2"><b>Name</b></th>
                <th rowspan="2"><b>Designation</b></th>
                <th rowspan="2"><b>Department</b></th>
                <th rowspan="2"><b>Section</b></th>
                <th rowspan="2"><b>DOJ</b></th>
                <th rowspan="2"><b>Present Salary</b></th>
                <th colspan="2" style="white-space: nowrap;"><b>Last Increment</b></th>
                <th colspan="4" style="white-space: nowrap;"><b>Proposed Increment</b></th>
            </tr>
            <tr>
                <th ><b>Amount</b></th>
                <th ><b>Date</b></th>
                <th ><b>Amount</b></th>
                <th ><b>%</b></th>
                <th ><b>Gross</b></th>
                <th ><b>Designation</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $k => $list)
                <tr >
                    <td style="text-align: center;">{{ ($k+1) }}</td>
                    <td>
                        {{ $list->associate_id }}
                    </td>
                    <td>{{ $list->as_oracle_code }}</td>
                    
                    <td>
                        {{ $list->as_name }}
                        
                    </td>
                    <td>
                        {{ $designation[$list->as_designation_id]['hr_designation_name']}}
                       
                    </td>
                    
                    <td>
                       {{ $department[$list->as_department_id]['hr_department_name']??''}}
                    </td>
                    <td>
                        {{ $section[$list->as_section_id]['hr_section_name']??''}}
                        
                    </td>
                    <td>
                        <p style="margin:0;padding:0;white-space: nowrap;">
                            @php
                                $doj = date('d/m/Y', strtotime($list->as_doj));
                            @endphp
                            {{ $doj }}
                        </p>
                    </td>

                    
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
                                {{date('d/m/Y',strtotime($incr->effective_date))}}
                            @endif
                        @endif
                    </td>
                    <td style="text-align: center;">
                        
                    </td>

                    <td style="text-align: center;">
                        <span class="proposed-percent"></span>
                    </td>
                    <td style="text-align: right;">
                        <span class="proposed-gross"></span>
                    </td>
                    <td >
                        
                    </td>
                </tr>
                    
            @endforeach
            
        </tbody>
        @if(count($data) > 0)
        
        @else
            <tr><td colspan="15" class="text-center">No eligible employee found!</td></tr>
        @endif
    </table>
</div> 

        


