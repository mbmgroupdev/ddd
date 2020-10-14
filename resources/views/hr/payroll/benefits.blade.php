@extends('hr.layout')
@section('title', 'End of Job')
@section('main-content')
@push('css')
    <style type="text/css">
        input[readonly] {
            color: black !important;
            background: azure !important;
            cursor: default !important;
        }
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
                <li class="active">End of Job </li>
                <li class="top-nav-btn"><a href="{{url('hr/payroll/given_benefits_list')}}" target="_blank" class="btn btn-sm btn-primary" >Benefit List <i class="fa fa-list bigger-120"></i></a></li>
            </ul><!-- /.breadcrumb --> 
        </div>

        
        @include('inc/message')
        <div class="panel panel-success" style="">
            <div class="panel-body">
                {{Form::open(['url'=>'hr/payroll/benefits_save', 'class'=>'form-horizontal'])}}
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group has-required has-float-label emp select-search-group">
                                
                                {{ Form::select('associate',  [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates form-control', 'data-validation'=>'required']) }}
                                <label >Employee</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            
                            <div class="form-group has-required has-float-label select-search-group">
                                <select id="benefit_on" name="benefit_on" class="form-control" required="required">
                                   <option value="">Select Type</option>
                                   <option value="on_resign">Resign/Left</option>
                                   <option value="on_dismiss">Dismiss</option>
                                   <option value="on_terminate">Termination</option>
                                   <option value="on_death">Death</option>
                                   <option value="on_retirement">Retirement</option>
                               </select>  
                                <label for="benefit_on">Benefit Type</label>
                            </div>
                        </div>
                        <div  id="death_reason_div" class="col-sm-3" style="display: none;">
                            <div  class="form-group has-required has-float-label select-search-group" >
                                
                                <select  name="death_reason" class="form-control death_reason"  required="required">
                                   <option value="none">Select One</option>
                                   <option value="natural_death" >Natural Death on Duty</option>
                                   <option value="duty_accidental_death">On Duty/On Duty Accidental Death </option>
                               </select>
                                <label >Death Reason</label>
                            </div>
                        </div>
                        
                        
                        <div id="suspension_days_div" class="col-sm-2" style="display: none;">
                            <div class="form-group has-required has-float-label" >
                                
                                <input type="text" class="form-control" name="suspension_days" id="suspension_days" value="0" required="required">
                                <label >Suspension Days</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group has-float-label has-required">
                                <input id="status_date" type="date" name="status_date" value="{{date('Y-m-d')}}" class="form-control" required >
                                <label for="status_date">Status Date</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" id="pay_button"  disabled="disabled">Pay Benefits</button>
                        </div>
                    </div>
                    
                {{Form::close()}}
            </div>
        </div>
        <div class="panel panel-success" style="">
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-4">
                        
                        <div class="user-details-block" style="border-right: 1px solid #d1d1d1;padding-top: 1.5rem;">
                            <div class="user-profile text-center mt-0">
                                <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                            </div>
                            <div class="text-center mt-3">
                                <h4><b id="name">-------------</b></h4>
                                <p><span id="designation">-------------</span>, <b id="department">----------</b></p>
                            </div>
                            <div class="text-center">
                                <p class="mb-0"  id="unit">-----------------</b></p>
                            </div>
                            <div class="text-center">
                                <p class="mb-0">DOJ: <b id="doj">-------------</b></p>
                            </div>
                        </div>
                        <br>
                        <ul class="speciality-list m-0 p-0">
                            <li class="d-flex mb-4 align-items-center">
                               <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-dollar-sign"></i></a></div>
                               <div class="media-support-info ml-3">
                                  <h6>Salary</h6>
                                  <p class="mb-0">Gross:  <span class="text-danger" id="gross_salary">0</span> Basic: <span class="text-success" id="basic_salary">0</span></p>
                               </div>
                            </li>
                            <li class="d-flex mb-4 align-items-center">
                               <div class="user-img img-fluid"><a href="#" class="iq-bg-info"><i class="las f-18 la-database"></i></a></div>
                               <div class="media-support-info ml-3">
                                  <h6>Earned Leave</h6>
                                  <p class="mb-0">Total:  <span class="text-danger" id="total_earn_leave">0</span class="text-danger"> Enjoyed: <span class="text-warning" id="enjoyed_earn_leave">0</span > Remained: <span class="text-success" id="remained_earn_leave">0</span></p>
                               </div>
                            </li>
                            <li class="d-flex mb-4 align-items-center">
                               <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-history"></i></a></div>
                               <div class="media-support-info ml-3">
                                  <h6>Total Service</h6>
                                  <p id="total_service" class="mb-0">Total:  <span id="total_earn_leave"><span style="color: darkblue; font-weight: 800; padding: 5px; border-radius: 10px; padding-left: 0px;" id="service_Y">0</span>
                                       <span style="color: darkblue;">Year/s</span>
                                       <span style="color: forestgreen; font-weight: 800; padding: 5px; border-radius: 10px;" id="service_m">0</span>
                                       <span style="color: forestgreen;">Month/s</span>
                                       <span style="color: maroon; font-weight: 800; padding: 5px; border-radius: 10px;" id="service_d">0</span>
                                       <span style="color: maroon;">Day/s</span></p>
                               </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-8">
                        <div id="benefit-voucher"></div>
                    </div>
                </div>
            </div>
        </div>
           
    </div>
</div>

@push('js')
<script type="text/javascript">
    $(document).ready(function(){

        $('#benefit_on').on('change', function(){     

            var category = $(this).val();
            if(category == ''){
                $('#save_button').attr('disabled', 'disabled');
                $('#pay_button').prop('disabled', 'disabled');
            }
            else{
                $('#save_button').removeAttr('disabled');
                $('#pay_button').removeAttr('disabled');

                if(category == 'on_death'){
                    $('#death_reason_div').show();
                }else{
                    $('#death_reason_div').hide();
                }

                if(category == 'on_dismiss'){
                    $('#suspension_days_div').show();
                }else{
                    $('#suspension_days_div').hide();
                }
            }
        });


        $('#associate').on('change', function(){
            var emp_id = $(this).val();
            if(emp_id != ""){
                $('.app-loader').show();
                var url = '{{url('')}}';
                $.ajax({
                    url : "{{ url('hr/payroll/benefits/get_employee_details') }}",
                    type: 'get',
                    dataType : 'json',
                    data: { 
                        emp_id : emp_id
                    },
                    success: function(data)
                    {
                        $('#associate_id').text(data['associate_id']);
                        $('#oracle_id').text(data['as_oracle_code']);
                        $('#name').text(data['as_name']);
                        $('#unit').text(data['hr_unit_name']);
                        $('#department').text(data['hr_department_name']);
                        $('#designation').text(data['hr_designation_name']);
                        $('#doj').text(data['date_join']);
                        $('#avatar').attr('src', url+data['as_pic']); 
                        $('#service_Y').html(data['service_years']);
                        $('#service_m').html(data['service_months']);
                        $('#service_d').html(data['service_days']);

                        $('#gross_salary').text(data['ben_current_salary'] + " ৳");
                        $('#basic_salary').text(data['ben_basic'] + " ৳");
                        $('#total_earn_leave').text(data['earned']);
                        $('#enjoyed_earn_leave').text(data['enjoyed']);
                        $('#remained_earn_leave').text(data['remain']);
                        $('.app-loader').hide();
                    },
                    error: function(data)
                    {
                        $.notify('failed...','error');
                        $('.app-loader').hide();
                    }
                });
            }
            else{
                $('#associate_id').text('-------------');
                $('#oracle_id').text('-------------');
                $('#name').text('-------------');
                $('#unit').text('-----------------');
                $('#department').text('----------');
                $('#designation').text('-------------');
                $('#doj').text('-------------');
                $('#avatar').attr('src','/assets/images/user/09.jpg'); 
                $('#service_Y').html('0');
                $('#service_m').html('0');
                $('#service_d').html('0');
                $('#gross_salary').text('0');
                $('#basic_salary').text('0');
                $('#total_earn_leave').text('0');
                $('#enjoyed_earn_leave').text('0');
                $('#remained_earn_leave').text('0');
                $('.app-loader').hide();
            }

        });

        $(document).on('click','#pay_button', function()
        {
            $('.app-loader').show();
            $.ajax({
                url: '{{url('hr/payroll/save_benefit_data')}}',
                type: 'get',
                dataType: 'json',
                data:{
                    benefit_on      : $('#benefit_on').val(),
                    associate_id    : $('#associate').val(),
                    status_date     : $('#status_date').val(),
                    death_reason    : $('#death_reason').val(),
                    suspension_days : $('#suspension_days').val(),
                    notice_pay      : $('#notice_pay').val()

                },
                success: function(data){
                    $('#benefit-voucher').html(data.benefit);
                    $('.app-loader').hide();
                },
                error: function(data){
                }
            });


        });
    });

    function banglaDigit(digit){
        var bn_digit = "";
        str_digit = new String(digit);
        // console.log(str_digit.length);
        for(var i=0; i<str_digit.length; i++){
            if(str_digit[i] == "0"){bn_digit += "০";}
            else if(str_digit[i] == "1"){bn_digit += "১";}
            else if(str_digit[i] == "2"){bn_digit += "২";}
            else if(str_digit[i] == "3"){bn_digit += "৩";}
            else if(str_digit[i] == "4"){bn_digit += "৪";}
            else if(str_digit[i] == "5"){bn_digit += "৫";}
            else if(str_digit[i] == "6"){bn_digit += "৬";}
            else if(str_digit[i] == "7"){bn_digit += "৭";}
            else if(str_digit[i] == "8"){bn_digit += "৮";}
            else if(str_digit[i] == "9"){bn_digit += "৯";}
            else if(str_digit[i] == "."){bn_digit += ".";}
        }
        return bn_digit;
    }

    function banglaReason(reason){
        if(reason == 'on_resign'){
            return "ইস্তফা";
        }
        else if(reason == 'on_dismiss'){
            return "বরখাস্ত";
        }
        else if(reason == 'on_terminate'){
            return "অবসান";
        }
        else if(reason == 'on_death'){
            return "মৃত্যু";
        }

    }

    //making the all calculation hidden
    function makeAllCalculatedDivFieldsHidden(){
            $('#death_reason_div').attr('hidden', 'hidden');
            $('#not_eligible_show').attr('hidden', 'hidden');
            $('#suspension_days_div').attr('hidden', 'hidden');
            $('#earn_leave_div').attr('hidden', 'hidden');
            $('#service_benefit_div').attr('hidden', 'hidden');
            $('#subsistence_allowance_div').attr('hidden', 'hidden');
            $('#notice_pay_div').attr('hidden', 'hidden');
            $('#termination_benefit_div').attr('hidden', 'hidden');
            $('#natural_death_benefit_div').attr('hidden', 'hidden');
            $('#on_duty_and_accidental_death_on_duty_div').attr('hidden', 'hidden');

            $('#loader').attr('hidden', 'hidden');
            $('#already_saved_data').attr('hidden', 'hidden');
    }

    function clearReadonlyFields(){
        $('#earn_leave_due').val(0);
        $('#service_benefit').val(0);
        $('#subsistence_allowance').val(0);
        $('#notice_pay').val(0);
        $('#termination_benefit').val(0);
        $('#natural_death_benefit').val(0);
        $('#on_duty_and_accidental_death_on_duty').val(0);
        $('#total_benefit_amount').val(0);
    }

    //Showing Fileds Category Wise..
    function showCategoryWiseBenefitFelids(category){
            if(category == 'on_resign'){
                $('#earn_leave_div').removeAttr('hidden');
                $('#service_benefit_div').removeAttr('hidden');
            }else if(category == 'on_dismiss'){
                $('#earn_leave_div').removeAttr('hidden');
                $('#service_benefit_div').removeAttr('hidden');
                $('#subsistence_allowance_div').removeAttr('hidden');
                $('#suspension_days_div').removeAttr('hidden');

            }else if(category == 'on_terminate'){
                $('#earn_leave_div').removeAttr('hidden');
                $('#notice_pay_div').removeAttr('hidden');
                $('#termination_benefit_div').removeAttr('hidden');

            }else if(category == 'on_death'){
                $('#death_reason_div').removeAttr('hidden');
                $('#earn_leave_div').removeAttr('hidden');
                $('#service_benefit_div').removeAttr('hidden');

                if($('#death_reason').val() == 'none'){
                    $('.emp').attr('style', 'pointer-events:none;');
                    $('#on_duty_and_accidental_death_on_duty_div').attr('hidden', 'hidden');
                    $('#natural_death_benefit_div').attr('hidden', 'hidden');
                }
                else if($('#death_reason').val() == 'natural_death'){
                    $('.emp').removeAttr('style');
                    $('#natural_death_benefit_div').removeAttr('hidden');
                    $('#on_duty_and_accidental_death_on_duty_div').attr('hidden', 'hidden');
                }else if($('#death_reason').val() == 'duty_accidental_death'){
                    $('.emp').removeAttr('style');
                    $('#on_duty_and_accidental_death_on_duty_div').removeAttr('hidden');
                    $('#natural_death_benefit_div').attr('hidden', 'hidden');
                }
            }    
    }

    function globalEarnLeaveCalculation(gross, el_days){
        return Math.round((gross/30.0)*el_days, 2);
    }

    function calculateAllBenefits(category, emp_details){
        // console.log(category, emp_details);
        var gross_salary     = emp_details['ben_current_salary'];
        var service_years    = emp_details['service_years'];
        var service_months   = emp_details['service_months'];
        var basic            = emp_details['ben_basic'];
        var remain_earn_days = emp_details['total_earnedLeaves_details']['total_remain'];


        //All global vaiables...
        var earn_amount = 0.0;
        var service_benefits = 0.0;
        var subsistence_allowance = 0.0;
        var notice_pay           = 0.0;
        var termination_benefits = 0.0;
        var natural_death_benefit = 0.0; 
        var on_duty_accidental_death_benefit = 0.0;
        var grand_total = 0.0;

        //earn amount is same in all conditions 
        earn_amount = globalEarnLeaveCalculation(gross_salary, remain_earn_days);
        
        //***Category wise benefits calculations***//------------------------------------------
        
        if(category == 'on_resign'){  //Resign Benefits Calculation....
            if(service_years < 5){
                service_benefits = 0.0;
                grand_total = earn_amount+service_benefits;
            }
            else if(service_years >= 5 && service_years < 10){
                //IF above or equal 240 days service on last year
                if(service_months >= 8){
                    service_benefits = Math.round((basic/30)*14*(service_years+1));
                }
                else{
                    service_benefits = Math.round((basic/30)*14*service_years);
                }

                grand_total = earn_amount+service_benefits;
            }
            else{
                //IF above or equal 240 days service on last year
                if(service_months >= 8){
                    service_benefits = Math.round(basic*(service_years+1));
                }
                else{
                    service_benefits = Math.round(basic*service_years);
                }

                grand_total = earn_amount+service_benefits;
            }      
        }
        else if(category == 'on_dismiss'){ //Dismiss Benefits Calculation...
            var suspension_days = parseInt($('#suspension_days').val());
            
            subsistence_allowance = Math.round( ((gross_salary/30)*suspension_days)/2, 2); 
            if(service_years < 5){
                service_benefits      = Math.round(((basic/30.0)*15)*service_years, 2);
                grand_total = earn_amount+service_benefits+subsistence_allowance;
            }
            else if(service_years >= 5 && service_years < 10){
                //IF above or equal 240 days service on last year
                if(service_months >= 8){
                    service_benefits      = Math.round(((basic/30.0)*15)*(service_years+1), 2);
                }
                else{
                    service_benefits      = Math.round(((basic/30.0)*15)*service_years, 2);
                }
                
                grand_total = earn_amount+service_benefits+subsistence_allowance;
            }
            else{
                //IF above or equal 240 days service on last year
                if(service_months >= 8){
                    service_benefits      = Math.round(((basic/30.0)*15)*(service_years+1), 2);
                }
                else{
                    service_benefits      = Math.round(((basic/30.0)*15)*service_years, 2);
                }
                
                grand_total = earn_amount+service_benefits+subsistence_allowance;
            }
            
        }
        else if(category == 'on_terminate'){ //Termination Benefits Calculation....
            if(service_years < 5){
                notice_pay           = Math.round( (basic/30)*120, 2);
                termination_benefits = Math.round( (basic)*service_years, 2);
                grand_total = earn_amount+notice_pay+termination_benefits;
            }
            else if(service_years >= 5 && service_years < 10){
                //IF above or equal 240 days service on last year
                if(service_months >= 8){
                    termination_benefits = Math.round( (basic)*(service_years+1), 2);
                }
                else{
                    termination_benefits = Math.round( (basic)*service_years, 2);
                }

                notice_pay           = Math.round( (basic/30)*120, 2);
                grand_total = earn_amount+notice_pay+termination_benefits;   
            }
            else{
                //IF above or equal 240 days service on last year
                if(service_months >= 8){
                    termination_benefits = Math.round( (basic)*(service_years+1), 2);
                }
                else{
                    termination_benefits = Math.round( (basic)*service_years, 2);
                }
                
                notice_pay  = Math.round( (basic/30)*120, 2);
                
                grand_total = earn_amount+notice_pay+termination_benefits;
            }
           

        }
        else if(category == 'on_death'){
            if(service_years < 5){

                service_benefits = 0.0;
                
                if(service_years >= 2){
                    if($('.death_reason').val() == 'natural_death'){
                        natural_death_benefit = naturalDeathBenefitCalculation(service_years, service_months, basic);
                    }
                    else if($('.death_reason').val() == 'duty_accidental_death'){
                        custom_basic = Math.round(basic+((basic/30)*15), 2); //45 days basic
                        on_duty_accidental_death_benefit = accidentalDeathBenefitCalculation(service_years, service_months, custom_basic);
                    }
                }

                grand_total = earn_amount+service_benefits+natural_death_benefit+on_duty_accidental_death_benefit;

            }
            else if(service_years >= 5 && service_years < 10){

                if(service_months >= 8){
                    service_benefits = Math.round((basic/30)*14*(service_years+1), 2);
                }
                else{
                    service_benefits = Math.round((basic/30)*14*service_years, 2);
                }

                //Death Benefits
                if($('.death_reason').val() == 'natural_death'){
                    natural_death_benefit = naturalDeathBenefitCalculation(service_years, service_months, basic);
                }
                else if($('.death_reason').val() == 'duty_accidental_death'){
                    custom_basic = Math.round(basic+((basic/30)*15), 2); //45 days basic
                    on_duty_accidental_death_benefit = accidentalDeathBenefitCalculation(service_years, service_months, custom_basic);  
                }

                grand_total = earn_amount+service_benefits+natural_death_benefit+on_duty_accidental_death_benefit;
            }
            else{

                if(service_months >= 8){
                    service_benefits = Math.round((basic/30)*14*(service_years+1), 2);
                }
                else{
                    service_benefits = Math.round((basic/30)*14*service_years, 2);
                }

                //Death Benefits
                if($('.death_reason').val() == 'natural_death'){
                    natural_death_benefit = naturalDeathBenefitCalculation(service_years, service_months, basic);
                }
                else if($('.death_reason').val() == 'duty_accidental_death'){
                    custom_basic = Math.round(basic+((basic/30)*15), 2); //45 days basic
                    on_duty_accidental_death_benefit = accidentalDeathBenefitCalculation(service_years, service_months, custom_basic);  
                }

                grand_total = earn_amount+service_benefits+natural_death_benefit+on_duty_accidental_death_benefit;

            }

        }


        //Putting the values on screen
        $('#earn_leave_due').val(earn_amount);       
        $('#service_benefit').val(service_benefits);       
        $('#subsistence_allowance').val(subsistence_allowance);
        $('#notice_pay').val(notice_pay);
        $('#termination_benefit').val(termination_benefits);
        $('#natural_death_benefit').val(natural_death_benefit);
        $('#on_duty_and_accidental_death_on_duty').val(on_duty_accidental_death_benefit);
        $('#total_benefit_amount').val(grand_total);
        
        //on voucher assignments...       
        $('#earn_leave_print_value').text(banglaDigit(earn_amount) + " ৳");       
        $('#service_benefit_print_value').text(banglaDigit(service_benefits) + " ৳");
        $('#subsistence_allowance_print_value').text(banglaDigit(subsistence_allowance) + " ৳");     
        $('#notice_pay_print_value').text(banglaDigit(notice_pay) + " ৳");
        $('#termination_benefit_print_value').text(banglaDigit(termination_benefits) + " ৳");
        $('#natural_death_print_value').text(banglaDigit(natural_death_benefit) + " ৳");
        $('#on_duty_and_acci_death_print_value').text(banglaDigit(on_duty_accidental_death_benefit) + " ৳");
        $('#grand_toal_print_value').text(banglaDigit(grand_total) + " ৳");

    }
    function naturalDeathBenefitCalculation(service_years, service_months, basic){
        // console.log(service_years, service_months, basic);
        if(service_months >= 4 && service_months < 6){
            natural_death_benefit = Math.round(basic*(service_years+0.5), 2);
        }
        else if(service_months >= 6){
            natural_death_benefit = Math.round(basic*(service_years+1), 2);
        }
        else{
            natural_death_benefit = Math.round(basic*service_years, 2);   
        }
        
        return natural_death_benefit;
    }
    function accidentalDeathBenefitCalculation(service_years, service_months, custom_basic){
        // console.log(service_years, service_months, custom_basic);
        if(service_months >= 4 && service_months < 6){
            on_duty_accidental_death_benefit = Math.round(custom_basic*(service_years+0.5), 2);
        }
        else if(service_months >= 6){
            on_duty_accidental_death_benefit = Math.round(custom_basic*(service_years+1), 2);
        }
        else{
            on_duty_accidental_death_benefit = Math.round(custom_basic*service_years, 2);      
        }
        
        return on_duty_accidental_death_benefit;
    }

   


    $(function(){
        $('body').on('click', '.printVoucher', function(){
            setTimeout(function(){
                // $('#printDiv')
                // var divToPrint = document.getElementById("print_div").innerHTML;
                var divToPrint = $(".print_div")[0].innerHTML;
                // console.log(divToPrint);
                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">'+divToPrint+'</body></html>');
                newWin.document.close();
                setTimeout(function(){newWin.close();},10);
            },500);
        });
    });
</script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
@endpush
@endsection