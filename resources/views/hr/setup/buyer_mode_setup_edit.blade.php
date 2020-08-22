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
            <div class="page-header"> 
				<h1>Setup<small> <i class="ace-icon fa fa-angle-double-right"></i> Add Buyer Mode</small></h1> 
            </div>

       
                @include('inc/message')
                <div class="col-sm-12 panel panel-default">  
                    {{ Form::open(['url'=>'hr/setup/buyer_template_update', 'class'=>'form-horizontal']) }}
                    <div class=" col-sm-offset-2 col-sm-8 add_training">
                        <!-- PAGE CONTENT BEGINS -->
                        <!-- <h1 align="center">Add New Employee</h1> -->
                        </br> 

                        <!-- Display Erro/Success Message -->


                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="unit_id"> Unit <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('as_unit_id', $unitList, $template->hr_unit_id, ['placeholder'=>'Select Unit', 'id'=>'as_unit_id', 'class'=>'col-sm-12 holidaylist','style'=>'width: 100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                                </div>
                            </div>

                            <div class="form-group"> 
                                <label class="col-sm-3 control-label no-padding-right" for="template_name"> Template Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input name="template_name" type="text" id="template_name" placeholder="Template Name" class="col-xs-12" data-validation="required" value="{{$template->template_name}}" />
                                </div>
                            </div> 
     
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_description"> Month - Year <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                   <div class="col-sm-6 no-padding-left ">
                                    @php
                                     
                                      $monthNumber = date("m",strtotime($template->month_year));
                                      $month=date('M', mktime(0, 0, 0, $monthNumber, 1));
                                      $year=date('Y', mktime(0, 0, 0, $monthNumber, 1));
                                    @endphp
                                        <input type="text" id="month" name="month" class="set-blank col-xs-12 col-sm-12 currentMonthPicker holidaylist" placeholder="Month" data-validation="required" autocomplete="off" value="{{$month}}" />

                                   </div> 
                                   <div class="col-sm-6  no-padding-left no-padding-right">
                                        <input type="text" name="year" id="year" class="set-blank col-xs-12 col-sm-12 yearpicker holidaylist" placeholder="Year" data-validation="required" autocomplete="off" value="{{$year}}" />
                                   </div>   
                                </div>
                            </div>  
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="ot_hour">OT Hour<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('ot_hour', range(0,20), $template->ot_hour, ['placeholder'=>'Select OT hour', 'id'=>'ot_hour', 'class'=> 'col-xs-12 responsive-no-padding-right', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit field is required']) }}  
                                </div>
                            </div>
                             <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_start_date">In-time Slot<span style="color: red; vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                    <div class="col-sm-6 no-padding-left input-icon">
                                        <input type="number" name="intime_slot_1" id="intime_slot_1" placeholder="Start" class="col-xs-12 form-control" data-validation="required" value="{{$template->in_time_start_range}}" />
                                    </div>
                                    <div class="col-sm-6 no-padding-right input-icon-right" id="">
                                        <input type="number" name="intime_slot_2" id="intime_slot_2" placeholder="End" class="col-xs-12" data-validation="required" value="{{$template->in_time_end_range}}" /> 
                                    </div>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="tr_start_date">Out-Time  Slot<span style="color: red; vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                    <div class="col-sm-6 no-padding-left input-icon">
                                        <input type="number" name="outtime_slot_1" id="outtime_slot_1" placeholder="Start" class="col-xs-12" data-validation="required" value="{{$template->out_time_start_range}}" />
                                    </div>
                                    <div class="col-sm-6 no-padding-right input-icon-right" id="">
                                        <input type="number" name="outtime_slot_2" id="outtime_slot_2" placeholder="End" class="col-xs-12" data-validation="required"  value="{{$template->out_time_end_range}}" /> 
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="holidays"> Holiday</label>
                                <div class="col-sm-8"> 
                                     
                                    <div id="weekendData"> 
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Comment</th>
                                                    <th>Open Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>                                                         

                                            @foreach ($holiday as  $value)
                                        
                                            @php 
                                                $statusname="open_status[$value->hr_yhp_id]";

                                                if(!empty($value->hr_buyer_mode_open_status)){

                                                
                                                    $statusValue1 = [];
                                                    $OldstatusValue=$value->hr_buyer_mode_open_status;
                                                    if(strpos($OldstatusValue, $template->temp_id.'-') !== false){
                                                        $statusSplit= explode(',',$OldstatusValue);  
                                                        // print_r($statusSplit);
                                                     
                                                        foreach($statusSplit as $key) {
                                                            $template_id=explode('-',$key); 
                                                            if($template_id[0]==$template->temp_id){
                                                                $statusValue1[$value->hr_yhp_id]=$template_id[1];
                                                            }         
                                                        }
                                                    } else {
                                                        $statusValue1[$value->hr_yhp_id]=$value->hr_yhp_open_status;
                                                    }  
                                                    // print_r($statusValue1); 
                                                }
                                                else {                                                  
                                                     $statusValue1[$value->hr_yhp_id]=$value->hr_yhp_open_status;
                                                }
                                            @endphp

                                            <tr>
                                              <td style="padding:0px;">
                                                <input type="hidden" name="hp_id[]" value="{{ $value->hr_yhp_id}}" readonly="readonly">
                                                <input type="text"  value="{{ $value->hr_yhp_dates_of_holidays}}" readonly="readonly"></td>                            
                                              <td style="padding:0px;"><input type="text" value="{{ $value->hr_yhp_comments}}" readonly="readonly"></td>
                                              <td style="padding:0;" width="40%">
                                                <label class="radio-inline" style="font-size:11px;padding:0 0 0 16px;">
                                                  <input type="radio" name="{{$statusname}}" class="open_status" value="0" style="margin-left:-15px" {{($statusValue1[$value->hr_yhp_id]==0?'checked':'')}}> Holiday

                                                </label>
                                                <label class="radio-inline" style="font-size:11px;padding:0 0 0 10px;">
                                                  <input type="radio" name="{{$statusname}}" class="open_status" value="1" {{($statusValue1[$value->hr_yhp_id]==1?'checked':'')}}> General

                                                </label>
                                                <label class="radio-inline" style="font-size:11px;padding:0 0 0 10px;">
                                                  <input type="radio" name="{{$statusname}}" class="open_status" value="2" {{($statusValue1[$value->hr_yhp_id]==2?'checked':'')}}> OT

                                                </label>
                                               
                                              </td>                               
                                            </tr>

                                            
                                        @endforeach

                                    </tbody>
                                    </table>

                                    {{-- <span>Select Unit, Enter Month and Year</span> --}}
                                 </div>


                                </div>
                            </div>

                            
     
                        </div>
                        <div class="col-sm-12 responsive-hundred">
     
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-4 col-md-4 text-center"> 
                                    <input type="hidden" name="templateId" value="{{$template->temp_id}}">
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