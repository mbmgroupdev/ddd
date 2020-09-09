@extends('hr.layout')
@section('title', 'Increment')
@section('main-content')
@push('css')
    <style type="text/css">
        {{-- removing the links in print and adding each page header --}}
        a[href]:after { content: none !important; }
        thead {display: table-header-group;}

        /*.form-group {overflow: hidden;}*/
        table.header-fixed1 tbody {max-height: 240px;  overflow-y: scroll;}

    </style>
@endpush
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
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            @can('Manage Increment')
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h6>Increment Entry</h6>
                </div>
                <div class="panel-body">
                      <!-- Display Erro/Success Message -->
                    @include('inc/message')
                    
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/payroll/increment')  }}" enctype="multipart/form-data">
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
                                        
                                        <div class="form-group has-float-label ">
                                            <label for="effective_date"> Effective Date </label>
                                            <input type="date" name="effective_date" id="effective_date" class="form-control  " placeholder="Enter Date" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group has-float-label has-required">
                                            <input type="text" name="increment_amount" id="increment_amount" placeholder="Increment Amount/Percentage" class="form-control" required/>
                                            <label  for="increment_amount">Amount </label>
                                        </div>
                                        
                                    </div>
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
            {{-- <div class="col-12 widget-box widget-color-blue dv" style="padding-left: 0px; border-radius: 2px; ">
                <div class="row">
                    <div class="col-4">
                        <button class="btn btn-xs btn-primary"  id="increment_list_button">Increment List</button>
                    </div>
                    <div class="col-4" style="text-align: center; color: blue;">
                        <h4 class="no-margin no-padding">Lists</h4>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-xs btn-primary pull-right"  id="arear_salary_list_button">Arear Salary List</button>
                    </div>
                </div>
                <div id="increment_list_div" class="col-12 table-responsive"  hidden="hidden" style="margin-top: 10px;">
                    <table id="dataTables" class="table table-striped table-bordered" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th colspan="5" class="text-center" style="color: blue;">Increment List</th>
                            </tr>
                            <tr>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Inc. Type</th>
                                <th>Inc. Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incrementList AS $increment)
                            <tr>
                                <td>{{ $increment->associate_id }}</td>
                                <td>{{ $increment->as_name }}</td>
                                <td>{{ $increment->inc_type_name }}</td>
                                <td>{{ $increment->increment_amount }}<?php if($increment->amount_type == 2) echo "%"; ?></td>
                                <td>
                                <div class="btn-group">
                                    <a type="button" href="{{ url('hr/payroll/increment_edit/'.$increment->id) }}" class='btn btn-xs btn-primary'><i class="fa fa-pencil"></i></a>
                                </div>
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="arear_salary_list_div" class="col-12 table-responsive"  hidden="hidden" style="margin-top: 10px;">
                    <table id="dataTables2" class="table table-striped table-bordered" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th colspan="8" class="text-center" style="color: blue;">Arear Salay List</th>
                            </tr>
                            <tr>
                                <th style="font-size: 13px !important;">#</th>
                                <th style="font-size: 13px !important;">Associate ID</th>
                                <th style="font-size: 13px !important;">Detail</th>
                                <th style="font-size: 13px !important;">Month/Year</th>
                                <th style="font-size: 13px !important;">Amount</th>
                                <th style="font-size: 13px !important;">Status</th>
                                <th style="font-size: 13px !important;">Total</th>
                                <th style="font-size: 13px !important;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($arrear_data))
                                @foreach($arrear_data as $arr)
                                    <tr>
                                        <td>{{$loop->index+1}}</td>
                                        <td>
                                            @foreach($arr as $val)
                                               <a href="{{url('hr/recruitment/employee/show/'.$val->associate_id)}}" target="_blank">{{ $val->associate_id }}</a>
                                                @break
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($arr as $val)
                                                <span style="color: black; font-weight: bold;">Name:</span> &nbsp{{ $val->as_name }}<br>
                                                <span style="color: black; font-weight: bold;">Unit:</span> &nbsp{{ $val->hr_unit_name}}<br>
                                                <span style="color: black; font-weight: bold;">Dept:</span> &nbsp{{ $val->hr_department_name}}<br>
                                                <span style="color: black; font-weight: bold;">Cell:</span> &nbsp{{ $val->as_contact}}<br>
                                                @break
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($arr as $val)
                                                <?php 
                                                    $monthNum  = $val->month;
                                                    $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                                                    $monthName = $dateObj->format('F'); // March
                                                ?>
                                                {{ $monthName }}, {{ $val->year }}<br>
                                            @endforeach
                                        </td>
                                        <td>
                                            <?php $total_amount = 0;?>
                                            @foreach($arr as $val)
                                                {{ $val->amount }}<br>
                                            <?php $total_amount += $val->amount?>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($arr as $val)
                                                @if($val->status == 0)
                                                    <span style="color: red; font-weight: bold;">Not given</span><br>
                                                @else
                                                    <span style="color: green; font-weight: bold;">Given</span><br>
                                                    
                                                @endif
                                            @endforeach

                                        </td>
                                        <td>
                                            {{$total_amount}}
                                        </td>
                                        <td>
                                            <div class="button-group">
                                                @foreach($arr as $val)
                                                    <a href="{{url('hr/payroll/arear_salary_disburse/'.$val->associate_id )}}" class="btn btn-xx btn-info" style="border-radius: 2px;" rel='tooltip' data-tooltip-location='top' data-tooltip='Take Action'><i class="fa fa-credit-card"></i></a>

                                                    <a href="{{url('#'.$val->associate_id )}}" class="btn btn-xx btn-warning" style="border-radius: 2px;" rel='tooltip' data-tooltip-location='top' data-tooltip='Performance'><i class="fa fa-bar-chart"></i></a>
                                                        @break
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif                            
                        </tbody>
                    </table>
                </div>
            </div>
           --}}

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
                    }
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