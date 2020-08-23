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
                <li class="active"> Shift Update</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            
            <div class="row">
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Shift Update <a href="{{ URL::to('hr/setup/shift') }}" class="btn btn-xx btn-success pull-right"> List of Shift</a></h6>
                    </div> 
                    <div class="panel-body">
                        @include('inc/message')
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/shift_update')  }}" enctype="multipart/form-data">
                            {{ csrf_field() }}  
                            <div class="col-sm-6 col-sm-offset-3">
                                <!-- PAGE CONTENT BEGINS --> 

                                    <input type="hidden" name="hr_shift_id"  value="{{ $shift->hr_shift_id }}" >

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="hr_shift_unit_id"> Unit Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-8">  
                                            <input type="hidden" value="{{$shift->hr_shift_unit_id}}" name="hr_shift_unit_id">  
                                            <input type="text" value="{{$shift->hr_unit_name}}" class="col-xs-12"  readonly="readonly">   
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="hr_shift_name" > Shift Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-8">
                                            <input type="text" name="hr_shift_name" id="hr_shift_name" placeholder="Shift Name" class="col-xs-12" value="{{ $shift->hr_shift_name }}"  data-validation="required length custom" data-validation-length="1-128" readonly="readonly" />
                                        </div>
                                    </div> 

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="hr_shift_name_bn" > শিফট (বাংলা) </label>
                                        <div class="col-sm-8">
                                            <input type="text" name="hr_shift_name_bn" id="hr_shift_name_bn" value="{{ $shift->hr_shift_name_bn }}"  placeholder="শিফট এর নাম" class="col-xs-12" data-validation="length" data-validation-length="0-255"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="hr_shift_start_time">Shift Time <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-8">
                                            <div class="col-sm-6 col-xs-6 no-padding-left input-icon">
                                                <input type="text" name="hr_shift_start_time" id="hr_shift_start_time" value="{{ $shift->hr_shift_start_time }}"  placeholder="Start Time" class="col-xs-12 " data-validation-error-msg="The Start Time field is required" />
                                            </div> 
                                            <div class="col-sm-6 col-xs-6 no-padding-right input-icon input-icon-right">
                                                <input type="text" name="hr_shift_end_time" id="hr_shift_end_time" value="{{ $shift->hr_shift_end_time }}" class="col-xs-12 "  data-validation-error-msg="The End Time field is required" /> 
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="hr_shift_break_time">Break Time <span style="color: red; vertical-align: top;">&#42;</span>(Minutes)</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="hr_shift_break_time" name="hr_shift_break_time"  value="{{ $shift->hr_shift_break_time }}" data-validation="required length number" data-validation-length="1-3" placeholder="Break time in Minutes" class="col-xs-12"/>
                                        </div>
                                    </div>

                                    @php
                                        // convert minute to H:i
                                        $breakTime = date('H:i', mktime(0, $shift->hr_shift_break_time));

                                        // sum end time + break time
                                        $break   = strtotime($breakTime)-strtotime("00:00:00");
                                        $outTime = date("H:i:s",strtotime($shift->hr_shift_end_time)+$break);
                                    @endphp

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label no-padding-right" for="hr_shift_out_time">Out Time</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="hr_shift_out_time" name="hr_shift_out_time" value="{{$outTime}}" data-validation="required length number" data-validation-length="1-3" placeholder="Out time in Minutes" class="col-xs-12" disabled="disabled" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <!-- <label class="col-sm-3 control-label no-padding-right" for="gender"> Night Shift</label>
                                        <div class="col-sm-3">
                                            <div class="control-group"> 
                                                <div class="checkbox">
                                                    <label>
                                                        <input name="hr_shift_night_flag" type="checkbox" value="1" class="ace" data-validation="checkbox_group" data-validation-qty="min1" data-validation-optional="true" {{ ($shift->hr_shift_night_flag==1)? "checked":"" }}>
                                                        <span class="lbl"></span>
                                                    </label>
                                                </div> 
                                            </div> 
                                        </div> -->
                                        <label class="col-sm-3 control-label no-padding-right" for="gender"> Default Shift</label>
                                        <div class="col-sm-3">
                                            <div class="control-group"> 
                                                <div class="checkbox">
                                                    <label>
                                                        <input name="hr_shift_default" type="checkbox" value="1" class="ace" data-validation="checkbox_group" data-validation-qty="min1" data-validation-optional="true" {{ ($shift->hr_shift_default == 1)?"checked":"" }}>
                                                        <span class="lbl"></span>
                                                    </label>
                                                </div> 
                                            </div> 
                                        </div>
                                    </div>
                                <!-- PAGE CONTENT ENDS -->
                            </div>

                            <div class="col-sm-12 col-xs-12">
                                <div class="clearfix form-actions">
                                    <div class="col-md-offset-4 col-md-4 text-center"> 
                                        <button class="btn btn-sm btn-success" type="submit">
                                            <i class="ace-icon fa fa-check bigger-110"></i> Update
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
        </div><!-- /.page-content -->
    </div>
</div>

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
});
</script>
@endsection