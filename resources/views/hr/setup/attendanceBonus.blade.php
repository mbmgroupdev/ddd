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
                <li class="active"> Attendance Bonus </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                @include('inc/message')
            
        <div class="panel panel-info">
         <div class="panel-heading"><h6>Attendance Bonus</h6></div> 
            <div class="panel-body">
            <div class="row">
                  <!-- Display Erro/Success Message -->
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/attendance_bonus_store')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                <div class="col-sm-offset-3 col-sm-6">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_floor_unit_id"> Unit Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8"> 
                                {{ Form::select('hr_floor_unit_id', $unitList, null, ['placeholder'=>'Select Unit Name', 'id'=>'hr_floor_unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit Name field is required']) }}  
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="ist_" > Primary (1st Month) <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" id="first_month" name="first_month" placeholder="First Month Bonus" class="col-xs-12"  data-validation="required length number" data-validation-allowing="float" data-validation-length="0-49"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="from_2nd_month" >Fixed (2nd Month to onward) <span style="color: red; vertical-align: top;">&#42;</span></label>
                            <div class="col-sm-8">
                                <input type="text" id="from_2nd_month" name="from_2nd_month" placeholder="2nd Month" class="col-xs-12" data-validation="required length number" data-validation-allowing="float" data-validation-length="0-49"/>
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
        </div>
    </div>
    <div class="panel panel-info">
              <div class="panel-heading"><h6>Attendance Bonus List</h6></div> 
                <div class="panel-body">

                <div class="col-sm-offset-2 col-sm-8 col-xs-12">
                    <table id="dataTables" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Unit Name</th>
                                    <th>First Month</th>
                                    <th>From 2nd Month</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bonusList as $bonus)
                                <tr>
                                    <td>{{ $bonus->hr_unit_name }}</td>
                                    <td>{{ $bonus->first_month }}</td>
                                    <td>{{ $bonus->from_2nd }}</td>                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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