@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
  <style type="text/css">
      }
    html {
     scroll-behavior: smooth;
    }
    #load{
        width:100%;
        height:100%;
        position:fixed;
        z-index:9999;
        background:url({{asset('assets/rubel/img/loader.gif')}}) no-repeat 35% 50%  rgba(192,192,192,0.1);
        visibility: hidden;
    }
    
    @media print{         
        
        /*.tableTrCenter {
            text-align: center !important;
        }*/
        
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
                    <a href="#"> Time & Attendance  </a>
                </li>
                <li class="active"> Daywise Manual Attendance </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
             <div id="load"></div>
            <div class="page-header">
                <h1>Time & Attendance <small> <i class="ace-icon fa fa-angle-double-right"></i> Daywise Manual Attendance </small></h1>
            </div>
            <div class="row">
                @include('inc/message')
                    <div class="col-sm-12" style="padding: 0px;">
                        <form role="form" method="get" action="{{ url('hr/timeattendance/daywise_manual_attendance') }}" id="searchform">
                             <!-- <input type="hidden" name="unit_att" class="unit_att" value="{{request()->unit_id}}"> -->
                            <div class="form-group col-sm-12">
                                <div class="col-sm-3 no-padding-left" style="padding-top: 15px;">
                                    <label class="col-sm-5 control-label no-padding-right" for="area"> Unit  <span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-7 no-padding-right" >
                                       {{ Form::select('unit_id', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit_id', 'class'=> 'form-control', 'data-validation'=>'required','data-validation-error-msg'=>'The Unit field is required']) }}
                                   </div>
                                </div>
                                <div class="col-sm-3 no-padding-left" style="padding-top: 15px;">
                                    <label class="col-sm-5 control-label no-padding-right" for="area"> Floor  </label>
                                    <div class="col-sm-7 no-padding-right" >
                                       {{ Form::select('floor_id', !empty(Request::get('unit_id'))?$floorList:[], Request::get('floor_id'), ['placeholder'=>'Select Floor', 'id'=>'floor_id', 'class'=> 'col-xs-12']) }}
                                   </div>
                                </div>
                                <div class="col-sm-3 no-padding-left" style="padding-top: 15px;">
                                    <label class="col-sm-5 control-label no-padding-right" for="area"> Line  </label>
                                    <div class="col-sm-7 no-padding-right" >
                                       {{ Form::select('line_id', !empty(Request::get('unit_id'))?$lineList:[], Request::get('line_id'), ['placeholder'=>'Select Line', 'id'=>'line_id', 'class'=> 'col-xs-12']) }}
                                   </div>
                                </div>
                                <div class="col-sm-3 no-padding-left" style="padding-top: 15px;">
                                    <label class="col-sm-5 control-label no-padding-right" for="area"> Area </label>
                                    <div class="col-sm-7 no-padding-right" >
                                       {{ Form::select('area', $areaList, Request::get('area'), ['placeholder'=>'Select Area', 'id'=>'area','class'=> 'col-xs-12','style'=> 'width:100%', 'data-validation-error-msg'=>'The Area field is required']) }}
                                   </div>
                                </div>

                            </div>

                            <div class="form-group col-sm-12">

                                <div class="col-sm-3 no-padding-left" style="padding-top: 15px;" >
                                    <label class="col-sm-5 control-label no-padding-right" for="area"> Department  </label>
                                    <div class="col-sm-7 no-padding-right"  >
                                       {{ Form::select('department', !empty(Request::get('area'))?$deptList:[], Request::get('department'), ['placeholder'=>'Select Department ', 'id'=>'department','class'=> 'col-xs-12', 'style'=> 'width:100%','data-validation-error-msg'=>'The Department field is required']) }}
                                   </div>
                                </div>
                                <div class="col-sm-3 no-padding-left" style="padding-top: 15px;" >
                                    <label class="col-sm-5 control-label no-padding-right" for="area"> Section  </label>
                                    <div class="col-sm-7 no-padding-right" >
                                       {{ Form::select('section', !empty(Request::get('department'))?$sectionList:[], Request::get('section'), ['placeholder'=>'Select Section ', 'id'=>'section', 'class'=> 'form-control', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                                   </div>
                                </div>
                                <div class="col-sm-3 no-padding-left" style="padding-top: 15px;">
                                    <label class="col-sm-5 control-label no-padding-right" for="area"> Sub Section  </label>
                                    <div class="col-sm-7 no-padding-right" >
                                       {{ Form::select('subSection', !empty(Request::get('section'))?$subSectionList:[], Request::get('subSection'), ['placeholder'=>'Select Sub-Section ', 'id'=>'subSection', 'class'=> 'form-control', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                                   </div>
                                </div>
                                <div class="col-sm-3 no-padding-left" style="padding-top: 15px;" >
                                    <label class="col-sm-5 control-label no-padding-right" for="area"> Date <span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-7 no-padding-right" >
                                       <input type="text" name="report_date" id="report_date" class="col-xs-12 datepicker" value="{{ Request::get('report_date') }}" placeholder="Y-m-d" data-validation="required" data-validation-error-msg="The Date field is required"/>

                                   </div>
                                </div>

                            </div>
                            <div class="form-group col-sm-12 responsive-hundred">
                                <div align="right" class="col-sm-2 pull-right">

                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                    @if(!empty(request()->unit_id)  && !empty(request()->report_date))
                                    <button type="button" onClick="printMe1('PrintArea')" class="btn btn-warning btn-sm" title="Print">
                                        <i class="fa fa-print"></i>
                                    </button>
                                    <style type="text/css" media="print" id="print"></style>
                                     <!--
                                    <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-sm" title="PDF">
                                        <i class="fa fa-file-pdf-o"></i>
                                    </a>
                                    <button type="button"  id="excel"  class="showprint btn btn-success btn-sm">
                                     <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                    </button> -->
                                    @endif
                                </div>

                            </div>
                        </form>
                    </div>
                <br><br>
            </div>

            <div id="attendance_content_section" class="row">

                @if(!empty(request()->unit_id)  && !empty(request()->report_date))

                    <div class="col-md-12" id="PrintArea">
                        <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:20px auto;border:1px solid #ccc">
                             <div class="tableTrCenter" style="text-align: center;">
                                <!-- <h2 style="margin:4px 10px; font-weight: bold; text-decoration: underline; text-align: center;">Line Wise Present/Absent</h2> -->

                                                <h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 14px;">Unit: &nbsp;&nbsp;{{ !empty($unit_name)?$unit_name:null }}</font></h4>
                                                @if(!empty(request()->floor_id))
                                                <h5 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Floor: &nbsp;&nbsp;{{ !empty($floor_name)?$floor_name:null }}</font></h5>
                                                @endif
                                                @if(!empty(request()->line_id))
                                                <h5 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Line: &nbsp;&nbsp;{{ !empty($line_name)?$line_name:null }}</font></h5>
                                                @endif
                                                @if(!empty(request()->area))
                                                <h5 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Area: &nbsp;&nbsp;{{ $areaList[request()->area]}}</font></h5>
                                                @endif
                                                @if(!empty(request()->department))
                                                <h5 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Department: &nbsp;&nbsp;{{ $deptList[request()->department]}}</font></h5>
                                                @endif
                                                @if(!empty(request()->section))
                                                <h5 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Section: &nbsp;&nbsp;{{ $sectionList[request()->section]}}</font></h5>
                                                @endif
                                                @if(!empty(request()->subSection))
                                                <h5 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Sub Section: &nbsp;&nbsp;{{ $subSectionList[request()->subSection]}}</font></h5>
                                                @endif
                                                <h5 style="margin:4px 5px; font-size: 10px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Report Date: </font>&nbsp;&nbsp;{{ !empty($report_date)?$report_date:null }}</h5>
                                            {{-- </td>
                                            <td style="margin: 0; padding: 10px 0px 0px 0px;"> --}}
                                                <h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 14px;">Total Absent:&nbsp;&nbsp;{{ !empty($info)?$info->count():null }}</font></h4>

                                                <!-- <h4 style="margin:4px 5px; text-align: right; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Absent Emp in Line(A):</font>&nbsp;&nbsp;{{ !empty($absent)?$absent:null }}</h4> -->
                                                <h5 style="margin:4px 5px; font-size: 10px; margin: 0; padding: 0"><font style="font-weight: bold;">Print:&nbsp;&nbsp;</font><?php echo date('d-M-Y H:i A');  ?></h5>
                                          

                            </div>
                            <br>
                            @if(!empty($departments))
                            <form class="form-horizontal" role="form" method="post" action="{{ url('hr/timeattendance/attendance_daywise_store')  }}" enctype="multipart/form-data">
                             {{ csrf_field() }}
                                @foreach($departments AS $department)
                                    <?php $count=0; $dept_sl=1; $p=0; $overtime_minutes=0; ?>
                                    <table class="table" style="width:100%;border:1px solid #ccc;font-size:12px;"  cellpadding="2" cellspacing="0" border="1" align="center">
                                        <thead>
                                            <tr>
                                                <th colspan="2">Department</th>
                                                <th colspan="6">{{ $department->hr_department_name }}</th>
                                            </tr>
                                            <tr>
                                                <th>Sl
                                                 <input type="hidden" name="startday" value="{{ !empty($report_date)?$report_date:null }}" readonly="readonly"></th>
                                                <th>Associate ID</th>
                                                <th>Name</th>
                                                <th>Designation</th>
                                                <th class="no_pv">In Time</th>
                                                <th class="no_pv">Out Time</th>
                                                <th class="no_pv">OT</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        // $designation =array_values(array_column($info->toArray(),'hr_department_name'));
                                        //dd($designation);
                                        // dd(array_count_values(array_column(array_column($info->toArray(),'hr_department_name'),'hr_designation_name')));exit;
                                        ?>
                                            @foreach($info AS $emp)
                                            <?php if(($emp->as_department_id== $department->as_department_id) && (!empty($emp->att)) && ($emp->att != "Weekend")) { ?>
                                                <tr>
                                                    <td>{{ $dept_sl }}
                                                        <input type="hidden" name="attendance_id[]" value="{{$emp->atid}}" class="attendance_id">
                                                    </td>

                                                    <td>{{ !empty($emp->associate_id)?$emp->associate_id:null }}</td>
                                                    <td>{{ !empty($emp->as_name)? $emp->as_name:null }}
                                                        <input type="hidden" class="att_status" name="att_status[]" value="{{ !empty($emp->att)? $emp->att:null }}">
                                                        <input type="hidden" name="hr_shift_start_time" id="hr_shift_start_time" class="col-xs-12 " value="{{ !empty($emp->hr_shift_start_time)? $emp->hr_shift_start_time:null }}"/>
                                                        <input type="hidden" name="hr_shift_end_time" id="hr_shift_end_time" class="col-xs-12 " value="{{ !empty($emp->hr_shift_end_time)? $emp->hr_shift_end_time:null }}" />
                                                        <input type="hidden" name="hr_shift_break_time" id="hr_shift_break_time" class="col-xs-12 " value="{{ !empty($emp->hr_shift_break_time)? $emp->hr_shift_break_time:null }}"/>

                                                    </td>
                                                   <!--  <td>{{ !empty($emp->att)? $emp->att:null }}


                                                    </td> -->
                                                    <td rowspan="">{{ !empty($emp->hr_designation_name)? $emp->hr_designation_name:null }}</td>
                                                    <td class="no_pv"><!-- {{!empty($emp->in_time)? $emp->in_time:null }} -->
                                                        <input class="intime manual" type="text" name="intime[]" value="{{!empty($emp->in_time)? $emp->in_time:null}}"  step="2" placeholder="HH:mm:ss">

                                                         <input type="hidden" name="unit_att" class="unit_att" value="{{request()->unit_id}}">

                                                    </td>
                                                    <td class="no_pv"><!-- {{ !empty($emp->out_time)? $emp->out_time:null }} -->
                                                        <input type="text"class="outtime manual" name="outtime[]" value="{{!empty($emp->out_time)? $emp->out_time:null }}" step="2" placeholder="HH:mm:ss"  >

                                                        <input type="hidden" name="ass_id[]" value="{{$emp->as_id}}">
                                                    </td>
                                                    <td class="no_pv"><!-- {{ !empty($emp->out_time)? $emp->out_time:null }} -->
                                                    <input type="text" class="ottime manual" readonly name="outtime[]"  step="2" placeholder="HH:mm:ss"  >

                                                    </td>
                                                    <td>
                                                        
                                                    </td>
                                                    <?php if($emp->att == "P") $p++; $count++; $dept_sl++;
                                                    $overtime_minutes+= $emp->otm;
                                                     ?>
                                                </tr>
                                            <?php } ?>
                                            @endforeach

                                            <tr>
                                                <td></td>
                                                <td colspan="2"></td>
                                                <td></td>
                                                <td><font style="font-weight: bold">Absent:&nbsp;&nbsp;</font><font style="text-align: right;"><?php echo ($count-$p); $count=0; ?></font></td>
                                                <td colspan="3" class="no_pv"></td>

                                            </tr>

                                        </tbody>

                                    </table>
                                    
                                    <?php if (!($loop->last)) {?>
                                            <p class="break-page"></p>
                                    <?php }?>

                                    @endforeach


                                    <?php if($info->count()==0){ ?>
                                           <h4 class="text-center"> No Data Found. <h4>
                                      <?php } ?>
                                    <div class="col-xs-12 pull-right" style="margin-bottom: 10px; padding: 0px;">

                                    <?php if($info->count()!=0){ ?>
                                        <button class="btn btn-info pull-right no_pv" type="submit">
                                              <i class="ace-icon fa fa-check bigger-110"></i> Insert
                                        </button>
                                    <?php } ?>
                                    </div>
                                 </form>
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
function printMe1(divName)
{   
    // var myWindow=window.open('','','width=800,height=800');
    // myWindow.document.write(document.getElementById(divName).innerHTML);
    // myWindow.document.close();
    // myWindow.focus();
    // myWindow.print();
    // myWindow.close();

    var mywindow=window.open('','','width=800,height=800');
    
    mywindow.document.write('<html><head><title>Print Contents</title>');
    mywindow.document.write('<style>.tableTrCenter {font-size: 20px !important; text-align: center !important; width: 100% !important;} .no_pv{display:none;} .break-page{page-break-after: always;}</style>');
    mywindow.document.write('</head><body>');
    mywindow.document.write(document.getElementById(divName).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close();  
    mywindow.focus();           
    mywindow.print();
    mywindow.close();
}

$(document).ready(function(){
      // loader visibility
          $('#searchform').submit(function() {
            $('#load').css('visibility', 'visible');
            });
    // $('#dataTable').DataTable({
    //     pagingType: 'full_numbers',
    // });

    $('#unit_id').on("change", function(){
         $.ajax({
            url : "{{ url('hr/attendance/floor_by_unit') }}",
            type: 'get',
            data: {unit : $(this).val()},
            success: function(data)
            {
                $("#floor_id").html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });

       //Load Line List By Unit ID
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
    //Load Department List By Area ID
    $('#area').on("change", function(){
         $.ajax({
            url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
            type: 'get',
            data: {area_id : $(this).val()},
            success: function(data)
            {
                $("#department").html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });

    //Load Section List By department ID
    $('#department').on("change", function(){
         $.ajax({
            url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
            type: 'get',
            data: {area_id: $("#area").val(), department_id: $(this).val()},
            success: function(data)
            {
                $("#section").html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });

    //Load Sub Section List by Section
    $('#section').on("change", function(){
         $.ajax({
            url : "{{ url('hr/setup/getSubSectionListBySectionID') }}",
            type: 'get',
            data: {
                   area_id: $("#area").val(),
                   department_id: $("#department").val(),
                   section_id: $(this).val()
                  },
            success: function(data)
            {
                $("#subSection").html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });



// Status Hidden field value change

    $(".manual").on("keyup click", function(){
        var intime=$(this).parent().parent().find('.intime').val();
        var outtime=$(this).parent().parent().find('.outtime').val();

        if(intime != ''||outtime != ''){
          $(this).parent().parent().find('.att_status').val('P');
        }
        else{
        $(this).parent().parent().find('.att_status').val('A');
        }

    });
    // Time picker -->
    // $('.intime, .outtime').datetimepicker({

    //         format: 'HH:mm:ss'
    //     });


     $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
        location.href=url
        return false
    })
});
// Radio button location
 function attLocation(loc){
    window.location = loc;
   }


     document.onreadystatechange = function () {
      var state = document.readyState
      if (state == 'interactive') {
           document.getElementById('attendance_content_section').style.visibility="hidden";
      } else if (state == 'complete') {
          setTimeout(function(){
             document.getElementById('interactive');
             document.getElementById('load').style.visibility="hidden";
             document.getElementById('attendance_content_section').style.visibility="visible";
             document.getElementById('attendance_content_section').scrollIntoView();
          },1000);
      }
    }

    /* $('input[name="prdsz_id[]"]').each(function(){
       var selectedgrp = $(this).val();
       $('input[name="sizeGroups[]"]').each(function(){
         if (selectedgrp == $(this).val())
         {
              $(this).prop("checked",true) ;
         }
       });
    }); */
    $('.outtime').on('change',function(){
      var out_time = $(this).val();

      var in_time = $(this).parent().parent().find('.intime').val();
      var associateId = $(this).parent().parent().find('td').eq(1).text();
      console.log(in_time,out_time)
      /* var date = $("#hr_att_date").val(); */
      var ths =$(this);
      var hr_shift_start_time = $(this).parent().parent().find('#hr_shift_start_time').val();
      var hr_shift_end_time = $(this).parent().parent().find('#hr_shift_end_time').val();
      var hr_shift_break_time = $(this).parent().parent().find('#hr_shift_break_time').val();
      $.ajax({
          url: '/hr/timeattendance/calculate_ot',
          method: "GET",
          dataType: 'json',
          data: {
            'out_time' : out_time,
            'in_time': in_time,
            'associateId' : associateId,
            /* 'date' : date, */
            'hr_shift_start_time' : hr_shift_start_time,
            'hr_shift_end_time' : hr_shift_end_time,
            'hr_shift_break_time' : hr_shift_break_time
          },
          success: function(data)
          {
            console.log(data);
            if(hr_shift_start_time){
              ths.parent().parent().find('.ottime').val(data+' hrs');
            }else{
              ths.parent().parent().find('.ottime').val('00');
            }

          }
      });


    });
</script>
@push('js')
    <script>
        $('[name="intime[]"], [name="outtime[]"]').datetimepicker({
          format:'HH:mm:ss'

        });

        // input focus select all element
        $(function () {
            var focusedElement;
            $(document).on('focus', 'input', function () {
                if (focusedElement == this) return;
                focusedElement = this;
                setTimeout(function () { focusedElement.select(); }, 50);
            });
        });
    </script>
@endpush
@endsection