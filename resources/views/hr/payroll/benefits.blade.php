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
        .custom-control-label:after, .custom-control-label:before {zoom:1.5;top: 2px;}
        .benefit-voucher .iq-card-body{
            padding: 0 !important;
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
            </ul>
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
                                   <option value="on_left">Left</option>
                                   <option value="on_resign">Resign</option>
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
                                
                                <select id="death_reason"  name="death_reason" class="form-control death_reason"  required="required">
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
                            <div class="form-group has-float-label has-required" data-toggle="tooltip" data-placement="top" title="" data-original-title="Employee salary will be calculated based on this date! Date suggestion will be the last working day (If last leave date greater than attendance date then last leave date). If any holiday after last attendance date than that holiday will be last working day.">
                                <input id="status_date" type="date" name="status_date" value="{{date('Y-m-d')}}" class="form-control" required  >
                                <label for="status_date">Effective Date</label>
                            </div>
                        </div>
                        <div id="notice_pay_div" class="col-sm-2 pt-2" style="display: none;">
                            <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                              <input type="checkbox" class="custom-control-input bg-primary pt-1" id="notice_pay" value="1">
                              <label class="custom-control-label" style="font-size: 14px;" for="notice_pay"> Notice Pay</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" id="pay_button"  disabled="disabled">Proceed</button>
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
                    <div class="col-sm-8 p-0">
                        <div id="benefit-voucher"></div>
                        <hr>
                        <div id="salary-voucher"></div>
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

                if(category == 'on_resign' || category == 'on_terminate'){
                    $('#notice_pay_div').show();
                }else{
                    $('#notice_pay_div').hide();
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
                        


                        if(data['already_given'] == 'yes'){
                            $('#benefit-voucher').html(data['benefit']);

                        }else{
                            $('#benefit-voucher').html(data['jobcard']);
                        }
                        $('.app-loader').hide();
                    },
                    error: function(data)
                    {
                        $.notify('failed...','error');
                        $('#benefit-voucher').html('');
                        $('#salary-voucher').html('');
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
                $('#benefit-voucher').html('');
                $('#salary-voucher').html('');
                $('.app-loader').hide();
            }

        });

        $(document).on('click','#pay_button', function()
        {
            
            $('.app-loader').show();
            var notice_pay = 0;
            if ($('input#notice_pay').is(':checked')) {
                notice_pay = 1;
            }
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
                    notice_pay      : notice_pay

                },
                success: function(data){
                    $('#benefit-voucher').html(data.benefit);
                    $('#salary-voucher').html(data.salary);
                    $('.app-loader').hide();
                },
                error: function(data){
                }
            });


        });
    });

  

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
@endpush
@endsection