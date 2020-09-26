@extends('hr.layout')
@section('title', 'Shift Assign')
@section('main-content')
@push('css')
    <style>
       label.col-sm-12.control-label.no-padding-right {
            padding-top: 15px;
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
                    <a href="#"> Time & Attendance </a>
                </li>
                <li class="active"> Shift Assign </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Shift Assign (Monthly) <a href="{{ url('/hr/timeattendance/shift_roaster')}}" target="_blank" class="btn btn-info btn-xx pull-right"> <i class="fa fa-eye"></i> View Shift Roster</a></h6>
                    </div>
                    <div class="panel-body">
                        @include('inc/message')
                        <form role="form" method="POST" action="{{ url('hr/timeattendance/shift_assign') }}" enctype="multipart/form-data"> 
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="col-sm-12 no-padding">
                                <div class="col-sm-6 no-padding-left">
                                    <div class="panel panel-warning">
                                        <div class="panel-body">
                                            <div class="col-sm-6 no-padding-left" style="">
                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="month"> Month </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::selectMonth('month', date("n"), ['placeholder'=>'Select Month', 'id'=>'month', 'class'=>'col-xs-12', 'data-validation'=> 'required']) }}
                                                    </div>
                                                </div>

                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="unit">Unit </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit','id'=>'unit_shift', 'class'=> 'form-control ', 'data-validation'=> 'required']) }} 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="area">Area </label>
                                                    <div class="col-sm-12"> 
                                                        {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area','id'=>'area_shift', 'class'=> 'form-control', 'disabled']) }} 
                                                        {{-- <select name="area" id="area_shift" class= "form-control filter" disabled ><option value="">Select Area</option></select> --}}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="department">Department </label>
                                                    <div class="col-sm-12">
                                                        {{-- {{ Form::select('department', $departmentList, null, ['placeholder'=>'Select Department','id'=>'department_shift', 'class'=> 'form-control filter','disabled']) }}  --}}
                                                        <select name="department" id="department_shift" class= "form-control filter" disabled ><option value="">Select Department</option></select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="section">Section </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::select('section', [], null, ['placeholder'=>'Select Section','id'=>'section_shift', 'class'=> 'form-control filter','disabled']) }} 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="subsection">Sub Section </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::select('subsection', [], null, ['placeholder'=>'Select Sub Section','id'=>'subsection_shift', 'class'=> 'form-control filter', 'disabled']) }} 
                                                        {{-- <select name="subsection_id" id="subsection_shift" class= "form-control filter" disabled ><option value="">Select Section First</option></select> --}}
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
                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="search_date"> Search Date </label>
                                                    <div class="col-sm-12"> 
                                                        <input type="text" class="form-control datepicker filter" name="search_date" id="search_date" value="{{date('Y-m-d')}}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 no-padding">
                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="year"> Year </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::selectRange('year', 2018, 2030, date("Y"), ['placeholder'=>'Select Year', 'id'=>'year', 'style'=>'width:100%;', 'data-validation'=> 'required']) }}  
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="emp_type">Employee Type </label>
                                                    <div class="col-sm-12" style="">
                                                        {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control filter']) }} 
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
                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="shift_id">Current Shift </label>
                                                    <div class="col-sm-12">
                                                       <!--  {{ Form::select('shift_id', $shiftList, null, ['placeholder'=>'Select Shift','id'=>'current_shift', 'class'=> 'form-control filter']) }} -->
                                                       <select name="shift_id" id="current_shift" class= "form-control filter" ><option value="">Select Unit First</option></select>
                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="month"> Target Shift </label>
                                                    <div class="col-sm-12" >
                                                     <!--    {{ Form::select('shift', $shiftList, null, ['placeholder'=>'Select Target Shift', 'id'=>'shift', 'style'=>'width:100%;', 'data-validation'=> 'required']) }}  -->
                                                         <select name="shift" id="shift" class= "form-control" data-validation='required' ><option value="">Select Unit First</option></select>
                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="start_day"> Start Date </label>
                                                    <div class="col-sm-12"> 
                                                        {{ Form::selectRange('start_day', 1, 31, date("j"), ['placeholder'=>'Select Start Day', 'id'=>'start_day', 'style'=>'width:100%;', 'data-validation'=> 'required']) }}  
                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="end_day"> End Date </label>
                                                    <div class="col-sm-12"> 
                                                        {{ Form::selectRange('end_day', 1, 31, date("j"), ['placeholder'=>'Select End Day', 'id'=>'end_day', 'style'=>'width:100%;', 'data-validation'=> 'required']) }}  
                                                    </div>
                                                </div>

                                                
                                            </div>

                                            <div class="col-sm-offset-4 col-sm-4">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                    <button type="submit" id="formSubmit" class="btn btn-primary btn-sm" style="margin-left: 8px;margin-top: 10px;">
                                                        Assign Shift
                                                    </button> 
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-sm-12 no-padding">
                                        <div class="panel panel-warning">
                                            <div class="panel-heading"><h6>Shift List (<span id="shiftListCurrentDate">{{date('Y-m-d')}}</span>)</h6></div>
                                            <div class="panel-body">
                                                <table id="" class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Shift Name</th>
                                                            <th>Start Time</th>
                                                            <th>Break Time</th>
                                                            <th>Out Time</th>
                                                            <th>Default <br>(No. of Employee)</th>
                                                            <th>Changed <br>(No. of Employee)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="shifttablerow"></tbody>
                                                </table> 
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <!-- tables -->
                                <div class="col-sm-6 no-padding">
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
                                            <div style="height: 490px; overflow: auto;">
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
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){

    var totalempcount = 0;
    var totalemp = 0;
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
                // $('#selectEmp').text(numberOfChecked);
                // console.log(numberOfChecked,numberOfCheckBox);
            }
        });
    });
 
    var userInfo = $("#user_info");
    var userFilter = $("#user_filter");
    var emp_type = $("select[name=emp_type]");
    var unit     = $("select[name=unit]");
    var otnonot     = $("select[name=otnonot]");
    var searchDate     = $("#search_date");
    var shift    = $("select[name=shift_id]");
    var section  = $("select[name=section]"); 
    var subsection = $("select[name=subsection]");
    var area = $("select[name=area]");
    var department = $("select[name=department]");
    $(".filter").on('change', function(){
        if(unit.val()) {
            // console.log('Searching...');
            userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th>');
            $.ajax({
                url: '{{ url("hr/timeattendance/get_associate_by_type_unit_shift") }}',
                data: {
                    emp_type: emp_type.val(),
                    unit    : unit.val(),
                    otnonot    : otnonot.val(),
                    shift   : shift.val(),
                    searchDate   : searchDate.val(),
                    section : section.val(),
                    subsection : subsection.val(),
                    area : area.val(),
                    department : department.val(),
                    status:$("select[name=status]").val()
                },
                success: function(data)
                {
                    userFilter.html(data.filter);
                    totalempcount = 0;
                    totalemp = 0;
                    if(data.result == ""){
                        $('#totalEmp').text('0');
                        $('#selectEmp').text('0');
                        userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color:red;\">No Data Found</th>');
                    }
                    else{
                        userInfo.html(data.result);
                        totalemp = data.total;
                        $('#selectEmp').text(totalempcount);
                        $('#totalEmp').text(data.total);
                    }
                },
                error:function(xhr)
                {
                    console.log('Employee Type Failed');
                }
            });
        }
    });

    $("#search_date").on("dp.change", function (e) {
        var currentDate = $(this).val();
        $('#shiftListCurrentDate').html(currentDate);
        if(unit.val()) {
            // console.log('Searching...');
            userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th>');
            $.ajax({
                url: '{{ url("hr/timeattendance/get_associate_by_type_unit_shift") }}',
                data: {
                    emp_type: emp_type.val(),
                    unit    : unit.val(),
                    otnonot    : otnonot.val(),
                    shift   : shift.val(),
                    searchDate   : searchDate.val(),
                    section : section.val(),
                    subsection : subsection.val(),
                    area : area.val(),
                    department : department.val()
                },
                success: function(data)
                { 
                    userFilter.html(data.filter);
                    totalempcount = 0;
                    totalemp = 0;
                    if(data.result == ""){
                        $('#totalEmp').text('0');
                        $('#selectEmp').text('0');
                        userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color:red;\">No Data Found</th>');
                    }
                    else{
                        userInfo.html(data.result);
                        totalemp = data.total;
                        $('#selectEmp').text(totalempcount);
                        $('#totalEmp').text(data.total);
                    }
                },
                error:function(xhr)
                {
                    console.log('Employee Type Failed');
                }
            });

            // update shift list
            var row=$("#shifttablerow");
            row.html('<tr><td colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</td></tr>')
            // Shift Table
            $.ajax({
                url : "{{ url('hr/timeattendance/shifttable') }}",
                type: 'get',
                data: {
                    unit_id : $('select[name=unit]').val(),
                    searchDate   : searchDate.val()
                },
                success: function(data)
                {
                    row.html(data);
                },
                error: function(data)
                {
                    console.log(data);
                }
            });
        }
    });

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: '{!! url("hr/timeattendance/all_leaves_data") !!}',
        dom: "lBftrip", 
        buttons: [   
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
        // console.log($(this).attr('id'), $(this).prop('checked'),totalempcount);
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
        row.html('<tr><td colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</td></tr>')
        // Shift Table
        $.ajax({
            url : "{{ url('hr/timeattendance/shifttable') }}",
            type: 'get',
            data: {
                unit_id : $(this).val(),
                searchDate   : searchDate.val()
            },
            success: function(data)
            {
                // console.log(data);
                row.html(data);
            },
            error: function(data)
            {
                console.log(data);
            }
        });

    });

    ///unit on change section enable

    unit.on("change", function(){ 
        userInfo.html('<th colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Area</th>');
        $( "#area_shift" ).prop( "disabled", false );

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
 
    
});
</script>
@endsection