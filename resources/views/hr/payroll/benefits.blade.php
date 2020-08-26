@extends('hr.layout')
@section('title', 'End of Job Benefits')
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
                <li class="active">End of Job Benefits </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        
        @include('inc/message')
        <div class="panel panel-success" style="">
            <div class="panel-heading page-headline-bar">
                <h6>
                    End of Job Benefits
                    <a href="{{url('hr/payroll/given_benefits_list')}}" target="_blank" class="btn btn-primary pull-right" >Benefit List <i class="fa fa-list bigger-120"></i></a>
                </h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-4">
                        {{Form::open(['url'=>'hr/payroll/benefits_save', 'class'=>'form-horizontal'])}}
                            <div class="row">
                                <div class="col-12">
                                    
                                    <div class="form-group has-required has-float-label select-search-group">
                                        <select id="benefit_on" name="benefit_on" class="form-control" required="required">
                                           <option value="">Select Type</option>
                                           <option value="on_resign">Resign Benefits</option>
                                           <option value="on_dismiss">Dismiss Benefits</option>
                                           <option value="on_terminate">Termination Benefits</option>
                                           <option value="on_death">Death Benefits</option>
                                           <option value="on_resign">Retirement Benefits</option>
                                       </select>  
                                        <label>Benefit Type</label>
                                    </div>
                                </div>
                                
                                
                                

                                <div  id="death_reason_div" class="col-12">
                                    <div  class="form-group has-required has-float-label select-search-group" >
                                        
                                        <select  name="death_reason" class="form-control death_reason"  required="required">
                                           <option value="none">Select One</option>
                                           <option value="natural_death" >Natural Death on Duty</option>
                                           <option value="duty_accidental_death">On Duty/On Duty Accidental Death </option>
                                       </select>
                                        <label >Death Reason</label>
                                    </div>
                                </div>
                                <div id="suspension_days_div" class="col-12">
                                    <div class="form-group has-required has-float-label" >
                                        
                                        <input type="text" class="form-control" name="suspension_days" id="suspension_days" value="0" required="required">
                                        <label >Suspension Days</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group has-required has-float-label emp select-search-group" style="pointer-events: none;">
                                        
                                        {{ Form::select('associate',  [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates form-control', 'data-validation'=>'required']) }}
                                        <label >Employee</label>
                                        
                                    </div>
                                </div>
                                <div id="not_eligible_show" class="col-12">
                                    <div class="form-group" >
                                        <label style="color: red;">Sorry Not Eligible (Service < 1 Year)</label>
                                    </div>
                                </div>
                                
                                <div id="subsistence_allowance_div" class="col-12">
                                    <div class="form-group  has-float-label" >
                                        <input class="form-control" type="text" name="subsistence_allowance" id="subsistence_allowance" readonly="readonly">
                                        <label>Subsistence Allowance</label>
                                    </div>
                                </div>
                                <div id="notice_pay_div" class="col-12">
                                    <div class="form-group  has-float-label" >
                                        <input class="form-control" type="text" name="notice_pay" id="notice_pay" readonly="readonly">
                                        <label>Notice Pay</label>
                                        
                                    </div>
                                </div>
                                <div id="termination_benefit_div" class="col-12">
                                    <div class="form-group  has-float-label" >
                                        
                                        <input class="form-control" type="text" name="termination_benefit" id="termination_benefit" readonly="readonly">
                                        <label>Termination Benefit</label>
                                    </div>
                                </div>
                                <div id="natural_death_benefit_div" class="col-12">
                                    <div class="form-group  has-float-label" >
                                        
                                        <input class="form-control" type="text" name="natural_death_benefit" id="natural_death_benefit" readonly="readonly">
                                        <label>Natural Death Benefits</label>
                                    </div>
                                </div>
                                <div id="on_duty_and_accidental_death_on_duty_div" class="col-12">
                                    <div class="form-group  has-float-label" >
                                        
                                        <input class="form-control" type="text" name="on_duty_and_accidental_death_on_duty" id="on_duty_and_accidental_death_on_duty" readonly="readonly">
                                        <label>On Duty and Accidental Death On Duty</label>
                                    </div>

                                </div>
                                
                            </div>
                            <div class="row">
                                <div id="earn_leave_div" class="col-12">
                                    <div class="form-group  has-float-label" >
                                        
                                        <input class="form-control" type="text" name="earn_leave_due" id="earn_leave_due" readonly="readonly">
                                        <label>Earn Leave</label>
                                    </div>
                                </div>
                                <div id="service_benefit_div" class="col-12">
                                    <div class="form-group  has-float-label" >
                                        <input class="form-control" type="text" name="service_benefit" id="service_benefit" readonly="readonly">
                                        <label>Service Benefits</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div id="total_benefit_amount_div" class="col-12">
                                    <div class="form-group  has-float-label" >
                                        
                                        <input class="form-control" type="text" name="total_benefit_amount" id="total_benefit_amount" readonly="readonly" style="background-color: antiquewhite !important;" value="0">
                                        <label>Total Amount</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" id="pay_button"  disabled="disabled">Pay Benefits</button>
                                </div>
                            </div>
                            
                        {{Form::close()}}
                    </div>
                    <div class="col-8">
                        <div class=" panel-info" id="basic_info_div">
                            <div class="panel-body">
                                <div class="row">
                                    
                                    <div class="col-6">
                                        
                                        <div class="user-details-block" style="border-left: 1px solid #d1d1d1;">
                                            <div class="user-profile text-center mt-0">
                                                <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                            </div>
                                            <div class="text-center mt-3">
                                             <h4><b id="name">-------------</b></h4>
                                             <p class="mb-0" id="designation">
                                                --------------------------</p>
                                             <p class="mb-0" >
                                                Oracle ID: <span id="oracle_id" class="text-success">-------------</span>
                                             </p>
                                             <p class="mb-0" >
                                                Associate ID: <span id="associate_id" class="text-success">-------------</span>
                                             </p>
                                             <p  class="mb-0">Department: <span id="department" class="text-success">------------------------</span> </p>
                                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <ul class="speciality-list m-0 p-0">
                                            <li class="d-flex mb-4 align-items-center">
                                               <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-city"></i></a></div>
                                               <div class="media-support-info ml-3">
                                                  <h6>Unit</h6>
                                                  <p id="unit" class="mb-0">------------------------</p>
                                               </div>
                                            </li>
                                            <li class="d-flex mb-4 align-items-center">
                                               <div class="user-img img-fluid"><a href="#" class="iq-bg-info"><i class="las f-18 la-calendar-day"></i></a></div>
                                               <div class="media-support-info ml-3">
                                                  <h6>Date of Joining</h6>
                                                  <p id="doj" class="mb-0">------------------------</p>
                                               </div>
                                            </li>
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
                                                  <p class="mb-0">Total:  <span class="text-danger" id="total_earn_leave">0</span class="text-danger"> Enjoyed: <span class="text-warning" id="enjoyed_earn_leave">0</span > <br>Remained: <span class="text-success" id="remained_earn_leave">0</span></p>
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
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-success" id="voucher" hidden="hidden">
            <div class="panel-heading">
                <h6>Voucher
                    <button class="btn  btn-danger pull-right printVoucher" style="border-radius: 2px;" data-tooltip="Print" Data-tooltip-location="left"><i class="fa fa-print"></i> Print</button>
                </h6>
            </div>
            <div class="panel-body">
                <div class="col-sm-12 print_div" style="border:1px solid grey;"  id="print_div">
                    <h1 style="text-align: center; color: forestgreen;" id="unit_print"></h1>
                    <h5 style="text-align: center; " id="unit_addr_print"></h5>
                    <h5 class="pull-right" style="margin-left: 80%;">তারিখঃ<?php
                        echo eng_to_bn(date('d-m-Y'));

                    ?></h5>


                    <div style="margin-left: 40px; margin-top: 60px;">
                        <div style="margin-left: 40px; padding: 0px;" id="already_saved_data" hidden="hidden">
                            <span style="margin-left: 90%; font-weight: 800; color:darkgrey; font-size: 16px;">COPY</span>
                        </div>
                        
                        <h5 style="margin-left: 10%;">অব্যাহতীকালীন সুযোগ-সুবিধার হিসাব-</h5>

                        <table style="border: none; margin-left: 10%; width: 60%; font-size: 11px;">
                            <tbody>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">নামঃ</th>
                                    <th style="padding:2px; text-align: left; width: 60%;" id="emp_name_print">  <br></th>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">পদবীঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_deg_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">ডিপার্টমেন্টঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_dep_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">আইডি নংঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_ass_id_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">মূল বেতনঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_basic_sal_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">মোট বেতনঃ</th>
                                    <td style="padding:2px; width: 60%;" id="emp_current_sal_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">চাকুরীর মোট সময়কালঃ</th>
                                    <td style="padding:2px; width: 60%;" id="total_service_days_print">  <br></td>
                                </tr>
                                <tr>
                                    <th style="padding:2px; text-align: left; width: 30%;">অব্যাহতীর কারনঃ</th>
                                    <td style="padding:2px; width: 60%;" id="reason_print">  <br></td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="margin:0px; padding:0px;" >
                            <h5 style="margin-top: 10px; margin-left: 10%; text-decoration: underline;">প্রদেয় সুযোগ-সুবিধা সমুহ ও পাওনাদিঃ</h5>
                            <table style="border: 1px solid darkgrey; margin-left: 10%; width: 60%; border-collapse: collapse; font-size: 11px; ">
                                <thead>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;">
                                        <th style="border: 1px solid darkgrey; padding: 5px;  text-align: left; width: 40%;  padding-left: 30px;">সুযোগ-সুবিধা সমুহ </th>
                                        <th style="border: 1px solid darkgrey; padding: 5px;  text-align: center;  padding-left: 30px;">টাকার পরিমান</th>
                                    </tr>
                                </thead>
                                <tbody id="the_payble_body_print">
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="earn_leave_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            আহরিত ছুটির হিসাব বাবদ
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px; text-align: right;" id="earn_leave_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="service_benefit_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            সেবা বাবদ     
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right" id="service_benefit_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="subsistence_allowance_row_print" {{-- hidden="hidden" --}}>
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            জীবিকা ভাতা বাবদ
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right" id="subsistence_allowance_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="notice_pay_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            নোটিশ পে বাবদ
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right" id="notice_pay_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="termination_benefit_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            অবসান সুবিধা বাবদ
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right;" id="termination_benefit_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" id="natural_death_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            স্বাভাবিক মৃত্যু
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right;" id="natural_death_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;" 
                                                    id="on_duty_and_accidental_death_row_print">
                                        <td style="border: 1px solid darkgrey; padding: 5px; padding-left: 30px;">
                                            কর্তব্যরত অবস্থায় এবং দুর্ঘটনায় মৃত্যু
                                        </td>
                                        <td style="border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;text-align: right;" id="on_duty_and_acci_death_print_value">
                                                    ৳
                                        </td>
                                    </tr>
                                    <tr style="border: 1px solid darkgrey; padding: 5px;">
                                        <th style="border: 1px solid darkgrey; padding: 5px; text-align: right; color: maroon;">মোট</th>
                                        <th style="border: 1px solid darkgrey; padding: 5px; text-align: left; color: maroon;  padding-left: 30px;text-align: right;" id="grand_toal_print_value"> ৳</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        

                        <table style="  width: 100%; margin-top: 20%; margin-bottom: 40px; font-size: 10px;">
                                <tr style=" padding: 5px;">
                                    <td style=" padding: 4px;">
                                        প্রস্তুতকারী
                                    </td>
                                    <td style=" padding: 4px;">
                                        হিসাব বিভাগ
                                    </td>
                                    <td style=" padding: 4px;">
                                        সহঃ ব্যবস্থাপক <br> প্রশাসন, মানবসম্পদ ও কমপ্লাইন্স
                                    </td>
                                    <td style=" padding: 4px;">
                                        সহঃ মহাব্যবস্থাপক <br> প্রশাসন, মানবসম্পদ ও কমপ্লাইন্স
                                    </td>
                                    <td style=" padding: 4px;">
                                        এভিপি <br> প্রশাসন, মানবসম্পদ ও কমপ্লাইন্স
                                    </td>
                                </tr>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
           
    </div>
</div>

@push('js')
<script type="text/javascript">
    $(document).ready(function(){
        makeAllCalculatedDivFieldsHidden();

        $('#benefit_on').on('change', function(){
            makeAllCalculatedDivFieldsHidden();
            clearReadonlyFields();
            $('#associate').val('').change();      

            var category = $(this).val();
            if(category == ''){
                $('.emp').attr('style', 'pointer-events:none;');
                $('#voucher').attr('hidden', 'hidden');
                $('#save_button').attr('disabled', 'disabled');
                $('#pay_button').prop('disabled', 'disabled');
                categoryWisePrintSectionHide(category);
            }
            else{
                //Show Relative Fields according on benefits category
                showCategoryWiseBenefitFelids(category);
                categoryWisePrintSectionHide(category);

                $('.emp').removeAttr('style');
                $('#save_button').removeAttr('disabled');
                $('#pay_button').removeAttr('disabled');

                if(category == 'on_death'){
                    $('.emp').attr('style', 'pointer-events:none;');
                    $('.death_reason').val("none").change();
                }
            }
        });

        $('#suspension_days').on('keyup', function(){
            clearReadonlyFields();
            $('#associate').val('').change();
        });

        $('#death_reason').on('change', function(){
            clearReadonlyFields();
            $('#associate').val('').change();

            var death_reason = $(this).val();
            // $('#on_duty_and_accidental_death_on_duty_div').attr('hidden', 'hidden');
            // $('#natural_death_benefit_div').attr('hidden', 'hidden');
            
            if(death_reason == 'none'){
                $('.emp').attr('style', 'pointer-events:none;');
                $('#on_duty_and_accidental_death_on_duty_div').attr('hidden', 'hidden');
                $('#natural_death_benefit_div').attr('hidden', 'hidden');
            }
            else if(death_reason == 'natural_death'){
                $('.emp').removeAttr('style');
                $('#natural_death_benefit_div').removeAttr('hidden');
                $('#on_duty_and_accidental_death_on_duty_div').attr('hidden', 'hidden');
            }else if(death_reason == 'duty_accidental_death'){
                $('.emp').removeAttr('style');
                $('#on_duty_and_accidental_death_on_duty_div').removeAttr('hidden');
                $('#natural_death_benefit_div').attr('hidden', 'hidden');
            }
        }).change();

        function formatState (state) {
            //console.log(state.element);
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "/user/pages/images/flags";
            var $state = $(
            '<span><img /> <span></span></span>'
            );
            // Use .text() instead of HTML string concatenation to avoid script injection issues
            var targetName = state.name;
            $state.find("span").text(targetName);
            // $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".png");
            return $state;
        };

        $('select.associates').select2({
            templateSelection:formatState,
            placeholder: 'Select Associate\'s ID',
            ajax: {
                url: '{{ url("hr/associate-search-only-active") }}',
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
                                text: $("<span><img src='"+(item.as_pic ==null?'/assets/images/avatars/profile-pic.jpg':item.as_pic)+"' height='50px' width='auto'/> " + item.associate_name + "</span>"),
                                id: item.associate_id,
                                name: item.associate_name
                            }
                        }) 
                    };
              },
              cache: true
            }
        });
        

        $('#associate').on('change', function(){
            var emp_id = $(this).val();
            $('#voucher').attr('hidden', 'hidden');
            clearReadonlyFields();
             // var yr_of_service = $('#year_of_service').val();
                //ajax call to check the existance of emp_type and service year.
                // console.log(emp_type_id,yr_of_service );
            
            if(emp_id != ""){
                var url = '{{url('')}}';
                // console.log(url);
                $.ajax({
                    url : "{{ url('hr/payroll/benefits/get_employee_details') }}",
                    type: 'get',
                    dataType : 'json',
                    data: { 
                        emp_id : emp_id
                    },
                    success: function(data)
                    {
                        // console.log(data);
                        $('#associate_id').text(data['associate_id']);
                        $('#oracle_id').text(data['as_oracle_code']);
                        $('#name').text(data['as_name']);
                        $('#unit').text(data['hr_unit_name']);
                        // $('#location').text(data['hr_location_name']);
                        $('#department').text(data['hr_department_name']);
                        $('#designation').text(data['hr_designation_name']);
                        $('#doj').text(data['as_doj']);
                        if(data['as_pic'] == null){
                            if(data['as_gender'] == 'Male'){
                                $('#avatar').attr('src',url+'/assets/images/user/09.jpg');   
                            }
                            else{
                                $('#avatar').attr('src',url+'/assets/images/user/1.jpg');   
                            }
                        }
                        else{
                            $('#avatar').attr('src', url+data['as_pic']);   
                        }
                        $('#service_Y').html(data['service_years']);
                        $('#service_m').html(data['service_months']);
                        $('#service_d').html(data['service_days']);

                        $('#gross_salary').text(data['ben_current_salary'] + " ৳");
                        $('#basic_salary').text(data['ben_basic'] + " ৳");
                        $('#total_earn_leave').text(data['total_earnedLeaves_details']['total_earned'] + ' Day/s');
                        $('#enjoyed_earn_leave').text(data['total_earnedLeaves_details']['total_enjoy'] + ' Day/s');
                        $('#remained_earn_leave').text(data['total_earnedLeaves_details']['total_remain'] + ' Day/s');


                        //printing values assign..
                        $('#unit_print').text(data['hr_unit_name_bn']);
                        $('#unit_addr_print').text(data['hr_unit_address_bn']);
                        $('#emp_name_print').text(data['hr_bn_associate_name']);
                        $('#emp_deg_print').text(data['hr_designation_name_bn']);
                        $('#emp_dep_print').text(data['hr_department_name_bn']);
                        $('#emp_ass_id_print').text(data['associate_id']);
                        $('#emp_basic_sal_print').text(banglaDigit(data['ben_basic'])+ " ৳");
                        $('#emp_current_sal_print').text(banglaDigit(data['ben_current_salary'])+ " ৳");
                        $('#total_service_days_print').text(banglaDigit(data['service_years'])+" বছর - "+banglaDigit(data['service_months'])+" মাস - "+banglaDigit(data['service_days'])+" দিন" );
                        $('#reason_print').text(banglaReason($('#benefit_on').val()) );

                        //----------------------------------------------------------------------

                        //showing the calculated benefits
                        if(data['service_years'] == 0){
                            makeAllCalculatedDivFieldsHidden();
                            $('#not_eligible_show').removeAttr('hidden');
                            $('#pay_button').attr('disabled', 'disabled');
                        }
                        else{
                            makeAllCalculatedDivFieldsHidden();
                            $('#not_eligible_show').attr('hidden', 'hidden');

                            var category = $('#benefit_on').val();
                            var Sv_year  = data['service_years'];
                            var Sv_month = data['service_months'];

                            showCategoryWiseBenefitFelids(category);
                            calculateAllBenefits(category, data);

                            $('#pay_button').removeAttr('disabled');
                        }

                        //If data already saved in database......    
                        if(data['already_given'] == "yes"){
                            makeAllCalculatedDivFieldsHidden();
                            clearReadonlyFields();
                            $('#already_saved_data').removeAttr('hidden');
                            $('#pay_button').attr('disabled', 'disabled');
                            $('#voucher').removeAttr('hidden');

                            $('#reason_print').text(banglaReason(data['given_benefit_data'].benefit_on) );
                            $('#earn_leave_print_value').text(banglaDigit(data['given_benefit_data'].earn_leave_amount) + " ৳");       
                            $('#service_benefit_print_value').text(banglaDigit(data['given_benefit_data'].service_benefits) + " ৳");
                            $('#subsistence_allowance_print_value').text(banglaDigit(data['given_benefit_data'].subsistance_allowance) + " ৳");     
                            $('#notice_pay_print_value').text(banglaDigit(data['given_benefit_data'].notice_pay) + " ৳");
                            $('#termination_benefit_print_value').text(banglaDigit(data['given_benefit_data'].termination_benefits) + " ৳");
                            $('#natural_death_print_value').text(banglaDigit(data['given_benefit_data'].natural_death_benefits) + " ৳");
                            $('#on_duty_and_acci_death_print_value').text(banglaDigit(data['given_benefit_data'].on_duty_accidental_death_benefits) + " ৳");

                            var grand_total =   data['given_benefit_data'].earn_leave_amount+
                                                data['given_benefit_data'].service_benefits+
                                                data['given_benefit_data'].subsistance_allowance+
                                                data['given_benefit_data'].notice_pay+
                                                data['given_benefit_data'].termination_benefits+
                                                data['given_benefit_data'].natural_death_benefits+
                                                data['given_benefit_data'].on_duty_accidental_death_benefits;
                            $('#grand_toal_print_value').text(banglaDigit(grand_total) + " ৳");

                            //Scrolling to portion
                            $('html,body').animate({
                                scrollTop: $("#voucher").offset().top},
                                'slow');
                        } 
                    },
                    error: function(data)
                    {
                        alert('failed...');
                    }
                });
            }
            else{
                $('#associate_id').text('-------------');
                $('#oracle_id').text('-------------');
                $('#name').text('-------------');
                $('#unit').text('------------------------');
                $('#department').text('------------------------');
                $('#designation').text('------------------------');
                $('#doj').text('------------------------');
                $('#avatar').attr('src','/assets/images/user/09.jpg'); 
                $('#service_Y').html('0');
                $('#service_m').html('0');
                $('#service_d').html('0');
                $('#gross_salary').text('0');
                $('#basic_salary').text('0');
                $('#total_earn_leave').text('0');
                $('#enjoyed_earn_leave').text('0');
                $('#remained_earn_leave').text('0');
            }

        });

        $(document).on('click','#pay_button', function(){
            $('#loader').removeAttr('hidden');
            $.ajax({
                url: '{{url('hr/payroll/save_benefit_data')}}',
                type: 'get',
                dataType: 'json',
                data:{
                    benefit_on      : $('#benefit_on').val(),
                    death_reason    : $('#death_reason').val(),
                    suspension_days : $('#suspension_days').val(),
                    associate_id    : $('#associate').val(),
                    earn_amount     : $('#earn_leave_due').val(),
                    service_benefits : $('#service_benefit').val(),
                    subsistence_allowance : $('#subsistence_allowance').val(),
                    notice_pay      : $('#notice_pay').val(),
                    termination_benefits: $('#termination_benefit').val(),
                    natural_death_benefits: $('#natural_death_benefit').val(),
                    on_duty_accidental_death_benefits : $('#on_duty_and_accidental_death_on_duty').val()

                },
                success: function(data){
                    console.log('done');
                    if(data == 1){
                        swal("Data Saved. Please Print Out the Voucher", "", "success");
                        $('#loader').attr('hidden', 'hidden');
                        $('#voucher').removeAttr('hidden');
                        //Scrolling to portion
                        $('html,body').animate({
                            scrollTop: $("#voucher").offset().top},
                            'slow');
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });


        });
    });

    //function that will return a number in bengali
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

    function categoryWisePrintSectionHide(benefit_on){
        // allPrintHidden();

        // if(benefit_on == ''){
        //     allPrintHidden();
        // }
        // else{
        //     if(benefit_on == 'on_resign'){
        //         $('#earn_leave_row_print').removeAttr('hidden');
        //         $('#service_benefit_row_print').removeAttr('hidden');       
        //     }
        //     else if(benefit_on == 'on_dismiss'){
        //         $('#earn_leave_row_print').removeAttr('hidden');
        //         $('#service_benefit_row_print').removeAttr('hidden');
        //         $('#subsistence_allowance_row_print').removeAttr('hidden');
        //     }
        //     else if(benefit_on == 'on_terminate'){
        //         $('#earn_leave_row_print').removeAttr('hidden');
        //         $('#notice_pay_row_print').removeAttr('hidden');
        //         $('#termination_benefit_row_print').removeAttr('hidden');
        //     }
        //     else if(benefit_on == 'on_death'){
        //         $('#earn_leave_row_print').removeAttr('hidden');
        //         $('#service_benefit_row_print').removeAttr('hidden');
        //         $('#natural_death_row_print').removeAttr('hidden');
        //         $('#on_duty_and_accidental_death_row_print').removeAttr('hidden');
        //     }
        // }
    }
    // function allPrintHidden(){
    //     $('#earn_leave_row_print').attr('hidden', 'hidden');
    //     $('#service_benefit_row_print').attr('hidden', 'hidden');
    //     $('#subsistence_allowance_row_print').attr('hidden', 'hidden');
    //     $('#notice_pay_row_print').attr('hidden', 'hidden');
    //     $('#termination_benefit_row_print').attr('hidden', 'hidden');
    //     $('#natural_death_row_print').attr('hidden', 'hidden');
    //     $('#on_duty_and_accidental_death_row_print').attr('hidden', 'hidden');
    // }   


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