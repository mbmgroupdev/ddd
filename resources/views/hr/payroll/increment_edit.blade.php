@extends('hr.layout')
@section('title', 'Increment Edit')
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
                <li class="active"> Increment Edit</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            @can('Manage Increment')
            @include('inc/message')
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h6>Increment Edit
                        <a href="{{url('hr/payroll/increment-list')}}" class="btn btn-primary pull-right">Increment List</a>
                    </h6>
                </div>
                <div class="panel-body">
                      <!-- Display Erro/Success Message -->
                    
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/payroll/increment_update')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <input type="hidden" name="increment_id" value="{{ $increment->id }}">
                        <div class="row justify-content-center">
                            <div class="col-6">
                                <div class="form-group has-float-label">
                                    <input type="text" name="associate_id" id="associate_id" value="{{ $increment->associate_id }}" class="form-control" readonly> 
                                    <label id="associate_id">Associate ID</label>
                                </div>
                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('unit', $unitList, $increment->as_unit_id, ['placeholder'=>'Select Unit', 'class'=> ' filter form-control']) }}
                                    <label for="hr_unit_name" >Unit </label>
                                </div>

                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('emp_type', $employeeTypes, $increment->as_emp_type_id, ['placeholder'=>'Select Associate Type', 'class'=> ' filter form-control']) }} 
                                    <label  for="hr_unit_name" >Associate Type </label>
                                </div>

                                <div class="form-group has-float-label select-search-group">
                                    {{ Form::select('increment_type', $typeList, $increment->increment_type, ['placeholder'=>'Select Increment Type','class'=>'form-control']) }}
                                    <label  for="hr_unit_name" >Increment Type </label>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        
                                        <div class="form-group has-float-label has-required">
                                            <input type="date" name="applied_date" id="applied_date" class="form-control  " placeholder="Enter Date"  value="{{ isset($increment->applied_date)?$increment->applied_date:'' }}"/>
                                            <label for="applied_date"> Applied Date </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        
                                        <div class="form-group has-float-label ">
                                            <label for="effective_date"> Effective Date </label>
                                            <input type="date" name="effective_date" id="effective_date" class="form-control  " placeholder="Enter Date" value="{{ isset($increment->effective_date)?$increment->effective_date:'' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group has-float-label has-required">
                                            <input type="text" name="increment_amount" id="increment_amount" placeholder="Increment Amount/Percentage" class="form-control" required value="{{$increment->increment_amount}}"/>
                                            <label  for="increment_amount">Amount </label>
                                        </div>


                                        
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group has-float-label select-search-group has-required">
                                            <select class="form-control" data-validation="required" id="amount_type" name="amount_type">
                                                <option value="">Select Amount Type</option>
                                                <option value="1" <?php if($increment->amount_type==1) echo "selected"; ?> >Increased Amount</option>
                                                <option value="2" <?php if($increment->amount_type==2) echo "selected"; ?> >Percent</option>
                                            </select>
                                            <label>Type</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn  btn-primary pull-right" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Update
                                    </button>
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