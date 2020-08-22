@extends('hr.layout')
@section('title', 'Add Role')
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
                    <a href="#">Report</a>
                </li>
                <li class="active">Group Attendance</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
          <div id="load"></div>
             <?php $type='group_attendance_report'; ?>
            @include('hr/reports/attendance_radio')
            <div class="page-header">
                <h1>Report<small><i class="ace-icon fa fa-angle-double-right"></i>Group Attendance</small></h1>
            </div>
            <div class="row">
                <div class="col-sm-12 no-padding-left"> 
                <form role="form" method="get" action="{{ url('hr/reports/group_attendance') }}" id="searchform" class="form-horizontal col-sm-12 no-padding-left">
                  <div class="col-sm-12" >
                           <div class="form-group">
                          {{--   <div class="col-sm-2 col-xs-2">
                                <label class="col-sm-3 control-label no-padding-left" for="unit"> Unit </label>
                                <div class="col-sm-9 no-padding-right no-padding-left">
                                    {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                                </div>
                            </div>
 --}}
                            <div class="col-sm-3 col-xs-3">
                                <label class="col-sm-3 control-label  no-padding-right no-padding-left" for="floor"> Date :<span style="color: red">*</span></label>
                                <div class="col-sm-9  no-padding-right no-padding-left">
                                  
                                    <input type="text" name="g_date" id="g_date" class="form-control datepicker" data-validation="required" data-validation-format="yyyy-mm-dd" autocomplete="off" placeholder="Y-m-d" value="{{ Request::get('g_date') }}" />
                                </div>
                            </div>

                            <div class="col-sm-5  col-xs-5">
                                <label class="col-sm-4 control-label no-padding-left" for="area"> Salary Range :<span style="color: red">*</span></label>

                                 <?php 
                                        if(Request::get('salary_to'))
                                            $minSalary=Request::get('salary_from');
                                        else
                                            $minSalary='50000';
                                        if(Request::get('salary_to'))
                                            $maxSalary=Request::get('salary_to');
                                        else
                                            $maxSalary=$salaryMax;
                                    ?>   
                                <div class="col-sm-4  no-padding-right no-padding-left">
                                    <input type="number" name="salary_from" id="salary_from" class="form-control"  style="height: 35px;" data-validation="required"  placeholder="" value="{{ $minSalary }}" min="50000" max="{{ $salaryMax}}"/>                                   
                                </div>
                                <div class="col-sm-4  no-padding-right">


                                    <input type="number" name="salary_to" id="salary_to" class="form-control" style="height: 35px;" data-validation="required"  placeholder="" value="{{$maxSalary }}" min="50000" max="{{ $salaryMax}}"  />
                                </div>
                            </div>

                            <div class="col-sm-4">
                                 <button type="submit" id="salary_generate" class="btn btn-primary btn-sm">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    @if (!empty(request()->has('g_date')))  
                                      <button type="button" onClick="printMe('html-2-pdfwrapper')" class="btn btn-warning btn-sm" title="Print">
                                         <i class="fa fa-print"></i> 
                                      </button> 
                                      <button type="button"  id="excel"  class="showprint btn btn-success btn-sm">
                                        <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                      </button>
                                      <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-sm" title="PDF">
                                          <i class="fa fa-file-pdf-o"></i> 
                                      </a>  
                                    @endif
                            </div>

                           
                          </div>
                         </div>

                        </form>


                        @if(!empty($g_date))

                       
                        <div class="col-sm-12" >
                          <div id="employee_content_section" class="row">
                          <!-- Display Erro/Success Message -->
                            @include('inc/message')
                          
                            <div class="col-xs-12 html-2-pdfwrapper" id="PrintArea">
                                <div id="html-2-pdfwrapper">
                                    <div id="form-element" class="col-sm-12" style="margin:20px auto;border:1px solid #ccc">

                                        <div class="page-header" style="margin:10px 10px;border-bottom:2px double #666;text-align: center;">
                                           <h4 style="margin:10px 10px; text-align: center;">
                                           Group Attendance Report</h4>
                                         {{--   <span>  Group Attendance Report</span><br> --}}
                                           <span> Date: {{$g_date}} </span><br>
                                        </div> 
                                        @php
                                        	$year = \Carbon\Carbon::parse($g_date)->format('Y');
                                        	$month = \Carbon\Carbon::parse($g_date)->format('F');
                                        @endphp                           
                            
                                        <table class="table table-sm responsive" style="width:100%;border:1px solid #ccc;font-size:12px;"  cellpadding="2" cellspacing="0" border="1" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Area</th>
                                                    <th rowspan="2">Associate ID</th>
                                                    <th rowspan="2">Name</th>
                                                    <th rowspan="2">Designation</th>
                                                    <th colspan="{{$countLocation}}" class="text-center">IN [ {{$g_date}} ]</th>
                                                    <th colspan="{{$countLocation}}" class="text-center">OUT [ {{ date('Y-m-d', strtotime('-1 day', strtotime($g_date)))}} ]</th>
                                                    <th rowspan="2">Remarks</th>
                                                </tr>
                                                <tr>
                                                     @foreach($locationlist as $location)
                                                        <th>{{$location->hr_location_name}}{{--  {{$loc_id=$location->hr_location_id}} --}}</th>
                                                                                       
                                                     @endforeach
                                                     @foreach($locationlist as $location)
                                                         <th>{{$location->hr_location_name}}</th>
                                                     @endforeach 
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach($all_empAttendance as $employe)
                                                @if(isset($employe['associate']) && $employe['associate'] != null)
                                                @php
                                                	$associate = $employe['associate'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $employe['area'] }}</td>
                                                    <td><a target="_blank" href='{{ URL::to("hr/reports/job_card?associate=$associate&month=$month&year=$year") }}'>{{$associate}}</a></td>
                                                    <td>{{$employe['name']}}</td>
                                                    <td>{{$employe['designation']}}</td>

                                                    <?php  $count1 =0; ?>

                                                    @foreach($locationlist as $location)
                                                       @if(isset($employe['location_id']) && $employe['location_id']==$location->hr_location_id)
                                                         <td> 
                                                            <?php 
                                                             $in=$employe['in_time'];
                                                             if($in !=""){
                                                           
                                                                 $dateTime = new DateTime($in);
                                                                 $date = $dateTime->format('Y-m-d');
                                                                 $inTime = $dateTime->format('H:i:s');
                                                               }
                                                           
                                                             else 
                                                                $inTime= "";

                                                             ?>
                                                            {{$inTime}}
                                                         </td>
                                                        @else

                                                            <?php

                                                            if(isset($employe['requested_place']) && $employe['requested_place']!=""){
                                                                $inTime=$employe['requested_place']; $count1++;
                                                              } 
                                                            else 
                                                                $inTime=isset($employe['leave']) ? $employe['leave']:''; $count1++;

                                                            ?>       
                                                         <td colspan=""> @if($count1==1) {{$inTime}} @endif </td>     {{--  @break --}}
                                                       @endif
                                                    @endforeach
                                                   
                                                    @php $count2 =0; @endphp
                                                    @foreach($locationlist as $location)

                                                       @if(isset($employe['location_id_out']) && $employe['location_id_out']==$location->hr_location_id)
                                                            <td> 
                                                                <?php 
                                                                 $out=$employe['outtime'];
                                                                 if($out !=""){
                                                               
                                                                     $dateTime = new DateTime($out);
                                                                     $date = $dateTime->format('Y-m-d');
                                                                     $outTime = $dateTime->format('H:i:s');
                                                                   }
                                                               
                                                                 else 
                                                                    $outTime= "";
                                                                 ?>
                                                                {{$outTime}} 
                                                            </td>
                                                        @else 
                                                          
                                                            @php
                                                            if(isset($employe['requested_place_out']) && $employe['requested_place_out']!=""){
                                                                $outTime=$employe['requested_place_out']; $count2++;
                                                              } 
                                                            else 
                                                                $outTime=isset($employe['leave_out']) ? $employe['leave_out']:''; $count2++;
                                                           @endphp       
                                                            <td> @if($count2==1) {{$outTime}} @endif  </td>
                                                        @endif

                                                     

                                                    @endforeach 

                                                    <td>{{$employe['remarks']}}</td>
                                                </tr>
                                                @endif
                                                @endforeach 
                                               {{--  <tr><td>Total</td>
                                                   <td colspan="{{$countLocation+$countLocation+4}}"></td>
                                                </tr> --}}
                                            </tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                          </div>
                        </div>
                    @endif 
          
              </div>
            </div>

          
     
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript"> 
  
    $(document).ready(function(){
        // loader visibility
          $('#searchform').submit(function() {
            $('#load').css('visibility', 'visible');
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
           document.getElementById('employee_content_section').style.visibility="hidden";
      } else if (state == 'complete') {
          setTimeout(function(){
             document.getElementById('interactive');
             document.getElementById('load').style.visibility="hidden";
             document.getElementById('employee_content_section').style.visibility="visible";
             document.getElementById('employee_content_section').scrollIntoView();
          },1000);
      }
    } 

    function printMe(divName)
    { 
        var myWindow=window.open('','','width=800,height=800');
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