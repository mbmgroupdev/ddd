@extends('hr.layout')
@section('title', 'Shift Roster Assign')
@section('main-content')
@push('css')
    <style>
        table.header-fixed1 tbody {max-height: 500px; overflow-y: scroll;}
        input[type=checkbox] {
		    transform: scale(1.5);
		}
        .form-group { margin-bottom: 25px;}

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
                <li class="active">Shift Roster Assign </li>
            </ul><!-- /.breadcrumb --> 
            
        </div>

        
        <div class="panel panel-info">
            
            
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
                                    <select class="col-sm-12" id="current_status" name="current_status">
                                        <option value="">Select Status</option>
                                        <option value="0" >Shift</option>
                                        <option value="1">Roster</option>
                                    </select> 
                                    <label  for="current_status" style="color: maroon;">Employee Status </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    <select name="otnonot" id="otnonot" class="form-control">
                                        <option value="">Select OT/Non-OT</option>
                                        <option value="0">Non-OT</option>
                                        <option value="1">OT</option>
                                    </select>
                                    <label  for="otnonot">OT/Non-OT </label>
                                </div>
                                
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-float-label has-required">
                                    <input type="date" class="report_date datepicker form-control" id="search_date" name="search_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                    <label for="search_date">Search Date</label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    <select class="col-sm-12 " id="current_shift" name="shift_id"  disabled="disabled">
                                        <option value="">Select</option>
                                    </select> 

                                    <label  for="current_shift" style="color: maroon;">Current Shift </label>
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
                    <div class="col-6">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6 text-center" style="background-color: lightcoral; color: #fff;">
                                    <p style="padding-top: 8px;">Selected Employee: <span id="selectEmp" style="font-weight: bold;"></span></p>
                                </div>
                                <div class="col-sm-6 text-center" style="background-color: #87B87F; color: #fff;">
                                    <p style="padding-top: 8px;">Total Employee: <span id="totalEmp" style="font-weight: bold;"></span></p>
                                </div>
                            </div>
                            <div class="row" style="height: 400px; overflow: auto;">
                                <table id="AssociateTable" class="table header-fixed1 table-compact table-bordered col-sm-12">
                                    <thead>
                                       
                                        <tr>
                                            <th class="sticky-th" ><input type="checkbox" id="checkAll"/></th>
                                            <th class="sticky-th">Image</th>
                                            <th class="sticky-th">Associate ID</th>
                                            <th class="sticky-th">Name</th>
                                            <th class="sticky-th">Current Shift</th>
                                        </tr>
                                         <tr>
                                            <th class="sticky-th" colspan="5" id="user_filter" style="top: 40px;"></th>
                                        </tr>
                                        
                                    </thead>
                                    <tbody id="user_info">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 pt-3">
                        <div class="selection-section">
                            <div class="form-group has-required has-float-label select-search-group">
                                <select class="form-control" id="target_shift" name="target_shift" required="required" disabled="disabled">
                                    <option value="">Select</option>

                                </select>
                                <label  for="target_shift" style="color: maroon;">Target Shift </label>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::selectRange('start_day', 1, 31, date("j"), ['placeholder'=>'Select End Day', 'id'=>'start_day', 'class'=>'form-control', 'required'=> 'required']) }}
                                        <label for="start_day">Date From</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::selectRange('end_day', 1, 31, date("j"), ['placeholder'=>'Select End Day', 'id'=>'end_day', 'class'=>'form-control', 'required'=> 'required']) }}
                                        <label for="end_day">Date To</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group overflow-hidden"> 
                                <button type="button" id="formSubmit" class="btn btn-primary pull-left" >
                                    <i class="fa fa-save"></i> Save
                                </button> 
                            </div>
                        </div>
                        <hr>
                        <div class="change-section">
                            <div class="panel panel-warning">
                                <div class="panel-heading"><h6>Shift Wise Employee Status (<span id="shiftListCurrentDate">{{date('Y-m-d')}}</span>)</h6></div>
                                <div class="panel-body">
                                    <table id="" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Shift Name</th>
                                                <th>Start Time</th>
                                                <th>Break Time</th>
                                                <th>Out Time</th>
                                                <th>Default (No. Employee)</th>
                                                <th>Changed (No. Employee)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="shifttablerow">
                                            <tr>
                                                <td colspan="6" class="text-center">No Unit Select</td>
                                            </tr>
                                        </tbody>
                                    </table> 
                                </div>
                            </div>
                        </div>
                    </div>
                </form>  
            </div>
        </div>
    </div>
</div>
@push('js')
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
    $("body").on("keyup", "#AssociateSearch", function(e) {
        if(e.keyCode == 13){
            var value = $(this).val().toLowerCase(); 
            // $('#AssociateTable tr input:checkbox').prop('checked', false);
            $('#AssociateTable tr').removeAttr('class');
            $("#AssociateTable #user_info tr").filter(function() {
                // $("#user_filter").html();
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
        }
        
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
    $(".filter").on('click', function(){
        if(unit.val()) {
            loadEmployeeSearchWise();
        }
    });


    $("#search_date").on("change", function (e) {
        var currentDate = $(this).val();
        $('#shiftListCurrentDate').html(currentDate);
        if(unit.val()) {
            // console.log('Searching...');
            loadEmployeeSearchWise();
            // update shift list
            var row=$("#shifttablerow");
            shiftWiseEmployeeCount();
        }
    });
    function loadEmployeeSearchWise(){
        userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th>');
        $.ajax({
            url: '{{ url("hr/operation/shift_assign_date_wise_employee") }}',
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
                // console.log(data);
                if(data !== 'error'){
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
                }
            },
            error:function(xhr)
            {
                console.log(xhr);
            }
        });
    }

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
        $("#target_shift").prop( "disabled", false );
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
        var msg = '';
        var flag = 0;
        var shift_target = $("select[name=target_shift]").val();
        $('input[type="checkbox"]:checked').each(function() {
            if(this.value != "on")
            {
                checkedBoxes.push($(this).val());
                //checkedIds.push($(this).data('id'));
            }
        });
        if(checkedBoxes.length === 0){
            msg = "Please Select Employee At Least One";
            flag = 1;
        }
        if(shift_target == null || shift_target == ''){
            msg = "Please Select Target Shift";
            flag = 1;
        }

        if(flag === 0){
            $(".app-loader").show();
            $.ajax({
                type: "POST",
                url: '{{ url("/hr/operation/shift_roster_assign_action") }}',
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: {
                    associate: checkedBoxes,
                    target_shift: shift_target,
                    start_day: $("select[name=start_day]").val(),
                    end_day: $("select[name=end_day]").val()
                },
                success: function(response)
                {
                    if(response.type === 'success'){
                        $('input[type="checkbox"]:checked').each(function() {
                            $(this).prop("checked", false);
                        });
                        $("#selectEmp").text('0');
                        totalempcount = 0;
                        loadEmployeeSearchWise();
                        shiftWiseEmployeeCount();
                    }
                    
                    setTimeout(function(){
                        $(".app-loader").hide();
                    }, 2000);

                    $.notify(response.message, response.type);
                },
                error: function (reject) {
                    $(".app-loader").hide();
                    // console.log(reject)
                }
            });
        }else{
            $.notify(msg, 'error');
        }
    });

    /// Target & Current Shift

    var unit= $("#unit_shift");  
    var current_shift = $("#current_shift");
    var target_shift = $("#target_shift");

    unit.on("change", function(){ 

        // Shift list
        $.ajax({
            url : "{{ url('hr/timeattendance/unitshift') }}",
            type: 'get',
            data: {unit_id : $(this).val()},
            success: function(data)
            {

                current_shift.html(data).removeAttr('disabled');
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
        shiftWiseEmployeeCount();

    });
    function shiftWiseEmployeeCount(){
        row.html('<tr><td colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</td></tr>');
        $.ajax({
            url : "{{ url('hr/timeattendance/shifttable') }}",
            type: 'get',
            data: {
                unit_id : unit.val(),
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
    }

    ///unit on change section enable

    unit.on("change", function(){ 
        //userInfo.html('<th colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Area</th>');
        $( "#area_shift" ).prop( "disabled", false );

    });

    /// area on change department select

    $('#area_shift').on('change', function(){
        //userInfo.html('<th colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Department</th>');
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
$(document).on('keypress','#AssociateSearch',function(e){
    if (e.keyCode === 13 || e.which === 13) {
        e.preventDefault();
        return false;
    }
});


</script>
@endpush


@endsection