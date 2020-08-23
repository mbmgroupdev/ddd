@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
    <style>
        .shift-content .panel-title a{font-size: 15px; display: block;}
        .select2 {width:100% !important;}
        .panel-heading { padding: 7px 15px !important;}
        .modal-header { padding: 7px 15px;}
    </style>
@endpush
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
                <li class="active"> Shift/Roster </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <!-- Display Erro/Success Message -->
            @include('inc/notify')
            <div class="row">
                <div id="accordion" class="accordion-style panel-group">
                    <div class="panel panel-info">
                        <div class="panel-heading shift-content">
                            <h2 class="panel-title">
                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#individual" aria-expanded="false">
                                    <i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                    &nbsp;Shift/Roster Create
                                </a>
                            </h2>
                        </div>

                        <div class="panel-collapse collapse" id="individual" aria-expanded="false">
                            <div class="panel-body no-padding">
                                <br>
                                <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/shift')  }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}  
                                    <div class="col-sm-offset-3 col-sm-6">
                                        <!-- PAGE CONTENT BEGINS --> 
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="hr_shift_unit_id"> Unit Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                            <div class="col-sm-8"> 
                                                {{ Form::select('hr_shift_unit_id', $unitList, null, ['placeholder'=>'Select Unit Name', 'id'=>'hr_shift_unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit Name field is required']) }}  
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="hr_shift_name" > Shift Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="hr_shift_name" id="hr_shift_name" placeholder="Shift Name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128"/>
                                            </div>
                                        </div> 

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="hr_shift_name_bn" > শিফট (বাংলা) </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="hr_shift_name_bn" id="hr_shift_name_bn" placeholder="শিফট এর নাম" class="col-xs-12" data-validation="length" data-validation-length="0-255"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="hr_shift_start_time">Shift Time <span style="color: red; vertical-align: top;">&#42;</span></label>
                                            <div class="col-sm-8">
                                                <div class="col-sm-6 col-xs-6 no-padding-left input-icon">
                                                    <input type="text" name="hr_shift_start_time" id="hr_shift_start_time" class="col-xs-12" data-validation-error-msg="The Start Time field is required" placeholder="--:--:--" onClick="this.select();" />
                                                </div> 
                                                <div class="col-sm-6 col-xs-6 no-padding-right input-icon input-icon-right">
                                                    <input type="text" name="hr_shift_end_time" id="hr_shift_end_time" class="col-xs-12"  data-validation-error-msg="The End Time field is required" placeholder="--:--:--" onClick="this.select();"/> 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="hr_shift_break_time">Break Time <span style="color: red; vertical-align: top;">&#42;</span>(Minutes)</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="hr_shift_break_time" name="hr_shift_break_time" data-validation="required length number" data-validation-length="1-3" placeholder="Break time in Minutes" class="col-xs-12"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="hr_shift_out_time">Out Time</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="hr_shift_out_time" name="hr_shift_out_time" data-validation="required length number" data-validation-length="1-3" placeholder="Out time in Minutes" class="col-xs-12" disabled="disabled" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <!-- <label class="col-sm-3 control-label no-padding-right" for="gender"> Night Shift</label>
                                            <div class="col-sm-3">
                                            
                                                <div class="control-group"> 
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="hr_shift_night_flag" type="checkbox" value="1" class="ace" data-validation="checkbox_group" data-validation-qty="min1" data-validation-optional="true">
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </div> 
                                                </div> 
                                            </div> -->
                                            {{-- default shift --}}
                                            <label class="col-sm-3 control-label no-padding-right" for="gender"> Default Shift</label>
                                            <div class="col-sm-3">

                                                <div class="control-group"> 
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="hr_shift_default" type="checkbox" value="1" class="ace" data-validation="checkbox_group" data-validation-qty="min1" data-validation-optional="true">
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </div> 
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="clearfix form-actions">
                                          <div class="col-sm-offset-5 col-sm-7 "> 
                                              <a href="{{ url('/hr/setup/shift') }}" class="btn btn-xs" type="reset">
                                                  <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                              </a>
                                              &nbsp; &nbsp; &nbsp;
                                              <button class="btn btn-success btn-xs" type="submit">
                                                  <i class="ace-icon fa fa-check bigger-110"></i> Save  &nbsp;
                                              </button>
                                          </div>
                                      </div>
                                    <!-- PAGE CONTENT ENDS -->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading shift-content">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#multi-search">
                                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                    &nbsp;List Of Shift/Roster
                                </a>
                            </h4>
                        </div>

                        <div class="panel-collapse collapse in" id="multi-search">
                            <div class="panel-body">
                                <div class="col-sm-12">
                                    <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%; white-space: nowrap;">
                                        <thead>
                                            <tr>
                                                <th width="10%">Sl.</th>
                                                <th width="20%">Unit Name</th>
                                                <th width="20%">Shift Name</th>
                                                {{-- <th width="20%">Shift Code</th> --}}
                                                <th width="20%">Shift Time</th>
                                                <th width="10%">Break Time</th>
                                                <th width="30%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i=0; @endphp
                                            @foreach($shifts as $shift)
                                            <?php 
                                                $code= $shift->hr_shift_code;
                                                $letters = preg_replace('/[^a-zA-Z]/', '', $code);
                                                ?>
                                            <tr>
                                                <td>{{ ++$i }}</td>
                                                <td>{{ $shift->hr_unit_name }}</td>
                                                <td>{{ $shift->hr_shift_name }}</td>
                                                <!-- <td>
                                                    
                                                    {{ $letters }} &nbsp
                                                    <button type="button" class="btn btn-xs btn-info no-margin no-padding shift_times_btn" style="border-radius: 3px; font-size: 9px;" value="{{ $letters }}" data-toggle="modal" data-target="#myModal">Shift-Times</button>
                                                </td> -->
                                                <td>{{ $shift->hr_shift_start_time }} - {{ $shift->hr_shift_end_time }}</td>
                                                <td>{{ $shift->hr_shift_break_time }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a type="button" href="{{ url('hr/setup/shift_update/'.$shift->hr_shift_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                        <a href="{{ url('hr/setup/shift/'.$shift->hr_shift_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                                        {{-- <a type="button" href="{{ url('hr/setup/shift_update/'.$shift->hr_shift_id) }}" class='btn btn-xs btn-success' data-toggle="tooltip" title="Edit"> <i class="fa fa-history"></i></a> --}}
                                                        <button type="button" class="btn btn-xs btn-success shift_times_btn" value="{{ $letters }}" data-toggle="modal" data-target="#myModal"><i class="fa fa-history"></i></button>
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
                </div>
            </div>
    
            <!-- Modal -->
            <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:  lightblue;">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Shift Times</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Shift Name</th>
                                        <th>Shift Code</th>
                                        <th>In-Time</th>
                                        <th>Out-Time</th>
                                        <th>Break-Time</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody  id="modal_table_body">

                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm no-padding" style="border-radius: 2px;" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">

// sum two time ex: 12:00:00+11:30:00
function additionTime() {
    var arr = [];
    $.each(arguments, function() {
        $.each(this.split(':'), function(i) {
            arr[i] = arr[i] ? arr[i] + (+this) : +this;
        });
    })
    return arr.map(function(n) {
        return n < 10 ? '0'+n : n;
    }).join(':');
}

// convert min to hour:min
function convertMinsToHrsMins(mins) {
    let h = Math.floor(mins / 60);
    let m = mins % 60;
    h = h < 10 ? '0' + h : h;
    m = m < 10 ? '0' + m : m;
    return `${h}:${m}`;
}

$('#hr_shift_end_time, #hr_shift_break_time').on('keyup', function() {
    var breakTime = $('#hr_shift_break_time').val()==''?0:$('#hr_shift_break_time').val();
    var endTime = $('#hr_shift_end_time').val()==''?0:$('#hr_shift_end_time').val();
    var sum = additionTime(endTime,convertMinsToHrsMins(breakTime));
    $('#hr_shift_out_time').val(sum);
});

$(document).ready(function(){
    $('#hr_shift_start_time,#hr_shift_end_time').datetimepicker({
        format:'HH:mm:ss'
    });

    $('#dataTables').DataTable();
        // Show Line List by Unit ID
        var unit  = $("#hr_shift_unit_id");
        var floor = $("#hr_shift_floor_id");
        unit.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getFloorListByUnitID') }}",
            type: 'json',
            method: 'get',
            data: {unit_id: $(this).val() },
            success: function(data)
            {
                floor.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });

    // Show Line List by Floor ID
    var unit = $("#hr_shift_unit_id");
    var floor = $("#hr_shift_floor_id");
    var line = $("#hr_shift_line_id");
    floor.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getLineListByFloorID') }}",
            type: 'json',
            method: 'get',
            data: {unit_id: unit.val(), floor_id: $(this).val() },
            success: function(data)
            {
                line.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });

    $('body').on('click','.shift_times_btn', function(){
        var shift_code = $(this).val();
        // console.log(shift_code);
        $('#modal_table_body').html('');
        $.ajax({
            url : "{{ url('hr/setup/get_presfhit_times') }}",
            type: 'json',
            method: 'get',
            data: {shift_code: shift_code },
            success: function(data)
            {
                // console.log(data);
                var number_pos = shift_code.length;
                var push_html="";
                for(var i=0; i<data.length; i++){

                    var the_code = data[i]['hr_shift_code'];
                    var check    = the_code[number_pos];
                    
                    // console.log(check);
                    // console.log(!isNaN(check));

                    if(typeof check == 'undefined'){
                        push_html +="<tr>"+
                        "<td>"+data[i]['hr_shift_name']+"</td>"+
                        "<td>"+data[i]['hr_shift_code']+"</td>"+
                        "<td>"+data[i]['hr_shift_start_time']+"</td>"+
                        "<td>"+data[i]['hr_shift_end_time']+"</td>"+
                        "<td>"+data[i]['hr_shift_break_time']+" min</td>"+
                        "<td>"+data[i]['created_at']+"</td>"+
                        "</tr>";
                    }
                    else{

                        if(!isNaN(check)){
                            push_html +="<tr>"+
                            "<td>"+data[i]['hr_shift_name']+"</td>"+
                            "<td>"+data[i]['hr_shift_code']+"</td>"+
                            "<td>"+data[i]['hr_shift_start_time']+"</td>"+
                            "<td>"+data[i]['hr_shift_end_time']+"</td>"+
                            "<td>"+data[i]['hr_shift_break_time']+" min</td>"+
                            "<td>"+data[i]['created_at']+"</td>"+
                            "</tr>";       
                        }
                    }
                }
                $('#modal_table_body').html(push_html);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });
});
</script>
@endpush
@endsection