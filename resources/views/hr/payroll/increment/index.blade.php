@extends('hr.layout')
@section('title', 'Eligible')
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
					<a href="#"> Payroll </a>
				</li>
				<li class="active"> Increment </li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/payroll/increment-list')}}" class="btn btn-primary pull-right">Eligible List</a>
                </li>
			</ul><!-- /.breadcrumb --> 
		</div>

        @include('inc/message')
        <div class="panel panel-success">
            <div class="panel-body">
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
                            <select name="line_id" class="form-control capitalize filter" id="line_id" disabled >
                                <option selected="" value="">Choose...</option>
                            </select>
                            <label for="line_id">Line</label>
                        </div>
                     </div>
                    <div class="col-3"> 
                        <div class="form-group has-float-label select-search-group">
                            <select class="col-sm-12 filter" id="shift_id" name="shift_id"  disabled="disabled">
                                <option value="">Select</option>
                            </select> 

                            <label  for="shift_id" style="color: maroon;">Shift </label>
                        </div>
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

		<div class="page-content"> 
            @can('Manage Increment')
            <div class="panel panel-success">
                <div class="panel-body">
                    
                    <form class="form-horizontal needs-validation" novalidate role="form" method="post" action="{{ url('hr/payroll/increment')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'class'=> ' filter form-control']) }}
                                    <label for="hr_unit_name" >Unit </label>
                                </div>

                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Associate Type', 'class'=> ' filter form-control']) }} 
                                    <label  for="hr_unit_name" >Associate Type </label>
                                </div>

                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('increment_type', $typeList, null, ['placeholder'=>'Select Increment Type','class'=>'form-control']) }}
                                    <label  for="hr_unit_name" >Increment Type </label>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        
                                        <div class="form-group has-float-label has-required">
                                            <input type="date" name="applied_date" id="applied_date" class="form-control  " placeholder="Enter Date"  />
                                            <label for="applied_date"> Applied Date </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        
                                        <div class="form-group has-float-label has-required ">
                                            <label for="effective_date"> Effective Date </label>
                                            <input type="date" name="effective_date" id="effective_date" class="form-control" required placeholder="Enter Date" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    
                                    <div class="col-6">
                                        <div class="form-group has-float-label select-search-group has-required">
                                            <select class="form-control" data-validation="required" id="amount_type" name="amount_type">
                                                <option value="">Select Amount Type</option>
                                                <option value="1">Increased Amount</option>
                                                <option value="2">Percent</option>
                                            </select>
                                            <label>Type</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group has-float-label has-required">
                                            <input type="text" name="increment_amount" id="increment_amount" placeholder="Increment Amount/Percentage" class="form-control" required/>
                                            <label  for="increment_amount">Amount </label>
                                        </div>
                                        
                                    </div>
                                </div>

                                
                                <div class="clearfix form-actions responsive-hundred">
                                    <div class="align-center"> 
                                        <button class="btn  btn-primary" type="submit">
                                            <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                        </button>

                                        &nbsp; &nbsp; &nbsp;
                                        <button class="btn" type="reset">
                                            <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="row m-0">
                                    <div class="col-6 text-center" style="background-color:#099faf; color: #fff;">
                                        <p style="padding-top: 5px;">Selected Employee: <span id="selectEmp" style="font-weight: bold;"></span></p>
                                    </div>
                                    <div class="col-6 text-center" style="background-color: #87B87F; color: #fff;">
                                        <p style="padding-top: 5px;">Total Employee: <span id="totalEmp" style="font-weight: bold;"></span></p>
                                    </div>
                                </div>
                                <div style="height: 400px; overflow: auto;">
                                    <table id="AssociateTable" class="table header-fixed1 table-compact table-bordered" >
                                        <thead>
                                            <tr>
                                                <th class="sticky-th"><input type="checkbox" id="checkAll" class="sticky-th" /></th>
                                                <th class="sticky-th">Associate ID</th>
                                                <th class="sticky-th">Associate Name</th>
                                            </tr>
                                            <tr>
                                                <th class="sticky-th" colspan="3" id="user_filter" style='top: 40px;' ></th>
                                            </tr>
                                        </thead> 
                                        <tbody id="user_info">
                                        </tbody>
                                    </table>
                                </div>    
                            </div>
                        </div>
                    </form>
                    <!-- /.col -->
                </div>
            </div> 
            @endcan
      
		</div><!-- /.page-content -->
	</div>
</div>
@push('js')
<script type="text/javascript"> 
$(document).ready(function(){
    var totalempcount = 0;
    var totalemp = 0;
     $('#dataTables').DataTable({
            pagingType: "full_numbers" ,
    }); 
    var dt = $('#dataTables2').DataTable({
            pagingType: "full_numbers",
            dom: "<'row'<'col-2'l><'col-4'i><'col-3 text-center'B><'col-3'f>>tp",
            buttons: [
                {
                    extend: 'print',
                    className: 'btn-sm btn-success',
                    title: 'Arear Salary List',
                    pageSize: 'A4',
                    header: true,
                    exportOptions: {
                        columns: ['0','1','2','3','4','5','6'],
                        stripHtml: false
                    },
                    "action": allExport,
                }
            ]
    }); 
    
    //Show increment list
    $('#increment_list_button').on('click', function(){
        $('#increment_list_div').removeAttr('hidden');
        $('#arear_salary_list_div').attr('hidden','hidden');
        $(this).attr('style','background : linear-gradient(45deg, #8a041a, transparent)!important; border-radius: 5px;');
        $('#arear_salary_list_button').removeAttr('style','background : linear-gradient(45deg, #8a041a, transparent) !important; border-radius: 5px;');

        $('html,body').animate({
            scrollTop: $(".dv").offset().top},
            'slow');
    });
    //Show arear salary list
    $('#arear_salary_list_button').on('click', function(){
        $('#arear_salary_list_div').removeAttr('hidden');
        $('#increment_list_div').attr('hidden','hidden');
        $(this).attr('style','background : linear-gradient(45deg, #8a041a, transparent)!important; border-radius: 5px;');
        $('#increment_list_button').removeAttr('style','background : linear-gradient(45deg, #8a041a, transparent) !important; border-radius: 5px;');
        $('html,body').animate({
            scrollTop: $(".dv").offset().top},
            'slow');
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


    var userInfo = $("#user_info");
    var userFilter = $("#user_filter");
    var emp_type = $("select[name=emp_type]");
    var unit     = $("select[name=unit]");
    var date     = $('input[name=effective_date]'); 
    $(".filter").on('change', function(){ 
        userInfo.html('<tr><th colspan="3" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th></tr>');
        $.ajax({
            url: '{{ url("hr/payroll/get_associate") }}',
            data: {
                emp_type: emp_type.val(),
                unit: unit.val(),
                // date: date.val(),
            },
            success: function(data)
            { 
                // console.log(data);
                totalempcount = 0;
                totalemp = 0;
                if(data.result == ""){
                    $('#totalEmp').text('0');
                    $('#selectEmp').text('0');
                    userInfo.html('<tr><th colspan="3" style=\"text-align: center; font-size: 14px; color:red;\">No Data Found</th></tr>');    
                }
                else{
                    userInfo.html(data.result);
                    totalemp = data.total;
                    $('#selectEmp').text(totalempcount);
                    $('#totalEmp').text(data.total);
                }
                userFilter.html(data.filter);
            },
            error:function(xhr)
            {
                console.log('Employee Type Failed');
            }
        });
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
        var checkedBoxes= [];
        $('input[type="checkbox"]:checked').each(function() {
            if(this.value != "on")
            checkedBoxes.push($(this).val());
        });
    });

    //date range validation..
    $('#applied_date, #effective_date').on('dp.change', function(){
        var elligible = $('#applied_date').val();
        var effective = $('#effective_date').val();
        if(elligible != '' && effective != ''){
            // console.log('applied_date :'+elligible+' effective_date: '+effective);
            if(new Date(elligible) > new Date(effective) ){
                alert('Elligible Date can not be greater than Effective Date');
                $('#applied_date').val($('#effective_date').val());
            }

        }
    });

});
</script>
@endpush
@endsection