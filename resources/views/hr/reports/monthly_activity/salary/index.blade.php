@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
<style>
   .panel{
    border: 1px solid #ccc;
   }
   .modal-h3{
    margin:5px 0;
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
            <li class="active"> Salary </li>
         </ul>
         <!-- /.breadcrumb -->
      </div>
      <div class="page-content">
         <div class="page-header">
            <h1>Reports<small> <i class="ace-icon fa fa-angle-double-right"></i> Salary </small></h1>
         </div>
         <form class="widget-container-col" role="form" id="activityReport" method="get" action="#">
            <div class="widget-box ui-sortable-handle">
               <div class="widget-body">
                  <div class="row" style="padding: 10px 20px">
                     <div class="col-md-3">
                          <div class="form-group row required">
                            <label class="col-sm-4 control-label no-padding-right" for="unit"> Unit: </label>
                            <div class="col-sm-8 no-padding-right">
                               {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'unit',  'class'=>'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                            </div>
                          </div>
                          <div class="form-group row required" >
                             <label class="col-sm-4 control-label no-padding-right" for="area"> Area: </label>
                             <div class="col-sm-8 no-padding-right" >
                                {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area', 'id'=>'area','class'=> 'col-xs-12','style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Area field is required']) }}
                             </div>
                          </div>
                          <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Department:  </label>
                           <div class="col-sm-8 no-padding-right" >
                              {{ Form::select('department', [], null, ['placeholder'=>'Select Department ', 'id'=>'department','class'=> 'col-xs-12', 'style'=> 'width:100%','data-validation-error-msg'=>'The Department field is required']) }}
                           </div>
                        </div>
                        

                     </div>
                     <div class="col-md-3">
                        <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Floor:  </label>
                           <div class="col-sm-8 no-padding-right" >
                              {{ Form::select('floor_id',[] , null, ['placeholder'=>'Select Floor', 'id'=>'floor_id', 'class'=> 'col-xs-12']) }}
                           </div>
                        </div>
                        <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Section:  </label>
                           <div class="col-sm-8 no-padding-right" >
                              {{ Form::select('section', [], null, ['placeholder'=>'Select Section ', 'id'=>'section', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                           </div>
                        </div>
                        <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Sub Section:  </label>
                           <div class="col-sm-8 no-padding-right" >
                              {{ Form::select('subSection', [],null, ['placeholder'=>'Select Sub-Section ', 'id'=>'subSection', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-optional' =>'true', 'data-validation-error-msg'=>'The Department field is required']) }}
                           </div>
                        </div>
                        
                     </div>
                    <div class="col-sm-3">
                      <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Line:  </label>
                         <div class="col-sm-8 no-padding-right" >
                            {{ Form::select('line_id', [], null, ['placeholder'=>'Select Line', 'id'=>'line_id', 'class'=> 'col-xs-12']) }}
                         </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 control-label no-padding-right" for="otnonot">OT/Non-OT: </label>
                          <div class="col-sm-8 no-padding-right">
                             <select name="otnonot" id="otnonot" class="form-control">
                                <option value="">Select OT/Non-OT</option>
                                <option value="0">Non-OT</option>
                                <option value="1">OT</option>
                             </select>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 control-label no-padding-right" for="reportType">Report Type: </label>
                          <div class="col-sm-8 no-padding-right">
                             <select name="report_type" id="reportType" class="form-control">
                                <option value="0">Details</option>
                                <option value="1">Summary</option>
                             </select>
                          </div>
                      </div>
                      
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group row" >
                           <label class="col-sm-5 control-label no-padding-right" for="type"> Report Format: </label>
                           <div class="col-sm-7 no-padding-right" >
                              <?php
                                 $type = ['as_line_id'=>'Line','as_floor_id'=>'Floor','as_department_id'=>'Department','as_designation_id'=>'Designation'];
                                 ?>
                              {{ Form::select('report_format', $type, null, ['placeholder'=>'Select Report Format ', 'id'=>'type', 'style'=> 'width:100%']) }}
                           </div>
                        </div>
                        <div class="form-group row required">
                          <label class="col-sm-5 control-label no-padding-right" for="present_date"> Month: </label>
                          <div class="col-sm-7 no-padding-right">
                             <input type="text" name="month" id="form-date" class="col-xs-12 monthYearpicker" value="{{ date('M-Y')}}" data-validation="required" data-validation-error-msg="Month field is required" placeholder=" Month-Year" />
                          </div>
                        </div>
                        <div class="form-group row required" >
                           <label class="col-sm-5 control-label no-padding-right" for="estatus"> Status: </label>
                           <div class="col-sm-7 no-padding-right" >
                              <?php
                                 $status = ['1'=>'Active','2'=>'Resign','3'=>'Terminate','4'=>'Suspend','5'=>'Left'];
                                 ?>
                              {{ Form::select('employee_status', $status, 1, ['placeholder'=>'Select Employee Status ', 'id'=>'estatus', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'Employee status field is required']) }}
                           </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="padding-top: 0px;">
                      <div class="col-sm-3">
                        <div class="form-group row" >
                           <label class="col-sm-4 control-label no-padding-right" for="area"> Range:  </label>
                           <div class="col-sm-8 no-padding" >
                              <div class="col-xs-5 no-padding">
                                  <input type="number" name="min_sal" id="min_sal" class="col-xs-12 min_sal" placeholder="Min Salary" value="{{ $salaryMin }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}">
                              </div>
                              <div class="col-xs-2">
                                 <div class="c1DHiF">-</div>
                              </div>
                              <div class="col-xs-5 no-padding-left">
                                  <input type="number" name="max_sal" id="max_sal" class="col-xs-12 max_sal" placeholder="Max Salary" value="{{ $salaryMax }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}">
                              </div>
                           </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group row">
                          <label class="col-sm-4 control-label no-padding-right" for="salaryType">Salary Type: </label>
                          <div class="col-sm-8 no-padding-right">
                             <select name="report_type" id="salaryType" class="form-control">
                                <option value=""> - Select - </option>
                                <option value="0">Cash</option>
                                <option value="1">Bank</option>
                             </select>
                          </div>
                      </div>
                      </div>
                      <div class="col-sm-4">
                         <span style="font-size: 16px; font-weight: bold; color: red;"></span>
                      </div>
                      <div class="col-sm-2 text-right">
                         <button type="submit" class="btn btn-primary btn-sm activityReportBtn">
                         <i class="fa fa-search"></i>
                         Search
                         </button>
                      </div>
                    </div>
                  </div>
               </div>
            </div>
         </form>
         <div class="row">
            <!-- Display Erro/Success Message -->
            @include('inc/message')
            <div class="col-sm-12">
              <div class="result-data" id="result-data"></div>
            </div>
            <!-- /.col -->
         </div>
         <!-- div for summary -->
      </div>
      <!-- /.page-content -->
   </div>
</div>
@push('js')
<script type="text/javascript">
  var loader = '<img src=\'{{ asset("assets/img/loader-box.gif")}}\' class="center-loader">';
  $('#activityReport').on('submit', function(e) {
    $("#result-data").html(loader);
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

  $(document).ready(function(){
    // change from data action
    $('#present_date').on('dp.change', function() {
      var before = 1 ;
      var dateBefore = moment($(this).val()).subtract(before , 'day');
      var dateBefore = dateBefore.format("YYYY-MM-DD");
      var absentDate = $('#absent_date').val();
      if(dateBefore !== '') {
        $('#absent_date').val(dateBefore);
      }
    });

    $('#unit').on("change", function(){
      $.ajax({
         url : "{{ url('hr/attendance/floor_by_unit') }}",
         type: 'get',
         data: {unit : $(this).val()},
         success: function(data)
         {
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
           $("#subSection").html(data);
         },
         error: function(reject)
         {
           console.log(reject);
         }
       });
    });
  });
</script>
@endpush
@endsection
