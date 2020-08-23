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
                <li class="active"> Floor </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                @include('inc/message')
            
        <div class="panel panel-info">
              <div class="panel-heading"><h6>Floor</h6></div> 
                <div class="panel-body">
                <div class="row">
                      <!-- Display Erro/Success Message -->
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/floor')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                    <div class="col-sm-offset-3 col-sm-6">
                        <!-- PAGE CONTENT BEGINS -->
                        <!-- <h1 align="center">Add New Employee</h1> -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="hr_floor_unit_id"> Unit Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('hr_floor_unit_id', $unitList, null, ['placeholder'=>'Select Unit Name', 'id'=>'hr_floor_unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit Name field is required']) }}  
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="hr_floor_name" > Floor Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="hr_floor_name" name="hr_floor_name" placeholder="Floor name" class="col-xs-12" data-validation="required length alphanumeric" data-validation-length="1-128"   data-validation-allowing="/ _-"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="hr_floor_name_bn" > ফ্লোর (বাংলা) </label>
                                <div class="col-sm-8">
                                    <input type="text" id="hr_floor_name_bn" name="hr_floor_name_bn" placeholder="ফ্লোরের নাম" class="col-xs-12" data-validation="length" data-validation-length="0-255" data-validation-error-msg="সঠিক নাম দিন"/>
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
            </div>
        </div>
        <div class="panel panel-info">
              <div class="panel-heading"><h6>Floor List</h6></div> 
                <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Unit Name</th>
                                    <th style="width: 20%;">Floor Name</th>
                                    <th style="width: 20%;">ফ্লোর (বাংলা)</th>
                                    <th style="width: 20%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($floors as $floor)
                                <tr>
                                    <td>{{ $floor->hr_unit_name }}</td>
                                    <td>{{ $floor->hr_floor_name }}</td>
                                    <td>{{ $floor->hr_floor_name_bn }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a type="button" href="{{ url('hr/setup/floor_update/'.$floor->hr_floor_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{ url('hr/setup/floor/'.$floor->hr_floor_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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