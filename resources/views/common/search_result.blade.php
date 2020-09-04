@extends('hr.layout')
@section('title', 'Search Employee')
@section('main-content')
@push('css')
    <style>
        
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">List Of Employee</a>
                </li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                     
                    <div class="panel">
                        <div class="panel-heading">
                            <h6>List Of Search Employee</h6>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-hover table-head">
                                <thead>
                                    <tr class="info">
                                        <th width="5%">Sl</th>
                                        <th width="10%">Picture</th>
                                        <th width="15%">Associate ID</th>
                                        <th width="15%">Name</th>
                                        <th width="15%">Unit</th>
                                        <th width="15%">Designation</th>
                                        <th width="10%">Location</th>
                                        <th width="10%">Floor</th>
                                        <th width="10%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($getEmployee) > 0)
                                    @php $i = 0; @endphp
                                    @foreach($getEmployee as $employee)
                                    <tr>
                                        <td>{{ ++$i}}</td>
                                        <td>
                                            <img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;">
                                        </td>
                                        <td><a href='{{url("/hr/recruitment/employee/show/$employee->associate_id")}}'>{{ $employee->associate_id}}</a></td>
                                        <td>{{ $employee->as_name }}</td>
                                        <td>{{ $employee->unit['hr_unit_name']??'' }}</td>
                                        <td>{{ $employee->designation['hr_designation_name']??'' }}</td>
                                        <td>{{ $employee->location['hr_location_name']??'' }}</td>
                                        <td>{{ $employee->floor['hr_floor_name']??''}}</td>
                                        <td>
                                            @if($employee->as_status == 0)
                                                Delete
                                            @elseif($employee->as_status == 1)
                                                Active
                                            @elseif($employee->as_status == 2)
                                                Resign
                                            @elseif($employee->as_status == 3)
                                                Terminate
                                            @elseif($employee->as_status == 4)
                                                Suspend
                                            @elseif($employee->as_status == 5)
                                                Left
                                            @elseif($employee->as_status == 6)
                                                Maternity
                                            @else
                                                
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="9"> <h4 class="text-center">No Employee Found !</h4></td>
                                    </tr>
                                    @endif

                                </tbody>
                            </table>
                            <div class="pagination text-center justify-center">
                                {{ $getEmployee->appends($_REQUEST)->render() }}
                            </div>
                        </div>
                    </div>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

@endsection
