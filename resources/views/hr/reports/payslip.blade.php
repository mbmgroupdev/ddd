@extends('hr.layout')
@section('title', 'Payslip')
@section('main-content')
@push('css')
  <style>
    html {
     scroll-behavior: smooth;
    }
    #load{
        width:100%;
        height:100%;
        position:fixed;
        z-index:9999;
        background:url({{asset('assets/rubel/img/loader.gif')}}) no-repeat 35% 75%  rgba(192,192,192,0.1);
        visibility: hidden;
    }

    @media only screen and (max-width: 1199px) {
        .pay_slip_fields .col-sm-3{width: 33%;}
}
#header{
     display: none;
}
@media only screen and (max-width: 767px) {
        .pay_slip_fields .col-sm-3{width: 50%;}
        .pay_slip_fields .col-sm-8{padding-left: 0px !important;}
}
@media print {
  #header {
    display: table-header-group;
      /* display: block; */
      text-align: center;

  }


  .pageprint{
     display:block;
     page-break-before: always !important;
  }
  .pageprint :first-of-type{
     page-break-before: avoid;
  }
}


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
                    <a href="#">Operations</a>
                </li>
                <li class="active"> Pay Slip</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
          <div id="load"></div>
            <div class="row">
                <div class="col-sm-12">
                <form role="form" method="get" action="{{ url('hr/reports/payslip') }}" id="searchform" class="form-horizontal col-sm-12 pay_slip_fields">

                           {{-- <div class="form-group"> --}}
                            <div class="col-sm-3 col-xs-3" style="padding-bottom: 10px;">
                                <label class="col-sm-4 control-label no-padding-left" for="unit"> Unit <span class="text-red" style="vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                    {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                                </div>
                            </div>

                            <div class="col-sm-3 col-xs-3" style="padding-bottom: 10px;">
                                <label class="col-sm-4 control-label no-padding-left" for="floor"> Floor </label>
                                <div class="col-sm-8">
                                    {{ Form::select('floor', !empty(Request::get('unit'))?$floorList:[], Request::get('floor'), ['placeholder'=>'Select Floor', 'id'=>'floor', 'style'=>'width:100%']) }}
                                </div>
                            </div>

                            <div class="col-sm-3 col-xs-3" style="padding-bottom: 10px;">
                                <label class="col-sm-4 control-label no-padding-left" for="area"> Area </label>
                                <div class="col-sm-8">
                                    {{ Form::select('area', $areaList, Request::get('area'), ['placeholder'=>'Select Area', 'id'=>'area', 'style'=> 'width:100%', 'data-validation-error-msg'=>'The Area field is required']) }}
                                </div>
                            </div>

                            <div class="col-sm-3 col-xs-3" style="padding-bottom: 10px;">
                                <label class="col-sm-4 control-label no-padding-left" for="department">Department </label>
                                <div class="col-sm-8">
                                    {{ Form::select('department', !empty(Request::get('area'))?$deptList:[], Request::get('department'), ['placeholder'=>'Select Department ', 'id'=>'department', 'style'=> 'width:100%','data-validation-error-msg'=>'The Department field is required']) }}
                                </div>
                            </div>
                          {{-- </div> --}}



                           {{-- <div class="form-group"> --}}

                            <div class="col-sm-3 col-xs-3" style="padding-bottom: 10px;">
                                <label class="col-sm-4 control-label no-padding-left" for="department">Section </label>
                                <div class="col-sm-8">
                                    {{ Form::select('section', !empty(Request::get('department'))?$sectionList:[], Request::get('section'), ['placeholder'=>'Select Section ', 'id'=>'section', 'style'=> 'width:100%', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                                </div>
                            </div>

                            <div class="col-sm-3 col-xs-3" style="padding-bottom: 10px;">
                                <label class="col-sm-4 control-label no-padding-left no-padding-right" for="department">Sub-Section </label>
                                <div class="col-sm-8">
                                    {{ Form::select('subSection', !empty(Request::get('section'))?$subSectionList:[], Request::get('subSection'), ['placeholder'=>'Select Sub-Section ', 'id'=>'subSection', 'style'=> 'width:100%', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                                </div>
                            </div>
                            <div class="col-sm-3 col-xs-3" style="padding-bottom: 10px;">
                                <label class="col-sm-4 control-label no-padding-left no-padding-right" for="month_number"> Month <span class="text-red" style="vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                  <select id="month_number" name="month_number" class="col-xs-12 month_number">
                                      <option value="">Select Month</option>
                                      <option value="01" {{sselected(date('F'),'January')}}>January</option>
                                      <option value="02" {{sselected(date('F'),'February')}}>February</option>
                                      <option value="03" {{sselected(date('F'),'March')}}>March</option>
                                      <option value="04" {{sselected(date('F'),'April')}}>April</option>
                                      <option value="05" {{sselected(date('F'),'May')}}>May</option>
                                      <option value="06" {{sselected(date('F'),'June')}}>June</option>
                                      <option value="07" {{sselected(date('F'),'July')}}>July</option>
                                      <option value="08" {{sselected(date('F'),'August')}}>August</option>
                                      <option value="09" {{sselected(date('F'),'September')}}>September</option>
                                      <option value="10" {{sselected(date('F'),'October')}}>October</option>
                                      <option value="11" {{sselected(date('F'),'November')}}>November</option>
                                      <option value="12" {{sselected(date('F'),'December')}}>December</option>
                                  </select>
                                  <span class="text-red" id="error_month_s"></span>
                                      <!-- <input type="text" name="start_date" id="start_date" placeholder="Start Date" class="form-control " data-validation="required"  value="{{ Request::get('start_date') }}"/> -->
                                </div>
                            </div>
                            <div class="col-sm-3 col-xs-3" style="padding-bottom: 10px;">
                                <label class="col-sm-4 control-label no-padding-left" for="year"> Year <span class="text-red" style="vertical-align: top;">&#42;</span></label>
                                <div class="col-sm-8">
                                  <select id="year" name="year" class="col-xs-12 year">
                                      @foreach($getYear as $year)
                                      <option value="{{ $year }}">{{ $year }}</option>
                                      @endforeach
                                  </select>
                                  <!-- <input type="number" id="year" class="col-xs-12 yearpicker" placeholder="Enter Year" name="year" value="{{ date('Y') }}"> -->
                                  <span class="text-red" id="error_year_s"></span>                                </div>
                            </div>

                          {{-- </div> --}}

                         <div class="col-sm-12" >
                            <div class="col-sm-12 align-right no-padding-right no-padding-left">

                                    <button type="submit" id="salary_generate" class="btn btn-primary btn-sm" style="margin-bottom: 20px;">
                                        <i class="fa fa-search"></i> Generate
                                    </button>
                                    @if (!empty(request()->has('unit')))
                                      <button type="button" onClick="printPayslip('html-2-pdfwrapper')" class="btn btn-warning btn-sm" title="Print" style="margin-bottom: 20px;">
                                         <i class="fa fa-print"></i>
                                      </button>
                                      <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" style="margin-bottom: 20px;">
                                        <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                      </button>
                                      <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-sm" title="PDF" style="margin-bottom: 20px;">
                                          <i class="fa fa-file-pdf-o"></i>
                                      </a>
                                    @endif
                            </div>
                         </div>
                </form>
              </div>
            </div>

            <div  id="pay_content_section" class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                @if(isset($info) && !empty($info))
             <div >


                <?php
                    date_default_timezone_set('Asia/Dhaka');
                    $en = array('0','1','2','3','4','5','6','7','8','9', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
                    $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর');
                ?>
                <?php
                   $totalsalary =0;
                if(!empty($info->employee)){
                  $pages = ceil(count($info->employee)/3);

                   //dd($page);exit;

                  ?>
                  <div id="header1">
                <h3 style="margin:4px 10px;text-align:center;">

                    পে-স্লিপঃ
                      @php
                        $monthObj = date('F', mktime(0, 0, 0, $info->month, 10));
                      @endphp
                      {{ eng_to_bn($monthObj) }} - {{ eng_to_bn($info->year) }}
                </h3>


                <h6 style="margin:4px 10px;text-align:center;font-weight:600;font-size:13px;">
                    সর্বমোট টাকার পরিমানঃ
                    <span style="color:hotpink;font-size:15px;" id="total-salary">{{str_replace($en, $bn,(string)number_format($grandTotal,2, '.', ','))}}</span><br>
                    <span></span>
                    মোট কর্মী/কর্মচারীঃ
                    <span style="color:hotpink;font-size:15px;" id="emp-count">{{ eng_to_bn(count($info->employee)) }}</span>

                </h6>
                </div>
                <?php } ?>
                <div class="col-xs-12" id="html-2-pdfwrapper">


                        <?php $i = 1;$p = 1;
                          // $totalsalary =0;
                        ?>
                    @foreach($info->employee as $k=>$employee)

                    @if($i==1)

                    <?php

                      $sumandpage = explode('-',$sum[$p]);

                    ?>
                    <div id="header">
                  <h3 style="margin:4px 10px;text-align:center;">

                      পে-স্লিপঃ
                        @php
                          $monthObj = date('F', mktime(0, 0, 0, $info->month, 10));
                        @endphp
                        {{ eng_to_bn($monthObj) }} - {{ eng_to_bn($info->year) }}
                  </h3>


                  <h6 style="margin:4px 10px;text-align:center;font-weight:600;font-size:13px;">
                      সর্বমোট টাকার পরিমানঃ
                      <span style="color:hotpink;font-size:15px;" id="total-salary">{{str_replace($en, $bn,(string)number_format($sumandpage[0],2, '.', ','))}}</span><br>
                      <span></span>
                      মোট কর্মী/কর্মচারীঃ
                      <span style="color:hotpink;font-size:15px;" id="emp-count">{{ eng_to_bn($sumandpage[1]) }}</span>

                  </h6>
                  </div>
                  @endif
                    <div class="col-sm-12" style="height:29%; padding-bottom: 20px;">
                        <table style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:9px;color:lightseagreen;text-align:left;display: block;overflow-x: auto;">
                             <thead>
                               <tr>
                                 <td></td>
                               </tr>
                             </thead>
                              <tbody>
                            <tr>
                                <td colspan="2" style="padding:10px 10px 0 10px;color:hotpink;">
                                    <p style="margin:0;padding:0;">নামঃ {{ $employee['employee_bengali']['hr_bn_associate_name']}}</p>
                                    <p style="margin:0;padding:0;">পদবীঃ {{ isset($employee['designation']['hr_designation_name_bn'])? eng_to_bn($employee['designation']['hr_designation_name_bn']):'' }}</p>
                                    <p style="margin:0;padding:0;">গ্রেডঃ {{ isset($employee['designation']['hr_designation_grade'])? eng_to_bn($employee['designation']['hr_designation_grade']):'' }}</p>
                                    <p style="margin:0;padding:0;">যোগদানের তারিখঃ {{ eng_to_bn($employee['as_doj']) }}</p>
                                </td>
                                <td colspan="3" style="padding:10px;color:hotpink;text-align:center">
                                    <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:16px;">{{ $info->unit }}</h3>
                                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:10px;">পে-স্লিপঃ
                                      @php
                                        $monthObj = date('F', mktime(0, 0, 0, $info->month, 10));
                                      @endphp
                                      {{ eng_to_bn($monthObj) }} - {{ eng_to_bn($info->year) }}
                                     </h5>
                                   <p style="color:black;font-weight:bolder;text-transform:uppercase">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <font style="margin:0 0 0 20px;padding:4px 0;">ফ্লোর নং  <font style="text-align:right;color:black">&nbsp;&nbsp; {{ $employee['floor']['hr_floor_name'] }}</font>
                                        </font>
                                   </p>

                                    <p style="clear:both;text-align: center;margin:0;padding:0;width:100%;display:block;color:lightseagreen">
                                        অতিরিক্ত কাজের মঞ্জুরি হারঃ &nbsp;<font style="color:hotpink"> {{ str_replace($en, $bn,(string)number_format($employee->salary['ot_rate'],2, '.', ',')) }}</font> /=টঃ
                                    </p>
                                </td>
                                <td width="30">
                                    <p style="border-radius:50%;width:30px;height:30px;border:1px solid #999;color:#999;line-height:30px;text-align:center">{{ str_replace($en, $bn, ($k+1))}}</p>
                                </td>
                                <td width="15%" style="padding:10px;color:hotpink;">
                                   <p style="margin:0;padding:4px 0;display:inline;text-align:right;color:maroon;font-weight:bolder">আই ডি #
                                        <font style="padding:4px 0;display:inline;text-align:right;color:black;font-weight:bolder"><!-- {{ (substr_replace($employee->associate, str_replace($en, $bn, $employee->temp_id), 3, 6)) }} -->
                                        {{ $employee->associate_id}}
                                        </font>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <!-- first -->
                                <td width="100" style="padding:0 0 0 10px;">
                                    <p style="margin:0;padding:0">&nbsp;</p>
                                    <p style="margin:0;padding:0">&nbsp;</p>
                                    <p style="margin:0;padding:0">উপস্থিত দিবস</p>
                                    <p style="margin:0;padding:0">ছুটি দিবস</p>
                                    <p style="margin:0;padding:0k">অনুপস্থিত দিবস</p>
                                    <p style="margin:0;padding:0">ছুটি মঞ্জুর</p>
                                    <p style="margin:0;padding:0;border-top:1px solid #999">মোট দেয়</p>
                                </td>
                                <td width="100" align="right">
                                    <p style="margin:0;padding:0">&nbsp;</p>
                                    <p style="margin:0;padding:0">&nbsp;</p>
                                    <p style="margin:0;padding:0">=&nbsp;&nbsp;&nbsp;{{ str_replace($en, $bn, $employee->salary['present']) }}</p>
                                    <p style="margin:0;padding:0">=&nbsp;&nbsp;&nbsp;{{ str_replace($en, $bn, $employee->salary['holiday']) }}</p>
                                    <p style="margin:0;padding:0k">=&nbsp;&nbsp;&nbsp;{{ str_replace($en, $bn, $employee->salary['absent']) }}</p>
                                    <p style="margin:0;padding:0">=&nbsp;&nbsp;&nbsp;{{ str_replace($en, $bn, $employee->salary['leave']) }} </p>
                                    <p style="margin:0;padding:0;border-top:1px solid #999">=&nbsp;&nbsp;&nbsp;{{ str_replace($en, $bn, ($employee->salary['present']+$employee->salary['holiday']+$employee->salary['leave'])) }}</p>
                                </td>
                                <!-- second -->
                                <td width="100" style="padding:0 0 0 20px;">
                                    <p style="margin:0;padding:0"></p>
                                    <p style="margin:0;padding:0">মূল বেতন</p>
                                    <p style="margin:0;padding:0">বাড়ী বাড়া (৪০%)</p>
                                    <p style="margin:0;padding:0">চিকিৎসা ভাতা</p>
                                    <p style="margin:0;padding:0">যাতায়াত</p>
                                    <p style="margin:0;padding:0">খাদ্য</p>
                                    <p style="margin:0;padding:0;border-top:1px solid #999">মোট মজুরি</p>
                                </td>
                                <td align="right">
                                    <p style="margin:0;padding:0"></p>
                                    <p style="margin:0;padding:0">=
                                   <?php $em_basic=$employee->salary['basic'];?>
                                   {{str_replace($en, $bn,(string)number_format($em_basic,2, '.', ','))}}
                                   </p>
                                    <p style="margin:0;padding:0">=
                                    <?php $em_house=$employee->salary['house'];?>
                                    {{str_replace($en, $bn,(string)number_format($em_house,2, '.', ','))}}
                                    </p>
                                    <p style="margin:0;padding:0">=
                                    <?php $medical=$employee->salary['medical'];?>
                                    {{str_replace($en, $bn,(string)number_format($medical,2, '.', ','))}}
                                    </p>
                                    <p style="margin:0;padding:0">=<?php $transport=$employee->salary['transport'];?>
                                    {{str_replace($en, $bn,(string)number_format($transport,2, '.', ','))}}
                                    </p>
                                    <p style="margin:0;padding:0">= <?php $food=$employee->salary['food'];?>
                                    {{str_replace($en, $bn,(string)number_format($food,2, '.', ','))}}
                                    </p>
                                    <p style="margin:0;padding:0;border-top:1px solid #999">=<?php
                                    $total_sal=$employee->salary['basic']+$employee->salary['house']+$employee->salary['medical']+$employee->salary['transport']+$employee->salary['food'];
                                    ?>
                                    {{str_replace($en, $bn,(string)number_format($total_sal,2, '.', ','))}}
                                    </p>
                                </td>
                                <td width="250" align="right" style="padding:0 0 0 20px;">
                                    <p style="margin:0;padding:0">প্রদেয় মজুরি&nbsp;&nbsp;&nbsp;=</p>
                                    <p style="margin:0;padding:0">খাবার বাবদ/অগ্রিম গ্রহণ/ভোগ্যপণ্য ক্রয়/অন্যান্য কর্তন&nbsp;&nbsp;&nbsp;=</p>
                                    <p style="margin:0;padding:0">স্টাম্পের জন্য কর্তন&nbsp;&nbsp;&nbsp;=</p>
                                    <p style="margin:0;padding:0">মজুরি সমন্বয়&nbsp;&nbsp;&nbsp;=</p>
                                    <p style="margin:0;padding:0">অতিরিক্ত কাজের মজুরি ({{ str_replace($en, $bn, $employee->salary['ot_hour']) }} ঘন্টা)&nbsp;&nbsp;&nbsp;=</p>
                                    <p style="margin:0;padding:0">অতিরিক্ত কাজের মজুরি হার&nbsp;&nbsp;&nbsp;=</p>
                                    <p style="margin:0;padding:0">হাজিরা বোনাস&nbsp;&nbsp;&nbsp;=</p>
                                    <p style="margin:0;padding:0;border-top:1px solid #999">মোট প্রদেয়&nbsp;&nbsp;&nbsp;=</p>
                                </td>
                                <!-- third -->
                                <td align="right" style="color:hotpink">
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;{{str_replace($en, $bn,(string)number_format($employee->salary['gross'],2, '.', ',')) }}</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;{{
                                    str_replace($en, $bn,(string)number_format($employee->salary['food']+(($employee->salary['add_deduct'] == null) ? '0.00' : $employee->salary['add_deduct']['advp_deduct'] )+($employee->salary['add_deduct'] == null) ? '0.00' : (isset($employee->salary['add_deduct']['cg_product'])?$employee->salary['add_deduct']['cg_product']:'') +(($employee->salary['add_deduct'] == null) ? '0.00' : $employee->salary['add_deduct']['others_deduct']),2, '.', ',')) }}</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;{{
                                    str_replace($en, $bn,(string)number_format(10,2, '.', ','))}}</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;{{
                                    str_replace($en, $bn,(string)number_format((($employee->salary['add_deduct'] == null) ? '0.00' : $employee->salary['add_deduct']['advp_deduct'] ),2, '.', ','))}}</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;{{
                                    str_replace($en, $bn,(string)number_format(($employee->salary['ot_rate']*$employee->salary['ot_hour']),2, '.', ','))}}</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;{{
                                    str_replace($en, $bn,(string)number_format($employee->salary['ot_rate'],2, '.', ','))}}</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;{{
                                    str_replace($en, $bn,(string)number_format($employee->salary['attendance_bonus'],2, '.', ','))}}</p>
                                    <p style="margin:0;padding:0;border-top:1px solid #999">&nbsp;&nbsp;&nbsp;{{
                                    str_replace($en, $bn,(string)number_format($employee->salary['total_payable'],2, '.', ','))}}</p>
                                </td>
                                <td align="left">
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;=/টঃ</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;=/টঃ</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;=/টঃ</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;=/টঃ</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;=/টঃ</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;=/টঃ</p>
                                    <p style="margin:0;padding:0">&nbsp;&nbsp;&nbsp;=/টঃ</p>
                                    <p style="margin:0;padding:0;border-top:1px solid #999">&nbsp;&nbsp;&nbsp;=/টঃ</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding:0 0 0 10px" >
                                    <p style="margin:0;padding:0">মোট অতিরিক্ত কাজের ঘন্টা = <span align="right">{{ str_replace($en, $bn, $employee->salary['ot_hour']) }} </span>ঘন্টা </p>
                                    <p style="margin:0;padding:0">বিলম্ব উপস্থিতিঃ {{ str_replace($en, $bn, $employee->salary['late_count']) }}</p>
                                </td>
                                <td colspan="2" style="padding:0 0 0 20px;">
                                    <p style="margin:0;padding:0">অনুপস্থিতির জন্য কর্তন = {{str_replace($en, $bn,(string)number_format($employee->salary['absent_deduct'],2, '.', ','))}}</p>
                                    <p style="margin:0;padding:0">বিলম্ব অর্ধ দিবসের জন্য কর্তন = {{str_replace($en, $bn,(string)number_format($employee->salary['half_day_deduct'],2, '.', ','))}}</p>
                                </td>
                                <td style="padding:0 0 0 20px;">
                                    <p style="margin:0;padding:0;color:#999;border-bottom:1px solid;display:inline-block;">কর্মচারীর স্বাক্ষর</p>
                                </td>
                                <td align="right"><h4 style="margin:0;padding:0;color:hotpink">=&nbsp;&nbsp;&nbsp;{{str_replace($en, $bn,(string)number_format($employee->salary['total_payable'],2, '.', ','))}}
                                </h4></td>
                                <td><h4 style="margin:0;padding:0">=/টঃ</h4></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if($i==3){?>
                     <div  style="display:block;page-break-before: always !important;">


                     </div>
                    <?php $i=0; $p++;?>
                  <?php } ?>
                    <?php $i++;

                    ?>
                    @endforeach
                </div>
                @endif

                <div class="col-xs-12">
                    <div class="col-xs-12">
                        <div class="col-xs-9 text-right">
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function(){
        // loader visibility
          // $('#searchform').submit(function() {
          //   $('#load').css('visibility', 'visible');
          //   });


        // HR Floor By Unit ID
        var unit  = $("#unit");
        var floor = $("#floor")
        unit.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getFloorListByUnitID') }}",
                type: 'get',
                data: {unit_id: $(this).val() },
                success: function(data)
                {
                    floor.html(data);
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });


        //Load Department List By Area ID
        var area       = $("#area");
        var department = $("#department");
        area.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
                type: 'get',
                data: {area_id: $(this).val() },
                success: function(data)
                {
                    department.html(data);
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });

        //Load Section List by department
        var section= $("#section");

        department.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
                type: 'get',
                data: {area_id: area.val(), department_id: $(this).val() },
                success: function(data)
                {
                    section.html(data);
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });

        //Load Sub Section List by Section
        var subSection= $("#subSection");

        section.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getSubSectionListBySectionID') }}",
                type: 'get',
                data: {area_id: area.val(), department_id: department.val(), section_id: $(this).val() },
                success: function(data)
                {
                    subSection.html(data);
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });


        // date
        $('#start_date').datetimepicker({
            showClose: true,
            showTodayButton: true,
            dayViewHeaderFormat: "YYYY MMMM",
            format: "YYYY-MM-DD"
        }).on("dp.update", function(){
            $('#end_date').each(function(){
                if($(this).data('DateTimePicker')){
                    $(this).data("DateTimePicker").destroy();
                    $(this).val("");
                }
            });
        });

        // end date according to start date
        $("body").on("focusin", '#end_date', function(){

            var startDate = $("#start_date").val();
            if(startDate == "")
            {
                $("#start_date").val(moment().format("YYYY-MM-DD"));
                var startDate = $("#start_date").val();
            }

            var day = startDate.substring(8, 10);
            var daysInMonth = moment(startDate).daysInMonth();
            var enableDays = daysInMonth-day;
            var lastDay = moment(startDate).add(enableDays, 'days').format("YYYY-MM-DD");
            var firstDay = moment(startDate).format("YYYY-MM-DD");

            $(this).datetimepicker({
                dayViewHeaderFormat: 'MMMM',
                format: "YYYY-MM-DD",
                minDate: firstDay,
                maxDate: lastDay
            });
        });


        // excel conversion -->
        $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
        location.href=url
        return false
        })

    })

    //  Loader
     document.onreadystatechange = function () {
      var state = document.readyState
      if (state == 'interactive') {
           document.getElementById('pay_content_section').style.visibility="hidden";
      } else if (state == 'complete') {
          setTimeout(function(){
             document.getElementById('interactive');
             document.getElementById('load').style.visibility="hidden";
             document.getElementById('pay_content_section').style.visibility="visible";
             document.getElementById('pay_content_section').scrollIntoView();
          },1000);
      }
    }

    function printPayslip(divName)
    {
        var myWindow=window.open('','PRINT','width=800,height=800');
        // var pageHeight = parseInt($('body').css('height'))
        // 	var offsetHeight=1230;
        //     for(var i=0;i<pageHeight;i++){
        //         if(i%offsetHeight==0 || i==0){
        //         	$('body').append('<div style="position: absolute;top:'+i+';">your header</div>')
        //         }
        //     }
        myWindow.document.write('<html><head></head><body>');
        myWindow.document.write(document.getElementById(divName).innerHTML);
        myWindow.document.write('</body></html>');
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
    function attLocation(loc){
    window.location = loc;
   }
</script>
@endsection
