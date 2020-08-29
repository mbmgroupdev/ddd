@extends('hr.layout')
@section('title', 'Holiday Roster')
@section('main-content')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.min.css') }}">
    <style>
        table.header-fixed1 tbody {max-height: 500px; overflow-y: scroll;}
        input[type=checkbox] {
            transform: scale(1.5);
        }
        .form-group { margin-bottom: 25px;}
        .fc-widget-content{
            height: auto !important;
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
                    <a href="#"> Operation </a>
                </li>
                <li class="active"> Holiday Roster Assign </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h6>Holiday Roster Assign <a href="{{ url('hr/timeattendance/shift_roaster')}}" target="_blank" class="btn btn-warning btn-sm pull-right"> <i class="fa fa-eye"></i> Shift Roster</a> &nbsp; <a href="{{ url('/hr/shift_roaster/roaster_view')}}" target="_blank" class="btn btn-primary btn-sm pull-right"> <i class="fa fa-eye"></i> View Holiday Roster</a></h6>
            </div>
            <div class="panel-body">
                @include('inc/message')
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-sm-3"> 
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit','id'=>'unit_shift', 'class'=> 'form-control', 'required'=> 'required']) }} 
                                    <label  for="unit">Unit </label>
                                </div>
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area','id'=>'area_shift', 'class'=> 'form-control ', 'disabled']) }} 
                                    <label  for="area">Area </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    <select name="department" id="department_shift" class= "form-control" disabled ><option value="">Select Department</option></select>
                                    <label  for="department">Department </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control']) }} 
                                    <label  for="emp_type">Employee Type </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('section', [], null, ['placeholder'=>'Select Section','id'=>'section_shift', 'class'=> 'form-control','disabled']) }} 
                                    <label  for="section">Section </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('subsection', [], null, ['placeholder'=>'Select Sub Section','id'=>'subsection_shift', 'class'=> 'form-control', 'disabled']) }}
                                    <label  for="subsection">Sub Section </label>
                                </div>
                            </div>
                            <div class="col-sm-3"> 
                                <div class="form-group has-float-label select-search-group">
                                    <select name="otnonot" id="otnonot" class="form-control">
                                        <option value="">Select OT/Non-OT</option>
                                        <option value="0">Non-OT</option>
                                        <option value="1">OT</option>
                                    </select>
                                    <label  for="otnonot">OT/Non-OT </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    <select class="form-control" id="emp_status" name="emp_status">
                                        <option value="">Select Status</option>
                                        <option value="0" >Shift</option>
                                        <option value="1">Roster</option>
                                    </select> 
                                    <label  for="emp_status" style="color: maroon;">Employee Status </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    <select class="form-control" id="dayType" name="searchtype">
                                        <option value="">Select Type</option>
                                        <option value="Holiday" >Holiday</option>
                                        <option value="General" >General</option>
                                        <option value="OT" >OT</option>
                                    </select> 
                                    <label  for="dayType" style="color: maroon;">Day Type </label>
                                </div>

                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-float-label has-required">
                                    <input type="date" class="report_date datepicker form-control" id="dates" name="dates" placeholder="Y-m-d" multiple autocomplete="off" />
                                    <label for="dates">Dates</label>
                                </div>
                                <div class="row">
                                    <div class="col pr-0">
                                        <div class="form-group has-float-label">
                                            <input type="date" class="report_date datepicker form-control" id="doj" name="doj" placeholder="Y-m-d" value="" autocomplete="off" />
                                            <label for="doj">Date of Join</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group has-float-label select-search-group"> 
                                            <select id="condition" class="form-control" name="condition">
                                                <option value="">Select Condition</option>
                                                <option value="Equal">Equal</option>
                                                <option value="Less Than">Less Than</option>
                                                <option value="Greater Than">Greater Than</option>
                                            </select>
                                            <label for="condition" style="color: maroon;">Condition </label>
                                        </div>
                                    </div>
                                  </div>
                                <div class="form-group">
                                    <button class="btn btn-outline-success nextBtn btn-lg pull-right filter" type="button" ><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-info">
            <div class="panel-body">
                <form role="form" class="row">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="col-sm-6">
                        <div class="panel panel-warning">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-6 text-center" style="background-color: lightcoral; color: #fff;">
                                        <p style="padding-top: 8px;">Selected Employee: <span id="selectEmp" style="font-weight: bold;"></span></p>
                                    </div>
                                    <div class="col-sm-6 text-center" style="background-color: #87B87F; color: #fff;">
                                        <p style="padding-top: 8px;">Total Employee: <span id="totalEmp" style="font-weight: bold;"></span></p>
                                    </div>
                                </div>
                                <div style="height: 400px; overflow: auto;">
                                    <table id="AssociateTable" class="table header-fixed1 table-compact table-bordered">
                                        <thead>
                                            <tr>
                                                <th style='position: sticky; top: -1px;'><input type="checkbox" id="checkAll"/></th>
                                                <th class="sticky-th">Image</th>
                                                <th class="sticky-th">Associate ID</th>
                                                <th class="sticky-th">Oracle ID</th>
                                                <th class="sticky-th">Name</th>
                                                <th class="sticky-th">Current Shift - ref.</th>
                                            </tr>
                                            <tr>
                                                <th colspan="6" id="user_filter" style='position: sticky; top: -1px;'></th>
                                            </tr>
                                        </thead>
                                        <tbody id="user_info">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 pt-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group has-required has-float-label select-search-group">
                                    <select class="form-control" id="targetType" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="Holiday" >Holiday</option>
                                        <option value="General" >General</option>
                                        <option value="OT" >OT</option>
                                    </select> 
                                    <label  for="targetType" style="color: maroon;">Type </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group has-float-label">
                                    <input type="text" class="form-control" id="comment" name="comment" placeholder="Comment" value="" autocomplete="off" />
                                    <label for="comment">Comment</label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="widget-box widget-color-blue3">
                                    <div class="widget-header">
                                        <h4 class="widget-title smaller">
                                          Select Dates From Calendar *
                                        </h4>
                                        {{-- <div class="widget-toolbar">
                                            <a href="#" data-action="collapse">
                                                <i class="ace-icon fa fa-chevron-down"></i>
                                            </a>
                                        </div> --}}
                                    </div>
                                    <div class="widget-body">
                                        <div class="widget-main padding-16">
                                            <div id="event-calendar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="panel panel-warning" id="calendarSection" style="display: none;">
                            <div class="panel-heading"> <h6>Assign Date  <a id="substitute-remove" class="btn btn-xx btn-danger pull-right">Remove</a></div>
                            <div class="panel-body">
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-12 no-padding control-label no-padding-right" for="">Type* </label>
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
                                <input type="hidden" name="subDates" id="subDates" value="">
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
                        </div> --}}
                        <div class="form-group overflow-hidden"> 
                            <button type="button" id="formSubmit" class="btn btn-primary pull-left" >
                                <i class="fa fa-save"></i> Save
                            </button> 
                        </div>
                    </div>
                    
                </form>
            </div>   
        </div>
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>

<script type="text/javascript">
var multiselect = [];
var singleselect = [];
$(document).ready(function(){
    var totalempcount = 0;
    var totalemp = 0;
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
    $("body").on("keyup", "#AssociateSearch", function(e) {
        if(e.keyCode == 13){
            var value = $(this).val().toLowerCase();
            // $('#AssociateTable tr input:checkbox').prop('checked', false);
            $('#AssociateTable tr').removeAttr('class');
            $("#AssociateTable #user_info tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                if($(this).text().toLowerCase().indexOf(value) > -1) {
                    $(this).attr('class','add');
                    var numberOfChecked = $('#AssociateTable tr.add input:checkbox:checked').length;
                    var numberOfCheckBox = $('#AssociateTable tr.add input:checkbox').length;
                    if(numberOfChecked == numberOfCheckBox) {
                        $('#checkAll').prop('checked', true);
                    } else {
                        $('#checkAll').prop('checked', false);
                    }
                }
            });
        }
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
    var emp_status = $("select[name=emp_status]");
    var doj = $("#doj");
    var condition = $("select[name=condition]");
    $(".filter").on('click', function(){
        loadEmployeeSearchWise();
    });
    function loadEmployeeSearchWise(){
        userInfo.html('<th colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th>');

        $.ajax({
            url: '{{ url("hr/operation/holiday_roster_assign_employee") }}',
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
                shift_roster_status:emp_status.val(),
                status:$("select[name=status]").val(),
                doj:doj.val(),
                condition: condition.val()

            },
            success: function(data)
            {
                userFilter.html(data.filter);
                totalempcount = 0;
                totalemp = 0;
                if(data.result == ""){
                    $('#totalEmp').text('0');
                    $('#selectEmp').text('0');
                    userInfo.html('<th colspan="6" style=\"text-align: center; font-size: 14px; color:red;\">No Data Found</th>');
                }
                else{
                  //console.log(data.total);
                    userInfo.html(data.result);
                    totalemp = data.total;
                    $('#selectEmp').text(totalempcount);
                    $('#totalEmp').text(data.total);
                }
            },
            error:function(xhr)
            {
                console.log(xhr);
            }
        });
    }

    

    $('#checkAll').click(function(){
        var checked =$(this).prop('checked');
        var selectemp = 0;
        if(!checked) {
            selectemp = $('#AssociateTable tr.add input:checkbox:checked').length;
            selectemp = totalempcount - selectemp;
            totalempcount = 0;
        } else {
            selectemp = $('#AssociateTable tr.add input:checkbox:not(:checked)').length;
        }
        $('#AssociateTable tr.add input:checkbox').prop('checked', checked);
        totalempcount = totalempcount+selectemp;
        $('#selectEmp').text(totalempcount);
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
        if($(this).prop('checked')) {
            if(typeof $(this).attr('id') === "undefined"){
                totalempcount += 1;
            }
        } else {
            if(typeof $(this).attr('id') === "undefined"){
                totalempcount -= 1;
            }
        }
        $('#selectEmp').text(totalempcount);
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

    var row=$("#shifttablerow");
    ///
    $("#doja").bind("change", function(e) {

        var data =
        // '<div class="form-group">'+

        '<div class="col-sm-12 no-padding-right" >'+
        '<?php
        $conditions = ['Equal'=>'Equal','Less Than'=>'Less Than','Greater Than'=>'Greater Than'];
        ?>'+
        '{{ Form::select('condition', $conditions,null, ['placeholder'=>'Select Condition', 'id'=>'condition', 'style'=> 'width:100%', 'data-validation'=>'required',  'data-validation-error-msg'=>'This field is required']) }}'+
        '</div>'+
        
        $('.ot').empty();
        $('.ot').append(data);
        ///unit on change section enable
    });
    unit.on("change", function(){
        userInfo.html('<th colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Area</th>');
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
        userInfo.html('<th colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Department</th>');
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

});

$(document).ready(function() {
    var i = 0;
    var subArrayDate = new Array();
    $('#event-calendar').fullCalendar({
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
                $("#assignDates").val(subArrayDate);
                // console.log(selectDate);
                // console.log(subArrayDate);
                $("#event-calendar").fullCalendar('addEventSource', [{
                    id: i,
                    selectDate: selectDate,
                    start: start,
                    end: end,
                    rendering: 'background',
                    block: true,
                }, ]);
                i++;
            }

            $("#event-calendar").fullCalendar("unselect");
        },
        selectOverlap: function(event) {
            var selectDate = event.selectDate;
            // console.log(selectDate);
            subArrayDate.splice(subArrayDate.indexOf(selectDate), 1);
            // console.log(subArrayDate);
            $('#event-calendar').fullCalendar('removeEvents', event.id);
            $("#assignDates").val(subArrayDate);

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
                // console.log(selectDate);
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
            subArrayDate.splice(subArrayDate.indexOf(selectDate), 1);
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
