@extends('hr.layout')
@section('title', 'Add Role')
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
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="page-header">
				<h1>Payroll <small><i class="ace-icon fa fa-angle-double-right"></i> Increment </small></h1>
            </div>

            <div class="panel panel-default">
               <div class="panel-body">
                   <div class="row" style="margin-left: 10px; margin-top: 10px;">
                  <!-- Display Erro/Success Message -->
                    @include('inc/message')
                       <form class="form-horizontal" role="form" method="post" action="{{ url('hr/payroll/increment_update')  }}" enctype="multipart/form-data">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <!-- <h1 align="center">Add New Employee</h1> -->
                           
                            {{ csrf_field() }} 
                                <input type="hidden" name="increment_id" value="{{ $increment->id }}">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="associate_id"> Associate's ID</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="associate_id" id="associate_id" value="{{ $increment->associate_id }}" class="col-xs-10 col-sm-6" readonly> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="unit" >Unit </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('unit', $unitList, $increment->as_unit_id, ['placeholder'=>'Select Unit', 'class'=> 'col-xs-10 col-sm-6 filter','disabled'=>'disabled' ]) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="as_emp_type_id" >Associate Type </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('as_emp_type_id', $employeeTypes, $increment->as_emp_type_id, ['placeholder'=>'Select Associate Type', 'class'=> 'col-xs-10 col-sm-6 filter', 'disabled'=>'disabled']) }} 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="increment_type" >Increment Type </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('increment_type', $typeList, $increment->increment_type, ['placeholder'=>'Select Increment Type', 'class'=> 'col-xs-10 col-sm-6']) }}
                                    </div>
                                </div>

                                {{-- <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="elligible_date"> Elligible Date </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="elligible_date" id="elligible_date" class="datepicker col-xs-10 col-sm-6 filter" value="{{ isset($increment->eligible_date)?$increment->eligible_date:'' }}" />
                                    </div>
                                </div> --}}
                                
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="applied_date"> Applied Date </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="applied_date" id="applied_date" class="datepicker col-xs-10 col-sm-6 filter" value="{{ isset($increment->applied_date)?$increment->applied_date:'' }}" readonly="readonly" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="effective_date"> Effective Date </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="effective_date" id="effective_date" class="datepicker col-xs-10 col-sm-6 filter" value="{{ isset($increment->effective_date)?$increment->effective_date:'' }}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="increment_amount"> Increment Amount/Percentage  </label>
                                    <div class="col-sm-2" style="padding-right: 0;">
                                        <input type="text" name="increment_amount" id="increment_amount" placeholder="Increment Amount/Percentage" class="col-xs-12" data-validation="required number length" data-validation-length="1-11" data-validation-allowing="float" value="{{$increment->increment_amount}}" />
                                    </div>
                                    <div class="col-sm-2" style="padding-right: 0;">
                                        <select class="no-select col-xs-12" data-validation="required" id="amount_type" name="amount_type">
                                            <option value="">Select Amount Type</option>
                                            <option value="1" <?php if($increment->amount_type==1) echo "selected"; ?> >Increased Amount</option>
                                            <option value="2" <?php if($increment->amount_type==2) echo "selected"; ?> >Percent</option>
                                        </select>
                                    </div>
                                </div> 

                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="clearfix form-actions">
                                    <div class="col-md-offset-3 col-md-9" style="padding-left: 9%;"> 
                                        <button class="btn btn-info btn-sm" type="submit">
                                            <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                        </button>

                                        &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-sm" type="reset">
                                            <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                        </button>
                                    </div>
                                </div>

                                <!-- /.row --> 
                             
                            <!-- PAGE CONTENT ENDS -->
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
    //Filter User
    $("body").on("keyup", "#AssociateSearch", function() {
        var value = $(this).val().toLowerCase(); 
        $("#AssociateTable #user_info tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });


    var userInfo = $("#user_info");
    var userFilter = $("#user_filter");
    var emp_type = $("select[name=emp_type]");
    var unit     = $("select[name=unit]");
    var date     = $('input[name=effective_date]'); 
    $(".filter").on('change keyup', function(){ 
        $.ajax({
            url: '{{ url("hr/payroll/get_associate") }}',
            data: {
                emp_type: emp_type.val(),
                unit: unit.val(),
                date: date.val(),
            },
            success: function(data)
            { 
                console.log(data)
                userInfo.html(data.result);
                userFilter.html(data.filter);
            },
            error:function(xhr)
            {
                console.log('Employee Type Failed');
            }
        });
    }); 

    $('#checkAll').click(function(){
        var checked = $(this).prop('checked');
        $('input:checkbox').prop('checked', checked);
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
    });

    $('#formSubmit').on("click", function(e){
        var checkedBoxes= [];
        $('input[type="checkbox"]:checked').each(function() {
            if(this.value != "on")
            checkedBoxes.push($(this).val());
        });
    });

    //date range validation...
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
@endsection