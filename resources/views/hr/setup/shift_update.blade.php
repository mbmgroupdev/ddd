@extends('hr.layout')
@section('title', 'Shift '.$shift->hr_shift_name)
@section('main-content')
    @push('css')
    @endpush
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="#"> Human Resource </a>
            </li> 
            <li>
                <a href="#"> Library </a>
            </li>
            <li class="active"> Shift </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="row">
       <div class="col-lg-2 pr-0">
           <!-- include library menu here  -->
           @include('hr.settings.library_menu')
       </div>
       <div class="col-lg-10 mail-box-detail">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>
                        Shift : {{ $shift->hr_shift_name }}
                        <a class="btn btn-success ml-3 pull-right" href="{{ url('hr/operation/shift_assign') }}"><i class="fa fa-list"></i> Shift List</a>
                        <a class="btn btn-primary pull-right" href="#list"><i class="fa fa-users"></i> Shift Assign</a>
                    </h6>
                </div> 
                <div class="panel-body">
                    
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/shift_update')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-sm-4">
                                <input type="hidden" value="{{$shift->hr_shift_unit_id}}" name="hr_shift_unit_id">  
                                <div class="form-group has-required has-float-label">
                                    <input type="text" value="{{$shift->hr_unit_name}}" id="hr_shift_unit_id" class="form-control"  readonly="readonly">
                                    <label  for="hr_shift_unit_id"> Unit Name  </label>
                                </div>

                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_shift_name" id="hr_shift_name" placeholder="Shift Name" class="form-control" required="required" value="{{ $shift->hr_shift_name }}"/>
                                    <label  for="hr_shift_name" > Shift Name  </label>
                                </div> 

                                <div class="form-group has-float-label">
                                    <input type="text" name="hr_shift_name_bn" id="hr_shift_name_bn" placeholder="শিফট এর নাম" class="form-control" value="{{ $shift->hr_shift_name_bn }}"/>
                                    <label  for="hr_shift_name_bn" > শিফট (বাংলা) </label>
                                </div>
                                

                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label">
                                    <input type="time" name="hr_shift_start_time" id="hr_shift_start_time" class="form-control" required="required" placeholder="--:--:--" onClick="this.select();" value="{{ $shift->hr_shift_start_time }}" />
                                    <label  for="hr_shift_start_time">Shift Time Start</label>
                                </div>
                                <div class="form-group has-required has-float-label">
                                    <input type="time" name="hr_shift_end_time" id="hr_shift_end_time" class="form-control"  placeholder="--:--:--" onClick="this.select();" required value="{{ $shift->hr_shift_end_time }}"/> 
                                    <label  for="hr_shift_end_time">Shift Time End</label>
                                </div>

                                <div class="form-group has-required has-float-label">
                                    <input type="text" id="hr_shift_break_time" name="hr_shift_break_time" required="required" placeholder="Break time in Minutes" class="form-control" value="{{ $shift->hr_shift_break_time }}"/>
                                    <label  for="hr_shift_break_time">Break Time (Minutes)</label>
                                </div>
                            </div>
                            @php
                                // convert minute to H:i
                                $breakTime = date('H:i', mktime(0, $shift->hr_shift_break_time));

                                // sum end time + break time
                                $break   = strtotime($breakTime)-strtotime("00:00:00");
                                $outTime = date("H:i:s",strtotime($shift->hr_shift_end_time)+$break);
                            @endphp
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label">
                                    <input type="time" id="hr_shift_out_time" name="hr_shift_out_time" required="required" class="form-control" disabled="disabled" value="{{$outTime}}" />
                                    <label  for="hr_shift_out_time">Out Time</label>
                                </div>

                                <div class="form-group">
                                    <label  for="gender"> Default Shift</label>
                                    <div class="control-group"> 
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_shift_default" type="checkbox" value="1" {{ ($shift->hr_shift_default == 1)?"checked":"" }} >
                                                <span class="lbl"></span>
                                            </label>
                                        </div> 
                                    </div> 
                                </div>
                                <input type="hidden" name="hr_shift_id" value="{{ $shift->hr_shift_id}}">
                                <div class="form-group"> 
                                    <button class="btn pull-right btn-primary" type="submit">Update</button>
                                </div>
                                
                            </div>
                                 

                            

                                
                        </div>    
                            
                    </form> 
                </div>
            </div>
            <div id="list" class="panel panel-info">
                <div class="panel-body">
                    <ul class="nav nav-tabs" id="myTab-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="false">Active</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="trash-tab" data-toggle="tab" href="#trash" role="tab" aria-controls="trash" aria-selected="false">Trash</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="active" role="tabpanel" aria-labelledby="active-tab">
                         
                            <div class="table-responsive">
                                <table id="global-datatable" class="table table-striped table-bordered" style="display: block;width: 100%;">
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
                                            <td>{{ $shift->hr_shift_start_time }} - {{ $shift->hr_shift_end_time }}</td>
                                            <td>{{ $shift->hr_shift_break_time }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a type="button" href="{{ url('hr/setup/shift_update/'.$shift->hr_shift_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                    <a href="{{ url('hr/setup/shift/'.$shift->hr_shift_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                                    <button type="button" class="btn btn-xs btn-success shift_times_btn" value="{{ $letters }}" data-toggle="modal" data-target="#myModal"><i class="fa fa-history"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                            <div class="table-responsive">
                                <table id="global-trash" class="table table-striped table-bordered" style="display: block;width: 100%;">
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
                                        @foreach($trashed as $shift)
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
    </div>
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
@endpush
@endsection

