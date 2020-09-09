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
                    <a href="#">Time & Attendance</a>
                </li>
                <li class="active">Line Change</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        @include('inc/message')
        <div id="accordion" class="accordion-style panel-group">
            <div class="panel panel-info">
                <div class="panel-heading station-card-content">
                    <h6 class="panel-title">
                        <a class="accordion-toggle collasped" data-toggle="collapse" data-parent="#accordion" href="#multi-search" aria-expanded="false">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Multiple Employee 
                        </a>
                    </h6>
                </div>

                <div class="panel-collapse collapse in show" id="multi-search">
                    <div class="panel-body">
                        {{ Form::open(['url'=>'hr/operation/line-change-multiple', 'class'=>'form-horizontal', 'method'=>'POST']) }}
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'required'=>'required','class'=>'multiple_unit']) }}
                                        <label for="unit"> Unit </label>
                                    </div>   
                                    <div class="form-group">
                                        <label for="associate_id"> Associate's ID </label>
                                        <select id="multiple_associate_id" class="form-control" name="multiple_associate_id[]" multiple="multiple" placeholder="Select employee's" style="height: auto;">
                                            
                                        </select>
                                    </div> 
                                    <div class="form-group">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                        </button>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{Form::select('floor_id_multiple', [], null, ['id'=> 'floor_id_multiple', 'placeholder' => "Select Floor", 'class'=> "no-select form-control", 'required'=>'required'])}}
                                        <label for="floor_id_multiple">Changed Floor </label>
                                    </div>      
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{Form::select('line_id_multiple', [], null, ['id'=> 'line_id_multiple', 'placeholder' => "Select Line", 'class'=> "no-select form-control ", 'required'=>'required'])}}
                                        <label for="line_id_multiple">Changed Line </label>
                                    </div>     

                                    <div class="form-group has-required has-float-label">
                                        <input type="date" name="start_date_multiple" id="start_date_multiple" class="datetimepicker form-control " placeholder="Start Date" required="required">
                                        <label for="shift_id">Start Date </label>
                                    </div> 

                                    <div class="form-group has-required has-float-label">
                                        <input type="date" name="end_date_multiple" id="end_date_multiple"  class="datetimepicker form-control" placeholder="End Date" required="required">
                                        <label for="shift_id">End Date </label>
                                    </div> 
                                </div>
                                <div class="col-sm-6">
                                    <div class="multiple_station_info row ">
                                        
                                    </div>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-heading station-card-content">
                    <h6 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#individual">
                            <i class="bigger-110 ace-icon fa fa-angle-down" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Individual Employee
                        </a>
                    </h6>
                </div>

                <div class="panel-collapse collapse" id="individual">
                    <div class="panel-body">
                        {{ Form::open(['url'=>'hr/operation/line-change', 'class'=>'form-horizontal', 'method'=>'POST']) }}
                            <div class="row"> 
                                <div class="col-sm-3">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates no-select form-control', 'required'=>'required']) }}  
                                        <label for="associate_id"> Associate's ID </label>
                                    </div> 

                                    <div class="form-group has-float-label">
                                        <input type="text" id="unit" class="form-control" readonly>
                                        <label> Unit </label>
                                    </div> 
                                    <div class="form-group has-float-label">
                                        <input type="text" id="floor" class="form-control" readonly>
                                        <label> Floor </label>
                                    </div> 
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{Form::select('floor_id', [], null, ['id'=> 'floor_id', 'placeholder' => "Select Floor", 'class'=> "no-select form-control", 'required'=>'required'])}}
                                        <label for="floor_id">Changed Floor </label>
                                    </div> 
                                    <div class="form-group has-float-label">
                                        <input type="date" name="start_date" id="start_date" class="datetimepicker form-control " placeholder="Start Date" required="required">
                                        <label for="shift_id">Start Date </label>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                        </button>
                                    </div> 
                                    

                                </div>
                                <div class="col-sm-3">  
                                      
                                    <div class="form-group has-float-label">
                                        <input type="text" id="line" class="form-control" readonly>
                                        <label> Line </label>
                                    </div> 
                                    <div class="form-group has-float-label">
                                        <input type="text" id="shift" class="form-control " readonly>
                                        <label> Shift </label>
                                    </div>
                                    <div class="empty-form-group"></div>
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{Form::select('line_id', [], null, ['id'=> 'line_id', 'placeholder' => "Select Line", 'class'=> "no-select form-control ", 'required'=>'required'])}}
                                        <label for="line_id">Changed Line </label>
                                    </div> 
                                    <div class="form-group has-float-label">
                                        <input type="date" name="end_date" id="end_date"  class="datetimepicker form-control" placeholder="End Date" required="required">
                                        <label for="shift_id">End Date </label>
                                    </div> 
                                    
                                </div>
                                <div class="col-sm-6">
                                    <div class="user-details-block" style="padding-top: 3rem;">
                                        <div class="user-profile text-center mt-0">
                                            <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                        </div>
                                        <div class="text-center mt-3">
                                         <h4><b id="name">-------------</b></h4>
                                         <p class="mb-0" id="designation">
                                            --------------------------</p>
                                         <p class="mb-0" >
                                            Oracle ID: <span id="oracle_id" class="text-success">-------------</span>
                                         </p>
                                         <p class="mb-0" >
                                            Associate ID: <span id="associate_id_emp" class="text-success">-------------</span>
                                         </p>
                                         <p  class="mb-0">Department: <span id="department" class="text-success">------------------------</span> </p>
                                         
                                        </div>
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
$(document).ready(function()
{   
    
   
    //get associate information on select associate id
    var url = '{{url('')}}';
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

    $('#start_date').on('dp.change', function(){
        $('#end_date').val($('#start_date').val());
    });    
    
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
                        var app = "<option value="+data[i]['associate_id']+">"+data[i]['associate_id']+
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