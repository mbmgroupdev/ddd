@extends('hr.layout')
@section('title', 'Maternity Leave Application')
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
                    <a href="#"> Operation </a>
                </li>
                <li>
                    <a href="#"> Maternity Leave </a>
                </li>
                <li class="active">Doctor's Clearence </li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/operation/maternity-leave/list')}}" target="_blank" class="btn btn-primary pull-right" >List <i class="fa fa-list bigger-120"></i></a>
                </li>
            </ul>
        </div>

        @include('inc/message')
        <div class="panel panel-success" style="">
            <div class="panel-body">
                <div class="row">
                    
                    <div class="col-sm-3">        
                        <div class="user-details-block" style="padding-top: 0.5rem;">
                            <div class="user-profile text-center mt-0">
                                <img id="avatar" class="avatar-130 img-fluid" src="{{ $employee->as_pic }} " >
                            </div>
                            <div class="text-center mt-3">
                                <h4><b id="name">{{ $employee->as_name }}</b></h4>
                                <p class="mb-0" id="designation">
                                {{ $employee->hr_designation_name }}, {{$employee->hr_department_name}}</p>
                                <p class="mb-0" id="designation">
                                {{$employee->hr_unit_name}}</p>
                            </div>
                             <table style="width: 100%;" border="0">
                                 <tr>
                                     <td><i class="field-title">Oracle ID</i></td>
                                     <td class="field-data">: {{ $employee->as_oracle_code }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Associate ID</i></td>
                                     <td class="field-data">: {{ $employee->associate_id }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Husband Name</i></td>
                                     <td class="field-data">: {{ $leave->husband_name }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Husband Occupation</i></td>
                                     <td class="field-data">: {{ $leave->husband_occupasion }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Husband Age</i></td>
                                     <td class="field-data">: {{ $leave->husband_age }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Total Child</i> <span class="field-data">: {{ ($leave->no_of_son + $leave->no_of_daughter) }}</span></td>
                                     <td><i class="field-title">Last Child Age</i> <span class="field-data">: {{ $leave->last_child_age }} </span></td>
                                 </tr>
                             </table>
                             <br>
                             <a href="{{url('hr/operation/maternity-leave/'.$leave->id)}}" class="btn btn-primary w-100"> View Status </a>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <h3 class="border-left-heading"> Doctor's Clearence </h3>
                        @if($leave->doctors_clearence)
                            @include('hr.operation.maternity.doctor_leave_suggestion')
                        @else
                            <form method="post" action="{{url('hr/operation/doctor-clearence-letter')}}" class="needs-validation mt-3" novalidate>
                                @csrf
                                <input type="hidden" name="hr_maternity_leave_id" value="{{$leave->id}}">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group has-required has-float-label">
                                            <input id="edd" type="date" name="edd" class="form-control" required  value="{{$leave->medical->edd}}">
                                            <label for="edd" >Confirm EDD</label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group has-required has-float-label">
                                            <input id="leave_from_suggestion" type="date" name="leave_from_suggestion" class="form-control" required  value="{{\Carbon\Carbon::create($leave->medical->edd)->subMonths(2)->format('Y-m-d')}}">
                                            <label for="leave_from_suggestion">Leave From</label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="submit" class="btn btn-primary">Proceed to HR</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                        <hr>
                        <h3 class="border-left-heading mb-3"> Report </h3>
                        @include('hr.operation.maternity.doctor_report')



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on('change', '#edd', function(){
        var d = new Date($(this).val());
        d.setMonth(d.getMonth() - 2);
        $('#leave_from_suggestion').val(JSON.stringify(new Date(d)).slice(1,11));
    });
</script>
@endsection