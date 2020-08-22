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
                <li class="active"> Department </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Setup <small><i class="ace-icon fa fa-angle-double-right"></i> Department </small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/department_update')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                <div class="col-sm-6 col-sm-offset-3">
                    <!-- PAGE CONTENT BEGINS --> 

                        <input type="hidden" name="hr_department_id"  value="{{ $department->hr_department_id }}" >
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_area_id" > Area Name <span style="color: red; vertical-align: top">&#42;</span> </label>
                            <div class="col-sm-8">
                                {{ Form::select('hr_department_area_id', $areaList, $department->hr_department_area_id, ['placeholder' => 'Select Area Name', 'class' => 'col-xs-12 no-select', 'id'=>'hr_department_area_id', 'data-validation'=>'required']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_name" > Department Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_department_name" id="hr_department_name" placeholder="Department Name" class="col-xs-12" value="{{ $department->hr_department_name }}"  data-validation="required length custom" data-validation-length="1-128" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_name_bn" >ডিপার্টমেন্ট (বাংলা) <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_department_name_bn" id="hr_department_name_bn" placeholder="ডিপার্টমেন্টের নাম " class="col-xs-12" value="{{ $department->hr_department_name_bn }}"  data-validation="length required" data-validation-length="0-255"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_code"> Department Code <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_department_code" placeholder="Department Code" class="col-xs-12" value="{{ $department->hr_department_code }}" data-validation="required length custom" data-validation-length="1-2"
                                />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_min_range"> Department ID Range <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <input type="text" id="hr_department_min_range" name="hr_department_min_range" data-validation=" required length number" data-validation-length="1-6" placeholder="Example: 000001 " class="col-xs-12" value="{{ $department->hr_department_min_range }}"  data-validation-error-msg="Maximum 6 digits" />
                                    </div>
                                    <div class="col-xs-6">
                                        <input type="text" id="hr_department_max_range" name="hr_department_max_range" data-validation=" required length number" data-validation-length="1-6" placeholder="Example: 001000" class="col-xs-12"  value="{{ $department->hr_department_max_range }}" data-validation-error-msg="Maximum 6 digits" />
                                    </div>
                                </div>
                            </div>
                        </div>
 
                    <!-- PAGE CONTENT ENDS -->
                </div>
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
                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

@endsection