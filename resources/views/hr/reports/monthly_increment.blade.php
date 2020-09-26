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
                <li class="active"> Monthly Increment </li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content">
            <div id="load"></div>
              <?php $type='m_increment'; ?>
              @include('hr/reports/attendance_radio')


            <div class="page-header">
                <h1>Reports<small> <i class="ace-icon fa fa-angle-double-right"></i> Monthly Increment </small></h1>
            </div>
            <div class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <form role="form" method="get" action="{{ url('hr/reports/monthy_increment') }}" id="searchform">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="col-sm-2" style="padding-bottom: 10px;">
                                <select class="col-xs-12" id="increment_report_type" name="increment_report_type" required="required">
                                    <option value="">Select Inc. Report Type</option>
                                    <option value="incremented" @if($increment_report_type == 'incremented') selected="selected" @endif>Increment Report</option>
                                    <option value="eligiblility" @if($increment_report_type == 'eligiblility') selected="selected" @endif>Eligibility Report</option>
                                    <option value="increment_pending" @if($increment_report_type == 'increment_pending') selected="selected" @endif>Increment Pending</option>
                                </select>
                            </div>
                            <div class="col-sm-2" style="padding-bottom: 10px;">
                                {{ Form::select('unit_id', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'required'=>'required']) }}
                            </div>
                            {{-- <div class="col-sm-3">
                                {{ Form::select('department_id', $deptList, null, ['placeholder'=>'Select Department', 'id'=>'department_id', 'class'=> 'col-xs-12']) }}
                            </div> --}}
                            <div class="col-sm-2" style="padding-bottom: 10px;">
                                <select class="col-xs-12" id="associate_type" name="associate_type" required="required">
                                    <option value="">Select Associate Type</option>
                                    <option value="1" @if($associate_type==1) selected="selected" @endif>Management</option>
                                    <option value="2" @if($associate_type==2) selected="selected" @endif>Staff</option>
                                    <option value="3" @if($associate_type==3) selected="selected" @endif>Worker</option>
                                </select>
                            </div>
                            <div class="col-sm-2" style="padding-bottom: 40px;">
                                <input type="text" name="month" id="month" class="col-xs-12 datepicker" value="{{ Request::get('month') }}" data-validation="required" placeholder="Enter Date" required="required" style="height: 33px;"/>
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-search"></i>
                                    Search
                                </button>
                                @if(!empty(request()->unit_id) && !empty(request()->month))
                                <button type="button" onClick="printMe('PrintArea')" class="btn btn-warning btn-sm" title="Print">
                                    <i class="fa fa-print"></i>
                                </button>
                                <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-sm" title="PDF">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                                <button type="button"  id="excel"  class="showprint btn btn-success btn-sm">
                                <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id="increment_content_section" class="row table-responsive"  style="overflow-y: scroll; height:400px; border: 1px solid whitesmoke; ">

                @if(!empty(request()->unit_id) && !empty(request()->month))

                    <div class="col-xs-12 no-padding no-margin " id="PrintArea" >
                        <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:20px auto;border:1px solid #ccc">
                            <div class="page-header" style="text-align:left;border-bottom:2px double #666">

                                <h2 style="margin:4px 10px; font-weight: bold;  text-align: center;">{{ !empty($unit)?$unit:null}}</h2>
                                <h4 style="margin:4px 10px; text-align: center; ">{{$type_of_list}} <font style="font-weight: bold; font-size: 16px;">{{ !empty($month)?$month:null }}</font></h4>
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td width="60%">
                                                <h5 style="margin:4px 5px; font-size: 12px;"><font style="font-weight: bold; font-size: 12px;">Print Date & Time: </font><?php echo date('d-m-Y H:i:s') ?></h5>
                                            </td>
                                            <td>
                                                <h5 style="margin:4px 5px; font-size: 10px; text-align: right;"><font style="font-weight: bold;">Page:</font></h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                            {{-- @if(!empty($departments)) --}}
                            @php
                                if(isset($departments))
                                    $cnt = count($departments);
                                else
                                    $cnt = 0;
                            @endphp
                            @if($cnt>0)
                                @foreach($departments AS $department)
                                    <?php $count=0; ?>
                                    <table class="table" style="width:100%;border:1px solid #ccc;font-size:12px;"  cellpadding="2" cellspacing="0" border="1" align="center">
                                        <thead>
                                            <tr>
                                                <th colspan="3">Department</th>
                                                <th colspan="14">{{ !empty($department->hr_department_name)?$department->hr_department_name:null }}</th>
                                            </tr>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Emp.Id</th>
                                                <th>Name</th>
                                                <th>D.O.J</th>
                                                <th>Designation.</th>
                                                <th>Section</th>
                                                <th>Line</th>
                                                <th>Education</th>
                                                <th>Absent without Pay</th>
                                                <th>Absent with Pay</th>
                                                {{-- <th>Effi</th>
                                                <th>Personal Attribute</th> --}}
                                                <th>Last Inc (Tk.)</th>
                                                <th>Last Inc Date</th>
                                                <th>Status A/M</th>
                                                <th>Current Salary</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                @foreach($info AS $emp)
                                                    @if($emp->as_department_id == $department->as_department_id)
                                                    <tr>
                                                        <?php $count++;  ?>
                                                        <td>{{ $count }}</td>
                                                        <td>{{ !empty($emp->associate_id)?$emp->associate_id:null }}</td>
                                                        <td>{{ !empty($emp->as_name)?$emp->as_name:null }}</td>
                                                        <td>{{ !empty($emp->as_doj)?$emp->as_doj:null }}</td>
                                                        <td>{{ !empty($emp->hr_designation_name)?$emp->hr_designation_name:null }}</td>
                                                        <td>{{ !empty($emp->hr_section_name)?$emp->hr_section_name:null }}</td>
                                                        <td>{{ !empty($emp->hr_line_name)?$emp->hr_line_name:null }}</td>
                                                        <td>{{ !empty($emp->edu)?$emp->edu:null }}</td>
                                                        <td>{{ !empty($emp->without_pay)?$emp->without_pay:null }}</td>
                                                        <td>{{ !empty($emp->with_pay)?$emp->with_pay:null }}</td>
                                                        {{-- <td></td>
                                                        <td></td> --}}
                                                        <td>{{ !empty($emp->lat_inc_amount)?$emp->lat_inc_amount:null }}</td>
                                                        <td>{{ !empty($emp->last_inc_date)?$emp->last_inc_date:null }}</td>
                                                        <td>{{ !empty($emp->status)?$emp->status:null }}</td>
                                                        <td>{{ !empty($emp->total_pay)?$emp->total_pay:null }}</td>
                                                        <td>
                                                            @php
                                                                $diffDays = 1;
                                                                if(!empty($emp->last_inc_date)){
                                                                    $from = Carbon\Carbon::parse($emp->last_inc_date);
                                                                    $to = Carbon\Carbon::parse($date);
                                                                    $diffDays = $from->diffInDays($to);
                                                                }
                                                            @endphp
                                                            @if($diffDays == 1)
                                                                <a href="{{url('hr/reports/emp_performance/'.$emp->associate_id.'/'.$date)}}" class="btn btn-info btn-xs"><i class="fa fa-level-up" title="Performance"></i></a>
                                                            @else
                                                                {{-- <a href="{{url('hr/payroll/increment_edit/'.$emp->last_inc_id)}}" class="btn btn-primary btn-xs"><i class="fa fa-pencil" title="Edit"></i></a> --}}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            <tr>
                                                <td></td>
                                                <td colspan="14"><?php echo $count; $count=0;  ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endforeach
                            @else
                                <h3 class="text-center">No Data Found</h3>
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
  $('#searchform').submit(function() {
    $('#load').css('visibility', 'visible');
    });

});
//  Loader
     document.onreadystatechange = function () {
      var state = document.readyState
      if (state == 'interactive') {
           document.getElementById('increment_content_section').style.visibility="hidden";
      } else if (state == 'complete') {
          setTimeout(function(){
             document.getElementById('interactive');
             document.getElementById('load').style.visibility="hidden";
             document.getElementById('increment_content_section').style.visibility="visible";
             document.getElementById('increment_content_section').scrollIntoView();
          },1000);
      }



    }



// Print function
function printMe(divName)
{
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write(document.getElementById(divName).innerHTML);
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}

// $(document).ready(function(){

//     $('#unit_id').on("change", function(){
//         $.ajax({
//             url : "{{ url('hr/reports/department_by_unit') }}",
//             type: 'get',
//             data: {unit : $(this).val()},
//             success: function(data)
//             {
//                 $("#department_id").html(data);
//             },
//             error: function()
//             {
//                 alert('failed...');
//             }
//         });
//     });
// });


// excel conversion -->

$(function(){
    $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
        location.href=url
        return false
    })
})
  function attLocation(loc){
    window.location = loc;
}

</script>
@endsection
