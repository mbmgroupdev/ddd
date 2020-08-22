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
            

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/department')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
            <div class="panel panel-info">
              <div class="panel-heading"><h6>Department</h6></div> 
                <div class="panel-body">
                <div class="col-sm-offset-3 col-sm-6">
                    <!-- PAGE CONTENT BEGINS --> 
 

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_area_id" > Area Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                {{ Form::select('hr_department_area_id', $areaList, null, ['placeholder' => 'Select Area Name', 'class' => 'col-xs-12 no-select', 'id'=>'hr_department_area_id', 'data-validation'=>'required']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_name" > Department Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_department_name" id="hr_department_name" placeholder="Department Name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_name_bn" >ডিপার্টমেন্ট (বাংলা) <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_department_name_bn" id="hr_department_name_bn" placeholder="ডিপার্টমেন্টের নাম " class="col-xs-12" data-validation="length required" data-validation-length="0-255"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_code"> Department Code <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" name="hr_department_code" placeholder="Department Code" class="col-xs-12" data-validation="required length custom" data-validation-length="1-2"
                                />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_department_min_range"> Department ID Range <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <input type="text" id="hr_department_min_range" name="hr_department_min_range" data-validation=" required length number" data-validation-length="6" placeholder="Example: 000001 " class="col-xs-12"  />
                                    </div>
                                    <div class="col-xs-6">
                                        <input type="text" id="hr_department_max_range" name="hr_department_max_range" data-validation=" required length number" data-validation-length="6" placeholder="Example: 001000" class="col-xs-12"  />
                                    </div>
                                </div>
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
              <div class="panel-heading"><h6>Department List</h6></div> 
                <div class="panel-body">
                <div class="col-sm-12">
                    <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Department Name</th>
                                    <th style="width: 20%;">ডিপার্টমেন্ট (বাংলা)</th>
                                    <th style="width: 20%;">Department Code</th>
                                    <th style="width: 30%;">Department ID Range</th>
                                    <th style="width: 30%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $department)
                                <tr>
                                    <td>{{ $department->hr_department_name }}</td>
                                    <td>{{ $department->hr_department_name_bn }}</td>
                                    <td>{{ $department->hr_department_code }}</td>
                                    <td>{{ $department->hr_department_min_range }}-{{ $department->hr_department_max_range }}</td>
                                    <td>
                                    <div class="btn-group">
                                        <a type="button" href="{{ url('hr/setup/department_update/'.$department->hr_department_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                        <a href="{{ url('hr/setup/department/'.$department->hr_department_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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
    //date validation------------------
    $('#hr_department_min_range').on('change',function(){
        $('#hr_department_max_range').val('');    
    });

    $('#hr_department_max_range').on('change',function(){
        var end     = $(this).val();
        var start   = $('#hr_department_min_range').val();
        if(start == '' || start == null){
            alert("Please enter Min-Value first");
            $('#hr_department_max_range').val('');
        }
        else{
             if(parseInt(end) < parseInt(start)){
                alert("Invalid!!\n Min_Value is bigger than Max-Value");
                $('#hr_department_max_range').val('');
            }
        }
    });
    //date validation end---------------
});
</script>
@endsection