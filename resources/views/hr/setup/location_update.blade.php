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
				<li class="active"> Location Update</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="page-header">
				<h1>Setup <small><i class="ace-icon fa fa-angle-double-right"></i> Location Update </small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('hr/setup/location_update')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                <div class="col-sm-offset-3 col-sm-6">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                      <input type="hidden" name="hr_location_id" value="{{ $location->hr_location_id }}"/>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_location_name" > Location Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_location_name" name="hr_location_name" placeholder="Location name" class="col-xs-12" value="{{ $location->hr_location_name }}" data-validation="required length custom" data-validation-length="1-128"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_location_short_name" > Location Short Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_location_short_name" name="hr_location_short_name" placeholder="Location short name" class="col-xs-12" value="{{ $location->hr_location_short_name }}" data-validation="required length custom" data-validation-length="1-20"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_location_unit_id" > Unit <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                {{ Form::select('hr_location_unit_id', $unitList, $location->hr_location_unit_id, ['id' => 'hr_location_unit_id', 'placeholder' => 'Select Unit', 'class' => 'col-xs-12 form-control', 'data-validation' => 'required']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_location_name_bn" > লোকেশন (বাংলা) </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_location_name_bn" name="hr_location_name_bn" placeholder="লোকেশনের নাম" class="col-xs-12" value="{{ $location->hr_location_name_bn }}" data-validation="length" data-validation-length="0-255" data-validation-error-msg="সঠিক নাম দিন"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_location_address" > Location Adrress </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_location_address" name="hr_location_address" placeholder="Location name" value="{{ $location->hr_location_address }}" class="col-xs-12"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_location_address_bn" > লোকেশনর ঠিকানা (বাংলা) </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_location_address_bn" name="hr_location_address_bn" placeholder="লোকেশনর ঠিকানা (বাংলা)" class="col-xs-12" value="{{ $location->hr_location_address_bn }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_location_code"> Location Code </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_location_code" name="hr_location_code" placeholder="Location code" class="col-xs-12" value="{{ $location->hr_location_code }}" data-validation="length" data-validation-length="0-10"/>
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
            </div>
		</div><!-- /.page-content -->
	</div>
</div>

@endsection