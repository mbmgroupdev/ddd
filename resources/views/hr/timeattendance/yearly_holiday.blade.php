@extends('hr.layout')
@section('title', 'Yearly Holiday Planner')
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
					<a href="#">Operation</a>
				</li>
				<li class="active"> Yearly Holiday Planner</li>
			</ul><!-- /.breadcrumb --> 
		</div>
		<div class="page-content"> 
                @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading"><h6>Yearly Holiday Planner<a href="{{ url('hr/timeattendance/operation/yearly_holidays')}}" class="pull-right btn btn-xx btn-info">Holiday list</a></h6></div> 
                  <div class="panel-body">

                    <div class="row">
                            <form class="form-horizontal" role="form" method="post" action="{{ url('hr/timeattendance/operation/yearly_holidays')}}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                        <div class="col-sm-12 responsive-hundred">
                            <!-- PAGE CONTENT BEGINS -->
                                    <div class="col-sm-12">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="as_unit_id"> Unit </label>
                                            <div class="col-sm-9"> 
                                                {{ Form::select('as_unit_id', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'as_unit_id', 'class'=>'col-sm-12','style'=>'width: 100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}  
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="year"> Year </label>
                                            <div class="col-sm-9">
                                                <input type="text" name="year" id="year" class="col-xs-10 col-sm-12 currentYearPicker" value="{{ date('Y') }}" placeholder="Year" data-validation="required" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <style type="text/css">
                                        .custom .bootstrap-datetimepicker-widget table thead .prev,
                                        .custom .bootstrap-datetimepicker-widget table thead .picker-switch,
                                        .custom .bootstrap-datetimepicker-widget table thead .next{display:none;}
                                        </style>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="month"> Month </label>
                                            <div class="col-sm-9 ">
                                                <input type="text" id="month" name="month" class="col-xs-10 col-sm-12 currentMonthPicker" value="{{ date('F') }}" placeholder="Month"  data-validation="required" autocomplete="off"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="year"> Weekday </label>
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
                                    </div>
                                    <div id="weekendData" class="col-sm-8">
                                    
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div id="holidaysData" class="col-sm-12">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="hr_yhp_dates_of_holidays">Dates Record as Holidays <span style="color: red; vertical-align: top;">&#42;</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="hr_yhp_dates_of_holidays[]" class="col-xs-4 col-sm-3 currentDatePicker" placeholder="Y-m-d" data-validation="required" />

                                                <input type="text" name="hr_yhp_comments[]" class="col-xs-4 col-sm-3" placeholder="Holiday Name" data-validation="required"/>

                                                <div class="form-group col-xs-4 col-sm-3">
                                                    <button type="button" class="btn btn-sm btn-success AddBtn">+</button>
                                                    <button type="button" class="btn btn-sm btn-danger RemoveBtn">-</button>
                                                </div>
                                            </div>
                                        </div> 
                                    </div> 
                                </div>
                    
                            <!-- PAGE CONTENT ENDS -->
                        </div>

                        <div class="col-sm-12 responsive-hundred">
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-4 col-md-4 text-center"> 
                                    <button class="btn btn-sm btn-success" type="submit" id="submitButton">
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
                        <!-- /.col -->
                    </div>
                  </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>

<script type="text/javascript">  
$(document).ready(function() {
    var data = $("#holidaysData").html();
    $('body').on('click', '.AddBtn', function(){
        $("#holidaysData").append(data);
    });

    $('body').on('click', '.RemoveBtn', function(){
        $(this).parent().parent().parent().remove();
    });

    $('.checkbox').on('change',function(e){
        var unit= $('#as_unit_id').val();
        var year= $('#year').val();
        var month= $('#month').val();

        var chkArray = [];
        $(".ace:checked").each(function() {
            chkArray.push($(this).val());
        });

        // e.preventDefault(); 
        $.ajax({
            url: '{{ url("/hr/timeattendance/get_holidays") }}',
            method: "GET",
            data: {'unit' : unit, 'year': year, 'month': month, 'weekdays': chkArray},
            success: function(data)
            {
                // console.log(data)
                if(data){
                $('#weekendData').html(data);
                }
            }
        });
    });
 
    $('.currentMonthPicker').datetimepicker({
        minDate: moment().add(-1, "months"), // Current day
        viewMode: 'months',
        format: "MMMM"
    }).on("dp.update", function(){  
        $('.currentDatePicker').each(function(){
            if($(this).data('DateTimePicker')){
                $(this).data("DateTimePicker").destroy();
                $(this).val("");
            }
        });  
    });

    //currentDatePicker
    $("body").on("focusin", '.currentDatePicker', function(){
        var months = new Array();
        months['January']  = 0; 
        months['February']  = 1; 
        months['March']  = 2; 
        months['April']  = 3; 
        months['May']  = 4; 
        months['June']  = 5; 
        months['July']  = 6; 
        months['August']    = 7; 
        months['September']  = 8; 
        months['October']   = 9; 
        months['November']  = 10; 
        months['December']  = 11;  
        var month = months[(($("#month").val())?($("#month").val()):"January")];
        var year  = (($("#year").val())?($("#year").val()):2018);
        var firstDay = new Date(year, month, 1);
        var lastDay = new Date(year, month+1, 0); 

        $(this).datetimepicker({
            dayViewHeaderFormat: 'MMMM',
            format: "YYYY-MM-DD",
            minDate: firstDay, 
            maxDate: lastDay 
        });  
    });

});
</script>

@endsection
