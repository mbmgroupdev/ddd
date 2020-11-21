@extends('hr.layout')
@section('title', 'Line Change')
@section('main-content')
@push('css')
<style type="text/css">
    /*.form-group{margin-bottom: 10px;}*/
    @media only screen and (max-width: 768px) {
        .form-group{margin-bottom: 0px;}
    }

    .station-card-content .panel-title {margin-top: 3px; margin-bottom: 3px;}
    .station-card-content .panel-title a{font-size: 15px; display: block;}
    .select2{width: 100% !important;}
    .panel-group { margin-bottom: 5px;}
    h3.smaller {font-size: 13px;}
    .header {margin-top: 0;}
    .select2-selection--multiple{
         max-height: 50px; overflow: auto;
    }
    .separator {
        width: 63% !important;
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
                    <a href="#">Operation</a>
                </li>
                <li class="active">Line Change</li>
                <li class="top-nav-btn">
                    <a class="btn btn-primary btn-sm pull-right" href="{{ url('hr/reports/line-changes') }}"><i class="fa fa-list"></i> List Of Line Change</a>
                </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        @include('inc/message')
        <div id="accordion" class="accordion-style panel-group">
            <div class="panel panel-info">
                <div class="panel-collapse collapse in show" id="multi-search">
                    <div class="panel-body">
                        {{ Form::open(['url'=>'hr/operation/line-change-multiple', 'class'=>'form-horizontal', 'method'=>'POST']) }}
                            <div class="row">
                                <div class="col">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'required'=>'required','class'=>'multiple_unit']) }}
                                        <label for="unit"> Unit </label>
                                    </div> 
                                </div>
                                <div class="col">  
                                    <div class="form-group has-float-label select-search-group">
                                        <select id="multiple_associate_id" class="form-control" name="multiple_associate_id[]" multiple="multiple" placeholder="Select employee's" style="height: auto;">
                                        </select>
                                        <label for="multiple_associate_id"> Associate's ID </label>
                                    </div> 
                                    
                                    
                                </div>
                                
                                <div class="col">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{Form::select('floor_id_multiple', [], null, ['id'=> 'floor_id_multiple', 'placeholder' => "Select Floor", 'class'=> "no-select form-control", 'required'=>'required'])}}
                                        <label for="floor_id_multiple">Changed Floor </label>
                                    </div> 
                                </div>
                                <div class="col">     
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{Form::select('line_id_multiple', [], null, ['id'=> 'line_id_multiple', 'placeholder' => "Select Line", 'class'=> "no-select form-control ", 'required'=>'required'])}}
                                        <label for="line_id_multiple">Changed Line </label>
                                    </div>     
                                </div>
                                <div class="col">
                                    <div class="form-group has-required has-float-label">
                                        <input type="datetime-local" name="start_date_multiple" id="start_date_multiple" class="datetimepicker form-control " required="required" value="{{ date('Y-m-d') }}T{{ date('H:i')}}">
                                        <label for="start_date_multiple">Start Date </label>
                                    </div> 
                                </div>
                                <div class="col">
                                    <div class="form-group has-float-label">
                                        <input type="datetime-local" name="end_date_multiple" id="end_date_multiple"  class="datetimepicker form-control" placeholder="End Date" >
                                        <label for="end_date_multiple">End Date </label>
                                    </div> 
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="offset-10 col-2">
                                    <div class="">
                                        <div class="form-group">
                                            <button class="btn btn-primary pull-right" type="submit">
                                                <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="multiple_station_info row ">
                                        
                                    </div>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">

    /*$('#multiple_associate_id').select2({
        placeholder: 'Select Employee',
        ajax: {
            url: url+'hr/adminstrator/employee/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {

                return { 
                    keyword: params.term,
                    unit: $("#unit").val()
                }; 
            },
            processResults: function (data) { 
                console.log(data);
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.user_name,
                            id: item.associate_id
                        }
                    }) 
                };
            },
            cache: true
        }
    });*/
$(document).ready(function()
{   
    var url = '{{url('')}}';
    //get associate information on select associate id
    
    $("#associate_id").on("change", function(){

        if($(this).val() != ""){
            $.ajax({
                url: '{{ url("hr/timeattendance/station_as_info") }}',
                data: {associate_id: $(this).val()},
                success: function(data)
                { 
                    $("#unit").val(data.unit);
                    $("#floor").val(data.floor);
                    $("#line").val(data.line);
                    $("#shift").val(data.shift);
                    $("#floor_id").html(data.floorList);

                    $('#associate_id_emp').text(data['associate_id']);
                    $('#oracle_id').text(data['as_oracle_code']);
                    $('#name').text(data['as_name']);
                    $('#department').text(data['hr_department_name']);
                    $('#designation').text(data['hr_designation_name']);
                    
                    $('#avatar').attr('src', url+data['as_pic']); 
                },
                error: function(xhr)
                {
                    alert('failed');
                }
            }); 
        }
    });

    //get line list of selected floor
    $("#floor_id").on("change", function(){

        if($(this).val() != ""){
            $.ajax({
                url: '{{ url("hr/timeattendance/station_line_info") }}',
                data: {floor_id: $(this).val()},
                success: function(data)
                { 
                    $("#line_id").html(data);
                },
                error: function(xhr)
                {
                    alert('failed');
                }
            }); 
        }
    });

    //dates validation..............................

    // $('#start_date').on('dp.change', function(){
    //     $('#end_date').val($('#start_date').val());
    // });    
    
    $('#end_date').on('dp.change', function(){
        var end_date   = new Date($(this).val());
        var start_date = new Date($('#start_date').val());
        // console.log(start_date);
        if(start_date == '' || start_date == null){
            alert("Please enter Start-Date-Time first");
            $('#end_date').val('');
        }
        else{
            // if($end_date == $start_date){
            //     alert("Warning!!\n Start-Date-Time, End-Date-Time are same");
            //     // $('#end_date').val('');
            // }
            if(end_date < start_date){
                alert("Invalid!!\n Start-Date-Time is latest than End-Date-Time");
                $('#end_date').val('');
            }
        }
    });
    //date validation end..............................


});

</script>
<script type="text/javascript">
$(document).ready(function()
{   
    
    //get associate information on select unit
    $(".multiple_unit").on("change", function(){
        console.log($(this).val());
        // var unit_id = 
        if($(this).val() != ""){


            $.ajax({
                url: '{{ url("hr/timeattendance/new_card/multiple_emp_for_unit") }}',
                data: {unit_id: $(this).val()},
                success: function(data)
                { 
                    $("#multiple_associate_id").html('<option value="">Select Employees</option>');    
                     for(var i=0; i<data.length; i++){
                        var app = "<option value="+data[i]['associate_id']+">"+data[i]['as_oracle_code']+'-'+data[i]['associate_id']+
                        "-"+data[i]['as_name']+"</option>";
                        $("#multiple_associate_id").append(app);
                     }
                },
                error: function(xhr)
                {
                    alert('failed');
                }
            }); 

            $.ajax({
                url: '{{ url("hr/timeattendance/new_card/floor_for_unit") }}',
                data: {unit_id: $(this).val()},
                success: function(data)
                {
                    // console.log(data);
                    $("#floor_id_multiple").html(data);
                },
                error: function(xhr)
                {
                    alert('failed');
                }
            }); 
        }
    });

    //get line list of selected floor
    $("#floor_id_multiple").on("change", function(){

        if($(this).val() != ""){
            $.ajax({
                url: '{{ url("hr/timeattendance/station_line_info") }}',
                data: {floor_id: $(this).val()},
                success: function(data)
                { 
                    //console.log( data);
                    $("#line_id_multiple").html(data);
                },
                error: function(xhr)
                {
                    alert('failed');
                }
            }); 
        }
    });



    //get associate information on select associate id
    $("#multiple_associate_id").on("change", function(){

        if($(this).val() != ""){
            $('.app-loader').show();
            $.ajax({
                url: '{{ url("hr/timeattendance/station_multiple_as_info") }}',
                data: {associate_id: $(this).val()},
                success: function(data)
                {
                    $(".multiple_station_info").html(data);
                    $('.app-loader').hide();
                },
                error: function(xhr)
                {
                    $(".multiple_station_info").empty();
                    $('.app-loader').hide();
                    alert('Please Select associate');
                }
            });
        }
    });


    //dates validation..............................

    $('#start_date_multiple').on('dp.change', function(){
        $('#end_date_multiple').val($('#start_date_multiple').val());
    });

    $('#end_date_multiple').on('dp.change', function(){
        var end_date_multiple   = new Date($(this).val());
        var start_date_multiple = new Date($('#start_date_multiple').val());
        // console.log(start_date);
        if(start_date_multiple == '' || start_date_multiple == null){
            alert("Please enter Start-Date-Time first");
            $('#end_date_multiple').val('');
        }
        else{
            // if($end_date == $start_date){
            //     alert("Warning!!\n Start-Date-Time, End-Date-Time are same");
            //     // $('#end_date').val('');
            // }
            if(end_date_multiple < start_date_multiple){
                alert("Invalid!!\n Start-Date-Time is latest than End-Date-Time");
                $('#end_date_multiple').val('');
            }
        }
    });
    //date validation end..............................
});
</script>
@endpush
@endsection