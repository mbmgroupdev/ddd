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
                <li class="active"> Floor Update </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Setup <small><i class="ace-icon fa fa-angle-double-right"></i> Floor Update</small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                
                <div class="col-sm-offset-3 col-sm-6" style="padding-top: 30px;">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/floor_update')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                        <input type="hidden" name="hr_floor_id" value="{{ $floor->hr_floor_id }}">

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="hr_floor_unit_id"> Unit Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8"> 
                                {{ Form::select('hr_floor_unit_id', $unitList, $floor->hr_floor_unit_id, ['placeholder'=>'Select Unit Name', 'id'=>'hr_floor_unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit Name field is required']) }}  
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="hr_floor_name" > Floor Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_floor_name" name="hr_floor_name" value="{{ $floor->hr_floor_name }}" placeholder="Floor name" class="col-xs-12" data-validation="required length alphanumeric" data-validation-length="1-128"  data-validation-allowing="/ _-" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="hr_floor_name_bn" > ফ্লোর (বাংলা) </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_floor_name_bn" name="hr_floor_name_bn"  value="{{ $floor->hr_floor_name_bn }}" placeholder="ফ্লোরের নাম" class="col-xs-12" data-validation="length" data-validation-length="0-255" data-validation-error-msg="সঠিক নাম দিন"/>
                            </div>
                        </div>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
                <div class="col-sm-12 col-xs-12">
                    <div class="clearfix form-actions">
                        <div class="col-md-offset-4 col-md-4 text-center"> 
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