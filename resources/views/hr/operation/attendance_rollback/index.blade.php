@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
    <style>
        .att_rollback .panel-title {margin-top: 3px; margin-bottom: 3px;}
        .att_rollback .panel-title a{font-size: 15px; display: block;}
        .panel-group { margin-bottom: 5px;}
        h3.smaller {font-size: 13px;}
        .header {margin-top: 0;}
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
                    <a href="#">Operations</a>
                </li>
                <li class="active"> Attendance file rollback</li>
            </ul><!-- /.breadcrumb -->
        </div>
        <div class="page-content">
           
            <div id="accordion" class="accordion-style panel-group">
                <div class="panel panel-info">
                    <div class="panel-heading att_rollback">
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#file-rollback">
                                <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                &nbsp;Attendance file rollback
                            </a>
                        </h4>
                    </div>
                    @include('inc.notify')
                    
                    <div class="panel-collapse collapse in" id="file-rollback">
                        <div class="panel-body">
                            @php
                                $today = date('Y-m-d');
                                $yesterday = date('Y-m-d',strtotime("-1 days"));
                            @endphp
                            <div class="form-horizontal">
                                <div class="col-sm-offset-3 col-sm-6 no-padding-left">
                                    <form role="form" method="post" action="{{ url('hr/operation/attendance-rollback') }}" id="searchform" >
                                        {{ csrf_field() }} 
                                        <div class="panel panel-info">
                                            <div class="panel-body">
                                                <h3 class="header smaller lighter green">
                                                    <i class="ace-icon fa fa-bullhorn"></i>
                                                    All <span class="text-red" style="vertical-align: top;">&#42;</span>  required
                                                </h3>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label" for="unit"> Unit <span class="text-red" style="vertical-align: top;">&#42;</span> : </label>
                                                    <div class="col-sm-9">
                                                        {{ Form::select('unit', ['1' => 'MBM GARMENTS LTD.', '2' => 'CUTTING EDGE INDUSTRIES LTD.', '8' => 'CUTTING EDGE INDUSTRIES LTD. (WASHING PLANT)', '3' => 'ABSOLUTE QUALITYWEAR LTD.'], null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required', 'required']) }}
                                                        <span class="text-red" id="error_unit_s"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right align-left" for="month_number">Day <span class="text-red" style="vertical-align: top;">&#42;</span> :</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="day" class="form-control datepicker" value="" required>
                                                        {{-- {{ Form::select('date', [$today => 'Today', $yesterday => 'Yesterday'], null, ['placeholder'=>'Select day', 'id'=>'month_number', 'required', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Day field is required', 'required']) }} --}}
                                                    </div>
                                                    
                                                </div>
                                                
                                                <div class="form-group">
                                                    <div class="col-sm-offset-3 col-sm-6 ">
                                                        <button type="submit" class="btn btn-primary btn-xs"
                                                        style=" " ><span class="glyphicon glyphicon-pencil"></span>&nbsp
                                                        Process</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>  {{-- page-content end --}}
    </div>  {{-- main-content-inner end --}}
</div>  {{-- main-content end --}}

@push('js')

@endpush


@endsection