@extends('hr.layout')
@section('title', 'Add Shift')
@section('main-content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="#"> Human Resource </a>
            </li> 
            <li>
                <a href="#"> Operation </a>
            </li>
            <li class="active">Add Shift </li>
            <li class="top-nav-btn">
                <a class="btn btn-success mr-3 btn-sm" href="{{ url('hr/operation/shift_assign') }}"><i class="fa fa-list"></i> Shift List</a>
                <a class="btn btn-primary pull-right btn-sm" href="#list"><i class="fa fa-users"></i> Shift Assign</a>
            </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="row">
       <div class="col-sm-12 mail-box-detail">
            <div class="panel panel-info">
                <div class="panel-body">
                    
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/shift')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('hr_shift_unit_id', $unitList, null, ['placeholder'=>'Select Unit Name', 'id'=>'hr_shift_unit_id', 'class'=> 'form-control', 'required'=>'required']) }} 
                                    <label  for="hr_shift_unit_id"> Unit Name  </label>
                                </div>

                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_shift_name" id="hr_shift_name" placeholder="Shift Name" class="form-control" required="required"/>
                                    <label  for="hr_shift_name" > Shift Name  </label>
                                </div> 

                                
                                

                                
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-required has-float-label">
                                    <input type="time" name="hr_shift_start_time" id="hr_shift_start_time" class="form-control" required="required" placeholder="--:--:--" onClick="this.select();" />
                                    <label  for="hr_shift_start_time">Shift Time Start</label>
                                </div>
                                <div class="form-group has-float-label">
                                    <input type="text" name="hr_shift_name_bn" id="hr_shift_name_bn" placeholder="শিফট এর নাম" class="form-control" />
                                    <label  for="hr_shift_name_bn" > শিফট (বাংলা) </label>
                                </div>
                                
                                
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-required has-float-label">
                                    <input type="time" name="hr_shift_end_time" id="hr_shift_end_time" class="form-control"  placeholder="--:--:--" onClick="this.select();" required/> 
                                    <label  for="hr_shift_end_time">Shift Time End</label>
                                </div>

                                <div class="form-group has-required has-float-label">
                                    <input type="text" id="hr_shift_break_time" name="hr_shift_break_time" required="required" placeholder="Break time in Minutes" class="form-control"/>
                                    <label  for="hr_shift_break_time">Break Time (Minutes)</label>
                                </div>
                                
                                
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-required has-float-label">
                                    <input type="time" id="hr_shift_out_time" name="hr_shift_out_time" required="required" class="form-control" disabled="disabled" />
                                    <label  for="hr_shift_out_time">Out Time</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                   <input type="checkbox" name="hr_shift_default" class="custom-control-input bg-primary" id="customCheck-1"  value="1">
                                   <label class="custom-control-label" for="customCheck-1"> Default Shift</label>
                                </div>
                                
                                <div class="form-group"> 
                                    <button class="btn pull-right btn-primary w-80" type="submit">Submit</button>
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

