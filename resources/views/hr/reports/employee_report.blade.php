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
        background:url({{asset('assets/rubel/img/loader.gif')}}) no-repeat 35% 70%  rgba(192,192,192,0.1);  
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
                    <a href="#"> Human Resource </a>
                </li>
                <li>
                    <a href="#"> Reports </a>
                </li>
                <li class="active"> Employee Report </li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        <div class="page-content">
          <div id="load"></div>  
            <?php $type='employee_report'; ?>
            @include('hr/reports/attendance_radio')
            <div class="page-header">
                <h1>Reports<small> <i class="ace-icon fa fa-angle-double-right"></i> Employee Report </small></h1>
            </div>
            <div class="row">
                {{ Form::open(['url'=>'hr/reports/employee_report', 'method'=>'get', 'id'=>'searchform']) }}
                    <div class="col-sm-12"> 
                        <div class="form-group">
                            <div class="col-sm-4" style="padding-bottom: 10px;">
                                {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'class'=> 'col-xs-12', 'data-validation'=>'required']) }}  
                            </div>
                            <div class="col-sm-4" style="padding-bottom: 40px;">
                                {{ Form::select('status', $statusList, null, ['placeholder'=>'Select Status', 'class'=> 'col-xs-12', 'data-validation'=>'required']) }}  
                            </div> 
                            <div class="col-sm-4" style="padding-bottom: 40px;">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-search"></i>
                                    Search
                                </button>
                                @if (!empty(request()->unit)
                                && !empty(request()->status)
                                )  
                                <button type="button" onClick="printMe('PrintArea')" class="btn btn-warning btn-sm" title="Print">
                                    <i class="fa fa-print"></i>  
                                </button> 

                                <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-sm" title="PDF">
                                    <i class="fa fa-file-pdf-o"> </i> 
                                </a>
                                <button type="button" title="EXCEL"  id="excel"  class="showprint btn btn-success btn-sm"><i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                </button>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-10">
                        <div class="text-right"> 
                           </div>
                    </div>


                    </div> 

                {{ Form::close() }}
            </div>

            <div id="employee_content_section" class="row" style="margin-right: 14px; margin-left: 14px;">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                @if (is_array($info) && sizeof($info) > 0)  

                    <div class="col-sm-12 html-2-pdfwrapper" id="PrintArea">
                    <div id="html-2-pdfwrapper">
                        <div class="col-sm-12" style="margin:0 20px 20px 20px auto;border:1px solid #ccc">
                            <div class="page-header" style="text-align:right;border-bottom:2px double #666">
                                <h3 style="margin:4px 10px">{{ $info['unit_name'] }}</h3>
                                <h5 style="margin:4px 10px">{{ $info['status_name'] }}</h5>
                            </div>
                            <div style="width:100%;overflow-x:auto;" >
                                <table class="table table-sm" style="width:100%;border:1px solid #ccc;font-size:12px; display: block;overflow-x: auto;white-space: nowrap;"  cellpadding="2" cellspacing="0" border="1" align="center"> 
                                <thead>
                                    <tr>
                                        <th width="10%">SL#</th>
                                        <th width="10%">AID</th>
                                        <th width="10%">NAME</th>
                                        <th width="10%">DATE OF JOIN</th>
                                        <th width="10%">DESIGNATION</th>
                                        <th width="10%">GRADE</th>
                                        <th width="10%">SECTION</th>
                                        <th width="10%">TRADE</th>
                                        <th width="10%">DATE OF BIRTH</th>
                                        <th width="10%">SALARY</th>
                                        <th width="10%">FLR</th>
                                        <th width="10%">LINE</th>
                                        <th width="10%">OT</th>
                                        <th width="10%">AGE</th>
                                        <th width="10%">SEX</th>
                                        <th width="10%">RELIGION</th>
                                        <th width="10%">DISTRICT</th>
                                        <th width="10%">EDUCATION</th>
                                    </tr> 
                                </thead>
                                <tbody>
                                @forelse ($reports as $report)
                                    <tr>
                                        <td> 
                                            {{ ($loop->index + 1) }}
                                        </td>
                                        <td>{{ $report->associate_id }}</td>
                                        <td>{{ $report->as_name }}</td>
                                        <td>{{ date("d-M-Y", strtotime($report->as_doj)) }}</td>
                                        <td>{{ $report->hr_designation_name }}</td>
                                        <td>{{ $report->hr_designation_grade }}</td>
                                        <td>{{ $report->hr_section_name }}</td>
                                        <td>{{ $report->hr_department_name }}</td>
                                        <td>{{ $birth_date =$report->as_dob }}</td>
                                        <td>{{ $report->ben_current_salary }}</td>
                                        <td>{{ $report->hr_floor_name }}</td>
                                        <td>{{ $report->hr_line_name }}</td>
                                        <td>
                                            <?php   if($report->as_ot==0){echo "N ";}
                                            else { echo "Y "; }?>
                                        </td>
                                        <td> 
                                             <?php  
                                                $age= date("Y") - date("Y", strtotime($birth_date)); 
                                                echo $age;
                                                ?>  
                                        </td>
                                        <td>{{ $report->as_gender }}</td>
                                        <td>{{ $report->emp_adv_info_religion }}</td>
                                        <td>{{ $report->dis_name }}</td>
                                        <td>{{ $report->education_title}}</td>
                                    </tr> 
                                @empty
                                    <tr>
                                        <td colspan="18" align="center">No user found!</td> 
                                    </tr>
                                @endforelse 
                                <!-- ends of report -->
                                </tbody>
                                </table>
                            </div>    
                        </div>
                    </div>
                    </div>  
                @endif 
                <!-- //ends of info  -->
            </div> 
        </div><!-- /.page-content -->
    </div>

<script type="text/javascript">
 
function printMe(divName)
{ 
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write(document.getElementById(divName).innerHTML); 
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
} 



$(document).ready(function(){ 
// loader visibility
  // $('#searchform').submit(function() {
  //   $('#load').css('visibility', 'visible');
  //   });    

// excel conversion -->
   $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html()) 
        location.href=url
        return false
    })

})
  function attLocation(loc){
    window.location = loc;
   }
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

</script>

@endsection
