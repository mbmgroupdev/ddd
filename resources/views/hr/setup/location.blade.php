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
				<li class="active"> Location </li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
                @include('inc/message')
            <div class="panel panel-info">
              <div class="panel-heading"><h6>Location</h6></div> 
                <div class="panel-body">
        
                    <div class="row">
                          <!-- Display Erro/Success Message -->
                            <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/location')  }}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                        <div class="col-sm-offset-3 col-sm-6">
                            <!-- PAGE CONTENT BEGINS -->
                            <!-- <h1 align="center">Add New Employee</h1> -->

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_location_name" > Location Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="hr_location_name" name="hr_location_name" placeholder="Location name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_location_short_name" > Location Short Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="hr_location_short_name" name="hr_location_short_name" placeholder="Location short name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-20" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_location_unit_id" > Unit <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('hr_location_unit_id', $unitList, null, ['id' => 'hr_location_unit_id', 'placeholder' => 'Select Unit', 'class' => 'col-xs-12 form-control', 'data-validation' => 'required']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_location_name_bn" > লোকেশন (বাংলা) </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="hr_location_name_bn" name="hr_location_name_bn" placeholder="লোকেশনের নাম" class="col-xs-12" data-validation="length" data-validation-length="0-255" data-validation-error-msg="সঠিক নাম দিন"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_location_address" > Location Adrress </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="hr_location_address" name="hr_location_address" placeholder="Location name" class="col-xs-12" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_location_address_bn" > লোকেশনর ঠিকানা (বাংলা) </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="hr_location_address_bn" name="hr_location_address_bn" placeholder="লোকেশনর ঠিকানা (বাংলা)" class="col-xs-12"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_location_code"> Location Code </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="hr_location_code" name="hr_location_code" placeholder="Location code" class="col-xs-12" data-validation="length" data-validation-length="0-10"/>
                                    </div>
                                </div>
                                
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-12 col-xs-12">
                            <div class="clearfix form-actions" >
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
                </div>
            </div>
            <div class="panel panel-info">
              <div class="panel-heading"><h6>Location List</h6></div> 
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%; white-space: nowrap;">
                                <thead>
                                    <tr>
                                        
                                        <th width="30%">Location Name</th>
                                        <th width="30%">Short Name</th>
                                        <th width="30%">লোকেশন (বাংলা)</th>
                                        <th width="30%">Location Code</th>
                                        <th width="30%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $loc)
                                    <tr>
                                        
                                        <td>{{ $loc->hr_location_name }}</td>
                                        <td>{{ $loc->hr_location_short_name }}</td>
                                        <td>{{ $loc->hr_location_name_bn }}</td>
                                        <td>{{ $loc->hr_location_code }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a type="button" href="{{ url('hr/setup/location_update/'.$loc->hr_location_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                <a href="{{ url('hr/setup/location/'.$loc->hr_location_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){ 

    $('#dataTables').DataTable({
        pagingType: "full_numbers" ,
        // searching: false,
        // "lengthChange": false,
        // 'sDom': 't' 
        "sDom": '<"F"tp>'

    }); 
});
</script>
@endsection