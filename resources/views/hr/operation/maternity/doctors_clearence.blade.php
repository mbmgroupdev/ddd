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
                        @include('hr.common.maternity-leave-card')
                        <a href="{{url('hr/operation/maternity-leave/'.$leave->id)}}" class="btn btn-primary w-100"> View Status </a>
                        
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
                                            <input id="leave_from_suggestion" type="date" name="leave_from_suggestion" class="form-control" required  value="{{\Carbon\Carbon::create($leave->medical->edd)->subDays(56)->format('Y-m-d')}}">
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
@include('hr.operation.maternity.maternity-modal')
<script type="text/javascript">
    $(document).on('change', '#edd', function(){
        var v = new Date($(this).val());
        var d = new Date( v - 56 * 24 * 60 * 60 * 1000);
        $('#leave_from_suggestion').val(JSON.stringify(d).slice(1,11));
    });
</script>
@endsection