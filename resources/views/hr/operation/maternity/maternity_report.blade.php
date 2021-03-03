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
                    
                    <h4>Maternity Leave (Approximate) </h4>
                    <div class="row">
                        <div class="col-sm-4 ">
                            <h5 class="text-left">Total Employee: <b>{{count($appoxleave)}}</b></h5>
                        </div>
                        <div class="col-sm-4 ">
                            <h5 class="text-center">Month: <b>{{ \Carbon\Carbon::createFromFormat("Y-m",$month)->format('F, Y') }}</b>
                            </h5>
                        </div>
                        <div class="col-sm-4">
                            <h5 class="text-right">Total Amount: 
                                @if(count($appoxleave) > 0)
                                <b>
                                {{bn_money(number_format(($appoxleave->sum('first_payment')),2, '.', ','))}}
                                </b>
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered table-hover table-head mt-3">
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
                                <td>{{ $unit[$leave->as_unit_id]['hr_unit_short_name']??'' }}</td>
                                <td>{{ $designation[$leave->as_designation_id]['hr_designation_name']??'' }}</td>
                                <td>{{ $department[$leave->as_department_id]['hr_department_name']??'' }}</td>
                                <td>{{ $leave->leave_from?? '' }}</td>
                                <td>{{ $leave->edd?? '' }}</td>
                                <td>
                                    {{ bn_money($leave->first_payment??0) }}
                                </td>
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
                                <td>{{bn_money(round($appoxleave->sum('first_payment'),2))}}</td>
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
                    
                    <h4>Maternity Leave End </h4>
                    <div class="row">
                        <div class="col-sm-4 ">
                            <h5 class="text-left">Total Employee: <b>{{count($appoxbacklist)}}</b></h5>
                        </div>
                        <div class="col-sm-4 ">
                            <h5 class="text-center">Month: <b>{{ \Carbon\Carbon::createFromFormat("Y-m",$month)->format('F, Y') }}</b>
                            </h5>
                        </div>
                        <div class="col-sm-4">
                            <h5 class="text-right">Total Amount: <b>{{bn_money(round($appoxbacklist->sum('second_payment'),2))}}</b></h5>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered table-hover table-head mt-3">
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
                                <td>{{ $unit[$leave->as_unit_id]['hr_unit_short_name']??'' }}</td>
                                <td>{{ $designation[$leave->as_designation_id]['hr_designation_name']??'' }}</td>
                                <td>{{ $department[$leave->as_department_id]['hr_department_name']??'' }}</td>
                                <td>{{ $leave->leave_to?? '' }}</td>
                                <td>{{ $leave->edd?? '' }}</td>
                                <td>{{ bn_money($leave->second_payment??0) }}</td>
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
                                <td>{{bn_money($payment)}}</td>
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
@include('hr.operation.maternity.maternity-modal')
@endsection