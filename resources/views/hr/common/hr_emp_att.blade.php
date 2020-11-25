@extends('hr.layout')
@section('title', 'Employee Attendance')

@section('main-content')
@push('css')
  <style>
    .single-employee-search {
      margin-top: 82px !important;
    }
    .view:hover, .view:hover{
      color: #ccc !important;
      
    }
    .grid_view{

    }
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .iq-card .iq-card-header {
      margin-bottom: 10px;
      padding: 15px 15px;
      padding-bottom: 8px;
    }
    .modal-h3{
      line-height: 15px !important;
    }
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
                    <a href="#">Reports</a>
                </li>
                <li class="active"> Monthly Salary Report</li>
            </ul>
        </div>

        <div class="panel"> 
            <div class="panel-body">
                <div class="page-header-summery">
                    
                    <h2>Daily Attendance Employee Report </h2>
                    <h3>Unit: {{$query['unit_name']}}</h3>
                    <h4>Date: {{$query['date']}}</h4>
                    <h4>Total Employee: <b>{{ count($employees) }}</b></h4>
                            
                </div>
                <table class="table table-bordered table-hover table-head">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Oracle ID</th>
                            <th>Associate ID</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>In/Out</th>
                            <th>OT Hour</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $key => $employee)
                        <tr>
                            @php $associate_id = $employee['associate_id']; @endphp
                            <td><a href="{{url('hr/recruitment/employee/show/'.$associate_id)}}"><img height="30" src="{{ $employee['image'] }}" class='small-image min-img-file'></a></td>
                            <td>{{ $employee['oracle_code'] }}</td>
                            <td><a href='{{ url("hr/operation/job_card?associate=$associate_id&month_year=") }}' target="_blank">{{ $associate_id }}</a></td>
                            <td>
                                <b>{{ $employee['name'] }}</b>
                            </td>
                            <td>{{ $employee['designation'] }}</td>
                            <td>{{ $employee['department'] }}</td>
                            <td>{{ $employee['status'] }}</td>
                            <td>{{ $employee['in_time'] }} - {{ $employee['out_time'] }}</td>
                            <td><b>{{ $employee['ot_hour'] }}</b></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection