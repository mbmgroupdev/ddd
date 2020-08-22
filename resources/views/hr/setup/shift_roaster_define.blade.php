@extends('hr.layout')
@section('title', 'Employee Shift/Roster Define')
@section('main-content')
@push('css')
    <style>
        .form-group {overflow: hidden;}
        table.header-fixed1 tbody {max-height: 500px; overflow-y: scroll;}
        input[type=checkbox] {
		    transform: scale(1.5);
		}
        .form-group { margin-bottom: 18px;}
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
                <li class="active"> Employee Shift/Roster Define </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Employee Shift/Roster Define </h6>
                    </div>
                    
                    <div class="panel-body">
                        @include('inc/message')
                        <form role="form" method="POST" action="{{ url('hr/operation/shift_roaster_status_save') }}" enctype="multipart/form-data"> 
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="col-sm-12 no-padding">
                                <div class="col-sm-6 no-padding-left">
                                    <div class="panel panel-warning">
                                        <div class="panel-body">
                                            <div class="col-sm-6 no-padding-left" style="">
                                                <div class="form-group required">
                                                    <label class="col-sm-12 control-label no-padding-right" for="unit">Unit </label>
                                                    <div class="col-sm-12">
                                                        {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit','id'=>'unit_shift', 'class'=> 'form-control', 'data-validation'=> 'required']) }} 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="area">Area </label>
                                                    <div class="col-sm-12"> 
                                                        {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area','id'=>'area_shift', 'class'=> 'form-control', 'disabled']) }} 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="department">Department </label>
                                                    <div class="col-sm-12">
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
                                                    <label class="col-sm-12 control-label no-padding-right" for="current_status" style="color: maroon;">Current Status <span style="color: red;"> *</span></label>
                                                    <div class="col-sm-12">
                                                        <select class="col-sm-12  filter" id="current_status" name="current_status" >
                                                            <option value="0" selected="selected">Shift</option>
                                                            <option value="1">Roster</option>
                                                        </select> 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label no-padding-right" for="target_status" style="color: maroon;">Target Status <span style="color: red;"> *</span></label>
                                                    <div class="col-sm-12">
                                                        <select class="col-sm-12" id="target_status" name="target_status" required="required" onchange="targetStatus(this.value)">
                                                        	<option value="">Select One</option>
                                                        	<option value="0">Shift</option>
                                                        	<option value="1">Roster</option>
                                                        </select> 
                                                        <p id="msg" style="color: red"></p>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-sm-offset-4 col-sm-4">
                                                <div class="form-group"> 
                                                    <div class="col-sm-12">
                                                    <button type="submit" id="formSubmit" class="btn btn-primary btn-sm" style="width: 100%; margin-top: 10px; border-radius: 2px;">
                                                        Submit
                                                    </button> 
                                                    </div>
                                                </div>
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
                                            <div style="height: 400px; overflow: auto;">
                                                <table id="AssociateTable" class="table header-fixed1 table-compact table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style='position: sticky; top: -1px;'><input type="checkbox" id="checkAll"/></th>
                                                            <th style='position: sticky; top: -1px;'>Image</th>
                                                            <th style='position: sticky; top: -1px;'>Associate ID</th>
                                                            <th style='position: sticky; top: -1px;'>Name</th>
                                                            <th style='position: sticky; top: -1px;'>Current Status</th>
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
@push('js')
<script type="text/javascript">
function targetStatus(value){
    var msg = '';
    if(value === '1'){
        msg = 'Employee will not avail General Holiday';
    }else{
        msg = '';
    }
    $("#msg").html(msg);
}
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
            }
        });
    });
 
    var userInfo 	= $("#user_info");
    var userFilter 	= $("#user_filter");
    var emp_type 	= $("select[name=emp_type]");
    var unit     	= $("select[name=unit]");
    var otnonot     = $("select[name=otnonot]");
    var shift    	= $("select[name=shift_id]");
    var section  	= $("select[name=section]"); 
    var subsection 	= $("select[name=subsection]");
    var area 		= $("select[name=area]");
    var department 	= $("select[name=department]");
    var current_status = $("select[name=current_status]");

    $(".filter").on('change', function(){
        if(unit.val()) {
            // console.log('Searching...');
            userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th>');
            
            $.ajax({
                url: '{{ url("hr/operation/get_associate_by_type_unit_shift_roaster") }}',
                data: {
                    emp_type: emp_type.val(),
                    unit    : unit.val(),
                    otnonot    : otnonot.val(),
                    shift   : shift.val(),
                    section : section.val(),
                    subsection : subsection.val(),
                    area : area.val(),
                    department : department.val(),
                    current_status : current_status.val()
                },
                success: function(data)
                { 
                    // console.log(data);
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
    });


   var unit= $("#unit_shift");  

    ///unit on change section enable

    unit.on("change", function(){ 
        userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Area</th>');
        $( "#area_shift" ).prop( "disabled", false );

    });

    /// area on change department select

    $('#area_shift').on('change', function(){
        userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Department</th>');
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
@endpush
@endsection