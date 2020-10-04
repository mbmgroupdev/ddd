@extends('hr.layout')
@section('title', 'Shift Roster Define')
@section('main-content')
@push('css')
    <style>
        table.header-fixed1 tbody {max-height: 500px; overflow-y: scroll;}
        input[type=checkbox] {
            transform: scale(1.5);
        }
        .form-group { margin-bottom: 25px;}
        .sticky-th {
            padding-top: 0px !important;
            padding-bottom: 0px !important;
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
                <li class="active">Default Punch </li>
            </ul> 
        </div>

        
        @include('inc/message')
        <div class="panel panel-info"> 
            <div class="panel-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-3"> 
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit','id'=>'unit_shift', 'class'=> 'form-control', 'required'=> 'required']) }} 
                                    <label  for="unit">Unit </label>
                                </div>
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area','id'=>'area_shift', 'class'=> 'form-control filter', 'disabled']) }} 
                                    <label  for="area">Area </label>
                                </div>
                             </div>
                            <div class="col-3">
                                <div class="form-group has-float-label select-search-group">
                                    <select name="department" id="department_shift" class= "form-control filter" disabled ><option value="">Select Department</option></select>
                                    <label  for="department">Department </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('section', [], null, ['placeholder'=>'Select Section','id'=>'section_shift', 'class'=> 'form-control filter','disabled']) }} 
                                    <label  for="section">Section </label>
                                </div>
                             </div>
                            <div class="col-3"> 
                                
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('subsection', [], null, ['placeholder'=>'Select Sub Section','id'=>'subsection_shift', 'class'=> 'form-control filter', 'disabled']) }}
                                    <label  for="subsection">Sub Section </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control filter']) }} 
                                    <label  for="emp_type">Employee Type </label>
                                </div>
                             </div>
                            <div class="col-3"> 
                                <div class="form-group has-float-label select-search-group">
                                    <select name="otnonot" id="otnonot" class="form-control filter">
                                        <option value="">Select OT/Non-OT</option>
                                        <option value="0">Non-OT</option>
                                        <option value="1">OT</option>
                                    </select>
                                    <label  for="otnonot">OT/Non-OT </label>
                                </div>
                                    
                                <div class="form-group">
                                </div>
                             </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-body">
                <form role="form" method="POST" action="{{ url('hr/timeattendance/default-punch') }}" enctype="multipart/form-data" class="row needs-validation" novalidate> 
                    @csrf
                    <div class="col-6">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6" style="background-color: #CEEBEE;color: #3f3f3f;font-weight: bold;border-radius: 3px;">
                                    <p style="padding-top: 2px;">Selected Employee: <span id="selectEmp" style="font-weight: bold;"></span></p>
                                </div>
                                <div class="col-sm-6" style="background-color: #225f9f;color: #fff;border-radius: 3px;">
                                    <p style="padding-top: 2px;">Total Employee: <span id="totalEmp" style="font-weight: bold;"></span></p>
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
                                            <th class="sticky-th">Designation</th>
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
                    <div class="col-6 pt-5">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group has-required has-float-label has-required">
                                    <input type="date" id="date_from" name="date_from" value="" class="form-control" required>
                                    <label  for="date_from" >Date From </label>
                                </div>
                                
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group has-required has-float-label has-required">
                                    <input type="date" id="date_to" name="date_to" value="" class="form-control">
                                    <label  for="date_to" required>Date To </label>
                                </div>
                                
                            </div>
                        </div>
                        
                        <div class="form-group"> 
                            <button type="submit" id="formSubmit" class="btn btn-primary text-center pull-right w-80" >
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
<script type="text/javascript">

$(document).ready(function(){

    var totalempcount = 0;
    var totalemp = 0;

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
 
    var userInfo    = $("#user_info");
    var userFilter  = $("#user_filter");
    var emp_type    = $("select[name=emp_type]");
    var unit        = $("select[name=unit]");
    var otnonot     = $("select[name=otnonot]");
    var shift       = $("select[name=shift_id]");
    var section     = $("select[name=section]"); 
    var subsection  = $("select[name=subsection]");
    var area        = $("select[name=area]");
    var department  = $("select[name=department]");

    $(".filter").on('change', function(){
        if(unit.val()) {
            // console.log('Searching...');
            userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th>');
            
            $.ajax({
                url: '{{ url("hr/timeattendance/employee_by_fields") }}',
                data: {
                    emp_type: emp_type.val(),
                    unit    : unit.val(),
                    otnonot    : otnonot.val(),
                    shift   : shift.val(),
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
        $("#target_status").prop( "disabled", false );
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

    $(document).on('change', '#date_from', function(){
        var d = $(this).val();
        $('#date_to').attr('min', d);
        var d = new Date($(this).val());
        $('#date_to').val(JSON.stringify(new Date(d)).slice(1,11));
    });


   var unit = $("#unit_shift");  

    ///unit on change section enable

    unit.on("change", function(){ 
        userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Area</th>');
        $( "#area_shift" ).prop( "disabled", false );

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
});
</script>
@endpush


@endsection