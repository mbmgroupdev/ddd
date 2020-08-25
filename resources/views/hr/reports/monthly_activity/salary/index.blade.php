@extends('hr.layout')
@section('title', 'Monthly Salary')

@section('main-content')

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
                <li class="active"> Monthly Salary</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="activityReport" method="get" action="#"> 
                        <div class="panel">
                            <div class="panel-heading">
                                <h6>Monthly Salary Report</h6>
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
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="area" class="form-control capitalize select-search" id="area" required="">
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
                                                $type = ['as_line_id'=>'Line','as_floor_id'=>'Floor','as_department_id'=>'Department','as_designation_id'=>'Designation'];
                                            ?>
                                            {{ Form::select('report_group', $type, null, ['placeholder'=>'Select Report Group ', 'class'=>'form-control capitalize select-search', 'id'=>'reportGroup']) }}
                                            <label for="reportGroup">Report Group</label>
                                        </div>
                                        <div class="form-group has-float-label has-required">
                                          <input type="month" class="report_date form-control" id="report-date" name="month" placeholder=" Month-Year"required="required" value="{{ date('Y-m')}}"autocomplete="off" />
                                          <label for="report-date">Month</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <?php
                                              $status = ['1'=>'Active','2'=>'Resign','3'=>'Terminate','4'=>'Suspend','5'=>'Left'];
                                            ?>
                                            {{ Form::select('employee_status', $status, 1, ['placeholder'=>'Select Employee Status ', 'class'=>'form-control capitalize select-search', 'id'=>'estatus']) }}
                                            <label for="estatus">Status</label>
                                        </div>
                                        
                                    </div>   
                                </div>
                                <div class="row">
                                  <div class="col-3">
                                    <div class="row">
                                      <div class="col-5 pr-0">
                                        <div class="form-group has-float-label has-required">
                                          <input type="number" class="report_date min_sal form-control" id="min_sal" name="min_sal" placeholder="Min Salary" required="required" value="{{ $salaryMin }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                          <label for="min_sal">Range From</label>
                                        </div>
                                      </div>
                                      <div class="col-1 p-0">
                                        <div class="c1DHiF text-center">-</div>
                                      </div>
                                      <div class="col-6">
                                        <div class="form-group has-float-label has-required">
                                          <input type="number" class="report_date max_sal form-control" id="max_sal" name="max_sal" placeholder="Max Salary" required="required" value="{{ $salaryMax }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                          <label for="max_sal">Range To</label>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="offset-5 col-4">
                                      <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" ><i class="fa fa-save"></i> Generate</button>
                                  </div>  
                                </div>
                                <div class="row">
                                    
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
<script src="{{ asset('assets/js/popper.min.js')}}"></script>
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){   
        var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
        $('#activityReport').on('submit', function(e) {
          $("#result-data").html(loader);
          $("#single-employee-search").hide();
          e.preventDefault();
          
          var unit = $('select[name="unit"]').val();
          var area = $('select[name="area"]').val();
          var month = $('input[name="month"]').val();
          var stauts = $('input[name="employee_status"]').val();
          var form = $("#activityReport");
          var flag = 0;
          if(unit === '' || area === '' || month === '' || stauts === ''){
            flag = 1;
          }
          if(flag === 0){
            $('html, body').animate({
                scrollTop: $("#result-data").offset().top
            }, 2000);
            $.ajax({
                type: "GET",
                url: '{{ url("hr/reports/monthly-salary-report") }}',
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                  console.log(response);
                  if(response !== 'error'){
                    $("#result-data").html(response);
                  }else{
                    console.log(response);
                    $("#result-data").html('');
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