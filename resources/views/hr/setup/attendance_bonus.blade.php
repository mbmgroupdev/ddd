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
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>Attendance Bonus</h6>
                </div>
                <div class="panel-body">
                    <div class="row no-padding no margin">
                       <!-- Display Erro/Success Message -->
                        @include('inc/message')
                      <div class="panel panel-info col-sm-6 col-sm-offset-3" style="margin-bottom: 5px;">
                          <div class="panel-body">
                            <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/attendance_bonus_save')  }}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="unit_id"> Unit Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('unit_id', $unitList, null, ['placeholder'=>'Select Unit Name', 'id'=>'unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit Name field is required']) }}  
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="late_count" > Late Count<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                 <input type="number" id="late_count" name="late_count" placeholder="Enter Late Count" class="col-xs-12"
                                 style="height: auto;" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="leave_count" > Leave Count<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                 <input type="number" id="leave_count" name="leave_count" placeholder="Enter Leave Count" class="col-xs-12"style="height: auto;"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="absent_count" > Absent Count<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                 <input type="number" id="absent_count" name="absent_count" placeholder="Enter Absent Count" class="col-xs-12" style="height: auto;"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="first_month" > Primary (1st Month)<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                 <input type="number" id="first_month" name="first_month" placeholder="Enter First Month Bonus" value="0" class="col-xs-12" style="height: auto;"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="second_month" > Fixed (2nd Month to onward)<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                 <input type="number" id="second_month" name="second_month" placeholder="Enter Second Month Bonus" value="0" class="col-xs-12" style="height: auto;"/>
                                </div>
                            </div>

                            <div class="clearfix form-actions">
                                <div class=" col-md-12 text-center" style="padding-left: 30px;"> 
                                    <button class="btn btn-xs btn-success" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                    </button>

                                    &nbsp; &nbsp; &nbsp;
                                    <button class="btn btn-xs" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                    </button>
                                </div>
                            </div>                                 
                            </form> 
                          </div>
                      </div>  
                    </div>
                    <div class="row no-padding no margin">
                        <div class="panel panel-info">
                            <div class="panel-body table-responsive"  style="margin-bottom: 5px;">
                                <table id="dataTables" class="table table-bordered  table-hover">
                                    <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Unit Name</th>
                                            <th>Late Count</th>
                                            <th>Leave Count</th>
                                            <th>Absent Count</th>
                                            <th>First Month</th>
                                            <th>Second Month</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($attBonusData)
                                            @foreach($attBonusData as $data)
                                            <tr>
                                                <td>{{$loop->index+1}}</td>
                                                <td>{{$data->hr_unit_name}}</td>
                                                <td>{{$data->late_count}}</td>
                                                <td>{{$data->leave_count}}</td>
                                                <td>{{$data->absent_count}}</td>
                                                <td>{{$data->first_month??''}}</td>
                                                <td>{{$data->second_month??''}}</td>
                                            </tr>

                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
            </div>


            
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){ 

    $('#dataTables').DataTable({
        pagingType: "full_numbers" ,
        searching: true,
        // "lengthChange": false,
        // 'sDom': 't' 
        "sDom": '<"F"tp>'
    }); 
    var _token = $('input[name="_token"]').val();
    $('#unit_id').on('change',function(){
        $.ajax({
                url : "{{ url('hr/setup/get_values') }}",
                type: 'json',
                method: 'post',
                data: {
                    _token : _token,
                    unit_id: $(this).val()
                },
                success: function(data) {
                    // console.log(data);
                    $('#late_count').val(data.late_count);
                    $('#leave_count').val(data.leave_count);
                    $('#absent_count').val(data.absent_count);
                    $('#first_month').val(data.first_month);
                    $('#second_month').val(data.second_month);
                },
                error: function() {
                    console.log('failed...');
                }
        });
    });
});
</script>
@endsection