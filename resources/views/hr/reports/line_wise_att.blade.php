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
                <li class="active"> Line Wise Present/Absent </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content">
          <div id="load"></div>  
          <?php $type='line'; ?> 
             @include('hr/reports/attendance_radio')
            <div class="page-header">
                <h1>Reports<small> <i class="ace-icon fa fa-angle-double-right"></i> Line Wise Present/Absent </small></h1>
            </div>
            <div class="row">
                @include('inc/message')
                    <div class="col-sm-12">
                        <form role="form" method="get" action="{{ url('hr/reports/line_wise_att') }}"  id="searchform">
                            <div class="form-group">
                                <div class="col-sm-3" style="padding-bottom: 10px;">
                                    {{ Form::select('unit_id', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required']) }}  
                                </div>
                                <div class="col-sm-3" style="padding-bottom: 10px;">
                                    {{ Form::select('line_id', !empty(Request::get('unit_id'))?$lineList:[], Request::get('line_id'), ['placeholder'=>'Select Line', 'id'=>'line_id', 'class'=> 'col-xs-12', 'data-validation'=>'required']) }}  
                                </div>
                                <div class="col-sm-3" style="padding-bottom: 40px;">
                                    <input type="text" name="report_date" id="report_date" class="col-xs-12 datepicker" value="{{ Request::get('report_date') }}" data-validation="required" placeholder="Y-m-d" style="height: 32px;" />
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-primary btn-xs">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                    @if(!empty(request()->unit_id) && !empty(request()->line_id) && !empty(request()->report_date))
                                    <button type="button" onClick="printMe('PrintArea')" class="btn btn-warning btn-xs" title="Print">
                                        <i class="fa fa-print"></i> 
                                    </button> 
                                    <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-xs" title="PDF">
                                        <i class="fa fa-file-pdf-o"></i> 
                                    </a>
                                    <button type="button"  id="excel"  class="showprint btn btn-success btn-xs">
                                     <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                <br><br>
            </div>

            <div id="attandance_content_section" class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')

                @if(!empty(request()->unit_id) && !empty(request()->line_id) && !empty(request()->report_date))

                    <div class="col-xs-12" id="PrintArea">
                        <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:20px auto;border:1px solid #ccc">
                            <div class="page-header" style="text-align:left;border-bottom:2px double #666">
                                <h2 style="margin:4px 10px; font-weight: bold; text-decoration: underline; text-align: center;">Line Wise Present/Absent</h2>
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td width="70%" style="margin: 0; padding: 0">
                                                <h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Unit: </font>&nbsp;&nbsp;{{ !empty($unit_name)?$unit_name:null }}</h4>
                                                <h5 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Line: </font>&nbsp;&nbsp;{{ !empty($line_name)?$line_name:null }}</h5>
                                                <h5 style="margin:4px 5px; font-size: 10px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Report Date: </font>&nbsp;&nbsp;{{ !empty($report_date)?$report_date:null }}</h5>
                                            </td>
                                            <td style="margin: 0; padding: 0">
                                                <h4 style="margin:4px 5px; text-align: right; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Total Emp in Line:</font>&nbsp;&nbsp;{{ !empty($info)?$info->count():null }}</h4>
                                                <h4 style="margin:4px 5px; text-align: right; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Present Emp in Line(P):</font>&nbsp;&nbsp;{{ !empty($present)?$present:null }}</h4>
                                                <h4 style="margin:4px 5px; text-align: right; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Absent Emp in Line(A):</font>&nbsp;&nbsp;{{ !empty($absent)?$absent:null }}</h4>
                                                <h5 style="margin:4px 5px; font-size: 10px; text-align: right; margin: 0; padding: 0"><font style="font-weight: bold;">Print:&nbsp;&nbsp;</font><?php echo date('d-M-Y H:i A');  ?></h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                
                            </div>
                            @if(!empty($departments))
                                @foreach($departments AS $department)
                                    <?php $count=0; $dept_sl=1; $p=0; $overtime_minutes=0; ?>
                                    <table class="table" style="width:100%;border:1px solid #ccc;font-size:12px; display: block; overflow-x: auto; white-space: nowrap;"  cellpadding="2" cellspacing="0" border="1" align="center"> 
                                        <thead>
                                            <tr>
                                                <th colspan="2">Department</th>
                                                <th colspan="5">{{ $department->hr_department_name }}</th>
                                            </tr> 
                                            <tr>
                                                <th width="20%">Sl</th>
                                                <th width="20%">Associate ID</th>
                                                <th width="20%">Name</th>
                                                <th width="20%">Present Status</th>
                                                <th width="20%">In Time</th>
                                                <th width="20%">Out Time</th>
                                                <th width="20%">Overtime(Hour)</th>
                                            </tr> 
                                        </thead>
                                        <tbody>
                                            @foreach($info AS $emp)
                                            @if($emp->as_department_id== $department->as_department_id)
                                                <tr>
                                                    <td>{{ $dept_sl }}</td>
                                                    <td>{{ !empty($emp->associate_id)?$emp->associate_id:null }}</td>
                                                    <td>{{ !empty($emp->as_name)? $emp->as_name:null }}</td>
                                                    <td>{{ !empty($emp->att)? $emp->att:null }}</td>
                                                    <td>{{ !empty($emp->in_time)? $emp->in_time:null }}</td>
                                                    <td>{{ !empty($emp->out_time)? $emp->out_time:null }}</td>
                                                    <td>{{ !empty($emp->oth)? $emp->oth:null }}</td>
                                                    <?php if($emp->att == "P") $p++; $count++; $dept_sl++;
                                                    $overtime_minutes+= $emp->otm;
                                                     ?>
                                                </tr>
                                            @endif
                                            @endforeach
                                            <tr>
                                                <td><?php echo "<font style=\"font-weight: bold\">Total:&nbsp;&nbsp;</font>". $count;?></td>
                                                <td colspan="2"></td>
                                                <td><font style="font-weight: bold">P:&nbsp;&nbsp;</font>{{$p}} &nbsp; &nbsp;<font style="font-weight: bold">A:&nbsp;&nbsp;</font><font style="text-align: right;"><?php echo ($count-$p); $count=0; ?></font></td>
                                                <td colspan="2"></td>
                                                <td><font style="font-weight: bold">Total Oth:&nbsp;&nbsp;</font>
                                                    <?php
                                                    $result= floor($overtime_minutes/60).":".((($overtime_minutes%60)>0)? (($overtime_minutes%60))."":"00");

                                                    ?>
                                                    <font><?php echo $result ?></font>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endforeach
                                @endif
                        </div>
                    </div>
                    @endif
                
                <!-- //ends of info  -->
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

    $('#unit_id').on("change", function(){ 
        $.ajax({
            url : "{{ url('hr/reports/line_by_unit') }}",
            type: 'get',
            data: {unit : $(this).val()},
            success: function(data)
            {
                $("#line_id").html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });

     $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html()) 
        location.href=url
        return false
    })
});
 function attLocation(loc){
    window.location = loc;
   }


//  Loader 
     document.onreadystatechange = function () {
      var state = document.readyState
      if (state == 'interactive') {
           document.getElementById('attandance_content_section').style.visibility="hidden";
      } else if (state == 'complete') {
          setTimeout(function(){
             document.getElementById('interactive');
             document.getElementById('load').style.visibility="hidden";
             document.getElementById('attandance_content_section').style.visibility="visible";
             document.getElementById('attandance_content_section').scrollIntoView();
          },1000);
      }
    }

function printMe(divName)
{ 
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write(document.getElementById(divName).innerHTML); 
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}
   
</script>
@endsection
