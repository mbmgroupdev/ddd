@extends('hr.layout')
@section('title', ' Shift')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />
    <style>
        .iq-accordion-block{
            padding: 10px 0;
        }
        .iq-accordion.career-style .iq-accordion-block {
            margin-bottom: 15px;
        }
        .select2-container--default .select2-selection--multiple {height: 85px;}
        .portion{background-color: #dff7f5;padding: 15px 5px;}
        .portion .form-control{background: #fff !important;margin-bottom: 5px;}
        .portion .form-group{margin-bottom: 10px;}
        .portion .has-float-label label { color: #000 !important;}

    </style>
@endpush
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
                    <a href="#"> Operation </a>
                </li>
                <li class="active"> Shift </li>
                <li class="top-nav-btn">
                    
                    <a class="btn btn-primary pull-right btn-sm" href="{{ url('hr/operation/shift_assign') }}"><i class="fa fa-users"></i> Shift Assign</a>
                </li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="panel">
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/shift')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group has-required has-float-label select-search-group" style="height:80px;">
                                    {{ Form::select('hr_shift_unit_id[]', $unitList, [], ['id'=>'hr_shift_unit_id', 'class'=> 'form-control', 'required'=>'required', 'multiple']) }} 
                                    <label  for="hr_shift_unit_id"> Unit Name  </label>
                                </div>
                                <p class="mb-3 mt-3"><strong>
                                    <i class="fa fa-clock-o text-primary"></i>
                                    &nbsp; Break
                                </strong></p>
                                <div class="portion">                   
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group has-required has-float-label">
                                                <input type="text" id="hr_shift_break_time" name="hr_shift_break_time" required="required" placeholder="Break time in Minutes" value="{{ old('hr_shift_break_time') ?? 0 }}" class="form-control" onClick="this.select();" />
                                                <label  for="hr_shift_break_time">Break Minute</label>
                                            </div>
                                            
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group has-required has-float-label">
                                                <input type="text" name="hr_default_break_start" id="hr_default_break_start" class="time form-control" value="{{ old('hr_default_break_start') ?? '00:00:00' }}" required="required" placeholder="--:--:--" onClick="this.select();" />
                                                <label  for="hr_default_break_start">Start Time</label>
                                            </div>
                                            
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="extra-break-rule-div"></div>
                                            <button  type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#extra_rule">Add Rule</button>
                                        </div>
                                    </div>
                                </div>
                                
                                        
                                <div class="form-group">Additional Break</div>

                                
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_shift_name" id="hr_shift_name" placeholder="Shift Name" class="form-control" required="required" value="{{ old('hr_shift_name') }}" autocomplete="off" />
                                    <label  for="hr_shift_name" > Name  </label>
                                </div>
                                <div class="form-group has-float-label">
                                    <input type="text" name="hr_shift_name_bn" id="hr_shift_name_bn" placeholder="শিফট এর নাম" class="form-control" autocomplete="off" value="{{ old('hr_shift_name_bn') }}" />
                                    <label  for="hr_shift_name_bn" > নাম (বাংলা) </label>
                                </div> 
                                <p class="mb-3 mt-3"><strong>
                                    <i class="fa fa-money text-primary"></i>
                                    &nbsp; Add Bill
                                </strong></p>
                                <div class="portion"> 
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group has-float-label">
                                                <input type="date" name="rule_start_date" id="rule_start_date" class="time form-control" value="" />
                                                <label  for="rule_start_date">Bill Start Date</label>
                                            </div>
                                            
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group has-float-label">
                                                <input type="date" name="rule_end_date" id="rule_end_date" class="time form-control" value=""  />
                                                <label  for="rule_end_date">Bill End Date</label>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>

                                
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_shift_start_time" id="hr_shift_start_time" class="time form-control" value="{{ old('hr_shift_start_time') ?? '00:00:00' }}" required="required" placeholder="--:--:--" onClick="this.select();" />
                                    <label  for="hr_shift_start_time">Start Time</label>
                                </div>
                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_shift_end_time" id="hr_shift_end_time" class="time form-control" value="{{ old('hr_shift_end_time') ?? '00:00:00' }}"  placeholder="--:--:--" onClick="this.select();" required/> 
                                    <label  for="hr_shift_end_time">End Time</label>
                                </div>{{-- 
                                <div class="form-group has-required has-float-label">
                                    <input type="text" id="hr_shift_out_time" name="hr_shift_out_time" required="required" class="time form-control" disabled="disabled" value="{{ old('hr_shift_out_time')}}" />
                                    <label  for="hr_shift_out_time">Out Time</label>
                                </div> --}}
                                <p class="mb-3 mt-3"><strong>
                                    <i class="fa fa-info-circle text-primary"></i>
                                    &nbsp; Additional  Information
                                </strong></p>
                                <div class="form-group custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                   <input type="checkbox" name="hr_shift_default" class="custom-control-input bg-primary" id="customCheck-2"  value="1">
                                   <label class="custom-control-label" for="customCheck-2"> Mark as default shift</label>
                                </div>
                                <div class="form-group custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                   <input type="checkbox" name="ot_status" class="custom-control-input bg-primary" id="customCheck-1"  value="1">
                                   <label class="custom-control-label" for="customCheck-1"> Mark as full OT</label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('ot_shift', $ot_shift, null , ['id'=>'ot_shift', 'class'=> 'form-control','placeholder'=>'Select OT Shift']) }} 
                                    <label  for="ot_shift"> Include OT Shift  </label>
                                </div>
                            </div>
                            <div class="col-sm-3 " style="border-left:1px solid #d1d1d1">
                                <p class="mb-3"><strong>
                                    <i class="fa fa-history text-primary" aria-hidden="true"></i>
                                    &nbsp; History
                                </strong></p>
                                <p>No history found!</p>
                                
                            </div>

                        </div>
                    </form>
                    
                </div> 
            </div>

            <div id="shift-break-rules" style="display:none;">
                <div class="shift-break-rules">
                    <div class="center"><h5>Add Break Rule</h5></div>
                    <hr>
                    <div class="form-group has-float-label">
                        <input type="text" name="rule_days" id="rule_days" placeholder="Shift Name" class="form-control"  value="" autocomplete="off" />
                        <label for="rule_days" > Days  </label>
                    </div>
                    <div class="form-group has-float-label">
                        <input type="text" name="rule_designation" id="rule_designation" placeholder="Shift Name" class="form-control"  value="" autocomplete="off" />
                        <label for="rule_designation" > Designation  </label>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group has-float-label">
                                <input type="text" name="rule_break_time" id="rule_break_time" class="time form-control" value="" />
                                <label  for="rule_break_time">Break Time</label>
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group has-float-label">
                                <input type="text" name="rule_break_start" id="rule_break_start" class="time form-control" value=""  />
                                <label  for="rule_break_start">Break Start</label>
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group has-float-label">
                                <input type="date" name="rule_start_date" id="rule_start_date" class="time form-control" value="" />
                                <label  for="rule_start_date">Start Date</label>
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group has-float-label">
                                <input type="date" name="rule_end_date" id="rule_end_date" class="time form-control" value=""  />
                                <label  for="rule_end_date">End Date</label>
                            </div>
                            
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm">Add</button>
                </div>
            </div>
            
        </div><!-- /.page-content -->
    </div>
</div>

<div class="modal fade" id="extra_rule" tabindex="-1" role="dialog" aria-labelledby="extra_ruleLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="extra_ruleLabel">Add Extra Rule</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
            <form class="extra-break" >
                @php
                    $days = [
                        'Fri' => 'Friday',
                        'Sat' => 'Saturday',
                        'Sun' => 'Sunday',
                        'Mon' => 'Monday',
                        'Tue' => 'Tuesday',
                        'Wed' => 'Wednesday',
                        'Thu' => 'Thursday'
                    ];
                @endphp
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group has-required has-float-label select-search-group d-block" style="height:85px !important;">
                            {{ Form::select('rule_days[]', $days, [], ['id'=>'rule_days', 'class'=> 'form-control', 'required'=>'required', 'multiple']) }} 
                            <label  for="rule_days"> Days  </label>
                        </div>
                        <div class="form-group has-float-label">
                            <input type="text" name="rule_break_time" id="rule_break_time" class="time form-control" value="" />
                            <label  for="rule_break_time">Break Minute</label>
                        </div>
                        
                        <div class="form-group has-float-label">
                            <input type="date" name="rule_start_date" id="rule_start_date" class="time form-control" value="" />
                            <label  for="rule_start_date">Start Date</label>
                        </div>
                        
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group has-required has-float-label select-search-group d-block" style="height:85px !important;">
                            {{ Form::select('rule_designation[]', $designation, [], ['id'=>'rule_designation', 'class'=> 'form-control', 'required'=>'required', 'multiple']) }} 
                            <label  for="rule_designation"> Designation  </label>
                        </div>
                        <div class="form-group has-float-label">
                            <input type="text" name="rule_break_start" id="rule_break_start" class="time form-control" value=""  />
                            <label  for="rule_break_start">Start Time</label>
                        </div>
                        
                        <div class="form-group has-float-label">
                            <input type="date" name="rule_end_date" id="rule_end_date" class="time form-control" value=""  />
                            <label  for="rule_end_date">End Date</label>
                        </div>
                        
                    </div>
                </div>
            </form>
            
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="add-break" type="button" class="btn btn-primary">Add Break</button>
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
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
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


$(document).on('click','#add-break', function(){
    let form = $('.extra-break').serialize();
    console.log(form);
});



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
    var sum = (moment.utc(endTime,'HH:mm').add(breakTime,'minutes').format('HH:mm:ss'));
    // var sum = additionTime(endTime,convertMinsToHrsMins(breakTime));
    $('#hr_shift_out_time').val(sum);
});

    $(document).on('click','.add-extra-break-rule', function(){
        let rules = $('#shift-break-rules').html(),
            strndom = btoa(Math.random()).substr(3, 13);

        $(this).prev().append(rules);
        // modify id with a random string
        $(this).prev().children().last().find('[id]').map(function(q, i) {
            $(this).attr('id', $(this).attr('id')+strndom)
        });
        $(this).prev().children().last().find('[for]').map(function(q, i) {
            $(this).attr('for', $(this).attr('for')+strndom)
        });
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
    $('.time').datetimepicker({
      format:'HH:mm:ss',
      allowInputToggle: false
    });
    
});
</script>
@endpush
@endsection

