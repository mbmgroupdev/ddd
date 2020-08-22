@extends('hr.layout')
@section('title', '')
@section('main-content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#">Human Resource</a>
				</li>
				<li>
					<a href="#">Setup</a>
				</li>
				<li class="active">Add Buyer Mode</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content">

                @include('inc/message')
          <div class="panel panel-info">
              <div class="panel-heading"><h6>Buyer Mode</h6></div> 
                <div class="panel-body">
                <div class="col-sm-12">
                    {{ Form::open(['url'=>'hr/setup/buyermode', 'class'=>'form-horizontal']) }}
                    <div class=" col-sm-offset-2 col-sm-8 add_training">
                        <!-- PAGE CONTENT BEGINS -->
                        <!-- <h1 align="center">Add New Employee</h1> -->
                        </br>

                        <!-- Display Erro/Success Message -->


                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="unit_id"> Unit <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    {{ Form::select('as_unit_id', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'as_unit_id', 'class'=>'col-sm-12 holidaylist','style'=>'width: 100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="template_name"> Template Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input name="template_name" type="text" id="template_name" placeholder="Template Name" class="col-xs-12" data-validation="required" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_description"> Month - Year <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                   <div class="col-sm-6 no-padding-left ">
                                        <input type="text" id="month" name="month" class="set-blank col-xs-12 col-sm-12 currentMonthPicker holidaylist" placeholder="Month" data-validation="required" autocomplete="off"/>
                                   </div>
                                   <div class="col-sm-6  no-padding-left no-padding-right">
                                        <input type="text" name="year" id="year" class="set-blank col-xs-12 col-sm-12 yearpicker holidaylist" placeholder="Year" data-validation="required" autocomplete="off" />
                                   </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="ot_hour">OT Hour<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    {{ Form::select('ot_hour', range(0,14), null, ['placeholder'=>'Select OT hour', 'id'=>'ot_hour', 'class'=> 'col-xs-12 responsive-no-padding-right', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit field is required']) }}  
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_start_date">In-time Slot<span style="color: red; vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                    <div class="col-sm-6 no-padding-left input-icon">
                                        <input type="number" name="intime_slot_1" id="intime_slot_1" placeholder="Start" class="col-xs-12" data-validation="required" />
                                    </div>
                                    <div class="col-sm-6 no-padding-right input-icon-right" id="">
                                        <input type="number" name="intime_slot_2" id="intime_slot_2" placeholder="End" class="col-xs-12" data-validation="required" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_start_date">Out-time Slot<span style="color: red; vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                    <div class="col-sm-6 no-padding-left input-icon">
                                        <input type="number" name="outtime_slot_1" id="outtime_slot_1" placeholder="Start" class="col-xs-12" data-validation="required"/>
                                    </div>
                                    <div class="col-sm-6 no-padding-right input-icon-right" id="">
                                        <input type="number" name="outtime_slot_2" id="outtime_slot_2" placeholder="End" class="col-xs-12" data-validation="required" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="holidays"> Holiday</label>
                                <div class="col-sm-8">
                                   {{--  <div class="col-sm-5 no-padding-left">
                                        <div class="form-group">

                                            <div class="col-sm-9">
                                                <div class="control-group">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="weekdays[]" type="checkbox" value="Saturday" class="ace">
                                                            <span class="lbl"> Saturday</span>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="weekdays[]" type="checkbox" value="Sunday" class="ace">
                                                            <span class="lbl"> Sunday</span>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="weekdays[]" type="checkbox" value="Monday" class="ace">
                                                            <span class="lbl"> Monday</span>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="weekdays[]" type="checkbox" value="Tuesday" class="ace">
                                                            <span class="lbl"> Tuesday</span>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="weekdays[]" type="checkbox" value="Wednesday" class="ace">
                                                            <span class="lbl"> Wednesday</span>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="weekdays[]" type="checkbox" value="Thursday" class="ace">
                                                            <span class="lbl"> Thursday</span>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="weekdays[]" type="checkbox" value="Friday" class="ace">
                                                            <span class="lbl"> Friday</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  --}}
                                    <div id="weekendData" class=""> <span>Select Unit, Enter Month and Year</span></div>


                                </div>
                            </div>


                        </div>
                        <div class="col-sm-12 responsive-hundred">

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

                        <!-- /.row -->
                        <hr />
                        <!-- PAGE CONTENT ENDS -->
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="panel panel-info">
              <div class="panel-heading"><h6>Buyer Mode List</h6></div> 
                <div class="panel-body">
                <div class="col-sm-12">
                    <table id="dataTables" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Unit Name</th>
                                    <th>Template Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($templateList as $template)
                                <tr>
                                    <td>{{ $template->hr_unit_name }}</td>
                                    <td>{{ $template->template_name }}</td>

                                    <td>
                                        <div class="btn-group">
                                            <a type="button" href="{{ url('hr/setup/buyer_template_update/'.$template->id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{ url('hr/setup/buyer_template_delete/'.$template->id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                   </table>
                </div>
            </div>
        </div>
                <!-- /.col -->

		</div><!-- /.page-content -->
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    var action_type = $(".holidaylist");
    var action_element = $("#weekendData");


    action_type.on('change dp.change', function(){
        var unit = $('#as_unit_id').val();
        var month = $('#month').val();
        var year =  $('#year').val();

        if(unit!=''&& month!=''&& year!=''){
            $.ajax({
                url : "{{ url('hr/setup/getholidays') }}",
                type: 'get',
                data: {
                    unit_id : unit,
                    month : month,
                    year : year,
                },
                success: function(data)
                {
                    action_element.html(data);
                },
                error: function(data)
                {
                    alert('failed...');

                }
            });
        }
        else{action_element.html('Select Unit, Enter Month and Year');}
    });



});
</script>

@endsection
