@extends('hr.layout')
@section('title', 'Maternity Leave Report')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Report </a>
                </li>
                <li class="active">Maternity Leave </li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/operation/maternity-leave/list')}}" target="_blank" class="btn btn-primary pull-right" >List <i class="fa fa-list bigger-120"></i></a>
                </li>
            </ul>
        </div>
        <form class="needs-validation" novalidate role="form" id="activityReport" method="get" action="#"> 
            <div class="panel">
                <div class="panel-body pb-0">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group has-float-label has-required">
                                <input type="month" class="form-control" id="present_date" name="month" placeholder="Y-m" required="required" value="{{ $month }}" autocomplete="off" />
                                <label for="present_date">Month</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                              <button class="btn btn-primary" type="submit" ><i class="fa fa-save"></i> Generate</button>
                        </div>   
                    </div>
                </div>
            </div>
        </form>
        @php
            $unit = unit_by_id();
            $line = line_by_id();
            $floor = floor_by_id();
            $department = department_by_id();
            $designation = designation_by_id();
            $section = section_by_id();
            $subSection = subSection_by_id();
            $area = area_by_id();
        @endphp
        <div class="panel">
            <div class="panel-body">
                <div class="page-header-summery">
                    
                    <h2>Maternity Leave (Approximate) </h2>
                    <h4>Month: <b>{{ \Carbon\Carbon::createFromFormat("Y-m",$month)->format('F, Y') }}</b></h4>
                    <h4>Total Employee: <b>{{count($appoxleave)}}</b></h4>
                    <h4>Total Amount: <b>0</b></h4>
                    <br>
                </div>

                <table class="table table-bordered table-hover table-head">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Photo</th>
                            <th>Associate ID</th>
                            <th>Name & Phone</th>
                            <th>Unit</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Leave From</th>
                            <th>EDD</th>
                            <th>Payment (apprx.)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $payment = 0; @endphp
                        @if(count($appoxleave) > 0)
                            @foreach($appoxleave as $key => $leave)
                            @php $payment += 0; @endphp
                            <tr>
                                <td>{{$key+1}}</td>
                                <td><img src="{{ emp_profile_picture($leave) }}" class='small-image' style="height: 40px; width: auto;"></td>
                                <td><a href='{{ url("hr/recruitment/employee/show/".$leave->associate_id) }}' target="_blank">{{ $leave->associate_id }}</a></td>
                                <td>
                                    <b>{{ $leave->as_name }}</b>
                                    <p>{{ $leave->as_contact }}</p>
                                </td>
                                <td>{{ $unit[$leave->as_unit_id]['hr_unit_name']??'' }}</td>
                                <td>{{ $designation[$leave->as_designation_id]['hr_designation_name']??'' }}</td>
                                <td>{{ $department[$leave->as_department_id]['hr_department_name']??'' }}</td>
                                <td>{{ $leave->leave_from?? '' }}</td>
                                <td>{{ $leave->edd?? '' }}</td>
                                <td>{{ $leave->edd?? '' }}</td>
                                <td>
                                    <a href='{{ url("hr/operation/maternity-leave/".$leave->id) }}' target="_blank">View</a>
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="6"></td>
                                <td>Total Employee</td>
                                <td>{{count($appoxleave)}}</td>
                                <td>Total Payment</td>
                                <td>{{$payment}}</td>
                                <td></td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="11">No record found</td>
                            </tr>
                        @endif
                    </tbody>
                    
                </table>
                                
            </div>
        </div>

         <div class="panel">
            <div class="panel-body">
                <div class="page-header-summery">
                    
                    <h2>Maternity Leave End </h2>
                    <h4>Month: <b>{{ \Carbon\Carbon::createFromFormat("Y-m",$month)->format('F, Y') }}</b></h4>
                    <h4>Total Employee: <b>{{count($appoxbacklist)}}</b></h4>
                    <h4>Total Amount: <b>{{round($appoxbacklist->sum('second_payment'),2)}}</b></h4>
                    <br>
                </div>

                <table class="table table-bordered table-hover table-head">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Photo</th>
                            <th>Associate ID</th>
                            <th>Name & Phone</th>
                            <th>Unit</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Leave End</th>
                            <th>EDD</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $payment = 0; @endphp
                        @if(count($appoxbacklist) > 0)
                            @foreach($appoxbacklist as $key => $leave)
                                @php $payment += $leave->second_payment; @endphp
                            <tr>
                                <td>{{$key+1}}</td>
                                <td><img src="{{ emp_profile_picture($leave) }}" class='small-image' style="height: 40px; width: auto;"></td>
                                <td><a href='{{ url("hr/recruitment/employee/show/".$leave->associate_id) }}' target="_blank">{{ $leave->associate_id }}</a></td>
                                <td>
                                    <b>{{ $leave->as_name }}</b>
                                    <p>{{ $leave->as_contact }}</p>
                                </td>
                                <td>{{ $unit[$leave->as_unit_id]['hr_unit_name']??'' }}</td>
                                <td>{{ $designation[$leave->as_designation_id]['hr_designation_name']??'' }}</td>
                                <td>{{ $department[$leave->as_department_id]['hr_department_name']??'' }}</td>
                                <td>{{ $leave->leave_to?? '' }}</td>
                                <td>{{ $leave->edd?? '' }}</td>
                                <td>{{ $leave->second_payment?? '' }}</td>
                                <td>
                                    <a href='{{ url("hr/operation/maternity-leave/".$leave->id) }}' target="_blank">View</a>
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="6"></td>
                                <td>Total Employee</td>
                                <td>{{count($appoxbacklist)}}</td>
                                <td>Total Payment</td>
                                <td>{{$payment}}</td>
                                <td></td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="11">No record found</td>
                            </tr>
                        @endif
                    </tbody>
                    
                </table>
                                
            </div>
        </div>
    </div>
</div>
@endsection