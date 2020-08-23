@extends('hr.layout')
@section('title', '')
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
                <li class="active">Station Card</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Time & Attendance<small> <i class="ace-icon fa fa-angle-double-right"></i>Station Card</small></h1>
            </div>

            <!-- Display Erro/Success Message -->
                @include('inc/message')

            {{-- <div class="row">

                <!-- Display Erro/Success Message -->
                @include('inc/message')

                <div class="col-xs-12">
                    {{ Form::open(['url'=>'hr/timeattendance/new_card', 'class'=>'form-horizontal', 'method'=>'POST']) }}
                    <div class="col-sm-5 col-xs-12 responsive-hundred">
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="associate_id"> Associate's ID </label>
                            <div class="col-sm-8">
                                {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }}  

                            </div>
                        </div> 

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Unit </label>
                            <div class="col-sm-8">
                                <input type="text" id="unit" class="col-xs-12" readonly>
                            </div>
                        </div>   
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Floor </label>
                            <div class="col-sm-8">
                                <input type="text" id="floor" class="col-xs-12" readonly>
                            </div>
                        </div>   
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Line </label>
                            <div class="col-sm-8">
                                <input type="text" id="line" class="col-xs-12" readonly>
                            </div>
                        </div>   
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Shift </label>
                            <div class="col-sm-8">
                                <input type="text" id="shift" class="col-xs-12 " readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2"></div>

                    <div class="col-sm-5 col-xs-12 responsive-hundred">     
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="floor_id">Changed Floor </label>
                            <div class="col-sm-8">
                                {{Form::select('floor_id', [], null, ['id'=> 'floor_id', 'placeholder' => "Select Floor", 'class'=> "no-select col-xs-12", 'data-validation'=>'required'])}}
                            </div>
                        </div>      
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="line_id">Changed Line </label>
                            <div class="col-sm-8">
                                {{Form::select('line_id', [], null, ['id'=> 'line_id', 'placeholder' => "Select Line", 'class'=> "no-select col-xs-12 ", 'data-validation'=>'required'])}}
                            </div>
                        </div>     

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="shift_id">Start Date </label>
                            <div class="col-sm-8">
                                <input type="text" name="start_date" id="start_date" class="datetimepicker col-xs-12 " placeholder="Start Date" data-validation="required">
                            </div>
                        </div> 

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="shift_id">End Date </label>
                            <div class="col-sm-8">
                                <input type="text" name="end_date" id="end_date"  class="datetimepicker col-xs-12" placeholder="End Date" data-validation="required">
                            </div>
                        </div> 
                    </div>   
                     
                <div class="col-sm-12 responsive-hundred">
                        
                        <div class="clearfix form-actions ">
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

                    {{ Form::close() }}
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
            </div> --}}

            <div id="accordion" class="accordion-style panel-group">
                <div class="panel panel-info">
                    <div class="panel-heading station-card-content">
                        <h2 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#individual">
                                <i class="bigger-110 ace-icon fa fa-angle-down" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                &nbsp;Individual
                            </a>
                        </h2>
                    </div>

                    <div class="panel-collapse collapse in" id="individual">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    {{ Form::open(['url'=>'hr/timeattendance/new_card', 'class'=>'form-horizontal', 'method'=>'POST']) }}
                                    <div class="col-sm-5 col-xs-12 responsive-hundred">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="associate_id"> Associate's ID </label>
                                            <div class="col-sm-8">
                                                {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }}  

                                            </div>
                                        </div> 

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right"> Unit </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="unit" class="col-xs-12" readonly>
                                            </div>
                                        </div>   
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right"> Floor </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="floor" class="col-xs-12" readonly>
                                            </div>
                                        </div>   
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right"> Line </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="line" class="col-xs-12" readonly>
                                            </div>
                                        </div>   
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right"> Shift </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="shift" class="col-xs-12 " readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2"></div>

                                    <div class="col-sm-5 col-xs-12 responsive-hundred">     
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="floor_id">Changed Floor </label>
                                            <div class="col-sm-8">
                                                {{Form::select('floor_id', [], null, ['id'=> 'floor_id', 'placeholder' => "Select Floor", 'class'=> "no-select col-xs-12", 'data-validation'=>'required'])}}
                                            </div>
                                        </div>      
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="line_id">Changed Line </label>
                                            <div class="col-sm-8">
                                                {{Form::select('line_id', [], null, ['id'=> 'line_id', 'placeholder' => "Select Line", 'class'=> "no-select col-xs-12 ", 'data-validation'=>'required'])}}
                                            </div>
                                        </div>     

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="shift_id">Start Date </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="start_date" id="start_date" class="datetimepicker col-xs-12 " placeholder="Start Date" data-validation="required">
                                            </div>
                                        </div> 

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="shift_id">End Date </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="end_date" id="end_date"  class="datetimepicker col-xs-12" placeholder="End Date" data-validation="required">
                                            </div>
                                        </div> 
                                    </div>   
                           
                                <div class="col-sm-12 responsive-hundred">
                                        
                                        <div class="clearfix form-actions ">
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

                                    {{ Form::close() }}
                                    <!-- PAGE CONTENT ENDS -->
                                </div>
                                <!-- /.col -->
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading station-card-content">
                        <h4 class="panel-title">
                            <a class="accordion-toggle collasped" data-toggle="collapse" data-parent="#accordion" href="#multi-search" aria-expanded="false">
                                <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                &nbsp;Multiple
                            </a>
                        </h4>
                    </div>

                    <div class="panel-collapse collapse" id="multi-search">
                        <div class="panel-body">
                            
                            
                            <div class="row">
                                <div class="col-xs-12">
                                    {{ Form::open(['url'=>'hr/timeattendance/new_card_multiple', 'class'=>'form-horizontal', 'method'=>'POST']) }}
                                    <div class="col-sm-5 col-xs-12 responsive-hundred">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="unit"> Unit <span class="text-red" style="vertical-align: top;">&#42;</span> : </label>
                                            <div class="col-sm-8">
                                                {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required', 'class'=>'multiple_unit']) }}
                                                <span class="text-red" id="error_unit_s"></span>
                                            </div>
                                        </div>   
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="associate_id"> Associate's ID </label>
                                            <div class="col-sm-8">
                                                {{-- {{ Form::select('multiple_associate_id', [], null, ['id'=>'multiple_associate_id', 'class'=> 'multiple_associates no-select col-xs-12 multiple', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required', 'multiple' => "multiple"]) }} --}}

                                                <select id="multiple_associate_id" name="multiple_associate_id[]" multiple="multiple">
                                                    
                                                </select>

                                            </div>
                                        </div> 

                                        <div class="multiple_station_info"></div>

                                        
                                    </div>

                                    <div class="col-sm-2"></div>

                                    <div class="col-sm-5 col-xs-12 responsive-hundred">     
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="floor_id_multiple">Changed Floor </label>
                                            <div class="col-sm-8">
                                                {{Form::select('floor_id_multiple', [], null, ['id'=> 'floor_id_multiple', 'placeholder' => "Select Floor", 'class'=> "no-select col-xs-12", 'data-validation'=>'required'])}}
                                            </div>
                                        </div>      
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="line_id_multiple">Changed Line </label>
                                            <div class="col-sm-8">
                                                {{Form::select('line_id_multiple', [], null, ['id'=> 'line_id_multiple', 'placeholder' => "Select Line", 'class'=> "no-select col-xs-12 ", 'data-validation'=>'required'])}}
                                            </div>
                                        </div>     

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="shift_id">Start Date </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="start_date_multiple" id="start_date_multiple" class="datetimepicker col-xs-12 " placeholder="Start Date" data-validation="required">
                                            </div>
                                        </div> 

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="shift_id">End Date </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="end_date_multiple" id="end_date_multiple"  class="datetimepicker col-xs-12" placeholder="End Date" data-validation="required">
                                            </div>
                                        </div> 
                                        <div class="clearfix form-actions ">
                                            <div class="col-sm-4"> &nbsp;</div>
                                            <div class="col-md-8 no-padding">
                                                <button class="btn btn-sm btn-success" type="submit">
                                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                                </button>

                                                &nbsp; &nbsp; &nbsp;
                                                <button class="btn btn-sm" type="reset">
                                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 responsive-hundred">
                                        {{ Form::close() }}
                                    </div>
                                <!-- /.col -->
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function()
{   
    function formatState (state) {
        //console.log(state.element);
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "/user/pages/images/flags";
        var $state = $(
        '<span><img /> <span></span></span>'
        );
        // Use .text() instead of HTML string concatenation to avoid script injection issues
        var targetName = state.name;
        $state.find("span").text(targetName);
        // $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".png");
        return $state;
    };

    $('select.associates').select2({
        templateSelection:formatState,
        placeholder: 'Select Associate\'s ID',
        ajax: {
            url: '{{ url("hr/associate-search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: $("<span><img src='"+(item.as_pic ==null?'/assets/images/avatars/profile-pic.jpg':item.as_pic)+"' height='50px' width='auto'/> " + item.associate_name + "</span>"),
                            id: item.associate_id,
                            name: item.associate_name
                        }
                    }) 
                };
          },
          cache: true
        }
    }); 

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
                    // console.log(data);
                     // $("#multiple_associate_id").select2({ data: data });
                     $("#multiple_associate_id").html('<option value="">Select Employees</option>');    
                     for(var i=0; i<data.length; i++){
                        var app = "<option value="+data[i]['associate_id']+">"+
                        "<span><img src='"+(data[i]['as_pic'] ==null?'/assets/images/avatars/profile-pic.jpg':data[i]['as_pic'])+"' height='10px' width='10px'/> "+
                        "-"+data[i]['associate_id']+
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
            // var asIds = $(this).val();
            $.ajax({
                url: '{{ url("hr/timeattendance/station_multiple_as_info") }}',
                data: {associate_id: $(this).val()},
                success: function(data)
                {
                    $(".multiple_station_info").html(data);
                },
                error: function(xhr)
                {
                    $(".multiple_station_info").empty();
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
@endsection