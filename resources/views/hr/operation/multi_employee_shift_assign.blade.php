@extends('hr.layout')
@section('title', 'Employee Shift Change')
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
                <li class="active">Employee Shift Change </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        
        <div class="panel panel-info">
            
            
            <div class="panel-body">
                @include('inc/message')
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
                                    {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control filter']) }} 
                                    <label  for="emp_type">Employee Type </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    <select name="department" id="department_shift" class= "form-control filter" disabled ><option value="">Select Department</option></select>
                                    <label  for="department">Department </label>
                                </div>
                             </div>
                            <div class="col-3"> 
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('section', [], null, ['placeholder'=>'Select Section','id'=>'section_shift', 'class'=> 'form-control filter','disabled']) }} 
                                    <label  for="section">Section </label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('subsection', [], null, ['placeholder'=>'Select Sub Section','id'=>'subsection_shift', 'class'=> 'form-control filter', 'disabled']) }}
                                    <label  for="subsection">Sub Section </label>
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
                                    
                                <div class="form-group has-float-label select-search-group">
                                    <select class="col-sm-12  filter" id="current_shift" name="current_shift"  disabled="disabled">
                                        <option value="">Select</option>
                                    </select> 
                                    <label  for="current_shift" style="color: maroon;">Current Shift </label>
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
                <form role="form" method="POST" action="{{ url('hr/operation/multi_emp_shift_status_save') }}" enctype="multipart/form-data" class="row"> 
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
                    <div class="col-6 pt-5">
                        <div class="form-group has-required has-float-label select-search-group">
                            <select class="form-control" id="target_shift" name="target_shift" required="required" disabled="disabled">
                                <option value="">Select</option>

                            </select>
                            <label  for="target_shift" style="color: maroon;">Target Shift </label>
                        </div>
                        <div class="form-group"> 
                            <button type="submit" id="formSubmit" class="btn btn-primary pull-right" >
                                Change Shift
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
 var dt = $('#dataTables2').DataTable({
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
    var current_shift = $("select[name=current_shift]");

    $(".filter").on('change', function(){
        if(unit.val()) {
            // console.log('Searching...');
            userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th>');
            
            $.ajax({
                url: '{{ url("hr/operation/get_associate_by_type_unit_for_multi_shift_assign") }}',
                data: {
                    emp_type: emp_type.val(),
                    unit    : unit.val(),
                    otnonot    : otnonot.val(),
                    shift   : shift.val(),
                    section : section.val(),
                    subsection : subsection.val(),
                    area : area.val(),
                    department : department.val(),
                    current_shift : current_shift.val()
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
        
        //Showing Unit Wise Current Shifts.....
        $( "#current_shift" ).prop( "disabled", false );
        $( "#target_shift" ).prop( "disabled", false );
        var unit_id = unit.val();
        $.ajax({
            url: '{{url('hr/operation/get_shift_unit_wise')}}',
            data: {unit_id: unit_id},
            type: 'get',
            dataType: 'json',
            success: function(data){
                // console.log(data);
                var show = "<option value=\"\">Select One</option>";
                for(var i = 0; i < data['current'].length; i++){
                    show += "<option value=\""+data['current'][i]+"\">"+data['current'][i]+"</option>";
                }
                $('#current_shift').html(show);

                show = "<option value=\"\">Select One</option>";
                for(var i = 0; i < data['target'].length; i++){
                    show += "<option value=\""+data['target'][i]['hr_shift_name']+"\">"+data['target'][i]['hr_shift_name']+"</option>";
                }
                $('#target_shift').html(show);
            },
            error: function(data){
                alert(data);
            }
        });
    });

    /// area on change department select

    $('#area_shift').on('change', function(){
        //userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Department</th>');
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