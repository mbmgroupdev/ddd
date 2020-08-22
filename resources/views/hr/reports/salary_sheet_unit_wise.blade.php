@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
    <style>
        .progress[data-percent]:after {
            color: #000 !important;
        }
    </style>
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
                    <a href="#">Operations</a>
                </li>
                <li class="active"> Salary Sheet</li>
            </ul>
            <!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div id="load"></div>
            <?php $type='salary_sheet'; ?>
                @include('hr/reports/operations_radio')
                <div class="page-header">
                    <h1>Operations<small><i class="ace-icon fa fa-angle-double-right"></i> Salary SheetOperations</small></h1>
                </div>
                <div class="row">
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')
                    <div id="selectOne"></div>
                    <div class="col-sm-10" id="search_area">
                        <form role="form" method="post" action="{{ url('hr/reports/save_salary_sheet2') }}" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="unit"> Unit </label>
                                <div class="col-sm-6">
                                    {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="start_date"> Start Date </label>
                                <div class="col-sm-6">
                                    <input type="text" name="start_date" id="start_date" placeholder="Start Date" class="form-control" data-validation="required" value="{{ Request::get('start_date') }}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="end_date"> End Date </label>
                                <div class="col-sm-6">
                                    <input type="text" name="end_date" id="end_date" placeholder="End Date" class="form-control " data-validation="required" value="{{ Request::get('end_date') }}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="disbursed_date"> Disbursed Date </label>
                                <div class="col-sm-6">
                                    <input type="text" name="disbursed_date" id="disbursed_date" class="form-control datepicker" data-validation="required" data-validation-format="yyyy-mm-dd" autocomplete="off" placeholder="Y-m-d" value="{{ Request::get('disbursed_date') }}" />
                                </div>
                            </div>

                            <div class="form-group text-right">
                                <div class="col-sm-6 col-sm-offset-4">
                                    <button type="button" id="salary_generate" class="btn btn-primary btn-sm">
                                        <i class="fa fa-search"></i> Generate
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="progress progress-striped pos-rel" id="progress_bar_main" data-percent="0%" style="display: none">
                    <div class="progress-bar" id="progress_bar" style="width:0%;"></div>
                </div>
                <div class="row text-center" id="loader"></div>
        </div>
        <!-- /.page-content -->
    </div>
</div>

<script type="text/javascript">
    // click done button to hide process div and show search area
    $(document).on('click', '#done_process', function(){
        $('#search_area').show();
        $('.prepend').remove('');
    });

    $(document).ready(function() {

        function ajax_loader_fn(divId) {
            var loaderPath = "{{asset('assets/rubel/img/loader.gif')}}";
            $("#"+divId).html('<div class="loader-cycle text-center"><img src="'+loaderPath+'" /></div>');
            $('html, body').animate({
                scrollTop: $("#"+divId).offset().top
            }, 2000);
        }        

        $('#salary_generate').on('click', function(){
            // hide form section
            $('#search_area').hide();
            var unit        = $('#unit').val();
            var start_date  = $('#start_date').val();
            var end_date    = $('#end_date').val();
            var disbursed_date = $('#disbursed_date').val();
            if(unit != '' && start_date != '' && end_date != '' && disbursed_date != '') {
                // remove error message
                $('#selectOne').html('');
                ajax_loader_fn('loader');                
                $('#progress_bar_main').before('<h3 class="text-center prepend" id="data_fach_update">Data Fetching</h3>');
                // fetch employee/user list data
                setTimeout(() => {
                    $.ajax({
                        url: '{{ url('hr/reports/save_salary_sheet_unit_wise') }}',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            unit: unit,
                            start_date: start_date,
                            end_date: end_date,
                            disbursed_date: disbursed_date
                        },
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function(res) {
                            if(res != '') {
                                // if list data amount > 100 then it will send data packet wise
                                if(res.employee_count > 100) {
                                    //$('#progress_bar_main').before('<h3 class="text-center prepend">Total Data Found: '+res.employee_count+'</h3>');
                                    var array_count = res.array_count;
                                    var emp_data    = res.employee_list;
                                    // console.log(emp_data);
                                    var percentage  = 0;
                                    for(var i = 0; i < array_count; i++) {
                                        // console.log(i, emp_data[i]);
                                        (function(i){
                                            setTimeout(function(){
                                                $.ajax({
                                                    url: '{{ url('hr/reports/save_salary_sheet_unit_wise_data') }}',
                                                    type: 'POST',
                                                    datatype: 'json',
                                                    data: {
                                                        employeelist: emp_data[i],
                                                        unit: unit,
                                                        start_date: start_date,
                                                        end_date: end_date,
                                                        disbursed_date: disbursed_date
                                                    },
                                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                                    success: function(res) {                                                        percentage = parseInt((i-0)*100/(array_count-1));
                                                        //console.log(i, percentage, emp_data[i]);
                                                        if(i == 0) {
                                                            $('#loader').hide();
                                                            $('#progress_bar_main').show();
                                                            $('#data_fach_update').text('Data Updating');
                                                        }
                                                        if(i == (array_count-1)) {
                                                            percentage = 100;
                                                            $('#progress_bar_main').before('<div class="row text-center prepend"><button class="btn btn-success btn-sm" id="done_process">Click to Done</button></div>');
                                                            $('#progress_bar_main').hide();
                                                        }
                                                        $('#progress_bar').css({width: percentage+'%'});
                                                        $('#progress_bar_main').attr('data-percent', percentage+'%');
                                                    }, error: function($ex) {
                                                        console.log($ex);
                                                    }
                                                });
                                            }, 1000*i);
                                        })(i);
                                    }
                                } else {
                                    // data found 100 or less
                                    $('#progress_bar_main').before('<h3 class="text-center">Total Data Found: '+res.array_count+'</h3>');
                                    console.log('100 or less entity found');
                                }
                            } else {
                                // no data found
                                $('#selectOne').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>No Data Found</div>');
                            }
                        },
                        error: function() {
                            // error occurred
                            $('#selectOne').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Error Occurred</div>');
                        }
                    });
                }, 1000);
            } else {
                // input field validation
                $('#selectOne').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Please select all option</div>');
            }
        });

        // day According to month
        $('#start_date').datetimepicker({
            showClose: true,
            showTodayButton: true,
            dayViewHeaderFormat: "YYYY MMMM",
            format: "YYYY-MM-DD"
        }).on("dp.update", function() {
            $('#end_date').each(function() {
                if ($(this).data('DateTimePicker')) {
                    $(this).data("DateTimePicker").destroy();
                    $(this).val("");
                }
            });
        });

        //end_date
        $("body").on("focusin", '#end_date', function() {
            var startDate = $("#start_date").val();
            if (startDate == "") {
                $("#start_date").val(moment().format("YYYY-MM-DD"));
                var startDate = $("#start_date").val();
            }
            var day = startDate.substring(8, 10);
            var daysInMonth = moment(startDate).daysInMonth();
            var enableDays = daysInMonth - day;
            var lastDay = moment(startDate).add(enableDays, 'days').format("YYYY-MM-DD");
            var firstDay = moment(startDate).format("YYYY-MM-DD");

            $(this).datetimepicker({
                dayViewHeaderFormat: 'MMMM',
                format: "YYYY-MM-DD",
                minDate: firstDay,
                maxDate: lastDay
            });
        });
    });
    // Radio Button Location
    function attLocation(loc) {
        window.location = loc;
    }
</script>
@endsection