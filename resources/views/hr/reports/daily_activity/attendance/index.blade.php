@extends('hr.layout')
@section('title', 'Daily Attendance')

@section('main-content')
@push('css')
<style type="text/css">
    .single-employee-search {
      margin-top: 82px !important;
    }
    .view:hover, .view:hover{
      color: #ccc !important;
      
    }
    .grid_view{

    }
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .iq-card .iq-card-header {
      margin-bottom: 10px;
      padding: 15px 15px;
      padding-bottom: 8px;
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
                    <a href="#">Reports</a>
                </li>
                <li class="active"> Daily Attendance Report</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="activityReport" method="get" action="#"> 
                        <div class="panel">
                            <div class="panel-body pb-0">
                              @php
                                $mbmFlag = 0;
                                $mbmAll = [1,4,5];
                                $permission = auth()->user()->unit_permissions();
                                $checkUnit = array_intersect($mbmAll,$permission);
                                if(count($checkUnit) > 2){
                                  $mbmFlag = 1;
                                }
                              @endphp
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="unit" class="form-control capitalize select-search" id="unit"required >
                                                <option selected="" value="">Choose...</option>
                                                @if($mbmFlag == 1)
                                                <option value="145">MBM + MBF + MBM 2</option>
                                                @endif
                                                @foreach($unitList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="unit">Unit</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="location" class="form-control capitalize select-search" id="location">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($locationList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="location">Location</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="area" class="form-control capitalize select-search" id="area">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($areaList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="area">Area</label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-3">
                                        
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="department">Department</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="section">Section</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="subSection" class="form-control capitalize select-search" id="subSection" disabled>
                                                <option selected="" value="">Choose...</option> 
                                            </select>
                                            <label for="subSection">Sub Section</label>
                                        </div>
                                        
                                    </div> 
                                    <div class="col-3">
                                      <div class="form-group has-float-label select-search-group">
                                            <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="floor_id">Floor</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="line_id" class="form-control capitalize select-search" id="line_id" disabled >
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="line_id">Line</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                                                <option selected="" value="">Choose...</option>
                                                <option value="0">Non-OT</option>
                                                <option value="1">OT</option>
                                            </select>
                                            <label for="otnonot">OT/Non-OT</label>
                                        </div>
                                        <input type="hidden" id="reportformat" name="report_format" value="0">
                                        <input type="hidden" id="reportGroup" name="report_group" value="as_line_id">
                                    </div>
                                    <div class="col-3">
                                      
                                        <div class="form-group has-float-label select-search-group">
                                            <?php
                                                $reportType = ['absent'=>'Absent', 'before_absent_after_present'=>'Present After Being Absent','leave'=>'Leave','ot'=>'OT', 'working_hour'=>'Working Hour', 'late'=>'Late', 'missing_token' => 'Punch Missing Token'];
                                                //$reportType = ['attendance'=>'Attendance', 'absent'=>'Absent', 'before_absent_after_present'=>'Before Absent After Present','leave'=>'Leave','ot'=>'OT', 'working_hour'=>'Working Hour', 'late'=>'Late'];
                                            ?>
                                            {{ Form::select('report_type', $reportType, null, ['placeholder'=>'Select Report Type ', 'class'=>'form-control capitalize select-search', 'id'=>'reportType']) }}
                                            <label for="reportType">Report Type</label>
                                        </div>
                                        
                                        <div id="single-date">
                                          <div class="form-group has-float-label has-required">
                                            <input type="date" class="report_date datepicker form-control" id="report-date" name="date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                            <label for="report-date">Date</label>
                                          </div>
                                        </div>
                                        <div id="double-date" style="display: none">
                                          <div class="row">
                                            <div class="col pr-0">
                                                <div class="form-group has-float-label has-required">
                                                    <input type="date" class="report_date datepicker form-control" id="present_date" name="present_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                                    <label for="present_date">Present Date</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group has-float-label has-required">
                                                    <input type="date" class="report_date datepicker form-control" id="absent_date" name="absent_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d', strtotime('-1 day')) }}" autocomplete="off" />
                                                    <label for="absent_date">Absent Date</label>
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" ><i class="fa fa-save"></i> Generate</button>
                                        </div>
                                    </div>   
                                </div>
                                {{-- <div class="row">
                                    <div class="offset-8 col-4">
                                        
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="single-employee-search" id="single-employee-search" style="display: none;">
                          <div class="form-group">
                            <input type="text" name="employee" class="form-control" placeholder="Search Employee Associate ID..." id="searchEmployee" autocomplete="off">
                          </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col">
                  <div class="iq-card" id="result-section" style="display: none">
                    <div class="iq-card-header d-flex mb-0">
                       <div class="iq-header-title w-100">
                          <div class="row">
                            <div class="col-3">
                              <h4 class="card-title capitalize inline">
                                  <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                                  <button class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                  <i class="fa fa-file-excel-o"></i>
                                </button>
                                </h4>
                            </div>
                            <div class="col-6 text-center">
                              <div id="head-arrow">
                                <h4 class="card-title capitalize inline">
                                  <a class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Date Report" >
                                    <i class="las la-chevron-left"></i>
                                  </a>
                                  <b class="f-16 uppercase" id="result-head"> </b>
                                  <a class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next Date Report" >
                                    <i class="las la-chevron-right"></i>
                                  </a>
                                </h4>
                              </div>
                            </div>
                            <div class="col-3">
                              <div class="row">
                                <div class="col-7 pr-0">
                                  <div class="format">
                                    <div class="form-group has-float-label select-search-group mb-0">
                                        <?php
                                            $type = ['as_unit_id'=>'Unit','as_designation_id'=>'Designation','as_line_id'=>'Line','as_floor_id'=>'Floor','as_department_id'=>'Department','as_section_id'=>'Section','as_subsection_id'=>'Sub Section'];
                                        ?>
                                        {{ Form::select('report_group_select', $type, 'as_section_id', ['class'=>'form-control capitalize', 'id'=>'reportGroupHead']) }}
                                        <label for="reportGroupHead">Report Format</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-5 pl-0">
                                  <div class="text-right">
                                    <a class="btn view grid_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                                      <i class="las la-th-large"></i>
                                    </a>
                                    <a class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                                      <i class="las la-list-ul"></i>
                                    </a>
                                    
                                  </div>
                                </div>
                              </div>
                              
                              
                            </div>
                          </div>
                       </div>
                    </div>
                    <div class="iq-card-body no-padding" id="print-area">
                      <style type="text/css">
                          .table{
                            width: 100%;
                          }
                          a{text-decoration: none;}
                          .table-bordered {
                              border-collapse: collapse;
                          }
                          .table-bordered th,
                          .table-bordered td {
                            border: 1px solid #777 !important;
                            padding:5px;
                          }
                          .no-border td, .no-border th{
                            border:0 !important;
                            vertical-align: top;
                          }
                          .f-16 th, .f-16 td, .f-16 td b{
                            font-size: 16px !important;
                          }
                      </style>
                      <div class="result-data" id="result-data">
                        
                      </div>
                    </div>
                 </div>
                  
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@include('hr.reports.daily_activity.attendance.employee_activity_modal')
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
    function printDiv(divName)
    { 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
    $(document).ready(function(){   
        var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
        $('#activityReport').on('submit', function(e) {
          e.preventDefault();
          activityProcess();
        });
        $(".next_btn").click(function(event) {
          var date = $('input[name="date"]').val();
          var type = $('select[name="report_type"]').val();
          var dateAfter = moment(date).add(1 , 'day').format("YYYY-MM-DD");
          $('input[name="date"]').val(dateAfter);
          var head = type+' - '+dateAfter;
          $("#result-head").html(head);
          activityProcess();
        });

        $(".prev_btn").click(function(event) {
          var date = $('input[name="date"]').val();
          var type = $('select[name="report_type"]').val();
          var dateBefore = moment(date).subtract(1 , 'day').format("YYYY-MM-DD");
          $('input[name="date"]').val(dateBefore);
          var head = type+' - '+dateBefore;
          $("#result-head").html(head);
          activityProcess();
        });
        $(".grid_view, .list_view").click(function() {
          var value = $(this).attr('id');
          // console.log(value);
          $("#reportformat").val(value);
          $('input[name="employee"]').val('');
          activityProcess();
        });
          
        $("#reportGroupHead").on("change", function(){
          var group = $(this).val();
          $("#reportGroup").val(group);
          activityProcess();
        });

        function activityProcess() {
          $("#result-section").show();
          $("#result-data").html(loader);
          $("#single-employee-search").hide();
          
          var unit = $('select[name="unit"]').val();
          var location = $('select[name="location"]').val();
          var area = $('select[name="area"]').val();
          var date = $('input[name="date"]').val();
          var format = $('input[name="report_format"]').val();
          var type = $('select[name="report_type"]').val();
          // console.log(type);
          if(type === 'before_absent_after_present'){
            $("#head-arrow").hide();
          }else{
            $("#head-arrow").show();
          }
          var form = $("#activityReport");
          var flag = 0;
          if(unit === '' || date === '' || type === ''){
            flag = 1;
            $.notify('Select required field', 'error');
          }
          
          if(flag === 0){
            $(".next_btn").attr('disabled', true);
            $(".prev_btn").attr('disabled', true);
            $('html, body').animate({
                scrollTop: $("#result-data").offset().top
            }, 2000);
            if(type == 'attendance'){
              url = '{{ url("hr/reports/daily-present-absent-activity-report") }}';
            }else{
              url = '{{ url("hr/reports/daily-attendance-activity-report") }}';
            }
            var head = type+' - '+date;
            $("#result-head").html(head);
            
            $.ajax({
                type: "GET",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                  $(".next_btn").attr('disabled', false);
                  $(".prev_btn").attr('disabled', false);
                  // console.log(response);
                  if(response !== 'error'){
                    $("#result-data").html(response);
                  }else{
                    // console.log(response);
                    $("#result-data").html('');
                  }

                  if(format == 0 && response !== 'error'){
                    $("#single-employee-search").show();
                    $('.list_view').addClass('active').attr('disabled', true);
                    $('.grid_view').removeClass('active').attr('disabled', false);
                  }else{
                    $("#single-employee-search").hide();
                    $('.grid_view').addClass('active').attr('disabled', true);
                    $('.list_view').removeClass('active').attr('disabled', false);
                  }
                },
                error: function (reject) {
                  console.log(reject);
                }
            });

          }else{
            console.log('required');
            $("#result-data").html('');
          }
        }
        $('#excel').click(function(){
          var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#report_section').html())
          location.href=url;
          return false;
        });
        // change from data action
        $('#present_date').on('change', function() {
          var before = 1 ;
          var dateBefore = moment($(this).val()).subtract(before , 'day');
          var dateBefore = dateBefore.format("YYYY-MM-DD");
          var absentDate = $('#absent_date').val();
          if(dateBefore !== '') {
            $('#absent_date').val(dateBefore);
          }
        });
        // change unit
        $('#unit').on("change", function(){
            $.ajax({
                url : "{{ url('hr/attendance/floor_by_unit') }}",
                type: 'get',
                data: {unit : $(this).val()},
                success: function(data)
                {
                    $('#floor_id').removeAttr('disabled');
                    
                    $("#floor_id").html(data);
                },
                error: function(reject)
                {
                   console.log(reject);
                }
            });

            //Load Line List By Unit ID
            $.ajax({
               url : "{{ url('hr/reports/line_by_unit') }}",
               type: 'get',
               data: {unit : $(this).val()},
               success: function(data)
               {
                    $('#line_id').removeAttr('disabled');
                    $("#line_id").html(data);
               },
               error: function(reject)
               {
                 console.log(reject);
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
                    $('#department').removeAttr('disabled');
                    
                    $("#department").html(data);
               },
               error: function(reject)
               {
                 console.log(reject);
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
                    $('#section').removeAttr('disabled');
                    
                    $("#section").html(data);
               },
               error: function(reject)
               {
                 console.log(reject);
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
                $('#subSection').removeAttr('disabled');
                
                $("#subSection").html(data);
             },
             error: function(reject)
             {
               console.log(reject);
             }
           });
        });
        //Report type
        $('#reportType').on("change", function(){
          var type = $(this).val();
          // console.log(type);
          $('input[name="employee"]').val('');
          if(type == 'ot'){
            $('#reportGroupHead').append('<option value="ot_hour">OT Hour</option>');
            $('#reportGroupHead').val('ot_hour');
            $('#reportGroup').val('ot_hour');
          }else{
            $("#reportGroupHead option[value='ot_hour']").remove();
            $('#reportGroupHead').val('as_section_id');
            $('#reportGroup').val('as_section_id');

          }
          var date = "{{ date('Y-m-d') }}";
          if(type == 'ot' || type == 'working_hour'){
            date = "{{ date('Y-m-d', strtotime('-1 day')) }}";
          }
          $("#report-date").val(date);
          if(type === 'before_absent_after_present'){
            $("#single-date").hide();
            $("#double-date").show();
          }else{
            $("#single-date").show();
            $("#double-date").hide();
          }
        });

        $('#reportFormat').on("change", function(){
          $('input[name="employee"]').val('');
        });



       
    });
    
    
</script>
@endpush
@endsection