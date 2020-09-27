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

        @include('inc/message')
		<div class="page-content">  
           <div class="panel panel-success">
                <div class="panel-heading">
                    <h6>
                        Benefits 
                        <a class="btn btn-primary pull-right" href="{{url('hr/payroll/benefit_list')}}">Benefit List</a>
                    </h6>
                </div> 
                
                <div class="panel-body"> 
                    <form class="form-horizontal needs-validation" novalidate role="form" method="post" action="{{ url('hr/recruitment/operation/benefits') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row justify-content-center">  
                            <div class="col-6 p-4">
                                <input type="hidden" name="ben_id" id="ben_id">
                                <div class="form-group has-float-label has-required select-search-group">
                                    {{ Form::select('ben_as_id', [request()->get("associate_id") => request()->get("associate_id")], request()->get("associate_id"), ['placeholder'=>'Select Associate\'s ID', 'id'=>'ben_as_id', 'class'=> 'associates no-select form-control','required']) }} 
                                    <label for="ben_as_id"> Associate's ID  </label>
                                </div>
                                <div class="form-group has-float-label has-required ">
                                    <input type="text" name="ben_joining_salary" id="ben_joining_salary" placeholder="Gross Salary(tk) As Per Joining Letter" class="form-control" required/>
                                    <label  for="ben_joining_salary"> Gross Salary  </label>
                                </div> 
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group has-float-label has-required">

                                            <input type="text" name="ben_basic" id="ben_basic" placeholder="Basic Salary" value="" class="form-control" required  readonly/>
                                            <label  for="ben_basic"> Basic Salary</label>
                                        </div>

                                        <div class="form-group has-float-label has-required">
                                            <input type="text" name="ben_house_rent" id="ben_house_rent" value="" placeholder="House Rent" class="form-control"  readonly required/>
                                            <label  for="ben_house_rent"> House Rent</label>
                                        </div>

                                        <div class="form-group has-float-label has-required">

                                            <input type="text" name="ben_medical" id="ben_medical" placeholder="Medical" class="form-control" value="{{ $structure->medical }}" required  readonly/>

                                            <label  for="ben_medical"> Medical</label>
                                        </div>

                                        <div class="form-group has-float-label has-required">

                                            <input type="text" name="ben_transport" id="ben_transport" value="{{ $structure->transport }}" placeholder="Transportation" class="form-control" readonly/ required>

                                            <label  for="ben_transport"> Transportation</label>
                                        </div>

                                        <div class="form-group has-float-label has-required">
                                            <input type="text" name="ben_food" id="ben_food" placeholder="Food" value="{{ $structure->food }}" class="form-control" required  readonly/>
                                            <label  for="ben_food"> Food</label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-6">
                                        <div class="form-inline mb-3">
                                            <div class="custom-control custom-radio custom-control-inline">
                                               <input type="radio" id="partial_amount" name="salary_type" class="salary_type custom-control-input" value="Partial" checked>
                                               <label class="custom-control-label" for="partial_amount"> Partial </label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                               <input type="radio" id="bank_amount" name="salary_type" class="salary_type custom-control-input" value="Bank">
                                               <label class="custom-control-label" for="bank_amount"> Bank </label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                               <input type="radio" id="cash_amount" name="salary_type" class="salary_type custom-control-input" value="Cash">
                                               <label class="custom-control-label" for="cash_amount"> Cash </label>
                                            </div>
                                        </div>
                                        

                                        <div id="cash_input" class="form-group has-float-label has-required">
                                            <input type="number" name="ben_cash_amount" id="ben_cash_amount" placeholder="Amount Paid in Cash" class="form-control" min="0"/>
                                            <label  for="ben_cash_amount"> CASH  </label>
                                        </div>
                                        
                                        <div id="bank_input" class="form-group has-float-label has-required ">
                                            <input type="number" name="ben_bank_amount" id="ben_bank_amount" placeholder="Amount Paid in Bank" class="form-control" required min="0" />
                                            <label  for="ben_bank_amount"> BANK </label>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary" type="submit" id="ben_submit">
                                                <i class=" fa fa-check bigger-110"></i> Submit
                                            </button>

                                            &nbsp; &nbsp; &nbsp;
                                            <button class="btn " type="reset">
                                                <i class=" fa fa-undo bigger-110"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-4 benefit-employee">
                                <div class="user-details-block">
                                      <div class="user-profile text-center">
                                            <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                      </div>
                                      <div class="text-center mt-3">
                                         <h4><b id="user-name">Selected User</b></h4>
                                         <p class="mb-0" id="designation">
                                            Employee designation</p>
                                         
                                      </div>
                                </div>
                            </div>
                       </div> 
                    </form> 
                </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>

@push('js')
<script type="text/javascript">  
function draw_new_button(associate_id)
{
    var url = "{{ url("") }}";
    var newUrl = "<div class=\"btn-group\">"+ 
        "<a href='"+url+'/hr/recruitment/employee/edit/'+associate_id+"'  class=\"btn btn-sm btn-success\" title=\"Basic Info\"><i class=\"las la-address-card\"></i></a>"+
        "<a href='"+url+'/hr/recruitment/operation/advance_info_edit/'+associate_id+"'  class=\"btn btn-sm btn-info\" title=\"Advance Info\"><i class=\"las la-user-tie\"></i></a>"+
        "<a href='"+url+'/hr/recruitment/operation/benefits?associate_id='+associate_id+"' class=\"btn btn-sm btn-primary\" title=\"Benefits\"><i class=\"las la-gifts\"></i></a>"+
        "<a href='"+url+'/hr/ess/medical_incident?associate_id='+associate_id+"'  class=\"btn btn-sm btn-warning\" title=\"Medical Incident\"><i class=\"las la-stethoscope\"></i></a>"+
        "<a href='"+url+'/hr/operation/servicebook?associate_id='+associate_id+"' class=\"btn btn-sm btn-danger\" title=\"Service Book\"><i class=\"las la-address-book\"></i></a>"+
    "</div>"; 
    $("#buttons").html(newUrl);
}

$(document).ready(function(){
    //get current benifit when id selected
    let associate_id = '{{ request()->get("associate_id") }}';
    var house= '{{ $structure->house_rent }}';
    var medical= '{{ $structure->medical }}';
    var trans= '{{ $structure->transport }}';
    var food= '{{ $structure->food }}';
    if (associate_id){ 
        get_benefit(associate_id) 
    }
    $(document).on('change','#ben_as_id', function(){
        $('#bank_input').show();
        $('#cash_input').show();
        $('#ben_cash_amount').val('').attr('required',true);
        $('#ben_bank_amount').val('').attr('required',true);
        $('input[name="salary_type"][value="Partial"]').prop('checked', 'checked');
        get_benefit($(this).val());
    });

    $(document).on('change','.salary_type', function(){
        var salary= parseFloat($('#ben_joining_salary').val());
        
        var sub =parseFloat(medical)+parseFloat(trans)+parseFloat(food);
        if(salary>sub){
            if($(this).val() == 'Cash'){
                $('#bank_input').hide();
                $('#cash_input').show();
                $('#ben_cash_amount').val(salary.toFixed(2)).attr('required',true);
                $('#ben_bank_amount').val('').attr('required',false);
                $('input[name="salary_type"][value="Cash"]').prop('checked', 'checked');
            }else if ($(this).val() == 'Bank'){
                $('#ben_bank_amount').val(salary.toFixed(2)).attr('required',true);
                $('#ben_cash_amount').val('').attr('required',false);
                $('#bank_input').show();
                $('#cash_input').hide();
                $('input[name="salary_type"][value="Bank"]').prop('checked', 'checked');
            }else{
                $('#bank_input').show();
                $('#cash_input').show();
                $('#ben_cash_amount').val('').attr('required',true);
                $('#ben_bank_amount').val('').attr('required',true);
                $('input[name="salary_type"][value="Partial"]').prop('checked', 'checked');
            }
        }else{
            $.notify("Please enter Salary first",'error');
        }
    });

    function get_benefit(associate_id)
    {
        $.ajax({
            url: '{{ url("hr/recruitment/get_benefit_by_id") }}',
            data: {
                id: associate_id
            },
            success: function(result)
            {  
                draw_new_button(associate_id);
                $('#avatar').attr('src',result.employee.as_pic);
                $('#user-name').text(result.employee.as_name);

                $('#designation').text(result.employee.hr_designation_name);

                if (result.benefit)
                {
                    $('#ben_id').val(result.benefit['ben_id']);
                    $('#ben_joining_salary').val(result.benefit['ben_current_salary']);
                    $('#ben_cash_amount').val(result.benefit['ben_cash_amount']);
                    $('#ben_bank_amount').val(result.benefit['ben_bank_amount']);
                    $('#ben_basic').val(result.benefit['ben_basic']);
                    $('#ben_house_rent').val(result.benefit['ben_house_rent']);
                    $('#ben_medical').val(result.benefit['ben_medical']);
                    $('#ben_transport').val(result.benefit['ben_transport']);
                    $('#ben_food').val(result.benefit['ben_food']);

                    if(result.benefit['ben_cash_amount'] > 0 && result.benefit['ben_bank_amount'] <=0){
                        $('#bank_input').hide();
                        $('#cash_input').show();
                        $('#ben_cash_amount').attr('required',true);
                        $('#ben_bank_amount').attr('required',false);
                        $('input[name="salary_type"][value="Cash"]').prop('checked', 'checked');
                    }else if(result.benefit['ben_bank_amount'] > 0 && result.benefit['ben_cash_amount'] <=0){ 
                        $('#ben_cash_amount').attr('required',false);
                        $('#ben_bank_amount').attr('required',true);
                        $('#bank_input').show();
                        $('#cash_input').hide();
                        $('input[name="salary_type"][value="Bank"]').prop('checked', 'checked');
                    }else{
                        $('#ben_cash_amount').attr('required',true);
                        $('#ben_bank_amount').attr('required',true);
                        $('input[name="salary_type"][value="Partial"]').prop('checked', 'checked');
                    }

                }else{
                    $('#ben_id').val('');
                    $('#ben_joining_salary').val('');
                    $('#ben_cash_amount').val('');
                    $('#ben_bank_amount').val('');
                    $('#ben_basic').val('');
                    $('#ben_house_rent').val(house);
                    $('#ben_medical').val(medical);
                    $('#ben_transport').val(trans);
                    $('#ben_food').val(food);
                }
            },
            error:function(xhr)
            {
                $.notify('No previous salary','error');
            }
        });
    }

    $('#ben_joining_salary').on('keyup', function(){
        var basic_percent= '{{ $structure->basic }}';
        var house= '{{ $structure->house_rent }}';
        var medical= '{{ $structure->medical }}';
        var trans= '{{ $structure->transport }}';
        var food= '{{ $structure->food }}';

        var salary= parseFloat($('#ben_joining_salary').val());
        var sub =parseFloat(medical)+parseFloat(trans)+parseFloat(food);
        if(salary>sub){
            var basic= parseFloat((salary-sub)/basic_percent).toFixed(2);
            $('#ben_basic').val(basic);
            var house= parseFloat(salary-sub-basic).toFixed(2);
            $('#ben_house_rent').val(house);           
        }else{
            $('#ben_basic').val('');
            $('#ben_house_rent').val(''); 
        }

    });

    $('#ben_cash_amount').on('keyup', function(){
        var salary= parseFloat($('#ben_joining_salary').val());
        
        if(isNaN(salary)){
            $.notify("Please enter Joining Salary first",'error');
            $('#ben_cash_amount').val('');
        }
        else{

            var cash= parseFloat($('#ben_cash_amount').val());
            
            if(((cash)>salary) || (cash<0))
            {
                $.notify('Cash Amount Can not be greater than Salary or Negative','error');
                $('#ben_cash_amount').val(salary.toFixed(2));
                $('#ben_bank_amount').val(0);
            }
            else{
                var bank= salary-cash;
                $('#ben_bank_amount').val(bank.toFixed(2));
            }
            
        }
    });

    $('#ben_bank_amount').on('keyup', function(){
        var salary= parseFloat($('#ben_joining_salary').val()).toFixed(2); 
        
        if(isNaN(salary)){
            $.notify("Please enter Joining Salary first",'error');
            $('#ben_bank_amount').val('');
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

    $('#ben_joining_salary').on('keyup', function(){
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
                $.notify("Cash Amount Can not be greater than Salary or Negative",'error');
                $('#ben_cash_amount_fixed').val(salary.toFixed(2));
                $('#ben_bank_amount_fixed').val(0);
            }
            else{
                var bank= salary-cash;
                $('#ben_bank_amount_fixed').val(bank.toFixed(2));
            }
            
        }
    });

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
       
        if(total_check_a==0){
            $.notify("Invalid Salary Calculation");  
            e.preventDefault();
        }
    });


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
@endpush
@endsection