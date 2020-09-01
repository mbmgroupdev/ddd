@extends('hr.layout')
@section('title', 'Daily Attendance')
@push('css')
   <link rel="stylesheet" href="{{ asset('assets/css/reports.css')}}">
@endpush
@section('main-content')
@push('css')
<style type="text/css">

    @media only screen and (max-width: 771px) {
        .background_field{width: 100% !important;}
    }

    @media only screen and (max-width: 771px) {
            .background_div{width: 100% !important;}
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
                <li class="active"> Daily Attendance</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="activityReport" method="get" action="#"> 
                        <div class="panel">
                            <div class="panel-heading">
                                <h6>Daily Attendance Report</h6>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="unit" class="form-control capitalize select-search" id="unit" required="">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($unitList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="unit">Unit</label>
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
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="department">Department</label>
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
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="report_format" class="form-control capitalize select-search" id="reportformat" >
                                                <option value="0">Details</option>
                                                <option value="1" selected>Summary</option>
                                            </select>
                                            <label for="reportformat">Report Format</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group has-float-label select-search-group">
                                            <?php
                                                $reportType = ['absent'=>'Absent', 'before_absent_after_present'=>'Before Absent After Present','leave'=>'Leave','ot'=>'OT', 'working_hour'=>'Working Hour', 'late'=>'Late'];
                                                //$reportType = ['attendance'=>'Attendance', 'absent'=>'Absent', 'before_absent_after_present'=>'Before Absent After Present','leave'=>'Leave','ot'=>'OT', 'working_hour'=>'Working Hour', 'late'=>'Late'];
                                            ?>
                                            {{ Form::select('report_type', $reportType, null, ['placeholder'=>'Select Report Type ', 'class'=>'form-control capitalize select-search', 'id'=>'reportType']) }}
                                            <label for="reportType">Report Type</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <?php
                                                $type = ['as_line_id'=>'Line','as_floor_id'=>'Floor','as_department_id'=>'Department','as_designation_id'=>'Designation'];
                                            ?>
                                            {{ Form::select('report_group', $type, null, ['placeholder'=>'Select Report Group ', 'class'=>'form-control capitalize select-search', 'id'=>'reportGroup']) }}
                                            <label for="reportGroup">Report Group</label>
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
                                    </div>   
                                </div>
                                <div class="row">
                                    <div class="offset-8 col-4">
                                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" ><i class="fa fa-save"></i> Generate</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="single-employee-search" id="single-employee-search" style="display: none;">
                          <div class="form-group">
                            <input type="text" name="employee" class="form-control" placeholder="Search Employee Associate ID..." id="searchEmployee">
                          </div>
                        </div>
                    </form>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col">
                    <div class="result-data" id="result-data"></div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){   
        var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
        $('#activityReport').on('submit', function(e) {
          $("#result-data").html(loader);
          $("#single-employee-search").hide();
          e.preventDefault();
          var url = '';
          var unit = $('select[name="unit"]').val();
          var area = $('select[name="area"]').val();
          var date = $('input[name="date"]').val();
          var format = $('select[name="report_format"]').val();
          var type = $('select[name="report_type"]').val();
          var form = $("#activityReport");
          var flag = 0;
          if(unit === '' || date === '' || type === ''){
            flag = 1;
          }
          if(flag === 0){
            $('html, body').animate({
                scrollTop: $("#result-data").offset().top
            }, 2000);
            if(type == 'attendance'){
              url = '{{ url("hr/reports/daily-present-absent-activity-report") }}';
            }else{
              url = '{{ url("hr/reports/daily-attendance-activity-report") }}';
            }

            $.ajax({
                type: "GET",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                  console.log(response);
                  // if(response !== 'error'){
                  //   $("#result-data").html(response);
                  // }else{
                  //   console.log(response);
                  //   $("#result-data").html('');
                  // }

                  // if(format == 0 && response !== 'error'){
                  //   $("#single-employee-search").show();
                  // }else{
                  //    $("#single-employee-search").hide();
                  // }
                },
                error: function (reject) {
                  console.log(reject);
                }
            });

          }else{
            console.log('required');
            $("#result-data").html('');
          }
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
          $('input[name="employee"]').val('');
          if(type == 'ot'){
            $('#reportGroup').append('<option value="ot_hour">OT Hour</option>');
          }else{
            $("#reportGroup option[value='ot_hour']").remove();
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

    function printMe(el){ 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<html><head></head><body style="font-size:9px;">');
        myWindow.document.write(document.getElementById(el).innerHTML);
        myWindow.document.write('</body></html>');
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
</script>
@endpush
@endsection