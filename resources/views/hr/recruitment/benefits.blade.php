@extends('hr.layout')
@section('title', 'Employee Benefits')
@section('main-content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#">Human Resource</a>
				</li> 
				<li>
					<a href="#">Employee</a>
				</li>
				<li class="active">Benefits</li>
			</ul><!-- /.breadcrumb -->

		</div>

		<div class="page-content">  
           <div class="panel panel-success">

                <div class="panel-heading">
                    <h6>
                    Benefits 
                        @if(request()->get("associate_id"))
                        <div class="btn-group pull-right"> 
                            <?php $ass_id = request()->get("associate_id"); ?>
                            <a href='{{ url("hr/recruitment/employee/show/$ass_id") }}' target="_blank" class="btn btn-sm btn-success" title="Profile"><i class="glyphicon glyphicon-user"></i></a>
                            <a href='{{ url("hr/recruitment/employee/edit/$ass_id") }}' class="btn btn-sm btn-success" title="Basic Info"><i class="glyphicon glyphicon-bold"></i></a>
                            <a href='{{ url("hr/recruitment/operation/advance_info_edit/$ass_id") }}' class="btn btn-sm btn-info" title="Advance Info"><i class="glyphicon  glyphicon-font"></i></a>
                            <a href='{{ url("hr/recruitment/operation/benefits?associate_id=$ass_id") }}' class="btn btn-sm btn-primary" title="Benefits"><i class="fa fa-usd"></i></a>
                            <a href='{{ url("hr/ess/medical_incident?associate_id=$ass_id") }}' class="btn btn-sm btn-warning" title="Medical Incident"><i class="fa fa-stethoscope"></i></a>
                            <a href='{{ url("hr/operation/servicebook?associate_id=$ass_id") }}' class="btn btn-sm btn-danger" title="Service Book"><i class="fa fa-book"></i></a>
                        </div>
                        @endif
                    </h6>
              </div> 
                <div class="panel-body"> 
                    <div class="row">
                          <!-- Display Erro/Success Message -->
                        @include('inc/message')
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <!-- <h1 align="center">Add New Employee</h1> -->
                            </br>
                            <form class="form-horizontal" role="form" method="post" action="{{ url('hr/recruitment/operation/benefits') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                               <div class="col-sm-12">  
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_as_id"> Associate's ID <span style="color: red; vertical-align: text-top;">&#42;</span> </label>
                                            <div class="col-sm-8">
                                                {{ Form::select('ben_as_id', [request()->get("associate_id") => request()->get("associate_id")], request()->get("associate_id"), ['placeholder'=>'Select Associate\'s ID', 'id'=>'ben_as_id', 'class'=> 'associates no-select form-control', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }}  
                                            </div>
                                        </div> 

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_joining_salary"> Gross Salary <span style="color: red; vertical-align: text-top;">&#42;</span> </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_joining_salary" id="ben_joining_salary" placeholder="Gross Salary(tk) As Per Joining Letter" class="form-control" data-validation="required length number"data-validation-length="1-20" data-validation-allowing="float" data-validation-error-msg="Invalid Gross salary"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_cash_amount"> CASH <span style="color: red; vertical-align: text-top;">&#42;</span> </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_cash_amount" id="ben_cash_amount" placeholder="Amount Paid in Cash" class="form-control" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid CASH amount"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_bank_amount"> BANK <span style="color: redk vertical-align: text-top;">&#42;</span> </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_bank_amount" id="ben_bank_amount" placeholder="Amount Paid in Bank" class="form-control" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid BANK Amount"/>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_basic"> Basic Salary</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_basic" id="ben_basic" placeholder="Basic Salary" value="0" class="form-control" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Basic Salary" readonly/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_house_rent"> House Rent</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_house_rent" id="ben_house_rent" value="0" placeholder="House Rent" class="form-control" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid House Rent" readonly/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_medical"> Medical</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_medical" id="ben_medical" placeholder="Medical" class="form-control" value="{{ $structure->medical }}" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Medical Allowance" readonly/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_transport"> Transportation</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_transport" id="ben_transport" value="{{ $structure->transport }}" placeholder="Transportation" class="form-control" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20"data-validation-error-msg="Invalid Transportation Allowance" readonly/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_food"> Food</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_food" id="ben_food" placeholder="Food" value="{{ $structure->food }}" class="form-control" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Food Allowance" readonly/>
                                            </div>
                                        </div>
                                       
                                    </div>    
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_food"> Fixed</label>
                                            <div class="col-sm-8 pull-left" style="padding-top: 7px;" >
                                                <input type="checkbox" name="fixed_check" id="fixed_check"/>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="full_salary"> Full Salary Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="full_salary" id="full_salary" placeholder="Full Salary" value="" class="form-control fixed-salary"  disabled="disabled"  />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_as_id"> Associate's ID<span style="color: red">&#42;</span> </label>
                                            <div class="col-sm-8">
                                              {{--   {{ Form::select('ben_as_id', [request()->get("associate_id") => request()->get("associate_id")], request()->get("associate_id"), ['placeholder'=>'Select Associate\'s ID', 'id'=>'ben_as_id', 'class'=> 'associates no-select form-control', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }}   --}}
                                            </div>
                                        </div>  -->

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_joining_salary_fixed"> Gross Salary <span style="color: red; vertical-align: text-top;">&#42;</span> </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_joining_salary_fixed" id="ben_joining_salary_fixed" placeholder="Gross Salary(tk) As Per Joining Letter" class="form-control fixed-salary" data-validation="length number"data-validation-length="1-20" data-validation-allowing="float" data-validation-error-msg="Invalid Gross salary"  disabled="disabled"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_cash_amount"> CASH <span style="color: red; vertical-align: text-top;">&#42;</span> </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_cash_amount_fixed" id="ben_cash_amount_fixed" placeholder="Amount Paid in Cash" class="form-control fixed-salary" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid CASH amount" disabled="disabled"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_bank_amount"> BANK <span style="color: red; vertical-align: text-top;">&#42;</span> </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_bank_amount_fixed" id="ben_bank_amount_fixed" placeholder="Amount Paid in Bank" class="form-control fixed-salary" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid BANK Amount"  disabled="disabled"/>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_basic"> Basic Salary</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_basic_fixed" id="ben_basic_fixed" placeholder="Basic Salary" value="0" class="form-control fixed-salary" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Basic Salary"  disabled="disabled" readonly/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_house_rent"> House Rent</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_house_rent_fixed" id="ben_house_rent_fixed" value="0" placeholder="House Rent" class="form-control fixed-salary" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid House Rent" disabled="disabled" readonly/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_medical"> Medical</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_medical_fixed" id="ben_medical_fixed" placeholder="Medical" class="form-control fixed-salary" value="{{ $structure->medical }}" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Medical Allowance" disabled="disabled" readonly/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_transport"> Transportation</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_transport_fixed" id="ben_transport_fixed" value="{{ $structure->transport }}" placeholder="Transportation" class="form-control fixed-salary" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20"data-validation-error-msg="Invalid Transportation Allowance" disabled="disabled" readonly/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label no-padding-right" for="ben_food"> Food</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ben_food_fixed" id="ben_food_fixed" placeholder="Food" value="{{ $structure->food }}" class="form-control fixed-salary" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Food Allowance" disabled="disabled" readonly/>
                                            </div>
                                        </div>
                                        
                                    </div>  
                               </div>    
                                <div class="col-sm-12">

                                    <div class="clearfix form-actions">
                                        <div class="col-md-offset-4 col-md-4 text-center"> 
                                            <button class="btn btn-sm btn-success" type="submit" id="ben_submit">
                                                <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                            </button>

                                            &nbsp; &nbsp; &nbsp;
                                            <button class="btn btn-sm" type="reset">
                                                <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>    

                                <!-- /.row --> 
                                <hr /> 
                            </form> 
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>


<script type="text/javascript">  
function drawNewBtn(associate_id)
{
    var url = "{{ url("") }}";
    var newUrl = "<div class=\"btn-group\">"+ 
        "<a href='"+url+'/hr/recruitment/employee/edit/'+associate_id+"'  class=\"btn btn-sm btn-success\" title=\"Basic Info\"><i class=\"glyphicon glyphicon-bold\"></i></a>"+
        "<a href='"+url+'/hr/recruitment/operation/advance_info_edit/'+associate_id+"'  class=\"btn btn-sm btn-info\" title=\"Advance Info\"><i class=\"glyphicon  glyphicon-font\"></i></a>"+
        "<a href='"+url+'/hr/recruitment/operation/benefits?associate_id='+associate_id+"' class=\"btn btn-sm btn-primary\" title=\"Benefits\"><i class=\"fa fa-usd\"></i></a>"+
        "<a href='"+url+'/hr/ess/medical_incident?associate_id='+associate_id+"'  class=\"btn btn-sm btn-warning\" title=\"Medical Incident\"><i class=\"fa fa-stethoscope\"></i></a>"+
        "<a href='"+url+'/hr/operation/servicebook?associate_id='+associate_id+"' class=\"btn btn-sm btn-danger\" title=\"Service Book\"><i class=\"fa fa-book\"></i></a>"+
    "</div>"; 
    $("#newBtn").html(newUrl);
}

$(window).load(function(){ 
    let associate_id = '{{ request()->get("associate_id") }}';
    if (associate_id)
    drawNewBtn(associate_id);
});


$(document).ready(function(){
    //get current benifit when id selected
    var id_selected= $('#ben_as_id');
    id_selected.on('change', function(){
        $.ajax({
            url: '{{ url("hr/recruitment/get_benefit_by_id") }}',
            data: {
                id: id_selected.val()
            },
            success: function(result)
            {  
                drawNewBtn(id_selected.val());
                //-----------------------------

                if (result.benefit)
                {
                    $('#ben_joining_salary').val(result.benefit['ben_joining_salary']);
                    $('#ben_cash_amount').val(result.benefit['ben_cash_amount']);
                    $('#ben_bank_amount').val(result.benefit['ben_bank_amount']);
                    $('#ben_basic').val(result.benefit['ben_basic']);
                    $('#ben_house_rent').val(result.benefit['ben_house_rent']);
                    $('#ben_medical').val(result.benefit['ben_medical']);
                    $('#ben_transport').val(result.benefit['ben_transport']);
                    $('#ben_food').val(result.benefit['ben_food']);

                    // Fixed Salary

                    $('#ben_joining_salary_fixed').val(result.benefit['ben_joining_salary']);
                    $('#ben_cash_amount_fixed').val(result.benefit['ben_cash_amount']);
                    $('#ben_bank_amount_fixed').val(result.benefit['ben_bank_amount']);
                    $('#ben_basic_fixed').val(result.benefit['ben_basic']);
                    $('#ben_house_rent_fixed').val(result.benefit['ben_house_rent']);
                    $('#ben_medical_fixed').val(result.benefit['ben_medical']);
                    $('#ben_transport_fixed').val(result.benefit['ben_transport']);
                    $('#ben_food_fixed').val(result.benefit['ben_food']);
                }else{
                    $('#ben_joining_salary').val(0);
                    $('#ben_cash_amount').val(0);
                    $('#ben_bank_amount').val(0);
                    $('#ben_basic').val(0);
                    $('#ben_house_rent').val(0);
                    $('#ben_medical').val(0);
                    $('#ben_transport').val(0);
                    $('#ben_food').val(0);

                    // Fixed Salary

                    $('#ben_joining_salary_fixed').val(0);
                    $('#ben_cash_amount_fixed').val(0);
                    $('#ben_bank_amount_fixed').val(0);
                    $('#ben_basic_fixed').val(0);
                    $('#ben_house_rent_fixed').val(0);
                    $('#ben_medical_fixed').val(0);
                    $('#ben_transport_fixed').val(0);
                    $('#ben_food_fixed').val(0);
                }
            },
            error:function(xhr)
            {
                console.log('No previous salary');
            }
        });
    }); 

    $('#ben_joining_salary').on('change', function(){
        var basic_percent= '{{ $structure->basic }}';
        var house= '{{ $structure->house_rent }}';
        var medical= '{{ $structure->medical }}';
        var trans= '{{ $structure->transport }}';
        var food= '{{ $structure->food }}';

        var salary= parseFloat($('#ben_joining_salary').val());
        var sub =parseFloat(medical)+parseFloat(trans)+parseFloat(food);
        var basic= parseFloat((salary-sub)/basic_percent).toFixed(2);
        $('#ben_basic').val(basic);
        var house= parseFloat(salary-sub-basic).toFixed(2);
        $('#ben_house_rent').val(house);

   
    });

    $('#ben_cash_amount').on('change', function(){
        var salary= parseFloat($('#ben_joining_salary').val());
        
        if(isNaN(salary)){
            alert("Please enter Joining Salary first");
        }
        else{

            var cash= parseFloat($('#ben_cash_amount').val());
            
            if(((cash)>salary) || (cash<0))
            {
                alert("Cash Amount Can not be greater than Salary or Negative");
                $('#ben_cash_amount').val(salary.toFixed(2));
                $('#ben_bank_amount').val(0);
            }
            else{
                var bank= salary-cash;
                $('#ben_bank_amount').val(bank.toFixed(2));
            }
            
        }
    });

    $('#ben_bank_amount').on('change', function(){
        var salary= parseFloat($('#ben_joining_salary').val()).toFixed(2); //alert(salary);
        
        if(isNaN(salary)){
            alert("Please enter Joining Salary first");
        }
        else{ 
            var bank= parseFloat($('#ben_bank_amount').val()).toFixed(2);
            if(bank>salary || bank<0)
            {
              //  alert("Cash Amount");
                $('#ben_cash_amount').val(salary.toFixed(2));
                $('#ben_bank_amount').val(0);
            }
            else{
                var cash= salary-bank;
                $('#ben_cash_amount').val(cash.toFixed(2));
            }

        }
    });

    $('#ben_joining_salary').on('change', function(){
        var cash= parseFloat($('#ben_cash_amount').val()).toFixed(2);
        var bank= parseFloat($('#ben_bank_amount').val()).toFixed(2);
        if(bank>0 || cash> 0){
            $('#ben_cash_amount').val(0);
            $('#ben_bank_amount').val(0);
        }
    });

/* For Fixed Salary */

     $('#ben_joining_salary_fixed').on('change', function(){
        var basic_percent= '{{ $structure->basic }}';
        var house= '{{ $structure->house_rent }}';
        var medical= '{{ $structure->medical }}';
        var trans= '{{ $structure->transport }}';
        var food= '{{ $structure->food }}';

        var salary= parseFloat($('#ben_joining_salary_fixed').val());
        var sub =parseFloat(medical)+parseFloat(trans)+parseFloat(food);
        var basic= parseFloat((salary-sub)/basic_percent).toFixed(2);
        $('#ben_basic_fixed').val(basic);
        var house= parseFloat(salary-sub-basic).toFixed(2);
        $('#ben_house_rent_fixed').val(house);
    });

    $('#ben_cash_amount_fixed').on('change', function(){
        var salary= parseFloat($('#ben_joining_salary_fixed').val());
        
        if(isNaN(salary)){
            alert("Please enter Joining Salary first");
        }
        else{

            var cash= parseFloat($('#ben_cash_amount_fixed').val());
            
            if(((cash)>salary) || (cash<0))
            {
                alert("Cash Amount Can not be greater than Salary or Negative");
                $('#ben_cash_amount_fixed').val(salary.toFixed(2));
                $('#ben_bank_amount_fixed').val(0);
            }
            else{
                var bank= salary-cash;
                $('#ben_bank_amount_fixed').val(bank.toFixed(2));
            }
            
        }
    });

    $('#ben_bank_amount_fixed').on('change', function(){
        var salary= parseFloat($('#ben_joining_salary_fixed').val()).toFixed(2); //alert(salary);
        
        if(isNaN(salary)){
            alert("Please enter Joining Salary first");
        }
        else{ 
            var bank= parseFloat($('#ben_bank_amount_fixed').val()).toFixed(2);
            if(bank>salary || bank<0)
            {
              //  alert("Cash Amount");
                $('#ben_cash_amount_fixed').val(salary.toFixed(2));
                $('#ben_bank_amount_fixed').val(0);
            }
            else{
                var cash= salary-bank;
                $('#ben_cash_amount_fixed').val(cash.toFixed(2));
            }

        }
    });

    $('#ben_joining_salary_fixed').on('change', function(){
        var cash= parseFloat($('#ben_cash_amount_fixed').val()).toFixed(2);
        var bank= parseFloat($('#ben_bank_amount_fixed').val()).toFixed(2);
        if(bank>0 || cash> 0){
            $('#ben_cash_amount_fixed').val(0);
            $('#ben_bank_amount_fixed').val(0);
        }
    });

/********************/

    $('form').submit(function(e){
        var salary   = parseFloat($('#ben_joining_salary').val());
        var basic    = parseFloat($('#ben_basic').val());
        var house    = parseFloat($('#ben_house_rent').val());
        var medical  = parseFloat($('#ben_medical').val());
        var transport= parseFloat($('#ben_transport').val());
        var food     = parseFloat($('#ben_food').val());
        var total_check_a;
        var total_check_b;

        total_check_a = totalSalary(salary,basic,house,medical,transport,food);

       // If Fixed checked then call totalSalary() for fixed Salary

        var is_checked =  $("#fixed_check").is(":checked");
        if(is_checked) { 
            var salary   = parseFloat($('#ben_joining_salary_fixed').val());
            var basic    = parseFloat($('#ben_basic_fixed').val());
            var house    = parseFloat($('#ben_house_rent_fixed').val());
            var medical  = parseFloat($('#ben_medical_fixed').val());
            var transport= parseFloat($('#ben_transport_fixed').val());
            var food     = parseFloat($('#ben_food_fixed').val()); 

            total_check_b = totalSalary(salary,basic,house,medical,transport,food);
        }
       
        if(total_check_a==0 || total_check_b==0){
            alert("Invalid Salary Calculation");            
            e.preventDefault();
        }
    });

// Calculate total Salary
    function totalSalary(salary,basic,house,medical,transport,food){  

        var formSubmit = 1;

        var total= parseFloat(basic+house+medical+transport+food).toFixed(2);
        
        if(salary != total){
            
     
          formSubmit = 0;          
        }
         return formSubmit;

           
    } 

  // If Fixed check box click then Full Salary Amount Enable
    
    $("#fixed_check").change(function() {
     var is_checked = $(this).is(":checked");
     if(!is_checked) {
      $(".fixed-salary").val("");
     }
     $(".fixed-salary").prop("disabled", !is_checked);

   });

});
</script>

<script type="text/javascript">
$(document).ready(function()
{   
    $('select.associates').select2({
        placeholder: 'Select Associate\'s ID',
        ajax: {
            url: '{{ url("hr/associate-search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.associate_name,
                            id: item.associate_id
                        }
                    }) 
                };
          },
          cache: true
        }
    }); 
});
</script>
@endsection