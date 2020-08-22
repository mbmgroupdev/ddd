@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
    <style>
        .form-group {overflow: hidden;}
        table.header-fixed1 tbody {max-height: 500px; overflow-y: scroll;}
        .fc-event-container a{margin-top: 10px; padding: 10px;}
        .select2-container{
            margin-bottom: 9px;
        }
        .select2 {width:100% !important;}
        .itemdiv.dialogdiv>.own-msg{
            border: 1px solid #e6eef9;
            background: #e6eef9;
        }
        .itemdiv.dialogdiv>.own-msg:before {
            border: 2px solid #e6eef9;
            background-color: #e6eef9;
        }
        .arrow-right:after {
            content: " ";
            position: absolute;
            right: -5px;
            border-top: 10px solid transparent;
            border-right: none;
            border-left: 5px solid #3A87AD;
            border-bottom: 10px solid transparent;
            top: 0;
        }

        a.main-box{
            width:126px;
            margin: 0.5%;
            display: inline-block;
            float: left;
        }
        .icon-img {
            height: 40px;
            width: 40px;
            margin-top:10% ;
        }


        .widget-box {
            border-radius: 5px;
        }
        .widget-body { background-color: transparent;}

        @media only screen and (max-width: 585px) {
            .send-body{float: left; width: 50%;}
            .message-body{width: 100%;}
        }

        .fc-day.selected {
           background: #e0604a;
        }
        .fc-day-number.selected {
           background: #e0604a;
        }
        /* .sidebar{display: none;}
        .sidebar+.main-content { margin-left: 0px;} */
        .fc-today.selected {
            background: #e0604a !important;
        }

        h4.smaller { font-size: 14px;}
        .form-group { margin-bottom: 0px;}

    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.min.css') }}">

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
                    <a href="#"> Time & Attendance </a>
                </li>
                <li class="active"> Monthly Holiday Roster Assign </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="row">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Monthly Holiday Roster Assign <a href="{{ url('hr/timeattendance/shift_roaster')}}" target="_blank" class="btn btn-success btn-xx pull-right"> <i class="fa fa-eye"></i> Shift Roster</a> &nbsp; <a href="{{ url('/hr/shift_roaster/roaster_view')}}" target="_blank" class="btn btn-info btn-xx pull-right"> <i class="fa fa-eye"></i> View Monthly Holiday Roster</a></h6>
                    </div>
                    <div class="panel-body">
                        @include('inc/message')
                        <form role="form" method="POST" action="{{ url('hr/shift_roaster/save_roaster') }}" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="col-sm-12 no-padding">
                                <div class="col-sm-6 no-padding-left">
                                    <div class="panel panel-warning">
                                        <div class="panel-body">
                                            <div class="col-sm-6 no-padding-left" style="">
                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="unit">Unit </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit','id'=>'unit_shift', 'class'=> 'form-control filter', 'data-validation'=> 'required']) }}
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="otnonot">OT/Non-OT </label>
                                                    <div class="col-sm-12">
                                                        <select name="otnonot" id="otnonot" class="form-control filter">
                                                            <option value="">Select OT/Non-OT</option>
                                                            <option value="0">Non-OT</option>
                                                            <option value="1">OT</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="area">Area </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area','id'=>'area_shift', 'class'=> 'form-control filter', 'disabled']) }}
                                                        {{-- <select name="area" id="area_shift" class= "form-control filter" disabled ><option value="">Select Area</option></select> --}}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="section">Section </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::select('section', $sectionList, null, ['placeholder'=>'Select Section','id'=>'section_shift', 'class'=> 'form-control filter','disabled']) }}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                  <label class="col-sm-12 control-label no-padding-right" for="searchtype">Day Type </label>
                                                  <div class="col-sm-12" style="">
                                                    <?php
                                                      $types=['Holiday'=>'Holiday','General'=>'General','OT'=>'OT']
                                                    ?>
                                                      {{ Form::select('searchtype', $types, null, ['placeholder'=>'Select Type', 'class'=> 'form-control filter', 'id' => 'dayType']) }}
                                                  </div>
                                                </div>

                                                <div class="form-group">
                                                  <label class="col-sm-12 control-label no-padding-right" for="searchstatus">Employee Status </label>
                                                  <div class="col-sm-12" style="">
                                                    <?php
                                                      $status=['0'=>'Shift','1'=>'Roster']
                                                    ?>
                                                      {{ Form::select('status', $status, null, ['placeholder'=>'Select Status', 'class'=> 'form-control filter']) }}
                                                  </div>
                                                </div>


                                            </div>
                                            <div class="col-sm-6 no-padding">
                                                <div class="form-group">
                                                  <label class="col-sm-12 control-label no-padding-right" for="emp_type">Employee Type </label>
                                                  <div class="col-sm-12" style="">
                                                      {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control filter']) }}
                                                  </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="department">Department </label>
                                                    <div class="col-sm-12">

                                                        <select name="department" id="department_shift" class= "form-control filter" disabled ><option value="">Select Department</option></select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="subsection">Sub Section </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::select('subsection', $subsectionList, null, ['placeholder'=>'Select Sub Section','id'=>'subsection_shift', 'class'=> 'form-control filter', 'disabled']) }}

                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                  <label class="col-sm-12 control-label no-padding-right" for="dates"> Dates </label>
                                                  <div class="col-sm-12">
                                                    <input name="dates" type="text" id="dates" placeholder="Y-m-d" autocomplete="off" class="col-xs-12 multidatepicker form-control filter" data-validation-format="yyyy-mm-dd" style="height: 30px; font-size: 12px;" disabled/>
                                                  </div>
                                                </div>

                                                <div class="form-group">
                                                  <label class="col-sm-12 control-label no-padding-right" for="doj">Date of Join </label>
                                                  <div class="col-sm-6">
                                                    <input name="doj" type="text" id="doj" placeholder="Y-m-d" autocomplete="off" class="col-xs-12 singledatepicker form-control filter" data-validation-format="yyyy-mm-dd" />
                                                  </div>
                                                  <div class="col-sm-6 ot">
                                                    <select id="condition" style="width:100%" class="form-control filter" data-validation-error-msg="This field is required" name="condition">
                                                      <option selected="selected" value="">Select Condition</option>
                                                      <option value="Equal">Equal</option>
                                                      <option value="Less Than">Less Than</option>
                                                      <option value="Greater Than">Greater Than</option>
                                                    </select>
                                                  </div>
                                                </div>

                                                 <input type="hidden" id="multi_select_dates" name="multi_select_dates" value="">
                                                 <input type="hidden" id="single_select_dates" name="single_select_dates" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- tables show short employee -->
                                <div class="col-sm-6 no-padding">
                                    <div class="panel panel-warning">
                                        <div class="panel-body">
                                            <div style="height: 325px; overflow: auto;">
                                                <div class="row">
                                                    <div class="col-sm-6 text-center" style="background-color: lightcoral; color: #fff;">
                                                        <p style="padding-top: 8px;">Selected Employee: <span id="selectEmp" style="font-weight: bold;"></span></p>
                                                    </div>
                                                    <div class="col-sm-6 text-center" style="background-color: #87B87F; color: #fff;">
                                                        <p style="padding-top: 8px;">Total Employee: <span id="totalEmp" style="font-weight: bold;"></span></p>
                                                    </div>
                                                </div>
                                                <table id="AssociateTable" class="table header-fixed1 table-compact table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style='position: sticky; top: -1px;'><input type="checkbox" id="checkAll"/></th>
                                                            <th style='position: sticky; top: -1px;'>Image</th>
                                                            <th style='position: sticky; top: -1px;'>Associate ID</th>
                                                            <th style='position: sticky; top: -1px;'>Name</th>
                                                            <th style='position: sticky; top: -1px;'>Current Shift - ref.</th>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="5" id="user_filter" style='position: sticky; top: -1px;'></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="user_info">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 no-padding">
                                <div class="col-sm-6 no-padding-left">
                                    <div class="panel panel-warning">
                                        <div class="panel-heading"> <h6>Assign Date  <a class="btn btn-xx btn-success pull-right" id="substitute">Substitute</a></div>
                                        <div class="panel-body">
                                            <div class="form-group col-sm-6">
                                                <label class="col-sm-12 no-padding control-label no-padding-right" for="type">Type* </label>
                                                <div class="col-sm-12 no-padding" style="">
                                                  <?php
                                                    $types=['Holiday'=>'Holiday','General'=>'General','OT'=>'OT']
                                                  ?>
                                                    {{ Form::select('type', $types, null, ['placeholder'=>'Select Type', 'class'=> 'form-control', 'data-validation'=> 'required']) }}
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="col-sm-12 no-padding control-label no-padding-right" for="comment">Comment </label>
                                                <div class="col-sm-12 no-padding" style="">
                                                  <input type="text" name="comment" class="form-control" value="" placeholder="Comment">
                                                </div>
                                            </div>
                                            {{-- assign date calender  --}}
                                            <div class="col-sm-12">
                                                <div class="widget-box widget-color-blue3">
                                                    <div class="widget-header">
                                                        <h4 class="widget-title smaller">
                                                          Select Dates From Calendar *
                                                        </h4>
                                                        <div class="widget-toolbar">
                                                            <a href="#" data-action="collapse">
                                                                <i class="ace-icon fa fa-chevron-down"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="widget-body">
                                                        <div class="widget-main padding-16">
                                                            <div id="event-calendar"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="panel panel-warning" id="calendarSection" style="display: none;">
                                        <div class="panel-heading"> <h6>Assign Date  <a id="substitute-remove" class="btn btn-xx btn-danger pull-right">Remove</a></div>
                                        <div class="panel-body">
                                            <div class="form-group col-sm-6">
                                                <label class="col-sm-12 no-padding control-label no-padding-right" for="type">Type* </label>
                                                <div class="col-sm-12 no-padding" style="">
                                                  <?php
                                                    $types=['Holiday'=>'Holiday','General'=>'General','OT'=>'OT']
                                                  ?>
                                                    {{ Form::select('subtype', $types, null, ['placeholder'=>'Select Type', 'class'=> 'form-control', 'data-validation'=> 'required']) }}
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="col-sm-12 no-padding control-label no-padding-right" for="comment">Comment </label>
                                                <div class="col-sm-12 no-padding" style="">
                                                  <input type="text" name="subcomment" class="form-control" value="Substitute" placeholder="Comment" disabled>

                                                </div>
                                            </div>
                                            <input type="hidden" name="subDates[]" id="subDates" value="">
                                            {{-- assign date calender  --}}
                                            <div class="col-sm-12">
                                                <div class="widget-box widget-color-blue3">
                                                    <div class="widget-header">
                                                        <h4 class="widget-title smaller">
                                                          Select Dates From Calendar *
                                                        </h4>
                                                        <div class="widget-toolbar">
                                                            <a href="#" data-action="collapse">
                                                                <i class="ace-icon fa fa-chevron-down"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="widget-body">
                                                        <div class="widget-main padding-16">
                                                            <div id='calendar'></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                {{-- assign btn --}}
                                <div class="col-sm-offset-4 col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" id="formSubmit" class="btn btn-primary btn-sm" style="width: auto;margin-left: 8px;margin-top: 10px;">
                                                Assign
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>

<script type="text/javascript">
var multiselect = [];
var singleselect = [];
$(document).ready(function(){
  // $('#date').datepicker({
  //
  //         multidate: true
  //
  //     });
  // .on('changeDate', function(e) {
  //     // `e` here contains the extra attributes
  //     $(this).find('.input-group-addon .count').text(' ' + e.dates.length);
  // });
    $('#dataTables2').DataTable({
        pagingType: "full_numbers" ,
        // searching: false,
        // "lengthChange": false,
        // 'sDom': 't'
        "sDom": '<"F"tp>'
    });
    //Filter User
    $("body").on("keyup", "#AssociateSearch", function() {
        var value = $(this).val().toLowerCase();
        $('#AssociateTable tr input:checkbox').prop('checked', false);
        $('#AssociateTable tr').removeAttr('class');
        $("#AssociateTable #user_info tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            if($(this).text().toLowerCase().indexOf(value) > -1) {
                $(this).attr('class','add');
                var numberOfChecked = $('#AssociateTable tr.add input:checkbox:checked').length;
                $('#selectEmp').html(numberOfChecked);
            }
        });
    });

    var userInfo = $("#user_info");
    var userFilter = $("#user_filter");
    var emp_type = $("select[name=emp_type]");
    var unit     = $("select[name=unit]");
    var otnonot     = $("select[name=otnonot]");
    var shift    = $("select[name=shift_id]");
    var section  = $("select[name=section]");
    var subsection = $("select[name=subsection]");
    var area = $("select[name=area]");
    var department = $("select[name=department]");
    var type = $("select[name=searchtype]");
    var doj = $("#doj");
    var condition = $("select[name=condition]");
    $(".filter").on('change', function(){
        // console.log('Searching...');
        userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th>');

        $.ajax({
            url: '{{ url("hr/timeattendance/get_associate_by_type_unit_shift_roster_ajax") }}',
            data: {
                emp_type: emp_type.val(),
                otnonot: otnonot.val(),
                unit    : unit.val(),
                shift   : shift.val(),
                section : section.val(),
                subsection : subsection.val(),
                area : area.val(),
                department : department.val(),
                dates:$('#dates').val(),
                type:type.val(),
                status:$("select[name=status]").val(),
                doj:doj.val(),
                condition: condition.val()

            },
            success: function(data)
            {
                //console.log(data);
                userFilter.html(data.filter);
                if(data.result == ""){
                    userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color:red;\">No Data Found</th>');
                }
                else{
                  //console.log(data.total);
                    userInfo.html(data.result);
                    $('#totalEmp').html(data.total);
                    var numberOfChecked = $('#AssociateTable tr.add input:checkbox:checked').length;
                    $('#selectEmp').html(numberOfChecked);
                }
            },
            error:function(xhr)
            {
                console.log('Employee Type Failed');
            }
        });
    });


    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: '{!! url("hr/timeattendance/all_leaves_data") !!}',
        dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp",
        buttons: [
            {
                extend: 'copy',
                className: 'btn-sm btn-info',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                className: 'btn-sm btn-success',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                className: 'btn-sm btn-warning',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                className: 'btn-sm btn-primary',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                className: 'btn-sm btn-default',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        columns: [
        ],
    });


    var today = new Date();
    var start = today.getDate();
    var end = today.getDate();
    $('#end_day').change(function(e){
        var start= $('#start_day').val();
        var end= $('#end_day').val();
        start= parseInt(start);
        end= parseInt(end);
        if(end<start){
            alert("Shift can not end before start");
            $('#end_day').prop('selectedIndex', start);
            e.preventDefault();
        }
    });

    $('#checkAll').click(function(){
        var checked =$(this).prop('checked');
        $('#AssociateTable tr.add input:checkbox').prop('checked', checked);
        var numberOfChecked = $('#AssociateTable tr.add input:checkbox:checked').length;
        $('#selectEmp').html(numberOfChecked);
        // var numberOfChecked = $('#AssociateTable tr.add input:checkbox:checked').length;
        // var totalCheckboxes = $('#AssociateTable tr.add input:checkbox').length;
        // var numberNotChecked = totalCheckboxes - numberOfChecked;
        // console.log(numberNotChecked, numberOfChecked, totalCheckboxes);
    });

    $('body').on('click', 'input:checkbox', function() {
        if(!this.checked) {
            $('#checkAll').prop('checked', false);
        }
        else {
            var numChecked = $('input:checkbox:checked:not(#checkAll)').length;
            var numTotal = $('input:checkbox:not(#checkAll)').length;
            if(numTotal == numChecked) {
                $('#checkAll').prop('checked', true);
            }
        }
        var numberOfChecked = $('#AssociateTable tr.add input:checkbox:checked').length;
        $('#selectEmp').html(numberOfChecked);
    });

    $('#formSubmit').on("click", function(e){
        var checkedBoxes = [];
        var checkedIds   = [];
        $('input[type="checkbox"]:checked').each(function() {
            if(this.value != "on")
            {
                checkedBoxes.push($(this).val());
                checkedIds.push($(this).data('id'));
            }
        });

        //console.log(multiselect,singleselect);

        // $('#event-calendar').find('multi').each(function() {
        //    console.log($(this).data('date'));
        // });

    });

    /// Target & Current Shift

    var unit= $("#unit_shift");
    var current_shift = $("#current_shift");
    var target_shift = $("#shift");

    unit.on("change", function(){

        // Shift list
        $.ajax({
            url : "{{ url('hr/timeattendance/unitshift') }}",
            type: 'get',
            data: {unit_id : $(this).val()},
            success: function(data)
            {
                current_shift.html(data);
                target_shift.html(data);
            },
            error: function(data)
            {
                console.log(data);
            }
        });

    });

    /// Shift List On Unit Selection


    var row=$("#shifttablerow");

    unit.on("change", function(){

        // Shift Table
        $.ajax({
            url : "{{ url('hr/timeattendance/shifttable') }}",
            type: 'get',
            data: {unit_id : $(this).val()},
            success: function(data)
            {
                row.html(data);

            },
            error: function(data)
            {
                console.log(data);
            }
        });

    });
    ///
    $("#doja").bind("change", function(e) {

      var data =
      // '<div class="form-group">'+

      '<div class="col-sm-12 no-padding-right" >'+
      '<?php
      $conditions = ['Equal'=>'Equal','Less Than'=>'Less Than','Greater Than'=>'Greater Than'];
      ?>'+
      '{{ Form::select('condition', $conditions,null, ['placeholder'=>'Select Condition', 'id'=>'condition', 'style'=> 'width:100%', 'data-validation'=>'required',  'data-validation-error-msg'=>'This field is required']) }}'+
      // '</div>'+
      '</div>';
      // if($('#ot_hour').val() > 0 && $('#type').val() != 'Absent'){
      //   $('.ot').empty();
      //   $('.ot').append(data);
      // }else{
      //   $('.ot').empty();
      // }
      $('.ot').empty();
      $('.ot').append(data);
    ///unit on change section enable
});
    unit.on("change", function(){

        $( "#area_shift" ).prop( "disabled", false );
        // $( "#dates" ).prop( "disabled", false );

    });
    ///unit on change section enable

    $("#dayType").on("change", function(){
        // console.log($(this).val());
        if($(this).val() !== ''){
            $( "#dates" ).prop( "disabled", false );
        }else{
           $( "#dates" ).prop( "disabled", true );
        }

    });

    /// area on change department select

    $('#area_shift').on('change', function(){

        $( "#department_shift" ).prop( "disabled", false );

        // console.log($(this).val());
        $.ajax({
            url : "{{ url('hr/timeattendance/areaDepartment') }}",
            type: 'get',
            data: {area_id : $(this).val()},
            success: function(data)
            {
                $("#department_shift").html(data);


            },
            error: function(data)
            {
                console.log(data);
            }
        });

    });

    /// department on change section select

    $('#department_shift').on('change', function(){

        $( "#section_shift" ).prop( "disabled", false );

        // console.log($(this).val());
        $.ajax({
            url : "{{ url('hr/timeattendance/departmentSection') }}",
            type: 'get',
            data: {department_id : $(this).val()},
            success: function(data)
            {
                $("#section_shift").html(data);


            },
            error: function(data)
            {
                console.log(data);
            }
        });

    });
    //section on change subsection enable

    $("#section_shift").on("change", function(){

        $( "#subsection_shift" ).prop( "disabled", false );

        $.ajax({
            url : "{{ url('hr/timeattendance/sectionSubsection') }}",
            type: 'get',
            data: {section_id : $(this).val()},
            success: function(data)
            {
                $("#subsection_shift").html(data);


            },
            error: function(data)
            {
                console.log(data);
            }
        });

    });

    // $('#event-calendar').fullCalendar();
    $('#event-calendar').fullCalendar();
    $('#event-calendar').on('click', '.fc-day-header', function(e) {

        let day = $(this).text().toLowerCase();
         //console.log($('.fc-day').filter('.fc-' + day));
        multiselect = [];
        singleselect=[];
        $('.fc-day').filter('.fc-' + day).each(function() {
             // if(this.value != "on")
             // {
             //     checkedBoxes.push($(this).val());
             //     checkedIds.push($(this).data('id'));
             // }
             var clss = $(this).attr('class').split(' ');
             if(!clss.includes("fc-other-month")){
               //console.log($(this).data('date'));
               multiselect.push($(this).data('date'));
             }

         });
         // console.log(multiselect);
         $('#single_select_dates').val(singleselect);
         $('#multi_select_dates').val(multiselect);
         $('.fc-day').removeClass('selected')
                     .removeClass('single')
                     .filter('.fc-' + day)
                     .addClass('selected')
                     .addClass('multi');
         $('.fc-other-month').removeClass('selected')
                     .removeClass('multi');

    });

    $('#event-calendar').on('click', '.fc-day', function(e) {
      let day = $(this).text().toLowerCase();
      var clss = $(this).attr('class').split(' ');

       //console.log(singleselect);
      if(clss.includes("selected") && clss.includes("single")){
        singleselect.pop($(this).data('date'))
        $(this).removeClass('selected')
                    .removeClass('single');
      }else{

        $('.multi').removeClass('selected').removeClass('multi');
        $('.fc-other-month').removeClass('selected')
                    .removeClass('single');
        multiselect = [];
        if(!clss.includes("fc-other-month")){
            $(this).addClass('selected');
            $(this).addClass('single');
          console.log($(this).data('date'));
          singleselect.push($(this).data('date'));
        }
        console.log(singleselect);
        $('#multi_select_dates').val(multiselect);
        $('#single_select_dates').val(singleselect);
      }

    });
    $('#event-calendar').on('click', '.fc-day-number', function(e) {

        let day = $(this).data('date');
        var clss = $(this).attr('class').split(' ');
         //console.log(day);
        var currentEl = $(this).parent().parent().parent().parent().parent().find('.fc-bg').find("tr").find("[data-date='" + day + "']");

        multiselect = [];


        if(clss.includes("selected") && clss.includes("single")){
           currentEl.removeClass('selected')
                       .removeClass('single');
        }else{
            if(!clss.includes("fc-other-month")){
                currentEl.addClass('selected');
                currentEl.addClass('single');
                console.log(currentEl.data('date'));
                singleselect.push(currentEl.data('date'));
                $('#multi_select_dates').val(multiselect);
                $('#single_select_dates').val(singleselect);
            }
           // console.log(singleselect);

           $('.multi').removeClass('selected').removeClass('multi');
           $('.fc-other-month').removeClass('selected')
                       .removeClass('single');
        }


    });

});

$('body').on("click",'#substitute', function(){
    $("#calendarSection").show();
    var i = 0;
    var subArrayDate = new Array();
    $('#calendar').fullCalendar({
        // defaultDate: '2014-11-10',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        events: [{
            start: '2014-11-12T13:00:00',
            end: '2014-11-12T16:00:00',
        }, ],
        selectable: true,
        select: function (start, end, jsEvent, view) {
            var selectDate = jsEvent.target.dataset.date;
            if(selectDate !== undefined){
                subArrayDate.push(selectDate);
                $("#subDates").val(subArrayDate);
                console.log(selectDate);
                // console.log(subArrayDate);
                $("#calendar").fullCalendar('addEventSource', [{
                    id: i,
                    selectDate: selectDate,
                    start: start,
                    end: end,
                    rendering: 'background',
                    block: true,
                }, ]);
                i++;
            }

            $("#calendar").fullCalendar("unselect");
        },
        selectOverlap: function(event) {
            var selectDate = event.selectDate;
            subArrayDate.pop(selectDate);
            // console.log(subArrayDate);
            $('#calendar').fullCalendar('removeEvents', event.id);
            $("#subDates").val(subArrayDate);

        }
    });
});

$(document).ready(function() {
    $(".fc-prev-button").click(function(event) {
        return false;
    });

    $("#substitute-remove").click(function(event) {
        $("#calendarSection").hide();
        $("#subDates").val(null);
        $("#subtype").val(null);
        $("#subcomment").val(null);
    });
});

</script>
@endpush
@endsection
