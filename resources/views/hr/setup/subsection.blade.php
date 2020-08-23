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
                <li class="active"> Sub-Section </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
        
            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/subsection')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
            <div class="panel panel-info">
              <div class="panel-heading"><h6>Sub Section</h6></div> 
                <div class="panel-body">
                <div class="col-sm-offset-3 col-sm-6">
                    <!-- PAGE CONTENT BEGINS -->

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_subsec_area_id" > Area Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                {{ Form::select('hr_subsec_area_id', $areaList, null, ['placeholder' => 'Select Area Name', 'class' => 'col-xs-12', 'id'=>'hr_subsec_area_id', 'data-validation'=>'required']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_subsec_department_id" >Department Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <select name="hr_subsec_department_id" id="hr_subsec_department_id" class="col-xs-12" data-validation="required">
                                    <option value="">Select Department Name </option> 
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_subsec_section_id" >Section Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <select name="hr_subsec_section_id" id="hr_subsec_section_id" class="col-xs-12" data-validation="required">
                                    <option value="">Select Section Name </option> 
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_subsec_name" > Sub Section Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_subsec_name" id="hr_subsec_name" placeholder="Sub Section Name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_subsec_name_bn" > সাব সেকশন (বাংলা) </label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_subsec_name_bn" id="hr_subsec_name_bn" placeholder="সাব সেকশনের নাম" class="col-xs-12" data-validation="length" data-validation-length="0-255"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_subsec_code"> Sub Section Code </label>
                            <div class="col-sm-8">
                                <input type="text" id="hr_subsec_code" name="hr_subsec_code" placeholder="Sub Section Code" class="col-xs-12" data-validation="length" data-validation-length="0-10" data-validation-current-error="The input value must be between 0-10 characters">
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
            </div>
        </div>
                    </form> 

        <div class="panel panel-info">
              <div class="panel-heading"><h6>Sub Section List</h6></div> 
                <div class="panel-body">
                <div class="col-sm-12">
                    <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Area Name</th>
                                    <th style="width: 20%;">Department Name</th>
                                    <th style="width: 20%;">Section Name</th>
                                    <th style="width: 20%;">Sub Section Name</th>
                                    <th style="width: 20%;">সাব সেকশন (বাংলা)</th>
                                    <th style="width: 20%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subSections as $subSection)
                                <tr>
                                    <td>{{ $subSection->hr_area_name }}</td>
                                    <td>{{ $subSection->hr_department_name }}</td>
                                    <td>{{ $subSection->hr_section_name }}</td>
                                    <td>{{ $subSection->hr_subsec_name }}</td>
                                    <td>{{ $subSection->hr_subsec_name_bn }}</td>
                                    <td>
                                    <div class="btn-group">
                                        <a type="button" href="{{ url('hr/setup/subsection_update/'.$subSection->hr_subsec_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                        <a href="{{ url('hr/setup/subsection/'.$subSection->hr_subsec_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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

<script type="text/javascript">
$(document).ready(function(){
    //Load Department List
    var area    = $("#hr_subsec_area_id");
    var department = $("#hr_subsec_department_id");
    area.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
            type: 'json',
            method: 'get',
            data: {area_id: $(this).val() },
            success: function(data)
            {
                department.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });


    //Load Section List By Department & Area ID
    var area    = $("#hr_subsec_area_id");
    var department = $("#hr_subsec_department_id")
    var section    = $("#hr_subsec_section_id");
    department.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
            type: 'json',
            method: 'get',
            data: {area_id: area.val(), department_id: $(this).val() },
            success: function(data)
            {
                section.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });
});
</script>
@endsection