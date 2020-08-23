@extends('hr.layout')
@section('title', 'Bonus Sheet')
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
                    <a href="#"> Operations </a>
                </li>
                <li class="active"> Bonus Sheet </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="row">
                @include('inc/message')
                <div class="row">
                    <form  id="searchform" class="form-horizontal" role="form" method="get" action="{{ url('hr/reports/new_bonus_slip') }}">
                        <div class="col-sm-10"> 
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="unit_id"> Unit <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        {{ Form::select('unit_id', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required']) }} 
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label class="col-sm-4 control-label no-padding-right" for="unit_id"> Bonus Type <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        <select class="col-xs-12" id="bonus_type_id" name="bonus_type_id">
                                            <option value="not_selected">Select Bonus type</option>
                                            @if($bonus_types)    
                                                @foreach($bonus_types as $bt)
                                                    <option value="{{$bt->id}}"
                                                        @if($bt->id == Request::get('bonus_type_id')) 
                                                            selected="selected"
                                                        @endif 
                                                        >{{$bt->bonus_type_name}}</option>
                                                @endforeach
                                            @else
                                            No data
                                            @endif
                                        </select> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="floor_id"> Floor </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('floor_id', !empty(Request::get('floor_id'))?$floorList:[], Request::get('floor_id'), ['placeholder'=>'Select Floor', 'id'=>'floor_id', 'class'=> 'col-xs-12']) }} 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="job_app_id">Month-Year</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="bt_month_year" id="bt_month_year" class="col-xs-12" placeholder="Month-Year of Bonus Type" readonly="readonly" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-5 control-label no-padding-right" for="department_id"> Department </label>
                                    <div class="col-sm-7">
                                        {{ Form::select('department_id', $deptList, Request::get('department_id'), ['placeholder'=>'Select Department', 'id'=>'department_id', 'class'=> 'col-xs-12']) }}
                                    </div>
                                </div>
                            </div>
                             <div class="col-sm-4">    
                             <div class="form-group">
                                    <label class="col-sm-5 control-label no-padding-right" for="job_app_id">Bonus Process Date <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-7">
                                        <input type="text" name="bonus_process_date" id="bonus_process_date" class="col-xs-12 datepicker" value="{{ Request::get('bonus_process_date')? Request::get('bonus_process_date'): date('Y-m-d') }}" data-validation="required"/>
                                    </div>
                                </div>
                            </div>    
                               
                          
                            <div class="col-sm-4 col-sm-offset-8 text-right" >
                                <div class="form-group">
                                    <button type="button" id="see-report" class="btn btn-primary btn-sm search_button" disabled="disabled">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <br><br>
            </div>
            <input type="hidden" value="0" id="setFlug">

            <div class="progress" id="result-process-bar" style="display: none;">
                <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress_bar_main" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                  0%
                </div>
            </div>

            <div  id="bonus_content_section" class="row" style="overflow-y: auto; height: 500px; ">

            </div> 
        </div><!-- /.page-content -->
    </div>
</div>



<script type="text/javascript">
    
function printMe(divName)
{ 
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write(document.getElementById(divName).innerHTML); 
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}

function errorMsgRepeter(id, check, text){
    var flug1 = false;
    if(check == ''){
        alert(text);
        flug1 = false;
    }else{
        $('#'+id).html('');
        flug1 = true;
    }
    return flug1;
}
$(document).ready(function(){ 

    //Fetching Month Year of Bonus Type
    $('body').on('change', '#bonus_type_id', function(){
        $('.search_button').removeAttr('disabled');
        var  bt_id = $(this).val();
        // console.log(bt_id);
        var d = new Date();
        var today_month = d.getMonth()+1;
        if(bt_id != 'not_selected'){   
               $.ajax({
                    url : "{{ url('hr/reports/bonustype_month_year_by_id') }}",
                    type: 'get',
                    data: {bt_id : bt_id},
                    success: function(data)
                    {
                        $("#bt_month_year").val(data['month']+'-'+data['year']);
                        // if(data['ck_month'] != today_month ){
                        //     alert("This month is not eligible to generate bonus sheet.");
                        //     $('.search_button').attr('disabled', 'disabled');
                        // }
                    },
                    error: function()
                    {
                        alert('failed...');
                    }
               });
        }
        else{
             $('.search_button').attr('disabled', 'disabled');
             $("#bt_month_year").val('');
        }
    });

    // loader visibility
      $('#searchform').submit(function() {
        $('#load').css('visibility', 'visible');
        }); 

    $(document).on('click','#see-report', function(){

        var flug = new Array();
        var unit_id = $('select[name="unit_id"]').val(),
            bonus_process_date = $('input[name="bonus_process_date"]').val();
        flug.push(errorMsgRepeter('error_unit_s',unit_id,'Unit should not empty'));
        flug.push(errorMsgRepeter('error_process_s',bonus_process_date,'Bonus process date should not empty'));
        // flug.push(errorMsgRepeter('error_area_s',area,'Area not empty'));
        //flug.push(errorMsgRepeter('error_month_s',month,'Month not empty'));
        // flug.push(errorMsgRepeter('error_department_s',department,'Department not empty'));
        //flug.push(errorMsgRepeter('error_year_s',year,'Year not empty'));
        // flug.push(errorMsgRepeter('error_status_s',employee_status,'Status not empty'));

       // console.log(flug);
        if(jQuery.inArray(false, flug) === -1){
            // remove all append message
            $('.prepend').remove();
            $("#see-report").attr('disabled','disabled');

            $('html, body').animate({
                scrollTop: $("#bonus_content_section").offset().top
            }, 2000);
            $("#bonus_content_section").html('');

            $("#result-process-bar").css('display', 'block');
            $('#setFlug').val(0);
            processbar(0);
            var dataObj = {
                unit_id : unit_id,
                floor_id : $('select[name="floor_id"]').val(),
                department_id : $('select[name="department_id"]').val(),
                bonus_type_id : $('select[name="bonus_type_id"]').val(),
                bonus_process_date: bonus_process_date
                /*department : department,
                section : sectionF,
                sub_section : sub_section,
                ot_range : ot_range,
                month : month,
                year : year,
                employee_status : employee_status,
                min_sal : min_sal,
                max_sal : max_sal,
                disbursed_date : disbursed_date*/
            };
            setTimeout(() => {
                $.ajax({
                    url: url+'/hr/reports/bonus-slip-generate',
                    type: "GET",
                    dataType : 'html',
                    data: dataObj,
                    success: function(response){
                        // console.log(response.length);
                        if(response !== 'error'){
                            $('#setFlug').val(1);
                            processbar('success');
                            $('.prepend').remove();
                            setTimeout(() => {
                                $("#bonus_content_section").html(response);
                                $("#see-report").removeAttr('disabled');
                            }, 1000);


                        }else{
                            $('#setFlug').val(2);
                            processbar('error');
                        }
                    }, error: function() {
                        processbar('error');
                        $('#setFlug').val(2);
                    }
                });
            }, 1000);
        }
    });

    var incValue = 1;

    function processbar(percentage) {
        var setFlug = $('#setFlug').val();
        if(parseInt(setFlug) === 1){
            var percentageVaule = 99;
            $('#progress_bar_main').html(percentageVaule+'%');
            $('#progress_bar_main').css({width: percentageVaule+'%'});
            $('#progress_bar_main').attr('aria-valuenow', percentageVaule+'%');
            setTimeout(() => {
                $("#result-process-bar").css('display', 'none');
                percentageVaule = 0;
                percentage = 0;
                $('#progress_bar_main').html(percentageVaule+'%');
                $('#progress_bar_main').css({width: percentageVaule+'%'});
                $('#progress_bar_main').attr('aria-valuenow', percentageVaule+'%');
            }, 1000);
        }else if(parseInt(setFlug) === 2){
            console.log('error');
        }else{
            // set percentage in progress bar
            percentage = parseFloat(parseFloat(percentage) + parseFloat(incValue)).toFixed(2);
            $('#progress_bar_main').html(percentage+'%');
            $('#progress_bar_main').css({width: percentage+'%'});
            $('#progress_bar_main').attr('aria-valuenow', percentage+'%');
            if(percentage < 70 ){
                incValue = 1;
                // processbar(percentage);
            }else if(percentage < 80){
                incValue = 0.8;
            }else if(percentage < 90){
                incValue = 0.5;
            }else if(percentage < 98){
                incValue = 0.1;
            }else{
                percentage = 'error';
            }
            setTimeout(() => {
                processbar(percentage);
            }, 1000);
        }

    }

   
     

    $('#unit_id').on("change", function(){ 
        $.ajax({
            url : "{{ url('hr/reports/floor_by_unit') }}",
            type: 'get',
            data: {unit : $(this).val()},
            success: function(data)
            {
                $("#floor_id").html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });
});
// excel conversion -->

$(function(){
    $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html()) 
        location.href=url
        return false
    })
})
// Radio button location

function attLocation(loc){
    window.location = loc;
   }

   $('.search_button').on('click', function(){
        //document.getElementById('bonus_content_section').style.visibility="hidden";
   });



</script>

@endsection
