@extends('hr.layout')
@section('title', '')
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
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> Designation Update</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Setup <small><i class="ace-icon fa fa-angle-double-right"></i> Designation Update</small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/designation_update')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                <div class="col-sm-offset-3 col-sm-6">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->

                        <input type="hidden" name="hr_designation_id" value="{{ $designation->hr_designation_id }}">
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_designation_emp_type"> Associate Type <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8"> 
                                {{ Form::select('hr_designation_emp_type', $emp_type, $designation->hr_designation_emp_type, ['placeholder'=>'Select Associate Type', 'id'=>'hr_designation_emp_type', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'Employee type is required']) }}  
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_designation_name" > Designation Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_designation_name" placeholder="Designation Name" class="col-xs-12"  value="{{ $designation->hr_designation_name }}" data-validation="required length custom" data-validation-length="1-128" />
                            </div>
                        </div>  

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_designation_name_bn" > পদবী (বাংলা)</label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_designation_name_bn" name="hr_designation_name_bn"  value="{{ $designation->hr_designation_name_bn }}" placeholder="পদের নাম" class="col-xs-12" data-validation="length" data-validation-length="0-255"/>
                            </div>
                        </div> 

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_designation_grade" > Grade <span style="color: red; vertical-align: top;">&#42;</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_designation_grade" placeholder="Grade" value="{{ $designation->hr_designation_grade }}" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128"/>
                            </div>
                        </div> 
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
                <div class="col-sm-12 col-xs-12">
                    <div class="clearfix form-actions">
                        <div class="col-md-offset-4 col-md-4 text-center" style="padding-left: 30px;"> 
                            <button class="btn btn-sm btn-success" type="submit">
                                <i class="ace-icon fa fa-check bigger-110"></i> Submit
                            </button>

                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-sm" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
                    </form> 
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

@endsection