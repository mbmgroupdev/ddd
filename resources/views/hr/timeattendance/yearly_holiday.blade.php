@extends('hr.layout')
@section('title', 'Yearly Holiday Planner')
@section('main-content')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />
@endpush
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
                <li class="top-nav-btn">
                    <a href="{{ url('hr/operation/yearly_holidays')}}" class="pull-right btn btn-sm btn-success"><i class="fa fa-list"></i> Holiday list</a>
                </li>
			</ul><!-- /.breadcrumb --> 
		</div>
		<div class="page-content"> 
            @include('inc/message')
            <div class="panel panel-info">
                {{-- <div class="panel-heading"><h6>Yearly Holiday Planner<a href="{{ url('hr/operation/yearly_holidays')}}" class="pull-right btn btn-sm btn-success"><i class="fa fa-list"></i> Holiday list</a></h6></div>  --}}
                    <div class="panel-body">

                        <div class="row">
                            <form class="form-horizontal" role="form" method="post" action="{{ url('hr/timeattendance/operation/yearly_holidays')}}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col-sm-12 responsive-hundred">
                                    <!-- PAGE CONTENT BEGINS -->
                                    <div class="row">
                                        <div class="col-sm-8 offset-4">
                                            <div class="form-group has-float-label has-required select-search-group">
                                                <select name="as_unit_id" class="form-control capitalize select-search" id="unit" required="">
                                                    <option selected="" value="">Choose...</option>
                                                    @foreach($unitList as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                              <label for="unit">Unit</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-8 offset-4">
                                            <div class="form-group has-float-label has-required">
                                                <input type="month" class="report_date form-control" id="month_year" name="month_year" placeholder=" Month-Year"required="required" value="{{ date('Y-m')}}"autocomplete="off" />
                                                <label for="month_year"> Month </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
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
                                <div class="">
                                    <div id="holidaysData" >
                                        <div class="form-group row">
                                            <label class="col-sm-3 control-label no-padding-right" for="hr_yhp_dates_of_holidays">Dates Record as Holidays <span style="color: red; vertical-align: top;">&#42;</span></label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="date" name="hr_yhp_dates_of_holidays[]" class="form-control col-sm-4 currentDatePicker" placeholder="Y-m-d" data-validation="required" />

                                                    <input type="text" name="hr_yhp_comments[]" class="form-control col-xs-4 col-sm-3" placeholder="Holiday Name"/>

                                                    <div class="form-group col-xs-4 col-sm-3">
                                                        <button type="button" class="btn btn-sm btn-success AddBtn">+</button>
                                                        <button type="button" class="btn btn-sm btn-danger RemoveBtn">-</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div> 
                                </div>
                    
                            <!-- PAGE CONTENT ENDS -->
                        </div>

                        <div class=" responsive-hundred">
                            <div class="clearfix form-actions row">
                                <div class="offset-4 col-md-4 text-center"> 
                                    <button class="btn btn-sm btn-success" type="submit" id="submitButton">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                    </button>

                                    <button class="btn btn-sm btn-danger" type="reset">
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
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript">  
$(document).ready(function() {
    $("#currentYearPicker").datetimepicker();
    var data = $("#holidaysData").html();
    $('body').on('click', '.AddBtn', function(){
        $("#holidaysData").append(data);
    });

    $('body').on('click', '.RemoveBtn', function(){
        $(this).parent().parent().parent().remove();
    });

    $('.checkbox').on('change',function(e){
        var unit= $('#as_unit_id').val();
        var monthYear= $('#month_year').val();

        var chkArray = [];
        $(".ace:checked").each(function() {
            chkArray.push($(this).val());
        });

        // e.preventDefault(); 
        $.ajax({
            url: '{{ url("/hr/timeattendance/get_holidays") }}',
            method: "GET",
            data: {'unit' : unit, 'month_year': monthYear, 'weekdays': chkArray},
            success: function(data)
            {
                console.log(data)
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
@endpush
@endsection
